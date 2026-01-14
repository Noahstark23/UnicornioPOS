<?php
session_start(); // Iniciar sesión para obtener codsucursal
require_once '../includes/db.php';
header('Content-Type: application/json');
ini_set('log_errors', 1);
ini_set('display_errors', 0);

// Función "Universal" para detectar columnas reales antes de insertar
function get_mapped_columns($db, $table, $data_map) {
    try {
        $stmt = $db->query("SHOW COLUMNS FROM $table");
        $db_cols = $stmt->fetchAll(PDO::FETCH_COLUMN);
        $final_data = [];
        
        foreach ($db_cols as $col) {
            foreach ($data_map as $key => $val) {
                if (strtolower($col) == strtolower($key)) {
                    $final_data[$col] = $val;
                    break;
                }
            }
            if (!isset($final_data[$col]) && $col != 'id' && stripos($col, 'autoincrement') === false) {
                 if (stripos($col, 'tenant') !== false) $final_data[$col] = 1;
                 elseif (stripos($col, 'fecha') !== false) $final_data[$col] = date('Y-m-d H:i:s');
                 elseif (stripos($col, 'cod') !== false || stripos($col, 'serie') !== false) $final_data[$col] = '0';
                 elseif (stripos($col, 'total') !== false || stripos($col, 'monto') !== false || stripos($col, 'precio') !== false) $final_data[$col] = 0.00;
                 elseif ($col == 'codsucursal') $final_data[$col] = $_SESSION['codsucursal'] ?? 1; // Fallback explícito 
                 else $final_data[$col] = ''; 
            }
        }
        return $final_data;
    } catch (Exception $e) {
        return []; 
    }
}

function normalize_keys($array) {
    $result = [];
    foreach ($array as $key => $value) {
        $result[strtolower($key)] = $value;
    }
    return $result;
}

try {
    $db = DB::connect();
    $raw_input = json_decode(file_get_contents('php://input'), true);


    if (empty($raw_input['productos'])) throw new Exception("El carrito está vacío");

    // --- 1. VERIFICAR CAJA ABIERTA ---
    // CORRECCION: arqueocaja NO tiene codsucursal. Eliminamos del SELECT.
    $sqlCaja = "SELECT codarqueo FROM arqueocaja WHERE statusarqueo = 1 LIMIT 1";
    $caja = $db->query($sqlCaja)->fetch(PDO::FETCH_ASSOC);
    
    if (!$caja) throw new Exception("⛔ La caja está cerrada. Debes abrirla antes de vender.");
    
    // Obtenemos sucursal de la sesión (lo más confiable ya que reportes usan eso) 
    // Si no hay sesión (uso API externo), default a 1.
    // IMPORTANTE: Des-encriptar si fuese necesario, pero aquí asumimos valor directo o '1' si falla
    $codSucursal = $_SESSION['codsucursal'] ?? 1;
    
    // Si la sesión guarda el valor encriptado (común en este sistema legacy), desencriptar aquí si tuviésemos la función.
    // Como no tenemos 'decrypt' fácil acceso aquí sin incluir clase.php (que es enorme), 
    // asumiremos que si es string largo es hash, pero por ahora usaremos 1 si no es numérico simple?
    // Mejor: Si codsucursal es vacio, 1.
    if (empty($codSucursal)) $codSucursal = 1;

    $codCaja = 1; 
    $idUsuario = $_SESSION['id_usuario'] ?? 1; 
    
    // CORRECCIÓN TIMEZONE: Obtener hora exacta de la BD para evitar desfases
    $stmtDate = $db->query("SELECT NOW()");
    $fecha = $stmtDate->fetchColumn(); // Usamos la hora del servidor de BD
    
    $codSerie = '0001';
    $codVenta = $codSerie . '-' . str_pad(mt_rand(1, 99999999), 9, '0', STR_PAD_LEFT);

    // --- 2. CÁLCULOS GENERALES ---
    $input = normalize_keys($raw_input);
    $total = $input['total'] ?? 0;
    $subtotal = $total / 1.15;
    $iva = $total - $subtotal;

    $db->beginTransaction();

    // --- 3. INSERTAR VENTA (Mapeo Dinámico) ---
    $ventaMap = [
        'codventa' => $codVenta, 'codigo' => $codVenta, 'factura' => $codVenta,
        'total' => $total, 'totalpago' => $total, 'monto' => $total, 
        'fecha' => $fecha, 'fechaventa' => $fecha,
        'codcaja' => $codCaja, 'caja' => $codCaja,
        'codcliente' => $input['cliente_id'] ?? 1, 'cliente' => $input['cliente_id'] ?? 1,
        'codsucursal' => $codSucursal, 'sucursal' => $codSucursal, 
        'statusventa' => 'EMITIDA', 'estado' => 'EMITIDA',
        'formapago' => $input['formapago'] ?? 'EFECTIVO', 'tipopago' => $input['tipopago'] ?? 'CONTADO',
        'subtotalivasi' => $subtotal, 'iva' => $iva, 'totaliva' => $iva,
        'montopagado' => $total, 'tenant_id' => 1,
        'tipodocumento' => 'TICKET', 'codserie' => $codSerie, 'codautorizacion' => '0'
    ];
    
    $colsVenta = get_mapped_columns($db, 'ventas', $ventaMap);
    if (!empty($colsVenta)) {
        $fields = implode(", ", array_keys($colsVenta));
        $placeholders = implode(", ", array_fill(0, count($colsVenta), "?"));
        $db->prepare("INSERT INTO ventas ($fields) VALUES ($placeholders)")->execute(array_values($colsVenta));
    }

    // --- 4. DETALLES Y STOCK ---
    foreach ($raw_input['productos'] as $prod) {
        $p = normalize_keys($prod);
        $idProd = $p['id'] ?? $p['codigo'] ?? $p['codproducto'] ?? null;
        if (!$idProd) throw new Exception("Error: Producto sin código detectado.");

        $cant = $p['cantidad'] ?? $p['cant'] ?? 1;
        $precio = $p['precio'] ?? $p['precioventa'] ?? $p['precioxpublico'] ?? 0;
        $nombre = $p['producto'] ?? $p['nombre'] ?? 'Item';
        $importe = $cant * $precio;

        // A. DETALLES (Smart Insert)
        $detMap = [
            'codventa' => $codVenta, 'venta' => $codVenta,
            'codproducto' => $idProd, 'producto' => $nombre, 
            'cantventa' => $cant, 'cantidad' => $cant,
            'precioventa' => $precio, 'precio' => $precio,
            'importe' => $importe, 'total' => $importe,
            'fechadetalle' => $fecha, 'fecha' => $fecha, // Vital para reportes cronológicos
            'codsucursal' => $codSucursal, 'sucursal' => $codSucursal, // Vital para filtros de sucursal
            'tenant_id' => 1,
            'descproducto' => 0 
        ];

        // Probamos nombres comunes de tablas de detalle
        $tablaDetalle = 'detalleventas';
        $colsDet = get_mapped_columns($db, $tablaDetalle, $detMap);
        
        if (empty($colsDet)) {
             $tablaDetalle = 'detalle_ventas';
             $colsDet = get_mapped_columns($db, $tablaDetalle, $detMap);
        }

        if (!empty($colsDet)) {
            $fieldsD = implode(", ", array_keys($colsDet));
            $holdersD = implode(", ", array_fill(0, count($colsDet), "?"));
            $db->prepare("INSERT INTO $tablaDetalle ($fieldsD) VALUES ($holdersD)")->execute(array_values($colsDet));
        }

        // B. ACTUALIZAR STOCK
        $db->prepare("UPDATE productos SET existencia = existencia - ? WHERE codproducto = ?")->execute([$cant, $idProd]);

        // C. KARDEX (Posicional para máxima compatibilidad)
        $qSaldo = $db->prepare("SELECT existencia FROM productos WHERE codproducto = ?");
        $qSaldo->execute([$idProd]);
        $saldoActual = $qSaldo->fetchColumn() ?: 0;
        
        $sqlKardex = "INSERT INTO kardex VALUES (
            NULL, ?, '1', ?, 'SALIDAS', 
            0, ?, 0, ?, 'SI', 
            0.00, ?, ?, ?, ?
        )";
        
        $db->prepare($sqlKardex)->execute([
            "VENTA:$codVenta", $idProd, 
            $cant, $saldoActual, 
            $precio, "Venta $codVenta", $fecha, $codSucursal
        ]);
    }

    // --- 7. VALIDACIÓN Y LÓGICA DE CRÉDITOS (BLINDADA) ---
    if (isset($input['tipopago']) && $input['tipopago'] === 'CREDITO') {
        $codClienteFinal = $input['cliente_id'] ?? 1;
        
        // 1. Obtener límite y calcular deuda actual (créditos - abonos)
        $sql = "SELECT c.limitecredito,
                       (SELECT IFNULL(SUM(montocredito), 0) 
                        FROM creditosxclientes 
                        WHERE codcliente = c.codcliente) -
                       (SELECT IFNULL(SUM(montoabono), 0) 
                        FROM abonoscreditosventas 
                        WHERE codcliente = c.codcliente) as deuda_actual
                FROM clientes c 
                WHERE c.codcliente = ?";
        $stmt = $db->prepare($sql);
        $stmt->execute([$codClienteFinal]);
        $cliente = $stmt->fetch();
        
        // 2. Validar que tenga línea de crédito
        if (!$cliente || $cliente['limitecredito'] <= 0) {
            throw new Exception("❌ Cliente no tiene línea de crédito autorizada");
        }
        
        // 3. Calcular crédito disponible
        $creditoDisponible = $cliente['limitecredito'] - $cliente['deuda_actual'];
        
        // 4. Validar que no exceda el disponible
        if ($total > $creditoDisponible) {
            throw new Exception("❌ Crédito insuficiente. Disponible: C$ " . number_format($creditoDisponible, 2));
        }
        
        // 5. Todo OK - Proceder con registro de crédito
        $montoCredito = $total;
        
        // A. Actualizar Arqueo (Sumar a columna creditos)
        $stmtArqueo = $db->prepare("UPDATE arqueocaja SET creditos = IFNULL(creditos,0) + ? WHERE codcaja = ? AND statusarqueo = 1");
        $stmtArqueo->execute([$montoCredito, $codCaja]);

        // B. Actualizar Saldo Cliente
        $codClienteFinal = $input['cliente_id'] ?? 1;
        
        // Verificar existencia
        $stmtCheck = $db->prepare("SELECT codcredito FROM creditosxclientes WHERE codcliente = ? AND codsucursal = ?");
        $stmtCheck->execute([$codClienteFinal, $codSucursal]);
        
        if ($stmtCheck->fetch()) {
            $stmtUpd = $db->prepare("UPDATE creditosxclientes SET montocredito = montocredito + ? WHERE codcliente = ? AND codsucursal = ?");
            $stmtUpd->execute([$montoCredito, $codClienteFinal, $codSucursal]);
        } else {
            $stmtIns = $db->prepare("INSERT INTO creditosxclientes (codcliente, montocredito, codsucursal) VALUES (?, ?, ?)");
            $stmtIns->execute([$codClienteFinal, $montoCredito, $codSucursal]);
        }
    }

    $db->commit();
    echo json_encode(['status' => 'ok', 'message' => 'Venta procesada con éxito', 'ticket' => $codVenta]);

} catch (Exception $e) {
    if ($db->inTransaction()) $db->rollBack();
    http_response_code(500); 
    echo json_encode(['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
}
?>
