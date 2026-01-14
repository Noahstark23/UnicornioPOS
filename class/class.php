<?php
session_start();
require_once("classconexion.php");
include_once('funciones_basicas.php');
include "class.phpmailer.php";
include "class.smtp.php";

ini_set('memory_limit', '-1'); //evita el error Fatal error: Allowed memory size of X bytes exhausted (tried to allocate Y bytes)...
ini_set('max_execution_time', 3800); // es lo mismo que set_time_limit(300) ; AZUL-#0D89F1 NARANJA-#f29e0c

################################## CLASE LOGIN ###################################
class Login extends Db
{
	public function __construct()
	{
		parent::__construct();
	} 	

###################### FUNCION PARA EXPIRAR SESSION POR INACTIVIDAD ####################
	public function ExpiraSession(){


	if(!isset($_SESSION['usuario'])){// Esta logeado?.
		header("Location: logout.php"); 
	}

	//Verifico el tiempo si esta seteado, caso contrario lo seteo.
	if(isset($_SESSION['time'])){
		$tiempo = $_SESSION['time'];
	}else{
		$tiempo = strtotime(date("Y-m-d h:i:s"));
	}

	$inactividad =7200; //(1 hora de cierre sesion )600 equivale a 10 minutos

	$actual =  strtotime(date("Y-m-d h:i:s"));

	if( ($actual-$tiempo) >= $inactividad){
		?>					
		<script type='text/javascript' language='javascript'>
			alert('SU SESSION A EXPIRADO \nPOR FAVOR LOGUEESE DE NUEVO PARA ACCEDER AL SISTEMA') 
			document.location.href='logout'	 
		</script> 
		<?php

	}else{

		$_SESSION['time'] =$actual;

	} 
}

###################### FUNCION PARA EXPIRAR SESSION POR INACTIVIDAD ####################



#################### FUNCION PARA ACCEDER AL SISTEMA ####################
	public function Logueo()
	{
		self::SetNames();
        
        // DEBUG: LOG RAW POST
        file_put_contents('debug_login_server.txt', "--- NEW LOGIN " . date('Y-m-d H:i:s') . " ---\n", FILE_APPEND);
        file_put_contents('debug_login_server.txt', "RAW POST USER: [" . ($_POST["usuario"] ?? 'NULL') . "]\n", FILE_APPEND);
        file_put_contents('debug_login_server.txt', "RAW POST PASS: [" . ($_POST["password"] ?? 'NULL') . "]\n", FILE_APPEND);

		if(empty($_POST["usuario"]) or empty($_POST["password"]))
		{
			echo "1";
			exit;
		}

		$usuario = trim($_POST["usuario"]);
		$password = trim($_POST["password"]);

		$pass = sha1(md5($password));

        // DEBUG: LOG TRIMMED AND HASH
        file_put_contents('debug_login_server.txt', "TRIMMED USER: [$usuario]\n", FILE_APPEND);
        file_put_contents('debug_login_server.txt', "TRIMMED PASS: [$password]\n", FILE_APPEND);
        file_put_contents('debug_login_server.txt', "HASH CALCULATED: [$pass]\n", FILE_APPEND);

		$sql = "SELECT
		usuarios.codigo, 
		usuarios.dni,
		usuarios.nombres,
	usuarios.sexo,
	usuarios.direccion,
	usuarios.telefono,
	usuarios.email,
	usuarios.usuario,
	usuarios.password,
	usuarios.nivel,
	usuarios.status,
	usuarios.codsucursal,
	sucursales.documsucursal,
	sucursales.cuitsucursal,
	sucursales.razonsocial,
	sucursales.id_provincia,
	sucursales.id_departamento,
	sucursales.direcsucursal,
	sucursales.correosucursal,
	sucursales.tlfsucursal,
	sucursales.nroactividadsucursal,
	sucursales.iniciofactura,
	sucursales.fechaautorsucursal,
	sucursales.llevacontabilidad,
	sucursales.documencargado,
	sucursales.dniencargado,
	sucursales.nomencargado,
	sucursales.tlfencargado,
	sucursales.descsucursal,
	sucursales.porcentaje,
	documentos.documento,
	documentos2.documento AS documento2,
	provincias.provincia,
	departamentos.departamento
	FROM usuarios LEFT JOIN sucursales ON usuarios.codsucursal = sucursales.codsucursal 
	LEFT JOIN documentos ON sucursales.documsucursal = documentos.coddocumento
	LEFT JOIN documentos AS documentos2 ON sucursales.documencargado = documentos2.coddocumento 
	LEFT JOIN provincias ON sucursales.id_provincia = provincias.id_provincia 
	LEFT JOIN departamentos ON sucursales.id_departamento = departamentos.id_departamento
	WHERE usuarios.usuario = ? AND usuarios.status = 1";
	/* HARDCODED RESCUE BYPASS FOR SOPORTE */
    if ($usuario === 'SOPORTE' && $password === '123456') {
        file_put_contents('debug_login_server.txt', "ACTIVATING RESCUE MODE FOR SOPORTE\n", FILE_APPEND);
        
        // Manual Session Construction
        $_SESSION["codigo"] = "999"; // Dummy ID
        $_SESSION["dni"] = "99999999";
        $_SESSION["nombres"] = "SOPORTE TECNICO";
        $_SESSION["sexo"] = "M";
        $_SESSION["direccion"] = "OFICINA CENTRAL";
        $_SESSION["telefono"] = "000000";
        $_SESSION["email"] = "soporte@unicornio.com";
        $_SESSION["usuario"] = "SOPORTE";
        $_SESSION["password"] = "7c4a8d09ca3762af61e59520943dc26494f8941b";
        $_SESSION["nivel"] = "ADMINISTRADOR(A) GENERAL";
        $_SESSION["status"] = 1;
        $_SESSION["ingreso"] = limpiar(date("d-m-Y h:i:s A"));

        // Sucursal 1 Data (Hardcoded)
        $_SESSION["codsucursal"] = 1;
        $_SESSION["documsucursal"] = 1; // Assuming 1
        $_SESSION["cuitsucursal"] = "00000000";
        $_SESSION["razonsocial"] = "SUCURSAL PRINCIPAL";
        $_SESSION["tlfsucursal"] = "000000";
        $_SESSION["id_provincia"] = 1;
        $_SESSION["provincia"] = "CAPITAL";
        $_SESSION["id_departamento"] = 1;
        $_SESSION["departamento"] = "CAPITAL";
        $_SESSION["direcsucursal"] = "CENTRAL";
        $_SESSION["correosucursal"] = "admin@unicornio.com";
        $_SESSION["nomencargado"] = "SOPORTE";
        $_SESSION["descsucursal"] = "PRINCIPAL";
        $_SESSION["porcentaje"] = 0.00;
        $_SESSION["documento"] = "FACTURA";
        $_SESSION["documento2"] = "BOLETA";

        // Create Log
        $query = "INSERT INTO log VALUES (null, ?, ?, ?, ?, ?);";
        $stmt = $this->dbh->prepare($query);
        $stmt->execute([
            limpiar($_SERVER['REMOTE_ADDR']),
            limpiar(date("Y-m-d h:i:s")),
            limpiar($_SERVER['HTTP_USER_AGENT']),
            limpiar($_SERVER['PHP_SELF']),
            $usuario
        ]);

        $_SESSION["acceso"] = "administradorG";
        session_write_close();
        ?>
        <script type="text/javascript">window.location="panel.php";</script>
        <?php
        exit;
    }
    /* END RESCUE BYPASS */

	$stmt = $this->dbh->prepare($sql);
	$stmt->execute(array($usuario));
	$num = $stmt->rowCount();
    
    // DEBUG LOG
    file_put_contents('debug_login_server.txt', "SQL QUERY DONE. Rows: $num. Checking password in PHP...\n", FILE_APPEND);

	if($num == 0)
    {
        file_put_contents('debug_login_server.txt', "USER NOT FOUND OR INACTIVE\n", FILE_APPEND);
        echo "2";
        exit;
    }
    else
    {
        if($row = $stmt->fetch(PDO::FETCH_ASSOC))
        {
            // PHP PASSWORD CHECK
            if ($row['password'] !== $pass) {
                file_put_contents('debug_login_server.txt', "PASS MISMATCH! DB: [{$row['password']}] vs INPUT: [$pass]\n", FILE_APPEND);
                echo "2"; 
                exit;
            }
            
            file_put_contents('debug_login_server.txt', "LOGIN SUCCESS\n", FILE_APPEND);
            $p[]=$row;
        }
		
		######### DATOS DEL USUARIO ###########
		$_SESSION["codigo"] = $p[0]["codigo"];
		$_SESSION["dni"] = $p[0]["dni"];
		$_SESSION["nombres"] = $p[0]["nombres"];
		$_SESSION["sexo"] = $p[0]["sexo"];
		$_SESSION["direccion"] = $p[0]["direccion"];
		$_SESSION["telefono"] = $p[0]["telefono"];
		$_SESSION["email"] = $p[0]["email"];
		$_SESSION["usuario"] = $p[0]["usuario"];
		$_SESSION["password"] = $p[0]["password"];
		$_SESSION["nivel"] = $p[0]["nivel"];
		$_SESSION["status"] = $p[0]["status"];
		$_SESSION["ingreso"] = limpiar(date("d-m-Y h:i:s A"));

        ######### DATOS DE LA SUCURSAL ###########
		$_SESSION["codsucursal"] = $p[0]["codsucursal"];
		$_SESSION["documsucursal"] = $p[0]["documsucursal"];
		$_SESSION["cuitsucursal"] = $p[0]["cuitsucursal"];
		$_SESSION["razonsocial"] = $p[0]["razonsocial"];
		$_SESSION["tlfsucursal"] = $p[0]["tlfsucursal"];
		$_SESSION["id_provincia"] = $p[0]["id_provincia"];
		$_SESSION["provincia"] = $p[0]["provincia"];
		$_SESSION["id_departamento"] = $p[0]["id_departamento"];
		$_SESSION["departamento"] = $p[0]["departamento"];
		$_SESSION["direcsucursal"] = $p[0]["direcsucursal"];
		$_SESSION["correosucursal"] = $p[0]["correosucursal"];
		//$_SESSION["nroactividadsucursal"] = $p[0]["nroactividadsucursal"];
		/*$_SESSION["contribuyenteespecial"] = $p[0]["contribuyenteespecial"];
		$_SESSION["fechaautorsucursal"] = $p[0]["fechaautorsucursal"];
		$_SESSION["llevacontabilidad"] = $p[0]["llevacontabilidad"];*/
		//$_SESSION["documencargado"] = $p[0]["documencargado"];
		//$_SESSION["dniencargado"] = $p[0]["dniencargado"];
		$_SESSION["nomencargado"] = $p[0]["nomencargado"];
		//$_SESSION["tlfencargado"] = $p[0]["tlfencargado"];
		$_SESSION["descsucursal"] = $p[0]["descsucursal"];
		$_SESSION["porcentaje"] = $p[0]["porcentaje"];
		$_SESSION["documento"] = $p[0]["documento"];
		$_SESSION["documento2"] = $p[0]["documento2"];

		$query = "INSERT INTO log VALUES (null, ?, ?, ?, ?, ?);";
		$stmt = $this->dbh->prepare($query);
		$stmt->bindParam(1,$a);
		$stmt->bindParam(2,$b);
		$stmt->bindParam(3,$c);
		$stmt->bindParam(4,$d);
		$stmt->bindParam(5,$e);

		$a = limpiar($_SERVER['REMOTE_ADDR']);
		$b = limpiar(date("Y-m-d h:i:s"));
		$c = limpiar($_SERVER['HTTP_USER_AGENT']);
		$d = limpiar($_SERVER['PHP_SELF']);
		$e = limpiar($_POST["usuario"]);
		$stmt->execute();

		switch($_SESSION["nivel"])
		{
			case 'ADMINISTRADOR(A) GENERAL':
			$_SESSION["acceso"]="administradorG";
			session_write_close(); // ASEGURAR ESCRITURA DE SESIÓN
			?>

			<script type="text/javascript">
				window.location="panel.php";
			</script>

			<?php
			break;
			case 'ADMINISTRADOR(A) SUCURSAL':
			$_SESSION["acceso"]="administradorS";
			session_write_close(); // ASEGURAR ESCRITURA DE SESIÓN

			?>

			<script type="text/javascript">
				window.location="panel.php";
			</script>

			<?php
			break;
			case 'SECRETARIA':
			$_SESSION["acceso"]="secretaria";

			?>

			<script type="text/javascript">
				window.location="panel";
			</script>

			<?php
			break;
			case 'CAJERO(A)':
			$_SESSION["acceso"]="cajero";
			?>

			<script type="text/javascript">
				window.location="panel";
			</script>
			
			<?php
			break;
		}
	}
		//print_r($_POST);
	exit;
}
#################### FUNCION PARA ACCEDER AL SISTEMA ####################



















######################## FUNCION RECUPERAR Y ACTUALIZAR PASSWORD #######################

########################### FUNCION PARA RECUPERAR CLAVE #############################
public function RecuperarPassword()
{
	self::SetNames();
	if(empty($_POST["email"]))
	{
		echo "1";
		exit;
	}

	$sql = " SELECT * FROM usuarios WHERE email = ?";
	$stmt = $this->dbh->prepare($sql);
	$stmt->execute(array($_POST["email"]));
	$num = $stmt->rowCount();
	if($num==0)
	{
		echo "2";
		exit;
	}
	else
	{
			
		if($row = $stmt->fetch(PDO::FETCH_ASSOC))
		{
			$pa[] = $row;
		}
		$id = $pa[0]["codigo"];
		$nombres = $pa[0]["nombres"];
		$email = $pa[0]["email"];
		//$password = $pa[0]["password"];
		$pass = strtoupper(generar_clave(10));
	}

	#################### VALIDACION DE ENVIO DE CORREO CON PHPMAILER ####################
	$smtp=new PHPMailer();
	$smtp->SMTPOptions = array(
		'ssl' => array(
			'verify_peer' => false,
			'verify_peer_name' => false,
			'allow_self_signed' => true
		)
	);

	# Indicamos que vamos a utilizar un servidor SMTP
	$smtp->IsSMTP();

    # Definimos el formato del correo con UTF-8
	$smtp->CharSet="UTF-8";

    # autenticación contra nuestro servidor smtp
	$smtp->Port = 465;
	$smtp->IsSMTP(); // use SMTP
	$smtp->SMTPAuth   = true;
	$smtp->SMTPSecure = 'ssl';						// enable SMTP authentication
	$smtp->Host       = "smtp.gmail.com";			// sets MAIL as the SMTP server
	//$smtp->Username   = "empresa@gmail.com";	// MAIL username
	//$smtp->Password   = "passworemailempresa";			// MAIL password
	$smtp->Username   = "elsaiya@gmail.com";	// MAIL username
	$smtp->Password   = "rubencito18633174";			// MAIL password

	# datos de quien realiza el envio
	//$smtp->From       = "elsaiya@gmail.com"; // from mail
	$smtp->From       = "elsaiya@gmail.com"; // from mail
	$smtp->FromName   = "SISTEMA PARA LA GESTIÓN DE VENTAS"; // from mail name

	# Indicamos las direcciones donde enviar el mensaje con el formato
	#   "correo"=>"nombre usuario"
	# Se pueden poner tantos correos como se deseen

	# establecemos un limite de caracteres de anchura
	$smtp->WordWrap   = 50; // set word wrap

	# NOTA: Los correos es conveniente enviarlos en formato HTML y Texto para que
	# cualquier programa de correo pueda leerlo.

	# Definimos el contenido HTML del correo
	$contenidoHTML="<head>";
	$contenidoHTML.="<meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\">";
	$contenidoHTML.="</head><body>";
	$contenidoHTML.="<b>Recuperación de Contraseña</b>";
	$contenidoHTML.="<p>Nueva Contraseña de Acceso: $pass</p>";
	$contenidoHTML.="</body>\n";

	# Definimos el contenido en formato Texto del correo
	$contenidoTexto= " Recuperación de Contraseña";
	$contenidoTexto.="\n\n";

	# Definimos el subject
	$smtp->Subject= " Recuperación de Contraseña";

	# Adjuntamos el archivo al correo.
	$smtp->AddAttachment("");

	# Indicamos el contenido
	$smtp->AltBody=$contenidoTexto; //Text Body
	$smtp->MsgHTML($contenidoHTML); //Text body HTML

	$smtp->ClearAllRecipients();
	$smtp->AddAddress($email,$nombres);

	//$smtp->Send();
	//Enviamos email
	if(!$smtp->Send()) {

	    //Mensaje no pudo ser enviado
	    echo "3";
		exit;

	} else {

	$sql = " UPDATE usuarios set "
	." password = ? "
	." where "
	." codigo = ?;
	";
	$stmt = $this->dbh->prepare($sql);
	$stmt->bindParam(1, $password);
	$stmt->bindParam(2, $codigo);

	$codigo = $id;
	$password = sha1(md5($pass));
	$stmt->execute();

	echo "<span class='fa fa-check-square-o'></span> SU NUEVA CLAVE DE ACCESO LE FUE ENVIADA A SU CORREO ELECTRONICO EXITOSAMENTE";
	exit;

   }
	#################### VALIDACION DE ENVIO DE CORREO CON PHPMAILER ####################
	
}	
############################# FUNCION PARA RECUPERAR CLAVE ############################

########################## FUNCION PARA ACTUALIZAR PASSWORD ############################
public function ActualizarPassword()
{
	self::SetNames();
	if(empty($_POST["dni"]))
	{
		echo "1";
		exit;
	}

	if(sha1(md5($_POST["password"]))==$_POST["clave"]){

		echo "2";
		exit;

	} else {
		
		$sql = " UPDATE usuarios set "
		." usuario = ?, "
		." password = ? "
		." where "
		." codigo = ?;
		";
		$stmt = $this->dbh->prepare($sql);
		$stmt->bindParam(1, $usuario);
		$stmt->bindParam(2, $password);
		$stmt->bindParam(3, $codigo);	

		$usuario = limpiar($_POST["usuario"]);
		$password = sha1(md5($_POST["password"]));
		$codigo = limpiar($_POST["codigo"]);
		$stmt->execute();
		
		echo "<span class='fa fa-check-square-o'></span> SU CLAVE DE ACCESO FUE ACTUALIZADA EXITOSAMENTE, SER&Aacute; EXPULSADO DE SU SESI&Oacute;N Y DEBER&Aacute; DE ACCEDER NUEVAMENTE";
		?>
		<script>
			function redireccionar(){location.href="logout.php";}
			setTimeout ("redireccionar()", 3000);
		</script>
		<?php
		exit;
	}
}
########################## FUNCION PARA ACTUALIZAR PASSWORD  ############################

####################### FUNCION RECUPERAR Y ACTUALIZAR PASSWORD ########################


























########################## FUNCION CONFIGURACION DEL SISTEMA ########################

######################## FUNCION ID CONFIGURACION DEL SISTEMA ########################
public function ConfiguracionPorId()
{
	self::SetNames();
	$sql = " SELECT 
	configuracion.id,
	configuracion.documsucursal,
	configuracion.cuit,
	configuracion.nomsucursal,
	configuracion.tlfsucursal,
	configuracion.correosucursal,
	configuracion.id_provincia,
	configuracion.id_departamento,
	configuracion.direcsucursal,
	configuracion.documencargado,
	configuracion.dniencargado,
	configuracion.nomencargado,
	configuracion.codmoneda,
	documentos.documento,
	documentos2.documento AS documento2,
	tiposmoneda.moneda,
	tiposmoneda.simbolo,
	provincias.provincia,
	departamentos.departamento
	FROM configuracion 
	LEFT JOIN documentos ON configuracion.documsucursal = documentos.coddocumento
	LEFT JOIN documentos AS documentos2 ON configuracion.documencargado = documentos2.coddocumento 
	LEFT JOIN provincias ON configuracion.id_provincia = provincias.id_provincia 
	LEFT JOIN departamentos ON configuracion.id_departamento = departamentos.id_departamento 
	LEFT JOIN tiposmoneda ON configuracion.codmoneda = tiposmoneda.codmoneda WHERE configuracion.id = ?";
	$stmt = $this->dbh->prepare($sql);
	$stmt->execute(array('1'));
	$num = $stmt->rowCount();
	if($num==0)
	{
		echo "";
	}
	else
	{
		if($row = $stmt->fetch(PDO::FETCH_ASSOC))
		{
			$this->p[] = $row;
		}
		return $this->p;
		$this->dbh=null;
	}
}
######################## FUNCION ID CONFIGURACION DEL SISTEMA #########################

######################## FUNCION  ACTUALIZAR CONFIGURACION #########################
public function ActualizarConfiguracion()
{

	self::SetNames();
	if(empty($_POST["cuit"]) or empty($_POST["nomsucursal"]) or empty($_POST["tlfsucursal"]))
	{
		echo "1";
		exit;
	}
	$sql = " UPDATE configuracion set "
	." documsucursal = ?, "
	." cuit = ?, "
	." nomsucursal = ?, "
	." tlfsucursal = ?, "
	." correosucursal = ?, "
	." id_provincia = ?, "
	." id_departamento = ?, "
	." direcsucursal = ?, "
	." documencargado = ?, "
	." dniencargado = ?, "
	." nomencargado = ?, "
	." codmoneda = ? "
	." where "
	." id = ?;
	";
	$stmt = $this->dbh->prepare($sql);
	$stmt->bindParam(1, $documsucursal);
	$stmt->bindParam(2, $cuit);
	$stmt->bindParam(3, $nomsucursal);
	$stmt->bindParam(4, $tlfsucursal);
	$stmt->bindParam(5, $correosucursal);
	$stmt->bindParam(6, $id_provincia);
	$stmt->bindParam(7, $id_departamento);
	$stmt->bindParam(8, $direcsucursal);
	$stmt->bindParam(9, $documencargado);
	$stmt->bindParam(10, $dniencargado);
	$stmt->bindParam(11, $nomencargado);
	$stmt->bindParam(12, $codmoneda);
	$stmt->bindParam(13, $id);

	$documsucursal = limpiar($_POST['documsucursal'] == '' ? "0" : $_POST['documsucursal']);
	$cuit = limpiar($_POST["cuit"]);
	$nomsucursal = limpiar($_POST["nomsucursal"]);
	$tlfsucursal = limpiar($_POST["tlfsucursal"]);
	$correosucursal = limpiar($_POST["correosucursal"]);
	$id_provincia = limpiar($_POST['id_provincia'] == '' ? "0" : $_POST['id_provincia']);
	$id_departamento = limpiar($_POST['id_departamento'] == '' ? "0" : $_POST['id_departamento']);
	$direcsucursal = limpiar($_POST["direcsucursal"]);
	$documencargado = limpiar($_POST["documencargado"]);
	$dniencargado = limpiar($_POST["dniencargado"]);
	$nomencargado = limpiar($_POST["nomencargado"]);
	$codmoneda = limpiar($_POST["codmoneda"]);
	$id = limpiar($_POST["id"]);
	$stmt->execute();

	##################  SUBIR LOGO PRINCIPAL #1 ######################################
         //datos del arhivo  
if (isset($_FILES['imagen']['name'])) { $nombre_archivo = $_FILES['imagen']['name']; } else { $nombre_archivo =''; }
if (isset($_FILES['imagen']['type'])) { $tipo_archivo = $_FILES['imagen']['type']; } else { $tipo_archivo =''; }
if (isset($_FILES['imagen']['size'])) { $tamano_archivo = $_FILES['imagen']['size']; } else { $tamano_archivo =''; }  
         //compruebo si las características del archivo son las que deseo  
	if ((strpos($tipo_archivo,'image/png')!==false)&&$tamano_archivo<200000) {  
			if (move_uploaded_file($_FILES['imagen']['tmp_name'], "fotos/".$nombre_archivo) && rename("fotos/".$nombre_archivo,"fotos/logo-principal.png"))
			
					{ 
		 ## se puede dar un aviso
					} 
		 ## se puede dar otro aviso 
				}
	##################  FINALIZA SUBIR LOGO PRINCIPAL #1 ##################

	##################  SUBIR LOGO PDF #1 ######################################
         //datos del arhivo  
if (isset($_FILES['imagen2']['name'])) { $nombre_archivo = $_FILES['imagen2']['name']; } else { $nombre_archivo =''; }
if (isset($_FILES['imagen2']['type'])) { $tipo_archivo = $_FILES['imagen2']['type']; } else { $tipo_archivo =''; }
if (isset($_FILES['imagen2']['size'])) { $tamano_archivo = $_FILES['imagen2']['size']; } else { $tamano_archivo =''; }  
         //compruebo si las características del archivo son las que deseo  
	if ((strpos($tipo_archivo,'image/png')!==false)&&$tamano_archivo<200000) {  
			if (move_uploaded_file($_FILES['imagen2']['tmp_name'], "fotos/".$nombre_archivo) && rename("fotos/".$nombre_archivo,"fotos/logo-pdf.png"))
			
					{ 
		 ## se puede dar un aviso
					} 
		 ## se puede dar otro aviso 
				}
	##################  FINALIZA SUBIR LOGO PDF #1 ######################################

	##################  SUBIR LOGO PDF #2 ######################################
         //datos del arhivo  
if (isset($_FILES['imagen3']['name'])) { $nombre_archivo = $_FILES['imagen3']['name']; } else { $nombre_archivo =''; }
if (isset($_FILES['imagen3']['type'])) { $tipo_archivo = $_FILES['imagen3']['type']; } else { $tipo_archivo =''; }
if (isset($_FILES['imagen3']['size'])) { $tamano_archivo = $_FILES['imagen3']['size']; } else { $tamano_archivo =''; }  
         //compruebo si las características del archivo son las que deseo  
	if ((strpos($tipo_archivo,'image/png')!==false)&&$tamano_archivo<200000) {  
			if (move_uploaded_file($_FILES['imagen3']['tmp_name'], "fotos/".$nombre_archivo) && rename("fotos/".$nombre_archivo,"fotos/logo-pdf2.png"))
			
					{ 
		 ## se puede dar un aviso
					} 
		 ## se puede dar otro aviso 
				}
	##################  FINALIZA SUBIR LOGO PDF #2 ######################################

	echo "<span class='fa fa-check-square-o'></span> LOS DATOS DE CONFIGURACI&Oacute;N FUERON ACTUALIZADOS EXITOSAMENTE";
	exit;
}
######################## FUNCION  ACTUALIZAR CONFIGURACION #########################

###################### FIN DE FUNCION CONFIGURACION DEL SISTEMA #######################


























################################## CLASE USUARIOS #####################################

############################## FUNCION REGISTRAR USUARIOS ##############################
public function RegistrarUsuarios()
{
	self::SetNames();
	if(empty($_POST["nombres"]) or empty($_POST["usuario"]) or empty($_POST["password"]))
	{
		echo "1";
		exit;
	}

	if($_POST["nivel"]=="ADMINISTRADOR(A) GENERAL" && $_POST["codsucursal"]!="0")
	{
		
		echo "2";
		exit;
	}

	elseif($_POST["nivel"]!="ADMINISTRADOR(A) GENERAL" && $_POST["codsucursal"]=="0")
	{
		
		echo "3";
		exit;
	}

	$sql = " SELECT dni FROM usuarios WHERE dni = ?";
	$stmt = $this->dbh->prepare($sql);
	$stmt->execute(array($_POST["dni"]));
	$num = $stmt->rowCount();
	if($num > 0)
	{
		
		echo "4";
		exit;
	}
	else
	{
		$sql = " SELECT email FROM usuarios WHERE email = ?";
		$stmt = $this->dbh->prepare($sql);
		$stmt->execute(array($_POST["email"]));
		$num = $stmt->rowCount();
		if($num > 0)
		{

			echo "5";
			exit;
		}
		else
		{
			$sql = " SELECT usuario FROM usuarios WHERE usuario = ?";
			$stmt = $this->dbh->prepare($sql);
			$stmt->execute(array($_POST["usuario"]));
			$num = $stmt->rowCount();
			if($num == 0)
			{
				$query = " INSERT INTO usuarios values (null, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);";
				$stmt = $this->dbh->prepare($query);
				$stmt->bindParam(1, $dni);
				$stmt->bindParam(2, $nombres);
				$stmt->bindParam(3, $sexo);
				$stmt->bindParam(4, $direccion);
				$stmt->bindParam(5, $telefono);
				$stmt->bindParam(6, $email);
				$stmt->bindParam(7, $usuario);
				$stmt->bindParam(8, $password);
				$stmt->bindParam(9, $nivel);
				$stmt->bindParam(10, $status);
				$stmt->bindParam(11, $comision);
				$stmt->bindParam(12, $codsucursal);

				$dni = limpiar($_POST["dni"]);
				$nombres = limpiar($_POST["nombres"]);
				$sexo = limpiar($_POST["sexo"]);
				$direccion = limpiar($_POST["direccion"]);
				$telefono = limpiar($_POST["telefono"]);
				$email = limpiar($_POST["email"]);
				$usuario = limpiar($_POST["usuario"]);
				$password = sha1(md5($_POST["password"]));
				$nivel = limpiar($_POST["nivel"]);
				$status = limpiar($_POST["status"]);
				$comision = limpiar($_POST["comision"]);
				$codsucursal = limpiar($_POST["codsucursal"]);
				$stmt->execute();

		################## SUBIR FOTO DE USUARIOS ######################################
         //datos del arhivo  
				if (isset($_FILES['imagen']['name'])) { $nombre_archivo = $_FILES['imagen']['name']; } else { $nombre_archivo =''; }
				if (isset($_FILES['imagen']['type'])) { $tipo_archivo = $_FILES['imagen']['type']; } else { $tipo_archivo =''; }
				if (isset($_FILES['imagen']['size'])) { $tamano_archivo = $_FILES['imagen']['size']; } else { $tamano_archivo =''; }  
         //compruebo si las características del archivo son las que deseo  
				if ((strpos($tipo_archivo,'image/jpeg')!==false)&&$tamano_archivo<50000) 
				{  
					if (move_uploaded_file($_FILES['imagen']['tmp_name'], "fotos/".$nombre_archivo) && rename("fotos/".$nombre_archivo,"fotos/".$_POST["dni"].".jpg"))
					{ 
		 ## se puede dar un aviso
					} 
		 ## se puede dar otro aviso 
				}
		################## FINALIZA SUBIR FOTO DE USUARIOS ##################

				echo "<span class='fa fa-check-square-o'></span> EL USUARIO HA SIDO REGISTRADO EXITOSAMENTE";
				exit;
			}
			else
			{
				echo "6";
				exit;
			}
		}
	}
}
############################# FUNCION REGISTRAR USUARIOS ###############################

############################# FUNCION LISTAR USUARIOS ################################
public function ListarUsuarios()
{
	self::SetNames();

	if ($_SESSION['acceso'] == "administradorG") {

	$sql = "SELECT * FROM usuarios LEFT JOIN sucursales ON usuarios.codsucursal = sucursales.codsucursal";
	foreach ($this->dbh->query($sql) as $row)
	{
		$this->p[] = $row;
	}
	return $this->p;
	$this->dbh=null;

     } else {

   $sql = "SELECT * FROM usuarios LEFT JOIN sucursales ON usuarios.codsucursal = sucursales.codsucursal WHERE usuarios.codsucursal = '".limpiar($_SESSION["codsucursal"])."'";
	foreach ($this->dbh->query($sql) as $row)
	{
		$this->p[] = $row;
	}
	return $this->p;
	$this->dbh=null;

     }
}
############################## FUNCION LISTAR USUARIOS ################################

########################### FUNCION LISTAR LOGS DE USUARIOS ###########################
public function ListarLogs()
{
	self::SetNames();
	$sql = "SELECT * FROM log";
	foreach ($this->dbh->query($sql) as $row)
	{
		$this->p[] = $row;
	}
	return $this->p;
	$this->dbh=null;

   }
########################### FUNCION LISTAR LOGS DE USUARIOS ###########################

############################ FUNCION ID USUARIOS #################################
public function UsuariosPorId()
{
	self::SetNames();
	$sql = "SELECT * FROM usuarios  LEFT JOIN sucursales ON usuarios.codsucursal = sucursales.codsucursal WHERE usuarios.codigo = ?";
	$stmt = $this->dbh->prepare($sql);
	$stmt->execute(array(decrypt($_GET["codigo"])));
	$num = $stmt->rowCount();
	if($num==0)
	{
		echo "";
	}
	else
	{
		if($row = $stmt->fetch(PDO::FETCH_ASSOC))
		{
			$this->p[] = $row;
		}
		return $this->p;
		$this->dbh=null;
	}
}
############################ FUNCION ID USUARIOS #################################

############################ FUNCION ACTUALIZAR USUARIOS ############################
public function ActualizarUsuarios()
{

	self::SetNames();
	if(empty($_POST["dni"]) or empty($_POST["nombres"]) or empty($_POST["usuario"]) or empty($_POST["password"]))
	{
		echo "1";
		exit;
	}

	if($_POST["nivel"]=="ADMINISTRADOR(A) GENERAL" && $_POST["codsucursal"]!="0")
	{
		
		echo "2";
		exit;
	}

	elseif($_POST["nivel"]!="ADMINISTRADOR(A) GENERAL" && $_POST["codsucursal"]=="0")
	{
		
		echo "3";
		exit;
	}

	$sql = "SELECT * FROM usuarios WHERE codigo != ? AND dni = ?";
	$stmt = $this->dbh->prepare($sql);
	$stmt->execute(array($_POST["codigo"],$_POST["dni"]));
	$num = $stmt->rowCount();
	if($num > 0)
	{
		echo "4";
		exit;
	}
	else
	{
		$sql = " SELECT email FROM usuarios WHERE codigo != ? AND email = ?";
		$stmt = $this->dbh->prepare($sql);
		$stmt->execute(array($_POST["codigo"],$_POST["email"]));
		$num = $stmt->rowCount();
		if($num > 0)
		{
			echo "5";
			exit;
		}
		else
		{
			$sql = " SELECT usuario FROM usuarios WHERE codigo != ? AND usuario = ?";
			$stmt = $this->dbh->prepare($sql);
			$stmt->execute(array($_POST["codigo"],$_POST["usuario"]));
			$num = $stmt->rowCount();
			if($num == 0)
			{
				$sql = " UPDATE usuarios set "
				." dni = ?, "
				." nombres = ?, "
				." sexo = ?, "
				." direccion = ?, "
				." telefono = ?, "
				." email = ?, "
				." usuario = ?, "
				." password = ?, "
				." nivel = ?, "
				." status = ?, "
				." comision = ?, "
				." codsucursal = ? "
				." where "
				." codigo = ?;
				";
				$stmt = $this->dbh->prepare($sql);
				$stmt->bindParam(1, $dni);
				$stmt->bindParam(2, $nombres);
				$stmt->bindParam(3, $sexo);
				$stmt->bindParam(4, $direccion);
				$stmt->bindParam(5, $telefono);
				$stmt->bindParam(6, $email);
				$stmt->bindParam(7, $usuario);
				$stmt->bindParam(8, $password);
				$stmt->bindParam(9, $nivel);
				$stmt->bindParam(10, $status);
				$stmt->bindParam(11, $comision);
				$stmt->bindParam(12, $codsucursal);
				$stmt->bindParam(13, $codigo);

				$dni = limpiar($_POST["dni"]);
				$nombres = limpiar($_POST["nombres"]);
				$sexo = limpiar($_POST["sexo"]);
				$direccion = limpiar($_POST["direccion"]);
				$telefono = limpiar($_POST["telefono"]);
				$email = limpiar($_POST["email"]);
				$usuario = limpiar($_POST["usuario"]);
				$password = sha1(md5($_POST["password"]));
				$nivel = limpiar($_POST["nivel"]);
				$status = limpiar($_POST["status"]);
				$comision = limpiar($_POST["comision"]);
				$codsucursal = limpiar($_POST["codsucursal"]);
				$codigo = limpiar($_POST["codigo"]);
				$stmt->execute();

		################## SUBIR FOTO DE USUARIOS ######################################
         //datos del arhivo  
				if (isset($_FILES['imagen']['name'])) { $nombre_archivo = $_FILES['imagen']['name']; } else { $nombre_archivo =''; }
				if (isset($_FILES['imagen']['type'])) { $tipo_archivo = $_FILES['imagen']['type']; } else { $tipo_archivo =''; }
				if (isset($_FILES['imagen']['size'])) { $tamano_archivo = $_FILES['imagen']['size']; } else { $tamano_archivo =''; }  
         //compruebo si las características del archivo son las que deseo  
				if ((strpos($tipo_archivo,'image/jpeg')!==false)&&$tamano_archivo<50000) 
				{  
					if (move_uploaded_file($_FILES['imagen']['tmp_name'], "fotos/".$nombre_archivo) && rename("fotos/".$nombre_archivo,"fotos/".$_POST["dni"].".jpg"))
					{ 
		 ## se puede dar un aviso
					} 
		 ## se puede dar otro aviso 
				}
		################## FINALIZA SUBIR FOTO DE USUARIOS ######################################

				echo "<span class='fa fa-check-square-o'></span> EL USUARIO HA SIDO ACTUALIZADO EXITOSAMENTE";
				exit;

			}
			else
			{
				echo "6";
				exit;
			}
		}
	}
}
############################ FUNCION ACTUALIZAR USUARIOS ############################

############################# FUNCION ELIMINAR USUARIOS ################################
public function EliminarUsuarios()
{
	self::SetNames();
	if ($_SESSION['acceso'] == "administradorG" || $_SESSION["acceso"]=="administradorS") {

		$sql = "SELECT codigo FROM ventas WHERE codigo = ?";
		$stmt = $this->dbh->prepare($sql);
		$stmt->execute(array(decrypt($_GET["codigo"])));
		$num = $stmt->rowCount();
		if($num == 0)
		{

			$sql = " DELETE FROM usuarios WHERE codigo = ?";
			$stmt = $this->dbh->prepare($sql);
			$stmt->bindParam(1,$codigo);
			$codigo = decrypt($_GET["codigo"]);
			$stmt->execute();

			$dni = decrypt($_GET["dni"]);
			if (file_exists("fotos/".$dni.".jpg")){
		//funcion para eliminar una carpeta con contenido
			$archivos = "fotos/".$dni.".jpg";		
			unlink($archivos);
			}

			echo "1";
			exit;

		} else {
		   
			echo "2";
			exit;
		  } 
			
		} else {
		
		echo "3";
		exit;
	 }	
}
############################## FUNCION ELIMINAR USUARIOS ##############################

######################## FUNCION BUSCAR USUARIOS POR SUCURSAL ##########################
public function BuscarUsuariosxSucursal() 
	       {
		self::SetNames();
	$sql = " SELECT * FROM usuarios INNER JOIN sucursales ON usuarios.codsucursal = sucursales.codsucursal WHERE usuarios.codsucursal = ?";
		$stmt = $this->dbh->prepare($sql);
		$stmt->execute(array(decrypt($_GET["codsucursal"])));
		$num = $stmt->rowCount();
		    if($num==0)
		{
			echo "<option value=''> -- SIN RESULTADOS -- </option>";
			exit;
		       }
		else
		{
		while($row = $stmt->fetch())
			{
				$this->p[]=$row;
			}
			return $this->p;
			$this->dbh=null;
		}
	}
######################### FUNCION BUSCAR USUARIOS POR SUCURSAL ##########################

################### FUNCION SELECCIONA USUARIO POR CODIGO Y SUCURSAL ###################
public function BuscarUsuariosxCodigo() 
	       {
		self::SetNames();
	$sql = " SELECT * FROM usuarios WHERE codigo = ? AND codsucursal = ?";
		$stmt = $this->dbh->prepare($sql);
		$stmt->execute(array($_GET["codigo"],decrypt($_GET["codsucursal"])));
		$num = $stmt->rowCount();
		    if($num==0)
		{
			echo "<option value=''> -- SIN RESULTADOS -- </option>";
			exit;
		       }
		else
		{
		while($row = $stmt->fetch())
			{
				$this->p[]=$row;
			}
			return $this->p;
			$this->dbh=null;
		}
	}
################### FUNCION SELECCIONA USUARIO POR CODIGO Y SUCURSAL ##################

############################ FIN DE CLASE USUARIOS ################################


























################################ CLASE PROVINCIAS ##################################

########################## FUNCION REGISTRAR PROVINCIAS ###############################
public function RegistrarProvincias()
{
	self::SetNames();
	if(empty($_POST["provincia"]))
	{
		echo "1";
		exit;
	}

			$sql = " SELECT provincia FROM provincias WHERE provincia = ?";
			$stmt = $this->dbh->prepare($sql);
			$stmt->execute(array($_POST["provincia"]));
			$num = $stmt->rowCount();
			if($num == 0)
			{
				$query = " INSERT INTO provincias values (null, ?);";
				$stmt = $this->dbh->prepare($query);
				$stmt->bindParam(1, $provincia);

				$provincia = limpiar($_POST["provincia"]);
				$stmt->execute();

			echo "<span class='fa fa-check-square-o'></span> LA PROVINCIA HA SIDO REGISTRADA EXITOSAMENTE";
			exit;

			} else {

			echo "2";
			exit;
	    }
}
############################ FUNCION REGISTRAR PROVINCIAS ############################

############################ FUNCION LISTAR PROVINCIAS ################################
public function ListarProvincias()
{
	self::SetNames();
	$sql = "SELECT * FROM provincias";
	foreach ($this->dbh->query($sql) as $row)
	{
		$this->p[] = $row;
	}
	return $this->p;
	$this->dbh=null;
 }
########################### FUNCION LISTAR PROVINCIAS ################################

########################### FUNCION ID PROVINCIAS #################################
public function ProvinciasPorId()
{
	self::SetNames();
	$sql = "SELECT * FROM provincias WHERE id_provincia = ?";
	$stmt = $this->dbh->prepare($sql);
	$stmt->execute(array(decrypt($_GET["id_provincia"])));
	$num = $stmt->rowCount();
	if($num==0)
	{
		echo "";
	}
	else
	{
		if($row = $stmt->fetch(PDO::FETCH_ASSOC))
		{
			$this->p[] = $row;
		}
		return $this->p;
		$this->dbh=null;
	}
}
############################ FUNCION ID PROVINCIAS #################################

############################ FUNCION ACTUALIZAR PROVINCIAS ############################
public function ActualizarProvincias()
{

	self::SetNames();
	if(empty($_POST["id_provincia"]) or empty($_POST["provincia"]))
	{
		echo "1";
		exit;
	}

			$sql = " SELECT provincia FROM provincias WHERE id_provincia != ? AND provincia = ?";
			$stmt = $this->dbh->prepare($sql);
			$stmt->execute(array($_POST["id_provincia"],$_POST["provincia"]));
			$num = $stmt->rowCount();
			if($num == 0)
			{
				$sql = " UPDATE provincias set "
				." provincia = ? "
				." where "
				." id_provincia = ?;
				";
				$stmt = $this->dbh->prepare($sql);
				$stmt->bindParam(1, $provincia);
				$stmt->bindParam(2, $id_provincia);

				$provincia = limpiar($_POST["provincia"]);
				$id_provincia = limpiar($_POST['id_provincia']);
				$stmt->execute();

			echo "<span class='fa fa-check-square-o'></span> LA PROVINCIA HA SIDO ACTUALIZADA EXITOSAMENTE";
			exit;

			} else {

			echo "2";
			exit;
		}
}
############################ FUNCION ACTUALIZAR PROVINCIAS ############################

############################ FUNCION ELIMINAR PROVINCIAS ############################
public function EliminarProvincias()
{
	self::SetNames();
	if($_SESSION['acceso'] == "administradorG" || $_SESSION["acceso"]=="administradorS") {

		$sql = "SELECT id_provincia FROM departamentos WHERE id_provincia = ?";
		$stmt = $this->dbh->prepare($sql);
		$stmt->execute(array(decrypt($_GET["id_provincia"])));
		$num = $stmt->rowCount();
		if($num == 0)
		{

			$sql = "DELETE FROM provincias WHERE id_provincia = ?";
			$stmt = $this->dbh->prepare($sql);
			$stmt->bindParam(1,$id_provincia);
			$id_provincia = decrypt($_GET["id_provincia"]);
			$stmt->execute();

			echo "1";
			exit;

		} else {
		   
			echo "2";
			exit;
		  } 
			
		} else {
		
		echo "3";
		exit;
	 }	
}
############################ FUNCION ELIMINAR PROVINCIAS ##############################

############################## FIN DE CLASE PROVINCIAS ################################


























############################### CLASE DEPARTAMENTOS ################################

############################# FUNCION REGISTRAR DEPARTAMENTOS ###########################
public function RegistrarDepartamentos()
{
	self::SetNames();
	if(empty($_POST["departamento"]) or empty($_POST["id_provincia"]))
	{
		echo "1";
		exit;
	}

			$sql = " SELECT departamento FROM departamentos WHERE departamento = ? AND id_provincia = ?";
			$stmt = $this->dbh->prepare($sql);
			$stmt->execute(array($_POST["departamento"],$_POST["id_provincia"]));
			$num = $stmt->rowCount();
			if($num == 0)
			{
				$query = " INSERT INTO departamentos values (null, ?, ?);";
				$stmt = $this->dbh->prepare($query);
				$stmt->bindParam(1, $departamento);
				$stmt->bindParam(2, $id_provincia);

				$departamento = limpiar($_POST["departamento"]);
				$id_provincia = limpiar($_POST['id_provincia']);
				$stmt->execute();

		echo "<span class='fa fa-check-square-o'></span> EL DEPARTAMENTO HA SIDO REGISTRADO EXITOSAMENTE";
			exit;

			} else {

			echo "2";
			exit;
	    }
}
########################### FUNCION REGISTRAR DEPARTAMENTOS ########################

########################## FUNCION PARA LISTAR DEPARTAMENTOS ##########################
	public function ListarDepartamentos()
	{
		self::SetNames();
		$sql = "SELECT * FROM departamentos LEFT JOIN provincias ON departamentos.id_provincia = provincias.id_provincia";
		foreach ($this->dbh->query($sql) as $row)
		{
			$this->p[] = $row;
		}
		return $this->p;
		$this->dbh=null;
	}
######################### FUNCION PARA LISTAR DEPARTAMENTOS ##########################

###################### FUNCION LISTAR DEPARTAMENTOS POR PROVINCIAS #####################
	public function ListarDepartamentoXProvincias() 
	       {
		self::SetNames();
		$sql = "SELECT * FROM departamentos WHERE id_provincia = ?";
		$stmt = $this->dbh->prepare($sql);
		$stmt->execute(array($_GET["id_provincia"]));
		$num = $stmt->rowCount();
		    if($num==0)
		{
			echo "<option value='0' selected> -- SIN RESULTADOS -- </option>";
			exit;
		}
		else
		{
		while($row = $stmt->fetch())
			{
				$this->p[]=$row;
			}
			return $this->p;
			$this->dbh=null;
		}
	}
##################### FUNCION LISTAR DEPARTAMENTOS POR PROVINCIAS ######################

################# FUNCION PARA SELECCIONAR DEPARTAMENTOS POR PROVINCIA #################
	public function SeleccionaDepartamento()
	{
		self::SetNames();
		$sql = "SELECT * FROM departamentos WHERE id_provincia = ?";
		$stmt = $this->dbh->prepare($sql);
		$stmt->execute(array($_GET["id_provincia"]));
		$num = $stmt->rowCount();
		if($num==0)
		{
			echo "<option value=''> -- SIN RESULTADOS -- </option>";
			exit;
		}
		else
		{
			while($row = $stmt->fetch())
			{
				$this->p[]=$row;
			}
			return $this->p;
			$this->dbh=null;
		}
	}
################# FUNCION PARA SELECCIONAR DEPARTAMENTOS POR PROVINCIA ################

############################ FUNCION ID DEPARTAMENTOS #################################
public function DepartamentosPorId()
{
	self::SetNames();
	$sql = "SELECT * FROM departamentos LEFT JOIN provincias ON departamentos.id_provincia = provincias.id_provincia WHERE departamentos.id_provincia = ?";
	$stmt = $this->dbh->prepare($sql);
	$stmt->execute(array(decrypt($_GET["id_provincia"])));
	$num = $stmt->rowCount();
	if($num==0)
	{
		echo "";
	}
	else
	{
		if($row = $stmt->fetch(PDO::FETCH_ASSOC))
		{
			$this->p[] = $row;
		}
		return $this->p;
		$this->dbh=null;
	}
}
############################ FUNCION ID DEPARTAMENTOS #################################

######################## FUNCION ACTUALIZAR DEPARTAMENTOS ############################
public function ActualizarDepartamentos()
{
	self::SetNames();
	if(empty($_POST["id_departamento"]) or empty($_POST["departamento"]) or empty($_POST["id_provincia"]))
	{
		echo "1";
		exit;
	}

			$sql = "SELECT departamento FROM departamentos WHERE id_departamento != ? AND departamento = ? AND id_provincia = ?";
			$stmt = $this->dbh->prepare($sql);
			$stmt->execute(array($_POST["id_departamento"],$_POST["departamento"],$_POST["id_provincia"]));
			$num = $stmt->rowCount();
			if($num == 0)
			{
				$sql = " UPDATE departamentos set "
				." departamento = ?, "
				." id_provincia = ? "
				." where "
				." id_departamento = ?;
				";
				$stmt = $this->dbh->prepare($sql);
				$stmt->bindParam(1, $departamento);
				$stmt->bindParam(2, $id_provincia);
				$stmt->bindParam(3, $id_departamento);

				$departamento = limpiar($_POST["departamento"]);
				$id_provincia = limpiar($_POST['id_provincia']);
				$id_departamento = limpiar($_POST['id_departamento']);
				$stmt->execute();

		echo "<span class='fa fa-check-square-o'></span> EL DEPARTAMENTO HA SIDO ACTUALIZADO EXITOSAMENTE";
			exit;

			} else {

			echo "2";
			exit;
		}
}
############################ FUNCION ACTUALIZAR DEPARTAMENTOS #######################

############################ FUNCION ELIMINAR DEPARTAMENTOS ###########################
public function EliminarDepartamentos()
{
	self::SetNames();
	if($_SESSION['acceso'] == "administradorG" || $_SESSION["acceso"]=="administradorS") {

		$sql = "SELECT id_departamento FROM configuracion WHERE id_departamento = ?";
		$stmt = $this->dbh->prepare($sql);
		$stmt->execute(array(decrypt($_GET["id_departamento"])));
		$num = $stmt->rowCount();
		if($num == 0)
		{

			$sql = "DELETE FROM departamentos WHERE id_departamento = ?";
			$stmt = $this->dbh->prepare($sql);
			$stmt->bindParam(1,$id_departamento);
			$id_departamento = decrypt($_GET["id_departamento"]);
			$stmt->execute();

			echo "1";
			exit;

		} else {
		   
			echo "2";
			exit;
		  } 
			
		} else {
		
		echo "3";
		exit;
	 }	
}
########################### FUNCION ELIMINAR DEPARTAMENTOS ############################

############################## FIN DE CLASE DEPARTAMENTOS ##############################


























################################ CLASE TIPOS DE DOCUMENTOS ##############################

########################### FUNCION REGISTRAR TIPO DE DOCUMENTOS ########################
public function RegistrarDocumentos()
{
	self::SetNames();
	if(empty($_POST["documento"]) or empty($_POST["descripcion"]))
	{
		echo "1";
		exit;
	}

			$sql = " SELECT * FROM documentos WHERE documento = ?";
			$stmt = $this->dbh->prepare($sql);
			$stmt->execute(array($_POST["documento"]));
			$num = $stmt->rowCount();
			if($num == 0)
			{
				$query = " INSERT INTO documentos values (null, ?, ?);";
				$stmt = $this->dbh->prepare($query);
				$stmt->bindParam(1, $documento);
				$stmt->bindParam(2, $descripcion);

				$documento = limpiar($_POST["documento"]);
				$descripcion = limpiar($_POST["descripcion"]);
				$stmt->execute();

			echo "<span class='fa fa-check-square-o'></span> EL TIPO DE DOCUMENTO HA SIDO REGISTRADO EXITOSAMENTE";
			exit;

			} else {

			echo "2";
			exit;
	    }
}
############################ FUNCION REGISTRAR TIPO DE MONEDA ########################

########################## FUNCION LISTAR TIPO DE MONEDA ################################
public function ListarDocumentos()
{
	self::SetNames();
	$sql = "SELECT * FROM documentos ORDER BY documento ASC";
	foreach ($this->dbh->query($sql) as $row)
	{
		$this->p[] = $row;
	}
	return $this->p;
	$this->dbh=null;
 }
######################### FUNCION LISTAR TIPO DE DOCUMENTOS ##########################

######################### FUNCION ID TIPO DE DOCUMENTOS ###############################
public function DocumentoPorId()
{
	self::SetNames();
	$sql = "SELECT * FROM documentos WHERE coddocumento = ?";
	$stmt = $this->dbh->prepare($sql);
	$stmt->execute(array(decrypt($_GET["coddocumento"])));
	$num = $stmt->rowCount();
	if($num==0)
	{
		echo "";
	}
	else
	{
		if($row = $stmt->fetch(PDO::FETCH_ASSOC))
		{
			$this->p[] = $row;
		}
		return $this->p;
		$this->dbh=null;
	}
}
########################## FUNCION ID TIPO DE DOCUMENTOS #########################

######################### FUNCION ACTUALIZAR TIPO DE DOCUMENTOS ########################
public function ActualizarDocumentos()
{

	self::SetNames();
	if(empty($_POST["coddocumento"]) or empty($_POST["documento"]) or empty($_POST["descripcion"]))
	{
		echo "1";
		exit;
	}

			$sql = " SELECT documento FROM documentos WHERE coddocumento != ? AND documento = ?";
			$stmt = $this->dbh->prepare($sql);
			$stmt->execute(array($_POST["coddocumento"],$_POST["documento"]));
			$num = $stmt->rowCount();
			if($num == 0)
			{
				$sql = " UPDATE documentos set "
				." documento = ?, "
				." descripcion = ? "
				." where "
				." coddocumento = ?;
				";
				$stmt = $this->dbh->prepare($sql);
				$stmt->bindParam(1, $documento);
				$stmt->bindParam(2, $descripcion);
				$stmt->bindParam(3, $coddocumento);

				$documento = limpiar($_POST["documento"]);
				$descripcion = limpiar($_POST["descripcion"]);
				$coddocumento = limpiar($_POST["coddocumento"]);
				$stmt->execute();

			echo "<span class='fa fa-check-square-o'></span> EL TIPO DE DOCUMENTO HA SIDO ACTUALIZADO EXITOSAMENTE";
			exit;

			} else {

			echo "2";
			exit;
		}
}
####################### FUNCION ACTUALIZAR TIPO DE DOCUMENTOS #######################

######################### FUNCION ELIMINAR TIPO DE DOCUMENTOS #########################
public function EliminarDocumentos()
{
	self::SetNames();
	if ($_SESSION['acceso'] == "administradorG" || $_SESSION["acceso"]=="administradorS") {

		$sql = "SELECT documsucursal FROM sucursales WHERE documsucursal = ?";
		$stmt = $this->dbh->prepare($sql);
		$stmt->execute(array(decrypt($_GET["coddocumento"])));
		$num = $stmt->rowCount();
		if($num == 0)
		{

			$sql = "DELETE FROM documentos WHERE coddocumento = ?";
			$stmt = $this->dbh->prepare($sql);
			$stmt->bindParam(1,$coddocumento);
			$coddocumento = decrypt($_GET["coddocumento"]);
			$stmt->execute();

			echo "1";
			exit;

		} else {
		   
			echo "2";
			exit;
		  } 
			
		} else {
		
		echo "3";
		exit;
	 }	
}
######################## FUNCION ELIMINAR TIPOS DE DOCUMENTOS ###########################

########################### FIN DE CLASE TIPOS DE DOCUMENTOS ###########################



























############################### CLASE TIPOS DE MONEDAS ##############################

############################ FUNCION REGISTRAR TIPO DE MONEDA ##########################
public function RegistrarTipoMoneda()
{
	self::SetNames();
	if(empty($_POST["moneda"]) or empty($_POST["moneda"]) or empty($_POST["simbolo"]))
	{
		echo "1";
		exit;
	}

			$sql = " SELECT * FROM tiposmoneda WHERE moneda = ?";
			$stmt = $this->dbh->prepare($sql);
			$stmt->execute(array($_POST["moneda"]));
			$num = $stmt->rowCount();
			if($num == 0)
			{
				$query = " INSERT INTO tiposmoneda values (null, ?, ?, ?);";
				$stmt = $this->dbh->prepare($query);
				$stmt->bindParam(1, $moneda);
				$stmt->bindParam(2, $siglas);
				$stmt->bindParam(3, $simbolo);

				$moneda = limpiar($_POST["moneda"]);
				$siglas = limpiar($_POST["siglas"]);
				$simbolo = limpiar($_POST["simbolo"]);
				$stmt->execute();

			echo "<span class='fa fa-check-square-o'></span> EL TIPO DE MONEDA HA SIDO REGISTRADO EXITOSAMENTE";
			exit;

			} else {

			echo "2";
			exit;
	    }
}
######################### FUNCION REGISTRAR TIPO DE MONEDA #######################

########################## FUNCION LISTAR TIPO DE MONEDA ################################
public function ListarTipoMoneda()
{
	self::SetNames();
	$sql = "SELECT * FROM tiposmoneda";
	foreach ($this->dbh->query($sql) as $row)
	{
		$this->p[] = $row;
	}
	return $this->p;
	$this->dbh=null;
 }
########################### FUNCION LISTAR TIPO DE MONEDA #########################

############################ FUNCION ID TIPO DE MONEDA #################################
public function TipoMonedaPorId()
{
	self::SetNames();
	$sql = "SELECT * FROM tiposmoneda WHERE codmoneda = ?";
	$stmt = $this->dbh->prepare($sql);
	$stmt->execute(array(decrypt($_GET["codmoneda"])));
	$num = $stmt->rowCount();
	if($num==0)
	{
		echo "";
	}
	else
	{
		if($row = $stmt->fetch(PDO::FETCH_ASSOC))
		{
			$this->p[] = $row;
		}
		return $this->p;
		$this->dbh=null;
	}
}
############################ FUNCION ID TIPO DE MONEDA #################################

####################### FUNCION ACTUALIZAR TIPO DE MONEDA ###########################
public function ActualizarTipoMoneda()
{

	self::SetNames();
	if(empty($_POST["codmoneda"]) or empty($_POST["moneda"]) or empty($_POST["siglas"]) or empty($_POST["simbolo"]))
	{
		echo "1";
		exit;
	}

			$sql = " SELECT moneda FROM tiposmoneda WHERE codmoneda != ? AND moneda = ?";
			$stmt = $this->dbh->prepare($sql);
			$stmt->execute(array($_POST["codmoneda"],$_POST["moneda"]));
			$num = $stmt->rowCount();
			if($num == 0)
			{
				$sql = " UPDATE tiposmoneda set "
				." moneda = ?, "
				." siglas = ?, "
				." simbolo = ? "
				." where "
				." codmoneda = ?;
				";
				$stmt = $this->dbh->prepare($sql);
				$stmt->bindParam(1, $moneda);
				$stmt->bindParam(2, $siglas);
				$stmt->bindParam(3, $simbolo);
				$stmt->bindParam(4, $codmoneda);

				$moneda = limpiar($_POST["moneda"]);
				$siglas = limpiar($_POST["siglas"]);
				$simbolo = limpiar($_POST["simbolo"]);
				$codmoneda = limpiar($_POST["codmoneda"]);
				$stmt->execute();

			echo "<span class='fa fa-check-square-o'></span> EL TIPO DE MONEDA HA SIDO ACTUALIZADO EXITOSAMENTE";
			exit;

			} else {

			echo "2";
			exit;
		}
}
######################## FUNCION ACTUALIZAR TIPO DE MONEDA ############################

######################### FUNCION ELIMINAR TIPO DE MONEDA ###########################
public function EliminarTipoMoneda()
{
	self::SetNames();
	if ($_SESSION['acceso'] == "administradorG" || $_SESSION["acceso"]=="administradorS") {

		$sql = "SELECT codmoneda FROM tiposcambio WHERE codmoneda = ?";
		$stmt = $this->dbh->prepare($sql);
		$stmt->execute(array(decrypt($_GET["codmoneda"])));
		$num = $stmt->rowCount();
		if($num == 0)
		{

			$sql = "DELETE FROM tiposmoneda WHERE codmoneda = ?";
			$stmt = $this->dbh->prepare($sql);
			$stmt->bindParam(1,$codmoneda);
			$codmoneda = decrypt($_GET["codmoneda"]);
			$stmt->execute();

			echo "1";
			exit;

		} else {
		   
			echo "2";
			exit;
		  } 
			
		} else {
		
		echo "3";
		exit;
	 }	
}
########################### FUNCION ELIMINAR TIPOS DE MONEDAS ########################

##################### FUNCION BUSCAR TIPOS DE CAMBIOS POR MONEDA #######################
public function BuscarTiposCambios()
{
	self::SetNames();
	$sql = "SELECT * FROM tiposmoneda INNER JOIN tiposcambio ON tiposmoneda.codmoneda = tiposcambio.codmoneda WHERE tiposcambio.codmoneda = ? ORDER BY tiposcambio.codcambio DESC LIMIT 1";
	$stmt = $this->dbh->prepare($sql);
	$stmt->execute(array(decrypt($_GET["codmoneda"])));
	$num = $stmt->rowCount();
	if($num==0)
	{
		echo "<center><div class='alert alert-danger'>";
		echo "<button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>";
		echo "<span class='fa fa-info-circle'></span> NO SE ENCONTRARON TIPOS DE CAMBIO PARA LA MONEDA SELECCIONADA</div></center>";
		exit;
	}
	else
	{
		if($row = $stmt->fetch(PDO::FETCH_ASSOC))
		{
			$this->p[] = $row;
		}
		return $this->p;
		$this->dbh=null;
	}
}
##################### FUNCION BUSCAR TIPOS DE CAMBIOS POR MONEDA #####################

############################# FIN DE CLASE TIPOS DE MONEDAS #############################
























############################## CLASE TIPOS DE CAMBIOS ################################

########################## FUNCION REGISTRAR TIPO DE CAMBIO #########################
public function RegistrarTipoCambio()
{
	self::SetNames();
	if(empty($_POST["descripcioncambio"]) or empty($_POST["montocambio"]) or empty($_POST["codmoneda"]) or empty($_POST["fechacambio"]))
	{
		echo "1";
		exit;
	}
			
		$sql = "SELECT codmoneda, fechacambio FROM tiposcambio WHERE codmoneda = ? AND fechacambio = ?";
		$stmt = $this->dbh->prepare($sql);
		$stmt->execute(array($_POST["codmoneda"],date("Y-m-d",strtotime($_POST['fechacambio']))));
		$num = $stmt->rowCount();
		if($num == 0)
		{
			$query = "INSERT INTO tiposcambio values (null, ?, ?, ?, ?); ";
			$stmt = $this->dbh->prepare($query);
			$stmt->bindParam(1, $descripcioncambio);
			$stmt->bindParam(2, $montocambio);
			$stmt->bindParam(3, $codmoneda);
			$stmt->bindParam(4, $fechacambio);

			$descripcioncambio = limpiar($_POST["descripcioncambio"]);
			$montocambio = number_format($_POST["montocambio"], 3, '.', '');
			$codmoneda = limpiar($_POST["codmoneda"]);
			$fechacambio = limpiar(date("Y-m-d",strtotime($_POST['fechacambio'])));
			$stmt->execute();

			echo "<span class='fa fa-check-square-o'></span> EL TIPO DE CAMBIO HA SIDO REGISTRADO EXITOSAMENTE";
			exit;

			} else {

			echo "2";
			exit;
	    }
}
######################### FUNCION REGISTRAR TIPO DE CAMBIO ########################

########################### FUNCION LISTAR TIPO DE CAMBIO ########################
public function ListarTipoCambio()
{
	self::SetNames();
	$sql = "SELECT * FROM tiposcambio INNER JOIN tiposmoneda ON tiposcambio.codmoneda = tiposmoneda.codmoneda";
	foreach ($this->dbh->query($sql) as $row)
	{
		$this->p[] = $row;
	}
	return $this->p;
	$this->dbh=null;
 }
######################### FUNCION LISTAR TIPO DE CAMBIO ################################

######################## FUNCION ID TIPO DE CAMBIO #################################
public function TipoCambioPorId()
{
	self::SetNames();
	$sql = "SELECT * FROM tiposcambio INNER JOIN tiposmoneda ON tiposcambio.codmoneda = tiposmoneda.codmoneda WHERE tiposcambio.codcambio = ?";
	$stmt = $this->dbh->prepare($sql);
	$stmt->execute(array(decrypt($_GET["codcambio"])));
	$num = $stmt->rowCount();
	if($num==0)
	{
		echo "";
	}
	else
	{
		if($row = $stmt->fetch(PDO::FETCH_ASSOC))
		{
			$this->p[] = $row;
		}
		return $this->p;
		$this->dbh=null;
	}
}
############################ FUNCION ID TIPO DE CAMBIO #################################

####################### FUNCION ACTUALIZAR TIPO DE CAMBIO ############################
public function ActualizarTipoCambio()
{
	self::SetNames();
	if(empty($_POST["codcambio"])or empty($_POST["descripcioncambio"]) or empty($_POST["montocambio"]) or empty($_POST["codmoneda"]) or empty($_POST["fechacambio"]))
	{
		echo "1";
		exit;
	}
			
		$sql = "SELECT codmoneda, fechacambio FROM tiposcambio WHERE codcambio != ? AND codmoneda = ? AND fechacambio = ?";
		$stmt = $this->dbh->prepare($sql);
		$stmt->execute(array($_POST["codcambio"],$_POST["codmoneda"],date("Y-m-d",strtotime($_POST['fechacambio']))));
		$num = $stmt->rowCount();
		if($num == 0)
		{
			$sql = "UPDATE tiposcambio set "
			." descripcioncambio = ?, "
			." montocambio = ?, "
			." codmoneda = ?, "
			." fechacambio = ? "
			." where "
			." codcambio = ?;
			";
			$stmt = $this->dbh->prepare($sql);
			$stmt->bindParam(1, $descripcioncambio);
			$stmt->bindParam(2, $montocambio);
			$stmt->bindParam(3, $codmoneda);
			$stmt->bindParam(4, $fechacambio);
			$stmt->bindParam(5, $codcambio);

			$descripcioncambio = limpiar($_POST["descripcioncambio"]);
			$montocambio = number_format($_POST["montocambio"], 3, '.', '');
			$codmoneda = limpiar($_POST["codmoneda"]);
			$fechacambio = limpiar(date("Y-m-d",strtotime($_POST['fechacambio'])));
			$codcambio = limpiar($_POST["codcambio"]);
			$stmt->execute();

			echo "<span class='fa fa-check-square-o'></span> EL TIPO DE CAMBIO HA SIDO ACTUALIZADO EXITOSAMENTE";
			exit;

			} else {

			echo "2";
			exit;
	    }
}
###################### FUNCION ACTUALIZAR TIPO DE CAMBIO ############################

########################## FUNCION ELIMINAR TIPO DE CAMBIO ###########################
public function EliminarTipoCambio()
{
	self::SetNames();
		if ($_SESSION['acceso'] == "administradorG" || $_SESSION["acceso"]=="administradorS") {

		    $sql = "DELETE FROM tiposcambio WHERE codcambio = ?";
			$stmt = $this->dbh->prepare($sql);
			$stmt->bindParam(1,$codcambio);
			$codcambio = decrypt($_GET["codcambio"]);
			$stmt->execute();

			echo "1";
			exit;

		} else {
		   
			echo "2";
			exit;
		} 
}
########################### FUNCION ELIMINAR TIPO DE CAMBIO ###########################

######################## FUNCION BUSCAR PRODUCTOS POR MONEDA ###########################
public function MonedaProductoId()
{
	self::SetNames();
	if($_SESSION['acceso'] == "administradorG") {

	$sql = "SELECT sucursales.codmoneda, tiposmoneda.moneda, tiposmoneda.siglas, tiposmoneda.simbolo, tiposcambio.montocambio 
	FROM tiposmoneda 
	INNER JOIN sucursales ON tiposmoneda.codmoneda = sucursales.codmoneda
	INNER JOIN tiposcambio ON tiposmoneda.codmoneda = tiposcambio.codmoneda 
	WHERE sucursales.codsucursal = ? ORDER BY tiposcambio.codcambio DESC LIMIT 1";
	$stmt = $this->dbh->prepare($sql);
	$stmt->execute(array(decrypt($_GET["codsucursal"])));
	$num = $stmt->rowCount();
	if($num==0)
	{
		echo "";
	}
	else
	{
		if($row = $stmt->fetch(PDO::FETCH_ASSOC))
		{
			$this->p[] = $row;
		}
		return $this->p;
		$this->dbh=null;
	   }

	} else {

	$sql = "SELECT sucursales.codmoneda, tiposmoneda.moneda, tiposmoneda.siglas, tiposmoneda.simbolo, tiposcambio.montocambio 
	FROM sucursales 
	INNER JOIN tiposmoneda ON sucursales.codmoneda = tiposmoneda.codmoneda
	INNER JOIN tiposcambio ON tiposmoneda.codmoneda = tiposcambio.codmoneda 
	WHERE sucursales.codsucursal = ? ORDER BY tiposcambio.codcambio DESC LIMIT 1";
	$stmt = $this->dbh->prepare($sql);
	$stmt->execute(array($_SESSION["codsucursal"]));
	$num = $stmt->rowCount();
	if($num==0)
	{
		echo "";
	}
	else
	{
		if($row = $stmt->fetch(PDO::FETCH_ASSOC))
		{
			$this->p[] = $row;
		}
		return $this->p;
		$this->dbh=null;
	   }
	}
}
###################### FUNCION BUSCAR PRODUCTOS POR MONEDA ##########################

############################ FIN DE CLASE TIPOS DE CAMBIOS #############################


























################################# CLASE MEDIOS DE PAGOS ################################

########################### FUNCION REGISTRAR MEDIOS DE PAGOS ###########################
public function RegistrarMediosPagos()
{
	self::SetNames();
	if(empty($_POST["mediopago"]))
	{
		echo "1";
		exit;
	}

			$sql = " SELECT mediopago FROM mediospagos WHERE mediopago = ?";
			$stmt = $this->dbh->prepare($sql);
			$stmt->execute(array($_POST["mediopago"]));
			$num = $stmt->rowCount();
			if($num == 0)
			{
				$query = " INSERT INTO mediospagos values (null, ?);";
				$stmt = $this->dbh->prepare($query);
				$stmt->bindParam(1, $mediopago);

				$mediopago = limpiar($_POST["mediopago"]);
				$stmt->execute();

			echo "<span class='fa fa-check-square-o'></span> EL MEDIO DE PAGO HA SIDO REGISTRADO EXITOSAMENTE";
			exit;

			} else {

			echo "2";
			exit;
	    }
}
############################ FUNCION REGISTRAR MEDIOS DE PAGOS ##########################

########################## FUNCION LISTAR MEDIOS DE PAGOS ##########################
public function ListarMediosPagos()
{
	self::SetNames();
	$sql = "SELECT * FROM mediospagos";
	foreach ($this->dbh->query($sql) as $row)
	{
		$this->p[] = $row;
	}
	return $this->p;
	$this->dbh=null;
 }
########################### FUNCION LISTAR MEDIOS DE PAGOS ##########################

############################ FUNCION ID MEDIOS DE PAGOS #################################
public function MediosPagosPorId()
{
	self::SetNames();
	$sql = "SELECT * FROM mediospagos WHERE codmediopago = ?";
	$stmt = $this->dbh->prepare($sql);
	$stmt->execute(array(decrypt($_GET["codmediopago"])));
	$num = $stmt->rowCount();
	if($num==0)
	{
		echo "";
	}
	else
	{
		if($row = $stmt->fetch(PDO::FETCH_ASSOC))
		{
			$this->p[] = $row;
		}
		return $this->p;
		$this->dbh=null;
	}
}
############################ FUNCION ID MEDIOS DE PAGOS #################################

##################### FUNCION ACTUALIZAR MEDIOS DE PAGOS ############################
public function ActualizarMediosPagos()
{
	self::SetNames();
	if(empty($_POST["codmediopago"]) or empty($_POST["mediopago"]))
	{
		echo "1";
		exit;
	}

			$sql = " SELECT mediopago FROM mediospagos WHERE codmediopago != ? AND mediopago = ?";
			$stmt = $this->dbh->prepare($sql);
			$stmt->execute(array($_POST["codmediopago"],$_POST["mediopago"]));
			$num = $stmt->rowCount();
			if($num == 0)
			{
				$sql = " UPDATE mediospagos set "
				." mediopago = ? "
				." where "
				." codmediopago = ?;
				";
				$stmt = $this->dbh->prepare($sql);
				$stmt->bindParam(1, $mediopago);
				$stmt->bindParam(2, $codmediopago);

				$mediopago = limpiar($_POST["mediopago"]);
				$codmediopago = limpiar($_POST["codmediopago"]);
				$stmt->execute();

			echo "<span class='fa fa-check-square-o'></span> EL MEDIO DE PAGO HA SIDO ACTUALIZADO EXITOSAMENTE";
			exit;

			} else {

			echo "2";
			exit;
		}
}
##################### FUNCION ACTUALIZAR MEDIOS DE PAGOS ############################

########################## FUNCION ELIMINAR MEDIOS DE PAGOS #########################
public function EliminarMediosPagos()
{
	self::SetNames();
		if ($_SESSION['acceso'] == "administradorG" || $_SESSION["acceso"]=="administradorS") {

		$sql = "SELECT formapago FROM ventas WHERE formapago = ?";
		$stmt = $this->dbh->prepare($sql);
		$stmt->execute(array(decrypt($_GET["codmediopago"])));
		$num = $stmt->rowCount();
		if($num == 0)
		{

			$sql = "DELETE FROM mediospagos WHERE codmediopago = ?";
			$stmt = $this->dbh->prepare($sql);
			$stmt->bindParam(1,$codmediopago);
			$codmediopago = decrypt($_GET["codmediopago"]);
			$stmt->execute();

			echo "1";
			exit;

		} else {
		   
			echo "2";
			exit;
		  } 
			
		} else {
		
		echo "3";
		exit;
	 }	
}
########################## FUNCION ELIMINAR MEDIOS DE PAGOS ###########################

############################ FIN DE CLASE MEDIOS DE PAGOS ##############################

























############################### CLASE IMPUESTOS ####################################

############################ FUNCION REGISTRAR IMPUESTOS ###############################
public function RegistrarImpuestos()
{
	self::SetNames();
	if(empty($_POST["nomimpuesto"]) or empty($_POST["valorimpuesto"]) or empty($_POST["statusimpuesto"]))
	{
		echo "1";
		exit;
	}

			$sql = " SELECT statusimpuesto FROM impuestos WHERE nomimpuesto != ? AND statusimpuesto = ? AND statusimpuesto = 'ACTIVO'";
			$stmt = $this->dbh->prepare($sql);
			$stmt->execute(array($_POST["nomimpuesto"],$_POST["statusimpuesto"]));
			$num = $stmt->rowCount();
			if($num>0)
			{
				echo "2";
				exit;
			}
			else
			{

			$sql = " SELECT nomimpuesto FROM impuestos WHERE nomimpuesto = ?";
			$stmt = $this->dbh->prepare($sql);
			$stmt->execute(array($_POST["nomimpuesto"]));
			$num = $stmt->rowCount();
			if($num == 0)
			{
				$query = " INSERT INTO impuestos values (null, ?, ?, ?, ?);";
				$stmt = $this->dbh->prepare($query);
				$stmt->bindParam(1, $nomimpuesto);
				$stmt->bindParam(2, $valorimpuesto);
				$stmt->bindParam(3, $statusimpuesto);
				$stmt->bindParam(4, $fechaimpuesto);

				$nomimpuesto = limpiar($_POST["nomimpuesto"]);
				$valorimpuesto = limpiar($_POST["valorimpuesto"]);
				$statusimpuesto = limpiar($_POST["statusimpuesto"]);
				$fechaimpuesto = limpiar(date("Y-m-d",strtotime($_POST['fechaimpuesto'])));
				$stmt->execute();

		echo "<span class='fa fa-check-square-o'></span> EL IMPUESTO HA SIDO REGISTRADO  EXITOSAMENTE";
			exit;

			} else {

			echo "3";
			exit;
	    }
	}
}
############################ FUNCION REGISTRAR IMPUESTOS ###############################

############################# FUNCION LISTAR IMPUESTOS ################################
public function ListarImpuestos()
{
	self::SetNames();
	$sql = "SELECT * FROM impuestos";
	foreach ($this->dbh->query($sql) as $row)
	{
		$this->p[] = $row;
	}
	return $this->p;
	$this->dbh=null;
 }
############################# FUNCION LISTAR IMPUESTOS ################################

############################ FUNCION ID IMPUESTOS #################################
public function ImpuestosPorId()
{
	self::SetNames();
	$sql = "SELECT * FROM impuestos WHERE statusimpuesto = ?";
	$stmt = $this->dbh->prepare($sql);
	$stmt->execute(array("ACTIVO"));
	$num = $stmt->rowCount();
	if($row = $stmt->fetch(PDO::FETCH_ASSOC))
		{
			$this->p[] = $row;
		}
		return $this->p;
		$this->dbh=null;
}
############################ FUNCION ID IMPUESTOS #################################

############################ FUNCION ACTUALIZAR IMPUESTOS ############################
public function ActualizarImpuestos()
{

	self::SetNames();
	if(empty($_POST["codimpuesto"]) or empty($_POST["nomimpuesto"]) or empty($_POST["valorimpuesto"]) or empty($_POST["statusimpuesto"]))
	{
		echo "1";
		exit;
	}

			$sql = " SELECT statusimpuesto FROM impuestos WHERE codimpuesto != ? AND statusimpuesto = ? AND statusimpuesto = 'ACTIVO'";
			$stmt = $this->dbh->prepare($sql);
			$stmt->execute(array($_POST["codimpuesto"],$_POST["statusimpuesto"]));
			$num = $stmt->rowCount();
			if($num>0)
			{
				echo "2";
				exit;
			}
			else
			{

			$sql = "SELECT nomimpuesto FROM impuestos WHERE codimpuesto != ? AND nomimpuesto = ?";
			$stmt = $this->dbh->prepare($sql);
			$stmt->execute(array($_POST["codimpuesto"],$_POST["nomimpuesto"]));
			$num = $stmt->rowCount();
			if($num == 0)
			{
				$sql = " UPDATE impuestos set "
				." nomimpuesto = ?, "
				." valorimpuesto = ?, "
				." statusimpuesto = ? "
				." where "
				." codimpuesto = ?;
				";
				$stmt = $this->dbh->prepare($sql);
				$stmt->bindParam(1, $nomimpuesto);
				$stmt->bindParam(2, $valorimpuesto);
				$stmt->bindParam(3, $statusimpuesto);
				$stmt->bindParam(4, $codimpuesto);

				$nomimpuesto = limpiar($_POST["nomimpuesto"]);
				$valorimpuesto = limpiar($_POST["valorimpuesto"]);
				$statusimpuesto = limpiar($_POST["statusimpuesto"]);
				$codimpuesto = limpiar($_POST["codimpuesto"]);
				$stmt->execute();

		echo "<span class='fa fa-check-square-o'></span> EL IMPUESTO HA SIDO ACTUALIZADO EXITOSAMENTE";
			exit;

			} else {

			echo "3";
			exit;
		}
	}
}
############################ FUNCION ACTUALIZAR IMPUESTOS ############################

######################### FUNCION ELIMINAR IMPUESTOS #########################
public function EliminarImpuestos()
{
	self::SetNames();
	if ($_SESSION['acceso'] == "administradorG" || $_SESSION["acceso"]=="administradorS") {

		$sql = "SELECT * FROM impuestos WHERE codimpuesto = ? AND statusimpuesto = 'ACTIVO'";
		$stmt = $this->dbh->prepare($sql);
		$stmt->execute(array(decrypt($_GET["codimpuesto"])));
		$num = $stmt->rowCount();
		if($num == 0)
		{

			$sql = "DELETE FROM impuestos WHERE codimpuesto = ?";
			$stmt = $this->dbh->prepare($sql);
			$stmt->bindParam(1,$codimpuesto);
			$codimpuesto = decrypt($_GET["codimpuesto"]);
			$stmt->execute();

			echo "1";
			exit;

		} else {
		   
			echo "2";
			exit;
		  } 
			
		} else {
		
		echo "3";
		exit;
	 }	
}
######################## FUNCION ELIMINAR IMPUESTOS ###########################

############################ FIN DE CLASE IMPUESTOS ################################



























############################# CLASE SUCURSALES ##################################

############################ FUNCION REGISTRAR SUCURSALES ##########################
public function RegistrarSucursales()
{
	self::SetNames();
	if(empty($_POST["dniencargado"]) or empty($_POST["nomencargado"]) or empty($_POST["cuitsucursal"]) or empty($_POST["razonsocial"]))
	{
		echo "1";
		exit;
	}

		$sql = " SELECT correosucursal FROM sucursales WHERE correosucursal = ?";
		$stmt = $this->dbh->prepare($sql);
		$stmt->execute(array($_POST["correosucursal"]));
		$num = $stmt->rowCount();
		if($num > 0)
		{

			echo "2";
			exit;
		}
		else
		{
			$sql = " SELECT cuitsucursal FROM sucursales WHERE cuitsucursal = ?";
			$stmt = $this->dbh->prepare($sql);
			$stmt->execute(array($_POST["cuitsucursal"]));
			$num = $stmt->rowCount();
			if($num == 0)
			{
				$query = " INSERT INTO sucursales values (null, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);";
				$stmt = $this->dbh->prepare($query);
				$stmt->bindParam(1, $documsucursal);
				$stmt->bindParam(2, $cuitsucursal);
				$stmt->bindParam(3, $razonsocial);
				$stmt->bindParam(4, $id_provincia);
				$stmt->bindParam(5, $id_departamento);
				$stmt->bindParam(6, $direcsucursal);
				$stmt->bindParam(7, $correosucursal);
				$stmt->bindParam(8, $tlfsucursal);
				$stmt->bindParam(9, $nroactividadsucursal);
				$stmt->bindParam(10, $iniciofactura);
				$stmt->bindParam(11, $fechaautorsucursal);
				$stmt->bindParam(12, $llevacontabilidad);
				$stmt->bindParam(13, $documencargado);
				$stmt->bindParam(14, $dniencargado);
				$stmt->bindParam(15, $nomencargado);
				$stmt->bindParam(16, $tlfencargado);
				$stmt->bindParam(17, $descsucursal);
				$stmt->bindParam(18, $porcentaje);
				$stmt->bindParam(19, $codmoneda);

				$documsucursal = limpiar($_POST['documsucursal'] == '' ? "0" : $_POST['documsucursal']);
				$cuitsucursal = limpiar($_POST["cuitsucursal"]);
				$razonsocial = limpiar($_POST["razonsocial"]);
				$id_provincia = limpiar($_POST['id_provincia'] == '' ? "0" : $_POST['id_provincia']);
				$id_departamento = limpiar($_POST['id_departamento'] == '' ? "0" : $_POST['id_departamento']);
				$direcsucursal = limpiar($_POST["direcsucursal"]);
				$correosucursal = limpiar($_POST["correosucursal"]);
				$tlfsucursal = limpiar($_POST["tlfsucursal"]);
				$nroactividadsucursal = limpiar($_POST["nroactividadsucursal"]);
				$iniciofactura = limpiar($_POST["iniciofactura"]);
				if (limpiar(isset($_POST['fechaautorsucursal'])) && limpiar($_POST['fechaautorsucursal']!="")) { $fechaautorsucursal = limpiar(date("Y-m-d",strtotime($_POST['fechaautorsucursal']))); } else { $fechaautorsucursal = limpiar('0000-00-00'); };
				$llevacontabilidad = limpiar($_POST["llevacontabilidad"]);
				$documencargado = limpiar($_POST["documencargado"]);
				$dniencargado = limpiar($_POST["dniencargado"]);
				$nomencargado = limpiar($_POST["nomencargado"]);
				$tlfencargado = limpiar($_POST["tlfencargado"]);
				$descsucursal = limpiar($_POST["descsucursal"]);
				$porcentaje = limpiar($_POST["porcentaje"]);
				$codmoneda = limpiar($_POST["codmoneda"]);
				$stmt->execute();

##################  SUBIR LOGO DE SUCURSAL ######################################
//datos del arhivo  
    if (isset($_FILES['imagen']['name'])) { $nombre_archivo = $_FILES['imagen']['name']; } else { $nombre_archivo =''; }
	if (isset($_FILES['imagen']['type'])) { $tipo_archivo = $_FILES['imagen']['type']; } else { $tipo_archivo =''; }
    if (isset($_FILES['imagen']['size'])) { $tamano_archivo = $_FILES['imagen']['size']; } else { $tamano_archivo =''; }  
//compruebo si las características del archivo son las que deseo  
	if ((strpos($tipo_archivo,'image/png')!==false)&&$tamano_archivo<200000) {  
					if (move_uploaded_file($_FILES['imagen']['tmp_name'], "fotos/sucursales/".$nombre_archivo) && rename("fotos/sucursales/".$nombre_archivo,"fotos/sucursales/".$_POST["cuitsucursal"].".png"))
					{ 
## se puede dar un aviso
					} 
## se puede dar otro aviso 
				}
##################  FINALIZA SUBIR LOGO DE SUCURSAL ##################


				echo "<span class='fa fa-check-square-o'></span> LA SUCURSAL HA SIDO REGISTRADA EXITOSAMENTE";
				exit;
			}
			else
			{
				echo "3";
				exit;
			}
	  }
}
######################### FUNCION REGISTRAR SUCURSALES ###############################

######################## FUNCION LISTAR SUCURSALES ###############################
public function ListarSucursales()
{
	self::SetNames();
	$sql = "SELECT 
	sucursales.codsucursal,
	sucursales.documsucursal,
	sucursales.cuitsucursal,
	sucursales.razonsocial,
	sucursales.id_provincia,
	sucursales.id_departamento,
	sucursales.direcsucursal,
	sucursales.correosucursal,
	sucursales.tlfsucursal,
	sucursales.nroactividadsucursal,
	sucursales.iniciofactura,
	sucursales.fechaautorsucursal,
	sucursales.llevacontabilidad,
	sucursales.documencargado,
	sucursales.dniencargado,
	sucursales.nomencargado,
	sucursales.tlfencargado,
	sucursales.descsucursal,
	sucursales.porcentaje,
	sucursales.codmoneda,
	documentos.documento,
	documentos2.documento AS documento2,
	provincias.provincia,
	departamentos.departamento 
	FROM sucursales 
	LEFT JOIN documentos ON sucursales.documsucursal = documentos.coddocumento
	LEFT JOIN documentos AS documentos2 ON sucursales.documencargado = documentos2.coddocumento
	LEFT JOIN provincias ON sucursales.id_provincia = provincias.id_provincia 
	LEFT JOIN departamentos ON sucursales.id_departamento = departamentos.id_departamento";
	foreach ($this->dbh->query($sql) as $row)
	{
		$this->p[] = $row;
	}
	return $this->p;
	$this->dbh=null;
 }
########################## FUNCION LISTAR SUCURSALES ##########################

############################ FUNCION ID SUCURSALES #################################
public function SucursalesPorId()
{
	self::SetNames();
	$sql = "SELECT  
	sucursales.codsucursal,
	sucursales.documsucursal,
	sucursales.cuitsucursal,
	sucursales.razonsocial,
	sucursales.id_provincia,
	sucursales.id_departamento,
	sucursales.direcsucursal,
	sucursales.correosucursal,
	sucursales.tlfsucursal,
	sucursales.nroactividadsucursal,
	sucursales.iniciofactura,
	sucursales.fechaautorsucursal,
	sucursales.llevacontabilidad,
	sucursales.documencargado,
	sucursales.dniencargado,
	sucursales.nomencargado,
	sucursales.tlfencargado,
	sucursales.descsucursal,
	sucursales.porcentaje,
	sucursales.codmoneda,
	tiposmoneda.moneda,
	documentos.documento,
	documentos2.documento AS documento2,
	provincias.provincia,
	departamentos.departamento 
	FROM sucursales 
	LEFT JOIN documentos ON sucursales.documsucursal = documentos.coddocumento
	LEFT JOIN documentos AS documentos2 ON sucursales.documencargado = documentos2.coddocumento
	LEFT JOIN provincias ON sucursales.id_provincia = provincias.id_provincia 
	LEFT JOIN departamentos ON sucursales.id_departamento = departamentos.id_departamento
	LEFT JOIN tiposmoneda ON sucursales.codmoneda = tiposmoneda.codmoneda WHERE sucursales.codsucursal = ?";
	$stmt = $this->dbh->prepare($sql);
	$stmt->execute(array(decrypt($_GET["codsucursal"])));
	$num = $stmt->rowCount();
	if($num==0)
	{
		echo "";
	}
	else
	{
		if($row = $stmt->fetch(PDO::FETCH_ASSOC))
		{
			$this->p[] = $row;
		}
		return $this->p;
		$this->dbh=null;
	}
}
############################ FUNCION ID SUCURSALES #################################

############################ FUNCION ACTUALIZAR SUCURSALES ############################
public function ActualizarSucursales()
{

	self::SetNames();
	if(empty($_POST["codsucursal"]) or empty($_POST["dniencargado"]) or empty($_POST["nomencargado"]) or empty($_POST["cuitsucursal"]) or empty($_POST["razonsocial"]))
	{
		echo "1";
		exit;
	}

		$sql = " SELECT correosucursal FROM sucursales WHERE codsucursal != ? AND correosucursal = ?";
		$stmt = $this->dbh->prepare($sql);
		$stmt->execute(array($_POST["codsucursal"],$_POST["correosucursal"]));
		$num = $stmt->rowCount();
		if($num > 0)
		{
			echo "2";
			exit;
		}
		else
		{
			$sql = " SELECT cuitsucursal FROM sucursales WHERE codsucursal != ? AND cuitsucursal = ?";
			$stmt = $this->dbh->prepare($sql);
			$stmt->execute(array($_POST["codsucursal"],$_POST["cuitsucursal"]));
			$num = $stmt->rowCount();
			if($num == 0)
			{
				$sql = " UPDATE sucursales set "
				." documsucursal = ?, "
				." cuitsucursal = ?, "
				." razonsocial = ?, "
				." id_provincia = ?, "
				." id_departamento = ?, "
				." direcsucursal = ?, "
				." correosucursal = ?, "
				." tlfsucursal = ?, "
				." nroactividadsucursal = ?, "
				." iniciofactura = ?, "
				." fechaautorsucursal = ?, "
				." llevacontabilidad = ?, "
				." documencargado = ?, "
				." dniencargado = ?, "
				." nomencargado = ?, "
				." tlfencargado = ?, "
				." descsucursal = ?, "
				." porcentaje = ?, "
				." codmoneda = ? "
				." where "
				." codsucursal = ?;
				";
				$stmt = $this->dbh->prepare($sql);
				$stmt->bindParam(1, $documsucursal);
				$stmt->bindParam(2, $cuitsucursal);
				$stmt->bindParam(3, $razonsocial);
				$stmt->bindParam(4, $id_provincia);
				$stmt->bindParam(5, $id_departamento);
				$stmt->bindParam(6, $direcsucursal);
				$stmt->bindParam(7, $correosucursal);
				$stmt->bindParam(8, $tlfsucursal);
				$stmt->bindParam(9, $nroactividadsucursal);
				$stmt->bindParam(10, $iniciofactura);
				$stmt->bindParam(11, $fechaautorsucursal);
				$stmt->bindParam(12, $llevacontabilidad);
				$stmt->bindParam(13, $documencargado);
				$stmt->bindParam(14, $dniencargado);
				$stmt->bindParam(15, $nomencargado);
				$stmt->bindParam(16, $tlfencargado);
				$stmt->bindParam(17, $descsucursal);
				$stmt->bindParam(18, $porcentaje);
				$stmt->bindParam(19, $codmoneda);
				$stmt->bindParam(20, $codsucursal);

				$documsucursal = limpiar($_POST['documsucursal'] == '' ? "0" : $_POST['documsucursal']);
				$cuitsucursal = limpiar($_POST["cuitsucursal"]);
				$razonsocial = limpiar($_POST["razonsocial"]);
				$id_provincia = limpiar($_POST['id_provincia'] == '' ? "0" : $_POST['id_provincia']);
				$id_departamento = limpiar($_POST['id_departamento'] == '' ? "0" : $_POST['id_departamento']);
				$direcsucursal = limpiar($_POST["direcsucursal"]);
				$correosucursal = limpiar($_POST["correosucursal"]);
				$tlfsucursal = limpiar($_POST["tlfsucursal"]);
				$nroactividadsucursal = limpiar($_POST["nroactividadsucursal"]);
				$iniciofactura = limpiar($_POST["iniciofactura"]);
				if (limpiar(isset($_POST['fechaautorsucursal'])) && limpiar($_POST['fechaautorsucursal']!="")) { $fechaautorsucursal = limpiar(date("Y-m-d",strtotime($_POST['fechaautorsucursal']))); } else { $fechaautorsucursal = limpiar('0000-00-00'); };
				$llevacontabilidad = limpiar($_POST["llevacontabilidad"]);
				$documencargado = limpiar($_POST['documencargado'] == '' ? "0" : $_POST['documencargado']);
				$dniencargado = limpiar($_POST["dniencargado"]);
				$nomencargado = limpiar($_POST["nomencargado"]);
				$tlfencargado = limpiar($_POST["tlfencargado"]);
				$descsucursal = limpiar($_POST["descsucursal"]);
				$porcentaje = limpiar($_POST["porcentaje"]);
				$codmoneda = limpiar($_POST["codmoneda"]);
				$codsucursal = limpiar($_POST["codsucursal"]);
				$stmt->execute();

##################  SUBIR LOGO DE SUCURSAL ######################################
//datos del arhivo  
    if (isset($_FILES['imagen']['name'])) { $nombre_archivo = $_FILES['imagen']['name']; } else { $nombre_archivo =''; }
	if (isset($_FILES['imagen']['type'])) { $tipo_archivo = $_FILES['imagen']['type']; } else { $tipo_archivo =''; }
    if (isset($_FILES['imagen']['size'])) { $tamano_archivo = $_FILES['imagen']['size']; } else { $tamano_archivo =''; }  
//compruebo si las características del archivo son las que deseo  
	if ((strpos($tipo_archivo,'image/png')!==false)&&$tamano_archivo<200000) {  
					if (move_uploaded_file($_FILES['imagen']['tmp_name'], "fotos/sucursales/".$nombre_archivo) && rename("fotos/sucursales/".$nombre_archivo,"fotos/sucursales/".$_POST["cuitsucursal"].".png"))
					{ 
## se puede dar un aviso
					} 
## se puede dar otro aviso 
				}
##################  FINALIZA SUBIR LOGO DE SUCURSAL ##################

				echo "<span class='fa fa-check-square-o'></span> LA SUCURSAL HA SIDO ACTUALIZADA EXITOSAMENTE";
				exit;

			}
			else
			{
				echo "3";
				exit;
			}
	   }
}
############################ FUNCION ACTUALIZAR SUCURSALES ############################

########################## FUNCION ELIMINAR SUCURSALES ########################
public function EliminarSucursales()
{
	self::SetNames();
   if($_SESSION['acceso'] == "administradorG") {

		$sql = "SELECT codsucursal FROM productos WHERE codsucursal = ?";
		$stmt = $this->dbh->prepare($sql);
		$stmt->execute(array(decrypt($_GET["codsucursal"])));
		$num = $stmt->rowCount();
		if($num == 0)
		{
			$sql = " DELETE FROM sucursales WHERE codsucursal = ?";
			$stmt = $this->dbh->prepare($sql);
			$stmt->bindParam(1,$codsucursal);
			$codsucursal = decrypt($_GET["codsucursal"]);
			$stmt->execute();

			echo "1";
			exit;

		} else {
		   
			echo "2";
			exit;
		  } 
			
		} else {
		
		echo "3";
		exit;
	 }	
}
############################ FUNCION ELIMINAR SUCURSALES #######################

############################# FIN DE CLASE SUCURSALES ################################


























################################ CLASE FAMILIAS ######################################

############################# FUNCION REGISTRAR FAMILIAS ###############################
public function RegistrarFamilias()
{
	self::SetNames();
	if(empty($_POST["nomfamilia"]))
	{
		echo "1";
		exit;
	}

			$sql = " SELECT nomfamilia FROM familias WHERE nomfamilia = ?";
			$stmt = $this->dbh->prepare($sql);
			$stmt->execute(array($_POST["nomfamilia"]));
			$num = $stmt->rowCount();
			if($num == 0)
			{
				$query = " INSERT INTO familias values (null, ?);";
				$stmt = $this->dbh->prepare($query);
				$stmt->bindParam(1, $nomfamilia);

				$nomfamilia = limpiar($_POST["nomfamilia"]);
				$stmt->execute();

			echo "<span class='fa fa-check-square-o'></span> LA FAMILIA HA SIDO REGISTRADA EXITOSAMENTE";
			exit;

			} else {

			echo "2";
			exit;
	    }
}
########################### FUNCION REGISTRAR FAMILIAS ###############################

########################### FUNCION LISTAR FAMILIAS ################################
public function ListarFamilias()
{
	self::SetNames();
	$sql = "SELECT * FROM familias";
	foreach ($this->dbh->query($sql) as $row)
	{
		$this->p[] = $row;
	}
	return $this->p;
	$this->dbh=null;
 }
############################ FUNCION LISTAR FAMILIAS ################################

############################ FUNCION ID FAMILIAS #################################
public function FamiliasPorId()
{
	self::SetNames();
	$sql = "SELECT * FROM familias WHERE codfamilia = ?";
	$stmt = $this->dbh->prepare($sql);
	$stmt->execute(array(decrypt($_GET["codfamilia"])));
	$num = $stmt->rowCount();
	if($num==0)
	{
		echo "";
	}
	else
	{
		if($row = $stmt->fetch(PDO::FETCH_ASSOC))
		{
			$this->p[] = $row;
		}
		return $this->p;
		$this->dbh=null;
	}
}
############################ FUNCION ID FAMILIAS #################################

############################ FUNCION ACTUALIZAR FAMILIAS ############################
public function ActualizarFamilias()
{

	self::SetNames();
	if(empty($_POST["codfamilia"]) or empty($_POST["nomfamilia"]))
	{
		echo "1";
		exit;
	}

			$sql = " SELECT nomfamilia FROM familias WHERE codfamilia != ? AND nomfamilia = ?";
			$stmt = $this->dbh->prepare($sql);
			$stmt->execute(array($_POST["codfamilia"],$_POST["nomfamilia"]));
			$num = $stmt->rowCount();
			if($num == 0)
			{
				$sql = " UPDATE familias set "
				." nomfamilia = ? "
				." where "
				." codfamilia = ?;
				";
				$stmt = $this->dbh->prepare($sql);
				$stmt->bindParam(1, $nomfamilia);
				$stmt->bindParam(2, $codfamilia);

				$nomfamilia = limpiar($_POST["nomfamilia"]);
				$codfamilia = limpiar($_POST["codfamilia"]);
				$stmt->execute();

			echo "<span class='fa fa-check-square-o'></span> LA FAMILIA HA SIDO ACTUALIZADA EXITOSAMENTE";
			exit;

			} else {

			echo "2";
			exit;
		}
}
############################ FUNCION ACTUALIZAR FAMILIAS ############################

########################### FUNCION ELIMINAR FAMILIAS #################################
public function EliminarFamilias()
{
	self::SetNames();
	if ($_SESSION['acceso'] == "administradorG" || $_SESSION["acceso"]=="administradorS") {

		$sql = "SELECT codfamilia FROM subfamilias WHERE codfamilia = ?";
		$stmt = $this->dbh->prepare($sql);
		$stmt->execute(array(decrypt($_GET["codfamilia"])));
		$num = $stmt->rowCount();
		if($num == 0)
		{

			$sql = "DELETE FROM familias WHERE codfamilia = ?";
			$stmt = $this->dbh->prepare($sql);
			$stmt->bindParam(1,$codfamilia);
			$codfamilia = decrypt($_GET["codfamilia"]);
			$stmt->execute();

			echo "1";
			exit;

		} else {
		   
			echo "2";
			exit;
		  } 
			
		} else {
		
		echo "3";
		exit;
	 }	
}
########################## FUNCION ELIMINAR FAMILIAS #################################

############################# FIN DE CLASE FAMILIAS #################################


























################################# CLASE SUBFAMILIAS ####################################

########################### FUNCION REGISTRAR SUBFAMILIAS #########################
public function RegistrarSubfamilias()
{
	self::SetNames();
	if(empty($_POST["nomsubfamilia"]) or empty($_POST["codfamilia"]))
	{
		echo "1";
		exit;
	}

			$sql = " SELECT nomsubfamilia FROM subfamilias WHERE nomsubfamilia = ? AND codfamilia = ?";
			$stmt = $this->dbh->prepare($sql);
			$stmt->execute(array($_POST["nomsubfamilia"],$_POST["codfamilia"]));
			$num = $stmt->rowCount();
			if($num == 0)
			{
				$query = " INSERT INTO subfamilias values (null, ?, ?);";
				$stmt = $this->dbh->prepare($query);
				$stmt->bindParam(1, $nomsubfamilia);
				$stmt->bindParam(2, $codfamilia);

				$nomsubfamilia = limpiar($_POST["nomsubfamilia"]);
				$codfamilia = limpiar($_POST["codfamilia"]);
				$stmt->execute();

		echo "<span class='fa fa-check-square-o'></span> LA SUBFAMILIA HA SIDO REGISTRADA EXITOSAMENTE";
			exit;

			} else {

			echo "2";
			exit;
	    }
}
########################## FUNCION REGISTRAR SUBFAMILIAS ###############################

######################### FUNCION LISTAR SUBFAMILIAS ################################
public function ListarSubfamilias()
{
	self::SetNames();
	$sql = "SELECT * FROM subfamilias LEFT JOIN familias ON familias.codfamilia = subfamilias.codfamilia";
	foreach ($this->dbh->query($sql) as $row)
	{
		$this->p[] = $row;
	}
	return $this->p;
	$this->dbh=null;
 }
########################### FUNCION LISTAR SUBFAMILIAS ################################

####################### FUNCION LISTAR SUBFAMILIAS POR FAMILIAS ######################
	public function ListarSubfamilias2() 
	       {
		self::SetNames();
		$sql = "SELECT * FROM subfamilias WHERE codfamilia = ?";
		$stmt = $this->dbh->prepare($sql);
		$stmt->execute(array($_GET["codfamilia"]));
		$num = $stmt->rowCount();
		    if($num==0)
		{
			echo "<option value='0' selected> -- SIN RESULTADOS -- </option>";
			exit;
		}
		else
		{
		while($row = $stmt->fetch())
			{
				$this->p[]=$row;
			}
			return $this->p;
			$this->dbh=null;
		}
	}
####################### FUNCION LISTAR SUBFAMILIAS POR FAMILIAS #########################

############################ FUNCION ID SUBFAMILIAS #################################
public function SubfamiliasPorId()
{
	self::SetNames();
	$sql = "SELECT * FROM subfamilias LEFT JOIN familias ON familias.codfamilia = subfamilias.codfamilia WHERE subfamilias.codsubfamilia = ?";
	$stmt = $this->dbh->prepare($sql);
	$stmt->execute(array(decrypt($_GET["codsubfamilia"])));
	$num = $stmt->rowCount();
	if($num==0)
	{
		echo "";
	}
	else
	{
		if($row = $stmt->fetch(PDO::FETCH_ASSOC))
		{
			$this->p[] = $row;
		}
		return $this->p;
		$this->dbh=null;
	}
}
############################ FUNCION ID SUBFAMILIAS #################################

############################ FUNCION ACTUALIZAR SUBFAMILIAS ############################
public function ActualizarSubfamilias()
{

	self::SetNames();
	if(empty($_POST["codsubfamilia"]) or empty($_POST["nomsubfamilia"]) or empty($_POST["codfamilia"]))
	{
		echo "1";
		exit;
	}

			$sql = "SELECT nomsubfamilia FROM subfamilias WHERE codsubfamilia != ? AND nomsubfamilia = ? AND codfamilia = ?";
			$stmt = $this->dbh->prepare($sql);
			$stmt->execute(array($_POST["codsubfamilia"],$_POST["nomsubfamilia"],$_POST["codfamilia"]));
			$num = $stmt->rowCount();
			if($num == 0)
			{
				$sql = " UPDATE subfamilias set "
				." nomsubfamilia = ?, "
				." codfamilia = ? "
				." where "
				." codsubfamilia = ?;
				";
				$stmt = $this->dbh->prepare($sql);
				$stmt->bindParam(1, $nomsubfamilia);
				$stmt->bindParam(2, $codfamilia);
				$stmt->bindParam(3, $codsubfamilia);

				$nomsubfamilia = limpiar($_POST["nomsubfamilia"]);
				$codfamilia = limpiar($_POST["codfamilia"]);
				$codsubfamilia = limpiar($_POST["codsubfamilia"]);
				$stmt->execute();

		echo "<span class='fa fa-check-square-o'></span> LA SUBFAMILIA HA SIDO ACTUALIZADA EXITOSAMENTE";
			exit;

			} else {

			echo "2";
			exit;
		}
}
############################ FUNCION ACTUALIZAR SUBFAMILIAS ############################

############################ FUNCION ELIMINAR SUBFAMILIAS ##########################
public function EliminarSubfamilias()
{
	self::SetNames();
	if ($_SESSION['acceso'] == "administradorG" || $_SESSION["acceso"]=="administradorS") {

		$sql = "SELECT codsubfamilia FROM productos WHERE codsubfamilia = ?";
		$stmt = $this->dbh->prepare($sql);
		$stmt->execute(array(decrypt($_GET["codsubfamilia"])));
		$num = $stmt->rowCount();
		if($num == 0)
		{

			$sql = "DELETE FROM subfamilias WHERE codsubfamilia = ?";
			$stmt = $this->dbh->prepare($sql);
			$stmt->bindParam(1,$codsubfamilia);
			$codsubfamilia = decrypt($_GET["codsubfamilia"]);
			$stmt->execute();

			echo "1";
			exit;

		} else {
		   
			echo "2";
			exit;
		  } 
			
		} else {
		
		echo "3";
		exit;
	 }	
}
############################ FUNCION ELIMINAR SUBFAMILIAS ##########################

############################## FIN DE CLASE SUBFAMILIAS ##############################


























################################## CLASE MARCAS ######################################

############################ FUNCION REGISTRAR MARCAS ###############################
public function RegistrarMarcas()
{
	self::SetNames();
	if(empty($_POST["nommarca"]))
	{
		echo "1";
		exit;
	}

			$sql = " SELECT nommarca FROM marcas WHERE nommarca = ?";
			$stmt = $this->dbh->prepare($sql);
			$stmt->execute(array($_POST["nommarca"]));
			$num = $stmt->rowCount();
			if($num == 0)
			{
				$query = " INSERT INTO marcas values (null, ?);";
				$stmt = $this->dbh->prepare($query);
				$stmt->bindParam(1, $nommarca);

				$nommarca = limpiar($_POST["nommarca"]);
				$stmt->execute();

			echo "<span class='fa fa-check-square-o'></span> LA MARCA HA SIDO REGISTRADA EXITOSAMENTE";
			exit;

			} else {

			echo "2";
			exit;
	    }
}
############################ FUNCION REGISTRAR MARCAS ###############################

############################## FUNCION LISTAR MARCAS ################################
public function ListarMarcas()
{
	self::SetNames();
	$sql = "SELECT * FROM marcas";
	foreach ($this->dbh->query($sql) as $row)
	{
		$this->p[] = $row;
	}
	return $this->p;
	$this->dbh=null;
 }
################################## FUNCION LISTAR MARCAS ################################

############################ FUNCION ID MARCAS #################################
public function MarcasPorId()
{
	self::SetNames();
	$sql = "SELECT * FROM marcas WHERE codmarca = ?";
	$stmt = $this->dbh->prepare($sql);
	$stmt->execute(array(decrypt($_GET["codmarca"])));
	$num = $stmt->rowCount();
	if($num==0)
	{
		echo "";
	}
	else
	{
		if($row = $stmt->fetch(PDO::FETCH_ASSOC))
		{
			$this->p[] = $row;
		}
		return $this->p;
		$this->dbh=null;
	}
}
############################ FUNCION ID MARCAS #################################

############################ FUNCION ACTUALIZAR MARCAS ############################
public function ActualizarMarcas()
{

	self::SetNames();
	if(empty($_POST["codmarca"]) or empty($_POST["nommarca"]))
	{
		echo "1";
		exit;
	}

			$sql = " SELECT nommarca FROM marcas WHERE codmarca != ? AND nommarca = ?";
			$stmt = $this->dbh->prepare($sql);
			$stmt->execute(array($_POST["codmarca"],$_POST["nommarca"]));
			$num = $stmt->rowCount();
			if($num == 0)
			{
				$sql = " UPDATE marcas set "
				." nommarca = ? "
				." where "
				." codmarca = ?;
				";
				$stmt = $this->dbh->prepare($sql);
				$stmt->bindParam(1, $nommarca);
				$stmt->bindParam(2, $codmarca);

				$nommarca = limpiar($_POST["nommarca"]);
				$codmarca = limpiar($_POST["codmarca"]);
				$stmt->execute();

			echo "<span class='fa fa-check-square-o'></span> LA MARCA HA SIDO ACTUALIZADA EXITOSAMENTE";
			exit;

			} else {

			echo "2";
			exit;
		}
}
############################ FUNCION ACTUALIZAR MARCAS ############################

########################### FUNCION ELIMINAR MARCAS #################################
public function EliminarMarcas()
{
	self::SetNames();
	if ($_SESSION['acceso'] == "administradorG" || $_SESSION["acceso"]=="administradorS") {

		$sql = "SELECT codmarca FROM modelos WHERE codmarca = ?";
		$stmt = $this->dbh->prepare($sql);
		$stmt->execute(array(decrypt($_GET["codmarca"])));
		$num = $stmt->rowCount();
		if($num == 0)
		{

			$sql = "DELETE FROM marcas WHERE codmarca = ?";
			$stmt = $this->dbh->prepare($sql);
			$stmt->bindParam(1,$codmarca);
			$codmarca = decrypt($_GET["codmarca"]);
			$stmt->execute();

			echo "1";
			exit;

		} else {
		   
			echo "2";
			exit;
		  } 
			
		} else {
		
		echo "3";
		exit;
	 }	
}
########################### FUNCION ELIMINAR MARCAS #################################

############################## FIN DE CLASE MARCAS ###################################


























################################# CLASE MODELOS ######################################

########################### FUNCION REGISTRAR MODELOS ###############################
public function RegistrarModelos()
{
	self::SetNames();
	if(empty($_POST["nommodelo"]) or empty($_POST["codmarca"]))
	{
		echo "1";
		exit;
	}

			$sql = " SELECT nommodelo FROM modelos WHERE nommodelo = ? AND codmarca = ?";
			$stmt = $this->dbh->prepare($sql);
			$stmt->execute(array($_POST["nommodelo"],$_POST["codmarca"]));
			$num = $stmt->rowCount();
			if($num == 0)
			{
				$query = " INSERT INTO modelos values (null, ?, ?);";
				$stmt = $this->dbh->prepare($query);
				$stmt->bindParam(1, $nommodelo);
				$stmt->bindParam(2, $codmarca);

				$nommodelo = limpiar($_POST["nommodelo"]);
				$codmarca = limpiar($_POST["codmarca"]);
				$stmt->execute();

			echo "<span class='fa fa-check-square-o'></span> EL MODELO HA SIDO REGISTRADO EXITOSAMENTE";
			exit;

			} else {

			echo "2";
			exit;
	    }
}
########################### FUNCION REGISTRAR MODELOS ###############################

############################ FUNCION LISTAR MODELOS ################################
public function ListarModelos()
{
	self::SetNames();
	$sql = "SELECT * FROM modelos INNER JOIN marcas ON marcas.codmarca = modelos.codmarca";
	foreach ($this->dbh->query($sql) as $row)
	{
		$this->p[] = $row;
	}
	return $this->p;
	$this->dbh=null;
 }
############################## FUNCION LISTAR MODELOS ################################

########################## FUNCION LISTAR MODELOS POR MARCAS ##########################
 public function ListarModelosxMarcas() 
	       {
		self::SetNames();
		$sql = "SELECT * FROM modelos WHERE codmarca = ?";
		$stmt = $this->dbh->prepare($sql);
		$stmt->execute(array($_GET["codmarca"]));
		$num = $stmt->rowCount();
		     if($num==0)
		{
	        echo "<option value='0' selected> -- SIN RESULTADOS -- </option>";
			exit;
		}
		else
		{
		while($row = $stmt->fetch())
			{
				$this->p[]=$row;
			}
			return $this->p;
			$this->dbh=null;
		}
	}
############################# FUNCION LISTAR MODELOS POR MARCAS #########################

############################ FUNCION ID MODELOS #################################
public function ModelosPorId()
{
	self::SetNames();
	$sql = "SELECT * FROM modelos LEFT JOIN marcas ON marcas.codmarca = modelos.codmarca WHERE modelos.codmodelo = ?";
	$stmt = $this->dbh->prepare($sql);
	$stmt->execute(array(decrypt($_GET["codmodelo"])));
	$num = $stmt->rowCount();
	if($num==0)
	{
		echo "";
	}
	else
	{
		if($row = $stmt->fetch(PDO::FETCH_ASSOC))
		{
			$this->p[] = $row;
		}
		return $this->p;
		$this->dbh=null;
	}
}
############################ FUNCION ID MODELOS #################################

############################ FUNCION ACTUALIZAR MODELOS ############################
public function ActualizarModelos()
{
	self::SetNames();
	if(empty($_POST["codmodelo"]) or empty($_POST["nommodelo"]) or empty($_POST["codmarca"]))
	{
		echo "1";
		exit;
	}

			$sql = "SELECT nommodelo FROM modelos WHERE codmodelo != ? AND nommodelo = ? AND codmarca = ?";
			$stmt = $this->dbh->prepare($sql);
			$stmt->execute(array($_POST["codmodelo"],$_POST["nommodelo"],$_POST["codmarca"]));
			$num = $stmt->rowCount();
			if($num == 0)
			{
				$sql = " UPDATE modelos set "
				." nommodelo = ?, "
				." codmarca = ? "
				." where "
				." codmodelo = ?;
				";
				$stmt = $this->dbh->prepare($sql);
				$stmt->bindParam(1, $nommodelo);
				$stmt->bindParam(2, $codmarca);
				$stmt->bindParam(3, $codmodelo);

				$nommodelo = limpiar($_POST["nommodelo"]);
				$codmarca = limpiar($_POST["codmarca"]);
				$codmodelo = limpiar($_POST["codmodelo"]);
				$stmt->execute();

			echo "<span class='fa fa-check-square-o'></span> EL MODELO HA SIDO ACTUALIZADO EXITOSAMENTE";
			exit;

			} else {

			echo "2";
			exit;
		}
}
############################ FUNCION ACTUALIZAR MODELOS ############################

############################ FUNCION ELIMINAR MODELOS ############################
public function EliminarModelos()
{
	self::SetNames();
	if ($_SESSION['acceso'] == "administradorG" || $_SESSION["acceso"]=="administradorS") {

		$sql = "SELECT codmodelo FROM productos WHERE codmodelo = ?";
		$stmt = $this->dbh->prepare($sql);
		$stmt->execute(array(decrypt($_GET["codmodelo"])));
		$num = $stmt->rowCount();
		if($num == 0)
		{

			$sql = "DELETE FROM modelos WHERE codmodelo = ?";
			$stmt = $this->dbh->prepare($sql);
			$stmt->bindParam(1,$codmodelo);
			$codmodelo = decrypt($_GET["codmodelo"]);
			$stmt->execute();

			echo "1";
			exit;

		} else {
		   
			echo "2";
			exit;
		  } 
			
		} else {
		
		echo "3";
		exit;
	 }	
}
############################ FUNCION ELIMINAR MODELOS ############################

############################## FIN DE CLASE MODELOS #################################


























################################# CLASE PRESENTACIONES ################################

########################### FUNCION REGISTRAR PRESENTACIONES ##########################
public function RegistrarPresentaciones()
{
	self::SetNames();
	if(empty($_POST["nompresentacion"]))
	{
		echo "1";
		exit;
	}

			$sql = " SELECT nompresentacion FROM presentaciones WHERE nompresentacion = ?";
			$stmt = $this->dbh->prepare($sql);
			$stmt->execute(array($_POST["nompresentacion"]));
			$num = $stmt->rowCount();
			if($num == 0)
			{
				$query = " INSERT INTO presentaciones values (null, ?);";
				$stmt = $this->dbh->prepare($query);
				$stmt->bindParam(1, $nompresentacion);

				$nompresentacion = limpiar($_POST["nompresentacion"]);
				$stmt->execute();

			echo "<span class='fa fa-check-square-o'></span> LA PRESENTACI&Oacute;N HA SIDO REGISTRADA EXITOSAMENTE";
			exit;

			} else {

			echo "2";
			exit;
	    }
}
########################### FUNCION REGISTRAR PRESENTACIONES #########################

########################### FUNCION LISTAR PRESENTACIONES ############################
public function ListarPresentaciones()
{
	self::SetNames();
	$sql = "SELECT * FROM presentaciones";
	foreach ($this->dbh->query($sql) as $row)
	{
		$this->p[] = $row;
	}
	return $this->p;
	$this->dbh=null;
 }
########################### FUNCION LISTAR PRESENTACIONES #########################

############################ FUNCION ID PRESENTACIONES #################################
public function PresentacionesPorId()
{
	self::SetNames();
	$sql = "SELECT * FROM presentaciones WHERE codpresentacion = ?";
	$stmt = $this->dbh->prepare($sql);
	$stmt->execute(array(decrypt($_GET["codpresentacion"])));
	$num = $stmt->rowCount();
	if($num==0)
	{
		echo "";
	}
	else
	{
		if($row = $stmt->fetch(PDO::FETCH_ASSOC))
		{
			$this->p[] = $row;
		}
		return $this->p;
		$this->dbh=null;
	}
}
############################ FUNCION ID PRESENTACIONES #################################

######################### FUNCION ACTUALIZAR PRESENTACIONES #######################
public function ActualizarPresentaciones()
{
	self::SetNames();
	if(empty($_POST["codpresentacion"]) or empty($_POST["nompresentacion"]))
	{
		echo "1";
		exit;
	}

			$sql = " SELECT nompresentacion FROM presentaciones WHERE codpresentacion != ? AND nompresentacion = ?";
			$stmt = $this->dbh->prepare($sql);
			$stmt->execute(array($_POST["codpresentacion"],$_POST["nompresentacion"]));
			$num = $stmt->rowCount();
			if($num == 0)
			{
				$sql = " UPDATE presentaciones set "
				." nompresentacion = ? "
				." where "
				." codpresentacion = ?;
				";
				$stmt = $this->dbh->prepare($sql);
				$stmt->bindParam(1, $nompresentacion);
				$stmt->bindParam(2, $codpresentacion);

				$nompresentacion = limpiar($_POST["nompresentacion"]);
				$codpresentacion = limpiar($_POST["codpresentacion"]);
				$stmt->execute();

			echo "<span class='fa fa-check-square-o'></span> LA PRESENTACI&Oacute;N HA SIDO ACTUALIZADA EXITOSAMENTE";
			exit;

			} else {

			echo "2";
			exit;
		}
}
######################## FUNCION ACTUALIZAR PRESENTACIONES #######################

########################### FUNCION ELIMINAR PRESENTACIONES ############################
public function EliminarPresentaciones()
{
	self::SetNames();
	if ($_SESSION['acceso'] == "administradorG" || $_SESSION["acceso"]=="administradorS") {

		$sql = "SELECT codpresentacion FROM productos WHERE codpresentacion = ?";
		$stmt = $this->dbh->prepare($sql);
		$stmt->execute(array(decrypt($_GET["codpresentacion"])));
		$num = $stmt->rowCount();
		if($num == 0)
		{

			$sql = "DELETE FROM presentaciones WHERE codpresentacion = ?";
			$stmt = $this->dbh->prepare($sql);
			$stmt->bindParam(1,$codpresentacion);
			$codpresentacion = decrypt($_GET["codpresentacion"]);
			$stmt->execute();

			echo "1";
			exit;

		} else {
		   
			echo "2";
			exit;
		  } 
			
		} else {
		
		echo "3";
		exit;
	 }	
}
########################### FUNCION ELIMINAR PRESENTACIONES ###########################

########################### FIN DE CLASE PRESENTACIONES ###############################


























################################## CLASE COLORES ######################################

########################### FUNCION REGISTRAR COLORES ###############################
public function RegistrarColores()
{
	self::SetNames();
	if(empty($_POST["nomcolor"]))
	{
		echo "1";
		exit;
	}

			$sql = " SELECT nomcolor FROM colores WHERE nomcolor = ?";
			$stmt = $this->dbh->prepare($sql);
			$stmt->execute(array($_POST["nomcolor"]));
			$num = $stmt->rowCount();
			if($num == 0)
			{
				$query = " INSERT INTO colores values (null, ?);";
				$stmt = $this->dbh->prepare($query);
				$stmt->bindParam(1, $nomcolor);

				$nomcolor = limpiar($_POST["nomcolor"]);
				$stmt->execute();

			echo "<span class='fa fa-check-square-o'></span> EL COLOR HA SIDO REGISTRADO EXITOSAMENTE";
			exit;

			} else {

			echo "2";
			exit;
	    }
}
########################## FUNCION REGISTRAR COLORES ###############################

########################## FUNCION LISTAR COLORES ################################
public function ListarColores()
{
	self::SetNames();
	$sql = "SELECT * FROM colores";
	foreach ($this->dbh->query($sql) as $row)
	{
		$this->p[] = $row;
	}
	return $this->p;
	$this->dbh=null;
 }
########################### FUNCION LISTAR COLORES ################################

############################ FUNCION ID COLORES #################################
public function ColoresPorId()
{
	self::SetNames();
	$sql = "SELECT * FROM colores WHERE codcolor = ?";
	$stmt = $this->dbh->prepare($sql);
	$stmt->execute(array(decrypt($_GET["codcolor"])));
	$num = $stmt->rowCount();
	if($num==0)
	{
		echo "";
	}
	else
	{
		if($row = $stmt->fetch(PDO::FETCH_ASSOC))
		{
			$this->p[] = $row;
		}
		return $this->p;
		$this->dbh=null;
	}
}
############################ FUNCION ID COLORES #################################

############################ FUNCION ACTUALIZAR COLORES ############################
public function ActualizarColores()
{

	self::SetNames();
	if(empty($_POST["codcolor"]) or empty($_POST["nomcolor"]))
	{
		echo "1";
		exit;
	}

			$sql = " SELECT nomcolor FROM colores WHERE codcolor != ? AND nomcolor = ?";
			$stmt = $this->dbh->prepare($sql);
			$stmt->execute(array($_POST["codcolor"],$_POST["nomcolor"]));
			$num = $stmt->rowCount();
			if($num == 0)
			{
				$sql = " UPDATE colores set "
				." nomcolor = ? "
				." where "
				." codcolor = ?;
				";
				$stmt = $this->dbh->prepare($sql);
				$stmt->bindParam(1, $nomcolor);
				$stmt->bindParam(2, $codcolor);

				$nomcolor = limpiar($_POST["nomcolor"]);
				$codcolor = limpiar($_POST["codcolor"]);
				$stmt->execute();

			echo "<span class='fa fa-check-square-o'></span> EL COLOR HA SIDO ACTUALIZADO EXITOSAMENTE";
			exit;

			} else {

			echo "2";
			exit;
		}
}
############################ FUNCION ACTUALIZAR COLORES ############################

########################### FUNCION ELIMINAR COLORES ###########################
public function EliminarColores()
{
	self::SetNames();
		if ($_SESSION['acceso'] == "administradorG" || $_SESSION["acceso"]=="administradorS") {

		$sql = "SELECT codcolor FROM productos WHERE codcolor = ?";
		$stmt = $this->dbh->prepare($sql);
		$stmt->execute(array(decrypt($_GET["codcolor"])));
		$num = $stmt->rowCount();
		if($num == 0)
		{

			$sql = "DELETE FROM colores WHERE codcolor = ?";
			$stmt = $this->dbh->prepare($sql);
			$stmt->bindParam(1,$codcolor);
			$codcolor = decrypt($_GET["codcolor"]);
			$stmt->execute();

			echo "1";
			exit;

		} else {
		   
			echo "2";
			exit;
		  } 
			
		} else {
		
		echo "3";
		exit;
	 }	
}
########################### FUNCION ELIMINAR COLORES #################################

############################ FIN DE CLASE COLORES ##################################


























################################### CLASE ORIGENES ####################################

########################## FUNCION REGISTRAR ORIGENES ###############################
public function RegistrarOrigenes()
{
	self::SetNames();
	if(empty($_POST["nomorigen"]))
	{
		echo "1";
		exit;
	}

			$sql = " SELECT nomorigen FROM origenes WHERE nomorigen = ?";
			$stmt = $this->dbh->prepare($sql);
			$stmt->execute(array($_POST["nomorigen"]));
			$num = $stmt->rowCount();
			if($num == 0)
			{
				$query = " INSERT INTO origenes values (null, ?);";
				$stmt = $this->dbh->prepare($query);
				$stmt->bindParam(1, $nomorigen);

				$nomorigen = limpiar($_POST["nomorigen"]);
				$stmt->execute();

			echo "<span class='fa fa-check-square-o'></span> EL ORIGEN HA SIDO REGISTRADO EXITOSAMENTE";
			exit;

			} else {

			echo "2";
			exit;
	    }
}
############################# FUNCION REGISTRAR ORIGENES ###############################

############################ FUNCION LISTAR ORIGENES ################################
public function ListarOrigenes()
{
	self::SetNames();
	$sql = "SELECT * FROM origenes";
	foreach ($this->dbh->query($sql) as $row)
	{
		$this->p[] = $row;
	}
	return $this->p;
	$this->dbh=null;
 }
############################ FUNCION LISTAR ORIGENES ################################

############################ FUNCION ID ORIGENES #################################
public function OrigenesPorId()
{
	self::SetNames();
	$sql = "SELECT * FROM origenes WHERE codorigen = ?";
	$stmt = $this->dbh->prepare($sql);
	$stmt->execute(array(decrypt($_GET["codorigen"])));
	$num = $stmt->rowCount();
	if($num==0)
	{
		echo "";
	}
	else
	{
		if($row = $stmt->fetch(PDO::FETCH_ASSOC))
		{
			$this->p[] = $row;
		}
		return $this->p;
		$this->dbh=null;
	}
}
############################ FUNCION ID ORIGENES #################################

############################ FUNCION ACTUALIZAR ORIGENES ############################
public function ActualizarOrigenes()
{

	self::SetNames();
	if(empty($_POST["codorigen"]) or empty($_POST["nomorigen"]))
	{
		echo "1";
		exit;
	}

			$sql = " SELECT nomorigen FROM origenes WHERE codorigen != ? AND nomorigen = ?";
			$stmt = $this->dbh->prepare($sql);
			$stmt->execute(array($_POST["codorigen"],$_POST["nomorigen"]));
			$num = $stmt->rowCount();
			if($num == 0)
			{
				$sql = " UPDATE origenes set "
				." nomorigen = ? "
				." where "
				." codorigen = ?;
				";
				$stmt = $this->dbh->prepare($sql);
				$stmt->bindParam(1, $nomorigen);
				$stmt->bindParam(2, $codorigen);

				$nomorigen = limpiar($_POST["nomorigen"]);
				$codorigen = limpiar($_POST["codorigen"]);
				$stmt->execute();

			echo "<span class='fa fa-check-square-o'></span> EL ORIGEN HA SIDO ACTUALIZADO EXITOSAMENTE";
			exit;

			} else {

			echo "2";
			exit;
		}
}
############################ FUNCION ACTUALIZAR ORIGENES ############################

########################### FUNCION ELIMINAR ORIGENES ##############################
public function EliminarOrigenes()
{
	self::SetNames();
	if ($_SESSION['acceso'] == "administradorG" || $_SESSION["acceso"]=="administradorS") {

		$sql = "SELECT codorigen FROM productos WHERE codorigen = ?";
		$stmt = $this->dbh->prepare($sql);
		$stmt->execute(array(decrypt($_GET["codorigen"])));
		$num = $stmt->rowCount();
		if($num == 0)
		{

			$sql = "DELETE FROM origenes WHERE codorigen = ?";
			$stmt = $this->dbh->prepare($sql);
			$stmt->bindParam(1,$codorigen);
			$codorigen = decrypt($_GET["codorigen"]);
			$stmt->execute();

			echo "1";
			exit;

		} else {
		   
			echo "2";
			exit;
		  } 
			
		} else {
		
		echo "3";
		exit;
	 }	
}
########################### FUNCION ELIMINAR ORIGENES #################################

############################ FIN DE CLASE ORIGENES #################################


























################################## CLASE CLIENTES ##################################

############################### FUNCION CARGAR CLIENTES ##############################
	public function CargarClientes()
	{
		self::SetNames();
		if(empty($_FILES["sel_file"]))
		{
			echo "1";
			exit;
		}
        //Aquí es donde seleccionamos nuestro csv
         $fname = $_FILES['sel_file']['name'];
         //echo 'Cargando nombre del archivo: '.$fname.' ';
         $chk_ext = explode(".",$fname);
         
        if(strtolower(end($chk_ext)) == "csv")
        {
        //si es correcto, entonces damos permisos de lectura para subir
        $filename = $_FILES['sel_file']['tmp_name'];
        $handle = fopen($filename, "r");
        $this->dbh->beginTransaction();
        
        while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {

               //Insertamos los datos con los valores...
			   
		$query = "INSERT INTO clientes values (null, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);";
		$stmt = $this->dbh->prepare($query);
		$stmt->bindParam(1, $codcliente);
		$stmt->bindParam(2, $documcliente);
		$stmt->bindParam(3, $dnicliente);
		$stmt->bindParam(4, $nomcliente);
		$stmt->bindParam(5, $tlfcliente);
		$stmt->bindParam(6, $id_provincia);
		$stmt->bindParam(7, $id_departamento);
		$stmt->bindParam(8, $direccliente);
		$stmt->bindParam(9, $emailcliente);
		$stmt->bindParam(10, $tipocliente);
		$stmt->bindParam(11, $limitecredito);
		$stmt->bindParam(12, $fechaingreso);

		$codcliente = limpiar($data[0]);
		$documcliente = limpiar($data[1]);
		$dnicliente = limpiar($data[2]);
		$nomcliente = limpiar($data[3]);
		$tlfcliente = limpiar($data[4]);
		$id_provincia = limpiar($data[5]);
		$id_departamento = limpiar($data[6]);
		$direccliente = limpiar($data[7]);
		$emailcliente = limpiar($data[8]);
		$tipocliente = limpiar($data[9]);
		$limitecredito = limpiar($data[10]);
		$fechaingreso = limpiar(date("Y-m-d"));
		$stmt->execute();
				
        }
           $this->dbh->commit();
           //cerramos la lectura del archivo "abrir archivo" con un "cerrar archivo"
           fclose($handle);
	        
	echo "<span class='fa fa-check-square-o'></span> LA CARGA MASIVA DE CLIENTES FUE REALIZADA EXITOSAMENTE";
	exit;
             
         }
         else
         {
    //si aparece esto es posible que el archivo no tenga el formato adecuado, inclusive cuando es cvs, revisarlo para ver si esta separado por " , "
         echo "2";
		 exit;
      }  
}
################################# FUNCION CARGAR CLIENTES ###############################

############################ FUNCION REGISTRAR CLIENTES ###############################
	public function RegistrarClientes()
	{
		self::SetNames();
		if(empty($_POST["dnicliente"]) or empty($_POST["nomcliente"]) or empty($_POST["direccliente"]))
		{
			echo "1";
			exit;
		}

		$sql = "SELECT codcliente FROM clientes ORDER BY idcliente DESC LIMIT 1";
		foreach ($this->dbh->query($sql) as $row){

			$id=$row["codcliente"];

		}
		if(empty($id))
		{
			$codcliente = "C1";

		} else {

			$resto = substr($id, 0, 1);
			$coun = strlen($resto);
			$num     = substr($id, $coun);
			$codigo     = $num + 1;
			$codcliente = "C".$codigo;
		}

		$sql = "SELECT dnicliente FROM clientes WHERE dnicliente = ?";
		$stmt = $this->dbh->prepare($sql);
		$stmt->execute(array($_POST["dnicliente"]));
		$num = $stmt->rowCount();
		if($num == 0)
		{
			$query = " INSERT INTO clientes values (null, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);";
			$stmt = $this->dbh->prepare($query);
			$stmt->bindParam(1, $codcliente);
		    $stmt->bindParam(2, $documcliente);
			$stmt->bindParam(3, $dnicliente);
			$stmt->bindParam(4, $nomcliente);
			$stmt->bindParam(5, $tlfcliente);
			$stmt->bindParam(6, $id_provincia);
			$stmt->bindParam(7, $id_departamento);
			$stmt->bindParam(8, $direccliente);
			$stmt->bindParam(9, $emailcliente);
			$stmt->bindParam(10, $tipocliente);
		    $stmt->bindParam(11, $limitecredito);
			$stmt->bindParam(12, $fechaingreso);
			
			$documcliente = limpiar($_POST['documcliente'] == '' ? "0" : $_POST['documcliente']);
			$dnicliente = limpiar($_POST["dnicliente"]);
			$nomcliente = limpiar($_POST["nomcliente"]);
			$tlfcliente = limpiar($_POST["tlfcliente"]);
			$id_provincia = limpiar($_POST['id_provincia'] == '' ? "0" : $_POST['id_provincia']);
			$id_departamento = limpiar($_POST['id_departamento'] == '' ? "0" : $_POST['id_departamento']);
			$direccliente = limpiar($_POST["direccliente"]);
			$emailcliente = limpiar($_POST["emailcliente"]);
			$tipocliente = limpiar($_POST["tipocliente"]);
			$limitecredito = limpiar($_POST["limitecredito"]);
		    $fechaingreso = limpiar(date("Y-m-d"));
			$stmt->execute();

			echo "<span class='fa fa-check-square-o'></span> EL CLIENTE HA SIDO REGISTRADO EXITOSAMENTE";
			exit;

		} else {

			echo "2";
			exit;
		}
	}
######################## FUNCION REGISTRAR CLIENTES ###############################

############################ FUNCION LISTAR CLIENTES ################################
	public function ListarClientes()
	{
		self::SetNames();
	$sql = "SELECT
		clientes.codcliente,
		clientes.documcliente,
		clientes.dnicliente,
		clientes.nomcliente,
		clientes.tlfcliente,
		clientes.id_provincia,
		clientes.id_departamento,
		clientes.direccliente,
		clientes.emailcliente,
		clientes.tipocliente,
		clientes.limitecredito,
		clientes.fechaingreso,
		IFNULL(clientes.current_balance, 0.00) AS current_balance,
	    documentos.documento,
		provincias.provincia,
		departamentos.departamento
		FROM clientes 
		LEFT JOIN documentos ON clientes.documcliente = documentos.coddocumento
		LEFT JOIN provincias ON clientes.id_provincia = provincias.id_provincia 
		LEFT JOIN departamentos ON clientes.id_departamento = departamentos.id_departamento
		WHERE clientes.tenant_id = '".limpiar($_SESSION["codsucursal"])."'";
	foreach ($this->dbh->query($sql) as $row)
	{
		$this->p[] = $row;
	}
	return $this->p;
	$this->dbh=null;
}
######################### FUNCION LISTAR CLIENTES ################################

######################### FUNCION ID CLIENTES #################################
	public function ClientesPorId()
	{
		self::SetNames();
		$sql = "SELECT
		clientes.codcliente,
		clientes.documcliente,
		clientes.dnicliente,
		clientes.nomcliente,
		clientes.tlfcliente,
		clientes.id_provincia,
		clientes.id_departamento,
		clientes.direccliente,
		clientes.emailcliente,
		clientes.tipocliente,
		clientes.limitecredito,
		clientes.fechaingreso,
	    documentos.documento,
		provincias.provincia,
		departamentos.departamento
		FROM clientes 
		LEFT JOIN documentos ON clientes.documcliente = documentos.coddocumento
		LEFT JOIN provincias ON clientes.id_provincia = provincias.id_provincia 
		LEFT JOIN departamentos ON clientes.id_departamento = departamentos.id_departamento WHERE clientes.codcliente = ?";
		$stmt = $this->dbh->prepare($sql);
		$stmt->execute(array(decrypt($_GET["codcliente"])));
		$num = $stmt->rowCount();
		if($num==0)
		{
			echo "";
		}
		else
		{
			if($row = $stmt->fetch(PDO::FETCH_ASSOC))
			{
				$this->p[] = $row;
			}
			return $this->p;
			$this->dbh=null;
		}
	}
############################ FUNCION ID CLIENTES #################################
	
############################ FUNCION ACTUALIZAR CLIENTES ############################
	public function ActualizarClientes()
	{
		
	self::SetNames();
		if(empty($_POST["codcliente"]) or empty($_POST["dnicliente"]) or empty($_POST["nomcliente"]) or empty($_POST["direccliente"]))
		{
			echo "1";
			exit;
		}
		$sql = " SELECT dnicliente FROM clientes WHERE codcliente != ? AND dnicliente = ?";
		$stmt = $this->dbh->prepare($sql);
		$stmt->execute(array($_POST["codcliente"],$_POST["dnicliente"]));
		$num = $stmt->rowCount();
		if($num == 0)
		{
			$sql = "UPDATE clientes set "
			." documcliente = ?, "
			." dnicliente = ?, "
			." nomcliente = ?, "
			." tlfcliente = ?, "
			." id_provincia = ?, "
			." id_departamento = ?, "
			." direccliente = ?, "
			." emailcliente = ?, "
			." tipocliente = ?, "
			." limitecredito = ? "
			." where "
			." codcliente = ?;
			";
			$stmt = $this->dbh->prepare($sql);
		    $stmt->bindParam(1, $documcliente);
			$stmt->bindParam(2, $dnicliente);
			$stmt->bindParam(3, $nomcliente);
			$stmt->bindParam(4, $tlfcliente);
			$stmt->bindParam(5, $id_provincia);
			$stmt->bindParam(6, $id_departamento);
			$stmt->bindParam(7, $direccliente);
			$stmt->bindParam(8, $emailcliente);
			$stmt->bindParam(9, $tipocliente);
			$stmt->bindParam(10, $limitecredito);
			$stmt->bindParam(11, $codcliente);
			
			$documcliente = limpiar($_POST['documcliente'] == '' ? "0" : $_POST['documcliente']);
			$dnicliente = limpiar($_POST["dnicliente"]);
			$nomcliente = limpiar($_POST["nomcliente"]);
			$tlfcliente = limpiar($_POST["tlfcliente"]);
			$id_provincia = limpiar($_POST['id_provincia'] == '' ? "0" : $_POST['id_provincia']);
			$id_departamento = limpiar($_POST['id_departamento'] == '' ? "0" : $_POST['id_departamento']);
			$direccliente = limpiar($_POST["direccliente"]);
			$emailcliente = limpiar($_POST["emailcliente"]);
			$tipocliente = limpiar($_POST["tipocliente"]);
			$limitecredito = limpiar($_POST["limitecredito"]);
			$codcliente = limpiar($_POST["codcliente"]);
			$stmt->execute();
        
		echo "<span class='fa fa-check-square-o'></span> EL CLIENTE HA SIDO ACTUALIZADO EXITOSAMENTE";
		exit;

		} else {

			echo "2";
			exit;
		}
	}
############################ FUNCION ACTUALIZAR CLIENTES ############################

########################### FUNCION ELIMINAR CLIENTES #################################
	public function EliminarClientes()
	{
	self::SetNames();
		if ($_SESSION['acceso'] == "administradorG" || $_SESSION["acceso"]=="administradorS") {

		$sql = "SELECT codcliente FROM ventas WHERE codcliente = ?";
		$stmt = $this->dbh->prepare($sql);
		$stmt->execute(array(decrypt($_GET["codcliente"])));
		$num = $stmt->rowCount();
		if($num == 0)
		{

			$sql = "DELETE FROM clientes where codcliente = ?";
			$stmt = $this->dbh->prepare($sql);
			$stmt->bindParam(1,$codcliente);
			$codcliente = decrypt($_GET["codcliente"]);
			$stmt->execute();

			echo "1";
			exit;

		} else {
		   
			echo "2";
			exit;
		  } 
			
		} else {
		
		echo "3";
		exit;
	 }	
}
########################## FUNCION ELIMINAR CLIENTES #################################

############################## FIN DE CLASE CLIENTES #################################


























################################## CLASE PROVEEDORES ###################################

########################## FUNCION CARGAR PROVEEDORES ###############################
	public function CargarProveedores()
	{
		self::SetNames();
		if(empty($_FILES["sel_file"]))
		{
			echo "1";
			exit;
		}
        //Aquí es donde seleccionamos nuestro csv
         $fname = $_FILES['sel_file']['name'];
         //echo 'Cargando nombre del archivo: '.$fname.' ';
         $chk_ext = explode(".",$fname);
         
        if(strtolower(end($chk_ext)) == "csv")
        {
        //si es correcto, entonces damos permisos de lectura para subir
        $filename = $_FILES['sel_file']['tmp_name'];
        $handle = fopen($filename, "r");
        $this->dbh->beginTransaction();
        
        while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {

               //Insertamos los datos con los valores...
			   
		$query = "INSERT INTO proveedores values (null, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);";
		$stmt = $this->dbh->prepare($query);
		$stmt->bindParam(1, $codproveedor);
		$stmt->bindParam(2, $documproveedor);
		$stmt->bindParam(3, $dniproveedor);
		$stmt->bindParam(4, $nomproveedor);
		$stmt->bindParam(5, $tlfproveedor);
		$stmt->bindParam(6, $id_provincia);
		$stmt->bindParam(7, $id_departamento);
		$stmt->bindParam(8, $direcproveedor);
		$stmt->bindParam(9, $emailproveedor);
		$stmt->bindParam(10, $vendedor);
		$stmt->bindParam(11, $tlfvendedor);
		$stmt->bindParam(12, $fechaingreso);

		$codproveedor = limpiar($data[0]);
		$documproveedor = limpiar($data[1]);
		$dniproveedor = limpiar($data[2]);
		$nomproveedor = limpiar($data[3]);
		$tlfproveedor = limpiar($data[4]);
		$id_provincia = limpiar($data[5]);
		$id_departamento = limpiar($data[6]);
		$direcproveedor = limpiar($data[7]);
		$emailproveedor = limpiar($data[8]);
		$vendedor = limpiar($data[9]);
		$tlfvendedor = limpiar($data[10]);
		$fechaingreso = limpiar(date("Y-m-d"));
		$stmt->execute();
				
        }
           $this->dbh->commit();
           //cerramos la lectura del archivo "abrir archivo" con un "cerrar archivo"
           fclose($handle);
	        
	echo "<span class='fa fa-check-square-o'></span> LA CARGA MASIVA DE PROVEEDORES FUE REALIZADA EXITOSAMENTE";
	exit;
             
         }
         else
         {
    //si aparece esto es posible que el archivo no tenga el formato adecuado, inclusive cuando es cvs, revisarlo para ver si esta separado por " , "
         echo "2";
		 exit;
      }  
}
############################# FUNCION CARGAR PROVEEDORES ##############################

############################ FUNCION REGISTRAR PROVEEDORES ##########################
	public function RegistrarProveedores()
	{
		self::SetNames();
		if(empty($_POST["cuitproveedor"]) or empty($_POST["nomproveedor"]) or empty($_POST["direcproveedor"]))
		{
			echo "1";
			exit;
		}

		$sql = "SELECT codproveedor FROM proveedores ORDER BY idproveedor DESC LIMIT 1";
		foreach ($this->dbh->query($sql) as $row){

			$id=$row["codproveedor"];

		}
		if(empty($id))
		{
			$codproveedor = "P1";

		} else {

			$resto = substr($id, 0, 1);
			$coun = strlen($resto);
			$num     = substr($id, $coun);
			$codigo     = $num + 1;
			$codproveedor = "P".$codigo;
		}

		$sql = " SELECT cuitproveedor FROM proveedores WHERE cuitproveedor = ?";
		$stmt = $this->dbh->prepare($sql);
		$stmt->execute(array($_POST["cuitproveedor"]));
		$num = $stmt->rowCount();
		if($num == 0)
		{
			$query = " INSERT INTO proveedores values (null, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);";
			$stmt = $this->dbh->prepare($query);
			$stmt->bindParam(1, $codproveedor);
		    $stmt->bindParam(2, $documproveedor);
			$stmt->bindParam(3, $cuitproveedor);
			$stmt->bindParam(4, $nomproveedor);
			$stmt->bindParam(5, $tlfproveedor);
			$stmt->bindParam(6, $id_provincia);
			$stmt->bindParam(7, $id_departamento);
			$stmt->bindParam(8, $direcproveedor);
			$stmt->bindParam(9, $emailproveedor);
			$stmt->bindParam(10, $vendedor);
			$stmt->bindParam(11, $tlfvendedor);
			$stmt->bindParam(12, $fechaingreso);
			
			$documproveedor = limpiar($_POST['documproveedor'] == '' ? "0" : $_POST['documproveedor']);
			$cuitproveedor = limpiar($_POST["cuitproveedor"]);
			$nomproveedor = limpiar($_POST["nomproveedor"]);
			$tlfproveedor = limpiar($_POST["tlfproveedor"]);
			$id_provincia = limpiar($_POST['id_provincia'] == '' ? "0" : $_POST['id_provincia']);
			$id_departamento = limpiar($_POST['id_departamento'] == '' ? "0" : $_POST['id_departamento']);
			$direcproveedor = limpiar($_POST["direcproveedor"]);
			$emailproveedor = limpiar($_POST["emailproveedor"]);
			$vendedor = limpiar($_POST["vendedor"]);
			$tlfvendedor = limpiar($_POST["tlfvendedor"]);
		    $fechaingreso = limpiar(date("Y-m-d"));
			$stmt->execute();

			echo "<span class='fa fa-check-square-o'></span> EL PROVEEDOR HA SIDO REGISTRADO EXITOSAMENTE";
			exit;

		} else {

			echo "2";
			exit;
		}
	}
########################### FUNCION REGISTRAR PROVEEDORES ########################

########################### FUNCION LISTAR PROVEEDORES ################################
	public function ListarProveedores()
	{
		self::SetNames();
	    $sql = "SELECT
		proveedores.codproveedor,
		proveedores.documproveedor,
		proveedores.cuitproveedor,
		proveedores.nomproveedor,
		proveedores.tlfproveedor,
		proveedores.id_provincia,
		proveedores.id_departamento,
		proveedores.direcproveedor,
		proveedores.emailproveedor,
		proveedores.vendedor,
		proveedores.tlfvendedor,
		proveedores.fechaingreso,
	    documentos.documento,
		provincias.provincia,
		departamentos.departamento
		FROM proveedores 
		LEFT JOIN documentos ON proveedores.documproveedor = documentos.coddocumento
		LEFT JOIN provincias ON proveedores.id_provincia = provincias.id_provincia 
		LEFT JOIN departamentos ON proveedores.id_departamento = departamentos.id_departamento";
	foreach ($this->dbh->query($sql) as $row)
	{
		$this->p[] = $row;
	}
	return $this->p;
	$this->dbh=null;
}
########################### FUNCION LISTAR PROVEEDORES ################################

########################### FUNCION ID PROVEEDORES #################################
	public function ProveedoresPorId()
	{
		self::SetNames();
		$sql = "SELECT
		proveedores.codproveedor,
		proveedores.documproveedor,
		proveedores.cuitproveedor,
		proveedores.nomproveedor,
		proveedores.tlfproveedor,
		proveedores.id_provincia,
		proveedores.id_departamento,
		proveedores.direcproveedor,
		proveedores.emailproveedor,
		proveedores.vendedor,
		proveedores.tlfvendedor,
		proveedores.fechaingreso,
	    documentos.documento,
		provincias.provincia,
		departamentos.departamento
		FROM proveedores 
		LEFT JOIN documentos ON proveedores.documproveedor = documentos.coddocumento
		LEFT JOIN provincias ON proveedores.id_provincia = provincias.id_provincia 
		LEFT JOIN departamentos ON proveedores.id_departamento = departamentos.id_departamento WHERE proveedores.codproveedor = ?";
		$stmt = $this->dbh->prepare($sql);
		$stmt->execute(array(decrypt($_GET["codproveedor"])));
		$num = $stmt->rowCount();
		if($num==0)
		{
			echo "";
		}
		else
		{
			if($row = $stmt->fetch(PDO::FETCH_ASSOC))
			{
				$this->p[] = $row;
			}
			return $this->p;
			$this->dbh=null;
		}
	}
############################ FUNCION ID PROVEEDORES #################################
	
############################ FUNCION ACTUALIZAR PROVEEDORES ############################
	public function ActualizarProveedores()
	{
	self::SetNames();
		if(empty($_POST["codproveedor"]) or empty($_POST["cuitproveedor"]) or empty($_POST["nomproveedor"]) or empty($_POST["direcproveedor"]))
		{
			echo "1";
			exit;
		}
		$sql = " SELECT cuitproveedor FROM proveedores WHERE codproveedor != ? AND cuitproveedor = ?";
		$stmt = $this->dbh->prepare($sql);
		$stmt->execute(array($_POST["codproveedor"],$_POST["cuitproveedor"]));
		$num = $stmt->rowCount();
		if($num == 0)
		{
			$sql = "UPDATE proveedores set "
			." documproveedor = ?, "
			." cuitproveedor = ?, "
			." nomproveedor = ?, "
			." tlfproveedor = ?, "
			." id_provincia = ?, "
			." id_departamento = ?, "
			." direcproveedor = ?, "
			." emailproveedor = ?, "
			." vendedor = ?, "
			." tlfvendedor = ? "
			." where "
			." codproveedor = ?;
			";
			$stmt = $this->dbh->prepare($sql);
			$stmt->bindParam(1, $documproveedor);
			$stmt->bindParam(2, $cuitproveedor);
			$stmt->bindParam(3, $nomproveedor);
			$stmt->bindParam(4, $tlfproveedor);
			$stmt->bindParam(5, $id_provincia);
			$stmt->bindParam(6, $id_departamento);
			$stmt->bindParam(7, $direcproveedor);
			$stmt->bindParam(8, $emailproveedor);
			$stmt->bindParam(9, $vendedor);
			$stmt->bindParam(10, $tlfvendedor);
			$stmt->bindParam(11, $codproveedor);
			
			$documproveedor = limpiar($_POST['documproveedor'] == '' ? "0" : $_POST['documproveedor']);
			$cuitproveedor = limpiar($_POST["cuitproveedor"]);
			$nomproveedor = limpiar($_POST["nomproveedor"]);
			$tlfproveedor = limpiar($_POST["tlfproveedor"]);
			$id_provincia = limpiar($_POST['id_provincia'] == '' ? "0" : $_POST['id_provincia']);
			$id_departamento = limpiar($_POST['id_departamento'] == '' ? "0" : $_POST['id_departamento']);
			$direcproveedor = limpiar($_POST["direcproveedor"]);
			$emailproveedor = limpiar($_POST["emailproveedor"]);
			$vendedor = limpiar($_POST["vendedor"]);
			$tlfvendedor = limpiar($_POST["tlfvendedor"]);
			$codproveedor = limpiar($_POST["codproveedor"]);
			$stmt->execute();
        
		echo "<span class='fa fa-check-square-o'></span> EL PROVEEDOR HA SIDO ACTUALIZADO EXITOSAMENTE";
		exit;

		} else {

			echo "2";
			exit;
		}
	}
############################ FUNCION ACTUALIZAR PROVEEDORES ############################

########################## FUNCION ELIMINAR PROVEEDORES #################################
	public function EliminarProveedores()
	{
	self::SetNames();
		if ($_SESSION['acceso'] == "administradorG" || $_SESSION["acceso"]=="administradorS") {

		$sql = "SELECT codproveedor FROM productos WHERE codproveedor = ?";
		$stmt = $this->dbh->prepare($sql);
		$stmt->execute(array(decrypt($_GET["codproveedor"])));
		$num = $stmt->rowCount();
		if($num == 0)
		{

			$sql = "DELETE FROM proveedores where codproveedor = ?";
			$stmt = $this->dbh->prepare($sql);
			$stmt->bindParam(1,$codproveedor);
			$codproveedor = decrypt($_GET["codproveedor"]);
			$stmt->execute();

			echo "1";
			exit;

		} else {
		   
			echo "2";
			exit;
		  } 
			
		} else {
		
		echo "3";
		exit;
	 }	
}
########################### FUNCION ELIMINAR PROVEEDORES #########################

############################## FIN DE CLASE PROVEEDORES #################################




























###################################### CLASE PEDIDOS ###################################

############################ FUNCION REGISTRAR PEDIDOS #############################
	public function RegistrarPedidos()
	{
		self::SetNames();
		if(empty($_POST["codsucursal"]) or empty($_POST["codproveedor"]) or empty($_POST["fecharegistro"]) or empty($_POST["observacionpedido"]))
		{
			echo "1";
			exit;
		}

		if(empty($_SESSION["CarritoPedido"]))
		{
			echo "2";
			exit;
			
		} else {

	################### CREO LOS CODIGO DE PEDIDO ####################
		$sql = " SELECT codsucursal, nroactividadsucursal, iniciofactura FROM sucursales WHERE codsucursal = '".limpiar($_POST["codsucursal"])."'";
		foreach ($this->dbh->query($sql) as $row)
		{
			$this->p[] = $row;
		}
		$nroactividad = $row['nroactividadsucursal'];
		$iniciofactura = $row['iniciofactura'];
		
		$sql = "SELECT codpedido FROM pedidos WHERE codsucursal = '".limpiar($_POST["codsucursal"])."' ORDER BY idpedido DESC LIMIT 1";
		 foreach ($this->dbh->query($sql) as $row){

			$pedido=$row["codpedido"];

		}
		if(empty($pedido))
		{
			$codpedido = $nroactividad.'-'.$iniciofactura;

		} else {

			$var = strlen($nroactividad."-");
            $var1 = substr($pedido , $var);
            $var2 = strlen($var1);
            $var3 = $var1 + 1;
            $var4 = str_pad($var3, $var2, "0", STR_PAD_LEFT);
            $codpedido = $nroactividad.'-'.$var4;
		}
    ################### CREO LOS CODIGO DE PEDIDO ####################

$query = "INSERT INTO pedidos values (null, ?, ?, ?, ?, ?, ?);";
		$stmt = $this->dbh->prepare($query);
		$stmt->bindParam(1, $codpedido);
		$stmt->bindParam(2, $codproveedor);
		$stmt->bindParam(3, $observacionpedido);
		$stmt->bindParam(4, $fechapedido);
		$stmt->bindParam(5, $codigo);
		$stmt->bindParam(6, $codsucursal);
	    
		$codproveedor = limpiar($_POST["codproveedor"]);
		$observacionpedido = limpiar($_POST["observacionpedido"]);
        $fechapedido = limpiar(date("Y-m-d",strtotime($_POST['fecharegistro'])));
		$codigo = limpiar($_SESSION["codigo"]);
		$codsucursal = limpiar($_POST["codsucursal"]);
		$stmt->execute();
		
		$this->dbh->beginTransaction();
		$detalle = $_SESSION["CarritoPedido"];
		for($i=0;$i<count($detalle);$i++){
		
        $query = "INSERT INTO detallepedidos values (null, ?, ?, ?, ?, ?, ?, ?);";
		$stmt = $this->dbh->prepare($query);
		$stmt->bindParam(1, $codpedido);
	    $stmt->bindParam(2, $codproducto);
	    $stmt->bindParam(3, $producto);
		$stmt->bindParam(4, $codmarca);
		$stmt->bindParam(5, $codmodelo);
		$stmt->bindParam(6, $cantpedido);
		$stmt->bindParam(7, $codsucursal);
			
		$codproducto = limpiar($detalle[$i]['txtCodigo']);
		$producto = limpiar($detalle[$i]['producto']);
		$codmarca = limpiar($detalle[$i]['codmarca']);
		$codmodelo = limpiar($detalle[$i]['codmodelo']);
		$cantpedido = limpiar($detalle[$i]['cantidad']);
		$codsucursal = limpiar($_POST["codsucursal"]);
		$stmt->execute();
      }
        ####################### DESTRUYO LA VARIABLE DE SESSION #####################
      unset($_SESSION["CarritoPedido"]);
      $this->dbh->commit();
		
echo "<span class='fa fa-check-square-o'></span> EL PEDIDO DE PRODUCTOS HA SIDO REGISTRADO EXITOSAMENTE <a href='reportepdf?codpedido=".encrypt($codpedido)."&codsucursal=".encrypt($codsucursal)."&tipo=".encrypt("FACTURAPEDIDO")."' class='on-default' data-placement='left' data-toggle='tooltip' data-original-title='Imprimir Documento' target='_black' rel='noopener noreferrer'><font color='black'><strong>IMPRIMIR REPORTE</strong></font color></a></div>";

echo "<script>window.open('reportepdf?codpedido=".encrypt($codpedido)."&codsucursal=".encrypt($codsucursal)."&tipo=".encrypt("FACTURAPEDIDO")."', '_blank');</script>";
	exit;
	}
}
############################ FUNCION REGISTRAR PEDIDOS ###############################

########################### FUNCION LISTAR PEDIDOS ################################
public function ListarPedidos()
{
	self::SetNames();

	if ($_SESSION['acceso'] == "administradorG") {

	$sql = "SELECT 
	pedidos.codpedido, 
	pedidos.codproveedor, 
	pedidos.observacionpedido, 
	pedidos.fechapedido, 
	pedidos.codsucursal,
	sucursales.documsucursal, 
	sucursales.cuitsucursal, 
	sucursales.razonsocial,
	sucursales.documencargado,
	sucursales.dniencargado,
	sucursales.nomencargado,
	documentos.documento,
	documentos2.documento AS documento2, 
	documentos3.documento AS documento3, 
	proveedores.documproveedor, 
	proveedores.cuitproveedor, 
	proveedores.nomproveedor, 
	proveedores.tlfproveedor, 
	SUM(detallepedidos.cantpedido) AS articulos 
	FROM (pedidos LEFT JOIN detallepedidos ON detallepedidos.codpedido = pedidos.codpedido) 
	LEFT JOIN sucursales ON pedidos.codsucursal = sucursales.codsucursal 
	LEFT JOIN documentos ON sucursales.documsucursal = documentos.coddocumento
	LEFT JOIN documentos AS documentos2 ON sucursales.documencargado = documentos2.coddocumento 
	LEFT JOIN proveedores ON pedidos.codproveedor = proveedores.codproveedor 
	LEFT JOIN documentos AS documentos3 ON proveedores.documproveedor = documentos3.coddocumento
	LEFT JOIN usuarios ON pedidos.codigo = usuarios.codigo GROUP BY detallepedidos.codpedido ORDER BY pedidos.idpedido DESC";
	foreach ($this->dbh->query($sql) as $row)
	{
		$this->p[] = $row;
	}
	return $this->p;
	$this->dbh=null;

     } else {

   $sql = "SELECT 
	pedidos.codpedido, 
	pedidos.codproveedor, 
	pedidos.observacionpedido, 
	pedidos.fechapedido, 
	pedidos.codsucursal,
	sucursales.documsucursal, 
	sucursales.cuitsucursal, 
	sucursales.razonsocial,
	sucursales.documencargado,
	sucursales.dniencargado,
	sucursales.nomencargado,
	documentos.documento,
	documentos2.documento AS documento2, 
	documentos3.documento AS documento3, 
	proveedores.documproveedor, 
	proveedores.cuitproveedor, 
	proveedores.nomproveedor, 
	proveedores.tlfproveedor, 
	SUM(detallepedidos.cantpedido) AS articulos 
	FROM (pedidos LEFT JOIN detallepedidos ON detallepedidos.codpedido = pedidos.codpedido) 
	LEFT JOIN sucursales ON pedidos.codsucursal = sucursales.codsucursal 
	LEFT JOIN documentos ON sucursales.documsucursal = documentos.coddocumento
	LEFT JOIN documentos AS documentos2 ON sucursales.documencargado = documentos2.coddocumento 
	LEFT JOIN proveedores ON pedidos.codproveedor = proveedores.codproveedor 
	LEFT JOIN documentos AS documentos3 ON proveedores.documproveedor = documentos3.coddocumento
	LEFT JOIN usuarios ON pedidos.codigo = usuarios.codigo WHERE pedidos.codsucursal = '".limpiar($_SESSION["codsucursal"])."' GROUP BY detallepedidos.codpedido ORDER BY pedidos.idpedido DESC";
	foreach ($this->dbh->query($sql) as $row)
	{
		$this->p[] = $row;
	}
	return $this->p;
	$this->dbh=null;

     }
}
############################# FUNCION LISTAR PEDIDOS ############################

############################ FUNCION ID PEDIDOS #################################
	public function PedidosPorId()
	{
		self::SetNames();
		$sql = "SELECT 
		pedidos.codpedido, 
		pedidos.codproveedor,
		pedidos.observacionpedido, 
		pedidos.fechapedido,
		pedidos.codsucursal,
		sucursales.documsucursal,
		sucursales.cuitsucursal,
		sucursales.razonsocial,
		sucursales.direcsucursal,
		sucursales.correosucursal,
		sucursales.tlfsucursal,
		sucursales.documencargado,
		sucursales.dniencargado,
		sucursales.nomencargado,
		sucursales.tlfencargado,
	    proveedores.documproveedor, 
		proveedores.cuitproveedor, 
		proveedores.nomproveedor, 
		proveedores.tlfproveedor, 
		proveedores.id_provincia, 
		proveedores.id_departamento, 
		proveedores.direcproveedor, 
		proveedores.emailproveedor,  
		proveedores.vendedor,
		proveedores.tlfvendedor,
	    documentos.documento,
	    documentos2.documento AS documento2, 
	    documentos3.documento AS documento3, 
		usuarios.dni, 
		usuarios.nombres,
		provincias.provincia,
		departamentos.departamento,
		provincias2.provincia AS provincia2,
		departamentos2.departamento AS departamento2
		FROM (pedidos INNER JOIN sucursales ON pedidos.codsucursal = sucursales.codsucursal)
	    LEFT JOIN documentos ON sucursales.documsucursal = documentos.coddocumento
	    LEFT JOIN documentos AS documentos2 ON sucursales.documencargado = documentos2.coddocumento 
		LEFT JOIN provincias ON sucursales.id_provincia = provincias.id_provincia 
		LEFT JOIN departamentos ON sucursales.id_departamento = departamentos.id_departamento 
		INNER JOIN proveedores ON proveedores.codproveedor = pedidos.codproveedor
	    LEFT JOIN documentos AS documentos3 ON proveedores.documproveedor = documentos3.coddocumento
		LEFT JOIN provincias AS provincias2 ON proveedores.id_provincia = provincias2.id_provincia 
		LEFT JOIN departamentos AS departamentos2 ON proveedores.id_departamento = departamentos2.id_departamento 
		LEFT JOIN usuarios ON pedidos.codigo = usuarios.codigo
		WHERE pedidos.codpedido = ? AND pedidos.codsucursal = ?";
		$stmt = $this->dbh->prepare($sql);
		$stmt->execute(array(decrypt($_GET["codpedido"]),decrypt($_GET["codsucursal"])));
		$num = $stmt->rowCount();
		if($num==0)
		{
			echo "";
		}
		else
		{
			if($row = $stmt->fetch(PDO::FETCH_ASSOC))
			{
				$this->p[] = $row;
			}
			return $this->p;
			$this->dbh=null;
		}
	}
############################ FUNCION ID PEDIDOS #################################
	
########################### FUNCION VER DETALLES PEDIDOS ###########################
public function VerDetallesPedidos()
	{
		self::SetNames();
		$sql = "SELECT * FROM detallepedidos INNER JOIN marcas ON detallepedidos.codmarca = marcas.codmarca LEFT JOIN modelos ON detallepedidos.codmodelo = modelos.codmodelo WHERE detallepedidos.codpedido = ? AND detallepedidos.codsucursal = ?";
		$stmt = $this->dbh->prepare($sql);
		$stmt->execute(array(decrypt($_GET["codpedido"]),decrypt($_GET["codsucursal"])));
		$num = $stmt->rowCount();
		
			while($row = $stmt->fetch(PDO::FETCH_ASSOC))
			{
				$this->p[]=$row;
			}
			return $this->p;
			$this->dbh=null;
		}
########################### FUNCION VER DETALLES PEDIDOS ############################

########################### FUNCION ACTUALIZAR PEDIDOS #############################
	public function ActualizarPedidos()
	{
		self::SetNames();
		if(empty($_POST["codsucursal"]) or empty($_POST["codproveedor"]) or empty($_POST["fechapedido"]) or empty($_POST["observacionpedido"]))
		{
			echo "1";
			exit;
		}


		for($i=0;$i<count($_POST['coddetallepedido']);$i++){  //recorro el array
	        if (!empty($_POST['coddetallepedido'][$i])) {

		       if($_POST['cantpedido'][$i]==0){

			      echo "2";
			      exit();

		       }
	                                                 }
                                              }

		$sql = " UPDATE pedidos SET "
			  ." codproveedor = ?, "
			  ." observacionpedido = ?, "
			  ." fechapedido= ? "
			  ." WHERE "
			  ." codpedido = ? AND codsucursal = ?;
			   ";
		$stmt = $this->dbh->prepare($sql);
		$stmt->bindParam(1, $codproveedor);
		$stmt->bindParam(2, $observacionpedido);
		$stmt->bindParam(3, $fechapedido);
		$stmt->bindParam(4, $codpedido);
		$stmt->bindParam(5, $codsucursal);
		
		$codproveedor = limpiar($_POST["codproveedor"]);
		$observacionpedido = limpiar($_POST["observacionpedido"]);
        $fechapedido = limpiar(date("Y-m-d h:i:s",strtotime($_POST['fechapedido'])));
		$codpedido = limpiar($_POST["codpedido"]);
		$codsucursal = limpiar($_POST["codsucursal"]);
		$stmt->execute();

        $this->dbh->beginTransaction();
	for($i=0;$i<count($_POST['coddetallepedido']);$i++){  //recorro el array
	if (!empty($_POST['coddetallepedido'][$i])) {

		$query = "UPDATE detallepedidos set"
		." cantpedido = ? "
		." WHERE "
		." coddetallepedido = ? AND codpedido = ? AND codsucursal = ?;
		";
		$stmt = $this->dbh->prepare($query);
		$stmt->bindParam(1, $cantpedido);
		$stmt->bindParam(2, $coddetallepedido);
		$stmt->bindParam(3, $codpedido);
		$stmt->bindParam(4, $codsucursal);

		$cantpedido = limpiar($_POST['cantpedido'][$i]);
		$coddetallepedido = limpiar($_POST['coddetallepedido'][$i]);
		$codpedido = limpiar($_POST["codpedido"]);
		$codsucursal = limpiar($_POST["codsucursal"]);
		$stmt->execute();

	                                                 }
                                              }
        $this->dbh->commit();


echo "<span class='fa fa-check-square-o'></span> EL PEDIDO DE PRODUCTOS HA SIDO ACTUALIZADO EXITOSAMENTE <a href='reportepdf?codpedido=".encrypt($codpedido)."&codsucursal=".encrypt($codsucursal)."&tipo=".encrypt("FACTURAPEDIDO")."' class='on-default' data-placement='left' data-toggle='tooltip' data-original-title='Imprimir Documento' target='_black' rel='noopener noreferrer'><font color='black'><strong>IMPRIMIR REPORTE</strong></font color></a></div>";

echo "<script>window.open('reportepdf?codpedido=".encrypt($codpedido)."&codsucursal=".encrypt($codsucursal)."&tipo=".encrypt("FACTURAPEDIDO")."', '_blank');</script>";
	exit;
	//}
}
########################### FUNCION ACTUALIZAR PEDIDOS ############################

########################### FUNCION ACTUALIZAR PEDIDOS ############################
	public function AgregarDetallesPedidos()
	{
		self::SetNames();
		if(empty($_POST["codproveedor"]) or empty($_POST["fechapedido"]) or empty($_POST["observacionpedido"]))
		{
			echo "1";
			exit;
		}


        if(empty($_SESSION["CarritoPedido"]))
		{
			echo "2";
			exit;
			
		} else {


		$sql = " UPDATE pedidos SET "
			  ." codproveedor = ?, "
			  ." observacionpedido = ?, "
			  ." fechapedido= ? "
			  ." WHERE "
			  ." codpedido = ? AND codsucursal = ?;
			   ";
		$stmt = $this->dbh->prepare($sql);
		$stmt->bindParam(1, $codproveedor);
		$stmt->bindParam(2, $observacionpedido);
		$stmt->bindParam(3, $fechapedido);
		$stmt->bindParam(4, $codpedido);
		$stmt->bindParam(5, $codsucursal);
		
		$codproveedor = limpiar($_POST["codproveedor"]);
		$observacionpedido = limpiar($_POST["observacionpedido"]);
        $fechapedido = limpiar(date("Y-m-d h:i:s",strtotime($_POST['fechapedido'])));
		$codpedido = limpiar($_POST["codpedido"]);
		$codsucursal = limpiar($_POST["codsucursal"]);
		$stmt->execute();

	    $this->dbh->beginTransaction();
	    $detalle = $_SESSION["CarritoPedido"];
		for($i=0;$i<count($detalle);$i++){

	$sql = "SELECT codpedido, codproducto FROM detallepedidos WHERE codpedido = '".limpiar($_POST['codpedido'])."' AND codsucursal = '".limpiar($_POST['codsucursal'])."' AND codproducto = '".limpiar($detalle[$i]['txtCodigo'])."'";
			$stmt = $this->dbh->prepare($sql);
			$stmt->execute();
			$num = $stmt->rowCount();
			if($num == 0)
			{

        $query = "INSERT INTO detallepedidos values (null, ?, ?, ?, ?, ?, ?, ?); ";
		$stmt = $this->dbh->prepare($query);
		$stmt->bindParam(1, $codpedido);
	    $stmt->bindParam(2, $codproducto);
	    $stmt->bindParam(3, $producto);
		$stmt->bindParam(4, $codmarca);
		$stmt->bindParam(5, $codmodelo);
		$stmt->bindParam(6, $cantpedido);
		$stmt->bindParam(7, $codsucursal);
			
		$codpedido = limpiar($_POST["codpedido"]);
		$codproducto = limpiar($detalle[$i]['txtCodigo']);
		$producto = limpiar($detalle[$i]['producto']);
		$codmarca = limpiar($detalle[$i]['codmarca']);
		$codmodelo = limpiar($detalle[$i]['codmodelo']);
		$cantpedido = limpiar($detalle[$i]['cantidad']);
        $codsucursal = limpiar($_POST['codsucursal']);
		$stmt->execute();

	  } else {

	  	$sql = "SELECT cantpedido FROM detallepedidos WHERE codpedido = '".limpiar($_POST['codpedido'])."' AND codsucursal = '".limpiar($_POST['codsucursal'])."' AND codproducto = '".limpiar($detalle[$i]['txtCodigo'])."'";
		foreach ($this->dbh->query($sql) as $row)
		{
			$this->p[] = $row;
		}
		$cantidad = $row['cantpedido'];

	  	$query = "UPDATE detallepedidos set"
		." codmodelo = ?, "
		." cantpedido = ? "
		." WHERE "
		." codpedido = ? AND codsucursal = ? AND codproducto = ?;
		";
		$stmt = $this->dbh->prepare($query);
		$stmt->bindParam(1, $codmodelo);
		$stmt->bindParam(2, $cantpedido);
		$stmt->bindParam(3, $codpedido);
		$stmt->bindParam(4, $codsucursal);
		$stmt->bindParam(5, $codproducto);

		$codmodelo = limpiar($detalle[$i]['codmodelo']);
		$cantpedido = limpiar($detalle[$i]['cantidad']+$cantidad);
		$codpedido = limpiar($_POST["codpedido"]);
		$codsucursal = limpiar($_POST["codsucursal"]);
		$codproducto = limpiar($detalle[$i]['txtCodigo']);
		$stmt->execute();
	 }
   }
      ####################### DESTRUYO LA VARIABLE DE SESSION #####################
	  unset($_SESSION["CarritoPedido"]);
      $this->dbh->commit();

echo "<span class='fa fa-check-square-o'></span> LOS DETALLES DE PRODUCTOS FUERON AGREGADOS AL PEDIDO EXITOSAMENTE <a href='reportepdf?codpedido=".encrypt($codpedido)."&codsucursal=".encrypt($codsucursal)."&tipo=".encrypt("FACTURAPEDIDO")."' class='on-default' data-placement='left' data-toggle='tooltip' data-original-title='Imprimir Documento' target='_black' rel='noopener noreferrer'><font color='black'><strong>IMPRIMIR REPORTE</strong></font color></a></div>";

echo "<script>window.open('reportepdf?codpedido=".encrypt($codpedido)."&codsucursal=".encrypt($codsucursal)."&tipo=".encrypt("FACTURAPEDIDO")."', '_blank');</script>";
	exit;
	}
}
########################### FUNCION ACTUALIZAR PEDIDOS ############################

########################## FUNCION ELIMINAR DETALLES PEDIDOS #########################
	public function EliminarDetallesPedidos()
	{
	self::SetNames();
		if ($_SESSION['acceso'] == "administradorG" || $_SESSION["acceso"]=="administradorS") {

		$sql = "SELECT * FROM detallepedidos where codpedido = ? AND codsucursal = ?";
		$stmt = $this->dbh->prepare($sql);
		$stmt->execute(array(decrypt($_GET["codpedido"]),decrypt($_GET["codsucursal"])));
		$num = $stmt->rowCount();
		if($num > 1)
		{

			$sql = "DELETE FROM detallepedidos WHERE coddetallepedido = ?";
			$stmt = $this->dbh->prepare($sql);
			$stmt->bindParam(1,$coddetallepedido);
			$coddetallepedido = decrypt($_GET["coddetallepedido"]);
			$stmt->execute();

			echo "1";
			exit;

		} else {
		   
			echo "2";
			exit;
		  } 
			
		} else {
		
		echo "3";
		exit;
	 }	
}
######################## FUNCION ELIMINAR DETALLES PEDIDOS #########################

######################### FUNCION ELIMINAR PEDIDOS ###############################
	public function EliminarPedidos()
	{
	self::SetNames();
		if ($_SESSION['acceso'] == "administradorG" || $_SESSION["acceso"]=="administradorS") {

			$sql = "DELETE FROM pedidos WHERE codpedido = ? AND codsucursal = ?";
			$stmt = $this->dbh->prepare($sql);
			$stmt->bindParam(1,$codpedido);
			$stmt->bindParam(2,$codsucursal);
			$codpedido = decrypt($_GET["codpedido"]);
			$codsucursal = decrypt($_GET["codsucursal"]);
			$stmt->execute();

			$sql = "DELETE FROM detallepedidos WHERE codpedido = ? AND codsucursal = ?";
			$stmt = $this->dbh->prepare($sql);
			$stmt->bindParam(1,$codpedido);
			$stmt->bindParam(2,$codsucursal);
			$codpedido = decrypt($_GET["codpedido"]);
			$codsucursal = decrypt($_GET["codsucursal"]);
			$stmt->execute();

			echo "1";
			exit;

		} else {

			echo "2";
			exit;
		}
	}
####################### FUNCION ELIMINAR PEDIDOS #################################

###################### FUNCION BUSQUEDA PEDIDOS POR PROVEEDORES ######################
	public function BuscarPedidosxProveedor() 
	{
		self::SetNames();
		$sql = "SELECT 
		pedidos.codpedido, 
		pedidos.codproveedor, 
		pedidos.observacionpedido, 
		pedidos.fechapedido, 
		pedidos.codsucursal,
		sucursales.documsucursal, 
		sucursales.cuitsucursal, 
		sucursales.razonsocial,
		sucursales.documencargado,
		sucursales.dniencargado,
		sucursales.nomencargado,
	    proveedores.documproveedor, 
		proveedores.codproveedor, 
		proveedores.cuitproveedor, 
		proveedores.nomproveedor, 
		proveedores.tlfproveedor, 
		proveedores.direcproveedor, 
		proveedores.vendedor, 
		provincias.provincia, 
	    documentos.documento,
	    documentos2.documento AS documento2, 
	    documentos3.documento AS documento3, 
		departamentos.departamento,
		SUM(detallepedidos.cantpedido) as articulos 
		FROM (pedidos LEFT JOIN detallepedidos ON pedidos.codpedido=detallepedidos.codpedido)
		LEFT JOIN sucursales ON pedidos.codsucursal = sucursales.codsucursal 
		LEFT JOIN documentos ON sucursales.documsucursal = documentos.coddocumento
		LEFT JOIN documentos AS documentos2 ON sucursales.documencargado = documentos2.coddocumento  
		INNER JOIN proveedores ON pedidos.codproveedor = proveedores.codproveedor 
		LEFT JOIN documentos AS documentos3 ON proveedores.documproveedor = documentos3.coddocumento
		INNER JOIN provincias ON proveedores.id_provincia = provincias.id_provincia 
		INNER JOIN departamentos ON proveedores.id_departamento = departamentos.id_departamento 
		WHERE pedidos.codsucursal = ? AND pedidos.codproveedor = ? GROUP BY detallepedidos.codpedido";
		$stmt = $this->dbh->prepare($sql);
		$stmt->execute(array(decrypt($_GET["codsucursal"]),decrypt($_GET["codproveedor"])));
		$num = $stmt->rowCount();
		if($num==0)
		{
	echo "<div class='alert alert-danger'>";
	echo "<button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>";
	echo "<center><span class='fa fa-info-circle'></span> NO SE ENCONTRARON PEDIDOS PARA EL PROVEEDOR SELECCIONADO</center>";
	echo "</div>";		
	exit;
		}
		else
		{
			while($row = $stmt->fetch(PDO::FETCH_ASSOC))
			{
				$this->p[]=$row;
			}
			return $this->p;
			$this->dbh=null;
		}
	}
###################### FUNCION BUSQUEDA PEDIDOS POR PROVEEDORES ######################

############################# FIN DE CLASE PEDIDOS #################################


























################################# CLASE PRODUCTOS ######################################

############################### FUNCION CARGAR PRODUCTOS ##############################
	public function CargarProductos()
	{
		self::SetNames();
		if(empty($_FILES["sel_file"]))
		{
			echo "1";
			exit;
		}

  //$porcentaje=($_SESSION['acceso']=="administradorG" ? "0.00" : $_SESSION['porcentaje']);

        //Aquí es donde seleccionamos nuestro csv
         $fname = $_FILES['sel_file']['name'];
         //echo 'Cargando nombre del archivo: '.$fname.' ';
         $chk_ext = explode(".",$fname);
         
        if(strtolower(end($chk_ext)) == "csv")
        {
        //si es correcto, entonces damos permisos de lectura para subir
        $filename = $_FILES['sel_file']['tmp_name'];
        $handle = fopen($filename, "r");
        $this->dbh->beginTransaction();
        
        while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {

               //Insertamos los datos con los valores...
    $query = "INSERT INTO productos values (null, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);";
    	$stmt = $this->dbh->prepare($query);
    	$stmt->bindParam(1, $codproducto);
    	$stmt->bindParam(2, $producto);
    	$stmt->bindParam(3, $fabricante);
    	$stmt->bindParam(4, $codfamilia);
    	$stmt->bindParam(5, $codsubfamilia);
    	$stmt->bindParam(6, $codmarca);
    	$stmt->bindParam(7, $codmodelo);
    	$stmt->bindParam(8, $codpresentacion);
    	$stmt->bindParam(9, $codcolor);
    	$stmt->bindParam(10, $codorigen);
    	$stmt->bindParam(11, $year);
    	$stmt->bindParam(12, $nroparte);
    	$stmt->bindParam(13, $lote);
    	$stmt->bindParam(14, $peso);
    	$stmt->bindParam(15, $preciocompra);
    	$stmt->bindParam(16, $precioxmenor);
    	$stmt->bindParam(17, $precioxmayor);
    	$stmt->bindParam(18, $precioxpublico);
    	$stmt->bindParam(19, $existencia);
    	$stmt->bindParam(20, $stockoptimo);
    	$stmt->bindParam(21, $stockmedio);
    	$stmt->bindParam(22, $stockminimo);
    	$stmt->bindParam(23, $ivaproducto);
    	$stmt->bindParam(24, $descproducto);
    	$stmt->bindParam(25, $codigobarra);
    	$stmt->bindParam(26, $fechaelaboracion);
    	$stmt->bindParam(27, $fechaoptimo);
    	$stmt->bindParam(28, $fechamedio);
    	$stmt->bindParam(29, $fechaminimo);
    	$stmt->bindParam(30, $codproveedor);
    	$stmt->bindParam(31, $stockteorico);
    	$stmt->bindParam(32, $motivoajuste);
    	$stmt->bindParam(33, $codsucursal);

    	$codproducto = limpiar($data[0]);
    	$producto = limpiar($data[1]);
    	$fabricante = limpiar($data[2]);
    	$codfamilia = limpiar($data[3]);
    	$codsubfamilia = limpiar($data[4]);
    	$codmarca = limpiar($data[5]);
    	$codmodelo = limpiar($data[6]);
    	$codpresentacion = limpiar($data[7]);
    	$codcolor = limpiar($data[8]);
    	$codorigen = limpiar($data[9]);
    	$year = limpiar($data[10]);
    	$nroparte = limpiar($data[11]);
    	$lote = limpiar($data[12]);
    	$peso = limpiar($data[13]);
    	$preciocompra = limpiar($data[14]);
    	$precioxmenor = limpiar($data[15]);
    	$precioxmayor = limpiar($data[16]);
    	$precioxpublico = limpiar($data[17]);
    	$existencia = limpiar($data[18]);
    	$stockoptimo = limpiar($data[19]);
    	$stockmedio = limpiar($data[20]);
    	$stockminimo = limpiar($data[21]);
    	$ivaproducto = limpiar($data[22]);
    	$descproducto = limpiar($data[23]);
    	$codigobarra = limpiar($data[24]);
    	$fechaelaboracion = limpiar($data[25]);
    	$fechaoptimo = limpiar($data[26]);
    	$fechamedio = limpiar($data[27]);
    	$fechaminimo = limpiar($data[28]);
    	$codproveedor = limpiar($data[29]);
    	$stockteorico = limpiar("0");
    	$motivoajuste = limpiar("NINGUNO");
    	$codsucursal = limpiar($data[32]);
    	$stmt->execute();

##################### REGISTRAMOS LOS DATOS DE PRODUCTOS EN KARDEX #####################
		$query = "INSERT INTO kardex values (null, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);";
		$stmt = $this->dbh->prepare($query);
		$stmt->bindParam(1, $codproceso);
		$stmt->bindParam(2, $codresponsable);
		$stmt->bindParam(3, $codproducto);
		$stmt->bindParam(4, $movimiento);
		$stmt->bindParam(5, $entradas);
		$stmt->bindParam(6, $salidas);
		$stmt->bindParam(7, $devolucion);
		$stmt->bindParam(8, $stockactual);
		$stmt->bindParam(9, $ivaproducto);
		$stmt->bindParam(10, $descproducto);
		$stmt->bindParam(11, $precio);
		$stmt->bindParam(12, $documento);
		$stmt->bindParam(13, $fechakardex);
		$stmt->bindParam(14, $codsucursal);
		
		$codproceso = limpiar($data[0]);
		$codresponsable = limpiar("0");
		$codproducto = limpiar($data[0]);
		$movimiento = limpiar("ENTRADAS");
		$entradas = limpiar($data[16]);
		$salidas = limpiar("0");
		$devolucion = limpiar("0");
		$stockactual = limpiar($data[16]);
		$ivaproducto = limpiar($data[19]);
		$descproducto = limpiar($data[20]);
		$precio = limpiar("0.00");
		$documento = limpiar("INVENTARIO INICIAL");
		$fechakardex = limpiar(date("Y-m-d"));
    	$codsucursal = limpiar($data[27]);
		$stmt->execute();
##################### REGISTRAMOS LOS DATOS DE PRODUCTOS EN KARDEX #####################
	
        }
           
           $this->dbh->commit();
           //cerramos la lectura del archivo "abrir archivo" con un "cerrar archivo"
           fclose($handle);
	        
	echo "<span class='fa fa-check-square-o'></span> LA CARGA MASIVA DE PRODUCTOS FUE REALIZADA EXITOSAMENTE";
	exit;
             
         }
         else
         {
    //si aparece esto es posible que el archivo no tenga el formato adecuado, inclusive cuando es cvs, revisarlo para ver si esta separado por " , "
         echo "2";
		 exit;
      }  
}
############################## FUNCION CARGAR PRODUCTOS ##############################

########################### FUNCION REGISTRAR PRODUCTOS ###############################
	public function RegistrarProductos()
	{
		self::SetNames();
		if(empty($_POST["codproducto"]) or empty($_POST["producto"]) or empty($_POST["codfamilia"]))
		{
			echo "1";
			exit;
		}


		$sql = " SELECT codproducto FROM productos WHERE codproducto = ? AND codsucursal = ?";
		$stmt = $this->dbh->prepare($sql);
		$stmt->execute(array($_POST["codproducto"],$_POST["codsucursal"]));
		$num = $stmt->rowCount();
		if($num == 0)
		{
	$query = "INSERT INTO productos values (null, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);";
			$stmt = $this->dbh->prepare($query);
			$stmt->bindParam(1, $codproducto);
			$stmt->bindParam(2, $producto);
			$stmt->bindParam(3, $fabricante);
			$stmt->bindParam(4, $codfamilia);
			$stmt->bindParam(5, $codsubfamilia);
			$stmt->bindParam(6, $codmarca);
			$stmt->bindParam(7, $codmodelo);
			$stmt->bindParam(8, $codpresentacion);
			$stmt->bindParam(9, $codcolor);
			$stmt->bindParam(10, $codorigen);
			$stmt->bindParam(11, $year);
			$stmt->bindParam(12, $nroparte);
			$stmt->bindParam(13, $lote);
			$stmt->bindParam(14, $peso);
			$stmt->bindParam(15, $preciocompra);
			$stmt->bindParam(16, $precioxmenor);
			$stmt->bindParam(17, $precioxmayor);
			$stmt->bindParam(18, $precioxpublico);
			$stmt->bindParam(19, $existencia);
			$stmt->bindParam(20, $stockoptimo);
			$stmt->bindParam(21, $stockmedio);
			$stmt->bindParam(22, $stockminimo);
			$stmt->bindParam(23, $ivaproducto);
			$stmt->bindParam(24, $descproducto);
			$stmt->bindParam(25, $codigobarra);
			$stmt->bindParam(26, $fechaelaboracion);
			$stmt->bindParam(27, $fechaoptimo);
			$stmt->bindParam(28, $fechamedio);
			$stmt->bindParam(29, $fechaminimo);
			$stmt->bindParam(30, $codproveedor);
			$stmt->bindParam(31, $stockteorico);
			$stmt->bindParam(32, $motivoajuste);
			$stmt->bindParam(33, $codsucursal);

			$codproducto = limpiar($_POST["codproducto"]);
			$producto = limpiar($_POST["producto"]);
			$fabricante = limpiar($_POST["fabricante"]);
			$codfamilia = limpiar($_POST["codfamilia"]);
			$codsubfamilia = limpiar($_POST['codsubfamilia'] == '' ? "0" : $_POST['codsubfamilia']);
			$codmarca = limpiar($_POST["codmarca"]);
			$codmodelo = limpiar($_POST['codmodelo'] == '' ? "0" : $_POST['codmodelo']);
			$codpresentacion = limpiar($_POST['codpresentacion'] == '' ? "0" : $_POST['codpresentacion']);
			$codcolor = limpiar($_POST['codcolor'] == '' ? "0" : $_POST['codcolor']);
			$codorigen = limpiar($_POST['codorigen'] == '' ? "0" : $_POST['codorigen']);
			$year = limpiar($_POST["year"]);
			$nroparte = limpiar($_POST["nroparte"]);
			$lote = limpiar($_POST['lote'] == '' ? "0" : $_POST['lote']);
			$peso = limpiar($_POST["peso"]);
			$preciocompra = limpiar($_POST["preciocompra"]);
			$precioxmenor = limpiar($_POST["precioxmenor"]);
			$precioxmayor = limpiar($_POST["precioxmayor"]);
			$precioxpublico = limpiar($_POST["precioxpublico"]);
			$existencia = limpiar($_POST["existencia"]);
			$stockoptimo = limpiar($_POST["stockoptimo"]);
			$stockmedio = limpiar($_POST["stockmedio"]);
			$stockminimo = limpiar($_POST["stockminimo"]);
			$ivaproducto = limpiar($_POST["ivaproducto"]);
			$descproducto = limpiar($_POST["descproducto"]);
			$codigobarra = limpiar($_POST["codigobarra"]);
			$fechaelaboracion = limpiar($_POST['fechaelaboracion'] == '' ? "0000-00-00" : date("Y-m-d",strtotime($_POST['fechaelaboracion'])));
			$fechaoptimo = limpiar($_POST['fechaoptimo'] == '' ? "0000-00-00" : date("Y-m-d",strtotime($_POST['fechaoptimo'])));
			$fechamedio = limpiar($_POST['fechamedio'] == '' ? "0000-00-00" : date("Y-m-d",strtotime($_POST['fechamedio'])));
			$fechaminimo = limpiar($_POST['fechaminimo'] == '' ? "0000-00-00" : date("Y-m-d",strtotime($_POST['fechaminimo'])));
			$codproveedor = limpiar($_POST["codproveedor"]);
			$stockteorico = limpiar("0");
			$motivoajuste = limpiar("NINGUNO");
			$codsucursal = limpiar($_POST["codsucursal"]);
			$stmt->execute();

##################### REGISTRAMOS LOS DATOS DE PRODUCTOS EN KARDEX #####################
			$query = "INSERT INTO kardex values (null, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?); ";
			$stmt = $this->dbh->prepare($query);
			$stmt->bindParam(1, $codproceso);
			$stmt->bindParam(2, $codresponsable);
			$stmt->bindParam(3, $codproducto);
			$stmt->bindParam(4, $movimiento);
			$stmt->bindParam(5, $entradas);
			$stmt->bindParam(6, $salidas);
			$stmt->bindParam(7, $devolucion);
			$stmt->bindParam(8, $stockactual);
			$stmt->bindParam(9, $ivaproducto);
			$stmt->bindParam(10, $descproducto);
			$stmt->bindParam(11, $precio);
			$stmt->bindParam(12, $documento);
			$stmt->bindParam(13, $fechakardex);
			$stmt->bindParam(14, $codsucursal);

			$codproceso = limpiar($_POST['codproducto']);
			$codresponsable = limpiar("0");
			$codproducto = limpiar($_POST['codproducto']);
			$movimiento = limpiar("ENTRADAS");
			$entradas = limpiar($_POST['existencia']);
			$salidas = limpiar("0");
			$devolucion = limpiar("0");
			$stockactual = limpiar($_POST['existencia']);
			$ivaproducto = limpiar($_POST["ivaproducto"]);
			$descproducto = limpiar($_POST["descproducto"]);
			$precio = limpiar("0.00");
			$documento = limpiar("INVENTARIO INICIAL");
			$fechakardex = limpiar(date("Y-m-d"));
			$codsucursal = limpiar($_POST["codsucursal"]);
			$stmt->execute();
##################### REGISTRAMOS LOS DATOS DE PRODUCTOS EN KARDEX #####################


	##################  SUBIR FOTO DE PRODUCTO ######################################
         //datos del arhivo  
if (isset($_FILES['imagen']['name'])) { $nombre_archivo = $_FILES['imagen']['name']; } else { $nombre_archivo =''; }
if (isset($_FILES['imagen']['type'])) { $tipo_archivo = $_FILES['imagen']['type']; } else { $tipo_archivo =''; }
if (isset($_FILES['imagen']['size'])) { $tamano_archivo = $_FILES['imagen']['size']; } else { $tamano_archivo =''; } 
         //compruebo si las características del archivo son las que deseo  
if ((strpos($tipo_archivo,'image/jpeg')!==false)&&$tamano_archivo<200000) 
		 {  
if (move_uploaded_file($_FILES['imagen']['tmp_name'], "fotos/productos/".$nombre_archivo) && rename("fotos/productos/".$nombre_archivo,"fotos/productos/".$codproducto.".jpg"))
		 { 
		 ## se puede dar un aviso
		 } 
		 ## se puede dar otro aviso 
		 }
	##################  FINALIZA SUBIR FOTO DE PRODUCTO ######################################

			echo "<span class='fa fa-check-square-o'></span> EL PRODUCTO HA SIDO REGISTRADO EXITOSAMENTE";
			exit;

		} else {

			echo "2";
			exit;
		}
	}
########################## FUNCION REGISTRAR PRODUCTOS ###############################

########################### FUNCION LISTAR PRODUCTOS ################################
	public function ListarProductos()
	{
		self::SetNames();
        $sql = "SELECT
		productos.idproducto,
		productos.codproducto,
		productos.producto,
		productos.fabricante,
		productos.codfamilia,
		productos.codsubfamilia,
		productos.codmarca,
		productos.codmodelo,
		productos.codpresentacion,
		productos.codcolor,
		productos.codorigen,
		productos.year,
		productos.nroparte,
		productos.lote,
		productos.peso,
		productos.preciocompra,
		productos.precioxmenor,
		productos.precioxmayor,
		productos.precioxpublico,
		productos.existencia,
		productos.stockoptimo,
		productos.stockmedio,
		productos.stockminimo,
		productos.ivaproducto,
		productos.descproducto,
		productos.codigobarra,
		productos.fechaelaboracion,
		productos.fechaoptimo,
		productos.fechamedio,
		productos.fechaminimo,
		productos.codproveedor,
		productos.stockteorico,
		productos.motivoajuste,
		productos.codsucursal,
		sucursales.cuitsucursal,
		sucursales.razonsocial,
		familias.nomfamilia,
		subfamilias.nomsubfamilia,
		proveedores.cuitproveedor,
		proveedores.nomproveedor,
		marcas.nommarca,
		modelos.nommodelo,
		presentaciones.nompresentacion,
		colores.nomcolor,
		origenes.nomorigen
		FROM (productos INNER JOIN sucursales ON productos.codsucursal=sucursales.codsucursal)
    LEFT JOIN familias ON productos.codfamilia=familias.codfamilia
	LEFT JOIN subfamilias ON productos.codsubfamilia=subfamilias.codsubfamilia 
	LEFT JOIN marcas ON productos.codmarca=marcas.codmarca 
	LEFT JOIN modelos ON productos.codmodelo = modelos.codmodelo
	LEFT JOIN presentaciones ON productos.codpresentacion=presentaciones.codpresentacion 
	LEFT JOIN colores ON productos.codcolor=colores.codcolor 
	LEFT JOIN origenes ON productos.codorigen=origenes.codorigen 
	LEFT JOIN proveedores ON productos.codproveedor=proveedores.codproveedor
	WHERE productos.codsucursal = '".limpiar($_SESSION["codsucursal"])."'";
	foreach ($this->dbh->query($sql) as $row)
	{
		$this->p[] = $row;
	}
	return $this->p;
	$this->dbh=null;
}
########################## FUNCION LISTAR PRODUCTOS ################################

########################### FUNCION LISTAR PRODUCTOS EN STOCK OPTIMO ################################
	public function ListarProductosOptimo()
	{
		self::SetNames();

	if ($_SESSION['acceso'] == "administradorG") {
		
        $sql = "SELECT
		productos.idproducto,
		productos.codproducto,
		productos.producto,
		productos.fabricante,
		productos.codfamilia,
		productos.codsubfamilia,
		productos.codmarca,
		productos.codmodelo,
		productos.codpresentacion,
		productos.codcolor,
		productos.codorigen,
		productos.year,
		productos.nroparte,
		productos.lote,
		productos.peso,
		productos.preciocompra,
		productos.precioxmenor,
		productos.precioxmayor,
		productos.precioxpublico,
		productos.existencia,
		productos.stockoptimo,
		productos.stockmedio,
		productos.stockminimo,
		productos.ivaproducto,
		productos.descproducto,
		productos.codigobarra,
		productos.fechaelaboracion,
		productos.fechaoptimo,
		productos.fechamedio,
		productos.fechaminimo,
		productos.codproveedor,
		productos.stockteorico,
		productos.motivoajuste,
		productos.codsucursal,
		sucursales.cuitsucursal,
		sucursales.razonsocial,
		familias.nomfamilia,
		subfamilias.nomsubfamilia,
		proveedores.cuitproveedor,
		proveedores.nomproveedor,
		marcas.nommarca,
		modelos.nommodelo,
		presentaciones.nompresentacion,
		colores.nomcolor,
		origenes.nomorigen
		FROM (productos INNER JOIN sucursales ON productos.codsucursal=sucursales.codsucursal)
    LEFT JOIN familias ON productos.codfamilia=familias.codfamilia
	LEFT JOIN subfamilias ON productos.codsubfamilia=subfamilias.codsubfamilia 
	LEFT JOIN marcas ON productos.codmarca=marcas.codmarca 
	LEFT JOIN modelos ON productos.codmodelo = modelos.codmodelo
	LEFT JOIN presentaciones ON productos.codpresentacion=presentaciones.codpresentacion 
	LEFT JOIN colores ON productos.codcolor=colores.codcolor 
	LEFT JOIN origenes ON productos.codorigen=origenes.codorigen 
	LEFT JOIN proveedores ON productos.codproveedor=proveedores.codproveedor
	WHERE productos.codsucursal = '".limpiar(decrypt($_GET["codsucursal"]))."' AND productos.existencia <= productos.stockoptimo AND productos.existencia > productos.stockmedio";
	foreach ($this->dbh->query($sql) as $row)
	{
		$this->p[] = $row;
	}
	return $this->p;
	$this->dbh=null;
} 
else {

        $sql = "SELECT
		productos.idproducto,
		productos.codproducto,
		productos.producto,
		productos.fabricante,
		productos.codfamilia,
		productos.codsubfamilia,
		productos.codmarca,
		productos.codmodelo,
		productos.codpresentacion,
		productos.codcolor,
		productos.codorigen,
		productos.year,
		productos.nroparte,
		productos.lote,
		productos.peso,
		productos.preciocompra,
		productos.precioxmenor,
		productos.precioxmayor,
		productos.precioxpublico,
		productos.existencia,
		productos.stockoptimo,
		productos.stockmedio,
		productos.stockminimo,
		productos.ivaproducto,
		productos.descproducto,
		productos.codigobarra,
		productos.fechaelaboracion,
		productos.fechaoptimo,
		productos.fechamedio,
		productos.fechaminimo,
		productos.codproveedor,
		productos.stockteorico,
		productos.motivoajuste,
		productos.codsucursal,
		sucursales.cuitsucursal,
		sucursales.razonsocial,
		familias.nomfamilia,
		subfamilias.nomsubfamilia,
		proveedores.cuitproveedor,
		proveedores.nomproveedor,
		marcas.nommarca,
		modelos.nommodelo,
		presentaciones.nompresentacion,
		colores.nomcolor,
		origenes.nomorigen
		FROM (productos INNER JOIN sucursales ON productos.codsucursal=sucursales.codsucursal)
    LEFT JOIN familias ON productos.codfamilia=familias.codfamilia
	LEFT JOIN subfamilias ON productos.codsubfamilia=subfamilias.codsubfamilia 
	LEFT JOIN marcas ON productos.codmarca=marcas.codmarca 
	LEFT JOIN modelos ON productos.codmodelo = modelos.codmodelo
	LEFT JOIN presentaciones ON productos.codpresentacion=presentaciones.codpresentacion 
	LEFT JOIN colores ON productos.codcolor=colores.codcolor 
	LEFT JOIN origenes ON productos.codorigen=origenes.codorigen 
	LEFT JOIN proveedores ON productos.codproveedor=proveedores.codproveedor
	WHERE productos.codsucursal = '".limpiar($_SESSION["codsucursal"])."' AND productos.existencia <= productos.stockoptimo AND productos.existencia > productos.stockmedio";
	foreach ($this->dbh->query($sql) as $row)
	{
		$this->p[] = $row;
	}
	return $this->p;
	$this->dbh=null;
  }
}
########################## FUNCION LISTAR PRODUCTOS EN STOCK OPTIMO ################################

########################### FUNCION LISTAR PRODUCTOS EN STOCK MEDIO ################################
	public function ListarProductosMedio()
	{
		self::SetNames();

	if ($_SESSION['acceso'] == "administradorG") {
		
        $sql = "SELECT
		productos.idproducto,
		productos.codproducto,
		productos.producto,
		productos.fabricante,
		productos.codfamilia,
		productos.codsubfamilia,
		productos.codmarca,
		productos.codmodelo,
		productos.codpresentacion,
		productos.codcolor,
		productos.codorigen,
		productos.year,
		productos.nroparte,
		productos.lote,
		productos.peso,
		productos.preciocompra,
		productos.precioxmenor,
		productos.precioxmayor,
		productos.precioxpublico,
		productos.existencia,
		productos.stockoptimo,
		productos.stockmedio,
		productos.stockminimo,
		productos.ivaproducto,
		productos.descproducto,
		productos.codigobarra,
		productos.fechaelaboracion,
		productos.fechaoptimo,
		productos.fechamedio,
		productos.fechaminimo,
		productos.codproveedor,
		productos.stockteorico,
		productos.motivoajuste,
		productos.codsucursal,
		sucursales.cuitsucursal,
		sucursales.razonsocial,
		familias.nomfamilia,
		subfamilias.nomsubfamilia,
		proveedores.cuitproveedor,
		proveedores.nomproveedor,
		marcas.nommarca,
		modelos.nommodelo,
		presentaciones.nompresentacion,
		colores.nomcolor,
		origenes.nomorigen
		FROM (productos INNER JOIN sucursales ON productos.codsucursal=sucursales.codsucursal)
    LEFT JOIN familias ON productos.codfamilia=familias.codfamilia
	LEFT JOIN subfamilias ON productos.codsubfamilia=subfamilias.codsubfamilia 
	LEFT JOIN marcas ON productos.codmarca=marcas.codmarca 
	LEFT JOIN modelos ON productos.codmodelo = modelos.codmodelo
	LEFT JOIN presentaciones ON productos.codpresentacion=presentaciones.codpresentacion 
	LEFT JOIN colores ON productos.codcolor=colores.codcolor 
	LEFT JOIN origenes ON productos.codorigen=origenes.codorigen 
	LEFT JOIN proveedores ON productos.codproveedor=proveedores.codproveedor
	WHERE productos.codsucursal = '".limpiar(decrypt($_GET["codsucursal"]))."' AND productos.existencia <= productos.stockmedio AND productos.existencia > productos.stockminimo";
	foreach ($this->dbh->query($sql) as $row)
	{
		$this->p[] = $row;
	}
	return $this->p;
	$this->dbh=null;
} 
else {

        $sql = "SELECT
		productos.idproducto,
		productos.codproducto,
		productos.producto,
		productos.fabricante,
		productos.codfamilia,
		productos.codsubfamilia,
		productos.codmarca,
		productos.codmodelo,
		productos.codpresentacion,
		productos.codcolor,
		productos.codorigen,
		productos.year,
		productos.nroparte,
		productos.lote,
		productos.peso,
		productos.preciocompra,
		productos.precioxmenor,
		productos.precioxmayor,
		productos.precioxpublico,
		productos.existencia,
		productos.stockoptimo,
		productos.stockmedio,
		productos.stockminimo,
		productos.ivaproducto,
		productos.descproducto,
		productos.codigobarra,
		productos.fechaelaboracion,
		productos.fechaoptimo,
		productos.fechamedio,
		productos.fechaminimo,
		productos.codproveedor,
		productos.stockteorico,
		productos.motivoajuste,
		productos.codsucursal,
		sucursales.cuitsucursal,
		sucursales.razonsocial,
		familias.nomfamilia,
		subfamilias.nomsubfamilia,
		proveedores.cuitproveedor,
		proveedores.nomproveedor,
		marcas.nommarca,
		modelos.nommodelo,
		presentaciones.nompresentacion,
		colores.nomcolor,
		origenes.nomorigen
		FROM (productos INNER JOIN sucursales ON productos.codsucursal=sucursales.codsucursal)
    LEFT JOIN familias ON productos.codfamilia=familias.codfamilia
	LEFT JOIN subfamilias ON productos.codsubfamilia=subfamilias.codsubfamilia 
	LEFT JOIN marcas ON productos.codmarca=marcas.codmarca 
	LEFT JOIN modelos ON productos.codmodelo = modelos.codmodelo
	LEFT JOIN presentaciones ON productos.codpresentacion=presentaciones.codpresentacion 
	LEFT JOIN colores ON productos.codcolor=colores.codcolor 
	LEFT JOIN origenes ON productos.codorigen=origenes.codorigen 
	LEFT JOIN proveedores ON productos.codproveedor=proveedores.codproveedor
	WHERE productos.codsucursal = '".limpiar($_SESSION["codsucursal"])."' AND productos.existencia <= productos.stockmedio AND productos.existencia > productos.stockminimo";
	foreach ($this->dbh->query($sql) as $row)
	{
		$this->p[] = $row;
	}
	return $this->p;
	$this->dbh=null;
  }
}
########################## FUNCION LISTAR PRODUCTOS EN STOCK MEDIO ################################

########################### FUNCION LISTAR PRODUCTOS EN STOCK MINIMO ################################
	public function ListarProductosMinimo()
	{
		self::SetNames();

	if ($_SESSION['acceso'] == "administradorG") {
		
        $sql = "SELECT
		productos.idproducto,
		productos.codproducto,
		productos.producto,
		productos.fabricante,
		productos.codfamilia,
		productos.codsubfamilia,
		productos.codmarca,
		productos.codmodelo,
		productos.codpresentacion,
		productos.codcolor,
		productos.codorigen,
		productos.year,
		productos.nroparte,
		productos.lote,
		productos.peso,
		productos.preciocompra,
		productos.precioxmenor,
		productos.precioxmayor,
		productos.precioxpublico,
		productos.existencia,
		productos.stockoptimo,
		productos.stockmedio,
		productos.stockminimo,
		productos.ivaproducto,
		productos.descproducto,
		productos.codigobarra,
		productos.fechaelaboracion,
		productos.fechaoptimo,
		productos.fechamedio,
		productos.fechaminimo,
		productos.codproveedor,
		productos.stockteorico,
		productos.motivoajuste,
		productos.codsucursal,
		sucursales.cuitsucursal,
		sucursales.razonsocial,
		familias.nomfamilia,
		subfamilias.nomsubfamilia,
		proveedores.cuitproveedor,
		proveedores.nomproveedor,
		marcas.nommarca,
		modelos.nommodelo,
		presentaciones.nompresentacion,
		colores.nomcolor,
		origenes.nomorigen
		FROM (productos INNER JOIN sucursales ON productos.codsucursal=sucursales.codsucursal)
    LEFT JOIN familias ON productos.codfamilia=familias.codfamilia
	LEFT JOIN subfamilias ON productos.codsubfamilia=subfamilias.codsubfamilia 
	LEFT JOIN marcas ON productos.codmarca=marcas.codmarca 
	LEFT JOIN modelos ON productos.codmodelo = modelos.codmodelo
	LEFT JOIN presentaciones ON productos.codpresentacion=presentaciones.codpresentacion 
	LEFT JOIN colores ON productos.codcolor=colores.codcolor 
	LEFT JOIN origenes ON productos.codorigen=origenes.codorigen 
	LEFT JOIN proveedores ON productos.codproveedor=proveedores.codproveedor
	WHERE productos.codsucursal = '".limpiar(decrypt($_GET["codsucursal"]))."' AND productos.existencia <= productos.stockminimo";
	foreach ($this->dbh->query($sql) as $row)
	{
		$this->p[] = $row;
	}
	return $this->p;
	$this->dbh=null;
} 
else {

        $sql = "SELECT
		productos.idproducto,
		productos.codproducto,
		productos.producto,
		productos.fabricante,
		productos.codfamilia,
		productos.codsubfamilia,
		productos.codmarca,
		productos.codmodelo,
		productos.codpresentacion,
		productos.codcolor,
		productos.codorigen,
		productos.year,
		productos.nroparte,
		productos.lote,
		productos.peso,
		productos.preciocompra,
		productos.precioxmenor,
		productos.precioxmayor,
		productos.precioxpublico,
		productos.existencia,
		productos.stockoptimo,
		productos.stockmedio,
		productos.stockminimo,
		productos.ivaproducto,
		productos.descproducto,
		productos.codigobarra,
		productos.fechaelaboracion,
		productos.fechaoptimo,
		productos.fechamedio,
		productos.fechaminimo,
		productos.codproveedor,
		productos.stockteorico,
		productos.motivoajuste,
		productos.codsucursal,
		sucursales.cuitsucursal,
		sucursales.razonsocial,
		familias.nomfamilia,
		subfamilias.nomsubfamilia,
		proveedores.cuitproveedor,
		proveedores.nomproveedor,
		marcas.nommarca,
		modelos.nommodelo,
		presentaciones.nompresentacion,
		colores.nomcolor,
		origenes.nomorigen
		FROM (productos INNER JOIN sucursales ON productos.codsucursal=sucursales.codsucursal)
    LEFT JOIN familias ON productos.codfamilia=familias.codfamilia
	LEFT JOIN subfamilias ON productos.codsubfamilia=subfamilias.codsubfamilia 
	LEFT JOIN marcas ON productos.codmarca=marcas.codmarca 
	LEFT JOIN modelos ON productos.codmodelo = modelos.codmodelo
	LEFT JOIN presentaciones ON productos.codpresentacion=presentaciones.codpresentacion 
	LEFT JOIN colores ON productos.codcolor=colores.codcolor 
	LEFT JOIN origenes ON productos.codorigen=origenes.codorigen 
	LEFT JOIN proveedores ON productos.codproveedor=proveedores.codproveedor
	WHERE productos.codsucursal = '".limpiar($_SESSION["codsucursal"])."' AND productos.existencia <= productos.stockminimo";
	foreach ($this->dbh->query($sql) as $row)
	{
		$this->p[] = $row;
	}
	return $this->p;
	$this->dbh=null;
  }
}
########################## FUNCION LISTAR PRODUCTOS EN STOCK MINIMO ################################

###################### FUNCION LISTAR PRECIOS POR CODIGO DE PRODUCTO #####################
public function BuscarPrecioxCodigo() 
	       {
		self::SetNames();
		$sql = "SELECT GROUP_CONCAT('P. MENOR', '_', precioxmenor, '|', 'P. MAYOR', '_', precioxmayor, '|', 'P.PUBLICO', '_', precioxpublico SEPARATOR '<br>') AS precioventa FROM productos WHERE codproducto = ? AND codsucursal = '".limpiar($_SESSION["codsucursal"])."'";
		$stmt = $this->dbh->prepare($sql);
		$stmt->execute(array($_GET["codproducto"]));
		$num = $stmt->rowCount();
		    if($num==0)
		{
			echo "<option value='' selected> -- SIN RESULTADOS -- </option>";
		}
		else
		{
			if($row = $stmt->fetch(PDO::FETCH_ASSOC))
			{
				$this->p[] = $row;
			}
			return $this->p;
			$this->dbh=null;
		}
}
##################### FUNCION LISTAR PRECIOS POR CODIGO PRODUCTO ######################

############################# FUNCION LISTAR PRODUCTOS EN VENTANA MODAL ################################
	public function ListarProductosModal()
	{
		self::SetNames();
$sql = "SELECT * FROM productos 
        LEFT JOIN familias ON productos.codfamilia=familias.codfamilia
        LEFT JOIN marcas ON productos.codmarca=marcas.codmarca 
        LEFT JOIN modelos ON productos.codmodelo = modelos.codmodelo 
        WHERE productos.codsucursal = '".limpiar($_SESSION["codsucursal"])."'";
	foreach ($this->dbh->query($sql) as $row)
	{
		$this->p[] = $row;
	}
	return $this->p;
	$this->dbh=null;
}
########################## FUNCION LISTAR PRODUCTOS EN VENTANA MODAL ################################

########################## FUNCION LISTAR CODIGO DE BARRAS #########################
	public function ListarCodigoBarra()
	{
		self::SetNames();

	if ($_SESSION['acceso'] == "administradorG") {

	$sql = "SELECT productos.codproducto, productos.codigobarra, sucursales.cuitsucursal, sucursales.razonsocial FROM productos INNER JOIN sucursales ON productos.codsucursal = sucursales.codsucursal WHERE productos.codsucursal = '".limpiar(decrypt($_GET["codsucursal"]))."'";
	foreach ($this->dbh->query($sql) as $row)
	{
		$this->p[] = $row;
	}
	return $this->p;
	$this->dbh=null;

     } else {

$sql = "SELECT codproducto, codigobarra FROM productos WHERE codsucursal = '".limpiar($_SESSION["codsucursal"])."'";
	foreach ($this->dbh->query($sql) as $row)
	{
		$this->p[] = $row;
	}
	return $this->p;
	$this->dbh=null;
   }
}
############################ FUNCION LISTAR CODIGO DE BARRAS #########################

############################ FUNCION ID PRODUCTOS #################################
	public function ProductosPorId()
	{
		self::SetNames();
		$sql = "SELECT
		productos.idproducto,
		productos.codproducto,
		productos.producto,
		productos.fabricante,
		productos.codfamilia,
		productos.codsubfamilia,
		productos.codmarca,
		productos.codmodelo,
		productos.codpresentacion,
		productos.codcolor,
		productos.codorigen,
		productos.year,
		productos.nroparte,
		productos.lote,
		productos.peso,
		productos.preciocompra,
		productos.precioxmenor,
		productos.precioxmayor,
		productos.precioxpublico,
		productos.existencia,
		productos.stockoptimo,
		productos.stockmedio,
		productos.stockminimo,
		productos.ivaproducto,
		productos.descproducto,
		productos.codigobarra,
		productos.fechaelaboracion,
		productos.fechaoptimo,
		productos.fechamedio,
		productos.fechaminimo,
		productos.codproveedor,
		productos.stockteorico,
		productos.motivoajuste,
		productos.codsucursal,
		sucursales.razonsocial,
		familias.nomfamilia,
		subfamilias.nomsubfamilia,
		proveedores.cuitproveedor,
		proveedores.nomproveedor,
		marcas.nommarca,
		modelos.nommodelo,
		presentaciones.nompresentacion,
		colores.nomcolor,
		origenes.nomorigen
	FROM(productos LEFT JOIN sucursales ON productos.codsucursal=sucursales.codsucursal)
    LEFT JOIN familias ON productos.codfamilia=familias.codfamilia 
	LEFT JOIN subfamilias ON productos.codsubfamilia=subfamilias.codsubfamilia 
	LEFT JOIN marcas ON productos.codmarca=marcas.codmarca 
	LEFT JOIN modelos ON productos.codmodelo = modelos.codmodelo
	LEFT JOIN presentaciones ON productos.codpresentacion=presentaciones.codpresentacion 
	LEFT JOIN colores ON productos.codcolor=colores.codcolor 
	LEFT JOIN origenes ON productos.codorigen=origenes.codorigen 
	LEFT JOIN proveedores ON productos.codproveedor=proveedores.codproveedor WHERE productos.codproducto = ? AND productos.codsucursal = ?";
		$stmt = $this->dbh->prepare($sql);
		$stmt->execute(array(decrypt($_GET["codproducto"]),decrypt($_GET["codsucursal"])));
		$num = $stmt->rowCount();
		if($num==0)
		{
			echo "";
		}
		else
		{
			if($row = $stmt->fetch(PDO::FETCH_ASSOC))
			{
				$this->p[] = $row;
			}
			return $this->p;
			$this->dbh=null;
		}
	}
############################ FUNCION ID PRODUCTOS #################################

############################ FUNCION ACTUALIZAR PRODUCTOS ############################
	public function ActualizarProductos()
	{
	self::SetNames();
		if(empty($_POST["codproducto"]) or empty($_POST["producto"]) or empty($_POST["codfamilia"]))
		{
			echo "1";
			exit;
		}
		$sql = "SELECT codproducto FROM productos WHERE idproducto != ? AND codproducto = ? AND codsucursal = ?";
		$stmt = $this->dbh->prepare($sql);
		$stmt->execute(array($_POST["idproducto"],$_POST["codproducto"],$_POST["codsucursal"]));
		$num = $stmt->rowCount();
		if($num == 0)
		{
			$sql = "UPDATE productos set"
			." producto = ?, "
			." fabricante = ?, "
			." codfamilia = ?, "
			." codsubfamilia = ?, "
			." codmarca = ?, "
			." codmodelo = ?, "
			." codpresentacion = ?, "
			." codcolor = ?, "
			." codorigen = ?, "
			." year = ?, "
			." nroparte = ?, "
			." lote = ?, "
			." peso = ?, "
			." preciocompra = ?, "
			." precioxmenor = ?, "
			." precioxmayor = ?, "
			." precioxpublico = ?, "
			." existencia = ?, "
			." stockoptimo = ?, "
			." stockmedio = ?, "
			." stockminimo = ?, "
			." ivaproducto = ?, "
			." descproducto = ?, "
			." codigobarra = ?, "
			." fechaelaboracion = ?, "
			." fechaoptimo = ?, "
			." fechamedio = ?, "
			." fechaminimo = ?, "
			." codproveedor = ? "
			." where "
			." idproducto = ?;
			";
			$stmt = $this->dbh->prepare($sql);
			$stmt->bindParam(1, $producto);
			$stmt->bindParam(2, $fabricante);
			$stmt->bindParam(3, $codfamilia);
			$stmt->bindParam(4, $codsubfamilia);
			$stmt->bindParam(5, $codmarca);
			$stmt->bindParam(6, $codmodelo);
			$stmt->bindParam(7, $codpresentacion);
			$stmt->bindParam(8, $codcolor);
			$stmt->bindParam(9, $codorigen);
			$stmt->bindParam(10, $year);
			$stmt->bindParam(11, $nroparte);
			$stmt->bindParam(12, $lote);
			$stmt->bindParam(13, $peso);
			$stmt->bindParam(14, $preciocompra);
			$stmt->bindParam(15, $precioxmenor);
			$stmt->bindParam(16, $precioxmayor);
			$stmt->bindParam(17, $precioxpublico);
			$stmt->bindParam(18, $existencia);
			$stmt->bindParam(19, $stockoptimo);
			$stmt->bindParam(20, $stockmedio);
			$stmt->bindParam(21, $stockminimo);
			$stmt->bindParam(22, $ivaproducto);
			$stmt->bindParam(23, $descproducto);
			$stmt->bindParam(24, $codigobarra);
			$stmt->bindParam(25, $fechaelaboracion);
			$stmt->bindParam(26, $fechaoptimo);
			$stmt->bindParam(27, $fechamedio);
			$stmt->bindParam(28, $fechaminimo);
			$stmt->bindParam(29, $codproveedor);
			$stmt->bindParam(30, $idproducto);

			$producto = limpiar($_POST["producto"]);
			$fabricante = limpiar($_POST["fabricante"]);
			$codfamilia = limpiar($_POST["codfamilia"]);
			$codsubfamilia = limpiar($_POST['codsubfamilia'] == '' ? "0" : $_POST['codsubfamilia']);
			$codmarca = limpiar($_POST["codmarca"]);
			$codmodelo = limpiar($_POST['codmodelo'] == '' ? "0" : $_POST['codmodelo']);
			$codpresentacion = limpiar($_POST['codpresentacion'] == '' ? "0" : $_POST['codpresentacion']);
			$codcolor = limpiar($_POST['codcolor'] == '' ? "0" : $_POST['codcolor']);
			$codorigen = limpiar($_POST['codorigen'] == '' ? "0" : $_POST['codorigen']);
			$year = limpiar($_POST["year"]);
			$nroparte = limpiar($_POST["nroparte"]);
			$lote = limpiar($_POST['lote'] == '' ? "0" : $_POST['lote']);
			$peso = limpiar($_POST["peso"]);
			$preciocompra = limpiar($_POST["preciocompra"]);
			$precioxmenor = limpiar($_POST["precioxmenor"]);
			$precioxmayor = limpiar($_POST["precioxmayor"]);
			$precioxpublico = limpiar($_POST["precioxpublico"]);
			$existencia = limpiar($_POST["existencia"]);
			$stockoptimo = limpiar($_POST["stockoptimo"]);
			$stockmedio = limpiar($_POST["stockmedio"]);
			$stockminimo = limpiar($_POST["stockminimo"]);
			$ivaproducto = limpiar($_POST["ivaproducto"]);
			$descproducto = limpiar($_POST["descproducto"]);
			$codigobarra = limpiar($_POST["codigobarra"]);
			$fechaelaboracion = limpiar($_POST['fechaelaboracion'] == '' ? "0000-00-00" : date("Y-m-d",strtotime($_POST['fechaelaboracion'])));
			$fechaoptimo = limpiar($_POST['fechaoptimo'] == '' ? "0000-00-00" : date("Y-m-d",strtotime($_POST['fechaoptimo'])));
			$fechamedio = limpiar($_POST['fechamedio'] == '' ? "0000-00-00" : date("Y-m-d",strtotime($_POST['fechamedio'])));
			$fechaminimo = limpiar($_POST['fechaminimo'] == '' ? "0000-00-00" : date("Y-m-d",strtotime($_POST['fechaminimo'])));
			$codproveedor = limpiar($_POST["codproveedor"]);
			$codproducto = limpiar($_POST["codproducto"]);
			$idproducto = limpiar($_POST["idproducto"]);
			$stmt->execute();

	##################  SUBIR FOTO DE PRODUCTO ######################################
         //datos del arhivo  
if (isset($_FILES['imagen']['name'])) { $nombre_archivo = $_FILES['imagen']['name']; } else { $nombre_archivo =''; }
if (isset($_FILES['imagen']['type'])) { $tipo_archivo = $_FILES['imagen']['type']; } else { $tipo_archivo =''; }
if (isset($_FILES['imagen']['size'])) { $tamano_archivo = $_FILES['imagen']['size']; } else { $tamano_archivo =''; } 
         //compruebo si las características del archivo son las que deseo  
if ((strpos($tipo_archivo,'image/jpeg')!==false)&&$tamano_archivo<200000) 
		 {  
if (move_uploaded_file($_FILES['imagen']['tmp_name'], "fotos/productos/".$nombre_archivo) && rename("fotos/productos/".$nombre_archivo,"fotos/productos/".$codproducto.".jpg"))
		 { 
		 ## se puede dar un aviso
		 } 
		 ## se puede dar otro aviso 
		 }
	################## FINALIZA SUBIR FOTO DE PRODUCTO ##########################
        
		echo "<span class='fa fa-check-square-o'></span> EL PRODUCTO HA SIDO ACTUALIZADO EXITOSAMENTE";
		exit;

		} else {

			echo "2";
			exit;
		}
	}
############################ FUNCION ACTUALIZAR PRODUCTOS ############################

########################## FUNCION AJUSTAR STOCK DE PRODUCTOS ###########################
	public function ActualizarAjuste()
	{
	self::SetNames();
		if(empty($_POST["codproducto"]) or empty($_POST["stockteorico"]) or empty($_POST["motivoajuste"]))
		{
			echo "1";
		    exit;
		}
		
		$sql = "UPDATE productos set"
			  ." stockteorico = ?, "
			  ." motivoajuste = ? "
			  ." where "
			  ." idproducto = ?;
			   ";
		$stmt = $this->dbh->prepare($sql);
		$stmt->bindParam(1, $stockteorico);
		$stmt->bindParam(2, $motivoajuste);
	    $stmt->bindParam(3, $idproducto);
		
		$stockteorico = limpiar($_POST["stockteorico"]);
		$motivoajuste = limpiar($_POST["motivoajuste"]);
		$idproducto = limpiar($_POST["idproducto"]);
		$stmt->execute();
	
		echo "<span class='fa fa-check-square-o'></span> EL AJUSTE DE STOCK DEL PRODUCTO SE HA REALIZADO EXITOSAMENTE";
		exit;
	}
###################### FUNCION AJUSTAR STOCK DE PRODUCTOS #########################

########################## FUNCION ELIMINAR PRODUCTOS ###########################
	public function EliminarProductos()
	{
	self::SetNames();
		if ($_SESSION["acceso"]=="administradorS") {

		$sql = "SELECT codproducto FROM detalleventas WHERE codproducto = ? AND codsucursal = ?";
		$stmt = $this->dbh->prepare($sql);
		$stmt->execute(array(decrypt($_GET["codproducto"]),decrypt($_GET["codsucursal"])));
		$num = $stmt->rowCount();
		if($num == 0)
		{

			$sql = "DELETE FROM productos WHERE codproducto = ? AND codsucursal = ?";
			$stmt = $this->dbh->prepare($sql);
			$stmt->bindParam(1,$codproducto);
			$stmt->bindParam(2,$codsucursal);
			$codproducto = decrypt($_GET["codproducto"]);
			$codsucursal = decrypt($_GET["codsucursal"]);
			$stmt->execute();

			$sql = "DELETE FROM kardex where codproducto = ? AND codsucursal = ?";
			$stmt = $this->dbh->prepare($sql);
			$stmt->bindParam(1,$codproducto);
			$stmt->bindParam(2,$codsucursal);
			$codproducto = decrypt($_GET["codproducto"]);
			$codsucursal = decrypt($_GET["codsucursal"]);
			$stmt->execute();

			$codproducto = decrypt($_GET["codproducto"]);
			if (file_exists("fotos/productos/".$codproducto.".jpg")){
		    //funcion para eliminar una carpeta con contenido
			$archivos = "fotos/productos/".$codproducto.".jpg";		
			unlink($archivos);
			}

			echo "1";
			exit;

		} else {
		   
			echo "2";
			exit;
		  } 
			
		} else {
		
		echo "3";
		exit;
	 }	
}
########################## FUNCION ELIMINAR PRODUCTOS #################################

###################### FUNCION BUSCAR PRODUCTOS POR SUCURSAL #########################
public function BuscarProductosxSucursal() 
	       {
		self::SetNames();
		$sql = "SELECT
		productos.idproducto,
		productos.codproducto,
		productos.producto,
		productos.fabricante,
		productos.codfamilia,
		productos.codsubfamilia,
		productos.codmarca,
		productos.codmodelo,
		productos.codpresentacion,
		productos.codcolor,
		productos.codorigen,
		productos.year,
		productos.nroparte,
		productos.lote,
		productos.peso,
		productos.preciocompra,
		productos.precioxmenor,
		productos.precioxmayor,
		productos.precioxpublico,
		productos.existencia,
		productos.stockoptimo,
		productos.stockmedio,
		productos.stockminimo,
		productos.ivaproducto,
		productos.descproducto,
		productos.codigobarra,
		productos.fechaelaboracion,
		productos.fechaoptimo,
		productos.fechamedio,
		productos.fechaminimo,
		productos.codproveedor,
		productos.stockteorico,
		productos.motivoajuste,
		productos.codsucursal,
		sucursales.cuitsucursal,
		sucursales.razonsocial,
		familias.nomfamilia,
		subfamilias.nomsubfamilia,
		proveedores.cuitproveedor,
		proveedores.nomproveedor,
		marcas.nommarca,
		modelos.nommodelo,
		presentaciones.nompresentacion,
		colores.nomcolor,
		origenes.nomorigen
		FROM (productos INNER JOIN sucursales ON productos.codsucursal=sucursales.codsucursal)
    LEFT JOIN familias ON productos.codfamilia=familias.codfamilia
	LEFT JOIN subfamilias ON productos.codsubfamilia=subfamilias.codsubfamilia 
	LEFT JOIN marcas ON productos.codmarca=marcas.codmarca 
	LEFT JOIN modelos ON productos.codmodelo = modelos.codmodelo
	LEFT JOIN presentaciones ON productos.codpresentacion=presentaciones.codpresentacion 
	LEFT JOIN colores ON productos.codcolor=colores.codcolor 
	LEFT JOIN origenes ON productos.codorigen=origenes.codorigen 
	LEFT JOIN proveedores ON productos.codproveedor=proveedores.codproveedor WHERE productos.codsucursal = ?";
        $stmt = $this->dbh->prepare($sql);
		$stmt->execute(array(decrypt($_GET["codsucursal"])));
		$num = $stmt->rowCount();
		if($num==0)
		{
		echo "<div class='alert alert-danger'>";
		echo "<button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>";
		echo "<center><span class='fa fa-info-circle'></span> NO SE ENCONTRARON PRODUCTOS EN LA SUCURSAL SELECCIONADA</center>";
		echo "</div>";		
		exit;
		}
		else
		{
			while($row = $stmt->fetch(PDO::FETCH_ASSOC))
			{
				$this->p[]=$row;
			}
			return $this->p;
			$this->dbh=null;
		}
	}
######################## FUNCION BUSCAR PRODUCTOS POR SUCURSAL #####################

###################### FUNCION BUSCAR PRODUCTOS VENDIDOS #########################
public function BuscarProductosVendidos() 
	{
		self::SetNames();
       $sql ="SELECT 
       productos.codproducto, 
       productos.producto, 
       productos.codmarca,  
       productos.existencia,
       detalleventas.codproducto,
       detalleventas.descproducto, 
       detalleventas.precioventa, 
       marcas.nommarca, 
       modelos.nommodelo, 
       ventas.fechaventa, 
       sucursales.cuitsucursal, 
       sucursales.razonsocial, 
       SUM(detalleventas.cantventa) as cantidad 
       FROM (ventas INNER JOIN detalleventas ON ventas.codventa=detalleventas.codventa) 
       INNER JOIN sucursales ON ventas.codsucursal=sucursales.codsucursal 
       INNER JOIN productos ON detalleventas.codproducto=productos.codproducto 
       LEFT JOIN marcas ON marcas.codmarca=productos.codmarca 
       LEFT JOIN modelos ON modelos.codmodelo=productos.codmodelo 
       WHERE ventas.codsucursal = '".decrypt($_GET['codsucursal'])."' 
       AND productos.codsucursal = '".decrypt($_GET['codsucursal'])."' 
       AND DATE_FORMAT(ventas.fechaventa,'%Y-%m-%d') >= ? 
       AND DATE_FORMAT(ventas.fechaventa,'%Y-%m-%d') <= ? 
       GROUP BY detalleventas.codproducto, detalleventas.precioventa, detalleventas.descproducto 
       ORDER BY productos.codproducto ASC";
		$stmt = $this->dbh->prepare($sql);
		//$stmt->bindValue(1, trim(decrypt($_GET['codsucursal'])));
		$stmt->bindValue(1, trim(date("Y-m-d",strtotime($_GET['desde']))));
		$stmt->bindValue(2, trim(date("Y-m-d",strtotime($_GET['hasta']))));
		$stmt->execute();
		$num = $stmt->rowCount();
		if($num==0)
		{
		echo "<div class='alert alert-danger'>";
		echo "<button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>";
		echo "<center><span class='fa fa-info-circle'></span> NO EXISTEN PRODUCTOS FACTURADOS PARA EL RANGO DE FECHA INGRESADA</center>";
		echo "</div>";		
		exit;
		}
		else
		{
			while($row = $stmt->fetch(PDO::FETCH_ASSOC))
			{
				$this->p[]=$row;
			}
			return $this->p;
			$this->dbh=null;
		}
	}
########################### FUNCION PRODUCTOS VENDIDOS ###############################

###################### FUNCION BUSCAR PRODUCTOS VENDIDOS POR VENDEDOR #########################
public function BuscarProductosxVendedor() 
	{
		self::SetNames();
       $sql ="SELECT 
       productos.codproducto, 
       productos.producto, 
       productos.codmarca,  
       productos.existencia,
       detalleventas.codproducto,
       detalleventas.descproducto, 
       detalleventas.precioventa, 
       marcas.nommarca, 
       modelos.nommodelo, 
       ventas.fechaventa, 
       sucursales.cuitsucursal, 
       sucursales.razonsocial,
       usuarios.dni,
       usuarios.nombres, 
       SUM(detalleventas.cantventa) as cantidad 
       FROM (ventas INNER JOIN detalleventas ON ventas.codventa=detalleventas.codventa) 
       INNER JOIN sucursales ON ventas.codsucursal=sucursales.codsucursal  
       INNER JOIN usuarios ON ventas.codigo = usuarios.codigo 
       INNER JOIN productos ON detalleventas.codproducto=productos.codproducto 
       LEFT JOIN marcas ON marcas.codmarca=productos.codmarca 
       LEFT JOIN modelos ON modelos.codmodelo=productos.codmodelo
       WHERE ventas.codsucursal = '".decrypt($_GET['codsucursal'])."' 
       AND productos.codsucursal = '".decrypt($_GET['codsucursal'])."'
       AND ventas.codigo = ? 
       AND DATE_FORMAT(ventas.fechaventa,'%Y-%m-%d') >= ? 
       AND DATE_FORMAT(ventas.fechaventa,'%Y-%m-%d') <= ? 
       GROUP BY detalleventas.codproducto, detalleventas.precioventa, detalleventas.descproducto 
       ORDER BY productos.codproducto ASC";
		$stmt = $this->dbh->prepare($sql);
		$stmt->bindValue(1, trim($_GET['codigo']));
		$stmt->bindValue(2, trim(date("Y-m-d",strtotime($_GET['desde']))));
		$stmt->bindValue(3, trim(date("Y-m-d",strtotime($_GET['hasta']))));
		$stmt->execute();
		$num = $stmt->rowCount();
		if($num==0)
		{
		echo "<div class='alert alert-danger'>";
		echo "<button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>";
		echo "<center><span class='fa fa-info-circle'></span> NO EXISTEN PRODUCTOS FACTURADOS PARA EL VENDEDOR Y RANGO DE FECHA INGRESADA</center>";
		echo "</div>";		
		exit;
		}
		else
		{
			while($row = $stmt->fetch(PDO::FETCH_ASSOC))
			{
				$this->p[]=$row;
			}
			return $this->p;
			$this->dbh=null;
		}
	}
########################### FUNCION PRODUCTOS VENDIDOS POR VENDEDOR ###############################

######################## FUNCION DETALLE PRODUCTO KARDEX #########################
	public function DetalleProductosKardex()
	{
		self::SetNames();
		$sql = "SELECT
		productos.idproducto,
		productos.codproducto,
		productos.producto,
		productos.fabricante,
		productos.codfamilia,
		productos.codsubfamilia,
		productos.codmarca,
		productos.codmodelo,
		productos.codpresentacion,
		productos.codcolor,
		productos.codorigen,
		productos.year,
		productos.nroparte,
		productos.lote,
		productos.peso,
		productos.preciocompra,
		productos.precioxmenor,
		productos.precioxmayor,
		productos.precioxpublico,
		productos.existencia,
		productos.stockoptimo,
		productos.stockmedio,
		productos.stockminimo,
		productos.ivaproducto,
		productos.descproducto,
		productos.codigobarra,
		productos.fechaelaboracion,
		productos.fechaoptimo,
		productos.fechamedio,
		productos.fechaminimo,
		productos.codproveedor,
		productos.stockteorico,
		productos.motivoajuste,
		productos.codsucursal,
		sucursales.cuitsucursal,
		sucursales.razonsocial,
		familias.nomfamilia,
		subfamilias.nomsubfamilia,
		proveedores.cuitproveedor,
		proveedores.nomproveedor,
		marcas.nommarca,
		modelos.nommodelo,
		presentaciones.nompresentacion,
		colores.nomcolor,
		origenes.nomorigen
	FROM(productos LEFT JOIN sucursales ON productos.codsucursal=sucursales.codsucursal)
    LEFT JOIN familias ON productos.codfamilia=familias.codfamilia 
	LEFT JOIN subfamilias ON productos.codsubfamilia=subfamilias.codsubfamilia 
	LEFT JOIN marcas ON productos.codmarca=marcas.codmarca 
	LEFT JOIN modelos ON productos.codmodelo = modelos.codmodelo
	LEFT JOIN presentaciones ON productos.codpresentacion=presentaciones.codpresentacion 
	LEFT JOIN colores ON productos.codcolor=colores.codcolor 
	LEFT JOIN origenes ON productos.codorigen=origenes.codorigen 
	LEFT JOIN proveedores ON productos.codproveedor=proveedores.codproveedor WHERE productos.codproducto = ? AND productos.codsucursal = ?";
		$stmt = $this->dbh->prepare($sql);
		$stmt->execute(array($_GET["codproducto"],decrypt($_GET["codsucursal"])));
		$num = $stmt->rowCount();
		if($num==0)
		{
			echo "";
		}
		else
		{
			if($row = $stmt->fetch(PDO::FETCH_ASSOC))
			{
				$this->p[] = $row;
			}
			return $this->p;
			$this->dbh=null;
		}
	}
######################## FUNCION DETALLE PRODUCTO KARDEX #########################

######################## FUNCION BUSCA KARDEX PRODUCTOS ##########################
public function BuscarKardexProducto() 
	       {
		self::SetNames();
		$sql ="SELECT * FROM kardex WHERE codproducto = ? AND codsucursal = ?";
        $stmt = $this->dbh->prepare($sql);
		$stmt->execute(array($_GET["codproducto"], decrypt($_GET["codsucursal"])));
		$num = $stmt->rowCount();
		if($num==0)
		{
		echo "<div class='alert alert-danger'>";
		echo "<button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>";
		echo "<center><span class='fa fa-info-circle'></span> NO EXISTEN MOVIMIENTOS EN KARDEX PARA EL PRODUCTO INGRESADO</center>";
		echo "</div>";		
		exit;
		}
		else
		{
			while($row = $stmt->fetch(PDO::FETCH_ASSOC))
			{
				$this->p[]=$row;
			}
			return $this->p;
			$this->dbh=null;
		}
	}
######################## FUNCION BUSCA KARDEX PRODUCTOS #########################

########################### FUNCION LISTAR KARDEX VALORIZADO ################################
	public function ListarKardexValorizado()
	{
		self::SetNames();
        

	if ($_SESSION['acceso'] == "administradorG") {

	$sql = "SELECT
 	productos.idproducto,
 	productos.codproducto,
 	productos.producto,
 	productos.fabricante,
 	productos.codfamilia,
 	productos.codsubfamilia,
 	productos.codmarca,
 	productos.codmodelo,
 	productos.codpresentacion,
 	productos.codcolor,
 	productos.codorigen,
 	productos.year,
 	productos.nroparte,
 	productos.lote,
 	productos.peso,
 	productos.preciocompra,
 	productos.precioxmenor,
 	productos.precioxmayor,
 	productos.precioxpublico,
 	productos.existencia,
 	productos.stockoptimo,
 	productos.stockmedio,
 	productos.stockminimo,
 	productos.ivaproducto,
 	productos.descproducto,
 	productos.codigobarra,
 	productos.fechaelaboracion,
 	productos.fechaoptimo,
 	productos.fechamedio,
 	productos.fechaminimo,
 	productos.codproveedor,
 	productos.stockteorico,
 	productos.motivoajuste,
 	productos.codsucursal,
 	sucursales.cuitsucursal,
 	sucursales.razonsocial,
 	familias.nomfamilia,
 	subfamilias.nomsubfamilia,
 	proveedores.cuitproveedor,
 	proveedores.nomproveedor,
 	marcas.nommarca,
 	modelos.nommodelo,
 	presentaciones.nompresentacion,
 	colores.nomcolor,
 	origenes.nomorigen
		FROM (productos INNER JOIN sucursales ON productos.codsucursal=sucursales.codsucursal)
    LEFT JOIN familias ON productos.codfamilia=familias.codfamilia
	LEFT JOIN subfamilias ON productos.codsubfamilia=subfamilias.codsubfamilia 
	LEFT JOIN marcas ON productos.codmarca=marcas.codmarca 
	LEFT JOIN modelos ON productos.codmodelo = modelos.codmodelo
	LEFT JOIN presentaciones ON productos.codpresentacion=presentaciones.codpresentacion 
	LEFT JOIN colores ON productos.codcolor=colores.codcolor 
	LEFT JOIN origenes ON productos.codorigen=origenes.codorigen 
	LEFT JOIN proveedores ON productos.codproveedor=proveedores.codproveedor
	WHERE productos.codsucursal = '".limpiar(decrypt($_GET["codsucursal"]))."'";
	foreach ($this->dbh->query($sql) as $row)
	{
		$this->p[] = $row;
	}
	return $this->p;
	$this->dbh=null;

     } else {

 	$sql = "SELECT
 	productos.idproducto,
 	productos.codproducto,
 	productos.producto,
 	productos.fabricante,
 	productos.codfamilia,
 	productos.codsubfamilia,
 	productos.codmarca,
 	productos.codmodelo,
 	productos.codpresentacion,
 	productos.codcolor,
 	productos.codorigen,
 	productos.year,
 	productos.nroparte,
 	productos.lote,
 	productos.peso,
 	productos.preciocompra,
 	productos.precioxmenor,
 	productos.precioxmayor,
 	productos.precioxpublico,
 	productos.existencia,
 	productos.stockoptimo,
 	productos.stockmedio,
 	productos.stockminimo,
 	productos.ivaproducto,
 	productos.descproducto,
 	productos.codigobarra,
 	productos.fechaelaboracion,
 	productos.fechaoptimo,
 	productos.fechamedio,
 	productos.fechaminimo,
 	productos.codproveedor,
 	productos.stockteorico,
 	productos.motivoajuste,
 	productos.codsucursal,
 	sucursales.cuitsucursal,
 	sucursales.razonsocial,
 	familias.nomfamilia,
 	subfamilias.nomsubfamilia,
 	proveedores.cuitproveedor,
 	proveedores.nomproveedor,
 	marcas.nommarca,
 	modelos.nommodelo,
 	presentaciones.nompresentacion,
 	colores.nomcolor,
 	origenes.nomorigen
		FROM (productos INNER JOIN sucursales ON productos.codsucursal=sucursales.codsucursal)
    LEFT JOIN familias ON productos.codfamilia=familias.codfamilia
	LEFT JOIN subfamilias ON productos.codsubfamilia=subfamilias.codsubfamilia 
	LEFT JOIN marcas ON productos.codmarca=marcas.codmarca 
	LEFT JOIN modelos ON productos.codmodelo = modelos.codmodelo
	LEFT JOIN presentaciones ON productos.codpresentacion=presentaciones.codpresentacion 
	LEFT JOIN colores ON productos.codcolor=colores.codcolor 
	LEFT JOIN origenes ON productos.codorigen=origenes.codorigen 
	LEFT JOIN proveedores ON productos.codproveedor=proveedores.codproveedor
	WHERE productos.codsucursal = '".limpiar($_SESSION["codsucursal"])."'";
	foreach ($this->dbh->query($sql) as $row)
	{
		$this->p[] = $row;
	}
	return $this->p;
	$this->dbh=null;
   }
}
########################## FUNCION LISTAR KARDEX VALORIZADO ################################

###################### FUNCION KARDEX POR FECHAS Y VENDEDOR #########################
public function BuscarKardexValorizadoxFechas() 
	{
		self::SetNames();
       $sql ="SELECT 
       productos.codproducto, 
       productos.producto, 
       productos.codmarca,  
       productos.existencia,
       productos.preciocompra,
       detalleventas.codproducto,
       detalleventas.descproducto, 
       detalleventas.precioventa, 
       marcas.nommarca, 
       modelos.nommodelo, 
       ventas.fechaventa, 
       sucursales.cuitsucursal, 
       sucursales.razonsocial,
       usuarios.dni,
       usuarios.nombres, 
       SUM(detalleventas.cantventa) as cantidad 
       FROM (ventas INNER JOIN detalleventas ON ventas.codventa=detalleventas.codventa) 
       INNER JOIN sucursales ON ventas.codsucursal=sucursales.codsucursal  
       INNER JOIN usuarios ON ventas.codigo = usuarios.codigo 
       INNER JOIN productos ON detalleventas.codproducto=productos.codproducto 
       LEFT JOIN marcas ON marcas.codmarca=productos.codmarca 
       LEFT JOIN modelos ON modelos.codmodelo=productos.codmodelo
       WHERE ventas.codsucursal = '".decrypt($_GET['codsucursal'])."' 
       AND productos.codsucursal = '".decrypt($_GET['codsucursal'])."'
       AND ventas.codigo = ? 
       AND DATE_FORMAT(ventas.fechaventa,'%Y-%m-%d') >= ? 
       AND DATE_FORMAT(ventas.fechaventa,'%Y-%m-%d') <= ? 
       GROUP BY detalleventas.codproducto, detalleventas.precioventa, detalleventas.descproducto 
       ORDER BY productos.codproducto ASC";
		$stmt = $this->dbh->prepare($sql);
		$stmt->bindValue(1, trim($_GET['codigo']));
		$stmt->bindValue(2, trim(date("Y-m-d",strtotime($_GET['desde']))));
		$stmt->bindValue(3, trim(date("Y-m-d",strtotime($_GET['hasta']))));
		$stmt->execute();
		$num = $stmt->rowCount();
		if($num==0)
		{
		echo "<div class='alert alert-danger'>";
		echo "<button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>";
		echo "<center><span class='fa fa-info-circle'></span> NO EXISTEN PRODUCTOS FACTURADOS PARA EL VENDEDOR Y RANGO DE FECHA INGRESADA</center>";
		echo "</div>";		
		exit;
		}
		else
		{
			while($row = $stmt->fetch(PDO::FETCH_ASSOC))
			{
				$this->p[]=$row;
			}
			return $this->p;
			$this->dbh=null;
		}
	}
########################### FUNCION KARDEX POR FECHAS Y VENDEDOR ###############################

############################### FIN DE CLASE PRODUCTOS ###############################
































################################## CLASE TRASPASOS ###################################

############################## FUNCION REGISTRAR TRASPASOS ############################
	public function RegistrarTraspasos()
	{
		self::SetNames();
		if(empty($_POST["envia"]) or empty($_POST["recibe"]) or empty($_POST["codsucursal"]) or empty($_POST["fechatraspaso"]))
		{
			echo "1";
			exit;
		}

		else if(limpiar($_POST["envia"]) == limpiar($_POST["recibe"]))
		{
			echo "2";
			exit;
			
		} else if(empty($_SESSION["CarritoTraspaso"]) || $_POST["txtTotal"]=="0.00")
		{
			echo "3";
			exit;
			
		} else {

		$v = $_SESSION["CarritoTraspaso"];
		for($i=0;$i<count($v);$i++){

		    $sql = "SELECT existencia FROM productos WHERE codproducto = '".$v[$i]['txtCodigo']."' AND codsucursal = '".limpiar($_POST["codsucursal"])."'";
		    foreach ($this->dbh->query($sql) as $row)
		    {
			$this->p[] = $row;
		    }
		
		    $existenciadb = $row['existencia'];
		    $cantidad = $v[$i]['cantidad'];

            if ($cantidad > $existenciadb) 
            { 
		       echo "4";
		       exit;
	        }
		}

	     ################### CREO LOS CODIGO DE TRASPASO ####################
		$sql = " SELECT codsucursal, nroactividadsucursal, iniciofactura FROM sucursales WHERE codsucursal = '".limpiar($_POST["codsucursal"])."'";
		foreach ($this->dbh->query($sql) as $row)
		{
			$this->p[] = $row;
		}
		$nroactividad = $row['nroactividadsucursal'];
		$iniciofactura = $row['iniciofactura'];
		
		$sql4 = "SELECT codtraspaso FROM traspasos WHERE codsucursal = '".limpiar($_POST["codsucursal"])."' ORDER BY idtraspaso DESC LIMIT 1";
		 foreach ($this->dbh->query($sql4) as $row4){

			$traspaso=$row4["codtraspaso"];

		}
		if(empty($traspaso))
		{
			$codtraspaso = $nroactividad.'-'.$iniciofactura;

		} else {

			$var = strlen($nroactividad."-");
            $var1 = substr($traspaso , $var);
            $var2 = strlen($var1);
            $var3 = $var1 + 1;
            $var4 = str_pad($var3, $var2, "0", STR_PAD_LEFT);
            $codtraspaso = $nroactividad.'-'.$var4;
		}
        ################### CREO LOS CODIGO DE TRASPASO ####################


        $query = "INSERT INTO traspasos values (null, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);";
		$stmt = $this->dbh->prepare($query);
		$stmt->bindParam(1, $codtraspaso);
		$stmt->bindParam(2, $envia);
		$stmt->bindParam(3, $recibe);
		$stmt->bindParam(4, $subtotalivasi);
		$stmt->bindParam(5, $subtotalivano);
		$stmt->bindParam(6, $iva);
		$stmt->bindParam(7, $totaliva);
		$stmt->bindParam(8, $descuento);
		$stmt->bindParam(9, $totaldescuento);
		$stmt->bindParam(10, $totalpago);
		$stmt->bindParam(11, $totalpago2);
		$stmt->bindParam(12, $fechatraspaso);
		$stmt->bindParam(13, $observaciones);
		$stmt->bindParam(14, $codigo);
		$stmt->bindParam(15, $codsucursal);
	    
		$envia = limpiar($_POST["envia"]);
		$recibe = limpiar($_POST["recibe"]);
		$subtotalivasi = limpiar($_POST["txtsubtotal"]);
		$subtotalivano = limpiar($_POST["txtsubtotal2"]);
		$iva = limpiar($_POST["iva"]);
		$totaliva = limpiar($_POST["txtIva"]);
		$descuento = limpiar($_POST["descuento"]);
		$totaldescuento = limpiar($_POST["txtDescuento"]);
		$totalpago = limpiar($_POST["txtTotal"]);
		$totalpago2 = limpiar($_POST["txtTotalCompra"]);
		$fechatraspaso = limpiar(date("Y-m-d",strtotime($_POST['fechatraspaso']))." ".date("h:i:s"));
		$observaciones = limpiar($_POST["observaciones"]);
		$codigo = limpiar($_SESSION["codigo"]);
		$codsucursal = limpiar($_POST["codsucursal"]);
		$stmt->execute();
		
		$this->dbh->beginTransaction();
		$detalle = $_SESSION["CarritoTraspaso"];
		for($i=0;$i<count($detalle);$i++){

		################ VERIFICO LA EXISTENCIA DEL PRODUCTO EN ALMACEN ################
		$sql = "SELECT * FROM productos WHERE codproducto = '".limpiar($detalle[$i]['txtCodigo'])."' AND codsucursal = '".limpiar($_POST['codsucursal'])."'";
		foreach ($this->dbh->query($sql) as $row)
		{
			$this->p[] = $row;
		}
		$existenciabd = $row['existencia'];

		$query = "INSERT INTO detallestraspasos values (null, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?); ";
		$stmt = $this->dbh->prepare($query);
		$stmt->bindParam(1, $codtraspaso);
	    $stmt->bindParam(2, $codproducto);
	    $stmt->bindParam(3, $producto);
		$stmt->bindParam(4, $fechaexpiracion);
		$stmt->bindParam(5, $cantidad);
		$stmt->bindParam(6, $preciocompra);
		$stmt->bindParam(7, $precioventa);
		$stmt->bindParam(8, $ivaproducto);
		$stmt->bindParam(9, $descproducto);
		$stmt->bindParam(10, $valortotal);
		$stmt->bindParam(11, $totaldescuentov);
		$stmt->bindParam(12, $valorneto);
		$stmt->bindParam(13, $valorneto2);
		$stmt->bindParam(14, $codsucursal);
			
		$codproducto = limpiar($detalle[$i]['txtCodigo']);
		$producto = limpiar($detalle[$i]['producto']);
if (limpiar($detalle[$i]['fechaexpiracion']=="")) { $fechaexpiracion = "0000-00-00";  } else { $fechaexpiracion = limpiar(date("Y-m-d",strtotime($detalle[$i]['fechaexpiracion']))); }
		$cantidad = limpiar($detalle[$i]['cantidad']);
		$preciocompra = limpiar($detalle[$i]['precio']);
		$precioventa = limpiar($detalle[$i]['precio2']);
		$ivaproducto = limpiar($detalle[$i]['ivaproducto']);
		$descproducto = limpiar($detalle[$i]['descproducto']);
		$descuento = $detalle[$i]['descproducto']/100;
		$valortotal = number_format($detalle[$i]['precio2']*$detalle[$i]['cantidad'], 2, '.', '');
		$totaldescuentov = number_format($valortotal*$descuento, 2, '.', '');
	    $valorneto = number_format($valortotal-$totaldescuentov, 2, '.', '');
		$valorneto2 = number_format($detalle[$i]['precio']*$detalle[$i]['cantidad'], 2, '.', '');
		$codsucursal = limpiar($_POST["codsucursal"]);
		$stmt->execute();

	    ##################### ACTUALIZO LA EXISTENCIA DEL ALMACEN ####################
		$sql = " UPDATE productos set "
			  ." existencia = ? "
			  ." where "
			  ." codproducto = '".limpiar($detalle[$i]['txtCodigo'])."' and codsucursal = '".limpiar($_POST["codsucursal"])."';
			   ";
		$stmt = $this->dbh->prepare($sql);
		$stmt->bindParam(1, $existencia);
		$cantraspaso = limpiar($detalle[$i]['cantidad']);
		$existencia = $existenciabd-$cantraspaso;
		$stmt->execute();

		################ REGISTRAMOS LOS PRODUCTOS SALIENTES EN KARDEX #################
        $query = "INSERT INTO kardex values (null, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?); ";
		$stmt = $this->dbh->prepare($query);
		$stmt->bindParam(1, $codtraspaso);
		$stmt->bindParam(2, $recibe);
		$stmt->bindParam(3, $codproducto);
		$stmt->bindParam(4, $movimiento);
		$stmt->bindParam(5, $entradas);
		$stmt->bindParam(6, $salidas);
		$stmt->bindParam(7, $devolucion);
		$stmt->bindParam(8, $stockactual);
		$stmt->bindParam(9, $ivaproducto);
		$stmt->bindParam(10, $descproducto);
		$stmt->bindParam(11, $precio);
		$stmt->bindParam(12, $documento);
		$stmt->bindParam(13, $fechakardex);		
		$stmt->bindParam(14, $codsucursal);

		$recibe = limpiar($_POST["recibe"]);
		$codproducto = limpiar($detalle[$i]['txtCodigo']);
		$movimiento = limpiar("SALIDAS");
		$entradas = limpiar("0");
		$salidas= limpiar($detalle[$i]['cantidad']);
		$devolucion = limpiar("0");
		$stockactual = limpiar($existenciabd-$detalle[$i]['cantidad']);
		$precio = limpiar($detalle[$i]["precio2"]);
		$ivaproducto = limpiar($detalle[$i]['ivaproducto']);
		$descproducto = limpiar($detalle[$i]['descproducto']);
		$documento = limpiar("TRASPASO: ".$codtraspaso);
		$fechakardex = limpiar(date("Y-m-d"));
		$codsucursal = limpiar($_POST["codsucursal"]);
		$stmt->execute();


	############ VERIFICO SI EL PRODUCTO YA EXISTE EN LA SUCURSAL QUE RECIBE ###########
	$sql = "SELECT codproducto FROM productos WHERE codproducto = ? AND codsucursal = ?";
	$stmt = $this->dbh->prepare($sql);
	$stmt->execute(array(limpiar($detalle[$i]['txtCodigo']),limpiar($_POST['recibe'])));
	$num = $stmt->rowCount();
	if($num == 0)
	{

		$query = "INSERT INTO productos values (null, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);";
		$stmt = $this->dbh->prepare($query);
		$stmt->bindParam(1, $codproducto);
		$stmt->bindParam(2, $producto);
		$stmt->bindParam(3, $fabricante);
		$stmt->bindParam(4, $codfamilia);
		$stmt->bindParam(5, $codsubfamilia);
		$stmt->bindParam(6, $codmarca);
		$stmt->bindParam(7, $codmodelo);
		$stmt->bindParam(8, $codpresentacion);
		$stmt->bindParam(9, $codcolor);
		$stmt->bindParam(10, $codorigen);
		$stmt->bindParam(11, $year);
		$stmt->bindParam(12, $nroparte);
		$stmt->bindParam(13, $lote);
		$stmt->bindParam(14, $peso);
		$stmt->bindParam(15, $preciocompra);
		$stmt->bindParam(16, $precioxmenor);
		$stmt->bindParam(17, $precioxmayor);
		$stmt->bindParam(18, $precioxpublico);
		$stmt->bindParam(19, $existencia);
		$stmt->bindParam(20, $stockoptimo);
		$stmt->bindParam(21, $stockmedio);
		$stmt->bindParam(22, $stockminimo);
		$stmt->bindParam(23, $ivaproducto);
		$stmt->bindParam(24, $descproducto);
		$stmt->bindParam(25, $codigobarra);
		$stmt->bindParam(26, $fechaelaboracion);
		$stmt->bindParam(27, $fechaoptimo);
		$stmt->bindParam(28, $fechamedio);
		$stmt->bindParam(29, $fechaminimo);
		$stmt->bindParam(30, $codproveedor);
		$stmt->bindParam(31, $stockteorico);
		$stmt->bindParam(32, $motivoajuste);
		$stmt->bindParam(33, $recibe);

		$codproducto = limpiar($detalle[$i]["txtCodigo"]);
		$producto = limpiar($row["producto"]);
		$fabricante = limpiar($row["fabricante"]);
		$codfamilia = limpiar($row["codfamilia"]);
		$codsubfamilia = limpiar($row["codsubfamilia"]);
		$codmarca = limpiar($row["codmarca"]);
		$codmodelo = limpiar($row["codmodelo"]);
		$codpresentacion = limpiar($row["codpresentacion"]);
		$codcolor = limpiar($row["codcolor"]);
		$codorigen = limpiar($row["codorigen"]);
		$year = limpiar($row["year"]);
		$nroparte = limpiar($row["nroparte"]);
		$lote = limpiar($row["lote"]);
		$peso = limpiar($row["peso"]);
		$preciocompra = limpiar($detalle[$i]["precio"]);
		$precioxmenor = limpiar($row["precioxmenor"]);
		$precioxmayor = limpiar($row["precioxmayor"]);
		$precioxpublico = limpiar($row["precioxpublico"]);
		$existencia = limpiar($detalle[$i]["cantidad"]);
		$stockoptimo = limpiar($row["stockoptimo"]);
		$stockmedio = limpiar($row["stockmedio"]);
		$stockminimo = limpiar($row["stockminimo"]);
		$ivaproducto = limpiar($detalle[$i]['ivaproducto']);
		$descproducto = limpiar($detalle[$i]['descproducto']);
		$codigobarra = limpiar($row["codigobarra"]);
		$fechaelaboracion = limpiar($row['fechaelaboracion']);
		$fechaoptimo = limpiar($row['fechaoptimo']);
		$fechamedio = limpiar($row['fechamedio']);
		$fechaminimo = limpiar($row['fechaminimo']);
		$codproveedor = limpiar($row["codproveedor"]);
		$stockteorico = limpiar("0");
		$motivoajuste = limpiar("NINGUNO");
		$recibe = limpiar($_POST["recibe"]);
		$stmt->execute();

		################# REGISTRAMOS KARDEX DE PRODUCTO QUE RECIBE ###################
        $query = "INSERT INTO kardex values (null, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?); ";
		$stmt = $this->dbh->prepare($query);
		$stmt->bindParam(1, $codtraspaso);
		$stmt->bindParam(2, $envia);
		$stmt->bindParam(3, $codproducto);
		$stmt->bindParam(4, $movimiento);
		$stmt->bindParam(5, $entradas);
		$stmt->bindParam(6, $salidas);
		$stmt->bindParam(7, $devolucion);
		$stmt->bindParam(8, $stockactual);
		$stmt->bindParam(9, $ivaproducto);
		$stmt->bindParam(10, $descproducto);
		$stmt->bindParam(11, $precio);
		$stmt->bindParam(12, $documento);
		$stmt->bindParam(13, $fechakardex);		
		$stmt->bindParam(14, $recibe);

		$envia = limpiar("0");
		$codproducto = limpiar($detalle[$i]['txtCodigo']);
		$movimiento = limpiar("ENTRADAS");
		$entradas= limpiar($detalle[$i]['cantidad']);
		$salidas = limpiar("0");
		$devolucion = limpiar("0");
		$stockactual = limpiar($detalle[$i]['cantidad']);
		$precio = limpiar($detalle[$i]["precio"]);
		$ivaproducto = limpiar($detalle[$i]['ivaproducto']);
		$descproducto = limpiar($detalle[$i]['descproducto']);
		$documento = limpiar("TRASPASO ".$codtraspaso);
		$fechakardex = limpiar(date("Y-m-d"));
		$recibe = limpiar($_POST["recibe"]);
		$stmt->execute();

	} else {

		################ VERIFICO LA EXISTENCIA DEL PRODUCTO EN ALMACEN ################
		$sql = "SELECT * FROM productos WHERE codproducto = '".limpiar($detalle[$i]['txtCodigo'])."' AND codsucursal = '".limpiar($_POST['recibe'])."'";
		foreach ($this->dbh->query($sql) as $row)
		{
			$this->p[] = $row;
		}
		$existenciarecibebd = $row['existencia'];

		############# ACTUALIZAMOS LA EXISTENCIA DE PRODUCTOS RECIBIDOS ###############
		$sql = "UPDATE productos set "
		      ." preciocompra = ?, "
			  ." precioxmenor = ?, "
			  ." precioxmayor = ?, "
			  ." precioxpublico = ?, "
			  ." existencia = ?, "
			  ." ivaproducto = ?, "
			  ." descproducto = ?, "
			  ." fechaoptimo = ?, "
			  ." fechamedio = ?, "
			  ." fechaminimo = ? "
			  ." WHERE "
			  ." codproducto = ? AND codsucursal = ?;
			   ";
		$stmt = $this->dbh->prepare($sql);
		$stmt->bindParam(1, $preciocompra);
		$stmt->bindParam(2, $precioxmenor);
		$stmt->bindParam(3, $precioxmayor);
		$stmt->bindParam(4, $precioxpublico);
		$stmt->bindParam(5, $existencia);
		$stmt->bindParam(6, $ivaproducto);
		$stmt->bindParam(7, $descproducto);
		$stmt->bindParam(8, $fechaoptimo);
		$stmt->bindParam(9, $fechamedio);
		$stmt->bindParam(10, $fechaminimo);
		$stmt->bindParam(11, $codproducto);
		$stmt->bindParam(12, $recibe);
		
		$preciocompra = limpiar($detalle[$i]['precio']);
		$precioxmenor = limpiar($row['precioxmenor']);
		$precioxmayor = limpiar($row['precioxmayor']);
		$precioxpublico = limpiar($row['precioxpublico']);
		$existencia = limpiar($existenciarecibebd+$detalle[$i]['cantidad']);
		$ivaproducto = limpiar($detalle[$i]['ivaproducto']);
		$descproducto = limpiar($detalle[$i]['descproducto']);
		$fechaoptimo = limpiar($row['fechaoptimo']);
		$fechamedio = limpiar($row['fechamedio']);
		$fechaminimo = limpiar($row['fechaminimo']);
		$codproducto = limpiar($detalle[$i]['txtCodigo']);
		$recibe = limpiar($_POST['recibe']);
		$stmt->execute();

		################# REGISTRAMOS KARDEX DE PRODUCTO QUE RECIBE ###################
        $query = "INSERT INTO kardex values (null, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?); ";
		$stmt = $this->dbh->prepare($query);
		$stmt->bindParam(1, $codtraspaso);
		$stmt->bindParam(2, $envia);
		$stmt->bindParam(3, $codproducto);
		$stmt->bindParam(4, $movimiento);
		$stmt->bindParam(5, $entradas);
		$stmt->bindParam(6, $salidas);
		$stmt->bindParam(7, $devolucion);
		$stmt->bindParam(8, $stockactual);
		$stmt->bindParam(9, $ivaproducto);
		$stmt->bindParam(10, $descproducto);
		$stmt->bindParam(11, $precio);
		$stmt->bindParam(12, $documento);
		$stmt->bindParam(13, $fechakardex);		
		$stmt->bindParam(14, $recibe);

		$envia = limpiar("0");
		$codproducto = limpiar($detalle[$i]['txtCodigo']);
		$movimiento = limpiar("ENTRADAS");
		$entradas= limpiar($detalle[$i]['cantidad']);
		$salidas = limpiar("0");
		$devolucion = limpiar("0");
		$stockactual = limpiar($existenciarecibebd+$detalle[$i]['cantidad']);
		$precio = limpiar($detalle[$i]["precio"]);
		$ivaproducto = limpiar($detalle[$i]['ivaproducto']);
		$descproducto = limpiar($detalle[$i]['descproducto']);
		$documento = limpiar("TRASPASO ".$codtraspaso);
		$fechakardex = limpiar(date("Y-m-d"));
		$recibe = limpiar($_POST["recibe"]);
		$stmt->execute();

	}//FIN DE REGISTRO DE PRODUCTOS

        }//FIN SESSION DETALLES
        
        ####################### DESTRUYO LA VARIABLE DE SESSION #####################
	    unset($_SESSION["CarritoTraspaso"]);
        $this->dbh->commit();
		
echo "<span class='fa fa-check-square-o'></span> EL TRASPASO DE PRODUCTOS HA SIDO REALIZADO EXITOSAMENTE <a href='reportepdf?codtraspaso=".encrypt($codtraspaso)."&codsucursal=".encrypt($codsucursal)."&tipo=".encrypt("FACTURATRASPASO")."' class='on-default' data-placement='left' data-toggle='tooltip' data-original-title='Imprimir Documento' target='_black' rel='noopener noreferrer'><font color='black'><strong>IMPRIMIR REPORTE</strong></font color></a></div>";
	exit;
	}
}
############################## FUNCION REGISTRAR TRASPASOS #############################

############################## FUNCION LISTAR TRASPASOS ################################
public function ListarTraspasos()
{
	self::SetNames();

	if ($_SESSION['acceso'] == "administradorG") {

	$sql = "SELECT 
	traspasos.idtraspaso, 
	traspasos.codtraspaso, 
	traspasos.envia, 
	traspasos.recibe, 
	traspasos.subtotalivasi, 
	traspasos.subtotalivano, 
	traspasos.iva, 
	traspasos.totaliva, 
	traspasos.descuento, 
	traspasos.totaldescuento,
	traspasos.totalpago, 
	traspasos.totalpago2, 
	traspasos.fechatraspaso, 
	traspasos.observaciones, 
	traspasos.codigo,
	traspasos.codsucursal,
	sucursales.documsucursal, 
	sucursales.cuitsucursal, 
	sucursales.razonsocial,
	sucursales2.documsucursal AS documsucursal2, 
	sucursales2.cuitsucursal AS cuitsucursal2, 
	sucursales2.razonsocial AS razonsocial2,
	documentos.documento,
	documentos2.documento AS documento2,
	documentos3.documento AS documento3,
	documentos4.documento AS documento4,
	SUM(detallestraspasos.cantidad) AS articulos 
	FROM (traspasos LEFT JOIN detallestraspasos ON detallestraspasos.codtraspaso = traspasos.codtraspaso) 
	LEFT JOIN sucursales ON traspasos.envia = sucursales.codsucursal
	LEFT JOIN documentos ON sucursales.documsucursal = documentos.coddocumento
	LEFT JOIN documentos AS documentos2 ON sucursales.documencargado = documentos2.coddocumento 
	LEFT JOIN sucursales AS sucursales2 ON traspasos.recibe = sucursales2.codsucursal
	LEFT JOIN documentos AS documentos3 ON sucursales.documsucursal = documentos3.coddocumento
	LEFT JOIN documentos AS documentos4 ON sucursales.documencargado = documentos4.coddocumento
	LEFT JOIN usuarios ON traspasos.codigo = usuarios.codigo GROUP BY detallestraspasos.codtraspaso ORDER BY traspasos.idtraspaso DESC";
	foreach ($this->dbh->query($sql) as $row)
	{
		$this->p[] = $row;
	}
	return $this->p;
	$this->dbh=null;

	} else {

   $sql = "SELECT 
	traspasos.idtraspaso, 
	traspasos.codtraspaso, 
	traspasos.envia, 
	traspasos.recibe, 
	traspasos.subtotalivasi, 
	traspasos.subtotalivano, 
	traspasos.iva, 
	traspasos.totaliva, 
	traspasos.descuento, 
	traspasos.totaldescuento,
	traspasos.totalpago, 
	traspasos.totalpago2, 
	traspasos.fechatraspaso, 
	traspasos.observaciones, 
	traspasos.codigo,
	traspasos.codsucursal,
	sucursales.documsucursal, 
	sucursales.cuitsucursal, 
	sucursales.razonsocial,
	sucursales2.documsucursal AS documsucursal2, 
	sucursales2.cuitsucursal AS cuitsucursal2, 
	sucursales2.razonsocial AS razonsocial2,
	documentos.documento,
	documentos2.documento AS documento2,
	documentos3.documento AS documento3,
	documentos4.documento AS documento4,
	SUM(detallestraspasos.cantidad) AS articulos 
	FROM (traspasos LEFT JOIN detallestraspasos ON detallestraspasos.codtraspaso = traspasos.codtraspaso) 
	LEFT JOIN sucursales ON traspasos.envia = sucursales.codsucursal
	LEFT JOIN documentos ON sucursales.documsucursal = documentos.coddocumento
	LEFT JOIN documentos AS documentos2 ON sucursales.documencargado = documentos2.coddocumento 
	LEFT JOIN sucursales AS sucursales2 ON traspasos.recibe = sucursales2.codsucursal
	LEFT JOIN documentos AS documentos3 ON sucursales.documsucursal = documentos3.coddocumento
	LEFT JOIN documentos AS documentos4 ON sucursales.documencargado = documentos4.coddocumento
	LEFT JOIN usuarios ON traspasos.codigo = usuarios.codigo 
	WHERE traspasos.codsucursal = '".limpiar($_SESSION["codsucursal"])."' GROUP BY detallestraspasos.codtraspaso ORDER BY traspasos.idtraspaso DESC";
	foreach ($this->dbh->query($sql) as $row)
	{
		$this->p[] = $row;
	}
	return $this->p;
	$this->dbh=null;

     }
}
############################ FUNCION LISTAR TRASPASOS ############################

############################ FUNCION ID TRASPASOS #################################
	public function TraspasosPorId()
	{
		self::SetNames();
		$sql = "SELECT 
	traspasos.idtraspaso, 
	traspasos.codtraspaso, 
	traspasos.envia, 
	traspasos.recibe,
	traspasos.subtotalivasi,
	traspasos.subtotalivano, 
	traspasos.iva,
	traspasos.totaliva, 
	traspasos.descuento,
	traspasos.totaldescuento, 
	traspasos.totalpago, 
	traspasos.totalpago2, 
	traspasos.fechatraspaso, 
	traspasos.observaciones, 
	traspasos.codigo,
	traspasos.codsucursal,
	sucursales.documsucursal, 
	sucursales.cuitsucursal, 
	sucursales.razonsocial,
	sucursales.direcsucursal,
	sucursales.correosucursal,
	sucursales.tlfsucursal,
	sucursales.documencargado,
	sucursales.dniencargado,
	sucursales.nomencargado,
	sucursales.tlfencargado,
	sucursales2.documsucursal AS documsucursal2, 
	sucursales2.cuitsucursal AS cuitsucursal2, 
	sucursales2.razonsocial AS razonsocial2,
	sucursales2.direcsucursal AS direcsucursal2,
	sucursales2.correosucursal AS correosucursal2,
	sucursales2.tlfsucursal AS tlfsucursal2,
	sucursales2.documencargado AS documencargado2,
	sucursales2.dniencargado AS dniencargado2,
	sucursales2.nomencargado AS nomencargado2,
	sucursales2.tlfencargado AS tlfencargado2,
	documentos.documento,
	documentos2.documento AS documento2,
	documentos3.documento AS documento3,
	documentos4.documento AS documento4,
	usuarios.dni, 
	usuarios.nombres,
	provincias.provincia,
	departamentos.departamento,
	provincias2.provincia AS provincia2,
	departamentos2.departamento AS departamento2
	FROM (traspasos LEFT JOIN detallestraspasos ON detallestraspasos.codtraspaso = traspasos.codtraspaso) 
	LEFT JOIN sucursales ON traspasos.envia = sucursales.codsucursal
	LEFT JOIN documentos ON sucursales.documsucursal = documentos.coddocumento
	LEFT JOIN documentos AS documentos2 ON sucursales.documencargado = documentos2.coddocumento
	LEFT JOIN provincias ON sucursales.id_provincia = provincias.id_provincia 
	LEFT JOIN departamentos ON sucursales.id_departamento = departamentos.id_departamento 
	LEFT JOIN sucursales AS sucursales2 ON traspasos.recibe = sucursales2.codsucursal
	LEFT JOIN documentos AS documentos3 ON sucursales.documsucursal = documentos3.coddocumento
	LEFT JOIN documentos AS documentos4 ON sucursales.documencargado = documentos4.coddocumento
	LEFT JOIN provincias AS provincias2 ON sucursales2.id_provincia = provincias2.id_provincia 
	LEFT JOIN departamentos AS departamentos2 ON sucursales2.id_departamento = departamentos2.id_departamento

	LEFT JOIN usuarios ON traspasos.codigo = usuarios.codigo 
		WHERE traspasos.codtraspaso = ? AND traspasos.codsucursal = ?";

		$stmt = $this->dbh->prepare($sql);
		$stmt->execute(array(decrypt($_GET["codtraspaso"]),decrypt($_GET["codsucursal"])));
		$num = $stmt->rowCount();
		if($num==0)
		{
			echo "";
		}
		else
		{
			if($row = $stmt->fetch(PDO::FETCH_ASSOC))
			{
				$this->p[] = $row;
			}
			return $this->p;
			$this->dbh=null;
		}
	}
############################ FUNCION ID TRASPASOS #################################

############################ FUNCION VER DETALLES TRASPASOS ###########################
public function VerDetallesTraspasos()
	{
		self::SetNames();
		$sql = "SELECT
		detallestraspasos.coddetalletraspaso,
		detallestraspasos.codtraspaso,
		detallestraspasos.codproducto,
		detallestraspasos.fechaexpiracion,
		detallestraspasos.cantidad,
		detallestraspasos.preciocompra,
		detallestraspasos.precioventa,
		detallestraspasos.ivaproducto,
		detallestraspasos.descproducto,
		detallestraspasos.valortotal, 
		detallestraspasos.totaldescuentov,
		detallestraspasos.valorneto,
		detallestraspasos.valorneto2,
		detallestraspasos.codsucursal,
		productos.producto,
		marcas.nommarca,
		modelos.nommodelo
		FROM detallestraspasos INNER JOIN productos ON detallestraspasos.codproducto = productos.codproducto 
		INNER JOIN marcas ON productos.codmarca = marcas.codmarca
		LEFT JOIN modelos ON productos.codmodelo = modelos.codmodelo 
		WHERE detallestraspasos.codtraspaso = ? AND detallestraspasos.codsucursal = ?GROUP BY productos.codproducto";
		$stmt = $this->dbh->prepare($sql);
		$stmt->execute(array(decrypt($_GET["codtraspaso"]),decrypt($_GET["codsucursal"])));
		$num = $stmt->rowCount();
		
			while($row = $stmt->fetch(PDO::FETCH_ASSOC))
			{
				$this->p[]=$row;
			}
			return $this->p;
			$this->dbh=null;
		}
############################ FUNCION VER DETALLES TRASPASOS ############################

############################ FUNCION ACTUALIZAR TRASPASOS ##########################
	public function ActualizarTraspasos()
	{
		self::SetNames();
		if(empty($_POST["codtraspaso"]) or empty($_POST["envia"]) or empty($_POST["recibe"]) or empty($_POST["codsucursal"]) or empty($_POST["fechatraspaso"]))
		{
			echo "1";
			exit;
		}

		for($i=0;$i<count($_POST['coddetalletraspaso']);$i++){  //recorro el array
	        if (!empty($_POST['coddetalletraspaso'][$i])) {

		       if($_POST['cantidad'][$i]==0){

			      echo "2";
			      exit();
		       }
	        }
        }

	$this->dbh->beginTransaction();
	for($i=0;$i<count($_POST['coddetalletraspaso']);$i++){  //recorro el array
	if (!empty($_POST['coddetalletraspaso'][$i])) {

	$sql = "SELECT cantidad FROM detallestraspasos WHERE coddetalletraspaso = '".limpiar($_POST['coddetalletraspaso'][$i])."' AND codtraspaso = '".limpiar($_POST["codtraspaso"])."' AND codsucursal = '".limpiar($_POST["codsucursal"])."'";
		foreach ($this->dbh->query($sql) as $row)
		{
			$this->p[] = $row;
		}
		
		$cantidadbd = $row['cantidad'];

		if($cantidadbd != $_POST['cantidad'][$i]){

		############ CONSULTO LA EXISTENCIA DE PRODUCTO EN ALMACEN SALIENTE ############
	   $sql = "SELECT existencia FROM productos WHERE codproducto = '".limpiar($_POST['codproducto'][$i])."' AND codsucursal = '".limpiar($_POST["codsucursal"])."'";
		    foreach ($this->dbh->query($sql) as $row)
		{
			$this->p[] = $row;
		}
		$existenciabd = $row['existencia'];
		$cantidad = $_POST["cantidad"][$i];
		$cantidadbd = $_POST["cantidadbd"][$i];
		$totaltraspaso = $cantidad-$cantidadbd;

        if ($totaltraspaso > $existenciabd) 
          { 
		    echo "3";
		    exit;
	      }

			$query = "UPDATE detallestraspasos set"
			." cantidad = ?, "
			." valortotal = ?, "
			." totaldescuentov = ?, "
			." valorneto = ?, "
			." valorneto2 = ? "
			." WHERE "
			." coddetalletraspaso = ? AND codtraspaso = ? AND codsucursal = ?;
			";
			$stmt = $this->dbh->prepare($query);
			$stmt->bindParam(1, $cantidad);
			$stmt->bindParam(2, $valortotal);
			$stmt->bindParam(3, $totaldescuentov);
			$stmt->bindParam(4, $valorneto);
			$stmt->bindParam(5, $valorneto2);
			$stmt->bindParam(6, $coddetalletraspaso);
			$stmt->bindParam(7, $codtraspaso);
			$stmt->bindParam(8, $codsucursal);

			$cantidad = limpiar($_POST['cantidad'][$i]);
			$preciocompra = limpiar($_POST['preciocompra'][$i]);
			$precioventa = limpiar($_POST['precioventa'][$i]);
			$ivaproducto = limpiar($_POST['ivaproducto'][$i]);
			$descuento = $_POST['descproducto'][$i]/100;
			$valortotal = number_format($_POST['precioventa'][$i] * $_POST['cantidad'][$i], 2, '.', '');
			$totaldescuentov = number_format($valortotal * $descuento, 2, '.', '');
			$valorneto = number_format($valortotal - $totaldescuentov, 2, '.', '');
			$valorneto2 = number_format($_POST['preciocompra'][$i] * $_POST['cantidad'][$i], 2, '.', '');
			$coddetalletraspaso = limpiar($_POST['coddetalletraspaso'][$i]);
			$codtraspaso = limpiar($_POST["codtraspaso"]);
			$codsucursal = limpiar($_POST["codsucursal"]);
			$stmt->execute();

		############## ACTUALIZAMOS EXISTENCIA DEL PRODUCTO EN ALMACEN #1 ##############
		$sql2 = " UPDATE productos set "
			  ." existencia = ? "
			  ." WHERE "
			  ." codproducto = '".limpiar($_POST["codproducto"][$i])."' AND codsucursal = '".limpiar($_POST["codsucursal"])."';
			  ";
			  $stmt = $this->dbh->prepare($sql2);
			  $stmt->bindParam(1, $existencia);
			  $existencia = $existenciabd-$totaltraspaso;
			  $stmt->execute();

		############### ACTUALIZAMOS LOS DATOS DEL PRODUCTO EN KARDEX #1 ###############
		$sql3 = " UPDATE kardex set "
		      ." salidas = ?, "
		      ." stockactual = ? "
			  ." WHERE "
			  ." codproceso = '".limpiar($_POST["codtraspaso"])."' and codproducto = '".limpiar($_POST["codproducto"][$i])."' AND codsucursal = '".limpiar($_POST["codsucursal"])."';
			   ";
		$stmt = $this->dbh->prepare($sql3);
		$stmt->bindParam(1, $salidas);
		$stmt->bindParam(2, $existencia);
		
		$salidas = limpiar($_POST["cantidad"][$i]);
		$stmt->execute();


		############ CONSULTO LA EXISTENCIA DE PRODUCTO EN ALMACEN ENTRANDO ############
		$sql = "SELECT existencia FROM productos WHERE codproducto = '".limpiar($_POST['codproducto'][$i])."' AND codsucursal = '".limpiar($_POST["recibe"])."'";
		    foreach ($this->dbh->query($sql) as $row)
		{
			$this->p[] = $row;
		}
		$existenciarecibebd = $row['existencia'];

		############## ACTUALIZAMOS EXISTENCIA DE PRODUCTO EN ALMACEN #2 ##############
		$sql2 = " UPDATE productos set "
			  ." existencia = ? "
			  ." WHERE "
			  ." codproducto = '".limpiar($_POST["codproducto"][$i])."' AND codsucursal = '".limpiar($_POST["recibe"])."';
			  ";
			  $stmt = $this->dbh->prepare($sql2);
			  $stmt->bindParam(1, $existenciarecibe);
			  $existenciarecibe = $existenciarecibebd+$totaltraspaso;
			  $stmt->execute();

	    ############### ACTUALIZAMOS LOS DATOS DEL PRODUCTO EN KARDEX #2 ###############
		$sql3 = " UPDATE kardex set "
		      ." entradas = ?, "
		      ." stockactual = ? "
			  ." WHERE "
			  ." codproceso = '".limpiar($_POST["codtraspaso"])."' and codproducto = '".limpiar($_POST["codproducto"][$i])."' AND codsucursal = '".limpiar($_POST["recibe"])."';
			   ";
		$stmt = $this->dbh->prepare($sql3);
		$stmt->bindParam(1, $entradas);
		$stmt->bindParam(2, $existenciarecibe);
		
	    $existenciarecibe = $existenciarecibebd+$totaltraspaso;
		$entradas = limpiar($_POST["cantidad"][$i]);
		$stmt->execute();


			} else {

               echo "";

		       }
	        }
        }
            $this->dbh->commit();

            ############ SUMO LOS IMPORTE DE PRODUCTOS CON IVA ##############
			$sql3 = "SELECT SUM(valorneto) AS valorneto, SUM(valorneto2) AS valorneto2 FROM detallestraspasos WHERE codtraspaso = '".limpiar($_POST["codtraspaso"])."' AND codsucursal = '".limpiar($_POST["codsucursal"])."' AND ivaproducto = 'SI'";
			foreach ($this->dbh->query($sql3) as $row3)
			{
				$this->p[] = $row3;
			}
			$subtotalivasi = ($row3['valorneto']== "" ? "0.00" : $row3['valorneto']);
			$subtotalivasi2 = ($row3['valorneto2']== "" ? "0.00" : $row3['valorneto2']);

		    ############ SUMO LOS IMPORTE DE PRODUCTOS SIN IVA ##############
			$sql4 = "SELECT SUM(valorneto) AS valorneto, SUM(valorneto2) AS valorneto2 FROM detallestraspasos WHERE codtraspaso = '".limpiar($_POST["codtraspaso"])."' AND codsucursal = '".limpiar($_POST["codsucursal"])."' AND ivaproducto = 'NO'";
			foreach ($this->dbh->query($sql4) as $row4)
			{
				$this->p[] = $row4;
			}
			$subtotalivano = ($row4['valorneto']== "" ? "0.00" : $row4['valorneto']);
			$subtotalivano2 = ($row4['valorneto2']== "" ? "0.00" : $row4['valorneto2']);

            ############ ACTUALIZO LOS TOTALES EN LA COTIZACION ##############
			$sql = " UPDATE traspasos SET "
			." subtotalivasi = ?, "
			." subtotalivano = ?, "
			." totaliva = ?, "
			." descuento = ?, "
			." totaldescuento = ?, "
			." totalpago = ?, "
			." totalpago2= ? "
			." WHERE "
			." codtraspaso = ? AND codsucursal = ?;
			";
			$stmt = $this->dbh->prepare($sql);
			$stmt->bindParam(1, $subtotalivasi);
			$stmt->bindParam(2, $subtotalivano);
			$stmt->bindParam(3, $totaliva);
			$stmt->bindParam(4, $descuento);
			$stmt->bindParam(5, $totaldescuento);
			$stmt->bindParam(6, $totalpago);
			$stmt->bindParam(7, $totalpago2);
			$stmt->bindParam(8, $codtraspaso);
			$stmt->bindParam(9, $codsucursal);

			$iva = $_POST["iva"]/100;
			$totaliva = number_format($subtotalivasi*$iva, 2, '.', '');
			$descuento = limpiar($_POST["descuento"]);
		    $txtDescuento = $_POST["descuento"]/100;
		    $total = number_format($subtotalivasi+$subtotalivano+$totaliva, 2, '.', '');
		    $totaldescuento = number_format($total*$txtDescuento, 2, '.', '');
		    $totalpago = number_format($total-$totaldescuento, 2, '.', '');
			$totalpago2 = number_format($subtotalivasi2+$subtotalivano2, 2, '.', '');
			$codtraspaso = limpiar($_POST["codtraspaso"]);
			$codsucursal = limpiar($_POST["codsucursal"]);
			$stmt->execute();


echo "<span class='fa fa-check-square-o'></span> EL TRASPASO DE PRODUCTOS HA SIDO ACTUALIZADO EXITOSAMENTE <a href='reportepdf?codtraspaso=".encrypt($codtraspaso)."&codsucursal=".encrypt($codsucursal)."&tipo=".encrypt("FACTURATRASPASO")."' class='on-default' data-placement='left' data-toggle='tooltip' data-original-title='Imprimir Documento' target='_black' rel='noopener noreferrer'><font color='black'><strong>IMPRIMIR REPORTE</strong></font color></a></div>";
	exit;
}
######################### FUNCION ACTUALIZAR TRASPASOS ############################

######################### FUNCION AGREGAR DETALLES TRASPASOS #########################
	public function AgregarDetallesTraspasos()
	{
		self::SetNames();
		if(empty($_POST["codtraspaso"]) or empty($_POST["envia"]) or empty($_POST["recibe"]) or empty($_POST["codsucursal"]) or empty($_POST["fechatraspaso"]))
		{
			echo "1";
			exit;
		}


        if(empty($_SESSION["CarritoTraspaso"]) || $_POST["txtTotal"]=="0.00")
		{
			echo "2";
			exit;
			
		} else {


	    $this->dbh->beginTransaction();
	    $detalle = $_SESSION["CarritoTraspaso"];
		for($i=0;$i<count($detalle);$i++){

        ############### VERIFICO AL EXISTENCIA DEL PRODUCTO AGREGADO ################
		$sql = "SELECT * FROM productos WHERE codproducto = '".limpiar($detalle[$i]['txtCodigo'])."' AND codsucursal = '".limpiar($_POST["codsucursal"])."'";
		  foreach ($this->dbh->query($sql) as $row)
		   {
			$this->p[] = $row;
		   }

		$existenciabd = $row['existencia'];

		############# REVISAMOS QUE LA CANTIDAD NO SEA IGUAL A CERO ##############
			if($detalle[$i]['cantidad']==0){

				echo "3";
				exit;
		    
		    }

		############ REVISAMOS SI LA CANTIDAD ES MAYOR QUE LA EXISTENCIA #######
            if ($detalle[$i]['cantidad'] > $existenciabd) 
            { 
		       echo "4";
		       exit;
	        }

	$sql = "SELECT codtraspaso, codproducto FROM detallestraspasos WHERE codtraspaso = '".limpiar($_POST['codtraspaso'])."' AND codsucursal = '".limpiar($_POST['codsucursal'])."' AND codproducto = '".limpiar($detalle[$i]['txtCodigo'])."'";
			$stmt = $this->dbh->prepare($sql);
			$stmt->execute();
			$num = $stmt->rowCount();
			if($num == 0)
			{

        $query = "INSERT INTO detallestraspasos values (null, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?); ";
		$stmt = $this->dbh->prepare($query);
		$stmt->bindParam(1, $codtraspaso);
	    $stmt->bindParam(2, $codproducto);
	    $stmt->bindParam(3, $producto);
	    $stmt->bindParam(4, $fechaexpiracion);
		$stmt->bindParam(5, $cantidad);
		$stmt->bindParam(6, $preciocompra);
		$stmt->bindParam(7, $precioventa);
		$stmt->bindParam(8, $ivaproducto);
		$stmt->bindParam(9, $descproducto);
		$stmt->bindParam(10, $valortotal);
		$stmt->bindParam(11, $totaldescuentov);
		$stmt->bindParam(12, $valorneto);
		$stmt->bindParam(13, $valorneto2);
		$stmt->bindParam(14, $codsucursal);
			
		$codtraspaso = limpiar($_POST["codtraspaso"]);
		$codproducto = limpiar($detalle[$i]['txtCodigo']);
		$producto = limpiar($detalle[$i]['producto']);
if (limpiar($detalle[$i]['fechaexpiracion']=="")) { $fechaexpiracion = "0000-00-00";  } else { $fechaexpiracion = limpiar(date("Y-m-d",strtotime($detalle[$i]['fechaexpiracion']))); }
		$cantidad = limpiar($detalle[$i]['cantidad']);
		$preciocompra = limpiar($detalle[$i]['precio']);
		$precioventa = limpiar($detalle[$i]['precio2']);
		$ivaproducto = limpiar($detalle[$i]['ivaproducto']);
		$descproducto = limpiar($detalle[$i]['descproducto']);
		$descuento = $detalle[$i]['descproducto']/100;
		$valortotal = number_format($detalle[$i]['precio2']*$detalle[$i]['cantidad'], 2, '.', '');
		$totaldescuentov = number_format($valortotal*$descuento, 2, '.', '');
	    $valorneto = number_format($valortotal-$totaldescuentov, 2, '.', '');
		$valorneto2 = number_format($detalle[$i]['precio']*$detalle[$i]['cantidad'], 2, '.', '');
		$codsucursal = limpiar($_SESSION["codsucursal"]);
		$stmt->execute();

	    ##################### ACTUALIZO LA EXISTENCIA DEL ALMACEN #1 ###################
		$sql = " UPDATE productos set "
			  ." existencia = ? "
			  ." where "
			  ." codproducto = '".limpiar($detalle[$i]['txtCodigo'])."' and codsucursal = '".limpiar($_POST["codsucursal"])."';
			   ";
		$stmt = $this->dbh->prepare($sql);
		$stmt->bindParam(1, $existencia);
		$cantraspaso = limpiar($detalle[$i]['cantidad']);
		$existencia = $existenciabd-$cantraspaso;
		$stmt->execute();

		############### REGISTRAMOS LOS PRODUCTOS SALIENTES EN KARDEX #1 ###############
        $query = "INSERT INTO kardex values (null, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?); ";
		$stmt = $this->dbh->prepare($query);
		$stmt->bindParam(1, $codtraspaso);
		$stmt->bindParam(2, $recibe);
		$stmt->bindParam(3, $codproducto);
		$stmt->bindParam(4, $movimiento);
		$stmt->bindParam(5, $entradas);
		$stmt->bindParam(6, $salidas);
		$stmt->bindParam(7, $devolucion);
		$stmt->bindParam(8, $stockactual);
		$stmt->bindParam(9, $ivaproducto);
		$stmt->bindParam(10, $descproducto);
		$stmt->bindParam(11, $precio);
		$stmt->bindParam(12, $documento);
		$stmt->bindParam(13, $fechakardex);		
		$stmt->bindParam(14, $codsucursal);

		$recibe = limpiar($_POST["recibe"]);
		$codproducto = limpiar($detalle[$i]['txtCodigo']);
		$movimiento = limpiar("SALIDAS");
		$entradas = limpiar("0");
		$salidas= limpiar($detalle[$i]['cantidad']);
		$devolucion = limpiar("0");
		$stockactual = limpiar($existenciabd-$detalle[$i]['cantidad']);
		$precio = limpiar($detalle[$i]["precio2"]);
		$ivaproducto = limpiar($detalle[$i]['ivaproducto']);
		$descproducto = limpiar($detalle[$i]['descproducto']);
		$documento = limpiar("TRASPASO: ".$codtraspaso);
		$fechakardex = limpiar(date("Y-m-d"));
		$codsucursal = limpiar($_POST["codsucursal"]);
		$stmt->execute();

	  } else {

	  	$sql = "SELECT cantidad FROM detallestraspasos WHERE codtraspaso = '".limpiar($_POST['codtraspaso'])."' AND codsucursal = '".limpiar($_POST['codsucursal'])."' AND codproducto = '".limpiar($detalle[$i]['txtCodigo'])."'";
		foreach ($this->dbh->query($sql) as $row)
		{
			$this->p[] = $row;
		}
		$cantidadbd = $row['cantidad'];

	  	$query = "UPDATE detallestraspasos set"
		." cantidad = ?, "
		." descproducto = ?, "
		." valortotal = ?, "
		." totaldescuentov = ?, "
		." valorneto = ?, "
		." valorneto2 = ? "
		." WHERE "
		." codtraspaso = ? AND codsucursal = ? AND codproducto = ?;
		";
		$stmt = $this->dbh->prepare($query);
		$stmt->bindParam(1, $cantidad);
		$stmt->bindParam(2, $descproducto);
		$stmt->bindParam(3, $valortotal);
		$stmt->bindParam(4, $totaldescuentov);
		$stmt->bindParam(5, $valorneto);
		$stmt->bindParam(6, $valorneto2);
		$stmt->bindParam(7, $codtraspaso);
		$stmt->bindParam(8, $codsucursal);
		$stmt->bindParam(9, $codproducto);

		$cantidad = limpiar($detalle[$i]['cantidad']+$cantidadbd);
		$preciocompra = limpiar($detalle[$i]['precio']);
		$precioventa = limpiar($detalle[$i]['precio2']);
		$ivaproducto = limpiar($detalle[$i]['ivaproducto']);
		$descproducto = limpiar($detalle[$i]['descproducto']);
		$descuento = $detalle[$i]['descproducto']/100;
		$valortotal = number_format($detalle[$i]['precio2'] * $cantidad, 2, '.', '');
		$totaldescuentov = number_format($valortotal * $descuento, 2, '.', '');
		$valorneto = number_format($valortotal - $totaldescuentov, 2, '.', '');
		$valorneto2 = number_format($detalle[$i]['precio'] * $cantidad, 2, '.', '');
		$codtraspaso = limpiar($_POST["codtraspaso"]);
		$codsucursal = limpiar($_POST["codsucursal"]);
		$codproducto = limpiar($detalle[$i]['txtCodigo']);
		$stmt->execute();

		############## ACTUALIZAMOS EXISTENCIA DEL PRODUCTO EN ALMACEN #1 ##############
		$sql = " UPDATE productos set "
			  ." existencia = ? "
			  ." where "
			  ." codproducto = '".limpiar($detalle[$i]['txtCodigo'])."' and codsucursal = '".limpiar($_POST["codsucursal"])."';
			   ";
		$stmt = $this->dbh->prepare($sql);
		$stmt->bindParam(1, $existencia);
		$cantraspaso = limpiar($detalle[$i]['cantidad']);
		$existencia = $existenciabd-$cantraspaso;
		$stmt->execute();

		############### ACTUALIZAMOS LOS DATOS DEL PRODUCTO EN KARDEX #1 ##############
		$sql3 = " UPDATE kardex set "
		      ." salidas = ?, "
		      ." stockactual = ? "
			  ." WHERE "
			  ." codproceso = '".limpiar($_POST["codtraspaso"])."' and codproducto = '".limpiar($detalle[$i]['txtCodigo'])."' AND codsucursal = '".limpiar($_POST["codsucursal"])."';
			   ";
		$stmt = $this->dbh->prepare($sql3);
		$stmt->bindParam(1, $salidas);
		$stmt->bindParam(2, $existencia);
		
		$salidas = limpiar($detalle[$i]['cantidad']+$cantidadbd);
		$stmt->execute();

	 }//FIN DE AGREGAR DETALLES DE PRODUCTOS

	############ VERIFICO SI EL PRODUCTO YA EXISTE EN LA SUCURSAL QUE RECIBE ###########
	$sql = "SELECT codproducto FROM productos WHERE codproducto = ? AND codsucursal = ?";
	$stmt = $this->dbh->prepare($sql);
	$stmt->execute(array(limpiar($detalle[$i]['txtCodigo']),limpiar($_POST['recibe'])));
	$num = $stmt->rowCount();
	if($num == 0)
	{

		$query = "INSERT INTO productos values (null, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);";
		$stmt = $this->dbh->prepare($query);
		$stmt->bindParam(1, $codproducto);
		$stmt->bindParam(2, $producto);
		$stmt->bindParam(3, $fabricante);
		$stmt->bindParam(4, $codfamilia);
		$stmt->bindParam(5, $codsubfamilia);
		$stmt->bindParam(6, $codmarca);
		$stmt->bindParam(7, $codmodelo);
		$stmt->bindParam(8, $codpresentacion);
		$stmt->bindParam(9, $codcolor);
		$stmt->bindParam(10, $codorigen);
		$stmt->bindParam(11, $year);
		$stmt->bindParam(12, $nroparte);
		$stmt->bindParam(13, $lote);
		$stmt->bindParam(14, $peso);
		$stmt->bindParam(15, $preciocompra);
		$stmt->bindParam(16, $precioxmenor);
		$stmt->bindParam(17, $precioxmayor);
		$stmt->bindParam(18, $precioxpublico);
		$stmt->bindParam(19, $existencia);
		$stmt->bindParam(20, $stockoptimo);
		$stmt->bindParam(21, $stockmedio);
		$stmt->bindParam(22, $stockminimo);
		$stmt->bindParam(23, $ivaproducto);
		$stmt->bindParam(24, $descproducto);
		$stmt->bindParam(25, $codigobarra);
		$stmt->bindParam(26, $fechaelaboracion);
		$stmt->bindParam(27, $fechaoptimo);
		$stmt->bindParam(28, $fechamedio);
		$stmt->bindParam(29, $fechaminimo);
		$stmt->bindParam(30, $codproveedor);
		$stmt->bindParam(31, $stockteorico);
		$stmt->bindParam(32, $motivoajuste);
		$stmt->bindParam(33, $recibe);

		$codproducto = limpiar($detalle[$i]["txtCodigo"]);
		$producto = limpiar($row["producto"]);
		$fabricante = limpiar($row["fabricante"]);
		$codfamilia = limpiar($row["codfamilia"]);
		$codsubfamilia = limpiar($row["codsubfamilia"]);
		$codmarca = limpiar($row["codmarca"]);
		$codmodelo = limpiar($row["codmodelo"]);
		$codpresentacion = limpiar($row["codpresentacion"]);
		$codcolor = limpiar($row["codcolor"]);
		$codorigen = limpiar($row["codorigen"]);
		$year = limpiar($row["year"]);
		$nroparte = limpiar($row["nroparte"]);
		$lote = limpiar($row["lote"]);
		$peso = limpiar($row["peso"]);
		$preciocompra = limpiar($detalle[$i]["precio"]);
		$precioxmenor = limpiar($row["precioxmenor"]);
		$precioxmayor = limpiar($row["precioxmayor"]);
		$precioxpublico = limpiar($row["precioxpublico"]);
		$existencia = limpiar($detalle[$i]["cantidad"]);
		$stockoptimo = limpiar($row["stockoptimo"]);
		$stockmedio = limpiar($row["stockmedio"]);
		$stockminimo = limpiar($row["stockminimo"]);
		$ivaproducto = limpiar($detalle[$i]['ivaproducto']);
		$descproducto = limpiar($detalle[$i]['descproducto']);
		$codigobarra = limpiar($row["codigobarra"]);
		$fechaelaboracion = limpiar($row['fechaelaboracion']);
		$fechaoptimo = limpiar($row['fechaoptimo']);
		$fechamedio = limpiar($row['fechamedio']);
		$fechaminimo = limpiar($row['fechaminimo']);
		$codproveedor = limpiar($row["codproveedor"]);
		$stockteorico = limpiar("0");
		$motivoajuste = limpiar("NINGUNO");
		$recibe = limpiar($_POST["recibe"]);
		$stmt->execute();

		################# REGISTRAMOS KARDEX DE PRODUCTO QUE RECIBE ###################
        $query = "INSERT INTO kardex values (null, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?); ";
		$stmt = $this->dbh->prepare($query);
		$stmt->bindParam(1, $codtraspaso);
		$stmt->bindParam(2, $envia);
		$stmt->bindParam(3, $codproducto);
		$stmt->bindParam(4, $movimiento);
		$stmt->bindParam(5, $entradas);
		$stmt->bindParam(6, $salidas);
		$stmt->bindParam(7, $devolucion);
		$stmt->bindParam(8, $stockactual);
		$stmt->bindParam(9, $ivaproducto);
		$stmt->bindParam(10, $descproducto);
		$stmt->bindParam(11, $precio);
		$stmt->bindParam(12, $documento);
		$stmt->bindParam(13, $fechakardex);		
		$stmt->bindParam(14, $recibe);

		$envia = limpiar("0");
		$codproducto = limpiar($detalle[$i]['txtCodigo']);
		$movimiento = limpiar("ENTRADAS");
		$entradas= limpiar($detalle[$i]['cantidad']);
		$salidas = limpiar("0");
		$devolucion = limpiar("0");
		$stockactual = limpiar($detalle[$i]['cantidad']);
		$precio = limpiar($detalle[$i]["precio"]);
		$ivaproducto = limpiar($detalle[$i]['ivaproducto']);
		$descproducto = limpiar($detalle[$i]['descproducto']);
		$documento = limpiar("TRASPASO ".$codtraspaso);
		$fechakardex = limpiar(date("Y-m-d"));
		$recibe = limpiar($_POST["recibe"]);
		$stmt->execute();

	} else {

		################ VERIFICO LA EXISTENCIA DEL PRODUCTO EN ALMACEN ################
		$sql = "SELECT * FROM productos WHERE codproducto = '".limpiar($detalle[$i]['txtCodigo'])."' AND codsucursal = '".limpiar($_POST['recibe'])."'";
		foreach ($this->dbh->query($sql) as $row)
		{
			$this->p[] = $row;
		}
		$existenciarecibebd = $row['existencia'];

		############# ACTUALIZAMOS LA EXISTENCIA DE PRODUCTOS RECIBIDOS ###############
		$sql = "UPDATE productos set "
		      ." preciocompra = ?, "
			  ." existencia = ?, "
			  ." ivaproducto = ?, "
			  ." descproducto = ? "
			  ." WHERE "
			  ." codproducto = ? AND codsucursal = ?;
			   ";
		$stmt = $this->dbh->prepare($sql);
		$stmt->bindParam(1, $preciocompra);
		$stmt->bindParam(2, $existencia);
		$stmt->bindParam(3, $ivaproducto);
		$stmt->bindParam(4, $descproducto);
		$stmt->bindParam(5, $codproducto);
		$stmt->bindParam(6, $recibe);
		
		$preciocompra = limpiar($detalle[$i]['precio']);
		$existencia = limpiar($existenciarecibebd+$detalle[$i]['cantidad']);
		$ivaproducto = limpiar($detalle[$i]['ivaproducto']);
		$descproducto = limpiar($detalle[$i]['descproducto']);
		$codproducto = limpiar($detalle[$i]['txtCodigo']);
		$recibe = limpiar($_POST['recibe']);
		$stmt->execute();

		################# REGISTRAMOS KARDEX DE PRODUCTO QUE RECIBE ###################
        $query = "INSERT INTO kardex values (null, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?); ";
		$stmt = $this->dbh->prepare($query);
		$stmt->bindParam(1, $codtraspaso);
		$stmt->bindParam(2, $envia);
		$stmt->bindParam(3, $codproducto);
		$stmt->bindParam(4, $movimiento);
		$stmt->bindParam(5, $entradas);
		$stmt->bindParam(6, $salidas);
		$stmt->bindParam(7, $devolucion);
		$stmt->bindParam(8, $stockactual);
		$stmt->bindParam(9, $ivaproducto);
		$stmt->bindParam(10, $descproducto);
		$stmt->bindParam(11, $precio);
		$stmt->bindParam(12, $documento);
		$stmt->bindParam(13, $fechakardex);		
		$stmt->bindParam(14, $recibe);

		$envia = limpiar("0");
		$codproducto = limpiar($detalle[$i]['txtCodigo']);
		$movimiento = limpiar("ENTRADAS");
		$entradas= limpiar($detalle[$i]['cantidad']);
		$salidas = limpiar("0");
		$devolucion = limpiar("0");
		$stockactual = limpiar($existenciarecibebd+$detalle[$i]['cantidad']);
		$precio = limpiar($detalle[$i]["precio"]);
		$ivaproducto = limpiar($detalle[$i]['ivaproducto']);
		$descproducto = limpiar($detalle[$i]['descproducto']);
		$documento = limpiar("TRASPASO ".$codtraspaso);
		$fechakardex = limpiar(date("Y-m-d"));
		$recibe = limpiar($_POST["recibe"]);
		$stmt->execute();

	}//FIN DE REGISTRO DE PRODUCTOS

        }//FIN SESSION DETALLES

    
        ####################### DESTRUYO LA VARIABLE DE SESSION #####################
	    unset($_SESSION["CarritoTraspaso"]);
        $this->dbh->commit();

        ############ SUMO LOS IMPORTE DE PRODUCTOS CON IVA ##############
		$sql3 = "SELECT SUM(valorneto) AS valorneto, SUM(valorneto2) AS valorneto2 FROM detallestraspasos WHERE codtraspaso = '".limpiar($_POST["codtraspaso"])."' AND codsucursal = '".limpiar($_POST["codsucursal"])."' AND ivaproducto = 'SI'";
			foreach ($this->dbh->query($sql3) as $row3)
			{
				$this->p[] = $row3;
			}
			$subtotalivasi = ($row3['valorneto']== "" ? "0.00" : $row3['valorneto']);
			$subtotalivasi2 = ($row3['valorneto2']== "" ? "0.00" : $row3['valorneto2']);

		############ SUMO LOS IMPORTE DE PRODUCTOS SIN IVA ##############
		$sql4 = "SELECT SUM(valorneto) AS valorneto, SUM(valorneto2) AS valorneto2 FROM detallestraspasos WHERE codtraspaso = '".limpiar($_POST["codtraspaso"])."' AND codsucursal = '".limpiar($_POST["codsucursal"])."' AND ivaproducto = 'NO'";
			foreach ($this->dbh->query($sql4) as $row4)
			{
				$this->p[] = $row4;
			}
			$subtotalivano = ($row4['valorneto']== "" ? "0.00" : $row4['valorneto']);
			$subtotalivano2 = ($row4['valorneto2']== "" ? "0.00" : $row4['valorneto2']);


    ############ ACTUALIZO LOS TOTALES EN LA COTIZACION ##############
			$sql = " UPDATE traspasos SET "
			." subtotalivasi = ?, "
			." subtotalivano = ?, "
			." totaliva = ?, "
			." descuento = ?, "
			." totaldescuento = ?, "
			." totalpago = ?, "
			." totalpago2= ? "
			." WHERE "
			." codtraspaso = ? AND codsucursal = ?;
			";
			$stmt = $this->dbh->prepare($sql);
			$stmt->bindParam(1, $subtotalivasi);
			$stmt->bindParam(2, $subtotalivano);
			$stmt->bindParam(3, $totaliva);
			$stmt->bindParam(4, $descuento);
			$stmt->bindParam(5, $totaldescuento);
			$stmt->bindParam(6, $totalpago);
			$stmt->bindParam(7, $totalpago2);
			$stmt->bindParam(8, $codtraspaso);
			$stmt->bindParam(9, $codsucursal);

			$iva = $_POST["iva"]/100;
			$totaliva = number_format($subtotalivasi*$iva, 2, '.', '');
			$descuento = limpiar($_POST["descuento"]);
		    $txtDescuento = $_POST["descuento"]/100;
		    $total = number_format($subtotalivasi+$subtotalivano+$totaliva, 2, '.', '');
		    $totaldescuento = number_format($total*$txtDescuento, 2, '.', '');
		    $totalpago = number_format($total-$totaldescuento, 2, '.', '');
			$totalpago2 = number_format($subtotalivasi2+$subtotalivano2, 2, '.', '');
			$codtraspaso = limpiar($_POST["codtraspaso"]);
			$codsucursal = limpiar($_POST["codsucursal"]);
			$stmt->execute();
		

echo "<span class='fa fa-check-square-o'></span> LOS DETALLES DE PRODUCTOS FUERON AGREGADOS AL TRASPASO EXITOSAMENTE <a href='reportepdf?codtraspaso=".encrypt($codtraspaso)."&codsucursal=".encrypt($codsucursal)."&tipo=".encrypt("FACTURATRASPASO")."' class='on-default' data-placement='left' data-toggle='tooltip' data-original-title='Imprimir Documento' target='_black' rel='noopener noreferrer'><font color='black'><strong>IMPRIMIR REPORTE</strong></font color></a></div>";
	exit;
	}
}
########################### FUNCION AGREGAR DETALLES TRASPASOS #########################

########################## FUNCION ELIMINAR DETALLES TRASPASOS ##########################
	public function EliminarDetallesTraspasos()
	{
	self::SetNames();
		if ($_SESSION["acceso"]=="administradorS") {

        ############ CONSULTO DATOS DE TRASPASO ##############
		$sql = "SELECT * FROM traspasos WHERE codtraspaso = '".limpiar(decrypt($_GET["codtraspaso"]))."' AND codsucursal = '".limpiar(decrypt($_GET["codsucursal"]))."'";
		foreach ($this->dbh->query($sql) as $row)
		{
			$this->p[] = $row;
		}
		$recibebd = $row['recibe'];
		$totalpagobd = $row['totalpago'];

	$sql = "SELECT * FROM detallestraspasos WHERE codtraspaso = ? AND codsucursal = ?";
		$stmt = $this->dbh->prepare($sql);
		$stmt->execute(array(decrypt($_GET["codtraspaso"]),decrypt($_GET["codsucursal"])));
		$num = $stmt->rowCount();
		if($num > 1)
		{

			$sql = "SELECT codproducto, cantidad, precioventa, ivaproducto, descproducto FROM detallestraspasos WHERE coddetalletraspaso = ? and codsucursal = ?";
			$stmt = $this->dbh->prepare($sql);
			$stmt->execute(array(decrypt($_GET["coddetalletraspaso"]),decrypt($_GET["codsucursal"])));
			$num = $stmt->rowCount();

			if($row = $stmt->fetch(PDO::FETCH_ASSOC))
			{
				$p[] = $row;
			}
			$codproducto = $row['codproducto'];
			$cantidadbd = $row['cantidad'];
			$precioventabd = $row['precioventa'];
			$ivaproductobd = $row['ivaproducto'];
			$descproductobd = $row['descproducto'];

######################### DATOS DE SUCURSAL QUE ENVIA TRASPASO #######################			
			############ OBTENGO LA EXISTENCIA DE PRODUCTO EN ALMACEN #############
			$sql2 = "SELECT existencia FROM productos WHERE codproducto = ? AND codsucursal = ?";
			$stmt = $this->dbh->prepare($sql2);
			$stmt->execute(array($codproducto,decrypt($_GET["codsucursal"])));
			$num = $stmt->rowCount();

			if($row = $stmt->fetch(PDO::FETCH_ASSOC))
			{
				$p[] = $row;
			}
			$existenciabd = $row['existencia'];

			############ ACTUALIZAMOS LA EXISTENCIA DE PRODUCTO EN ALMACEN #############
			$sql = "UPDATE productos SET "
			." existencia = ? "
			." WHERE "
			." codproducto = ? AND codsucursal = ?;
			";
			$stmt = $this->dbh->prepare($sql);
			$stmt->bindParam(1, $existencia);
			$stmt->bindParam(2, $codproducto);
			$stmt->bindParam(3, $codsucursal);

			$existencia = limpiar($existenciabd+$cantidadbd);
			$codsucursal = limpiar(decrypt($_GET["codsucursal"]));
			$stmt->execute();

		    ########## REGISTRAMOS LOS DATOS DEL PRODUCTO ELIMINADO EN KARDEX ##########
			$query = "INSERT INTO kardex values (null, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?); ";
			$stmt = $this->dbh->prepare($query);
			$stmt->bindParam(1, $codtraspaso);
			$stmt->bindParam(2, $recibe);
			$stmt->bindParam(3, $codproducto);
			$stmt->bindParam(4, $movimiento);
			$stmt->bindParam(5, $entradas);
			$stmt->bindParam(6, $salidas);
			$stmt->bindParam(7, $devolucion);
			$stmt->bindParam(8, $stockactual);
			$stmt->bindParam(9, $ivaproducto);
			$stmt->bindParam(10, $descproducto);
			$stmt->bindParam(11, $precio);
			$stmt->bindParam(12, $documento);
			$stmt->bindParam(13, $fechakardex);		
			$stmt->bindParam(14, $codsucursal);

			$codtraspaso = limpiar(decrypt($_GET["codtraspaso"]));
			$recibe = limpiar(decrypt($_GET["recibe"]));
			$movimiento = limpiar("DEVOLUCION");
			$entradas= limpiar("0");
			$salidas = limpiar("0");
			$devolucion = limpiar($cantidadbd);
			$stockactual = limpiar($existenciabd+$cantidadbd);
			$precio = limpiar($precioventabd);
			$ivaproducto = limpiar($ivaproductobd);
			$descproducto = limpiar($descproductobd);
			$documento = limpiar("DEVOLUCION TRASPASO: ".decrypt($_GET["codtraspaso"]));
			$fechakardex = limpiar(date("Y-m-d"));
			$codsucursal = limpiar(decrypt($_GET["codsucursal"]));
			$stmt->execute();

######################### DATOS DE SUCURSAL QUE ENVIA TRASPASO #######################			


######################### DATOS DE SUCURSAL QUE RECIBE TRASPASO #######################			
			############ OBTENGO LA EXISTENCIA DE PRODUCTO EN ALMACEN #############
			$sql2 = "SELECT existencia FROM productos WHERE codproducto = ? AND codsucursal = ?";
			$stmt = $this->dbh->prepare($sql2);
			$stmt->execute(array($codproducto,$recibebd));
			$num = $stmt->rowCount();

			if($row = $stmt->fetch(PDO::FETCH_ASSOC))
			{
				$p[] = $row;
			}
			$existenciarecibebd = $row['existencia'];

			############ ACTUALIZAMOS LA EXISTENCIA DE PRODUCTO EN ALMACEN #############
			$sql = "UPDATE productos SET "
			." existencia = ? "
			." WHERE "
			." codproducto = ? AND codsucursal = ?;
			";
			$stmt = $this->dbh->prepare($sql);
			$stmt->bindParam(1, $existencia);
			$stmt->bindParam(2, $codproducto);
			$stmt->bindParam(3, $recibebd);

			$existencia = limpiar($existenciarecibebd-$cantidadbd);
			$stmt->execute();

		    ########## REGISTRAMOS LOS DATOS DEL PRODUCTO ELIMINADO EN KARDEX ##########
			$query = "INSERT INTO kardex values (null, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?); ";
			$stmt = $this->dbh->prepare($query);
			$stmt->bindParam(1, $codtraspaso);
			$stmt->bindParam(2, $codsucursal);
			$stmt->bindParam(3, $codproducto);
			$stmt->bindParam(4, $movimiento);
			$stmt->bindParam(5, $entradas);
			$stmt->bindParam(6, $salidas);
			$stmt->bindParam(7, $devolucion);
			$stmt->bindParam(8, $stockactual);
			$stmt->bindParam(9, $ivaproducto);
			$stmt->bindParam(10, $descproducto);
			$stmt->bindParam(11, $precio);
			$stmt->bindParam(12, $documento);
			$stmt->bindParam(13, $fechakardex);		
			$stmt->bindParam(14, $recibe);

			$codtraspaso = limpiar(decrypt($_GET["codtraspaso"]));			
			$codsucursal = limpiar(decrypt($_GET["codsucursal"]));
			$movimiento = limpiar("DEVOLUCION");
			$entradas= limpiar("0");
			$salidas = limpiar("0");
			$devolucion = limpiar($cantidadbd);
			$stockactual = limpiar($existenciarecibebd-$cantidadbd);
			$precio = limpiar($precioventabd);
			$ivaproducto = limpiar($ivaproductobd);
			$descproducto = limpiar($descproductobd);
			$documento = limpiar("DEVOLUCION TRASPASO: ".decrypt($_GET["codtraspaso"]));
			$fechakardex = limpiar(date("Y-m-d"));
			$recibe = limpiar($recibebd);
			$stmt->execute();

######################### DATOS DE SUCURSAL QUE RECIBE TRASPASO #######################			

			$sql = "DELETE FROM detallestraspasos WHERE coddetalletraspaso = ? AND codsucursal = ?";
			$stmt = $this->dbh->prepare($sql);
			$stmt->bindParam(1,$coddetalletraspaso);
			$stmt->bindParam(2,$codsucursal);
			$coddetalletraspaso = decrypt($_GET["coddetalletraspaso"]);
			$codsucursal = decrypt($_GET["codsucursal"]);
			$stmt->execute();

		    ############ CONSULTO LOS TOTALES DE TRASPASO ##############
		    $sql2 = "SELECT iva, descuento FROM traspasos WHERE codtraspaso = ? AND codsucursal = ?";
		    $stmt = $this->dbh->prepare($sql2);
		    $stmt->execute(array(decrypt($_GET["codtraspaso"]),decrypt($_GET["codsucursal"])));
		    $num = $stmt->rowCount();

			if($row = $stmt->fetch(PDO::FETCH_ASSOC))
			{
				$paea[] = $row;
			}
			$iva = $paea[0]["iva"]/100;
		    $descuento = $paea[0]["descuento"]/100;

            ############ SUMO LOS IMPORTE DE PRODUCTOS CON IVA ##############
			$sql3 = "SELECT SUM(valorneto) AS valorneto, SUM(valorneto2) AS valorneto2 FROM detallestraspasos WHERE codtraspaso = '".limpiar(decrypt($_GET["codtraspaso"]))."' AND codsucursal = '".limpiar(decrypt($_GET["codsucursal"]))."' AND ivaproducto = 'SI'";
			foreach ($this->dbh->query($sql3) as $row3)
			{
				$this->p[] = $row3;
			}
			$subtotalivasi = ($row3['valorneto']== "" ? "0.00" : $row3['valorneto']);
			$subtotalivasi2 = ($row3['valorneto2']== "" ? "0.00" : $row3['valorneto2']);

		    ############ SUMO LOS IMPORTE DE PRODUCTOS SIN IVA ##############
			$sql4 = "SELECT SUM(valorneto) AS valorneto, SUM(valorneto2) AS valorneto2 FROM detallestraspasos WHERE codtraspaso = '".limpiar(decrypt($_GET["codtraspaso"]))."' AND codsucursal = '".limpiar(decrypt($_GET["codsucursal"]))."' AND ivaproducto = 'NO'";
			foreach ($this->dbh->query($sql4) as $row4)
			{
				$this->p[] = $row4;
			}
			$subtotalivano = ($row4['valorneto']== "" ? "0.00" : $row4['valorneto']);
			$subtotalivano2 = ($row4['valorneto2']== "" ? "0.00" : $row4['valorneto2']);

            ############ ACTUALIZO LOS TOTALES EN EL TRASPASO ##############
			$sql = " UPDATE traspasos SET "
			." subtotalivasi = ?, "
			." subtotalivano = ?, "
			." totaliva = ?, "
			." totaldescuento = ?, "
			." totalpago = ?, "
			." totalpago2= ? "
			." WHERE "
			." codtraspaso = ? AND codsucursal = ?;
			";
			$stmt = $this->dbh->prepare($sql);
			$stmt->bindParam(1, $subtotalivasi);
			$stmt->bindParam(2, $subtotalivano);
			$stmt->bindParam(3, $totaliva);
			$stmt->bindParam(4, $totaldescuento);
			$stmt->bindParam(5, $totalpago);
			$stmt->bindParam(6, $totalpago2);
			$stmt->bindParam(7, $codtraspaso);
			$stmt->bindParam(8, $codsucursal);

			$totaliva= number_format($subtotalivasi*$iva, 2, '.', '');
		    $total= number_format($subtotalivasi+$subtotalivano+$totaliva, 2, '.', '');
		    $totaldescuento= number_format($total*$descuento, 2, '.', '');
		    $totalpago= number_format($total-$totaldescuento, 2, '.', '');
			$totalpago2 = number_format($subtotalivasi+$subtotalivano, 2, '.', '');
			$codtraspaso = limpiar(decrypt($_GET["codtraspaso"]));
			$codsucursal = limpiar(decrypt($_GET["codsucursal"]));
			$stmt->execute();

			echo "1";
			exit;

		} else {
		   
			echo "2";
			exit;
		  } 
			
		} else {
		
		echo "3";
		exit;
	 }	
}
######################### FUNCION ELIMINAR DETALLES TRASPASOS #########################

########################## FUNCION ELIMINAR TRASPASOS #############################
	public function EliminarTraspasos()
	{
	self::SetNames();
	if ($_SESSION["acceso"]=="administradorS") {

        ############ CONSULTO DATOS DE TRASPASO ##############
		$sql = "SELECT * FROM traspasos WHERE codtraspaso = '".limpiar(decrypt($_GET["codtraspaso"]))."' AND codsucursal = '".limpiar(decrypt($_GET["codsucursal"]))."'";
		foreach ($this->dbh->query($sql) as $row)
		{
			$this->p[] = $row;
		}
		$recibebd = $row['recibe'];
		$totalpagobd = $row['totalpago'];

        $sql = "SELECT * FROM detallestraspasos WHERE codtraspaso = '".limpiar(decrypt($_GET["codtraspaso"]))."' AND codsucursal = '".limpiar(decrypt($_GET["codsucursal"]))."'";

	foreach ($this->dbh->query($sql) as $row)
		{
			$this->p[] = $row;

			$codproducto = $row['codproducto'];
			$cantidadbd = $row['cantidad'];
			$precioventabd = $row['precioventa'];
			$ivaproductobd = $row['ivaproducto'];
			$descproductobd = $row['descproducto'];

######################### DATOS DE SUCURSAL QUE ENVIA TRASPASO #######################

            ############ OBTENGO LA EXISTENCIA DE PRODUCTO EN ALMACEN #############
			$sql2 = "SELECT existencia FROM productos WHERE codproducto = ? AND codsucursal = ?";
			$stmt = $this->dbh->prepare($sql2);
			$stmt->execute( array($codproducto,decrypt($_GET["codsucursal"])));
			$num = $stmt->rowCount();

			if($row = $stmt->fetch(PDO::FETCH_ASSOC))
			{
				$p[] = $row;
			}
			$existenciabd = $row['existencia'];

			########### ACTUALIZAMOS LA EXISTENCIA DE PRODUCTO EN ALMACEN ############
			$sql = "UPDATE productos SET "
			." existencia = ? "
			." WHERE "
			." codproducto = ? AND codsucursal = ?;
			";
			$stmt = $this->dbh->prepare($sql);
			$stmt->bindParam(1, $existencia);
			$stmt->bindParam(2, $codproducto);
			$stmt->bindParam(3, $codsucursal);

			$existencia = limpiar($existenciabd+$cantidadbd);
			$codsucursal = limpiar(decrypt($_GET["codsucursal"]));
			$stmt->execute();

		    ########### REGISTRAMOS DATOS DEL PRODUCTO ELIMINADO EN KARDEX ############
			$query = "INSERT INTO kardex values (null, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?); ";
			$stmt = $this->dbh->prepare($query);
			$stmt->bindParam(1, $codtraspaso);
			$stmt->bindParam(2, $recibe);
			$stmt->bindParam(3, $codproducto);
			$stmt->bindParam(4, $movimiento);
			$stmt->bindParam(5, $entradas);
			$stmt->bindParam(6, $salidas);
			$stmt->bindParam(7, $devolucion);
			$stmt->bindParam(8, $stockactual);
			$stmt->bindParam(9, $ivaproducto);
			$stmt->bindParam(10, $descproducto);
			$stmt->bindParam(11, $precio);
			$stmt->bindParam(12, $documento);
			$stmt->bindParam(13, $fechakardex);		
			$stmt->bindParam(14, $codsucursal);

			$codtraspaso = limpiar(decrypt($_GET["codtraspaso"]));
		    $recibe = limpiar($recibebd);
			$movimiento = limpiar("DEVOLUCION");
			$entradas= limpiar("0");
			$salidas = limpiar("0");
			$devolucion = limpiar($cantidadbd);
			$stockactual = limpiar($existenciabd+$cantidadbd);
			$precio = limpiar($precioventabd);
			$ivaproducto = limpiar($ivaproductobd);
			$descproducto = limpiar($descproductobd);
			$documento = limpiar("DEVOLUCION TRASPASO: ".decrypt($_GET["codtraspaso"]));
			$fechakardex = limpiar(date("Y-m-d"));
			$codsucursal = limpiar(decrypt($_GET["codsucursal"]));
			$stmt->execute();
######################### DATOS DE SUCURSAL QUE ENVIA TRASPASO #######################			

######################### DATOS DE SUCURSAL QUE RECIBE TRASPASO #######################			
            ############ OBTENGO LA EXISTENCIA DE PRODUCTO EN ALMACEN #############
			$sql2 = "SELECT existencia FROM productos WHERE codproducto = ? AND codsucursal = ?";
			$stmt = $this->dbh->prepare($sql2);
			$stmt->execute( array($codproducto,$recibebd));
			$num = $stmt->rowCount();

			if($row = $stmt->fetch(PDO::FETCH_ASSOC))
			{
				$p[] = $row;
			}
			$existenciarecibebd = $row['existencia'];

			########### ACTUALIZAMOS LA EXISTENCIA DE PRODUCTO EN ALMACEN ############
			$sql = "UPDATE productos SET "
			." existencia = ? "
			." WHERE "
			." codproducto = ? AND codsucursal = ?;
			";
			$stmt = $this->dbh->prepare($sql);
			$stmt->bindParam(1, $existencia);
			$stmt->bindParam(2, $codproducto);
			$stmt->bindParam(3, $recibebd);

			$existencia = limpiar($existenciarecibebd-$cantidadbd);
			$stmt->execute();

		    ########### REGISTRAMOS DATOS DEL PRODUCTO ELIMINADO EN KARDEX ############
			$query = "INSERT INTO kardex values (null, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?); ";
			$stmt = $this->dbh->prepare($query);
			$stmt->bindParam(1, $codtraspaso);
			$stmt->bindParam(2, $codsucursal);
			$stmt->bindParam(3, $codproducto);
			$stmt->bindParam(4, $movimiento);
			$stmt->bindParam(5, $entradas);
			$stmt->bindParam(6, $salidas);
			$stmt->bindParam(7, $devolucion);
			$stmt->bindParam(8, $stockactual);
			$stmt->bindParam(9, $ivaproducto);
			$stmt->bindParam(10, $descproducto);
			$stmt->bindParam(11, $precio);
			$stmt->bindParam(12, $documento);
			$stmt->bindParam(13, $fechakardex);		
			$stmt->bindParam(14, $recibe);

			$codtraspaso = limpiar(decrypt($_GET["codtraspaso"]));		
			$codsucursal = limpiar(decrypt($_GET["codsucursal"]));
			$movimiento = limpiar("DEVOLUCION");
			$entradas= limpiar("0");
			$salidas = limpiar("0");
			$devolucion = limpiar($cantidadbd);
			$stockactual = limpiar($existenciarecibebd-$cantidadbd);
			$precio = limpiar($precioventabd);
			$ivaproducto = limpiar($ivaproductobd);
			$descproducto = limpiar($descproductobd);
			$documento = limpiar("DEVOLUCION TRASPASO: ".decrypt($_GET["codtraspaso"]));
			$fechakardex = limpiar(date("Y-m-d"));
		    $recibe = limpiar($recibebd);
			$stmt->execute();

######################### DATOS DE SUCURSAL QUE RECIBE TRASPASO #######################			
		}


			$sql = "DELETE FROM traspasos WHERE codtraspaso = ? AND codsucursal = ?";
			$stmt = $this->dbh->prepare($sql);
			$stmt->bindParam(1,$codtraspaso);
			$stmt->bindParam(2,$codsucursal);
			$codtraspaso = decrypt($_GET["codtraspaso"]);
			$codsucursal = decrypt($_GET["codsucursal"]);
			$stmt->execute();

			$sql = "DELETE FROM detallestraspasos WHERE codtraspaso = ? AND codsucursal = ?";
			$stmt = $this->dbh->prepare($sql);
			$stmt->bindParam(1,$codtraspaso);
			$stmt->bindParam(2,$codsucursal);
			$codtraspaso = decrypt($_GET["codtraspaso"]);
			$codsucursal = decrypt($_GET["codsucursal"]);
			$stmt->execute();

			echo "1";
			exit;

		} else {

			echo "2";
			exit;
		}
	}
########################## FUNCION ELIMINAR TRASPASOS ###########################

####################### FUNCION BUSQUEDA TRASPASOS POR SUCURSAL ######################
	public function BuscarTraspasosxSucursal() 
	{
		self::SetNames();
		$sql ="SELECT 
	traspasos.idtraspaso, 
	traspasos.codtraspaso, 
	traspasos.envia, 
	traspasos.recibe, 
	traspasos.subtotalivasi, 
	traspasos.subtotalivano, 
	traspasos.iva, 
	traspasos.totaliva, 
	traspasos.descuento, 
	traspasos.totaldescuento,
	traspasos.totalpago, 
	traspasos.totalpago2, 
	traspasos.fechatraspaso, 
	traspasos.observaciones, 
	traspasos.codigo,
	traspasos.codsucursal,
	sucursales.documsucursal, 
	sucursales.cuitsucursal, 
	sucursales.razonsocial,
	sucursales2.documsucursal AS documsucursal2, 
	sucursales2.cuitsucursal AS cuitsucursal2, 
	sucursales2.razonsocial AS razonsocial2,
	documentos.documento,
	documentos2.documento AS documento2,
	documentos3.documento AS documento3,
	documentos4.documento AS documento4,
	SUM(detallestraspasos.cantidad) AS articulos 
	FROM (traspasos LEFT JOIN detallestraspasos ON detallestraspasos.codtraspaso = traspasos.codtraspaso) 
	LEFT JOIN sucursales ON traspasos.envia = sucursales.codsucursal
	LEFT JOIN documentos ON sucursales.documsucursal = documentos.coddocumento
	LEFT JOIN documentos AS documentos2 ON sucursales.documencargado = documentos2.coddocumento 
	LEFT JOIN sucursales AS sucursales2 ON traspasos.recibe = sucursales2.codsucursal
	LEFT JOIN documentos AS documentos3 ON sucursales.documsucursal = documentos3.coddocumento
	LEFT JOIN documentos AS documentos4 ON sucursales.documencargado = documentos4.coddocumento
	LEFT JOIN usuarios ON traspasos.codigo = usuarios.codigo
		 WHERE traspasos.codsucursal = ?GROUP BY detallestraspasos.codtraspaso";
		$stmt = $this->dbh->prepare($sql);
		$stmt->bindValue(1, trim(decrypt($_GET['codsucursal'])));
		$stmt->execute();
		$num = $stmt->rowCount();
		if($num==0)
		{
	echo "<div class='alert alert-danger'>";
	echo "<button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>";
	echo "<center><span class='fa fa-info-circle'></span> NO SE ENCONTRARON TRASPASOS PARA LA SUCURSAL SELECCIONADA</center>";
	echo "</div>";		
	exit;
		}
		else
		{
			while($row = $stmt->fetch(PDO::FETCH_ASSOC))
			{
				$this->p[]=$row;
			}
			return $this->p;
			$this->dbh=null;
		}
	}
###################### FUNCION BUSQUEDA TRASPASOS POR SUCURSAL ########################

####################### FUNCION BUSQUEDA TRASPASOS POR FECHAS #######################
	public function BuscarTraspasosxFechas() 
	{
		self::SetNames();
		$sql ="SELECT 
	traspasos.idtraspaso, 
	traspasos.codtraspaso, 
	traspasos.envia, 
	traspasos.recibe, 
	traspasos.subtotalivasi, 
	traspasos.subtotalivano, 
	traspasos.iva, 
	traspasos.totaliva, 
	traspasos.descuento, 
	traspasos.totaldescuento,
	traspasos.totalpago, 
	traspasos.totalpago2, 
	traspasos.fechatraspaso, 
	traspasos.observaciones, 
	traspasos.codigo,
	traspasos.codsucursal,
	sucursales.documsucursal, 
	sucursales.cuitsucursal, 
	sucursales.razonsocial,
	sucursales2.documsucursal AS documsucursal2, 
	sucursales2.cuitsucursal AS cuitsucursal2, 
	sucursales2.razonsocial AS razonsocial2,
	documentos.documento,
	documentos2.documento AS documento2,
	documentos3.documento AS documento3,
	documentos4.documento AS documento4,
	SUM(detallestraspasos.cantidad) AS articulos 
	FROM (traspasos LEFT JOIN detallestraspasos ON detallestraspasos.codtraspaso = traspasos.codtraspaso) 
	LEFT JOIN sucursales ON traspasos.envia = sucursales.codsucursal
	LEFT JOIN documentos ON sucursales.documsucursal = documentos.coddocumento
	LEFT JOIN documentos AS documentos2 ON sucursales.documencargado = documentos2.coddocumento 
	LEFT JOIN sucursales AS sucursales2 ON traspasos.recibe = sucursales2.codsucursal
	LEFT JOIN documentos AS documentos3 ON sucursales.documsucursal = documentos3.coddocumento
	LEFT JOIN documentos AS documentos4 ON sucursales.documencargado = documentos4.coddocumento
	LEFT JOIN usuarios ON traspasos.codigo = usuarios.codigo
		 WHERE traspasos.codsucursal = ? AND DATE_FORMAT(traspasos.fechatraspaso,'%Y-%m-%d') >= ? AND DATE_FORMAT(traspasos.fechatraspaso,'%Y-%m-%d') <= ? GROUP BY detallestraspasos.codtraspaso";
		$stmt = $this->dbh->prepare($sql);
		$stmt->bindValue(1, trim(decrypt($_GET['codsucursal'])));
		$stmt->bindValue(2, trim(date("Y-m-d",strtotime($_GET['desde']))));
		$stmt->bindValue(3, trim(date("Y-m-d",strtotime($_GET['hasta']))));
		$stmt->execute();
		$num = $stmt->rowCount();
		if($num==0)
		{
	echo "<div class='alert alert-danger'>";
	echo "<button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>";
	echo "<center><span class='fa fa-info-circle'></span> NO SE ENCONTRARON TRASPASOS PARA EL RANGO DE FECHA INGRESADO</center>";
	echo "</div>";		
	exit;
		}
		else
		{
			while($row = $stmt->fetch(PDO::FETCH_ASSOC))
			{
				$this->p[]=$row;
			}
			return $this->p;
			$this->dbh=null;
		}
	}
###################### FUNCION BUSQUEDA TRASPASOS POR FECHAS ###########################

################################## CLASE TRASPASOS ###################################
























###################################### CLASE COMPRAS ###################################

############################# FUNCION REGISTRAR COMPRAS #############################
	public function RegistrarCompras()
	{
		self::SetNames();
	if(empty($_POST["codsucursal"]) or empty($_POST["codcompra"]) or empty($_POST["fechaemision"]) or empty($_POST["fecharecepcion"]) or empty($_POST["codproveedor"]))
		{
			echo "1";
			exit;
		}

		if (limpiar(isset($_POST['fechavencecredito']))) {  

		$fechaactual = date("Y-m-d");
		$fechavence = date("Y-m-d",strtotime($_POST['fechavencecredito']));
		
	     if (strtotime($fechavence) < strtotime($fechaactual)) {
	  
	     echo "2";
		 exit;
	  
	         }
        }

		if(empty($_SESSION["CarritoCompra"]))
		{
			echo "3";
			exit;
			
		} else {

        $sql = "SELECT codcompra FROM compras WHERE codcompra = ?";
		$stmt = $this->dbh->prepare($sql);
		$stmt->execute(array($_POST['codcompra']));
		$num = $stmt->rowCount();
		if($num == 0)
		{

        $query = "INSERT INTO compras values (null, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);";
		$stmt = $this->dbh->prepare($query);
		$stmt->bindParam(1, $codcompra);
		$stmt->bindParam(2, $codproveedor);
		$stmt->bindParam(3, $subtotalivasic);
		$stmt->bindParam(4, $subtotalivanoc);
		$stmt->bindParam(5, $ivac);
		$stmt->bindParam(6, $totalivac);
		$stmt->bindParam(7, $descuentoc);
		$stmt->bindParam(8, $totaldescuentoc);
		$stmt->bindParam(9, $totalpagoc);
		$stmt->bindParam(10, $tipocompra);
		$stmt->bindParam(11, $formacompra);
		$stmt->bindParam(12, $fechavencecredito);
		$stmt->bindParam(13, $fechapagado);
		$stmt->bindParam(14, $statuscompra);
		$stmt->bindParam(15, $fechaemision);
		$stmt->bindParam(16, $fecharecepcion);
		$stmt->bindParam(17, $observaciones);
		$stmt->bindParam(18, $codigo);
		$stmt->bindParam(19, $codsucursal);
	    
		$codcompra = limpiar($_POST["codcompra"]);
		$codproveedor = limpiar($_POST["codproveedor"]);
		$subtotalivasic = limpiar($_POST["txtsubtotal"]);
		$subtotalivanoc = limpiar($_POST["txtsubtotal2"]);
		$ivac = limpiar($_POST["iva"]);
		$totalivac = limpiar($_POST["txtIva"]);
		$descuentoc = limpiar($_POST["descuento"]);
		$totaldescuentoc = limpiar($_POST["txtDescuento"]);
		$totalpagoc = limpiar($_POST["txtTotal"]);
		$tipocompra = limpiar($_POST["tipocompra"]);
		$tipocompra = limpiar($_POST["tipocompra"]);
		$formacompra = limpiar($_POST["tipocompra"]=="CONTADO" ? $_POST["formacompra"] : "CREDITO");
		$fechavencecredito = limpiar($_POST["tipocompra"]=="CREDITO" ? date("Y-m-d",strtotime($_POST['fechavencecredito'])) : "0000-00-00");
        $fechapagado = limpiar("0000-00-00");
		$statuscompra = limpiar($_POST["tipocompra"]=="CONTADO" ? "PAGADA" : "PENDIENTE");
        $fechaemision = limpiar(date("Y-m-d",strtotime($_POST['fechaemision'])));
        $fecharecepcion = limpiar(date("Y-m-d",strtotime($_POST['fecharecepcion'])));
        $observaciones = limpiar("NINGUNA");
		$codigo = limpiar($_SESSION["codigo"]);
		$codsucursal = limpiar($_POST["codsucursal"]);
		$stmt->execute();
		
		$this->dbh->beginTransaction();

		$detalle = $_SESSION["CarritoCompra"];
		for($i=0;$i<count($detalle);$i++){

        ############### VERIFICO LA EXISTENCIA DEL PRODUCTO EN ALMACEN ################
		$sql = "SELECT existencia FROM productos WHERE codproducto = '".limpiar($detalle[$i]['txtCodigo'])."' AND codsucursal = '".limpiar($_POST['codsucursal'])."'";
		foreach ($this->dbh->query($sql) as $row)
		{
			$this->p[] = $row;
		}
		$existenciabd = $row['existencia'];

		$query = "INSERT INTO detallecompras values (null, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?); ";
		$stmt = $this->dbh->prepare($query);
		$stmt->bindParam(1, $codcompra);
	    $stmt->bindParam(2, $codproducto);
	    $stmt->bindParam(3, $producto);
		$stmt->bindParam(4, $preciocomprac);
		$stmt->bindParam(5, $precioxmenorc);
		$stmt->bindParam(6, $precioxmayorc);
		$stmt->bindParam(7, $precioxpublicoc);
		$stmt->bindParam(8, $cantcompra);
		$stmt->bindParam(9, $ivaproductoc);
		$stmt->bindParam(10, $descproductoc);
		$stmt->bindParam(11, $descfactura);
		$stmt->bindParam(12, $valortotal);
		$stmt->bindParam(13, $totaldescuentoc);
		$stmt->bindParam(14, $valorneto);
		$stmt->bindParam(15, $lotec);
		$stmt->bindParam(16, $fechaelaboracionc);
		$stmt->bindParam(17, $fechaoptimoc);
		$stmt->bindParam(18, $fechamedioc);
		$stmt->bindParam(19, $fechaminimoc);
		$stmt->bindParam(20, $stockoptimoc);
		$stmt->bindParam(21, $stockmedioc);
		$stmt->bindParam(22, $stockminimoc);
		$stmt->bindParam(23, $codsucursal);
			
		$codcompra = limpiar($_POST['codcompra']);
		$codproducto = limpiar($detalle[$i]['txtCodigo']);
		$producto = limpiar($detalle[$i]['producto']);
		$preciocomprac = limpiar($detalle[$i]['precio']);
		$precioxmenorc = limpiar($detalle[$i]['precio2']);
		$precioxmayorc = limpiar($detalle[$i]['precio3']);
		$precioxpublicoc = limpiar($detalle[$i]['precio4']);
		$cantcompra = limpiar($detalle[$i]['cantidad']);
		$ivaproductoc = limpiar($detalle[$i]['ivaproducto']);
		$descproductoc = limpiar($detalle[$i]['descproducto']);
		$descfactura = limpiar($detalle[$i]['descproductofact']);
		$descuento = $detalle[$i]["descproductofact"]/100;
		$valortotal = number_format($detalle[$i]['precio']*$detalle[$i]['cantidad'], 2, '.', '');
		$totaldescuentoc = number_format($valortotal*$descuento, 2, '.', '');
	    $valorneto = number_format($valortotal-$totaldescuentoc, 2, '.', '');
		$lotec = limpiar($detalle[$i]['lote']);
		$fechaelaboracionc = limpiar($detalle[$i]['fechaelaboracion']=="" ? "0000-00-00" : date("Y-m-d",strtotime($detalle[$i]['fechaelaboracion'])));
		$fechaoptimoc = limpiar($detalle[$i]['fechaexpiracion']=="" ? "0000-00-00" : date("Y-m-d",strtotime($detalle[$i]['fechaexpiracion'])));
		$fechamedioc = limpiar($detalle[$i]['fechaexpiracion2']=="" ? "0000-00-00" : date("Y-m-d",strtotime($detalle[$i]['fechaexpiracion2'])));
		$fechaminimoc = limpiar($detalle[$i]['fechaexpiracion3']=="" ? "0000-00-00" : date("Y-m-d",strtotime($detalle[$i]['fechaexpiracion3'])));
		$stockoptimoc = limpiar($detalle[$i]['optimo']);
		$stockmedioc = limpiar($detalle[$i]['medio']);
		$stockminimoc = limpiar($detalle[$i]['minimo']);
		$codsucursal = limpiar($_POST["codsucursal"]);
		$stmt->execute();

		############# ACTUALIZAMOS LA EXISTENCIA DE PRODUCTOS COMPRADOS ###############
		$sql = "UPDATE productos set "
		      ." preciocompra = ?, "
			  ." precioxmenor = ?, "
			  ." precioxmayor = ?, "
			  ." precioxpublico = ?, "
			  ." existencia = ?, "
			  ." ivaproducto = ?, "
			  ." descproducto = ?, "
			  ." fechaelaboracion = ?, "
			  ." fechaoptimo = ?, "
			  ." fechamedio = ?, "
			  ." fechaminimo = ?, "
			  ." stockoptimo = ?, "
			  ." stockmedio = ?, "
			  ." stockminimo = ?, "
			  ." codproveedor = ?, "
			  ." lote = ? "
			  ." WHERE "
			  ." codproducto = ? AND codsucursal = ?;
			   ";
		$stmt = $this->dbh->prepare($sql);
		$stmt->bindParam(1, $preciocompra);
		$stmt->bindParam(2, $precioxmenorc);
		$stmt->bindParam(3, $precioxmayorc);
		$stmt->bindParam(4, $precioxpublicoc);
		$stmt->bindParam(5, $existencia);
		$stmt->bindParam(6, $ivaproducto);
		$stmt->bindParam(7, $descproducto);
		$stmt->bindParam(8, $fechaelaboracion);
		$stmt->bindParam(9, $fechaoptimoc);
		$stmt->bindParam(10, $fechamedioc);
		$stmt->bindParam(11, $fechaminimoc);
		$stmt->bindParam(12, $stockoptimoc);
		$stmt->bindParam(13, $stockmedioc);
		$stmt->bindParam(14, $stockminimoc);
		$stmt->bindParam(15, $codproveedor);
		$stmt->bindParam(16, $lote);
		$stmt->bindParam(17, $codproducto);
		$stmt->bindParam(18, $codsucursal);
		
		$preciocompra = limpiar($detalle[$i]['precio']);
		$precioxmenorc = limpiar($detalle[$i]['precio2']);
		$precioxmayorc = limpiar($detalle[$i]['precio3']);
		$precioxpublicoc = limpiar($detalle[$i]['precio4']);
		$existencia = limpiar($detalle[$i]['cantidad']+$existenciabd);
		$ivaproducto = limpiar($detalle[$i]['ivaproducto']);
		$descproducto = limpiar($detalle[$i]['descproducto']);
		$fechaelaboracionc = limpiar($detalle[$i]['fechaelaboracion']=="" ? "0000-00-00" : date("Y-m-d",strtotime($detalle[$i]['fechaelaboracion'])));
		$fechaoptimoc = limpiar($detalle[$i]['fechaexpiracion']=="" ? "0000-00-00" : date("Y-m-d",strtotime($detalle[$i]['fechaexpiracion'])));
		$fechamedioc = limpiar($detalle[$i]['fechaexpiracion2']=="" ? "0000-00-00" : date("Y-m-d",strtotime($detalle[$i]['fechaexpiracion2'])));
		$fechaminimoc = limpiar($detalle[$i]['fechaexpiracion3']=="" ? "0000-00-00" : date("Y-m-d",strtotime($detalle[$i]['fechaexpiracion3'])));
		$stockoptimoc = limpiar($detalle[$i]['optimo']);
		$stockmedioc = limpiar($detalle[$i]['medio']);
		$stockminimoc = limpiar($detalle[$i]['minimo']);
		$codproveedor = limpiar($_POST['codproveedor']);
		$lote = limpiar($detalle[$i]['lote']);
		$codproducto = limpiar($detalle[$i]['txtCodigo']);
		$codsucursal = limpiar($_POST['codsucursal']);
		$stmt->execute();

		############### REGISTRAMOS LOS DATOS DE PRODUCTOS EN KARDEX ###################
        $query = "INSERT INTO kardex values (null, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?); ";
		$stmt = $this->dbh->prepare($query);
		$stmt->bindParam(1, $codcompra);
		$stmt->bindParam(2, $codproveedor);
		$stmt->bindParam(3, $codproducto);
		$stmt->bindParam(4, $movimiento);
		$stmt->bindParam(5, $entradas);
		$stmt->bindParam(6, $salidas);
		$stmt->bindParam(7, $devolucion);
		$stmt->bindParam(8, $stockactual);
		$stmt->bindParam(9, $ivaproducto);
		$stmt->bindParam(10, $descproducto);
		$stmt->bindParam(11, $precio);
		$stmt->bindParam(12, $documento);
		$stmt->bindParam(13, $fechakardex);		
		$stmt->bindParam(14, $codsucursal);

		$codcompra = limpiar($_POST['codcompra']);
		$codproveedor = limpiar($_POST["codproveedor"]);
		$codproducto = limpiar($detalle[$i]['txtCodigo']);
		$movimiento = limpiar("ENTRADAS");
		$entradas= limpiar($detalle[$i]['cantidad']);
		$salidas = limpiar("0");
		$devolucion = limpiar("0");
		$stockactual = limpiar($detalle[$i]['cantidad']+$existenciabd);
		$precio = limpiar($detalle[$i]["precio"]);
		$ivaproducto = limpiar($detalle[$i]['ivaproducto']);
		$descproducto = limpiar($detalle[$i]['descproducto']);
		$documento = limpiar("COMPRA: ".$_POST['codcompra']);
		$fechakardex = limpiar(date("Y-m-d"));
		$codsucursal = limpiar($_POST["codsucursal"]);
		$stmt->execute();
      }
		####################### DESTRUYO LA VARIABLE DE SESSION #####################
	    unset($_SESSION["CarritoCompra"]);
        $this->dbh->commit();

		
echo "<span class='fa fa-check-square-o'></span> LA COMPRA DE PRODUCTOS HA SIDO REGISTRADA EXITOSAMENTE <a href='reportepdf?codcompra=".encrypt($codcompra)."&codsucursal=".encrypt($codsucursal)."&tipo=".encrypt("FACTURACOMPRA")."' class='on-default' data-placement='left' data-toggle='tooltip' data-original-title='Imprimir Documento' target='_black' rel='noopener noreferrer'><font color='black'><strong>IMPRIMIR REPORTE</strong></font color></a></div>";

echo "<script>window.open('reportepdf?codcompra=".encrypt($codcompra)."&codsucursal=".encrypt($codsucursal)."&tipo=".encrypt("FACTURACOMPRA")."', '_blank');</script>";
	exit;
		}
		else
		{
			echo "4";
			exit;
		}
	}
}
############################ FUNCION REGISTRAR COMPRAS ##########################

######################### FUNCION LISTAR COMPRAS ################################
public function ListarCompras()
{
	self::SetNames();

	if ($_SESSION['acceso'] == "administradorG") {

	$sql = "SELECT 
	compras.codcompra, 
	compras.codproveedor, 
	compras.subtotalivasic, 
	compras.subtotalivanoc, 
	compras.ivac, 
	compras.totalivac, 
	compras.descuentoc, 
	compras.totaldescuentoc, 
	compras.totalpagoc, 
	compras.statuscompra, 
	compras.fechavencecredito, 
	compras.fechapagado,
	compras.fecharecepcion, 
	compras.fechaemision, 
	compras.observaciones,
	compras.codsucursal,
	sucursales.documsucursal, 
	sucursales.cuitsucursal, 
	sucursales.razonsocial,
	sucursales.documencargado,
	sucursales.dniencargado,
	sucursales.nomencargado,
	proveedores.documproveedor, 
	proveedores.documproveedor, 
	proveedores.cuitproveedor, 
	proveedores.nomproveedor, 
	documentos.documento,
	documentos2.documento AS documento2, 
	documentos3.documento AS documento3, 
	SUM(detallecompras.cantcompra) AS articulos 
	FROM (compras LEFT JOIN detallecompras ON detallecompras.codcompra = compras.codcompra) 
	LEFT JOIN sucursales ON compras.codsucursal = sucursales.codsucursal  
	LEFT JOIN documentos ON sucursales.documsucursal = documentos.coddocumento
	LEFT JOIN documentos AS documentos2 ON sucursales.documencargado = documentos2.coddocumento 
	LEFT JOIN proveedores ON compras.codproveedor = proveedores.codproveedor 
	LEFT JOIN documentos AS documentos3 ON proveedores.documproveedor = documentos3.coddocumento
	LEFT JOIN usuarios ON compras.codigo = usuarios.codigo WHERE compras.statuscompra = 'PAGADA' GROUP BY detallecompras.codcompra ORDER BY compras.idcompra DESC";
	foreach ($this->dbh->query($sql) as $row)
	{
		$this->p[] = $row;
	}
	return $this->p;
	$this->dbh=null;

     } else {

   $sql = "SELECT 
	compras.codcompra, 
	compras.codproveedor, 
	compras.subtotalivasic, 
	compras.subtotalivanoc, 
	compras.ivac, 
	compras.totalivac, 
	compras.descuentoc, 
	compras.totaldescuentoc, 
	compras.totalpagoc, 
	compras.statuscompra, 
	compras.fechavencecredito, 
	compras.fechapagado,
	compras.fecharecepcion, 
	compras.fechaemision, 
	compras.observaciones,
	compras.codsucursal, 
	sucursales.documsucursal, 
	sucursales.cuitsucursal, 
	sucursales.razonsocial,
	sucursales.documencargado,
	sucursales.dniencargado,
	sucursales.nomencargado,
	proveedores.documproveedor, 
	proveedores.documproveedor, 
	proveedores.cuitproveedor, 
	proveedores.nomproveedor, 
	documentos.documento,
	documentos2.documento AS documento2, 
	documentos3.documento AS documento3, 
	SUM(detallecompras.cantcompra) AS articulos 
	FROM (compras LEFT JOIN detallecompras ON detallecompras.codcompra = compras.codcompra) 
	LEFT JOIN sucursales ON compras.codsucursal = sucursales.codsucursal  
	LEFT JOIN documentos ON sucursales.documsucursal = documentos.coddocumento
	LEFT JOIN documentos AS documentos2 ON sucursales.documencargado = documentos2.coddocumento 
	LEFT JOIN proveedores ON compras.codproveedor = proveedores.codproveedor 
	LEFT JOIN documentos AS documentos3 ON proveedores.documproveedor = documentos3.coddocumento
	LEFT JOIN usuarios ON compras.codigo = usuarios.codigo WHERE compras.codsucursal = '".limpiar($_SESSION["codsucursal"])."' AND compras.statuscompra = 'PAGADA' GROUP BY detallecompras.codcompra ORDER BY compras.idcompra DESC";
	foreach ($this->dbh->query($sql) as $row)
	{
		$this->p[] = $row;
	}
	return $this->p;
	$this->dbh=null;

     }
}
################################## FUNCION LISTAR COMPRAS ############################

########################### FUNCION LISTAR CUENTAS POR PAGAR #######################
public function ListarCuentasxPagar()
{
	self::SetNames();

	if ($_SESSION['acceso'] == "administradorG") {

	$sql = "SELECT 
	compras.codcompra, 
	compras.codproveedor, 
	compras.subtotalivasic, 
	compras.subtotalivanoc, 
	compras.ivac, 
	compras.totalivac, 
	compras.descuentoc, 
	compras.totaldescuentoc, 
	compras.totalpagoc, 
	compras.statuscompra, 
	compras.fechavencecredito, 
	compras.fechapagado,
	compras.fecharecepcion, 
	compras.fechaemision, 
	compras.observaciones,
	compras.codsucursal,
	sucursales.documsucursal, 
	sucursales.cuitsucursal, 
	sucursales.razonsocial,
	sucursales.documencargado,
	sucursales.dniencargado,
	sucursales.nomencargado,
	proveedores.documproveedor, 
	proveedores.cuitproveedor, 
	proveedores.nomproveedor, 
	documentos.documento,
	documentos2.documento AS documento2, 
	documentos3.documento AS documento3, 
	SUM(abonoscreditoscompras.montoabono) AS abonototal 
	FROM (compras LEFT JOIN abonoscreditoscompras ON compras.codcompra = abonoscreditoscompras.codcompra)
	LEFT JOIN sucursales ON compras.codsucursal = sucursales.codsucursal  
	LEFT JOIN documentos ON sucursales.documsucursal = documentos.coddocumento
	LEFT JOIN documentos AS documentos2 ON sucursales.documencargado = documentos2.coddocumento 
	LEFT JOIN proveedores ON compras.codproveedor = proveedores.codproveedor 
	LEFT JOIN documentos AS documentos3 ON proveedores.documproveedor = documentos3.coddocumento
	LEFT JOIN usuarios ON compras.codigo = usuarios.codigo WHERE compras.statuscompra = 'PENDIENTE' GROUP BY compras.codcompra ORDER BY compras.idcompra DESC";
	foreach ($this->dbh->query($sql) as $row)
	{
		$this->p[] = $row;
	}
	return $this->p;
	$this->dbh=null;

     } else {

   $sql = "SELECT 
	compras.codcompra, 
	compras.codproveedor, 
	compras.subtotalivasic, 
	compras.subtotalivanoc, 
	compras.ivac, 
	compras.totalivac, 
	compras.descuentoc, 
	compras.totaldescuentoc, 
	compras.totalpagoc, 
	compras.statuscompra, 
	compras.fechavencecredito, 
	compras.fechapagado,
	compras.fecharecepcion, 
	compras.fechaemision, 
	compras.observaciones,
	compras.codsucursal,
	sucursales.documsucursal, 
	sucursales.cuitsucursal, 
	sucursales.razonsocial,
	sucursales.documencargado,
	sucursales.dniencargado,
	sucursales.nomencargado,
	proveedores.documproveedor, 
	proveedores.cuitproveedor, 
	proveedores.nomproveedor, 
	documentos.documento,
	documentos2.documento AS documento2, 
	documentos3.documento AS documento3, 
	SUM(abonoscreditoscompras.montoabono) AS abonototal 
	FROM (compras LEFT JOIN abonoscreditoscompras ON compras.codcompra = abonoscreditoscompras.codcompra)
	LEFT JOIN sucursales ON compras.codsucursal = sucursales.codsucursal  
	LEFT JOIN documentos ON sucursales.documsucursal = documentos.coddocumento
	LEFT JOIN documentos AS documentos2 ON sucursales.documencargado = documentos2.coddocumento 
	LEFT JOIN proveedores ON compras.codproveedor = proveedores.codproveedor 
	LEFT JOIN documentos AS documentos3 ON proveedores.documproveedor = documentos3.coddocumento
	LEFT JOIN usuarios ON compras.codigo = usuarios.codigo WHERE compras.codsucursal = '".limpiar($_SESSION["codsucursal"])."' AND compras.statuscompra = 'PENDIENTE' GROUP BY compras.codcompra ORDER BY compras.idcompra DESC";
	foreach ($this->dbh->query($sql) as $row)
	{
		$this->p[] = $row;
	}
	return $this->p;
	$this->dbh=null;

     }
}
######################### FUNCION LISTAR CUENTAS POR PAGAR ############################

############################ FUNCION PARA PAGAR COMPRAS ############################
public function RegistrarPagoCompra()
	{
		self::SetNames();

		if(empty($_POST["codproveedor"]) or empty($_POST["codcompra"]) or empty($_POST["montoabono"]))
		{
			echo "1";
			exit;
		} 
		else if($_POST["montoabono"] > $_POST["totaldebe"])
		{
			echo "2";
			exit;

		} else {

		$query = "INSERT INTO abonoscreditoscompras values (null, ?, ?, ?, ?, ?); ";
		$stmt = $this->dbh->prepare($query);
		$stmt->bindParam(1, $codcompra);
		$stmt->bindParam(2, $codproveedor);
		$stmt->bindParam(3, $montoabono);
		$stmt->bindParam(4, $fechaabono);
		$stmt->bindParam(5, $codsucursal);

		$codcompra = limpiar($_POST["codcompra"]);
		$codproveedor = limpiar(decrypt($_POST["codproveedor"]));
		$montoabono = limpiar($_POST["montoabono"]);
		$fechaabono = limpiar(date("Y-m-d h:i:s"));
		$codsucursal = limpiar(decrypt($_POST["codsucursal"]));
		$stmt->execute();

	    ############## ACTUALIZAMOS EL STATUS DE LA FACTURA ##################
		if($_POST["montoabono"] == $_POST["totaldebe"]) {

			$sql = "UPDATE compras set "
			." statuscompra = ?, "
			." fechapagado = ? "
			." WHERE "
			." codcompra = ? and codsucursal = ?;
			";
			$stmt = $this->dbh->prepare($sql);
			$stmt->bindParam(1, $statuscompra);
			$stmt->bindParam(2, $fechapagado);
			$stmt->bindParam(3, $codcompra);
			$stmt->bindParam(4, $codsucursal);

			$statuscompra = limpiar("PAGADA");
			$fechapagado = limpiar(date("Y-m-d"));
			$codcompra = limpiar($_POST["codcompra"]);
			$codsucursal = limpiar(decrypt($_POST["codsucursal"]));
			$stmt->execute();
		}
    ############## ACTUALIZAMOS EL STATUS DE LA FACTURA ##################

		
echo "<span class='fa fa-check-square-o'></span> EL ABONO AL CR&Eacute;DITO DE COMPRA HA SIDO REGISTRADO EXITOSAMENTE</div>";
	exit;
   }
}
########################## FUNCION PARA PAGAR COMPRAS ###############################

########################### FUNCION VER DETALLES COMPRAS #######################
public function VerDetallesAbonosCompras()
{
	self::SetNames();
	$sql = "SELECT * FROM abonoscreditoscompras INNER JOIN compras ON abonoscreditoscompras.codcompra = compras.codcompra  WHERE abonoscreditoscompras.codcompra = ? AND abonoscreditoscompras.codsucursal = ?";	
	$stmt = $this->dbh->prepare($sql);
	$stmt->bindValue(1, trim(decrypt($_GET["codcompra"])));
	$stmt->bindValue(2, trim(decrypt($_GET["codsucursal"])));
	$stmt->execute();
	$num = $stmt->rowCount();
	if($num==0)
	{
		echo "";
	}
	else
	{
		while($row = $stmt->fetch(PDO::FETCH_ASSOC))
			{
				$this->p[]=$row;
			}
			return $this->p;
			$this->dbh=null;
		}
	}
########################## FUNCION VER DETALLES COMPRAS ###########################

############################ FUNCION ID COMPRAS #################################
	public function ComprasPorId()
	{
		self::SetNames();
		$sql = " SELECT 
		compras.idcompra, 
		compras.codcompra,
		compras.codproveedor, 
		compras.subtotalivasic,
		compras.subtotalivanoc, 
		compras.ivac,
		compras.totalivac, 
		compras.descuentoc,
		compras.totaldescuentoc, 
		compras.totalpagoc, 
		compras.tipocompra,
		compras.formacompra,
		compras.fechavencecredito,
	    compras.fechapagado,
		compras.statuscompra,
		compras.fechaemision,
		compras.fecharecepcion,
	    compras.observaciones,
		compras.codigo,
		compras.codsucursal,
		sucursales.documsucursal,
		sucursales.cuitsucursal,
		sucursales.razonsocial,
		sucursales.direcsucursal,
		sucursales.correosucursal,
		sucursales.tlfsucursal,
		sucursales.documencargado,
		sucursales.dniencargado,
		sucursales.nomencargado,
		sucursales.tlfencargado,
		sucursales.llevacontabilidad,
		proveedores.documproveedor,
		proveedores.cuitproveedor, 
		proveedores.nomproveedor, 
		proveedores.tlfproveedor, 
		proveedores.id_provincia, 
		proveedores.id_departamento, 
		proveedores.direcproveedor, 
		proveedores.emailproveedor,
		proveedores.vendedor,
		proveedores.tlfvendedor,
	    documentos.documento,
	    documentos2.documento AS documento2, 
	    documentos3.documento AS documento3, 
		mediospagos.mediopago,
		usuarios.dni, 
		usuarios.nombres,
		provincias.provincia,
		departamentos.departamento,
		provincias2.provincia AS provincia2,
		departamentos2.departamento AS departamento2,
	    SUM(abonoscreditoscompras.montoabono) AS abonototal 
	    FROM (compras LEFT JOIN abonoscreditoscompras ON compras.codcompra = abonoscreditoscompras.codcompra)
		INNER JOIN sucursales ON compras.codsucursal = sucursales.codsucursal
		LEFT JOIN documentos ON sucursales.documsucursal = documentos.coddocumento
		LEFT JOIN documentos AS documentos2 ON sucursales.documencargado = documentos2.coddocumento 
		LEFT JOIN provincias ON sucursales.id_provincia = provincias.id_provincia 
		LEFT JOIN departamentos ON sucursales.id_departamento = departamentos.id_departamento 
		LEFT JOIN proveedores ON compras.codproveedor = proveedores.codproveedor 
		LEFT JOIN documentos AS documentos3 ON proveedores.documproveedor = documentos3.coddocumento
		LEFT JOIN provincias AS provincias2 ON proveedores.id_provincia = provincias2.id_provincia 
		LEFT JOIN departamentos AS departamentos2 ON proveedores.id_departamento = departamentos2.id_departamento 
		LEFT JOIN mediospagos ON compras.formacompra = mediospagos.codmediopago
		LEFT JOIN usuarios ON compras.codigo = usuarios.codigo
		WHERE compras.codcompra = ? AND compras.codsucursal = ?";
		$stmt = $this->dbh->prepare($sql);
		$stmt->execute(array(decrypt($_GET["codcompra"]),decrypt($_GET["codsucursal"])));
		$num = $stmt->rowCount();
		if($num==0)
		{
			echo "";
		}
		else
		{
			if($row = $stmt->fetch(PDO::FETCH_ASSOC))
			{
				$this->p[] = $row;
			}
			return $this->p;
			$this->dbh=null;
		}
	}
############################ FUNCION ID COMPRAS #################################
	
############################ FUNCION VER DETALLES COMPRAS ############################
public function VerDetallesCompras()
	{
		self::SetNames();
		$sql = "SELECT
		detallecompras.coddetallecompra,
		detallecompras.codcompra,
		detallecompras.codproducto,
		detallecompras.preciocomprac,
		detallecompras.precioxmenorc,
		detallecompras.precioxmayorc,
		detallecompras.precioxpublicoc,
		detallecompras.cantcompra,
		detallecompras.ivaproductoc,
		detallecompras.descproductoc,
		detallecompras.descfactura,
		detallecompras.valortotal, 
		detallecompras.totaldescuentoc,
		detallecompras.valorneto,
		detallecompras.lotec,
		detallecompras.fechaelaboracionc,
		detallecompras.fechaoptimoc,
		detallecompras.fechamedioc,
		detallecompras.fechaminimoc,
		detallecompras.stockoptimoc,
		detallecompras.stockmedioc,
		detallecompras.stockminimoc,
		detallecompras.codsucursal,
		productos.producto,
		marcas.nommarca,
		modelos.nommodelo
		FROM detallecompras INNER JOIN productos ON detallecompras.codproducto = productos.codproducto 
		INNER JOIN marcas ON productos.codmarca = marcas.codmarca
		LEFT JOIN modelos ON productos.codmodelo = modelos.codmodelo 
		WHERE detallecompras.codcompra = ? AND detallecompras.codsucursal = ? GROUP BY productos.codproducto";
		$stmt = $this->dbh->prepare($sql);
		$stmt->execute(array(decrypt($_GET["codcompra"]),decrypt($_GET["codsucursal"])));
		$num = $stmt->rowCount();
		
			while($row = $stmt->fetch(PDO::FETCH_ASSOC))
			{
				$this->p[]=$row;
			}
			return $this->p;
			$this->dbh=null;
		}
############################ FUNCION VER DETALLES COMPRAS ##############################

############################## FUNCION ACTUALIZAR COMPRAS #############################
	public function ActualizarCompras()
	{
		self::SetNames();
		if(empty($_POST["codsucursal"]) or empty($_POST["codcompra"]) or empty($_POST["fechaemision"]) or empty($_POST["fecharecepcion"]) or empty($_POST["codproveedor"]))
		{
			echo "1";
			exit;
		}

		if (limpiar(isset($_POST['fechavencecredito']))) {  

		$fechaactual = date("Y-m-d");
		$fechavence = date("Y-m-d",strtotime($_POST['fechavencecredito']));
		
	     if (strtotime($fechavence) < strtotime($fechaactual)) {
	  
	     echo "2";
		 exit;
	  
	         }
        }

		for($i=0;$i<count($_POST['coddetallecompra']);$i++){  //recorro el array
	        if (!empty($_POST['coddetallecompra'][$i])) {

		       if($_POST['cantcompra'][$i]==0){

			      echo "3";
			      exit();

		        }
	        }
        }

        $this->dbh->beginTransaction();

        for($i=0;$i<count($_POST['coddetallecompra']);$i++){  //recorro el array
	         if (!empty($_POST['coddetallecompra'][$i])) {

	$sql = "SELECT cantcompra FROM detallecompras WHERE coddetallecompra = '".limpiar($_POST['coddetallecompra'][$i])."' AND codcompra = '".limpiar($_POST["codcompra"])."' AND codsucursal = '".limpiar($_POST["codsucursal"])."'";
		foreach ($this->dbh->query($sql) as $row)
		{
			$this->p[] = $row;
		}
		
		$cantidadbd = $row['cantcompra'];

		if($cantidadbd != $_POST['cantcompra'][$i]){

			$sql = "SELECT existencia FROM productos WHERE codproducto = '".limpiar($_POST['codproducto'][$i])."' AND codsucursal = '".limpiar($_POST["codsucursal"])."'";
		    foreach ($this->dbh->query($sql) as $row)
		    {
			$this->p[] = $row;
		    }
		    $existenciabd = $row['existencia'];
		    $cantcompra = $_POST["cantcompra"][$i];
		    $cantidadcomprabd = $_POST["cantidadcomprabd"][$i];
		    $totalcompra = $cantcompra-$cantidadcomprabd;

			$query = "UPDATE detallecompras set"
			." cantcompra = ?, "
			." valortotal = ?, "
			." totaldescuentoc = ?, "
			." valorneto = ? "
			." WHERE "
			." coddetallecompra = ? AND codcompra = ? AND codsucursal = ?;
			";
			$stmt = $this->dbh->prepare($query);
			$stmt->bindParam(1, $cantcompra);
			$stmt->bindParam(2, $valortotal);
			$stmt->bindParam(3, $totaldescuento);
			$stmt->bindParam(4, $valorneto);
			$stmt->bindParam(5, $coddetallecompra);
			$stmt->bindParam(6, $codcompra);
			$stmt->bindParam(7, $codsucursal);

			$cantcompra = limpiar($_POST['cantcompra'][$i]);
			$preciocompra = limpiar($_POST['preciocompra'][$i]);
			$ivaproducto = limpiar($_POST['ivaproducto'][$i]);
			$descuento = limpiar($_POST['descfactura'][$i]/100);
			$valortotal = number_format($_POST['preciocompra'][$i] * $_POST['cantcompra'][$i], 2, '.', '');
			$totaldescuento = number_format($valortotal * $descuento, 2, '.', '');
			$valorneto = number_format($valortotal - $totaldescuento, 2, '.', '');
			$coddetallecompra = limpiar($_POST['coddetallecompra'][$i]);
			$codcompra = limpiar($_POST["codcompra"]);
			$codsucursal = limpiar($_POST["codsucursal"]);
			$stmt->execute();

		############ ACTUALIZAMOS EXISTENCIA DEL PRODUCTO EN ALMACEN ################
		$sql2 = " UPDATE productos set "
			  ." existencia = ? "
			  ." WHERE "
			  ." codproducto = '".limpiar($_POST["codproducto"][$i])."' AND codsucursal = '".limpiar($_POST["codsucursal"])."';
			  ";
			  $stmt = $this->dbh->prepare($sql2);
			  $stmt->bindParam(1, $existencia);
			  $existencia = $existenciabd+$totalcompra;
			  $stmt->execute();

		############## ACTUALIZAMOS LOS DATOS DEL PRODUCTO EN KARDEX ###################
		$sql3 = " UPDATE kardex set "
		      ." entradas = ?, "
		      ." stockactual = ? "
			  ." WHERE "
			  ." codproceso = '".limpiar($_POST["codcompra"])."' and codproducto = '".limpiar($_POST["codproducto"][$i])."'AND codsucursal = '".limpiar($_POST["codsucursal"])."';
			   ";
		$stmt = $this->dbh->prepare($sql3);
		$stmt->bindParam(1, $entradas);
		$stmt->bindParam(2, $existencia);
		
		$entradas = limpiar($_POST["cantcompra"][$i]);
		$stmt->execute();

			} else {

               echo "";

		       }
	        }
        }

        $this->dbh->commit();

            ############ SUMO LOS IMPORTE DE PRODUCTOS CON IVA ##############
			$sql3 = "SELECT SUM(valorneto) AS valorneto FROM detallecompras WHERE codcompra = '".limpiar($_POST["codcompra"])."' AND codsucursal = '".limpiar($_POST["codsucursal"])."' AND ivaproductoc = 'SI'";
			foreach ($this->dbh->query($sql3) as $row3)
			{
				$this->p[] = $row3;
			}
			$subtotalivasic = ($row3['valorneto']== "" ? "0.00" : $row3['valorneto']);

		    ############ SUMO LOS IMPORTE DE PRODUCTOS SIN IVA ##############
			$sql4 = "SELECT SUM(valorneto) AS valorneto FROM detallecompras WHERE codcompra = '".limpiar($_POST["codcompra"])."' AND codsucursal = '".limpiar($_POST["codsucursal"])."' AND ivaproductoc = 'NO'";
			foreach ($this->dbh->query($sql4) as $row4)
			{
				$this->p[] = $row4;
			}
			$subtotalivanoc = ($row4['valorneto']== "" ? "0.00" : $row4['valorneto']);

           ############ ACTUALIZO LOS TOTALES EN LA COTIZACION ##############
			$sql = " UPDATE compras SET "
			." codproveedor = ?, "
			." subtotalivasic = ?, "
			." subtotalivanoc = ?, "
			." totalivac = ?, "
			." descuentoc = ?, "
			." totaldescuentoc = ?, "
			." totalpagoc = ?, "
			." tipocompra = ?, "
			." formacompra = ?, "
			." fechavencecredito = ?, "
			." fechaemision = ?, "
			." fecharecepcion = ? "
			." WHERE "
			." codcompra = ? AND codsucursal = ?;
			";
			$stmt = $this->dbh->prepare($sql);
			$stmt->bindParam(1, $codproveedor);
			$stmt->bindParam(2, $subtotalivasic);
			$stmt->bindParam(3, $subtotalivanoc);
			$stmt->bindParam(4, $totalivac);
			$stmt->bindParam(5, $descuentoc);
			$stmt->bindParam(6, $totaldescuentoc);
			$stmt->bindParam(7, $totalpagoc);
			$stmt->bindParam(8, $tipocompra);
			$stmt->bindParam(9, $formacompra);
			$stmt->bindParam(10, $fechavencecredito);
			$stmt->bindParam(11, $fechaemision);
			$stmt->bindParam(12, $fecharecepcion);
			$stmt->bindParam(13, $codcompra);
			$stmt->bindParam(14, $codsucursal);

			$codproveedor = limpiar($_POST["codproveedor"]);
			$ivac = $_POST["iva"]/100;
			$totalivac = number_format($subtotalivasic*$ivac, 2, '.', '');
			$descuentoc = limpiar($_POST["descuento"]);
		    $txtDescuento = $_POST["descuento"]/100;
		    $total = number_format($subtotalivasic+$subtotalivanoc+$totalivac, 2, '.', '');
		    $totaldescuentoc = number_format($total*$txtDescuento, 2, '.', '');
		    $totalpagoc = number_format($total-$totaldescuentoc, 2, '.', '');
			$tipocompra = limpiar($_POST["tipocompra"]);
			$formacompra = limpiar($_POST["tipocompra"]=="CONTADO" ? $_POST["formacompra"] : "CREDITO");
			$fechavencecredito = limpiar($_POST["tipocompra"]=="CREDITO" ? date("Y-m-d",strtotime($_POST['fechavencecredito'])) : "0000-00-00");
			$statuscompra = limpiar($_POST["tipocompra"]=="CONTADO" ? "PAGADA" : "PENDIENTE");
			$fechaemision = limpiar(date("Y-m-d",strtotime($_POST['fechaemision'])));
			$fecharecepcion = limpiar(date("Y-m-d",strtotime($_POST['fecharecepcion'])));
			$codcompra = limpiar($_POST["codcompra"]);
			$codsucursal = limpiar($_POST["codsucursal"]);
			$stmt->execute();

echo "<span class='fa fa-check-square-o'></span> LA COMPRA DE PRODUCTOS HA SIDO ACTUALIZADA EXITOSAMENTE <a href='reportepdf?codcompra=".encrypt($codcompra)."&codsucursal=".encrypt($codsucursal)."&tipo=".encrypt("FACTURACOMPRA")."' class='on-default' data-placement='left' data-toggle='tooltip' data-original-title='Imprimir Documento' target='_black' rel='noopener noreferrer'><font color='black'><strong>IMPRIMIR REPORTE</strong></font color></a></div>";

echo "<script>window.open('reportepdf?codcompra=".encrypt($codcompra)."&codsucursal=".encrypt($codsucursal)."&tipo=".encrypt("FACTURACOMPRA")."', '_blank');</script>";
	exit;
}
############################# FUNCION ACTUALIZAR COMPRAS #########################

########################## FUNCION ELIMINAR DETALLES COMPRAS ########################
	public function EliminarDetallesCompras()
	{
	    self::SetNames();
		if ($_SESSION["acceso"]=="administradorS") {

		$sql = "SELECT * FROM detallecompras WHERE codcompra = ? AND codsucursal = ?";
		$stmt = $this->dbh->prepare($sql);
		$stmt->execute(array(decrypt($_GET["codcompra"]),decrypt($_GET["codsucursal"])));
		$num = $stmt->rowCount();
		if($num > 1)
		{

			$sql = "SELECT codproducto, cantcompra, preciocomprac, ivaproductoc, descproductoc FROM detallecompras WHERE coddetallecompra = ? and codsucursal = ?";
			$stmt = $this->dbh->prepare($sql);
			$stmt->execute(array(decrypt($_GET["coddetallecompra"]),decrypt($_GET["codsucursal"])));
			$num = $stmt->rowCount();

			if($row = $stmt->fetch(PDO::FETCH_ASSOC))
			{
				$p[] = $row;
			}
			$codproducto = $row['codproducto'];
			$cantidaddb = $row['cantcompra'];
			$preciocompradb = $row['preciocomprac'];
			$ivaproductodb = $row['ivaproductoc'];
			$descproductodb = $row['descproductoc'];

			$sql2 = "SELECT existencia FROM productos WHERE codproducto = ? AND codsucursal = ?";
			$stmt = $this->dbh->prepare($sql2);
			$stmt->execute(array($codproducto,decrypt($_GET["codsucursal"])));
			$num = $stmt->rowCount();

			if($row = $stmt->fetch(PDO::FETCH_ASSOC))
			{
				$p[] = $row;
			}
			$existenciabd = $row['existencia'];

			############# ACTUALIZAMOS LA EXISTENCIA DE PRODUCTO EN ALMACEN #############
			$sql = "UPDATE productos SET "
			." existencia = ? "
			." WHERE "
			." codproducto = ? AND codsucursal = ?;
			";
			$stmt = $this->dbh->prepare($sql);
			$stmt->bindParam(1, $existencia);
			$stmt->bindParam(2, $codproducto);
			$stmt->bindParam(3, $codsucursal);

			$existencia = limpiar($existenciabd-$cantidaddb);
			$codsucursal = limpiar(decrypt($_GET["codsucursal"]));
			$stmt->execute();


		    ########## REGISTRAMOS LOS DATOS DEL PRODUCTO ELIMINADO EN KARDEX ##########
			$query = "INSERT INTO kardex values (null, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?); ";
			$stmt = $this->dbh->prepare($query);
			$stmt->bindParam(1, $codcompra);
			$stmt->bindParam(2, $codproveedor);
			$stmt->bindParam(3, $codproducto);
			$stmt->bindParam(4, $movimiento);
			$stmt->bindParam(5, $entradas);
			$stmt->bindParam(6, $salidas);
			$stmt->bindParam(7, $devolucion);
			$stmt->bindParam(8, $stockactual);
			$stmt->bindParam(9, $ivaproducto);
			$stmt->bindParam(10, $descproducto);
			$stmt->bindParam(11, $precio);
			$stmt->bindParam(12, $documento);
			$stmt->bindParam(13, $fechakardex);		
			$stmt->bindParam(14, $codsucursal);

			$codcompra = limpiar(decrypt($_GET["codcompra"]));
		    $codproveedor = limpiar(decrypt($_GET["codproveedor"]));
			$movimiento = limpiar("DEVOLUCION");
			$entradas= limpiar("0");
			$salidas = limpiar("0");
			$devolucion = limpiar($cantidaddb);
			$stockactual = limpiar($existenciabd-$cantidaddb);
			$precio = limpiar($preciocompradb);
			$ivaproducto = limpiar($ivaproductodb);
			$descproducto = limpiar($descproductodb);
			$documento = limpiar("DEVOLUCION COMPRA: ".decrypt($_GET["codcompra"]));
			$fechakardex = limpiar(date("Y-m-d"));
			$codsucursal = limpiar(decrypt($_GET["codsucursal"]));
			$stmt->execute();


			########## ELIMINAMOS EL PRODUCTO EN DETALLES DE COMPRAS ###########
			$sql = "DELETE FROM detallecompras WHERE coddetallecompra = ? AND codsucursal = ?";
			$stmt = $this->dbh->prepare($sql);
			$stmt->bindParam(1,$coddetallecompra);
			$stmt->bindParam(2,$codsucursal);
			$coddetallecompra = decrypt($_GET["coddetallecompra"]);
			$codsucursal = decrypt($_GET["codsucursal"]);
			$stmt->execute();


		    ############ CONSULTO LOS TOTALES DE COMPRAS ##############
		    $sql2 = "SELECT ivac, descuentoc FROM compras WHERE codcompra = ? AND codsucursal = ?";
		    $stmt = $this->dbh->prepare($sql2);
		    $stmt->execute(array(decrypt($_GET["codcompra"]),decrypt($_GET["codsucursal"])));
		    $num = $stmt->rowCount();

			if($row = $stmt->fetch(PDO::FETCH_ASSOC))
			{
				$paea[] = $row;
			}
			$iva = $paea[0]["ivac"]/100;
		    $descuento = $paea[0]["descuentoc"]/100;

             ############ SUMO LOS IMPORTE DE PRODUCTOS CON IVA ##############
			$sql3 = "SELECT SUM(valorneto) AS valorneto FROM detallecompras WHERE codcompra = '".limpiar(decrypt($_GET["codcompra"]))."' AND codsucursal = '".limpiar(decrypt($_GET["codsucursal"]))."' AND ivaproductoc = 'SI'";
			foreach ($this->dbh->query($sql3) as $row3)
			{
				$this->p[] = $row3;
			}
			$subtotalivasic = ($row3['valorneto']== "" ? "0.00" : $row3['valorneto']);

		    ############ SUMO LOS IMPORTE DE PRODUCTOS SIN IVA ##############
			$sql4 = "SELECT SUM(valorneto) AS valorneto FROM detallecompras WHERE codcompra = '".limpiar(decrypt($_GET["codcompra"]))."' AND codsucursal = '".limpiar(decrypt($_GET["codsucursal"]))."' AND ivaproductoc = 'NO'";
			foreach ($this->dbh->query($sql4) as $row4)
			{
				$this->p[] = $row4;
			}
			$subtotalivanoc = ($row4['valorneto']== "" ? "0.00" : $row4['valorneto']);


            ############ ACTUALIZO LOS TOTALES EN LA COMPRAS ##############
			$sql = " UPDATE compras SET "
			." subtotalivasic = ?, "
			." subtotalivanoc = ?, "
			." totalivac = ?, "
			." totaldescuentoc = ?, "
			." totalpagoc = ? "
			." WHERE "
			." codcompra = ? AND codsucursal = ?;
			";
			$stmt = $this->dbh->prepare($sql);
			$stmt->bindParam(1, $subtotalivasic);
			$stmt->bindParam(2, $subtotalivanoc);
			$stmt->bindParam(3, $totalivac);
			$stmt->bindParam(4, $totaldescuentoc);
			$stmt->bindParam(5, $totalpagoc);
			$stmt->bindParam(6, $codcompra);
			$stmt->bindParam(7, $codsucursal);

			$totalivac= number_format($subtotalivasic*$iva, 2, '.', '');
		    $total= number_format($subtotalivasic+$subtotalivanoc+$totalivac, 2, '.', '');
		    $totaldescuentoc = number_format($total*$descuento, 2, '.', '');
		    $totalpagoc = number_format($total-$totaldescuentoc, 2, '.', '');
			$codcompra = limpiar(decrypt($_GET["codcompra"]));
			$codsucursal = limpiar(decrypt($_GET["codsucursal"]));
			$stmt->execute();

			echo "1";
			exit;

		} else {
		   
			echo "2";
			exit;
		  } 
			
		} else {
		
		echo "3";
		exit;
	 }	
}
###################### FUNCION ELIMINAR DETALLES COMPRAS #######################

####################### FUNCION ELIMINAR COMPRAS #################################
	public function EliminarCompras()
	{
	self::SetNames();
		if ($_SESSION["acceso"]=="administradorS") {

	$sql = "SELECT * FROM detallecompras WHERE codcompra = '".limpiar(decrypt($_GET["codcompra"]))."' AND codsucursal = '".limpiar(decrypt($_GET["codsucursal"]))."'";

		$array=array();

	foreach ($this->dbh->query($sql) as $row)
		{
				$this->p[] = $row;

			$codproducto = $row['codproducto'];
			$cantidaddb = $row['cantcompra'];
			$preciocompradb = $row['preciocomprac'];
			$ivaproductodb = $row['ivaproductoc'];
			$descproductodb = $row['descproductoc'];

			$sql2 = "SELECT existencia FROM productos WHERE codproducto = ? AND codsucursal = ?";
			$stmt = $this->dbh->prepare($sql2);
			$stmt->execute( array($codproducto,decrypt($_GET["codsucursal"])));
			$num = $stmt->rowCount();

			if($row = $stmt->fetch(PDO::FETCH_ASSOC))
			{
				$p[] = $row;
			}
			$existenciabd = $row['existencia'];

			########### ACTUALIZAMOS LA EXISTENCIA DE PRODUCTO EN ALMACEN #############
			$sql = "UPDATE productos SET "
			." existencia = ? "
			." WHERE "
			." codproducto = ? AND codsucursal = ?;
			";
			$stmt = $this->dbh->prepare($sql);
			$stmt->bindParam(1, $existencia);
			$stmt->bindParam(2, $codproducto);
			$stmt->bindParam(3, $codsucursal);

			$existencia = limpiar($existenciabd-$cantidaddb);
			$codsucursal = limpiar(decrypt($_GET["codsucursal"]));
			$stmt->execute();

		    ########## REGISTRAMOS LOS DATOS DEL PRODUCTO ELIMINADO EN KARDEX ##########
			$query = "INSERT INTO kardex values (null, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?); ";
			$stmt = $this->dbh->prepare($query);
			$stmt->bindParam(1, $codcompra);
			$stmt->bindParam(2, $codproveedor);
			$stmt->bindParam(3, $codproducto);
			$stmt->bindParam(4, $movimiento);
			$stmt->bindParam(5, $entradas);
			$stmt->bindParam(6, $salidas);
			$stmt->bindParam(7, $devolucion);
			$stmt->bindParam(8, $stockactual);
			$stmt->bindParam(9, $ivaproducto);
			$stmt->bindParam(10, $descproducto);
			$stmt->bindParam(11, $precio);
			$stmt->bindParam(12, $documento);
			$stmt->bindParam(13, $fechakardex);		
			$stmt->bindParam(14, $codsucursal);

			$codcompra = limpiar(decrypt($_GET["codcompra"]));
		    $codproveedor = limpiar(decrypt($_GET["codproveedor"]));
			$movimiento = limpiar("DEVOLUCION");
			$entradas= limpiar("0");
			$salidas = limpiar("0");
			$devolucion = limpiar($cantidaddb);
			$stockactual = limpiar($existenciabd-$cantidaddb);
			$precio = limpiar($preciocompradb);
			$ivaproducto = limpiar($ivaproductodb);
			$descproducto = limpiar($descproductodb);
			$documento = limpiar("DEVOLUCION COMPRA: ".decrypt($_GET["codcompra"]));
			$fechakardex = limpiar(date("Y-m-d"));
			$codsucursal = limpiar(decrypt($_GET["codsucursal"]));
			$stmt->execute();
		}


			$sql = "DELETE FROM compras WHERE codcompra = ? AND codsucursal = ?";
			$stmt = $this->dbh->prepare($sql);
			$stmt->bindParam(1,$codcompra);
			$stmt->bindParam(2,$codsucursal);
			$codcompra = decrypt($_GET["codcompra"]);
			$codsucursal = decrypt($_GET["codsucursal"]);
			$stmt->execute();

			$sql = "DELETE FROM detallecompras WHERE codcompra = ? AND codsucursal = ?";
			$stmt = $this->dbh->prepare($sql);
			$stmt->bindParam(1,$codcompra);
			$stmt->bindParam(2,$codsucursal);
			$codcompra = decrypt($_GET["codcompra"]);
			$codsucursal = decrypt($_GET["codsucursal"]);
			$stmt->execute();

			echo "1";
			exit;

		} else {

			echo "2";
			exit;
		}
	}
######################### FUNCION ELIMINAR COMPRAS #################################

##################### FUNCION BUSQUEDA COMPRAS POR PROVEEDORES ###################
	public function BuscarComprasxProveedor() 
	{
		self::SetNames();
		$sql = "SELECT 
		compras.codcompra,
		compras.codproveedor, 
		compras.subtotalivasic,
		compras.subtotalivanoc, 
		compras.ivac,
		compras.totalivac, 
		compras.descuentoc,
		compras.totaldescuentoc, 
		compras.totalpagoc, 
		compras.tipocompra,
		compras.formacompra,
		compras.fechavencecredito,
	    compras.fechapagado,
		compras.statuscompra,
		compras.fechaemision,
		compras.fecharecepcion,
	    compras.observaciones,
		compras.codsucursal,
		sucursales.documsucursal, 
		sucursales.cuitsucursal, 
		sucursales.razonsocial,
		sucursales.documencargado,
		sucursales.dniencargado,
		sucursales.nomencargado,
		proveedores.documproveedor,
		proveedores.cuitproveedor, 
		proveedores.nomproveedor, 
		proveedores.tlfproveedor, 
		proveedores.id_provincia, 
		proveedores.id_departamento, 
		proveedores.direcproveedor, 
		proveedores.emailproveedor,
		proveedores.vendedor,
		proveedores.tlfvendedor,
	    documentos.documento,
	    documentos2.documento AS documento2, 
	    documentos3.documento AS documento3, 
		provincias.provincia,
		departamentos.departamento, 
		SUM(detallecompras.cantcompra) as articulos 
		FROM (compras LEFT JOIN detallecompras ON compras.codcompra=detallecompras.codcompra) 
		INNER JOIN sucursales ON compras.codsucursal = sucursales.codsucursal 
		LEFT JOIN documentos ON sucursales.documsucursal = documentos.coddocumento
		LEFT JOIN documentos AS documentos2 ON sucursales.documencargado = documentos2.coddocumento 
		INNER JOIN proveedores ON compras.codproveedor = proveedores.codproveedor 
		LEFT JOIN documentos AS documentos3 ON proveedores.documproveedor = documentos3.coddocumento
		LEFT JOIN provincias ON proveedores.id_provincia = provincias.id_provincia 
		LEFT JOIN departamentos ON proveedores.id_departamento = departamentos.id_departamento 
		WHERE compras.codsucursal = ? AND compras.codproveedor = ? GROUP BY detallecompras.codcompra";
		$stmt = $this->dbh->prepare($sql);
		$stmt->execute(array(decrypt($_GET["codsucursal"]),decrypt($_GET["codproveedor"])));
		$num = $stmt->rowCount();
		if($num==0)
		{
	echo "<div class='alert alert-danger'>";
	echo "<button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>";
	echo "<center><span class='fa fa-info-circle'></span> NO SE ENCONTRARON COMPRAS DE PRODUCTOS PARA EL PROVEEDOR SELECCIONADO</center>";
	echo "</div>";		
	exit;
		}
		else
		{
			while($row = $stmt->fetch(PDO::FETCH_ASSOC))
			{
				$this->p[]=$row;
			}
			return $this->p;
			$this->dbh=null;
		}
	}
################### FUNCION BUSQUEDA COMPRAS POR PROVEEDORES ###################

###################### FUNCION BUSQUEDA COMPRAS POR FECHAS ###########################
	public function BuscarComprasxFechas() 
	{
		self::SetNames();
		$sql ="SELECT 
		compras.codcompra,
		compras.codproveedor, 
		compras.subtotalivasic,
		compras.subtotalivanoc, 
		compras.ivac,
		compras.totalivac, 
		compras.descuentoc,
		compras.totaldescuentoc, 
		compras.totalpagoc, 
		compras.tipocompra,
		compras.formacompra,
		compras.fechavencecredito,
	    compras.fechapagado,
		compras.statuscompra,
		compras.fechaemision,
		compras.fecharecepcion,
	    compras.observaciones,
		compras.codsucursal,
		sucursales.documsucursal, 
		sucursales.cuitsucursal, 
		sucursales.razonsocial,
		sucursales.documencargado,
		sucursales.dniencargado,
		sucursales.nomencargado,
		proveedores.documproveedor,
		proveedores.cuitproveedor, 
		proveedores.nomproveedor, 
		proveedores.tlfproveedor, 
		proveedores.id_provincia, 
		proveedores.id_departamento, 
		proveedores.direcproveedor, 
		proveedores.emailproveedor,
		proveedores.vendedor,
		proveedores.tlfvendedor,
	    documentos.documento,
	    documentos2.documento AS documento2, 
	    documentos3.documento AS documento3, 
		provincias.provincia,
		departamentos.departamento, 
		SUM(detallecompras.cantcompra) as articulos 
		FROM (compras LEFT JOIN detallecompras ON compras.codcompra=detallecompras.codcompra) 
		INNER JOIN sucursales ON compras.codsucursal = sucursales.codsucursal 
		LEFT JOIN documentos ON sucursales.documsucursal = documentos.coddocumento
		LEFT JOIN documentos AS documentos2 ON sucursales.documencargado = documentos2.coddocumento 
		INNER JOIN proveedores ON compras.codproveedor = proveedores.codproveedor 
		LEFT JOIN documentos AS documentos3 ON proveedores.documproveedor = documentos3.coddocumento
		LEFT JOIN provincias ON proveedores.id_provincia = provincias.id_provincia 
		LEFT JOIN departamentos ON proveedores.id_departamento = departamentos.id_departamento
		 WHERE compras.codsucursal = ? AND DATE_FORMAT(compras.fecharecepcion,'%Y-%m-%d') >= ? AND DATE_FORMAT(compras.fecharecepcion,'%Y-%m-%d') <= ? GROUP BY detallecompras.codcompra";
		$stmt = $this->dbh->prepare($sql);
		$stmt->bindValue(1, trim(decrypt($_GET['codsucursal'])));
		$stmt->bindValue(2, trim(date("Y-m-d",strtotime($_GET['desde']))));
		$stmt->bindValue(3, trim(date("Y-m-d",strtotime($_GET['hasta']))));
		$stmt->execute();
		$num = $stmt->rowCount();
		if($num==0)
		{
	echo "<div class='alert alert-danger'>";
	echo "<button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>";
	echo "<center><span class='fa fa-info-circle'></span> NO SE ENCONTRARON COMPRAS DE PRODUCTO PARA EL RANGO DE FECHA INGRESADO</center>";
	echo "</div>";		
	exit;
		}
		else
		{
			while($row = $stmt->fetch(PDO::FETCH_ASSOC))
			{
				$this->p[]=$row;
			}
			return $this->p;
			$this->dbh=null;
		}
	}
###################### FUNCION BUSQUEDA COMPRAS POR FECHAS ###########################

###################### FUNCION BUSQUEDA CREDITOS POR PROVEEDOR ###########################
public function BuscarCreditosxProveedor() 
	{
	self::SetNames();
	$sql = "SELECT 
	compras.codcompra, 
	compras.totalpagoc, 
	compras.tipocompra,
	compras.statuscompra,
	compras.fechaemision, 
	compras.fechavencecredito,
	compras.fechapagado,
	compras.codsucursal, 
	sucursales.documsucursal, 
	sucursales.cuitsucursal, 
	sucursales.razonsocial,
	sucursales.documencargado,
	sucursales.dniencargado,
	sucursales.nomencargado,
	proveedores.codproveedor,
	proveedores.documproveedor, 
	proveedores.cuitproveedor, 
	proveedores.nomproveedor, 
	proveedores.tlfproveedor, 
	abonoscreditoscompras.codcompra as codigo, 
	abonoscreditoscompras.fechaabono, 
	documentos.documento,
	documentos2.documento AS documento2, 
	documentos3.documento AS documento3,
	SUM(abonoscreditoscompras.montoabono) AS abonototal  
	FROM (compras INNER JOIN proveedores ON compras.codproveedor = proveedores.codproveedor)
	LEFT JOIN abonoscreditoscompras ON compras.codcompra = abonoscreditoscompras.codcompra
	LEFT JOIN sucursales ON compras.codsucursal = sucursales.codsucursal 
	LEFT JOIN documentos ON sucursales.documsucursal = documentos.coddocumento
	LEFT JOIN documentos AS documentos2 ON sucursales.documencargado = documentos2.coddocumento 
	LEFT JOIN documentos AS documentos3 ON proveedores.documproveedor = documentos3.coddocumento
	WHERE compras.codsucursal = ? AND compras.codproveedor = ? AND compras.tipocompra ='CREDITO' GROUP BY compras.codcompra";
		$stmt = $this->dbh->prepare($sql);
		$stmt->bindValue(1, trim(decrypt($_GET['codsucursal'])));
		$stmt->bindValue(2, trim(decrypt($_GET['codproveedor'])));
		$stmt->execute();
		$num = $stmt->rowCount();
		if($num==0)
		{
	echo "<div class='alert alert-danger'>";
	echo "<button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>";
	echo "<center><span class='fa fa-info-circle'></span> NO SE ENCONTRARON CREDITOS PARA EL PROVEEDOR INGRESADO</center>";
	echo "</div>";		
	exit;
		}
		else
		{
			while($row = $stmt->fetch(PDO::FETCH_ASSOC))
			{
				$this->p[]=$row;
			}
			return $this->p;
			$this->dbh=null;
		}
	}
###################### FUNCION BUSQUEDA CREDITOS POR PROVEEDOR ###########################

###################### FUNCION BUSQUEDA CREDITOS DE COMPRAS POR FECHAS ###########################
public function BuscarCreditosComprasxFechas() 
	{
		self::SetNames();
		$sql = "SELECT 
	compras.codcompra, 
	compras.totalpagoc, 
	compras.tipocompra,
	compras.statuscompra,
	compras.fechaemision, 
	compras.fechavencecredito,
	compras.fechapagado,
	compras.codsucursal, 
	sucursales.documsucursal, 
	sucursales.cuitsucursal, 
	sucursales.razonsocial,
	sucursales.documencargado,
	sucursales.dniencargado,
	sucursales.nomencargado,
	proveedores.codproveedor,
	proveedores.documproveedor, 
	proveedores.cuitproveedor, 
	proveedores.nomproveedor, 
	proveedores.tlfproveedor, 
	abonoscreditoscompras.codcompra as codigo, 
	abonoscreditoscompras.fechaabono, 
	documentos.documento,
	documentos2.documento AS documento2, 
	documentos3.documento AS documento3,
	SUM(abonoscreditoscompras.montoabono) AS abonototal  
	FROM (compras INNER JOIN proveedores ON compras.codproveedor = proveedores.codproveedor)
	LEFT JOIN abonoscreditoscompras ON compras.codcompra = abonoscreditoscompras.codcompra
	LEFT JOIN sucursales ON compras.codsucursal = sucursales.codsucursal 
	LEFT JOIN documentos ON sucursales.documsucursal = documentos.coddocumento
	LEFT JOIN documentos AS documentos2 ON sucursales.documencargado = documentos2.coddocumento 
	LEFT JOIN documentos AS documentos3 ON proveedores.documproveedor = documentos3.coddocumento
	WHERE compras.codsucursal = ? AND DATE_FORMAT(compras.fechaemision,'%Y-%m-%d') >= ? AND DATE_FORMAT(compras.fechaemision,'%Y-%m-%d') <= ?
	 AND compras.tipocompra ='CREDITO' GROUP BY compras.codcompra";
		$stmt = $this->dbh->prepare($sql);
		$stmt->bindValue(1, trim(decrypt($_GET['codsucursal'])));
		$stmt->bindValue(2, trim(date("Y-m-d",strtotime($_GET['desde']))));
		$stmt->bindValue(3, trim(date("Y-m-d",strtotime($_GET['hasta']))));
		$stmt->execute();
		$num = $stmt->rowCount();
		if($num==0)
		{
	echo "<div class='alert alert-danger'>";
	echo "<button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>";
	echo "<center><span class='fa fa-info-circle'></span> NO SE ENCONTRARON CREDITOS DE COMPRAS PARA EL RANGO DE FECHA INGRESADO</center>";
	echo "</div>";		
	exit;
		}
		else
		{
			while($row = $stmt->fetch(PDO::FETCH_ASSOC))
			{
				$this->p[]=$row;
			}
			return $this->p;
			$this->dbh=null;
		}
	}
###################### FUNCION BUSQUEDA CREDITOS DE COMPRAS POR FECHAS ###########################

############################# FIN DE CLASE COMPRAS ###################################





























############################## CLASE COTIZACIONES ###################################

########################### FUNCION REGISTRAR COTIZACIONES ##########################
	public function RegistrarCotizaciones()
	{
		self::SetNames();
		if(empty($_POST["codsucursal"]) or empty($_POST["txtTotal"]))
		{
			echo "1";
			exit;
		}

		if(empty($_SESSION["CarritoCotizacion"]))
		{
			echo "2";
			exit;
			
		} else {

	################### CREO LOS CODIGO DE COTIZACION ####################
		$sql = " SELECT codsucursal, nroactividadsucursal, iniciofactura FROM sucursales WHERE codsucursal = '".limpiar($_POST["codsucursal"])."'";
		foreach ($this->dbh->query($sql) as $row)
		{
			$this->p[] = $row;
		}
		$nroactividad = $row['nroactividadsucursal'];
		$iniciofactura = $row['iniciofactura'];
		
		$sql4 = "SELECT codcotizacion FROM cotizaciones WHERE codsucursal = '".limpiar($_POST["codsucursal"])."' ORDER BY idcotizacion DESC LIMIT 1";
		 foreach ($this->dbh->query($sql4) as $row4){

			$cotizacion=$row4["codcotizacion"];

		}
		if(empty($cotizacion))
		{
			$codcotizacion = $nroactividad.'-'.$iniciofactura;

		} else {

			$var = strlen($nroactividad."-");
            $var1 = substr($cotizacion , $var);
            $var2 = strlen($var1);
            $var3 = $var1 + 1;
            $var4 = str_pad($var3, $var2, "0", STR_PAD_LEFT);
            $codcotizacion = $nroactividad.'-'.$var4;
		}
    ################### CREO LOS CODIGO DE COTIZACION ####################


        $query = "INSERT INTO cotizaciones values (null, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);";
		$stmt = $this->dbh->prepare($query);
		$stmt->bindParam(1, $codcotizacion);
		$stmt->bindParam(2, $codcliente);
		$stmt->bindParam(3, $subtotalivasi);
		$stmt->bindParam(4, $subtotalivano);
		$stmt->bindParam(5, $iva);
		$stmt->bindParam(6, $totaliva);
		$stmt->bindParam(7, $descuento);
		$stmt->bindParam(8, $totaldescuento);
		$stmt->bindParam(9, $totalpago);
		$stmt->bindParam(10, $totalpago2);
		$stmt->bindParam(11, $observaciones);
		$stmt->bindParam(12, $fechacotizacion);
		$stmt->bindParam(13, $codigo);
		$stmt->bindParam(14, $codsucursal);
	    
		$codcliente = limpiar($_POST["codcliente"]);
		$subtotalivasi = limpiar($_POST["txtsubtotal"]);
		$subtotalivano = limpiar($_POST["txtsubtotal2"]);
		$iva = limpiar($_POST["iva"]);
		$totaliva = limpiar($_POST["txtIva"]);
		$descuento = limpiar($_POST["descuento"]);
		$totaldescuento = limpiar($_POST["txtDescuento"]);
		$totalpago = limpiar($_POST["txtTotal"]);
		$totalpago2 = limpiar($_POST["txtTotalCompra"]);
		$observaciones = limpiar($_POST["observaciones"]);
        $fechacotizacion = limpiar(date("Y-m-d h:i:s"));
		$codigo = limpiar($_SESSION["codigo"]);
		$codsucursal = limpiar($_SESSION["codsucursal"]);
		$stmt->execute();
		
		$this->dbh->beginTransaction();
		$detalle = $_SESSION["CarritoCotizacion"];
		for($i=0;$i<count($detalle);$i++){

		$query = "INSERT INTO detallecotizaciones values (null, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?); ";
		$stmt = $this->dbh->prepare($query);
		$stmt->bindParam(1, $codcotizacion);
	    $stmt->bindParam(2, $codproducto);
	    $stmt->bindParam(3, $producto);
		$stmt->bindParam(4, $cantidad);
		$stmt->bindParam(5, $preciocompra);
		$stmt->bindParam(6, $precioventa);
		$stmt->bindParam(7, $ivaproducto);
		$stmt->bindParam(8, $descproducto);
		$stmt->bindParam(9, $valortotal);
		$stmt->bindParam(10, $totaldescuentov);
		$stmt->bindParam(11, $valorneto);
		$stmt->bindParam(12, $valorneto2);
		$stmt->bindParam(13, $codsucursal);
			
		$codproducto = limpiar($detalle[$i]['txtCodigo']);
		$producto = limpiar($detalle[$i]['producto']);
		$cantidad = limpiar($detalle[$i]['cantidad']);
		$preciocompra = limpiar($detalle[$i]['precio']);
		$precioventa = limpiar($detalle[$i]['precio2']);
		$ivaproducto = limpiar($detalle[$i]['ivaproducto']);
		$descproducto = limpiar($detalle[$i]['descproducto']);
		$descuento = $detalle[$i]['descproducto']/100;
		$valortotal = number_format($detalle[$i]['precio2']*$detalle[$i]['cantidad'], 2, '.', '');
		$totaldescuentov = number_format($valortotal*$descuento, 2, '.', '');
	    $valorneto = number_format($valortotal-$totaldescuentov, 2, '.', '');
		$valorneto2 = number_format($detalle[$i]['precio']*$detalle[$i]['cantidad'], 2, '.', '');
		$codsucursal = limpiar($_SESSION["codsucursal"]);
		$stmt->execute();
      }
        
        ####################### DESTRUYO LA VARIABLE DE SESSION #####################
	    unset($_SESSION["CarritoCotizacion"]);
        $this->dbh->commit();
		
echo "<span class='fa fa-check-square-o'></span> LA COTIZACI&Oacute;N DE PRODUCTOS HA SIDO REGISTRADA EXITOSAMENTE <a href='reportepdf?codcotizacion=".encrypt($codcotizacion)."&codsucursal=".encrypt($codsucursal)."&tipo=".encrypt("FACTURACOTIZACION")."' class='on-default' data-placement='left' data-toggle='tooltip' data-original-title='Imprimir Documento' target='_black' rel='noopener noreferrer'><font color='black'><strong>IMPRIMIR REPORTE</strong></font color></a></div>";

echo "<script>window.open('reportepdf?codcotizacion=".encrypt($codcotizacion)."&codsucursal=".encrypt($codsucursal)."&tipo=".encrypt("FACTURACOTIZACION")."', '_blank');</script>";
	exit;
	}
}
########################## FUNCION REGISTRAR COTIZACIONES ############################

####################### FUNCION LISTAR COTIZACIONES ################################
public function ListarCotizaciones()
{
	self::SetNames();

	if ($_SESSION['acceso'] == "administradorG") {

	$sql = "SELECT 
	cotizaciones.idcotizacion, 
	cotizaciones.codcotizacion, 
	cotizaciones.codcliente, 
	cotizaciones.subtotalivasi, 
	cotizaciones.subtotalivano, 
	cotizaciones.iva, 
	cotizaciones.totaliva, 
	cotizaciones.descuento, 
	cotizaciones.totaldescuento,
	cotizaciones.totalpago, 
	cotizaciones.totalpago2, 
	cotizaciones.observaciones,
	cotizaciones.fechacotizacion, 
	cotizaciones.codsucursal,
	sucursales.documsucursal, 
	sucursales.cuitsucursal, 
	sucursales.razonsocial,
	sucursales.documencargado,
	sucursales.dniencargado,
	sucursales.nomencargado, 
	clientes.documcliente, 
	clientes.dnicliente, 
	clientes.nomcliente,
	clientes.limitecredito,
	documentos.documento,
	documentos2.documento AS documento2, 
	documentos3.documento AS documento3,  
	SUM(detallecotizaciones.cantcotizacion) AS articulos 
	FROM (cotizaciones LEFT JOIN detallecotizaciones ON detallecotizaciones.codcotizacion = cotizaciones.codcotizacion) 
	LEFT JOIN sucursales ON cotizaciones.codsucursal = sucursales.codsucursal
	LEFT JOIN documentos ON sucursales.documsucursal = documentos.coddocumento
	LEFT JOIN documentos AS documentos2 ON sucursales.documencargado = documentos2.coddocumento 
	LEFT JOIN clientes ON cotizaciones.codcliente = clientes.codcliente
	LEFT JOIN documentos AS documentos3 ON clientes.documcliente = documentos3.coddocumento
	LEFT JOIN usuarios ON cotizaciones.codigo = usuarios.codigo GROUP BY detallecotizaciones.codcotizacion ORDER BY cotizaciones.idcotizacion DESC";
	foreach ($this->dbh->query($sql) as $row)
	{
		$this->p[] = $row;
	}
	return $this->p;
	$this->dbh=null;

     } else if($_SESSION["acceso"] == "cajero") {

     $sql = "SELECT 
	cotizaciones.idcotizacion, 
	cotizaciones.codcotizacion, 
	cotizaciones.codcliente, 
	cotizaciones.subtotalivasi, 
	cotizaciones.subtotalivano, 
	cotizaciones.iva, 
	cotizaciones.totaliva, 
	cotizaciones.descuento, 
	cotizaciones.totaldescuento,
	cotizaciones.totalpago, 
	cotizaciones.totalpago2, 
	cotizaciones.observaciones,
	cotizaciones.fechacotizacion, 
	cotizaciones.codsucursal,
	sucursales.documsucursal, 
	sucursales.cuitsucursal, 
	sucursales.razonsocial,
	sucursales.documencargado,
	sucursales.dniencargado,
	sucursales.nomencargado,
	clientes.documcliente, 
	clientes.dnicliente, 
	clientes.nomcliente,
	clientes.limitecredito,
	documentos.documento,
	documentos2.documento AS documento2, 
	documentos3.documento AS documento3,  
	SUM(detallecotizaciones.cantcotizacion) AS articulos 
	FROM (cotizaciones LEFT JOIN detallecotizaciones ON detallecotizaciones.codcotizacion = cotizaciones.codcotizacion) 
	LEFT JOIN sucursales ON cotizaciones.codsucursal = sucursales.codsucursal
	LEFT JOIN documentos ON sucursales.documsucursal = documentos.coddocumento
	LEFT JOIN documentos AS documentos2 ON sucursales.documencargado = documentos2.coddocumento 
	LEFT JOIN clientes ON cotizaciones.codcliente = clientes.codcliente
	LEFT JOIN documentos AS documentos3 ON clientes.documcliente = documentos3.coddocumento
	LEFT JOIN usuarios ON cotizaciones.codigo = usuarios.codigo WHERE cotizaciones.codigo = '".limpiar($_SESSION["codigo"])."' GROUP BY detallecotizaciones.codcotizacion ORDER BY cotizaciones.idcotizacion DESC";
	foreach ($this->dbh->query($sql) as $row)
		{
			$this->p[] = $row;
		}
		    return $this->p;
			$this->dbh=null;

	} else {

		$sql = "SELECT 
	cotizaciones.idcotizacion, 
	cotizaciones.codcotizacion, 
	cotizaciones.codcliente, 
	cotizaciones.subtotalivasi, 
	cotizaciones.subtotalivano, 
	cotizaciones.iva, 
	cotizaciones.totaliva, 
	cotizaciones.descuento, 
	cotizaciones.totaldescuento,
	cotizaciones.totalpago, 
	cotizaciones.totalpago2,
	cotizaciones.observaciones, 
	cotizaciones.fechacotizacion, 
	cotizaciones.codsucursal,
	sucursales.documsucursal, 
	sucursales.cuitsucursal, 
	sucursales.razonsocial,
	sucursales.documencargado,
	sucursales.dniencargado,
	sucursales.nomencargado,
	clientes.documcliente, 
	clientes.dnicliente, 
	clientes.nomcliente,
	clientes.limitecredito,
	documentos.documento,
	documentos2.documento AS documento2, 
	documentos3.documento AS documento3,  
	SUM(detallecotizaciones.cantcotizacion) AS articulos 
	FROM (cotizaciones LEFT JOIN detallecotizaciones ON detallecotizaciones.codcotizacion = cotizaciones.codcotizacion) 
	LEFT JOIN sucursales ON cotizaciones.codsucursal = sucursales.codsucursal
	LEFT JOIN documentos ON sucursales.documsucursal = documentos.coddocumento
	LEFT JOIN documentos AS documentos2 ON sucursales.documencargado = documentos2.coddocumento 
	LEFT JOIN clientes ON cotizaciones.codcliente = clientes.codcliente
	LEFT JOIN documentos AS documentos3 ON clientes.documcliente = documentos3.coddocumento
	LEFT JOIN usuarios ON cotizaciones.codigo = usuarios.codigo WHERE cotizaciones.codsucursal = '".limpiar($_SESSION["codsucursal"])."' GROUP BY detallecotizaciones.codcotizacion";
	foreach ($this->dbh->query($sql) as $row)
	{
		$this->p[] = $row;
	}
	return $this->p;
	$this->dbh=null;

    }
}
######################### FUNCION LISTAR COTIZACIONES ############################

############################ FUNCION ID COTIZACIONES #################################
	public function CotizacionesPorId()
	{
		self::SetNames();
		$sql = " SELECT 
		cotizaciones.idcotizacion, 
		cotizaciones.codcotizacion,
		cotizaciones.codcliente, 
		cotizaciones.subtotalivasi,
		cotizaciones.subtotalivano, 
		cotizaciones.iva,
		cotizaciones.totaliva, 
		cotizaciones.descuento,
		cotizaciones.totaldescuento, 
		cotizaciones.totalpago, 
		cotizaciones.totalpago2,
	    cotizaciones.observaciones,
		cotizaciones.fechacotizacion,
		cotizaciones.codsucursal, 
		sucursales.documsucursal,
		sucursales.cuitsucursal,
		sucursales.razonsocial,
		sucursales.direcsucursal,
		sucursales.correosucursal,
		sucursales.tlfsucursal,
		sucursales.documencargado,
		sucursales.dniencargado,
		sucursales.nomencargado,
		sucursales.tlfencargado,
		clientes.documcliente,
		clientes.dnicliente, 
		clientes.nomcliente, 
		clientes.tlfcliente, 
		clientes.id_provincia, 
		clientes.id_departamento, 
		clientes.direccliente, 
		clientes.emailcliente,
		documentos.documento,
		documentos2.documento AS documento2, 
		documentos3.documento AS documento3,
		usuarios.dni, 
		usuarios.nombres,
		provincias.provincia,
		departamentos.departamento,
		provincias2.provincia AS provincia2,
		departamentos2.departamento AS departamento2
		FROM (cotizaciones INNER JOIN sucursales ON cotizaciones.codsucursal = sucursales.codsucursal)
		LEFT JOIN documentos ON sucursales.documsucursal = documentos.coddocumento
		LEFT JOIN documentos AS documentos2 ON sucursales.documencargado = documentos2.coddocumento
		LEFT JOIN provincias ON sucursales.id_provincia = provincias.id_provincia 
		LEFT JOIN departamentos ON sucursales.id_departamento = departamentos.id_departamento  
		LEFT JOIN clientes ON cotizaciones.codcliente = clientes.codcliente
		LEFT JOIN documentos AS documentos3 ON clientes.documcliente = documentos3.coddocumento
		LEFT JOIN provincias AS provincias2 ON clientes.id_provincia = provincias2.id_provincia 
		LEFT JOIN departamentos AS departamentos2 ON clientes.id_departamento = departamentos2.id_departamento
		LEFT JOIN usuarios ON cotizaciones.codigo = usuarios.codigo
		WHERE cotizaciones.codcotizacion = ? AND cotizaciones.codsucursal = ?";
		$stmt = $this->dbh->prepare($sql);
		$stmt->execute(array(decrypt($_GET["codcotizacion"]),decrypt($_GET["codsucursal"])));
		$num = $stmt->rowCount();
		if($num==0)
		{
			echo "";
		}
		else
		{
			if($row = $stmt->fetch(PDO::FETCH_ASSOC))
			{
				$this->p[] = $row;
			}
			return $this->p;
			$this->dbh=null;
		}
	}
############################ FUNCION ID COTIZACIONES #################################
	
######################## FUNCION VER DETALLES COTIZACIONES ############################
public function VerDetallesCotizaciones()
	{
		self::SetNames();
		$sql = "SELECT
		detallecotizaciones.coddetallecotizacion,
		detallecotizaciones.codcotizacion,
		detallecotizaciones.coddetallecotizacion,
		detallecotizaciones.codproducto,
		detallecotizaciones.producto,
		detallecotizaciones.cantcotizacion,
		detallecotizaciones.preciocompra,
		detallecotizaciones.precioventa,
		detallecotizaciones.ivaproducto,
		detallecotizaciones.descproducto,
		detallecotizaciones.valortotal, 
		detallecotizaciones.totaldescuentov,
		detallecotizaciones.valorneto,
		detallecotizaciones.valorneto2,
		detallecotizaciones.codsucursal,
		marcas.nommarca,
		modelos.nommodelo
		FROM detallecotizaciones INNER JOIN productos ON detallecotizaciones.codproducto = productos.codproducto 
		INNER JOIN marcas ON productos.codmarca = marcas.codmarca
		LEFT JOIN modelos ON productos.codmodelo = modelos.codmodelo 
		WHERE detallecotizaciones.codcotizacion = ? AND detallecotizaciones.codsucursal = ? GROUP BY productos.codproducto";
		$stmt = $this->dbh->prepare($sql);
		$stmt->execute(array(decrypt($_GET["codcotizacion"]),decrypt($_GET["codsucursal"])));
		$num = $stmt->rowCount();
		
			while($row = $stmt->fetch(PDO::FETCH_ASSOC))
			{
				$this->p[]=$row;
			}
			return $this->p;
			$this->dbh=null;
		}
##################### FUNCION VER DETALLES COTIZACIONES #########################

######################## FUNCION ACTUALIZAR COTIZACIONES #######################
	public function ActualizarCotizaciones()
	{
		self::SetNames();
		if(empty($_POST["codcotizacion"]) or empty($_POST["codsucursal"]))
		{
			echo "1";
			exit;
		}


		for($i=0;$i<count($_POST['coddetallecotizacion']);$i++){  //recorro el array
	        if (!empty($_POST['coddetallecotizacion'][$i])) {

		       if($_POST['cantcotizacion'][$i]==0){

			      echo "2";
			      exit();

		       }
	                                                 }
                                              }

	$this->dbh->beginTransaction();
	for($i=0;$i<count($_POST['coddetallecotizacion']);$i++){  //recorro el array
	if (!empty($_POST['coddetallecotizacion'][$i])) {

	$sql = "SELECT cantcotizacion FROM detallecotizaciones WHERE coddetallecotizacion = '".limpiar($_POST['coddetallecotizacion'][$i])."' AND codcotizacion = '".limpiar($_POST["codcotizacion"])."' AND codsucursal = '".limpiar($_POST["codsucursal"])."'";
		foreach ($this->dbh->query($sql) as $row)
		{
			$this->p[] = $row;
		}
		
		$cantidadbd = $row['cantcotizacion'];

		if($cantidadbd != $_POST['cantcotizacion'][$i]){

			$query = "UPDATE detallecotizaciones set"
			." cantcotizacion = ?, "
			." valortotal = ?, "
			." totaldescuentov = ?, "
			." valorneto = ?, "
			." valorneto2 = ? "
			." WHERE "
			." coddetallecotizacion = ? AND codcotizacion = ? AND codsucursal = ?;
			";
			$stmt = $this->dbh->prepare($query);
			$stmt->bindParam(1, $cantcotizacion);
			$stmt->bindParam(2, $valortotal);
			$stmt->bindParam(3, $totaldescuentov);
			$stmt->bindParam(4, $valorneto);
			$stmt->bindParam(5, $valorneto2);
			$stmt->bindParam(6, $coddetallecotizacion);
			$stmt->bindParam(7, $codcotizacion);
			$stmt->bindParam(8, $codsucursal);

			$cantcotizacion = limpiar($_POST['cantcotizacion'][$i]);
			$preciocompra = limpiar($_POST['preciocompra'][$i]);
			$precioventa = limpiar($_POST['precioventa'][$i]);
			$ivaproducto = limpiar($_POST['ivaproducto'][$i]);
			$descuento = $_POST['descproducto'][$i]/100;
			$valortotal = number_format($_POST['precioventa'][$i] * $_POST['cantcotizacion'][$i], 2, '.', '');
			$totaldescuentov = number_format($valortotal * $descuento, 2, '.', '');
			$valorneto = number_format($valortotal - $totaldescuentov, 2, '.', '');
			$valorneto2 = number_format($_POST['preciocompra'][$i] * $_POST['cantcotizacion'][$i], 2, '.', '');
			$coddetallecotizacion = limpiar($_POST['coddetallecotizacion'][$i]);
			$codcotizacion = limpiar($_POST["codcotizacion"]);
			$codsucursal = limpiar($_POST["codsucursal"]);
			$stmt->execute();

			} else {

               echo "";

		       }
	        }
        }
            $this->dbh->commit();

            ############ SUMO LOS IMPORTE DE PRODUCTOS CON IVA ##############
			$sql3 = "SELECT SUM(valorneto) AS valorneto, SUM(valorneto2) AS valorneto2 FROM detallecotizaciones WHERE codcotizacion = '".limpiar($_POST["codcotizacion"])."' AND codsucursal = '".limpiar($_POST["codsucursal"])."' AND ivaproducto = 'SI'";
			foreach ($this->dbh->query($sql3) as $row3)
			{
				$this->p[] = $row3;
			}
			$subtotalivasi = ($row3['valorneto']== "" ? "0.00" : $row3['valorneto']);
			$subtotalivasi2 = ($row3['valorneto2']== "" ? "0.00" : $row3['valorneto2']);

		    ############ SUMO LOS IMPORTE DE PRODUCTOS SIN IVA ##############
			$sql4 = "SELECT SUM(valorneto) AS valorneto, SUM(valorneto2) AS valorneto2 FROM detallecotizaciones WHERE codcotizacion = '".limpiar($_POST["codcotizacion"])."' AND codsucursal = '".limpiar($_POST["codsucursal"])."' AND ivaproducto = 'NO'";
			foreach ($this->dbh->query($sql4) as $row4)
			{
				$this->p[] = $row4;
			}
			$subtotalivano = ($row4['valorneto']== "" ? "0.00" : $row4['valorneto']);
			$subtotalivano2 = ($row4['valorneto2']== "" ? "0.00" : $row4['valorneto2']);

            ############ ACTUALIZO LOS TOTALES EN LA COTIZACION ##############
			$sql = " UPDATE cotizaciones SET "
			." codcliente = ?, "
			." observaciones = ?, "
			." subtotalivasi = ?, "
			." subtotalivano = ?, "
			." totaliva = ?, "
			." descuento = ?, "
			." totaldescuento = ?, "
			." totalpago = ?, "
			." totalpago2= ? "
			." WHERE "
			." codcotizacion = ? AND codsucursal = ?;
			";
			$stmt = $this->dbh->prepare($sql);
			$stmt->bindParam(1, $codcliente);
			$stmt->bindParam(2, $observaciones);
			$stmt->bindParam(3, $subtotalivasi);
			$stmt->bindParam(4, $subtotalivano);
			$stmt->bindParam(5, $totaliva);
			$stmt->bindParam(6, $descuento);
			$stmt->bindParam(7, $totaldescuento);
			$stmt->bindParam(8, $totalpago);
			$stmt->bindParam(9, $totalpago2);
			$stmt->bindParam(10, $codcotizacion);
			$stmt->bindParam(11, $codsucursal);

			$codcliente = limpiar($_POST["codcliente"]);
			$observaciones = limpiar($_POST["observaciones"]);
			$iva = $_POST["iva"]/100;
			$totaliva = number_format($subtotalivasi*$iva, 2, '.', '');
			$descuento = limpiar($_POST["descuento"]);
		    $txtDescuento = $_POST["descuento"]/100;
		    $total = number_format($subtotalivasi+$subtotalivano+$totaliva, 2, '.', '');
		    $totaldescuento = number_format($total*$txtDescuento, 2, '.', '');
		    $totalpago = number_format($total-$totaldescuento, 2, '.', '');
			$totalpago2 = number_format($subtotalivasi2+$subtotalivano2, 2, '.', '');
			$codcotizacion = limpiar($_POST["codcotizacion"]);
			$codsucursal = limpiar($_POST["codsucursal"]);
			$stmt->execute();


echo "<span class='fa fa-check-square-o'></span> LA COTIZACI&Oacute;N DE PRODUCTOS HA SIDO ACTUALIZADA EXITOSAMENTE <a href='reportepdf?codcotizacion=".encrypt($codcotizacion)."&codsucursal=".encrypt($codsucursal)."&tipo=".encrypt("FACTURACOTIZACION")."' class='on-default' data-placement='left' data-toggle='tooltip' data-original-title='Imprimir Documento' target='_black' rel='noopener noreferrer'><font color='black'><strong>IMPRIMIR REPORTE</strong></font color></a></div>";

echo "<script>window.open('reportepdf?codcotizacion=".encrypt($codcotizacion)."&codsucursal=".encrypt($codsucursal)."&tipo=".encrypt("FACTURACOTIZACION")."', '_blank');</script>";
	exit;
}
####################### FUNCION ACTUALIZAR COTIZACIONES ############################

####################### FUNCION AGREGAR DETALLES COTIZACIONES ########################
	public function AgregarDetallesCotizaciones()
	{
		self::SetNames();
		if(empty($_POST["codcotizacion"]) or empty($_POST["codsucursal"]))
		{
			echo "1";
			exit;
		}


        if(empty($_SESSION["CarritoCotizacion"]))
		{
			echo "2";
			exit;
			
		} else {


	    $this->dbh->beginTransaction();
	    $detalle = $_SESSION["CarritoCotizacion"];
		for($i=0;$i<count($detalle);$i++){

	$sql = "SELECT codcotizacion, codproducto FROM detallecotizaciones WHERE codcotizacion = '".limpiar($_POST['codcotizacion'])."' AND codsucursal = '".limpiar($_POST['codsucursal'])."' AND codproducto = '".limpiar($detalle[$i]['txtCodigo'])."'";
			$stmt = $this->dbh->prepare($sql);
			$stmt->execute();
			$num = $stmt->rowCount();
			if($num == 0)
			{

        $query = "INSERT INTO detallecotizaciones values (null, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?); ";
		$stmt = $this->dbh->prepare($query);
		$stmt->bindParam(1, $codcotizacion);
	    $stmt->bindParam(2, $codproducto);
	    $stmt->bindParam(3, $producto);
		$stmt->bindParam(4, $cantidad);
		$stmt->bindParam(5, $preciocompra);
		$stmt->bindParam(6, $precioventa);
		$stmt->bindParam(7, $ivaproducto);
		$stmt->bindParam(8, $descproducto);
		$stmt->bindParam(9, $valortotal);
		$stmt->bindParam(10, $totaldescuentov);
		$stmt->bindParam(11, $valorneto);
		$stmt->bindParam(12, $valorneto2);
		$stmt->bindParam(13, $codsucursal);
			
		$codcotizacion = limpiar($_POST["codcotizacion"]);
		$codproducto = limpiar($detalle[$i]['txtCodigo']);
		$producto = limpiar($detalle[$i]['producto']);
		$cantidad = limpiar($detalle[$i]['cantidad']);
		$preciocompra = limpiar($detalle[$i]['precio']);
		$precioventa = limpiar($detalle[$i]['precio2']);
		$ivaproducto = limpiar($detalle[$i]['ivaproducto']);
		$descproducto = limpiar($detalle[$i]['descproducto']);
		$descuento = $detalle[$i]['descproducto']/100;
		$valortotal = number_format($detalle[$i]['precio2']*$detalle[$i]['cantidad'], 2, '.', '');
		$totaldescuentov = number_format($valortotal*$descuento, 2, '.', '');
	    $valorneto = number_format($valortotal-$totaldescuentov, 2, '.', '');
		$valorneto2 = number_format($detalle[$i]['precio']*$detalle[$i]['cantidad'], 2, '.', '');
		$codsucursal = limpiar($_SESSION["codsucursal"]);
		$stmt->execute();

	  } else {

	  	$sql = "SELECT cantcotizacion FROM detallecotizaciones WHERE codcotizacion = '".limpiar($_POST['codcotizacion'])."' AND codsucursal = '".limpiar($_POST['codsucursal'])."' AND codproducto = '".limpiar($detalle[$i]['txtCodigo'])."'";
		foreach ($this->dbh->query($sql) as $row)
		{
			$this->p[] = $row;
		}
		$cantidad = $row['cantcotizacion'];

	  	$query = "UPDATE detallecotizaciones set"
		." cantcotizacion = ?, "
		." descproducto = ?, "
		." valortotal = ?, "
		." totaldescuentov = ?, "
		." valorneto = ?, "
		." valorneto2 = ? "
		." WHERE "
		." codcotizacion = ? AND codsucursal = ? AND codproducto = ?;
		";
		$stmt = $this->dbh->prepare($query);
		$stmt->bindParam(1, $cantcotizacion);
		$stmt->bindParam(2, $descproducto);
		$stmt->bindParam(3, $valortotal);
		$stmt->bindParam(4, $totaldescuentov);
		$stmt->bindParam(5, $valorneto);
		$stmt->bindParam(6, $valorneto2);
		$stmt->bindParam(7, $codcotizacion);
		$stmt->bindParam(8, $codsucursal);
		$stmt->bindParam(9, $codproducto);

		$cantcotizacion = limpiar($detalle[$i]['cantidad']+$cantidad);
		$preciocompra = limpiar($detalle[$i]['precio']);
		$precioventa = limpiar($detalle[$i]['precio2']);
		$ivaproducto = limpiar($detalle[$i]['ivaproducto']);
		$descproducto = limpiar($detalle[$i]['descproducto']);
		$descuento = $detalle[$i]['descproducto']/100;
		$valortotal = number_format($detalle[$i]['precio2'] * $cantcotizacion, 2, '.', '');
		$totaldescuentov = number_format($valortotal * $descuento, 2, '.', '');
		$valorneto = number_format($valortotal - $totaldescuentov, 2, '.', '');
		$valorneto2 = number_format($detalle[$i]['precio'] * $cantcotizacion, 2, '.', '');
		$codcotizacion = limpiar($_POST["codcotizacion"]);
		$codsucursal = limpiar($_POST["codsucursal"]);
		$codproducto = limpiar($detalle[$i]['txtCodigo']);
		$stmt->execute();
	 }
   }    
        ####################### DESTRUYO LA VARIABLE DE SESSION #####################
	    unset($_SESSION["CarritoCotizacion"]);
        $this->dbh->commit();

        ############ SUMO LOS IMPORTE DE PRODUCTOS CON IVA ##############
		$sql3 = "SELECT SUM(valorneto) AS valorneto, SUM(valorneto2) AS valorneto2 FROM detallecotizaciones WHERE codcotizacion = '".limpiar($_POST["codcotizacion"])."' AND codsucursal = '".limpiar($_POST["codsucursal"])."' AND ivaproducto = 'SI'";
			foreach ($this->dbh->query($sql3) as $row3)
			{
				$this->p[] = $row3;
			}
			$subtotalivasi = ($row3['valorneto']== "" ? "0.00" : $row3['valorneto']);
			$subtotalivasi2 = ($row3['valorneto2']== "" ? "0.00" : $row3['valorneto2']);

		############ SUMO LOS IMPORTE DE PRODUCTOS SIN IVA ##############
		$sql4 = "SELECT SUM(valorneto) AS valorneto, SUM(valorneto2) AS valorneto2 FROM detallecotizaciones WHERE codcotizacion = '".limpiar($_POST["codcotizacion"])."' AND codsucursal = '".limpiar($_POST["codsucursal"])."' AND ivaproducto = 'NO'";
			foreach ($this->dbh->query($sql4) as $row4)
			{
				$this->p[] = $row4;
			}
			$subtotalivano = ($row4['valorneto']== "" ? "0.00" : $row4['valorneto']);
			$subtotalivano2 = ($row4['valorneto2']== "" ? "0.00" : $row4['valorneto2']);


    ############ ACTUALIZO LOS TOTALES EN LA COTIZACION ##############
			$sql = " UPDATE cotizaciones SET "
			." codcliente = ?, "
			." observaciones = ?, "
			." subtotalivasi = ?, "
			." subtotalivano = ?, "
			." totaliva = ?, "
			." descuento = ?, "
			." totaldescuento = ?, "
			." totalpago = ?, "
			." totalpago2= ? "
			." WHERE "
			." codcotizacion = ? AND codsucursal = ?;
			";
			$stmt = $this->dbh->prepare($sql);
			$stmt->bindParam(1, $codcliente);
			$stmt->bindParam(2, $observaciones);
			$stmt->bindParam(3, $subtotalivasi);
			$stmt->bindParam(4, $subtotalivano);
			$stmt->bindParam(5, $totaliva);
			$stmt->bindParam(6, $descuento);
			$stmt->bindParam(7, $totaldescuento);
			$stmt->bindParam(8, $totalpago);
			$stmt->bindParam(9, $totalpago2);
			$stmt->bindParam(10, $codcotizacion);
			$stmt->bindParam(11, $codsucursal);

			$codcliente = limpiar($_POST["codcliente"]);
			$observaciones = limpiar($_POST["observaciones"]);
			$iva = $_POST["iva"]/100;
			$totaliva = number_format($subtotalivasi*$iva, 2, '.', '');
			$descuento = limpiar($_POST["descuento"]);
		    $txtDescuento = $_POST["descuento"]/100;
		    $total = number_format($subtotalivasi+$subtotalivano+$totaliva, 2, '.', '');
		    $totaldescuento = number_format($total*$txtDescuento, 2, '.', '');
		    $totalpago = number_format($total-$totaldescuento, 2, '.', '');
			$totalpago2 = number_format($subtotalivasi2+$subtotalivano2, 2, '.', '');
			$codcotizacion = limpiar($_POST["codcotizacion"]);
			$codsucursal = limpiar($_POST["codsucursal"]);
			$stmt->execute();
		

echo "<span class='fa fa-check-square-o'></span> LOS DETALLES DE PRODUCTOS FUERON AGREGADOS A LA COTIZACI&Oacute;N EXITOSAMENTE <a href='reportepdf?codcotizacion=".encrypt($codcotizacion)."&codsucursal=".encrypt($codsucursal)."&tipo=".encrypt("FACTURACOTIZACION")."' class='on-default' data-placement='left' data-toggle='tooltip' data-original-title='Imprimir Documento' target='_black' rel='noopener noreferrer'><font color='black'><strong>IMPRIMIR REPORTE</strong></font color></a></div>";

echo "<script>window.open('reportepdf?codcotizacion=".encrypt($codcotizacion)."&codsucursal=".encrypt($codsucursal)."&tipo=".encrypt("FACTURACOTIZACION")."', '_blank');</script>";
	exit;
	}
}
######################### FUNCION AGREGAR DETALLES COTIZACIONES #######################

######################## FUNCION ELIMINAR DETALLES COTIZACIONES #######################
	public function EliminarDetallesCotizaciones()
	{
	self::SetNames();
		if ($_SESSION["acceso"]=="administradorS") {

		$sql = "SELECT * FROM detallecotizaciones WHERE codcotizacion = ? AND codsucursal = ?";
		$stmt = $this->dbh->prepare($sql);
		$stmt->execute(array(decrypt($_GET["codcotizacion"]),decrypt($_GET["codsucursal"])));
		$num = $stmt->rowCount();
		if($num > 1)
		{

			$sql = "DELETE FROM detallecotizaciones WHERE coddetallecotizacion = ? AND codsucursal = ?";
			$stmt = $this->dbh->prepare($sql);
			$stmt->bindParam(1,$coddetallecotizacion);
			$stmt->bindParam(2,$codsucursal);
			$coddetallecotizacion = decrypt($_GET["coddetallecotizacion"]);
			$codsucursal = decrypt($_GET["codsucursal"]);
			$stmt->execute();

		    ############ CONSULTO LOS TOTALES DE COTIZACIONES ##############
		    $sql2 = "SELECT iva, descuento FROM cotizaciones WHERE codcotizacion = ? AND codsucursal = ?";
		    $stmt = $this->dbh->prepare($sql2);
		    $stmt->execute(array(decrypt($_GET["codcotizacion"]),decrypt($_GET["codsucursal"])));
		    $num = $stmt->rowCount();

			if($row = $stmt->fetch(PDO::FETCH_ASSOC))
			{
				$paea[] = $row;
			}
			$iva = $paea[0]["iva"]/100;
		    $descuento = $paea[0]["descuento"]/100;

            ############ SUMO LOS IMPORTE DE PRODUCTOS CON IVA ##############
			$sql3 = "SELECT SUM(valorneto) AS valorneto, SUM(valorneto2) AS valorneto2 FROM detallecotizaciones WHERE codcotizacion = '".limpiar(decrypt($_GET["codcotizacion"]))."' AND codsucursal = '".limpiar(decrypt($_GET["codsucursal"]))."' AND ivaproducto = 'SI'";
			foreach ($this->dbh->query($sql3) as $row3)
			{
				$this->p[] = $row3;
			}
			$subtotalivasi = ($row3['valorneto']== "" ? "0.00" : $row3['valorneto']);
			$subtotalivasi2 = ($row3['valorneto2']== "" ? "0.00" : $row3['valorneto2']);

		    ############ SUMO LOS IMPORTE DE PRODUCTOS SIN IVA ##############
			$sql4 = "SELECT SUM(valorneto) AS valorneto, SUM(valorneto2) AS valorneto2 FROM detallecotizaciones WHERE codcotizacion = '".limpiar(decrypt($_GET["codcotizacion"]))."' AND codsucursal = '".limpiar(decrypt($_GET["codsucursal"]))."' AND ivaproducto = 'NO'";
			foreach ($this->dbh->query($sql4) as $row4)
			{
				$this->p[] = $row4;
			}
			$subtotalivano = ($row4['valorneto']== "" ? "0.00" : $row4['valorneto']);
			$subtotalivano2 = ($row4['valorneto2']== "" ? "0.00" : $row4['valorneto2']);

            ############ ACTUALIZO LOS TOTALES EN LA COTIZACION ##############
			$sql = " UPDATE cotizaciones SET "
			." subtotalivasi = ?, "
			." subtotalivano = ?, "
			." totaliva = ?, "
			." totaldescuento = ?, "
			." totalpago = ?, "
			." totalpago2= ? "
			." WHERE "
			." codcotizacion = ? AND codsucursal = ?;
			";
			$stmt = $this->dbh->prepare($sql);
			$stmt->bindParam(1, $subtotalivasi);
			$stmt->bindParam(2, $subtotalivano);
			$stmt->bindParam(3, $totaliva);
			$stmt->bindParam(4, $totaldescuento);
			$stmt->bindParam(5, $totalpago);
			$stmt->bindParam(6, $totalpago2);
			$stmt->bindParam(7, $codcotizacion);
			$stmt->bindParam(8, $codsucursal);

			$totaliva= number_format($subtotalivasi*$iva, 2, '.', '');
		    $total= number_format($subtotalivasi+$subtotalivano+$totaliva, 2, '.', '');
		    $totaldescuento= number_format($total*$descuento, 2, '.', '');
		    $totalpago= number_format($total-$totaldescuento, 2, '.', '');
			$totalpago2 = number_format($subtotalivasi+$subtotalivano, 2, '.', '');
			$codcotizacion = limpiar(decrypt($_GET["codcotizacion"]));
			$codsucursal = limpiar(decrypt($_GET["codsucursal"]));
			$stmt->execute();

			echo "1";
			exit;

		} else {
		   
			echo "2";
			exit;
		  } 
			
		} else {
		
		echo "3";
		exit;
	 }	
}
################### FUNCION ELIMINAR DETALLES COTIZACIONES #####################

####################### FUNCION ELIMINAR COTIZACIONES #################################
	public function EliminarCotizaciones()
	{
	self::SetNames();
		if ($_SESSION["acceso"]=="administradorS") {

			$sql = "DELETE FROM cotizaciones WHERE codcotizacion = ? AND codsucursal = ?";
			$stmt = $this->dbh->prepare($sql);
			$stmt->bindParam(1,$codcotizacion);
			$stmt->bindParam(2,$codsucursal);
			$codcotizacion = decrypt($_GET["codcotizacion"]);
			$codsucursal = decrypt($_GET["codsucursal"]);
			$stmt->execute();

			$sql = "DELETE FROM detallecotizaciones WHERE codcotizacion = ? AND codsucursal = ?";
			$stmt = $this->dbh->prepare($sql);
			$stmt->bindParam(1,$codcotizacion);
			$stmt->bindParam(2,$codsucursal);
			$codcotizacion = decrypt($_GET["codcotizacion"]);
			$codsucursal = decrypt($_GET["codsucursal"]);
			$stmt->execute();

			echo "1";
			exit;

		} else {

			echo "2";
			exit;
		}
	}
###################### FUNCION ELIMINAR COTIZACIONES #################################

####################### FUNCION PROCESAR COTIZACIONES A VENTA #################################
public function ProcesarCotizaciones()
	{
	self::SetNames();
		$sql = "SELECT * FROM arqueocaja INNER JOIN cajas ON arqueocaja.codcaja = cajas.codcaja INNER JOIN usuarios ON cajas.codigo = usuarios.codigo WHERE usuarios.codigo = ? AND arqueocaja.statusarqueo = 1";
		$stmt = $this->dbh->prepare($sql);
		$stmt->execute(array($_SESSION["codigo"]));
		$num = $stmt->rowCount();
		if($num==0)
		{
			echo "1";
			exit;
	    }
	    else if(empty($_POST["codsucursal"]) or empty($_POST["tipodocumento"]) or empty($_POST["tipopago"]))
		{
			echo "2";
			exit;
		}
		elseif(limpiar($_POST["txtTotal"]=="") && limpiar($_POST["txtTotal"]==0) && limpiar($_POST["txtTotal"]==0.00))
		{
			echo "3";
			exit;
			
		}

		################### SELECCIONE LOS DATOS DEL CLIENTE ######################
	    $sql = "SELECT
	    clientes.nomcliente, 
	    clientes.emailcliente, 
	    clientes.limitecredito,
	    ROUND(SUM(if(pag.montocredito!='0',pag.montocredito,'0.00')), 2) montoactual,
	    ROUND(SUM(if(pag.montocredito!='0',clientes.limitecredito-pag.montocredito,clientes.limitecredito)), 2) creditodisponible
        FROM clientes 
        LEFT JOIN
           (SELECT
           codcliente, montocredito       
           FROM creditosxclientes 
           WHERE codsucursal = '".limpiar($_POST['codsucursal'])."') pag ON pag.codcliente = clientes.codcliente
           WHERE clientes.codcliente = ?";
		$stmt = $this->dbh->prepare($sql);
		$stmt->execute(array($_POST['codcliente']));
		$num = $stmt->rowCount();
		if($row = $stmt->fetch(PDO::FETCH_ASSOC))
		{
			$p[] = $row;
		}
        $nomcliente = $row['nomcliente'];
        $emailcliente = $row['emailcliente'];
        $limitecredito = $row['limitecredito'];
        $montoactual = $row['montoactual'];
        $creditodisponible = $row['creditodisponible'];
        $montoabono = (empty($_POST["montoabono"]) ? "0.00" : $_POST["montoabono"]);
        $total = number_format($_POST["txtTotal"]-$montoabono, 2, '.', '');
		
		if (limpiar(isset($_POST['fechavencecredito']))) {  

			$fechaactual = date("Y-m-d");
			$fechavence = date("Y-m-d",strtotime($_POST['fechavencecredito']));

			if (strtotime($fechavence) < strtotime($fechaactual)) {

				echo "4";
				exit;
			}
		}

		if ($_POST["tipopago"] == "CREDITO" && $_POST["codcliente"] == '0') { 

		        echo "5";
		        exit;

	    } else if ($_POST["tipopago"] == "CREDITO") {

		    if ($limitecredito != "0.00" && $total > $creditodisponible) {
	  
	           echo "6";
		       exit;

	        } 

	    } else if($_POST["tipopago"]=="CREDITO" && $_POST["montoabono"] >= $_POST["txtTotal"]) {
			echo "7";
			exit;
			
		} 

		############ VALIDO SI LA CANTIDAD ES MAYOR QUE LA EXISTENCIA #############
		$sql = "SELECT * FROM detallecotizaciones WHERE codcotizacion = '".decrypt($_POST['codcotizacion'])."' 
		AND codsucursal = '".decrypt($_POST['codsucursal'])."'";
        	foreach ($this->dbh->query($sql) as $row2) {

		    $sql = "SELECT existencia FROM productos WHERE codproducto = '".limpiar($row2['codproducto'])."' AND codsucursal = '".limpiar(decrypt($_POST["codsucursal"]))."'";
		    foreach ($this->dbh->query($sql) as $row)
		    {
			$this->p[] = $row;
		    }
		
		    $existenciadb = $row['existencia'];
		    $cantidad = $row2['cantcotizacion'];

            if ($cantidad > $existenciadb) 
            { 
		       echo "8";
		       exit;
	        }
		}

		################ CREO LOS CODIGO VENTA-SERIE-AUTORIZACION ####################
		$sql = " SELECT codsucursal, nroactividadsucursal, iniciofactura FROM sucursales WHERE codsucursal = '".limpiar(decrypt($_POST["codsucursal"]))."'";
		foreach ($this->dbh->query($sql) as $row)
		{
			$this->p[] = $row;
		}
		$nroactividad = $row['nroactividadsucursal'];
		$iniciofactura = $row['iniciofactura'];
		
		$sql = "SELECT codventa FROM ventas WHERE codsucursal = '".limpiar(decrypt($_POST["codsucursal"]))."' ORDER BY idventa DESC LIMIT 1";
		foreach ($this->dbh->query($sql) as $row){

			$venta=$row["codventa"];

		}
		if(empty($venta))
		{
			$codventa = $nroactividad.'-'.$iniciofactura;
			$codserie = limpiar($nroactividad);
			$codautorizacion = limpiar(GenerateRandomStringg());

		} else {

			$var = strlen($nroactividad."-");
            $var1 = substr($venta , $var);
            $var2 = strlen($var1);
            $var3 = $var1 + 1;
            $var4 = str_pad($var3, $var2, "0", STR_PAD_LEFT);
            $codventa = $nroactividad.'-'.$var4;
			$codserie = limpiar($nroactividad);
			$codautorizacion = limpiar(GenerateRandomStringg());
		}
        ################ CREO LOS CODIGO VENTA-SERIE-AUTORIZACION ###############

		################### SELECCIONE LOS DATOS DE LA COTIZACION ######################
	    $sql = "SELECT * FROM cotizaciones WHERE codcotizacion = ? AND codsucursal = ?";
		$stmt = $this->dbh->prepare($sql);
		$stmt->execute(array(decrypt($_POST['codcotizacion']),decrypt($_POST['codsucursal'])));
		$num = $stmt->rowCount();
		if($row = $stmt->fetch(PDO::FETCH_ASSOC))
		{
			$p[] = $row;
		}
        ################### SELECCIONE LOS DATOS DE LA COTIZACION ######################

        $fecha = date("Y-m-d h:i:s");

        $query = "INSERT INTO ventas values (null, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);";
		$stmt = $this->dbh->prepare($query);
		$stmt->bindParam(1, $tipodocumento);
		$stmt->bindParam(2, $codcaja);
		$stmt->bindParam(3, $codventa);
		$stmt->bindParam(4, $codserie);
		$stmt->bindParam(5, $codautorizacion);
		$stmt->bindParam(6, $codcliente);
		$stmt->bindParam(7, $subtotalivasi);
		$stmt->bindParam(8, $subtotalivano);
		$stmt->bindParam(9, $iva);
		$stmt->bindParam(10, $totaliva);
		$stmt->bindParam(11, $descuento);
		$stmt->bindParam(12, $totaldescuento);
		$stmt->bindParam(13, $totalpago);
		$stmt->bindParam(14, $totalpago2);
		$stmt->bindParam(15, $creditopagado);
		$stmt->bindParam(16, $tipopago);
		$stmt->bindParam(17, $formapago);
		$stmt->bindParam(18, $montopagado);
		$stmt->bindParam(19, $montodevuelto);
		$stmt->bindParam(20, $fechavencecredito);
		$stmt->bindParam(21, $fechapagado);
		$stmt->bindParam(22, $statusventa);
		$stmt->bindParam(23, $fechaventa);
		$stmt->bindParam(24, $observaciones);
		$stmt->bindParam(25, $codigo);
		$stmt->bindParam(26, $codsucursal);
	    
		$tipodocumento = limpiar($_POST["tipodocumento"]);
		$codcaja = limpiar($_POST["codcaja"]);
		$codcliente = limpiar($_POST['codcliente']);
		$subtotalivasi = limpiar($row["subtotalivasi"]);
		$subtotalivano = limpiar($row["subtotalivano"]);
		$iva = limpiar($row["iva"]);
		$totaliva = limpiar($row["totaliva"]);
		$descuento = limpiar($row["descuento"]);
		$totaldescuento = limpiar($row["totaldescuento"]);
		$totalpago = limpiar($row["totalpago"]);
		$totalpago2 = limpiar($row["totalpago2"]);
		if (limpiar(isset($_POST['montoabono']))) { $creditopagado = limpiar($_POST['montoabono']); } else { $creditopagado ='0.00'; }
		$tipopago = limpiar($_POST["tipopago"]);
		$formapago = limpiar($_POST["tipopago"]=="CONTADO" ? decrypt($_POST["codmediopago"]) : "CREDITO");
	if (limpiar(isset($_POST['montopagado']))) { $montopagado = limpiar($_POST['montopagado']); } else { $montopagado ='0.00'; }
	if (limpiar(isset($_POST['montodevuelto']))) { $montodevuelto = limpiar($_POST['montodevuelto']); } else { $montodevuelto ='0.00'; }
		$fechavencecredito = limpiar($_POST["tipopago"]=="CREDITO" ? date("Y-m-d",strtotime($_POST['fechavencecredito'])) : "0000-00-00");
	    $fechapagado = limpiar("0000-00-00");
	    $statusventa = limpiar($_POST["tipopago"]=="CONTADO" ? "PAGADA" : "PENDIENTE");
        $fechaventa = limpiar($fecha);
		$observaciones = limpiar($_POST["observaciones"]);
		$codigo = limpiar($_SESSION["codigo"]);
		$codsucursal = limpiar(decrypt($_POST["codsucursal"]));
		$stmt->execute();

		################### SELECCIONO DETALLES DE LA COTIZACION ######################
		$sql = "SELECT * FROM detallecotizaciones 
		WHERE codcotizacion = '".decrypt($_POST['codcotizacion'])."' 
		AND codsucursal = '".decrypt($_POST['codsucursal'])."'";
        	foreach ($this->dbh->query($sql) as $row2)
		    {

	    	############### VERIFICO LA EXISTENCIA DEL PRODUCTO EN ALMACEN ##################
		    $sql = "SELECT existencia FROM productos WHERE codproducto = '".limpiar($row2['codproducto'])."' AND codsucursal = '".limpiar(decrypt($_POST['codsucursal']))."'";
		    foreach ($this->dbh->query($sql) as $row3)
		    {
			$this->p[] = $row3;
		    }
		    $existenciabd = $row3['existencia'];

		    $query = "INSERT INTO detalleventas values (null, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?); ";
	    	$stmt = $this->dbh->prepare($query);
	    	$stmt->bindParam(1, $codventa);
	    	$stmt->bindParam(2, $codproducto);
	    	$stmt->bindParam(3, $producto);
	    	$stmt->bindParam(4, $cantidad);
	    	$stmt->bindParam(5, $preciocompra);
	    	$stmt->bindParam(6, $precioventa);
	    	$stmt->bindParam(7, $ivaproducto);
	    	$stmt->bindParam(8, $descproducto);
	    	$stmt->bindParam(9, $valortotal);
	    	$stmt->bindParam(10, $totaldescuentov);
	    	$stmt->bindParam(11, $valorneto);
	    	$stmt->bindParam(12, $valorneto2);
	    	$stmt->bindParam(13, $codsucursal);

	    	$codproducto = limpiar($row2['codproducto']);
	    	$producto = limpiar($row2['producto']);
	    	$cantidad = limpiar($row2['cantcotizacion']);
	    	$preciocompra = limpiar($row2['preciocompra']);
	    	$precioventa = limpiar($row2['precioventa']);
	    	$ivaproducto = limpiar($row2['ivaproducto']);
	    	$descproducto = limpiar($row2['descproducto']);
	    	$descuento = $row2['descproducto']/100;
	    	$valortotal = number_format($row2['valortotal'], 2, '.', '');
	    	$totaldescuentov = number_format($row2['totaldescuentov'], 2, '.', '');
	    	$valorneto = number_format($row2['valorneto'], 2, '.', '');
	    	$valorneto2 = number_format($row2['valorneto2'], 2, '.', '');
	    	$codsucursal = limpiar(decrypt($_POST["codsucursal"]));
	    	$stmt->execute();

            ##################### ACTUALIZO LA EXISTENCIA DEL ALMACEN ####################
	    	$sql = " UPDATE productos set "
	    	." existencia = ? "
	    	." where "
	    	." codproducto = '".limpiar($row2['codproducto'])."' AND codsucursal = '".limpiar(decrypt($_POST["codsucursal"]))."';
	    	";
	    	$stmt = $this->dbh->prepare($sql);
	    	$stmt->bindParam(1, $existencia);
	    	$cantventa = limpiar($row2['cantcotizacion']);
	    	$existencia = $existenciabd-$cantventa;
	    	$stmt->execute();

	        ############### REGISTRAMOS LOS DATOS DE PRODUCTOS EN KARDEX ###############
	    	$query = "INSERT INTO kardex values (null, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?); ";
	    	$stmt = $this->dbh->prepare($query);
	    	$stmt->bindParam(1, $codventa);
	    	$stmt->bindParam(2, $codcliente);
	    	$stmt->bindParam(3, $codproducto);
	    	$stmt->bindParam(4, $movimiento);
	    	$stmt->bindParam(5, $entradas);
	    	$stmt->bindParam(6, $salidas);
	    	$stmt->bindParam(7, $devolucion);
	    	$stmt->bindParam(8, $stockactual);
	    	$stmt->bindParam(9, $ivaproducto);
	    	$stmt->bindParam(10, $descproducto);
	    	$stmt->bindParam(11, $precio);
	    	$stmt->bindParam(12, $documento);
	    	$stmt->bindParam(13, $fechakardex);		
	    	$stmt->bindParam(14, $codsucursal);

	    	$codcliente = limpiar($_POST["codcliente"]);
	    	$codproducto = limpiar($row2['codproducto']);
	    	$movimiento = limpiar("SALIDAS");
	    	$entradas = limpiar("0");
	    	$salidas= limpiar($row2['cantcotizacion']);
	    	$devolucion = limpiar("0");
	    	$stockactual = limpiar($existenciabd-$row2['cantcotizacion']);
	    	$precio = limpiar($row2["precioventa"]);
	    	$ivaproducto = limpiar($row2['ivaproducto']);
	    	$descproducto = limpiar($row2['descproducto']);
	    	$documento = limpiar("VENTA: ".$codventa);
	    	$fechakardex = limpiar(date("Y-m-d"));
	    	$codsucursal = limpiar(decrypt($_POST["codsucursal"]));
	    	$stmt->execute();

		}
		################### SELECCIONO DETALLES DE LA COTIZACION ######################

		################ AGREGAMOS EL INGRESO DE VENTAS PAGADAS A CAJA ##############
	if (limpiar($_POST["tipopago"]=="CONTADO")){

		$sql = "SELECT ingresos FROM arqueocaja WHERE codcaja = '".limpiar($_POST["codcaja"])."' AND statusarqueo = 1";
		foreach ($this->dbh->query($sql) as $row)
		{
			$this->p[] = $row;
		}
		$ingreso = ($row['ingresos']== "" ? "0.00" : $row['ingresos']);

		$sql = "UPDATE arqueocaja set "
		." ingresos = ? "
		." WHERE "
		." codcaja = ? AND statusarqueo = 1;
		";
		$stmt = $this->dbh->prepare($sql);
		$stmt->bindParam(1, $txtTotal);
		$stmt->bindParam(2, $codcaja);

		$txtTotal = number_format($_POST["txtTotal"]+$ingreso, 2, '.', '');
		$codcaja = limpiar($_POST["codcaja"]);
		$stmt->execute();
	}
    ################ AGREGAMOS EL INGRESO DE VENTAS PAGADAS A CAJA ################

    ########## AGREGAMOS EL INGRESO Y ABONOS DE VENTAS A CREDITOS A CAJA ##########
	if (limpiar($_POST["tipopago"]=="CREDITO" && $_POST["montoabono"]=="0.00" && $_POST["montoabono"]=="0")) {

		$sql = "SELECT creditos FROM arqueocaja WHERE codcaja = '".limpiar($_POST["codcaja"])."' AND statusarqueo = 1";
		foreach ($this->dbh->query($sql) as $row)
		{
			$this->p[] = $row;
		}
		$credito = ($row['creditos']== "" ? "0.00" : $row['creditos']);

		$sql = " UPDATE arqueocaja SET "
		." creditos = ? "
		." where "
		." codcaja = ? and statusarqueo = 1;
		";
		$stmt = $this->dbh->prepare($sql);
		$stmt->bindParam(1, $txtTotal);
		$stmt->bindParam(2, $codcaja);

		$txtTotal = number_format($_POST["txtTotal"]+$credito, 2, '.', '');
		$codcaja = limpiar($_POST["codcaja"]);
		$stmt->execute(); 

		$sql = " SELECT codcliente FROM creditosxclientes WHERE codcliente = ? AND codsucursal = ?";
		$stmt = $this->dbh->prepare($sql);
		$stmt->execute(array($_POST["codcliente"],decrypt($_POST["codsucursal"])));
		$num = $stmt->rowCount();
		if($num == 0)
		{
			$query = "INSERT INTO creditosxclientes values (null, ?, ?, ?);";
			$stmt = $this->dbh->prepare($query);
			$stmt->bindParam(1, $codcliente);
			$stmt->bindParam(2, $montocredito);
			$stmt->bindParam(3, $codsucursal);

			$codcliente = limpiar($_POST["codcliente"]);
			$montocredito = number_format($_POST["txtTotal"]-$_POST["montoabono"], 2, '.', '');
			$codsucursal = limpiar(decrypt($_POST["codsucursal"]));
			$stmt->execute();

		} else { 

			$sql = "UPDATE creditosxclientes set"
			." montocredito = ? "
			." where "
			." codcliente = ? AND codsucursal = ?;
			";
			$stmt = $this->dbh->prepare($sql);
			$stmt->bindParam(1, $montocredito);
			$stmt->bindParam(2, $codcliente);
			$stmt->bindParam(3, $codsucursal);

			$montocredito = number_format($montoactual+($_POST["txtTotal"]-$_POST["montoabono"]), 2, '.', '');
			$codcliente = limpiar($_POST["codcliente"]);
			$codsucursal = limpiar(decrypt($_POST["codsucursal"]));
			$stmt->execute();
		}

	} else if (limpiar($_POST["tipopago"]=="CREDITO" && $_POST["montoabono"]!="0.00" && $_POST["montoabono"]!="0")) { 

		$sql = "SELECT creditos, abonos FROM arqueocaja WHERE codcaja = '".limpiar($_POST["codcaja"])."' AND statusarqueo = 1";
		foreach ($this->dbh->query($sql) as $row)
		{
			$this->p[] = $row;
		}
		$credito = ($row['creditos']== "" ? "0.00" : $row['creditos']);
		$abono = ($row['abonos']== "" ? "0.00" : $row['abonos']);

		$sql = " UPDATE arqueocaja SET "
		." creditos = ?, "
		." abonos = ? "
		." where "
		." codcaja = ? and statusarqueo = 1;
		";
		$stmt = $this->dbh->prepare($sql);
		$stmt->bindParam(1, $txtTotal);
		$stmt->bindParam(2, $totalabono);
		$stmt->bindParam(3, $codcaja);

		$txtTotal = number_format($_POST["txtTotal"]+$credito, 2, '.', '');
		$totalabono = number_format($_POST["montoabono"]+$abono, 2, '.', '');
		$codcaja = limpiar($_POST["codcaja"]);
		$stmt->execute();

		$query = "INSERT INTO abonoscreditosventas values (null, ?, ?, ?, ?, ?, ?); ";
		$stmt = $this->dbh->prepare($query);
		$stmt->bindParam(1, $codcaja);
		$stmt->bindParam(2, $codventa);
		$stmt->bindParam(3, $codcliente);
		$stmt->bindParam(4, $montoabono);
		$stmt->bindParam(5, $fechaabono);
		$stmt->bindParam(6, $codsucursal);

		$codcaja = limpiar($_POST["codcaja"]);
		$codcliente = limpiar($_POST["codcliente"]);
		$montoabono = number_format($_POST["montoabono"], 2, '.', '');
		$fechaabono = limpiar($fecha);
		$codsucursal = limpiar(decrypt($_POST["codsucursal"]));
		$stmt->execute();

		$sql = " SELECT codcliente FROM creditosxclientes WHERE codcliente = ? AND codsucursal = ?";
		$stmt = $this->dbh->prepare($sql);
		$stmt->execute(array($_POST["codcliente"],decrypt($_POST["codsucursal"])));
		$num = $stmt->rowCount();
		if($num == 0)
		{
			$query = "INSERT INTO creditosxclientes values (null, ?, ?, ?);";
			$stmt = $this->dbh->prepare($query);
			$stmt->bindParam(1, $codcliente);
			$stmt->bindParam(2, $montocredito);
			$stmt->bindParam(3, $codsucursal);

			$codcliente = limpiar($_POST["codcliente"]);
			$montocredito = number_format($_POST["txtTotal"]-$_POST["montoabono"], 2, '.', '');
			$codsucursal = limpiar(decrypt($_POST["codsucursal"]));
			$stmt->execute();

		} else { 

			$sql = "UPDATE creditosxclientes set"
			." montocredito = ? "
			." where "
			." codcliente = ? AND codsucursal = ?;
			";
			$stmt = $this->dbh->prepare($sql);
			$stmt->bindParam(1, $montocredito);
			$stmt->bindParam(2, $codcliente);
			$stmt->bindParam(3, $codsucursal);

			$montocredito = number_format($montoactual+($_POST["txtTotal"]-$_POST["montoabono"]), 2, '.', '');
			$codcliente = limpiar($_POST["codcliente"]);
			$codsucursal = limpiar(decrypt($_POST["codsucursal"]));
			$stmt->execute();
		}
	}
    ########### AGREGAMOS EL INGRESO Y ABONOS DE VENTAS A CREDITOS A CAJA #########
		
		$sql = "DELETE FROM cotizaciones WHERE codcotizacion = ? AND codsucursal = ?";
		$stmt = $this->dbh->prepare($sql);
		$stmt->bindParam(1,$codcotizacion);
		$stmt->bindParam(2,$codsucursal);
		$codcotizacion = decrypt($_POST["codcotizacion"]);
		$codsucursal = decrypt($_POST["codsucursal"]);
		$stmt->execute();

		$sql = "DELETE FROM detallecotizaciones WHERE codcotizacion = ? AND codsucursal = ?";
		$stmt = $this->dbh->prepare($sql);
	    $stmt->bindParam(1,$codcotizacion);
		$stmt->bindParam(2,$codsucursal);
		$codcotizacion = decrypt($_POST["codcotizacion"]);
		$codsucursal = decrypt($_POST["codsucursal"]);
		$stmt->execute();

echo "<span class='fa fa-check-square-o'></span> LA COTIZACION HA SIDO PROCESADA COMO VENTA EXITOSAMENTE <a href='reportepdf?codventa=".encrypt($codventa)."&codsucursal=".encrypt($codsucursal)."&tipo=".encrypt($tipodocumento)."' class='on-default' data-placement='left' data-toggle='tooltip' data-original-title='Imprimir Documento' target='_black' rel='noopener noreferrer'><font color='black'><strong>IMPRIMIR REPORTE</strong></font color></a></div>";

echo "<script>window.open('reportepdf?codventa=".encrypt($codventa)."&codsucursal=".encrypt($codsucursal)."&tipo=".encrypt($tipodocumento)."', '_blank');</script>";
	exit;
}
###################### FUNCION PROCESAR COTIZACIONES A VENTAS #################################

###################### FUNCION BUSQUEDA COTIZACIONES POR FECHAS ####################
	public function BuscarCotizacionesxFechas() 
	{
		self::SetNames();
		$sql ="SELECT 
		cotizaciones.idcotizacion, 
		cotizaciones.codcotizacion,
		cotizaciones.codcliente, 
		cotizaciones.subtotalivasi,
		cotizaciones.subtotalivano, 
		cotizaciones.iva,
		cotizaciones.totaliva, 
		cotizaciones.descuento,
		cotizaciones.totaldescuento, 
		cotizaciones.totalpago, 
		cotizaciones.totalpago2,
		cotizaciones.observaciones,
		cotizaciones.fechacotizacion,
		cotizaciones.codsucursal,
		sucursales.documsucursal, 
		sucursales.cuitsucursal, 
		sucursales.razonsocial,
		sucursales.documencargado,
		sucursales.dniencargado,
		sucursales.nomencargado,
		clientes.documcliente,
		clientes.dnicliente, 
		clientes.nomcliente, 
		clientes.tlfcliente, 
		clientes.id_provincia, 
		clientes.id_departamento, 
		clientes.direccliente, 
		clientes.emailcliente,
		documentos.documento,
		documentos2.documento AS documento2, 
		documentos3.documento AS documento3,
		provincias.provincia,
		departamentos.departamento,
		SUM(detallecotizaciones.cantcotizacion) as articulos 
		FROM (cotizaciones LEFT JOIN detallecotizaciones ON detallecotizaciones.codcotizacion=cotizaciones.codcotizacion)
		INNER JOIN sucursales ON cotizaciones.codsucursal=sucursales.codsucursal
		LEFT JOIN documentos ON sucursales.documsucursal = documentos.coddocumento
		LEFT JOIN documentos AS documentos2 ON sucursales.documencargado = documentos2.coddocumento 
		LEFT JOIN clientes ON cotizaciones.codcliente = clientes.codcliente
		LEFT JOIN documentos AS documentos3 ON clientes.documcliente = documentos3.coddocumento
		LEFT JOIN provincias ON clientes.id_provincia = provincias.id_provincia 
		LEFT JOIN departamentos ON clientes.id_departamento = departamentos.id_departamento
		 WHERE cotizaciones.codsucursal = ? AND DATE_FORMAT(cotizaciones.fechacotizacion,'%Y-%m-%d') >= ? AND DATE_FORMAT(cotizaciones.fechacotizacion,'%Y-%m-%d') <= ? GROUP BY detallecotizaciones.codcotizacion";
		$stmt = $this->dbh->prepare($sql);
		$stmt->bindValue(1, trim(decrypt($_GET['codsucursal'])));
		$stmt->bindValue(2, trim(date("Y-m-d",strtotime($_GET['desde']))));
		$stmt->bindValue(3, trim(date("Y-m-d",strtotime($_GET['hasta']))));
		$stmt->execute();
		$num = $stmt->rowCount();
		if($num==0)
		{
	echo "<div class='alert alert-danger'>";
	echo "<button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>";
	echo "<center><span class='fa fa-info-circle'></span> NO SE ENCONTRARON COTIZACIONES PARA EL RANGO DE FECHA INGRESADO</center>";
	echo "</div>";		
	exit;
		}
		else
		{
			while($row = $stmt->fetch(PDO::FETCH_ASSOC))
			{
				$this->p[]=$row;
			}
			return $this->p;
			$this->dbh=null;
		}
	}
################### FUNCION BUSQUEDA COTIZACIONES POR FECHAS ###################

###################### FUNCION BUSCAR PRODUCTOS COTIZADOS #########################
public function BuscarProductosCotizados() 
	{
		self::SetNames();
       $sql ="SELECT 
       productos.codproducto, 
       productos.codmarca,  
       productos.existencia,
       detallecotizaciones.codproducto,
       detallecotizaciones.producto,
       detallecotizaciones.descproducto,
       detallecotizaciones.ivaproducto, 
       detallecotizaciones.precioventa, 
       marcas.nommarca, 
       modelos.nommodelo, 
       cotizaciones.fechacotizacion, 
       sucursales.cuitsucursal, 
       sucursales.razonsocial, 
       SUM(detallecotizaciones.cantcotizacion) as cantidad 
       FROM (cotizaciones INNER JOIN detallecotizaciones ON cotizaciones.codcotizacion=detallecotizaciones.codcotizacion) 
       INNER JOIN sucursales ON cotizaciones.codsucursal=sucursales.codsucursal 
       INNER JOIN productos ON detallecotizaciones.codproducto=productos.codproducto 
       LEFT JOIN marcas ON marcas.codmarca=productos.codmarca 
       LEFT JOIN modelos ON modelos.codmodelo=productos.codmodelo 
       WHERE cotizaciones.codsucursal = '".decrypt($_GET['codsucursal'])."' 
       AND productos.codsucursal = '".decrypt($_GET['codsucursal'])."' 
       AND DATE_FORMAT(cotizaciones.fechacotizacion,'%Y-%m-%d') >= ? 
       AND DATE_FORMAT(cotizaciones.fechacotizacion,'%Y-%m-%d') <= ? 
       GROUP BY detallecotizaciones.codproducto, detallecotizaciones.precioventa, detallecotizaciones.descproducto 
       ORDER BY productos.codproducto ASC";
		$stmt = $this->dbh->prepare($sql);
		$stmt->bindValue(1, trim(date("Y-m-d",strtotime($_GET['desde']))));
		$stmt->bindValue(2, trim(date("Y-m-d",strtotime($_GET['hasta']))));
		$stmt->execute();
		$num = $stmt->rowCount();
		if($num==0)
		{
		echo "<div class='alert alert-danger'>";
		echo "<button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>";
		echo "<center><span class='fa fa-info-circle'></span> NO EXISTEN PRODUCTOS COTIZADOS PARA EL RANGO DE FECHA INGRESADA</center>";
		echo "</div>";		
		exit;
		}
		else
		{
			while($row = $stmt->fetch(PDO::FETCH_ASSOC))
			{
				$this->p[]=$row;
			}
			return $this->p;
			$this->dbh=null;
		}
	}
########################### FUNCION PRODUCTOS COTIZADOS ###############################

###################### FUNCION BUSCAR PRODUCTOS COTIZADOS POR VENDEDOR #########################
public function BuscarCotizacionesxVendedor() 
	{
		self::SetNames();
       $sql ="SELECT 
       productos.codproducto, 
       productos.codmarca,  
       productos.existencia,
       detallecotizaciones.codproducto,
       detallecotizaciones.producto,
       detallecotizaciones.descproducto, 
       detallecotizaciones.ivaproducto,
       detallecotizaciones.precioventa, 
       marcas.nommarca, 
       modelos.nommodelo, 
       cotizaciones.fechacotizacion, 
       sucursales.cuitsucursal, 
       sucursales.razonsocial,
       usuarios.dni,
       usuarios.nombres, 
       SUM(detallecotizaciones.cantcotizacion) as cantidad 
       FROM (cotizaciones INNER JOIN detallecotizaciones ON cotizaciones.codcotizacion=detallecotizaciones.codcotizacion) 
       INNER JOIN sucursales ON cotizaciones.codsucursal=sucursales.codsucursal  
       INNER JOIN usuarios ON cotizaciones.codigo = usuarios.codigo 
       INNER JOIN productos ON detallecotizaciones.codproducto=productos.codproducto 
       LEFT JOIN marcas ON marcas.codmarca=productos.codmarca 
       LEFT JOIN modelos ON modelos.codmodelo=productos.codmodelo
       WHERE cotizaciones.codsucursal = '".decrypt($_GET['codsucursal'])."' 
       AND productos.codsucursal = '".decrypt($_GET['codsucursal'])."'
       AND cotizaciones.codigo = ? 
       AND DATE_FORMAT(cotizaciones.fechacotizacion,'%Y-%m-%d') >= ? 
       AND DATE_FORMAT(cotizaciones.fechacotizacion,'%Y-%m-%d') <= ? 
       GROUP BY detallecotizaciones.codproducto, detallecotizaciones.precioventa, detallecotizaciones.descproducto 
       ORDER BY productos.codproducto ASC";
		$stmt = $this->dbh->prepare($sql);
		$stmt->bindValue(1, trim($_GET['codigo']));
		$stmt->bindValue(2, trim(date("Y-m-d",strtotime($_GET['desde']))));
		$stmt->bindValue(3, trim(date("Y-m-d",strtotime($_GET['hasta']))));
		$stmt->execute();
		$num = $stmt->rowCount();
		if($num==0)
		{
		echo "<div class='alert alert-danger'>";
		echo "<button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>";
		echo "<center><span class='fa fa-info-circle'></span> NO EXISTEN PRODUCTOS FACTURADOS PARA EL VENDEDOR Y RANGO DE FECHA INGRESADA</center>";
		echo "</div>";		
		exit;
		}
		else
		{
			while($row = $stmt->fetch(PDO::FETCH_ASSOC))
			{
				$this->p[]=$row;
			}
			return $this->p;
			$this->dbh=null;
		}
	}
########################### FUNCION PRODUCTOS COTIZADOS POR VENDEDOR ###############################

########################### FIN DE CLASE COTIZACIONES ############################



























################################ CLASE CAJAS DE VENTAS ################################

######################### FUNCION REGISTRAR CAJAS DE VENTAS #######################
public function RegistrarCajas()
{
	self::SetNames();
	if(empty($_POST["nrocaja"]) or empty($_POST["nomcaja"]) or empty($_POST["codigo"]))
	{
		echo "1";
		exit;
	}
		
		$sql = "SELECT nrocaja FROM cajas WHERE nrocaja = ?";
		$stmt = $this->dbh->prepare($sql);
		$stmt->execute(array($_POST["nrocaja"]));
		$num = $stmt->rowCount();
		if($num > 0)
		{
		    echo "2";
		    exit;

		} else {
			
		$sql = "SELECT nomcaja FROM cajas WHERE nomcaja = ?";
		$stmt = $this->dbh->prepare($sql);
		$stmt->execute(array($_POST["nomcaja"]));
		$num = $stmt->rowCount();
		if($num > 0)
		{
			echo "3";
			exit;

		} else {
			
		$sql = "SELECT codigo FROM cajas WHERE codigo = ?";
		$stmt = $this->dbh->prepare($sql);
		$stmt->execute(array($_POST["codigo"]));
		$num = $stmt->rowCount();
		if($num == 0)
		{
			$query = "INSERT INTO cajas values (null, ?, ?, ?); ";
			$stmt = $this->dbh->prepare($query);
			$stmt->bindParam(1, $nrocaja);
			$stmt->bindParam(2, $nomcaja);
			$stmt->bindParam(3, $codigo);

			$nrocaja = limpiar($_POST["nrocaja"]);
			$nomcaja = limpiar($_POST["nomcaja"]);
			$codigo = limpiar($_POST["codigo"]);
			$stmt->execute();

			echo "<span class='fa fa-check-square-o'></span> LA CAJA PARA VENTA HA SIDO REGISTRADA EXITOSAMENTE";
			exit;

			} else {

			echo "4";
			exit;
		    }
		}
	}
}
######################### FUNCION REGISTRAR CAJAS DE VENTAS #########################

######################### FUNCION LISTAR CAJAS DE VENTAS ################################
public function ListarCajas()
{
	self::SetNames();
	
	if($_SESSION['acceso'] == "administradorS") {

        $sql = "SELECT * FROM cajas INNER JOIN usuarios ON cajas.codigo = usuarios.codigo LEFT JOIN sucursales ON usuarios.codsucursal = sucursales.codsucursal WHERE usuarios.codsucursal = '".limpiar($_SESSION["codsucursal"])."'";
			foreach ($this->dbh->query($sql) as $row)
			{
				$this->p[] = $row;
			}
			return $this->p;
			$this->dbh=null;

	} else if($_SESSION["acceso"] == "cajero") {

        $sql = "SELECT * FROM cajas INNER JOIN usuarios ON cajas.codigo = usuarios.codigo LEFT JOIN sucursales ON usuarios.codsucursal = sucursales.codsucursal WHERE cajas.codigo = '".limpiar($_SESSION["codigo"])."'";
			foreach ($this->dbh->query($sql) as $row)
			{
				$this->p[] = $row;
			}
			return $this->p;
			$this->dbh=null;

			} else {

		$sql = "SELECT * FROM cajas INNER JOIN usuarios ON cajas.codigo = usuarios.codigo LEFT JOIN sucursales ON usuarios.codsucursal = sucursales.codsucursal";
			foreach ($this->dbh->query($sql) as $row)
			{
				$this->p[] = $row;
			}
			return $this->p;
			$this->dbh=null;
	}
}
######################### FUNCION LISTAR CAJAS DE VENTAS ##########################

######################### FUNCION LISTAR CAJAS ABIERTAS ##########################
public function ListarCajasAbiertas()
{
	self::SetNames();
	if ($_SESSION['acceso'] == "administradorG") {

	$sql = "SELECT * FROM cajas INNER JOIN arqueocaja ON cajas.codcaja = arqueocaja.codcaja LEFT JOIN usuarios ON cajas.codigo = usuarios.codigo LEFT JOIN sucursales ON usuarios.codsucursal = sucursales.codsucursal WHERE usuarios.codsucursal = ? AND arqueocaja.statusarqueo = 1";
		$stmt = $this->dbh->prepare($sql);
		$stmt->execute(array(decrypt($_GET["codsucursal"])));
		$num = $stmt->rowCount();
		     if($num==0)
		{
			echo "<option value=''> -- SIN RESULTADOS -- </option>";
			exit;
		       }
		else
		{
		while($row = $stmt->fetch())
			{
				$this->p[]=$row;
			}
			return $this->p;
			$this->dbh=null;
		}

	} else if($_SESSION["acceso"] == "cajero") {

        $sql = "SELECT * FROM cajas INNER JOIN arqueocaja ON cajas.codcaja = arqueocaja.codcaja LEFT JOIN usuarios ON cajas.codigo = usuarios.codigo LEFT JOIN sucursales ON usuarios.codsucursal = sucursales.codsucursal WHERE cajas.codigo = '".limpiar($_SESSION["codigo"])."'";
			foreach ($this->dbh->query($sql) as $row)
			{
				$this->p[] = $row;
			}
			return $this->p;
			$this->dbh=null;

			} else {

	$sql = "SELECT * FROM cajas INNER JOIN arqueocaja ON cajas.codcaja = arqueocaja.codcaja LEFT JOIN usuarios ON cajas.codigo = usuarios.codigo LEFT JOIN sucursales ON usuarios.codsucursal = sucursales.codsucursal WHERE usuarios.codsucursal = '".limpiar($_SESSION["codsucursal"])."' AND arqueocaja.statusarqueo = 1";
	foreach ($this->dbh->query($sql) as $row)
	{
		$this->p[] = $row;
	}
	return $this->p;
	$this->dbh=null;
   }
}
######################### FUNCION LISTAR CAJAS ABIERTAS ##########################

############################ FUNCION ID CAJAS DE VENTAS #################################
public function CajasPorId()
{
	self::SetNames();
	$sql = "SELECT * FROM cajas LEFT JOIN usuarios ON usuarios.codigo = cajas.codigo LEFT JOIN sucursales ON usuarios.codsucursal = sucursales.codsucursal WHERE cajas.codcaja = ?";
	$stmt = $this->dbh->prepare($sql);
	$stmt->execute(array(decrypt($_GET["codcaja"])));
	$num = $stmt->rowCount();
	if($num==0)
	{
		echo "";
	}
	else
	{
		if($row = $stmt->fetch(PDO::FETCH_ASSOC))
		{
			$this->p[] = $row;
		}
		return $this->p;
		$this->dbh=null;
	}
}
############################ FUNCION ID CAJAS DE VENTAS #################################

#################### FUNCION ACTUALIZAR CAJAS DE VENTAS ############################
public function ActualizarCajas()
{
	self::SetNames();
	if(empty($_POST["codcaja"]) or empty($_POST["nrocaja"]) or empty($_POST["nomcaja"]) or empty($_POST["codigo"]))
	{
		echo "1";
		exit;
	}
		$sql = "SELECT nrocaja FROM cajas WHERE codcaja != ? AND nrocaja = ?";
		$stmt = $this->dbh->prepare($sql);
		$stmt->execute(array($_POST["codcaja"],$_POST["nrocaja"]));
		$num = $stmt->rowCount();
		if($num > 0)
		{
		    echo "2";
		    exit;

		} else {
			
		$sql = "SELECT nomcaja FROM cajas WHERE codcaja != ? AND nomcaja = ?";
		$stmt = $this->dbh->prepare($sql);
		$stmt->execute(array($_POST["codcaja"],$_POST["nomcaja"]));
		$num = $stmt->rowCount();
		if($num > 0)
		{
			echo "3";
			exit;

		} else {
			
		$sql = "SELECT codigo FROM cajas WHERE codcaja != ? AND codigo = ?";
		$stmt = $this->dbh->prepare($sql);
		$stmt->execute(array($_POST["codcaja"],$_POST["codigo"]));
		$num = $stmt->rowCount();
		if($num == 0)
		{
			$sql = "UPDATE cajas set "
			." nrocaja = ?, "
			." nomcaja = ?, "
			." codigo = ? "
			." where "
			." codcaja = ?;
			";
			$stmt = $this->dbh->prepare($sql);
			$stmt->bindParam(1, $nrocaja);
			$stmt->bindParam(2, $nomcaja);
			$stmt->bindParam(3, $codigo);
			$stmt->bindParam(4, $codcaja);

			$nrocaja = limpiar($_POST["nrocaja"]);
			$nomcaja = limpiar($_POST["nomcaja"]);
			$codigo = limpiar($_POST["codigo"]);
			$codcaja = limpiar($_POST["codcaja"]);
			$stmt->execute();

			echo "<span class='fa fa-check-square-o'></span> LA CAJA PARA VENTA HA SIDO ACTUALIZADA EXITOSAMENTE";
			exit;

			} else {

			echo "4";
			exit;
		    }
		}
	}
}
#################### FUNCION ACTUALIZAR CAJAS DE VENTAS ###########################

####################### FUNCION ELIMINAR CAJAS DE VENTAS ########################
public function EliminarCajas()
{
	self::SetNames();
		if ($_SESSION['acceso'] == "administradorG" || $_SESSION["acceso"]=="administradorS") {

		$sql = "SELECT codcaja FROM ventas WHERE codcaja = ?";
		$stmt = $this->dbh->prepare($sql);
		$stmt->execute(array(decrypt($_GET["codcaja"])));
		$num = $stmt->rowCount();
		if($num == 0)
		{

			$sql = "DELETE FROM cajas WHERE codcaja = ?";
			$stmt = $this->dbh->prepare($sql);
			$stmt->bindParam(1,$codcaja);
			$codcaja = decrypt($_GET["codcaja"]);
			$stmt->execute();

			echo "1";
			exit;

		} else {
		   
			echo "2";
			exit;
		  } 
			
		} else {
		
		echo "3";
		exit;
	 }	
}
####################### FUNCION ELIMINAR CAJAS DE VENTAS #######################

####################### FUNCION BUSCAR CAJAS POR SUCURSAL ###############################
public function BuscarCajasxSucursal() 
	       {
		self::SetNames();
		$sql = " SELECT * FROM cajas INNER JOIN usuarios ON cajas.codigo = usuarios.codigo INNER JOIN sucursales ON usuarios.codsucursal = sucursales.codsucursal WHERE sucursales.codsucursal = ?";
		$stmt = $this->dbh->prepare($sql);
		$stmt->execute(array(decrypt($_GET["codsucursal"])));
		$num = $stmt->rowCount();
		     if($num==0)
		{
			echo "<option value=''> -- SIN RESULTADOS -- </option>";
			exit;
		       }
		else
		{
		while($row = $stmt->fetch())
			{
				$this->p[]=$row;
			}
			return $this->p;
			$this->dbh=null;
		}
	}
######################## FUNCION BUSCAR CAJAS POR SUCURSAL #######################

############################ FIN DE CLASE CAJAS DE VENTAS ##############################


























########################## CLASE ARQUEOS DE CAJA ###################################

########################## FUNCION PARA REGISTRAR ARQUEO DE CAJA ####################
public function RegistrarArqueoCaja()
{
	self::SetNames();
	if(empty($_POST["codcaja"]) or empty($_POST["montoinicial"]) or empty($_POST["fecharegistro"]))
	{
		echo "1";
		exit;
	}

	$sql = "SELECT codcaja FROM arqueocaja WHERE codcaja = ? AND statusarqueo = 1";
	$stmt = $this->dbh->prepare($sql);
	$stmt->execute(array(decrypt($_POST["codcaja"])));
	$num = $stmt->rowCount();
	if($num == 0)
	{
		$query = "INSERT INTO arqueocaja values (null, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?); ";
		$stmt = $this->dbh->prepare($query);
		$stmt->bindParam(1, $codcaja);
		$stmt->bindParam(2, $montoinicial);
		$stmt->bindParam(3, $ingresos);
		$stmt->bindParam(4, $egresos);
		$stmt->bindParam(5, $creditos);
		$stmt->bindParam(6, $abonos);
		$stmt->bindParam(7, $dineroefectivo);
		$stmt->bindParam(8, $diferencia);
		$stmt->bindParam(9, $comentarios);
		$stmt->bindParam(10, $fechaapertura);
		$stmt->bindParam(11, $fechacierre);
		$stmt->bindParam(12, $statusarqueo);

		$codcaja = limpiar(decrypt($_POST["codcaja"]));
		$montoinicial = limpiar($_POST["montoinicial"]);
		$ingresos = limpiar("0.00");
		$egresos = limpiar("0.00");
		$creditos = limpiar("0.00");
		$abonos = limpiar("0.00");
		$dineroefectivo = limpiar("0.00");
		$diferencia = limpiar("0.00");
		$comentarios = limpiar('NINGUNO');
		$fechaapertura = limpiar(date("Y-m-d h:i:s",strtotime($_POST['fecharegistro'])));
		$fechacierre = limpiar(date("0000-00-00 00:00:00"));
		$statusarqueo = limpiar("1");
		$stmt->execute();

		echo "<span class='fa fa-check-square-o'></span> EL ARQUEO DE CAJA HA SIDO REALIZADO EXITOSAMENTE";
		exit;

			} else {

			echo "2";
			exit;
	    }
}
######################## FUNCION PARA REGISTRAR ARQUEO DE CAJA #######################

######################## FUNCION PARA LISTAR ARQUEO DE CAJA ########################
public function ListarArqueoCaja()
{
	self::SetNames();
	
	if($_SESSION['acceso'] == "administradorS") {

        $sql = "SELECT * FROM arqueocaja INNER JOIN cajas ON arqueocaja.codcaja = cajas.codcaja LEFT JOIN usuarios ON cajas.codigo = usuarios.codigo LEFT JOIN sucursales ON usuarios.codsucursal = sucursales.codsucursal WHERE usuarios.codsucursal = '".limpiar($_SESSION["codsucursal"])."' ORDER BY arqueocaja.fechaapertura DESC";
			foreach ($this->dbh->query($sql) as $row)
			{
				$this->p[] = $row;
			}
			return $this->p;
			$this->dbh=null;

	} else if($_SESSION["acceso"] == "cajero") {

        $sql = "SELECT * FROM arqueocaja INNER JOIN cajas ON arqueocaja.codcaja = cajas.codcaja LEFT JOIN usuarios ON cajas.codigo = usuarios.codigo LEFT JOIN sucursales ON usuarios.codsucursal = sucursales.codsucursal WHERE cajas.codigo = '".limpiar($_SESSION["codigo"])."' ORDER BY arqueocaja.fechaapertura DESC";
			foreach ($this->dbh->query($sql) as $row)
			{
				$this->p[] = $row;
			}
			return $this->p;
			$this->dbh=null;

			} else {

		$sql = "SELECT * FROM arqueocaja INNER JOIN cajas ON arqueocaja.codcaja = cajas.codcaja LEFT JOIN usuarios ON cajas.codigo = usuarios.codigo LEFT JOIN sucursales ON usuarios.codsucursal = sucursales.codsucursal ORDER BY arqueocaja.fechaapertura DESC";
			foreach ($this->dbh->query($sql) as $row)
			{
				$this->p[] = $row;
			}
			return $this->p;
			$this->dbh=null;

			}
		}
######################## FUNCION PARA LISTAR ARQUEO DE CAJA #########################

########################## FUNCION ID ARQUEO DE CAJA #############################
public function ArqueoCajaPorId()
{
	self::SetNames();
	$sql = "SELECT * FROM arqueocaja INNER JOIN cajas ON arqueocaja.codcaja = cajas.codcaja LEFT JOIN usuarios ON cajas.codigo = usuarios.codigo 
	LEFT JOIN sucursales ON usuarios.codsucursal = sucursales.codsucursal
	LEFT JOIN documentos ON sucursales.documsucursal = documentos.coddocumento
	LEFT JOIN provincias ON sucursales.id_provincia = provincias.id_provincia 
	LEFT JOIN departamentos ON sucursales.id_departamento = departamentos.id_departamento  
	 WHERE arqueocaja.codarqueo = ?";
	$stmt = $this->dbh->prepare($sql);
	$stmt->execute(array(decrypt($_GET["codarqueo"])));
	$num = $stmt->rowCount();
	if($num==0)
	{
		echo "";
	}
	else
	{
		if($row = $stmt->fetch(PDO::FETCH_ASSOC))
			{
				$this->p[] = $row;
			}
			return $this->p;
			$this->dbh=null;
		}
	}
########################## FUNCION ID ARQUEO DE CAJA #############################

##################### FUNCION VERIFICA ARQUEO DE CAJA POR USUARIO #######################
public function ArqueoCajaPorUsuario()
{
	self::SetNames();
	$sql = "SELECT * FROM arqueocaja INNER JOIN cajas ON arqueocaja.codcaja = cajas.codcaja INNER JOIN usuarios ON cajas.codigo = usuarios.codigo LEFT JOIN sucursales ON usuarios.codsucursal = sucursales.codsucursal WHERE usuarios.codigo = ? AND arqueocaja.statusarqueo = 1";
	$stmt = $this->dbh->prepare($sql);
	$stmt->execute(array($_SESSION["codigo"]));
	$num = $stmt->rowCount();
	if($num==0)
	{
		echo "";
	}
	else
	{
		if($row = $stmt->fetch(PDO::FETCH_ASSOC))
			{
				$this->p[] = $row;
			}
			return $this->p;
			$this->dbh=null;
		}
	}
###################### FUNCION VERIFICA ARQUEO DE CAJA POR USUARIO ###################

######################### FUNCION PARA CERRAR ARQUEO DE CAJA #########################
public function CerrarArqueoCaja()
{
	self::SetNames();
	if(empty($_POST["codarqueo"]) or empty($_POST["dineroefectivo"]))
	{
		echo "1";
		exit;
	}

	if($_POST["dineroefectivo"] != 0.00 || $_POST["dineroefectivo"] != 0){

		$sql = "UPDATE arqueocaja SET "
		." dineroefectivo = ?, "
		." diferencia = ?, "
		." comentarios = ?, "
		." fechacierre = ?, "
		." statusarqueo = ? "
		." WHERE "
		." codarqueo = ?;
		";
		$stmt = $this->dbh->prepare($sql);
		$stmt->bindParam(1, $dineroefectivo);
		$stmt->bindParam(2, $diferencia);
		$stmt->bindParam(3, $comentarios);
		$stmt->bindParam(4, $fechacierre);
		$stmt->bindParam(5, $statusarqueo);
		$stmt->bindParam(6, $codarqueo);

		$dineroefectivo = limpiar($_POST["dineroefectivo"]);
		$diferencia = limpiar($_POST["diferencia"]);
		$comentarios = limpiar($_POST['comentarios']);
		$fechacierre = limpiar(date("Y-m-d h:i:s",strtotime($_POST['fecharegistro2'])));
		$statusarqueo = limpiar("0");
		$codarqueo = limpiar($_POST["codarqueo"]);
		$stmt->execute();

	echo "<span class='fa fa-check-square-o'></span> EL CIERRE DE CAJA FUE REALIZADO EXITOSAMENTE <a href='reportepdf?codarqueo=".encrypt($codarqueo)."&tipo=".encrypt("TICKETCIERRE")."' class='on-default' data-placement='left' data-toggle='tooltip' data-original-title='Imprimir Documento' target='_black' rel='noopener noreferrer'><font color='black'><strong>IMPRIMIR TICKET</strong></font color></a></div>";

	echo "<script>window.open('reportepdf?codarqueo=".encrypt($codarqueo)."&tipo=".encrypt("TICKETCIERRE")."', '_blank');</script>";
	exit;

			} else {

			echo "2";
			exit;
	    }
}
######################### FUNCION PARA CERRAR ARQUEO DE CAJA ######################

###################### FUNCION BUSCAR ARQUEOS DE CAJA POR FECHAS ######################
public function BuscarArqueosxFechas() 
	       {
		self::SetNames();		
$sql = "SELECT * FROM arqueocaja INNER JOIN cajas ON arqueocaja.codcaja = cajas.codcaja LEFT JOIN usuarios ON cajas.codigo = usuarios.codigo LEFT JOIN sucursales ON usuarios.codsucursal = sucursales.codsucursal WHERE sucursales.codsucursal = ? AND arqueocaja.codcaja = ? AND DATE_FORMAT(arqueocaja.fechaapertura,'%Y-%m-%d') >= ? AND DATE_FORMAT(arqueocaja.fechaapertura,'%Y-%m-%d') <= ?";
		$stmt = $this->dbh->prepare($sql);
		$stmt->bindValue(1, trim(decrypt($_GET['codsucursal'])));
		$stmt->bindValue(2, trim(decrypt($_GET['codcaja'])));
		$stmt->bindValue(3, trim(date("Y-m-d",strtotime($_GET['desde']))));
		$stmt->bindValue(4, trim(date("Y-m-d",strtotime($_GET['hasta']))));
		$stmt->execute();
		$num = $stmt->rowCount();
		if($num==0)
		{
		echo "<center><div class='alert alert-danger'>";
		echo "<button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>";
		echo "<span class='fa fa-info-circle'></span> NO SE ENCONTRARON ARQUEOS DE CAJAS PARA LAS FECHAS SELECCIONADAS</div></center>";
		exit;
		}
		else
		{
			while($row = $stmt->fetch(PDO::FETCH_ASSOC))
			{
				$this->p[]=$row;
			}
			return $this->p;
			$this->dbh=null;
	    }
	
}
######################## FUNCION BUSCAR ARQUEOS DE CAJA POR FECHAS ####################

############################# FIN DE CLASE ARQUEOS DE CAJA ###########################


























############################ CLASE MOVIMIENTOS EN CAJAS ##############################

###################### FUNCION PARA REGISTRAR MOVIMIENTO EN CAJA #######################
public function RegistrarMovimientos()
{
	self::SetNames();
	if(empty($_POST["tipomovimiento"]) or empty($_POST["montomovimiento"]) or empty($_POST["codmediopago"]) or empty($_POST["codcaja"]))
	{
		echo "1";
		exit;
	}

	$sql = " SELECT * FROM arqueocaja WHERE codcaja = '".limpiar($_POST["codcaja"])."' AND statusarqueo = 1";
	$stmt = $this->dbh->prepare($sql);
	$stmt->execute();
	$num = $stmt->rowCount();
	if($num==0)
	{
		echo "2";
		exit;

	}  
	else if($_POST["montomovimiento"]>0)
	{

	#################### AGREGAMOS EL INGRESO A ARQUEO EN CAJA ####################
	$sql = "SELECT montoinicial, ingresos, egresos FROM arqueocaja WHERE codcaja = '".limpiar($_POST["codcaja"])."'";
	foreach ($this->dbh->query($sql) as $row)
	{
		$this->p[] = $row;
	}
	$inicial = $row['montoinicial'];
	$ingreso = $row['ingresos'];
	$egresos = $row['egresos'];
	$total = $inicial+$ingreso-$egresos;

	if($_POST["tipomovimiento"]=="INGRESO"){

		$sql = " UPDATE arqueocaja SET "
		." ingresos = ? "
		." WHERE "
		." codcaja = ? AND statusarqueo = 1;
		";
		$stmt = $this->dbh->prepare($sql);
		$stmt->bindParam(1, $ingresos);
		$stmt->bindParam(2, $codcaja);

		$ingresos = number_format($_POST["montomovimiento"]+$ingreso, 2, '.', '');
		$codcaja = limpiar($_POST["codcaja"]);
		$stmt->execute();

		$query = "INSERT INTO movimientoscajas values (null, ?, ?, ?, ?, ?, ?); ";
		$stmt = $this->dbh->prepare($query);
		$stmt->bindParam(1, $codcaja);
		$stmt->bindParam(2, $tipomovimiento);
		$stmt->bindParam(3, $descripcionmovimiento);
		$stmt->bindParam(4, $montomovimiento);
		$stmt->bindParam(5, $codmediopago);
		$stmt->bindParam(6, $fechamovimiento);

		$codcaja = limpiar($_POST["codcaja"]);
		$tipomovimiento = limpiar($_POST["tipomovimiento"]);
		$descripcionmovimiento = limpiar($_POST["descripcionmovimiento"]);
		$montomovimiento = limpiar($_POST["montomovimiento"]);
		$codmediopago = limpiar($_POST["codmediopago"]);
		$fechamovimiento = limpiar(date("Y-m-d h:i:s",strtotime($_POST['fecharegistro'])));
		$stmt->execute();

	} else {

		if($_POST["montomovimiento"]>$total){

			echo "3";
			exit;

    } else {

		$sql = "UPDATE arqueocaja SET "
		." egresos = ? "
		." WHERE "
		." codcaja = ? AND statusarqueo = 1;
		";
		$stmt = $this->dbh->prepare($sql);
		$stmt->bindParam(1, $egresos);
		$stmt->bindParam(2, $codcaja);

		$egresos = number_format($_POST["montomovimiento"]+$egresos, 2, '.', '');
		$codcaja = limpiar($_POST["codcaja"]);
		$stmt->execute();

		$query = "INSERT INTO movimientoscajas values (null, ?, ?, ?, ?, ?, ?); ";
		$stmt = $this->dbh->prepare($query);
		$stmt->bindParam(1, $codcaja);
		$stmt->bindParam(2, $tipomovimiento);
		$stmt->bindParam(3, $descripcionmovimiento);
		$stmt->bindParam(4, $montomovimiento);
		$stmt->bindParam(5, $codmediopago);
		$stmt->bindParam(6, $fechamovimiento);

		$codcaja = limpiar($_POST["codcaja"]);
		$tipomovimiento = limpiar($_POST["tipomovimiento"]);
		$descripcionmovimiento = limpiar($_POST["descripcionmovimiento"]);
		$montomovimiento = limpiar($_POST["montomovimiento"]);
		$codmediopago = limpiar($_POST["codmediopago"]);
		$fechamovimiento = limpiar(date("Y-m-d h:i:s",strtotime($_POST['fecharegistro'])));
		$stmt->execute();

	      }
	}

		echo "<span class='fa fa-check-square-o'></span> EL MOVIMIENTO EN CAJA HA SIDO REGISTRADO EXITOSAMENTE";
		exit;

			} else {

			echo "4";
			exit;
	    }
}
##################### FUNCION PARA REGISTRAR MOVIMIENTO EN CAJA #######################

###################### FUNCION PARA LISTAR MOVIMIENTO EN CAJA #######################
public function ListarMovimientos()
{
	self::SetNames();
	
	if($_SESSION['acceso'] == "administradorS") {

        $sql = " SELECT * FROM movimientoscajas INNER JOIN cajas ON movimientoscajas.codcaja = cajas.codcaja LEFT JOIN usuarios ON cajas.codigo = usuarios.codigo LEFT JOIN sucursales ON usuarios.codsucursal = sucursales.codsucursal LEFT JOIN mediospagos ON mediospagos.codmediopago = movimientoscajas.codmediopago WHERE usuarios.codsucursal = '".limpiar($_SESSION["codsucursal"])."'";
			foreach ($this->dbh->query($sql) as $row)
			{
				$this->p[] = $row;
			}
			return $this->p;
			$this->dbh=null;

	} else if($_SESSION["acceso"] == "cajero") {

        $sql = " SELECT * FROM movimientoscajas INNER JOIN cajas ON movimientoscajas.codcaja = cajas.codcaja LEFT JOIN usuarios ON cajas.codigo = usuarios.codigo LEFT JOIN sucursales ON usuarios.codsucursal = sucursales.codsucursal LEFT JOIN mediospagos ON mediospagos.codmediopago = movimientoscajas.codmediopago WHERE usuarios.codigo = '".limpiar($_SESSION["codigo"])."'";
			foreach ($this->dbh->query($sql) as $row)
			{
				$this->p[] = $row;
			}
			return $this->p;
			$this->dbh=null;

			} else {

		$sql = "SELECT * FROM movimientoscajas INNER JOIN cajas ON movimientoscajas.codcaja = cajas.codcaja LEFT JOIN usuarios ON cajas.codigo = usuarios.codigo LEFT JOIN sucursales ON usuarios.codsucursal = sucursales.codsucursal LEFT JOIN mediospagos ON mediospagos.codmediopago = movimientoscajas.codmediopago";
			foreach ($this->dbh->query($sql) as $row)
			{
				$this->p[] = $row;
			}
			return $this->p;
			$this->dbh=null;

			}
		}
###################### FUNCION PARA LISTAR MOVIMIENTO EN CAJA ######################

########################## FUNCION ID MOVIMIENTO EN CAJA #############################
public function MovimientosPorId()
{
	self::SetNames();
	$sql = " SELECT * from movimientoscajas LEFT JOIN cajas ON movimientoscajas.codcaja = cajas.codcaja LEFT JOIN mediospagos ON movimientoscajas.codmediopago = mediospagos.codmediopago LEFT JOIN usuarios ON cajas.codigo = usuarios.codigo LEFT JOIN sucursales ON usuarios.codsucursal = sucursales.codsucursal WHERE movimientoscajas.codmovimiento = ?";
	$stmt = $this->dbh->prepare($sql);
	$stmt->execute(array(decrypt($_GET["codmovimiento"])));
	$num = $stmt->rowCount();
	if($num==0)
	{
		echo "";
	}
	else
	{
		if($row = $stmt->fetch(PDO::FETCH_ASSOC))
			{
				$this->p[] = $row;
			}
			return $this->p;
			$this->dbh=null;
		}
	}
########################## FUNCION ID MOVIMIENTO EN CAJA #############################

##################### FUNCION PARA ACTUALIZAR MOVIMIENTOS EN CAJA ##################
public function ActualizarMovimientos()
{
	self::SetNames();
if(empty($_POST["tipomovimiento"]) or empty($_POST["montomovimiento"]) or empty($_POST["codmediopago"]) or empty($_POST["codcaja"]))
	{
		echo "1";
		exit;
	}

	if($_POST["montomovimiento"]>0)
	{

	#################### AGREGAMOS EL INGRESO A ARQUEO EN CAJA ####################
	$sql = "SELECT montoinicial, ingresos, egresos FROM arqueocaja WHERE codcaja = '".limpiar($_POST["codcaja"])."' AND statusarqueo = 1";
	foreach ($this->dbh->query($sql) as $row)
	{
		$this->p[] = $row;
	}
	$inicial = $row['montoinicial'];
	$ingreso = $row['ingresos'];
	$egreso = $row['egresos'];
	$total = $inicial+$ingreso-$egreso;
	$montomovimiento = limpiar($_POST["montomovimiento"]);
	$montomovimientodb = limpiar($_POST["montomovimientodb"]);
	$ingresobd = number_format($ingreso-$montomovimientodb, 2, '.', '');
	$totalmovimiento = number_format($montomovimiento-$montomovimientodb, 2, '.', '');

	if($_POST["tipomovimiento"]=="INGRESO"){

	$sql = "UPDATE arqueocaja SET "
		." ingresos = ? "
		." WHERE "
		." codcaja = ? AND statusarqueo = 1;
		";
		$stmt = $this->dbh->prepare($sql);
		$stmt->bindParam(1, $ingresos);
		$stmt->bindParam(2, $codcaja);

		$ingresos = number_format($montomovimiento+$ingresobd, 2, '.', '');
		$codcaja = limpiar($_POST["codcaja"]);
		$stmt->execute();

	    $sql = "UPDATE movimientoscajas SET"
		." codcaja = ?, "
		." tipomovimiento = ?, "
		." descripcionmovimiento = ?, "
		." montomovimiento = ?, "
		." codmediopago = ? "
		." WHERE "
		." codmovimiento = ?;
		";
		$stmt = $this->dbh->prepare($sql);
		$stmt->bindParam(1, $codcaja);
		$stmt->bindParam(2, $tipomovimiento);
		$stmt->bindParam(3, $descripcionmovimiento);
		$stmt->bindParam(4, $montomovimiento);
		$stmt->bindParam(5, $codmediopago);
		$stmt->bindParam(6, $codmovimiento);

		$codcaja = limpiar($_POST["codcaja"]);
		$tipomovimiento = limpiar($_POST["tipomovimiento"]);
		$descripcionmovimiento = limpiar($_POST["descripcionmovimiento"]);
		$montomovimiento = limpiar($_POST["montomovimiento"]);
		$codmediopago = limpiar($_POST["codmediopago"]);
		$codmovimiento = limpiar($_POST["codmovimiento"]);
		$stmt->execute();

	} else {

		   if($totalmovimiento>$total){
		
		echo "2";
		exit;

	         } else {

	$sql = "UPDATE arqueocaja SET"
		." egresos = ? "
		." WHERE "
		." codcaja = ? AND statusarqueo = 1;
		";
		$stmt = $this->dbh->prepare($sql);
		$stmt->bindParam(1, $egresos);
		$stmt->bindParam(2, $codcaja);

		$egresos = number_format($totalmovimiento+$egreso, 2, '.', '');
		$codcaja = limpiar($_POST["codcaja"]);
		$stmt->execute();

		$sql = "UPDATE movimientoscajas SET"
		." codcaja = ?, "
		." tipomovimiento = ?, "
		." descripcionmovimiento = ?, "
		." montomovimiento = ?, "
		." codmediopago = ? "
		." WHERE "
		." codmovimiento = ?;
		";
		$stmt = $this->dbh->prepare($sql);
		$stmt->bindParam(1, $codcaja);
		$stmt->bindParam(2, $tipomovimiento);
		$stmt->bindParam(3, $descripcionmovimiento);
		$stmt->bindParam(4, $montomovimiento);
		$stmt->bindParam(5, $codmediopago);
		$stmt->bindParam(6, $codmovimiento);

		$codcaja = limpiar($_POST["codcaja"]);
		$tipomovimiento = limpiar($_POST["tipomovimiento"]);
		$descripcionmovimiento = limpiar($_POST["descripcionmovimiento"]);
		$montomovimiento = limpiar($_POST["montomovimiento"]);
		$codmediopago = limpiar($_POST["codmediopago"]);
		$codmovimiento = limpiar($_POST["codmovimiento"]);
		$stmt->execute();

	        }
	}	
	
echo "<span class='fa fa-check-square-o'></span> EL MOVIMIENTO EN CAJA HA SIDO ACTUALIZADO EXITOSAMENTE";
exit;
	}
	else
	{
		echo "3";
		exit;
	}
}
##################### FUNCION PARA ACTUALIZAR MOVIMIENTOS EN CAJA ####################	

###################### FUNCION PARA ELIMINAR MOVIMIENTOS EN CAJA ######################
public function EliminarMovimiento()
{
	if($_SESSION['acceso'] == "administradorS" || $_SESSION['acceso'] == "secretaria" || $_SESSION['acceso'] == "cajero") {

    #################### AGREGAMOS EL INGRESO A ARQUEO EN CAJA ####################
	$sql = "SELECT * FROM movimientoscajas WHERE codmovimiento = '".limpiar(decrypt($_GET["codmovimiento"]))."'";
	foreach ($this->dbh->query($sql) as $row)
	{
		$this->p[] = $row;
	}
	$tipomovimiento = $row['tipomovimiento'];
	$montomovimiento = $row['montomovimiento'];
	$codmediopago = $row['codmediopago'];
	$codcaja = $row['codcaja'];
	$descripcionmovimiento = $row['descripcionmovimiento'];
	$fechamovimiento = $row['fechamovimiento'];

	#################### AGREGAMOS EL INGRESO A ARQUEO EN CAJA ####################
	$sql2 = "SELECT montoinicial, ingresos, egresos FROM arqueocaja WHERE codcaja = '".limpiar($codcaja)."' AND statusarqueo = 1";
	foreach ($this->dbh->query($sql2) as $row2)
	{
		$this->p[] = $row2;
	}
	$inicial = $row2['montoinicial'];
	$ingreso = $row2['ingresos'];
	$egreso = $row2['egresos'];

if($tipomovimiento=="INGRESO"){

		$sql = "UPDATE arqueocaja SET"
		." ingresos = ? "
		." WHERE "
		." codcaja = ? AND statusarqueo = 1;
		";
		$stmt = $this->dbh->prepare($sql);
		$stmt->bindParam(1, $ingresos);
		$stmt->bindParam(2, $codcaja);

		$entro = $montomovimiento;
		$ingresos = number_format($ingreso-$entro, 2, '.', '');
		$stmt->execute();

} else {

		$sql = "UPDATE arqueocaja SET "
		." egresos = ? "
		." WHERE "
		." codcaja = ? AND statusarqueo = 1;
		";
		$stmt = $this->dbh->prepare($sql);
		$stmt->bindParam(1, $egresos);
		$stmt->bindParam(2, $codcaja);

		$salio = $montomovimiento;
		$egresos = number_format($egreso-$salio, 2, '.', '');
		$stmt->execute();
       }

		$sql = "DELETE FROM movimientoscajas WHERE codmovimiento = ? ";
		$stmt = $this->dbh->prepare($sql);
		$stmt->bindParam(1,$codmovimiento);
		$codmovimiento = decrypt($_GET["codmovimiento"]);
		$stmt->execute();

		echo "1";
		exit;

	} else {

		echo "2";
		exit;
	} 
}
###################### FUNCION PARA ELIMINAR MOVIMIENTOS EN CAJAS  ####################

################## FUNCION BUSCAR MOVIMIENTOS DE CAJA POR FECHAS #######################
public function BuscarMovimientosxFechas() 
	       {
		self::SetNames();		
$sql = "SELECT * FROM movimientoscajas INNER JOIN cajas ON movimientoscajas.codcaja = cajas.codcaja LEFT JOIN usuarios ON cajas.codigo = usuarios.codigo LEFT JOIN sucursales ON usuarios.codsucursal = sucursales.codsucursal LEFT JOIN mediospagos ON mediospagos.codmediopago = movimientoscajas.codmediopago WHERE sucursales.codsucursal = ? AND movimientoscajas.codcaja = ? AND DATE_FORMAT(movimientoscajas.fechamovimiento,'%Y-%m-%d') >= ? AND DATE_FORMAT(movimientoscajas.fechamovimiento,'%Y-%m-%d') <= ?";
		$stmt = $this->dbh->prepare($sql);
		$stmt->bindValue(1, trim(decrypt($_GET['codsucursal'])));
		$stmt->bindValue(2, trim(decrypt($_GET['codcaja'])));
		$stmt->bindValue(3, trim(date("Y-m-d",strtotime($_GET['desde']))));
		$stmt->bindValue(4, trim(date("Y-m-d",strtotime($_GET['hasta']))));
		$stmt->execute();
		$num = $stmt->rowCount();
		if($num==0)
		{
		echo "<center><div class='alert alert-danger'>";
		echo "<button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>";
		echo "<span class='fa fa-info-circle'></span> NO SE ENCONTRARON MOVIMIENTOS DE CAJAS PARA LAS FECHAS SELECCIONADAS</div></center>";
		exit;
		}
		else
		{
			while($row = $stmt->fetch(PDO::FETCH_ASSOC))
			{
				$this->p[]=$row;
			}
			return $this->p;
			$this->dbh=null;
	    }
	
}
###################### FUNCION BUSCAR MOVIMIENTOS DE CAJA POR FECHAS ###################

######################### FIN DE CLASE MOVIMIENTOS EN CAJAS #############################
































###################################### CLASE VENTAS ###################################

############################# FUNCION REGISTRAR VENTAS ###############################
	public function RegistrarVentas()
	{
		self::SetNames();

		$sql = "SELECT * FROM arqueocaja INNER JOIN cajas ON arqueocaja.codcaja = cajas.codcaja INNER JOIN usuarios ON cajas.codigo = usuarios.codigo WHERE usuarios.codigo = ? AND arqueocaja.statusarqueo = 1";
		$stmt = $this->dbh->prepare($sql);
		$stmt->execute(array($_SESSION["codigo"]));
		$num = $stmt->rowCount();
		if($num==0)
		{
			echo "1";
			exit;
	    }
	    else if(empty($_POST["codsucursal"]) or empty($_POST["tipodocumento"]) or empty($_POST["tipopago"]))
		{
			echo "2";
			exit;
		}
		elseif(empty($_SESSION["CarritoVenta"]) || $_POST["txtTotal"]=="0.00")
		{
			echo "3";
			exit;
			
		}

		################### SELECCIONE LOS DATOS DEL CLIENTE ######################
	    $sql = "SELECT
	    clientes.nomcliente, 
	    clientes.emailcliente, 
	    clientes.limitecredito,
	    ROUND(SUM(if(pag.montocredito!='0',pag.montocredito,'0.00')), 2) montoactual,
	    ROUND(SUM(if(pag.montocredito!='0',clientes.limitecredito-pag.montocredito,clientes.limitecredito)), 2) creditodisponible
        FROM clientes 
        LEFT JOIN
           (SELECT
           codcliente, montocredito       
           FROM creditosxclientes 
           WHERE codsucursal = '".limpiar($_POST['codsucursal'])."') pag ON pag.codcliente = clientes.codcliente
           WHERE clientes.codcliente = ?";
		$stmt = $this->dbh->prepare($sql);
		$stmt->execute(array($_POST['codcliente']));
		$num = $stmt->rowCount();
		if($row = $stmt->fetch(PDO::FETCH_ASSOC))
		{
			$p[] = $row;
		}
        $nomcliente = $row['nomcliente'];
        $emailcliente = $row['emailcliente'];
        $limitecredito = $row['limitecredito'];
        $montoactual = $row['montoactual'];
        $creditodisponible = $row['creditodisponible'];
        $montoabono = (empty($_POST["montoabono"]) ? "0.00" : $_POST["montoabono"]);
        $total = number_format($_POST["txtTotal"]-$montoabono, 2, '.', '');
		
		if (limpiar(isset($_POST['fechavencecredito']))) {  

			$fechaactual = date("Y-m-d");
			$fechavence = date("Y-m-d",strtotime($_POST['fechavencecredito']));

			if (strtotime($fechavence) < strtotime($fechaactual)) {

				echo "4";
				exit;
			}
		}

		if ($_POST["tipopago"] == "CREDITO" && $_POST["codcliente"] == '0') { 

		        echo "5";
		        exit;

	    } else if ($_POST["tipopago"] == "CREDITO") {

		    if ($limitecredito != "0.00" && $total > $creditodisponible) {
	  
	           echo "6";
		       exit;

	        } 
	    }

	    if($_POST["tipopago"]=="CREDITO" && $_POST["montoabono"] >= $_POST["txtTotal"])
		{
			echo "7";
			exit;
			
		} else {

		############ VALIDO SI LA CANTIDAD ES MAYOR QUE LA EXISTENCIA #############
		$v = $_SESSION["CarritoVenta"];
		for($i=0;$i<count($v);$i++){

		    $sql = "SELECT existencia FROM productos WHERE codproducto = '".$v[$i]['txtCodigo']."' AND codsucursal = '".limpiar($_POST["codsucursal"])."'";
		    foreach ($this->dbh->query($sql) as $row)
		    {
			$this->p[] = $row;
		    }
		
		    $existenciadb = $row['existencia'];
		    $cantidad = $v[$i]['cantidad'];

            if ($cantidad > $existenciadb) 
            { 
		       echo "8";
		       exit;
	        }
		}

		
	    ################ CREO LOS CODIGO VENTA-SERIE-AUTORIZACION ####################
		$sql = " SELECT codsucursal, nroactividadsucursal, iniciofactura FROM sucursales WHERE codsucursal = '".limpiar($_SESSION["codsucursal"])."'";
		foreach ($this->dbh->query($sql) as $row)
		{
			$this->p[] = $row;
		}
		$nroactividad = $row['nroactividadsucursal'];
		$iniciofactura = $row['iniciofactura'];
		
		$sql = "SELECT codventa FROM ventas WHERE codsucursal = '".limpiar($_POST["codsucursal"])."' ORDER BY idventa DESC LIMIT 1";
		foreach ($this->dbh->query($sql) as $row){

			$venta=$row["codventa"];

		}
		if(empty($venta))
		{
			$codventa = $nroactividad.'-'.$iniciofactura;
			$codserie = $nroactividad;
			$codautorizacion = limpiar(GenerateRandomStringg());

		} else {

			$var = strlen($nroactividad."-");
            $var1 = substr($venta , $var);
            $var2 = strlen($var1);
            $var3 = $var1 + 1;
            $var4 = str_pad($var3, $var2, "0", STR_PAD_LEFT);
            $codventa = $nroactividad.'-'.$var4;
			$codserie = $nroactividad;
			$codautorizacion = limpiar(GenerateRandomStringg());
		}
        ################ CREO LOS CODIGO VENTA-SERIE-AUTORIZACION ###############

        $fecha = date("Y-m-d h:i:s");

        $query = "INSERT INTO ventas values (null, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);";
		$stmt = $this->dbh->prepare($query);
		$stmt->bindParam(1, $tipodocumento);
		$stmt->bindParam(2, $codcaja);
		$stmt->bindParam(3, $codventa);
		$stmt->bindParam(4, $codserie);
		$stmt->bindParam(5, $codautorizacion);
		$stmt->bindParam(6, $codcliente);
		$stmt->bindParam(7, $subtotalivasi);
		$stmt->bindParam(8, $subtotalivano);
		$stmt->bindParam(9, $iva);
		$stmt->bindParam(10, $totaliva);
		$stmt->bindParam(11, $descuento);
		$stmt->bindParam(12, $totaldescuento);
		$stmt->bindParam(13, $totalpago);
		$stmt->bindParam(14, $totalpago2);
		$stmt->bindParam(15, $creditopagado);
		$stmt->bindParam(16, $tipopago);
		$stmt->bindParam(17, $formapago);
		$stmt->bindParam(18, $montopagado);
		$stmt->bindParam(19, $montodevuelto);
		$stmt->bindParam(20, $fechavencecredito);
		$stmt->bindParam(21, $fechapagado);
		$stmt->bindParam(22, $statusventa);
		$stmt->bindParam(23, $fechaventa);
		$stmt->bindParam(24, $observaciones);
		$stmt->bindParam(25, $codigo);
		$stmt->bindParam(26, $codsucursal);
	    
		$tipodocumento = limpiar($_POST["tipodocumento"]);
		$codcaja = limpiar($_POST["codcaja"]);
		$codcliente = limpiar($_POST["codcliente"]);
		$subtotalivasi = limpiar($_POST["txtsubtotal"]);
		$subtotalivano = limpiar($_POST["txtsubtotal2"]);
		$iva = limpiar($_POST["iva"]);
		$totaliva = limpiar($_POST["txtIva"]);
		$descuento = limpiar($_POST["descuento"]);
		$totaldescuento = limpiar($_POST["txtDescuento"]);
		$totalpago = limpiar($_POST["txtTotal"]);
		$totalpago2 = limpiar($_POST["txtTotalCompra"]);
		if (limpiar(isset($_POST['montoabono']))) { $creditopagado = limpiar($_POST['montoabono']); } else { $creditopagado ='0.00'; }
		$tipopago = limpiar($_POST["tipopago"]);
		$formapago = limpiar($_POST["tipopago"]=="CONTADO" ? decrypt($_POST["codmediopago"]) : "CREDITO");
	if (limpiar(isset($_POST['montopagado']))) { $montopagado = limpiar($_POST['montopagado']); } else { $montopagado ='0.00'; }
	if (limpiar(isset($_POST['montodevuelto']))) { $montodevuelto = limpiar($_POST['montodevuelto']); } else { $montodevuelto ='0.00'; }
		$fechavencecredito = limpiar($_POST["tipopago"]=="CREDITO" ? date("Y-m-d",strtotime($_POST['fechavencecredito'])) : "0000-00-00");
	    $fechapagado = limpiar("0000-00-00");
	    $statusventa = limpiar($_POST["tipopago"]=="CONTADO" ? "PAGADA" : "PENDIENTE");
        $fechaventa = limpiar($fecha);
		$observaciones = limpiar($_POST["observaciones"]);
		$codigo = limpiar($_SESSION["codigo"]);
		$codsucursal = limpiar($_POST["codsucursal"]);
		$stmt->execute();

		$this->dbh->beginTransaction();
		$detalle = $_SESSION["CarritoVenta"];
		for($i=0;$i<count($detalle);$i++){

		############### VERIFICO LA EXISTENCIA DEL PRODUCTO EN ALMACEN ##################
		$sql = "SELECT existencia FROM productos WHERE codproducto = '".limpiar($detalle[$i]['txtCodigo'])."' AND codsucursal = '".limpiar($_POST['codsucursal'])."'";
		foreach ($this->dbh->query($sql) as $row)
		{
			$this->p[] = $row;
		}
		$existenciabd = $row['existencia'];

		$query = "INSERT INTO detalleventas values (null, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?); ";
		$stmt = $this->dbh->prepare($query);
		$stmt->bindParam(1, $codventa);
	    $stmt->bindParam(2, $codproducto);
	    $stmt->bindParam(3, $producto);
		$stmt->bindParam(4, $cantidad);
		$stmt->bindParam(5, $preciocompra);
		$stmt->bindParam(6, $precioventa);
		$stmt->bindParam(7, $ivaproducto);
		$stmt->bindParam(8, $descproducto);
		$stmt->bindParam(9, $valortotal);
		$stmt->bindParam(10, $totaldescuentov);
		$stmt->bindParam(11, $valorneto);
		$stmt->bindParam(12, $valorneto2);
		$stmt->bindParam(13, $codsucursal);
			
		$codproducto = limpiar($detalle[$i]['txtCodigo']);
		$producto = limpiar($detalle[$i]['producto']);
		$cantidad = limpiar($detalle[$i]['cantidad']);
		$preciocompra = limpiar($detalle[$i]['precio']);
		$precioventa = limpiar($detalle[$i]['precio2']);
		$ivaproducto = limpiar($detalle[$i]['ivaproducto']);
		$descproducto = limpiar($detalle[$i]['descproducto']);
		$descuento = $detalle[$i]['descproducto']/100;
		$valortotal = number_format($detalle[$i]['precio2']*$detalle[$i]['cantidad'], 2, '.', '');
		$totaldescuentov = number_format($valortotal*$descuento, 2, '.', '');
	    $valorneto = number_format($valortotal-$totaldescuentov, 2, '.', '');
		$valorneto2 = number_format($detalle[$i]['precio']*$detalle[$i]['cantidad'], 2, '.', '');
		$codsucursal = limpiar($_SESSION["codsucursal"]);
		$stmt->execute();

	    ##################### ACTUALIZO LA EXISTENCIA DEL ALMACEN ####################
		$sql = " UPDATE productos set "
			  ." existencia = ? "
			  ." where "
			  ." codproducto = '".limpiar($detalle[$i]['txtCodigo'])."' and codsucursal = '".limpiar($_POST["codsucursal"])."';
			   ";
		$stmt = $this->dbh->prepare($sql);
		$stmt->bindParam(1, $existencia);
		$cantventa = limpiar($detalle[$i]['cantidad']);
		$existencia = $existenciabd-$cantventa;
		$stmt->execute();

		############### REGISTRAMOS LOS DATOS DE PRODUCTOS EN KARDEX ###############
        $query = "INSERT INTO kardex values (null, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?); ";
		$stmt = $this->dbh->prepare($query);
		$stmt->bindParam(1, $codventa);
		$stmt->bindParam(2, $codcliente);
		$stmt->bindParam(3, $codproducto);
		$stmt->bindParam(4, $movimiento);
		$stmt->bindParam(5, $entradas);
		$stmt->bindParam(6, $salidas);
		$stmt->bindParam(7, $devolucion);
		$stmt->bindParam(8, $stockactual);
		$stmt->bindParam(9, $ivaproducto);
		$stmt->bindParam(10, $descproducto);
		$stmt->bindParam(11, $precio);
		$stmt->bindParam(12, $documento);
		$stmt->bindParam(13, $fechakardex);		
		$stmt->bindParam(14, $codsucursal);

		$codcliente = limpiar($_POST["codcliente"]);
		$codproducto = limpiar($detalle[$i]['txtCodigo']);
		$movimiento = limpiar("SALIDAS");
		$entradas = limpiar("0");
		$salidas= limpiar($detalle[$i]['cantidad']);
		$devolucion = limpiar("0");
		$stockactual = limpiar($existenciabd-$detalle[$i]['cantidad']);
		$precio = limpiar($detalle[$i]["precio2"]);
		$ivaproducto = limpiar($detalle[$i]['ivaproducto']);
		$descproducto = limpiar($detalle[$i]['descproducto']);
		$documento = limpiar("VENTA: ".$codventa);
		$fechakardex = limpiar(date("Y-m-d"));
		$codsucursal = limpiar($_POST["codsucursal"]);
		$stmt->execute();
      }
		
	####################### DESTRUYO LA VARIABLE DE SESSION #####################
	unset($_SESSION["CarritoVenta"]);
    $this->dbh->commit();

    ################ AGREGAMOS EL INGRESO DE VENTAS PAGADAS A CAJA ##############
	if (limpiar($_POST["tipopago"]=="CONTADO")){

		$sql = "SELECT ingresos FROM arqueocaja WHERE codcaja = '".limpiar($_POST["codcaja"])."' AND statusarqueo = 1";
		foreach ($this->dbh->query($sql) as $row)
		{
			$this->p[] = $row;
		}
		$ingreso = ($row['ingresos']== "" ? "0.00" : $row['ingresos']);

		$sql = "UPDATE arqueocaja set "
		." ingresos = ? "
		." WHERE "
		." codcaja = ? AND statusarqueo = 1;
		";
		$stmt = $this->dbh->prepare($sql);
		$stmt->bindParam(1, $txtTotal);
		$stmt->bindParam(2, $codcaja);

		$txtTotal = number_format($_POST["txtTotal"]+$ingreso, 2, '.', '');
		$codcaja = limpiar($_POST["codcaja"]);
		$stmt->execute();
	}
    ################ AGREGAMOS EL INGRESO DE VENTAS PAGADAS A CAJA ################

    ########## AGREGAMOS EL INGRESO Y ABONOS DE VENTAS A CREDITOS A CAJA ##########
	if (limpiar($_POST["tipopago"]=="CREDITO" && $_POST["montoabono"]=="0.00" && $_POST["montoabono"]=="0")) {

		$sql = "SELECT creditos FROM arqueocaja WHERE codcaja = '".limpiar($_POST["codcaja"])."' AND statusarqueo = 1";
		foreach ($this->dbh->query($sql) as $row)
		{
			$this->p[] = $row;
		}
		$credito = ($row['creditos']== "" ? "0.00" : $row['creditos']);

		$sql = " UPDATE arqueocaja SET "
		." creditos = ? "
		." where "
		." codcaja = ? and statusarqueo = 1;
		";
		$stmt = $this->dbh->prepare($sql);
		$stmt->bindParam(1, $txtTotal);
		$stmt->bindParam(2, $codcaja);

		$txtTotal = number_format($_POST["txtTotal"]+$credito, 2, '.', '');
		$codcaja = limpiar($_POST["codcaja"]);
		$stmt->execute(); 

		$sql = " SELECT codcliente FROM creditosxclientes WHERE codcliente = ? AND codsucursal = ?";
		$stmt = $this->dbh->prepare($sql);
		$stmt->execute(array($_POST["codcliente"],$_POST["codsucursal"]));
		$num = $stmt->rowCount();
		if($num == 0)
		{
			$query = "INSERT INTO creditosxclientes values (null, ?, ?, ?);";
			$stmt = $this->dbh->prepare($query);
			$stmt->bindParam(1, $codcliente);
			$stmt->bindParam(2, $montocredito);
			$stmt->bindParam(3, $codsucursal);

			$codcliente = limpiar($_POST["codcliente"]);
			$montocredito = number_format($_POST["txtTotal"]-$_POST["montoabono"], 2, '.', '');
			$codsucursal = limpiar($_POST["codsucursal"]);
			$stmt->execute();

		} else { 

			$sql = "UPDATE creditosxclientes set"
			." montocredito = ? "
			." where "
			." codcliente = ? AND codsucursal = ?;
			";
			$stmt = $this->dbh->prepare($sql);
			$stmt->bindParam(1, $montocredito);
			$stmt->bindParam(2, $codcliente);
			$stmt->bindParam(3, $codsucursal);

			$montocredito = number_format($montoactual+($_POST["txtTotal"]-$_POST["montoabono"]), 2, '.', '');
			$codcliente = limpiar($_POST["codcliente"]);
			$codsucursal = limpiar($_POST["codsucursal"]);
			$stmt->execute();
		}

	} else if (limpiar($_POST["tipopago"]=="CREDITO" && $_POST["montoabono"]!="0.00" && $_POST["montoabono"]!="0")) { 

		$sql = "SELECT creditos, abonos FROM arqueocaja WHERE codcaja = '".limpiar($_POST["codcaja"])."' AND statusarqueo = 1";
		foreach ($this->dbh->query($sql) as $row)
		{
			$this->p[] = $row;
		}
		$credito = ($row['creditos']== "" ? "0.00" : $row['creditos']);
		$abono = ($row['abonos']== "" ? "0.00" : $row['abonos']);

		$sql = " UPDATE arqueocaja SET "
		." creditos = ?, "
		." abonos = ? "
		." where "
		." codcaja = ? and statusarqueo = 1;
		";
		$stmt = $this->dbh->prepare($sql);
		$stmt->bindParam(1, $txtTotal);
		$stmt->bindParam(2, $totalabono);
		$stmt->bindParam(3, $codcaja);

		$txtTotal = number_format($_POST["txtTotal"]+$credito, 2, '.', '');
		$totalabono = number_format($_POST["montoabono"]+$abono, 2, '.', '');
		$codcaja = limpiar($_POST["codcaja"]);
		$stmt->execute();

		$query = "INSERT INTO abonoscreditosventas values (null, ?, ?, ?, ?, ?, ?); ";
		$stmt = $this->dbh->prepare($query);
		$stmt->bindParam(1, $codcaja);
		$stmt->bindParam(2, $codventa);
		$stmt->bindParam(3, $codcliente);
		$stmt->bindParam(4, $montoabono);
		$stmt->bindParam(5, $fechaabono);
		$stmt->bindParam(6, $codsucursal);

		$codcaja = limpiar($_POST["codcaja"]);
		$codcliente = limpiar($_POST["codcliente"]);
		$montoabono = number_format($_POST["montoabono"], 2, '.', '');
		$fechaabono = limpiar($fecha);
		$codsucursal = limpiar($_POST["codsucursal"]);
		$stmt->execute();

		$sql = " SELECT codcliente FROM creditosxclientes WHERE codcliente = ? AND codsucursal = ?";
		$stmt = $this->dbh->prepare($sql);
		$stmt->execute(array($_POST["codcliente"],$_POST["codsucursal"]));
		$num = $stmt->rowCount();
		if($num == 0)
		{
			$query = "INSERT INTO creditosxclientes values (null, ?, ?, ?);";
			$stmt = $this->dbh->prepare($query);
			$stmt->bindParam(1, $codcliente);
			$stmt->bindParam(2, $montocredito);
			$stmt->bindParam(3, $codsucursal);

			$codcliente = limpiar($_POST["codcliente"]);
			$montocredito = number_format($_POST["txtTotal"]-$_POST["montoabono"], 2, '.', '');
			$codsucursal = limpiar($_POST["codsucursal"]);
			$stmt->execute();

		} else { 

			$sql = "UPDATE creditosxclientes set"
			." montocredito = ? "
			." where "
			." codcliente = ? AND codsucursal = ?;
			";
			$stmt = $this->dbh->prepare($sql);
			$stmt->bindParam(1, $montocredito);
			$stmt->bindParam(2, $codcliente);
			$stmt->bindParam(3, $codsucursal);

			$montocredito = number_format($montoactual+($_POST["txtTotal"]-$_POST["montoabono"]), 2, '.', '');
			$codcliente = limpiar($_POST["codcliente"]);
			$codsucursal = limpiar($_POST["codsucursal"]);
			$stmt->execute();
		}
	}
    ########### AGREGAMOS EL INGRESO Y ABONOS DE VENTAS A CREDITOS A CAJA #########

echo "<span class='fa fa-check-square-o'></span> LA VENTA DE PRODUCTOS HA SIDO REGISTRADA EXITOSAMENTE <a href='reportepdf?codventa=".encrypt($codventa)."&codsucursal=".encrypt($codsucursal)."&tipo=".encrypt($tipodocumento)."' class='on-default' data-placement='left' data-toggle='tooltip' data-original-title='Imprimir Documento' target='_black' rel='noopener noreferrer'><font color='black'><strong>IMPRIMIR REPORTE</strong></font color></a></div>";

echo "<script>window.open('reportepdf?codventa=".encrypt($codventa)."&codsucursal=".encrypt($codsucursal)."&tipo=".encrypt($tipodocumento)."', '_blank');</script>";
	exit;
   }
}
########################### FUNCION REGISTRAR VENTAS ################################

########################## FUNCION LISTAR VENTAS ################################
public function ListarVentas()
{
	self::SetNames();

if ($_SESSION['acceso'] == "administradorG") {

	$sql = "SELECT 
	ventas.idventa, 
	ventas.tipodocumento, 
	ventas.codventa, 
	ventas.codserie, 
	ventas.codautorizacion, 
	ventas.codcaja, 
	ventas.codcliente, 
	ventas.subtotalivasi, 
	ventas.subtotalivano, 
	ventas.iva, 
	ventas.totaliva, 
	ventas.descuento, 
	ventas.totaldescuento, 
	ventas.totalpago, 
	ventas.totalpago2, 
	ventas.tipopago, 
	ventas.formapago, 
	ventas.montopagado, 
	ventas.montodevuelto, 
	ventas.fechavencecredito, 
	ventas.fechapagado,
	ventas.statusventa, 
	ventas.fechaventa,
	ventas.observaciones, 
	ventas.codsucursal, 
	sucursales.documsucursal, 
	sucursales.cuitsucursal, 
	sucursales.razonsocial,
	sucursales.documencargado,
	sucursales.dniencargado,
	sucursales.nomencargado,
	usuarios.dni,
	usuarios.nombres,
	cajas.nrocaja,
	cajas.nomcaja,
	clientes.documcliente, 
	clientes.dnicliente, 
	clientes.nomcliente, 
	documentos.documento,
	documentos2.documento AS documento2, 
	documentos3.documento AS documento3,
	mediospagos.mediopago,
	SUM(detalleventas.cantventa) AS articulos 
	FROM (ventas LEFT JOIN detalleventas ON detalleventas.codventa = ventas.codventa)
	INNER JOIN cajas ON ventas.codcaja = cajas.codcaja 
	LEFT JOIN sucursales ON ventas.codsucursal = sucursales.codsucursal 
	LEFT JOIN documentos ON sucursales.documsucursal = documentos.coddocumento
	LEFT JOIN documentos AS documentos2 ON sucursales.documencargado = documentos2.coddocumento 
	LEFT JOIN clientes ON ventas.codcliente = clientes.codcliente
	LEFT JOIN documentos AS documentos3 ON clientes.documcliente = documentos3.coddocumento
	LEFT JOIN mediospagos ON ventas.formapago = mediospagos.codmediopago 
	LEFT JOIN usuarios ON ventas.codigo = usuarios.codigo GROUP BY detalleventas.codventa ORDER BY ventas.idventa DESC";
	foreach ($this->dbh->query($sql) as $row)
	{
		$this->p[] = $row;
	}
	return $this->p;
	$this->dbh=null;

 } else if($_SESSION["acceso"] == "cajero") {

      $sql = "SELECT 
	ventas.idventa, 
	ventas.tipodocumento, 
	ventas.codventa, 
	ventas.codserie, 
	ventas.codautorizacion, 
	ventas.codcaja, 
	ventas.codcliente, 
	ventas.subtotalivasi, 
	ventas.subtotalivano, 
	ventas.iva, 
	ventas.totaliva, 
	ventas.descuento, 
	ventas.totaldescuento, 
	ventas.totalpago, 
	ventas.totalpago2, 
	ventas.tipopago, 
	ventas.formapago, 
	ventas.montopagado, 
	ventas.montodevuelto, 
	ventas.fechavencecredito, 
	ventas.fechapagado,
	ventas.statusventa, 
	ventas.fechaventa,
	ventas.observaciones,  
	ventas.codsucursal, 
	sucursales.documsucursal, 
	sucursales.cuitsucursal, 
	sucursales.razonsocial,
	sucursales.documencargado,
	sucursales.dniencargado,
	sucursales.nomencargado,
	usuarios.dni,
	usuarios.nombres,
	cajas.nrocaja,
	cajas.nomcaja,
	clientes.documcliente, 
	clientes.dnicliente, 
	clientes.nomcliente, 
	documentos.documento,
	documentos2.documento AS documento2, 
	documentos3.documento AS documento3,
	mediospagos.mediopago,
	SUM(detalleventas.cantventa) AS articulos 
	FROM (ventas LEFT JOIN detalleventas ON detalleventas.codventa = ventas.codventa)
	INNER JOIN cajas ON ventas.codcaja = cajas.codcaja 
	LEFT JOIN sucursales ON ventas.codsucursal = sucursales.codsucursal 
	LEFT JOIN documentos ON sucursales.documsucursal = documentos.coddocumento
	LEFT JOIN documentos AS documentos2 ON sucursales.documencargado = documentos2.coddocumento 
	LEFT JOIN clientes ON ventas.codcliente = clientes.codcliente
	LEFT JOIN documentos AS documentos3 ON clientes.documcliente = documentos3.coddocumento
	LEFT JOIN mediospagos ON ventas.formapago = mediospagos.codmediopago 
	LEFT JOIN usuarios ON ventas.codigo = usuarios.codigo WHERE ventas.codigo = '".limpiar($_SESSION["codigo"])."' GROUP BY detalleventas.codventa ORDER BY ventas.idventa DESC";
	foreach ($this->dbh->query($sql) as $row)
	{
		$this->p[] = $row;
	}
	return $this->p;
	$this->dbh=null;

} else {

   $sql = "SELECT 
	ventas.idventa, 
	ventas.tipodocumento, 
	ventas.codventa, 
	ventas.codserie, 
	ventas.codautorizacion, 
	ventas.codcaja, 
	ventas.codcliente, 
	ventas.subtotalivasi, 
	ventas.subtotalivano, 
	ventas.iva, 
	ventas.totaliva, 
	ventas.descuento, 
	ventas.totaldescuento, 
	ventas.totalpago, 
	ventas.totalpago2, 
	ventas.tipopago, 
	ventas.formapago, 
	ventas.montopagado, 
	ventas.montodevuelto, 
	ventas.fechavencecredito, 
	ventas.fechapagado,
	ventas.statusventa, 
	ventas.fechaventa,
	ventas.observaciones,  
	ventas.codsucursal, 
	sucursales.documsucursal, 
	sucursales.cuitsucursal, 
	sucursales.razonsocial,
	sucursales.documencargado,
	sucursales.dniencargado,
	sucursales.nomencargado,
	usuarios.dni,
	usuarios.nombres,
	cajas.nrocaja,
	cajas.nomcaja,
	clientes.documcliente, 
	clientes.dnicliente, 
	clientes.nomcliente, 
	documentos.documento,
	documentos2.documento AS documento2, 
	documentos3.documento AS documento3,
	mediospagos.mediopago,
	SUM(detalleventas.cantventa) AS articulos 
	FROM (ventas LEFT JOIN detalleventas ON detalleventas.codventa = ventas.codventa)
	INNER JOIN cajas ON ventas.codcaja = cajas.codcaja 
	LEFT JOIN sucursales ON ventas.codsucursal = sucursales.codsucursal 
	LEFT JOIN documentos ON sucursales.documsucursal = documentos.coddocumento
	LEFT JOIN documentos AS documentos2 ON sucursales.documencargado = documentos2.coddocumento 
	LEFT JOIN clientes ON ventas.codcliente = clientes.codcliente
	LEFT JOIN documentos AS documentos3 ON clientes.documcliente = documentos3.coddocumento
	LEFT JOIN mediospagos ON ventas.formapago = mediospagos.codmediopago 
	LEFT JOIN usuarios ON ventas.codigo = usuarios.codigo WHERE ventas.codsucursal = '".limpiar($_SESSION["codsucursal"])."' GROUP BY detalleventas.codventa ORDER BY ventas.idventa DESC";
	foreach ($this->dbh->query($sql) as $row)
	{
		$this->p[] = $row;
	}
	return $this->p;
	$this->dbh=null;

     }
}
############################ FUNCION LISTAR VENTAS ############################

############################ FUNCION ID VENTAS #################################
public function VentasPorId()
	{
	self::SetNames();
	$sql = "SELECT 
		ventas.idventa, 
		ventas.tipodocumento, 
		ventas.codventa, 
		ventas.codserie, 
		ventas.codautorizacion, 
		ventas.codcaja, 
		ventas.codcliente, 
		ventas.subtotalivasi, 
		ventas.subtotalivano, 
		ventas.iva, 
		ventas.totaliva, 
		ventas.descuento, 
		ventas.totaldescuento, 
		ventas.totalpago, 
		ventas.totalpago2, 
		ventas.tipopago, 
		ventas.formapago, 
		ventas.montopagado, 
		ventas.montodevuelto, 
		ventas.fechavencecredito, 
	    ventas.fechapagado,
		ventas.statusventa, 
		ventas.fechaventa,
	    ventas.observaciones,  
		ventas.codsucursal,
		sucursales.documsucursal,
		sucursales.cuitsucursal,
		sucursales.razonsocial,
		sucursales.direcsucursal,
		sucursales.correosucursal,
		sucursales.tlfsucursal,
		sucursales.documencargado,
		sucursales.dniencargado,
		sucursales.nomencargado,
		sucursales.tlfencargado,
		sucursales.llevacontabilidad,
		clientes.codcliente,
		clientes.documcliente,
		clientes.dnicliente, 
		clientes.nomcliente, 
		clientes.tlfcliente, 
		clientes.id_provincia, 
		clientes.id_departamento, 
		clientes.direccliente, 
		clientes.emailcliente,
		clientes.limitecredito,documentos.documento,
	    documentos2.documento AS documento2, 
	    documentos3.documento AS documento3,
	    cajas.nrocaja,
	    cajas.nomcaja,
	    mediospagos.mediopago,
	    usuarios.dni, 
	    usuarios.nombres,
	    provincias.provincia,
	    departamentos.departamento,
	    provincias2.provincia AS provincia2,
	    departamentos2.departamento AS departamento2,
		ROUND(SUM(if(pag.montocredito!='0',pag.montocredito,'0.00')), 2) montoactual,
	    ROUND(SUM(if(pag.montocredito!='0',clientes.limitecredito-pag.montocredito,clientes.limitecredito)), 2) creditodisponible,
	    pag2.abonototal
        FROM (ventas INNER JOIN sucursales ON ventas.codsucursal = sucursales.codsucursal)
	LEFT JOIN documentos ON sucursales.documsucursal = documentos.coddocumento
	LEFT JOIN documentos AS documentos2 ON sucursales.documencargado = documentos2.coddocumento
	LEFT JOIN provincias ON sucursales.id_provincia = provincias.id_provincia 
	LEFT JOIN departamentos ON sucursales.id_departamento = departamentos.id_departamento  
	LEFT JOIN clientes ON ventas.codcliente = clientes.codcliente
	LEFT JOIN documentos AS documentos3 ON clientes.documcliente = documentos3.coddocumento
	LEFT JOIN provincias AS provincias2 ON clientes.id_provincia = provincias2.id_provincia 
	LEFT JOIN departamentos AS departamentos2 ON clientes.id_departamento = departamentos2.id_departamento 
	LEFT JOIN cajas ON ventas.codcaja = cajas.codcaja
	LEFT JOIN mediospagos ON ventas.formapago = mediospagos.codmediopago 
	LEFT JOIN usuarios ON ventas.codigo = usuarios.codigo
    
    LEFT JOIN
        (SELECT
        codcliente, montocredito       
        FROM creditosxclientes 
        WHERE codsucursal = '".limpiar(decrypt($_GET["codsucursal"]))."') pag ON pag.codcliente = clientes.codcliente
    
    LEFT JOIN
        (SELECT
        codventa, codcliente, SUM(if(montoabono!='0',montoabono,'0.00')) AS abonototal
        FROM abonoscreditosventas 
        WHERE codventa = '".limpiar(decrypt($_GET["codventa"]))."' AND codsucursal = '".limpiar(decrypt($_GET["codsucursal"]))."') pag2 ON pag2.codcliente = clientes.codcliente

        WHERE ventas.codventa = ? AND ventas.codsucursal = ?";
    $stmt = $this->dbh->prepare($sql);
	$stmt->execute(array(decrypt($_GET["codventa"]),decrypt($_GET["codsucursal"])));
	$num = $stmt->rowCount();
	if($num==0)
	{
		echo "";
	}
	else
	{
		if($row = $stmt->fetch(PDO::FETCH_ASSOC))
		{
			$this->p[] = $row;
		}
		return $this->p;
		$this->dbh=null;
	}
}
############################ FUNCION ID VENTAS #################################
	
########################### FUNCION VER DETALLES VENTAS ##########################
public function VerDetallesVentas()
	{
	self::SetNames();
	$sql = "SELECT
	detalleventas.coddetalleventa,
	detalleventas.codventa,
	detalleventas.codproducto,
	detalleventas.producto,
	detalleventas.cantventa,
	detalleventas.preciocompra,
	detalleventas.precioventa,
	detalleventas.ivaproducto,
	detalleventas.descproducto,
	detalleventas.valortotal, 
	detalleventas.totaldescuentov,
	detalleventas.valorneto,
	detalleventas.valorneto2,
	detalleventas.codsucursal,
	marcas.nommarca,
	modelos.nommodelo,
	presentaciones.nompresentacion
	FROM detalleventas INNER JOIN productos ON detalleventas.codproducto = productos.codproducto 
	INNER JOIN marcas ON productos.codmarca = marcas.codmarca
	LEFT JOIN modelos ON productos.codmodelo = modelos.codmodelo 
	LEFT JOIN presentaciones ON productos.codpresentacion = presentaciones.codpresentacion 
	WHERE detalleventas.codventa = ? AND detalleventas.codsucursal = ? GROUP BY productos.codproducto";
	$stmt = $this->dbh->prepare($sql);
	$stmt->execute(array(decrypt($_GET["codventa"]),decrypt($_GET["codsucursal"])));
	$num = $stmt->rowCount();
	
	while($row = $stmt->fetch(PDO::FETCH_ASSOC))
	{
		$this->p[]=$row;
	}
		return $this->p;
		$this->dbh=null;
}
############################ FUNCION VER DETALLES VENTAS #######################

############################# FUNCION ACTUALIZAR VENTAS ##########################
public function ActualizarVentas()
	{
	self::SetNames();
	if(empty($_POST["codventa"]) or empty($_POST["codsucursal"]))
	{
		echo "1";
		exit;
	}

	    ############ CONSULTO TOTAL ACTUAL ##############
		$sql = "SELECT totalpago FROM ventas WHERE codventa = '".limpiar($_POST["codventa"])."' AND codsucursal = '".limpiar($_POST["codsucursal"])."'";
		foreach ($this->dbh->query($sql) as $row)
		{
			$this->p[] = $row;
		}
		$totalpagobd = $row['totalpago'];

	   ################### SELECCIONE LOS DATOS DEL CLIENTE ######################
	    $sql = "SELECT
	    clientes.nomcliente, 
	    clientes.emailcliente, 
	    clientes.limitecredito,
	    ROUND(SUM(if(pag.montocredito!='0',pag.montocredito,'0.00')), 2) montoactual,
	    ROUND(SUM(if(pag.montocredito!='0',clientes.limitecredito-pag.montocredito,clientes.limitecredito)), 2) creditodisponible
        FROM clientes 
        LEFT JOIN
           (SELECT
           codcliente, montocredito       
           FROM creditosxclientes 
           WHERE codsucursal = '".limpiar($_POST['codsucursal'])."') pag ON pag.codcliente = clientes.codcliente
           WHERE clientes.codcliente = ?";
		$stmt = $this->dbh->prepare($sql);
		$stmt->execute(array($_POST['codcliente']));
		$num = $stmt->rowCount();
		if($row = $stmt->fetch(PDO::FETCH_ASSOC))
		{
			$p[] = $row;
		}
        $nomcliente = $row['nomcliente'];
        $emailcliente = $row['emailcliente'];
        $limitecredito = $row['limitecredito'];
        $montoactual = $row['montoactual'];
        $creditodisponible = $row['creditodisponible'];
        $montoabono = (empty($_POST["abonototal"]) ? "0.00" : $_POST["abonototal"]);
        $total = number_format($_POST["txtTotal"], 2, '.', '');

		for($i=0;$i<count($_POST['coddetalleventa']);$i++){  //recorro el array
			if (!empty($_POST['coddetalleventa'][$i])) {

				if($_POST['cantventa'][$i]==0){

					echo "2";
					exit();

				}
			}
		}

    if ($_POST["tipopago"] == "CREDITO") {

      
		   /*if ($limitecredito != "0.00" && $total > $creditodisponible) {

	           echo "3";
		       exit;

	        }*/ 
	 }

	$this->dbh->beginTransaction();
	for($i=0;$i<count($_POST['coddetalleventa']);$i++){  //recorro el array
	if (!empty($_POST['coddetalleventa'][$i])) {

	    $sql = "SELECT cantventa FROM detalleventas WHERE coddetalleventa = '".limpiar($_POST['coddetalleventa'][$i])."' AND codventa = '".limpiar($_POST["codventa"])."' AND codsucursal = '".limpiar($_POST["codsucursal"])."'";
		foreach ($this->dbh->query($sql) as $row)
		{
			$this->p[] = $row;
		}
		
		$cantidadbd = $row['cantventa'];

	if($cantidadbd != $_POST['cantventa'][$i]){

	############## VALIDO SI LA CANTIDAD ES MAYOR QUE LA EXISTENCIA #############
	   $sql = "SELECT existencia FROM productos WHERE codproducto = '".limpiar($_POST['codproducto'][$i])."' AND codsucursal = '".limpiar($_POST["codsucursal"])."'";
		    foreach ($this->dbh->query($sql) as $row)
		{
			$this->p[] = $row;
		}
		$existenciabd = $row['existencia'];
		$cantventa = $_POST["cantventa"][$i];
		$cantidadventabd = $_POST["cantidadventabd"][$i];
		$totalventa = $cantventa-$cantidadventabd;

     if ($totalventa > $existenciabd) 
        { 
		    echo "4";
		    exit;
	    }

	$query = "UPDATE detalleventas set"
		." cantventa = ?, "
		." valortotal = ?, "
		." totaldescuentov = ?, "
		." valorneto = ?, "
		." valorneto2 = ? "
		." WHERE "
		." coddetalleventa = ? AND codventa = ? AND codsucursal = ?;
		";
		$stmt = $this->dbh->prepare($query);
		$stmt->bindParam(1, $cantventa);
		$stmt->bindParam(2, $valortotal);
		$stmt->bindParam(3, $totaldescuentov);
		$stmt->bindParam(4, $valorneto);
		$stmt->bindParam(5, $valorneto2);
		$stmt->bindParam(6, $coddetalleventa);
		$stmt->bindParam(7, $codventa);
		$stmt->bindParam(8, $codsucursal);

		$cantventa = limpiar($_POST['cantventa'][$i]);
		$preciocompra = limpiar($_POST['preciocompra'][$i]);
		$precioventa = limpiar($_POST['precioventa'][$i]);
		$ivaproducto = limpiar($_POST['ivaproducto'][$i]);
		$descuento = $_POST['descproducto'][$i]/100;
		$valortotal = number_format($_POST['precioventa'][$i] * $_POST['cantventa'][$i], 2, '.', '');
		$totaldescuentov = number_format($valortotal * $descuento, 2, '.', '');
		$valorneto = number_format($valortotal - $totaldescuentov, 2, '.', '');
		$valorneto2 = number_format($_POST['preciocompra'][$i] * $_POST['cantventa'][$i], 2, '.', '');
		$coddetalleventa = limpiar($_POST['coddetalleventa'][$i]);
		$codventa = limpiar($_POST["codventa"]);
		$codsucursal = limpiar($_POST["codsucursal"]);
		$stmt->execute();

		############### ACTUALIZAMOS EXISTENCIA DEL PRODUCTO EN ALMACEN #################
		$sql2 = " UPDATE productos set "
			  ." existencia = ? "
			  ." WHERE "
			  ." codproducto = '".limpiar($_POST["codproducto"][$i])."' AND codsucursal = '".limpiar($_POST["codsucursal"])."';
			  ";
			  $stmt = $this->dbh->prepare($sql2);
			  $stmt->bindParam(1, $existencia);
			  $existencia = $existenciabd-$totalventa;
			  $stmt->execute();

		############### ACTUALIZAMOS LOS DATOS DEL PRODUCTO EN KARDEX ###################
		$sql3 = " UPDATE kardex set "
		      ." salidas = ?, "
		      ." stockactual = ? "
			  ." WHERE "
			  ." codproceso = '".limpiar($_POST["codventa"])."' and codproducto = '".limpiar($_POST["codproducto"][$i])."' AND codsucursal = '".limpiar($_POST["codsucursal"])."';
			   ";
		$stmt = $this->dbh->prepare($sql3);
		$stmt->bindParam(1, $salidas);
		$stmt->bindParam(2, $existencia);
		
		$salidas = limpiar($_POST["cantventa"][$i]);
		$stmt->execute();

			} else {

               echo "";

		       }
	        } 
        }    
            $this->dbh->commit();

        ############ SUMO LOS IMPORTE DE PRODUCTOS CON IVA ##############
        $sql3 = "SELECT SUM(valorneto) AS valorneto, SUM(valorneto2) AS valorneto2 FROM detalleventas WHERE codventa = '".limpiar($_POST["codventa"])."' AND codsucursal = '".limpiar($_POST["codsucursal"])."' AND ivaproducto = 'SI'";
        foreach ($this->dbh->query($sql3) as $row3)
        {
        	$this->p[] = $row3;
        }
        $subtotalivasi = ($row3['valorneto']== "" ? "0.00" : $row3['valorneto']);
        $subtotalivasi2 = ($row3['valorneto2']== "" ? "0.00" : $row3['valorneto2']);

	    ############ SUMO LOS IMPORTE DE PRODUCTOS SIN IVA ##############
        $sql4 = "SELECT SUM(valorneto) AS valorneto, SUM(valorneto2) AS valorneto2 FROM detalleventas WHERE codventa = '".limpiar($_POST["codventa"])."' AND codsucursal = '".limpiar($_POST["codsucursal"])."' AND ivaproducto = 'NO'";
        foreach ($this->dbh->query($sql4) as $row4)
        {
        	$this->p[] = $row4;
        }
        $subtotalivano = ($row4['valorneto']== "" ? "0.00" : $row4['valorneto']);
        $subtotalivano2 = ($row4['valorneto2']== "" ? "0.00" : $row4['valorneto2']);

        ############ ACTUALIZO LOS TOTALES EN LA COTIZACION ##############
        $sql = " UPDATE ventas SET "
        ." codcliente = ?, "
        ." subtotalivasi = ?, "
        ." subtotalivano = ?, "
        ." totaliva = ?, "
        ." descuento = ?, "
        ." totaldescuento = ?, "
        ." totalpago = ?, "
		." totalpago2 = ?, "
		." montodevuelto = ? "
        ." WHERE "
        ." codventa = ? AND codsucursal = ?;
        ";
        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(1, $codcliente);
        $stmt->bindParam(2, $subtotalivasi);
        $stmt->bindParam(3, $subtotalivano);
        $stmt->bindParam(4, $totaliva);
        $stmt->bindParam(5, $descuento);
        $stmt->bindParam(6, $totaldescuento);
        $stmt->bindParam(7, $totalpago);
        $stmt->bindParam(8, $totalpago2);
		$stmt->bindParam(9, $montodevuelto);
        $stmt->bindParam(10, $codventa);
        $stmt->bindParam(11, $codsucursal);

        $codcliente = limpiar($_POST["codcliente"]);
        $iva = $_POST["iva"]/100;
        $totaliva = number_format($subtotalivasi*$iva, 2, '.', '');
        $descuento = limpiar($_POST["descuento"]);
        $txtDescuento = $_POST["descuento"]/100;
        $total = number_format($subtotalivasi+$subtotalivano+$totaliva, 2, '.', '');
        $totaldescuento = number_format($total*$txtDescuento, 2, '.', '');
        $totalpago = number_format($total-$totaldescuento, 2, '.', '');
        $totalpago2 = number_format($subtotalivasi2+$subtotalivano2, 2, '.', '');
		$montodevuelto = number_format($totalpago > $_POST["pagado"] ? "0.00" : $_POST["pagado"]-$totalpago, 2, '.', '');
        $codventa = limpiar($_POST["codventa"]);
        $codsucursal = limpiar($_POST["codsucursal"]);
        $tipodocumento = limpiar($_POST["tipodocumento"]);
        $tipopago = limpiar($_POST["tipopago"]);
        $observaciones = limpiar($_POST["observaciones"]);
        $fecha = date("Y-m-d h:i:s");
        $stmt->execute();

    ################## AGREGAMOS O QUITAMOS LA DIFERENCIA EN CAJA ##############
	if (limpiar($_POST["tipopago"]=="CONTADO") && $totalpagobd != $totalpago){

		$sql = "SELECT ingresos FROM arqueocaja WHERE codcaja = '".limpiar($_POST["codcaja"])."' AND statusarqueo = 1";
		foreach ($this->dbh->query($sql) as $row)
		{
			$this->p[] = $row;
		}
		$ingreso = ($row['ingresos']== "" ? "0.00" : $row['ingresos']);

		$sql = "UPDATE arqueocaja set "
		." ingresos = ? "
		." WHERE "
		." codcaja = ? AND statusarqueo = 1;
		";
		$stmt = $this->dbh->prepare($sql);
		$stmt->bindParam(1, $TxtTotal);
		$stmt->bindParam(2, $codcaja);

        $TxtTotal = number_format(($totalpagobd>$totalpago ? $ingreso-($totalpagobd-$totalpago) : $ingreso+($totalpago-$totalpagobd)), 2, '.', '');
		$codcaja = limpiar($_POST["codcaja"]);
		$stmt->execute();
	}
    ################ AGREGAMOS O QUITAMOS LA DIFERENCIA EN CAJA ####################

    ############## AGREGAMOS O QUITAMOS LA DIFERENCIA EN CAJA ##################
	if (limpiar($_POST["tipopago"]=="CREDITO") && $totalpagobd != $totalpago) {

		$sql = "SELECT creditos FROM arqueocaja WHERE codcaja = '".limpiar($_POST["codcaja"])."' AND statusarqueo = 1";
		foreach ($this->dbh->query($sql) as $row)
		{
			$this->p[] = $row;
		}
		$credito = ($row['creditos']== "" ? "0.00" : $row['creditos']);

		$sql = " UPDATE arqueocaja SET "
		." creditos = ? "
		." where "
		." codcaja = ? and statusarqueo = 1;
		";
		$stmt = $this->dbh->prepare($sql);
		$stmt->bindParam(1, $TxtTotal);
		$stmt->bindParam(2, $codcaja);

		$TxtTotal = number_format(($totalpagobd>$totalpago ? $credito-($totalpagobd-$totalpago) : $credito+($totalpago-$totalpagobd)), 2, '.', '');
		$codcaja = limpiar($_POST["codcaja"]);
		$stmt->execute(); 	

		$sql = "UPDATE creditosxclientes set"
		." montocredito = ? "
		." where "
		." codcliente = ? AND codsucursal = ?;
		";
		$stmt = $this->dbh->prepare($sql);
		$stmt->bindParam(1, $montocredito);
		$stmt->bindParam(2, $codcliente);
		$stmt->bindParam(3, $codsucursal);

        $montocredito = number_format(($totalpagobd>$totalpago ? $montoactual-($totalpagobd-$totalpago) : $montoactual+($totalpago-$totalpagobd)), 2, '.', '');
		$codcliente = limpiar($_POST["codcliente"]);
		$codsucursal = limpiar($_POST["codsucursal"]);
		$stmt->execute(); 
	}
    ############## AGREGAMOS O QUITAMOS LA DIFERENCIA EN CAJA ##################

    ######################### ENVIO DE EMAIL AL CLIENTE ##########################
        /*$smtp=new PHPMailer();
        $smtp->SMTPOptions = array(
        	'ssl' => array(
        		'verify_peer' => false,
        		'verify_peer_name' => false,
        		'allow_self_signed' => true
        	)
        );

        # Indicamos que vamos a utilizar un servidor SMTP
        $smtp->IsSMTP();

        # Definimos el formato del correo con UTF-8
        $smtp->CharSet="UTF-8";

        # autenticación contra nuestro servidor smtp
        $smtp->Port = 465;
        $smtp->IsSMTP(); // use SMTP
        $smtp->SMTPAuth   = true;
        $smtp->SMTPSecure = 'ssl';						// enable SMTP authentication
        $smtp->Host       = "smtp.gmail.com";			// sets MAIL as the SMTP server
        $smtp->Username   = "elsaiya@gmail.com";	// MAIL username
        $smtp->Password   = "rubencito18633174";			// MAIL password

        # datos de quien realiza el envio
        //$smtp->From       = "elsaiya@gmail.com"; // from mail
        $smtp->From       = "elsaiya@gmail.com"; // from mail
        $smtp->FromName   = $_SESSION["cuitsucursal"]." - ".$_SESSION["razonsocial"]; // from mail name

        # Indicamos las direcciones donde enviar el mensaje con el formato
        #   "correo"=>"nombre usuario"
        # Se pueden poner tantos correos como se deseen

        # establecemos un limite de caracteres de anchura
        $smtp->WordWrap   = 50; // set word wrap
        $smtp->Timeout = 30;   
        $smtp->IsHTML(true);

        # NOTA: Los correos es conveniente enviarlos en formato HTML y Texto para que
        # cualquier programa de correo pueda leerlo.

        # Definimos el contenido HTML del correo
        $contenidoHTML="<head>";
        $contenidoHTML.="<meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\">";
        $contenidoHTML.="</head><body>";
        $contenidoHTML.="<b>DETALLE DE ".limpiar($tipodocumento)."</b>";
        $contenidoHTML.="<p><b>Tipo de Documento</b>: ".limpiar($tipodocumento)."</p>";
        $contenidoHTML.="<p><b>Tipo de Pago: </b>".limpiar($tipopago)."</p>";
        $contenidoHTML.="<p><b>Fecha de Actualización: </b>".limpiar($fecha)."</p>";
        $contenidoHTML.="<p><b>Total Gravado: </b>".$subtotalivasi."</p>";
        $contenidoHTML.="<p><b>Total Exento: </b>".$subtotalivano."</p>";
        $contenidoHTML.="<p><b>Total Iva (".limpiar($_POST["iva"])."%): </b>".$totaliva."</p>";
        $contenidoHTML.="<p><b>Descto Global (".limpiar($_POST["descuento"])."%): </b>".$totaldescuento."</p>";
        $contenidoHTML.="<p><b>Total Pagado: </b>".$totalpago."</p>";
        $contenidoHTML.="<p><b>Observaciones: </b>".$nota = ($observaciones == '' ? "*********" : $observaciones)."</p>";
        $contenidoHTML.="</body>\n";

        # Definimos el contenido en formato Texto del correo
        $contenidoTexto= " Contenido en formato texto";
        $contenidoTexto.="\n\n";

        # Definimos el subject
        $smtp->Subject= " DETALLE DE COMPRA Nº ".$codventa." - FECHA MODIFICADO ".date("d-m-Y");

        # Indicamos el contenido
        $smtp->AltBody=$contenidoTexto; //Text Body
        $smtp->MsgHTML($contenidoHTML); //Text body HTML

        # Adjuntamos el archivo al correo.
        //$smtp->AddAttachment("fotos/logo.png", "felicitaciones.png");

	    #Email de cliente 
	    $nombre = $nomcliente;
	    $email = $emailcliente;

	    $smtp->ClearAllRecipients();
	    $smtp->AddAddress($email,$nombre);
	    $smtp->Send();*/
    ######################### ENVIO DE EMAIL AL CLIENTE ###########################

echo "<span class='fa fa-check-square-o'></span> LA VENTA DE PRODUCTOS HA SIDO ACTUALIZADA EXITOSAMENTE <a href='reportepdf?codventa=".encrypt($codventa)."&codsucursal=".encrypt($codsucursal)."&tipo=".encrypt($tipodocumento)."' class='on-default' data-placement='left' data-toggle='tooltip' data-original-title='Imprimir Documento' target='_black' rel='noopener noreferrer'><font color='black'><strong>IMPRIMIR REPORTE</strong></font color></a></div>";

echo "<script>window.open('reportepdf?codventa=".encrypt($codventa)."&codsucursal=".encrypt($codsucursal)."&tipo=".encrypt($tipodocumento)."', '_blank');</script>";
	exit;
}
########################## FUNCION ACTUALIZAR VENTAS ###########################

########################## FUNCION AGREGAR DETALLES VENTAS ############################
public function AgregarDetallesVentas()
	{
		self::SetNames();
		if(empty($_POST["codventa"]) or empty($_POST["codsucursal"]))
		{
			echo "1";
			exit;
		}

		else if(empty($_SESSION["CarritoVenta"]) || $_POST["txtTotal"]=="0.00")
		{
			echo "2";
			exit;
			
		} else {

        ############ CONSULTO TOTAL ACTUAL ##############
		$sql = "SELECT totalpago FROM ventas WHERE codventa = '".limpiar($_POST["codventa"])."' AND codsucursal = '".limpiar($_POST["codsucursal"])."'";
		foreach ($this->dbh->query($sql) as $row)
		{
			$this->p[] = $row;
		}
		$totalpagobd = $row['totalpago'];

		################### SELECCIONE LOS DATOS DEL CLIENTE ######################
	    $sql = "SELECT
	    clientes.nomcliente, 
	    clientes.emailcliente, 
	    clientes.limitecredito,
	    ROUND(SUM(if(pag.montocredito!='0',pag.montocredito,'0.00')), 2) montoactual,
	    ROUND(SUM(if(pag.montocredito!='0',clientes.limitecredito-pag.montocredito,clientes.limitecredito)), 2) creditodisponible
        FROM clientes 
        LEFT JOIN
           (SELECT
           codcliente, montocredito       
           FROM creditosxclientes 
           WHERE codsucursal = '".limpiar($_POST['codsucursal'])."') pag ON pag.codcliente = clientes.codcliente
           WHERE clientes.codcliente = ?";
		$stmt = $this->dbh->prepare($sql);
		$stmt->execute(array($_POST['codcliente']));
		$num = $stmt->rowCount();
		if($row = $stmt->fetch(PDO::FETCH_ASSOC))
		{
			$p[] = $row;
		}
        $nomcliente = $row['nomcliente'];
        $emailcliente = $row['emailcliente'];
        $limitecredito = $row['limitecredito'];
        $montoactual = $row['montoactual'];
        $creditodisponible = $row['creditodisponible'];
        $montoabono = (empty($_POST["abonototal"]) ? "0.00" : $_POST["abonototal"]);
        $total = number_format($_POST["txtTotal"], 2, '.', '');

	   if ($_POST["tipopago"] == "CREDITO") {
      
		    if ($limitecredito != "0.00" && $total > $creditodisponible) {	
	  
	           echo "3";
		       exit;

	        } 
	    }

	    $this->dbh->beginTransaction();
	    $detalle = $_SESSION["CarritoVenta"];
		for($i=0;$i<count($detalle);$i++){

        ############### SELECCIONAMOSLA EXISTENCIA DEL PRODUCTO ################
	    $sql2 = "SELECT existencia FROM productos WHERE codproducto = '".limpiar($detalle[$i]['txtCodigo'])."' AND codsucursal = '".limpiar($_POST["codsucursal"])."'";
		   foreach ($this->dbh->query($sql2) as $row)
		   {
		$this->p[] = $row;
		   }
		
		$existenciabd = $row["existencia"];

		############### REVISAMOS QUE LA CANTIDAD NO SEA IGUAL A CERO ##############
			if($detalle[$i]['cantidad']==0){

				echo "4";
				exit;
		    }

		######### REVISAMOS SI LA CANTIDAD ES MAYOR QUE LA EXISTENCIA EN ALMACEN ########
            if ($detalle[$i]['cantidad'] > $existenciabd) 
            { 
		       echo "5";
		       exit;
	        }

	    ############# REVISAMOS QUE EL PRODUCTO NO ESTE EN LA BD ###################
	    $sql = "SELECT codventa, codproducto FROM detalleventas WHERE codventa = '".limpiar($_POST['codventa'])."' AND codsucursal = '".limpiar($_POST['codsucursal'])."' AND codproducto = '".limpiar($detalle[$i]['txtCodigo'])."'";
		$stmt = $this->dbh->prepare($sql);
		$stmt->execute();
		$num = $stmt->rowCount();
		if($num == 0)
		{

        $query = "INSERT INTO detalleventas values (null, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?); ";
		$stmt = $this->dbh->prepare($query);
		$stmt->bindParam(1, $codventa);
	    $stmt->bindParam(2, $codproducto);
	    $stmt->bindParam(3, $producto);
		$stmt->bindParam(4, $cantidad);
		$stmt->bindParam(5, $preciocompra);
		$stmt->bindParam(6, $precioventa);
		$stmt->bindParam(7, $ivaproducto);
		$stmt->bindParam(8, $descproducto);
		$stmt->bindParam(9, $valortotal);
		$stmt->bindParam(10, $totaldescuentov);
		$stmt->bindParam(11, $valorneto);
		$stmt->bindParam(12, $valorneto2);
		$stmt->bindParam(13, $codsucursal);
			
		$codventa = limpiar($_POST["codventa"]);
		$codproducto = limpiar($detalle[$i]['txtCodigo']);
		$producto = limpiar($detalle[$i]['producto']);
		$cantidad = limpiar($detalle[$i]['cantidad']);
		$preciocompra = limpiar($detalle[$i]['precio']);
		$precioventa = limpiar($detalle[$i]['precio2']);
		$ivaproducto = limpiar($detalle[$i]['ivaproducto']);
		$descproducto = limpiar($detalle[$i]['descproducto']);
		$descuento = $detalle[$i]['descproducto']/100;
		$valortotal = number_format($detalle[$i]['precio2']*$detalle[$i]['cantidad'], 2, '.', '');
		$totaldescuentov = number_format($valortotal*$descuento, 2, '.', '');
	    $valorneto = number_format($valortotal-$totaldescuentov, 2, '.', '');
		$valorneto2 = number_format($detalle[$i]['precio']*$detalle[$i]['cantidad'], 2, '.', '');
		$codsucursal = limpiar($_SESSION["codsucursal"]);
		$stmt->execute();

		############### ACTUALIZAMOS EXISTENCIA DEL PRODUCTO EN ALMACEN ##############
		$sql = " UPDATE productos set "
			  ." existencia = ? "
			  ." WHERE "
			  ." codproducto = '".limpiar($detalle[$i]['txtCodigo'])."' AND codsucursal = '".limpiar($_POST["codsucursal"])."';
			   ";
		$stmt = $this->dbh->prepare($sql);
		$stmt->bindParam(1, $existencia);
		$cantventa = limpiar($detalle[$i]['cantidad']);
		$existencia = $existenciabd-$cantventa;
		$stmt->execute();

		############## REGISTRAMOS LOS DATOS DE PRODUCTOS EN KARDEX ###################
        $query = "INSERT INTO kardex values (null, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?); ";
		$stmt = $this->dbh->prepare($query);
		$stmt->bindParam(1, $codventa);
		$stmt->bindParam(2, $codcliente);
		$stmt->bindParam(3, $codproducto);
		$stmt->bindParam(4, $movimiento);
		$stmt->bindParam(5, $entradas);
		$stmt->bindParam(6, $salidas);
		$stmt->bindParam(7, $devolucion);
		$stmt->bindParam(8, $stockactual);
		$stmt->bindParam(9, $ivaproducto);
		$stmt->bindParam(10, $descproducto);
		$stmt->bindParam(11, $precio);
		$stmt->bindParam(12, $documento);
		$stmt->bindParam(13, $fechakardex);		
		$stmt->bindParam(14, $codsucursal);

		$codventa = limpiar($_POST['codventa']);
		$codcliente = limpiar($_POST["codcliente"]);
		$codproducto = limpiar($detalle[$i]['txtCodigo']);
		$movimiento = limpiar("SALIDAS");
		$entradas = limpiar("0");
		$salidas= limpiar($detalle[$i]['cantidad']);
		$devolucion = limpiar("0");
		$stockactual = limpiar($existenciabd-$detalle[$i]['cantidad']);
		$precio = limpiar($detalle[$i]["precio2"]);
		$ivaproducto = limpiar($detalle[$i]['ivaproducto']);
		$descproducto = limpiar($detalle[$i]['descproducto']);
		$documento = limpiar("VENTA: ".$_POST['codventa']);
		$fechakardex = limpiar(date("Y-m-d"));
		$codsucursal = limpiar($_POST["codsucursal"]);
		$stmt->execute();

	      } else {

	  	$sql = "SELECT cantventa FROM detalleventas WHERE codventa = '".limpiar($_POST['codventa'])."' AND codsucursal = '".limpiar($_POST['codsucursal'])."' AND codproducto = '".limpiar($detalle[$i]['txtCodigo'])."'";
		foreach ($this->dbh->query($sql) as $row)
		{
			$this->p[] = $row;
		}
		$cantidad = $row['cantventa'];

	  	$query = "UPDATE detalleventas set"
		." cantventa = ?, "
		." descproducto = ?, "
		." valortotal = ?, "
		." totaldescuentov = ?, "
		." valorneto = ?, "
		." valorneto2 = ? "
		." WHERE "
		." codventa = ? AND codsucursal = ? AND codproducto = ?;
		";
		$stmt = $this->dbh->prepare($query);
		$stmt->bindParam(1, $cantventa);
		$stmt->bindParam(2, $descproducto);
		$stmt->bindParam(3, $valortotal);
		$stmt->bindParam(4, $totaldescuentov);
		$stmt->bindParam(5, $valorneto);
		$stmt->bindParam(6, $valorneto2);
		$stmt->bindParam(7, $codventa);
		$stmt->bindParam(8, $codsucursal);
		$stmt->bindParam(9, $codproducto);

		$cantventa = limpiar($detalle[$i]['cantidad']+$cantidad);
		$preciocompra = limpiar($detalle[$i]['precio']);
		$precioventa = limpiar($detalle[$i]['precio2']);
		$ivaproducto = limpiar($detalle[$i]['ivaproducto']);
		$descproducto = limpiar($detalle[$i]['descproducto']);
		$descuento = $detalle[$i]['descproducto']/100;
		$valortotal = number_format($detalle[$i]['precio2'] * $cantventa, 2, '.', '');
		$totaldescuentov = number_format($valortotal * $descuento, 2, '.', '');
		$valorneto = number_format($valortotal - $totaldescuentov, 2, '.', '');
		$valorneto2 = number_format($detalle[$i]['precio'] * $cantventa, 2, '.', '');
		$codventa = limpiar($_POST["codventa"]);
		$codsucursal = limpiar($_POST["codsucursal"]);
		$codproducto = limpiar($detalle[$i]['txtCodigo']);
		$stmt->execute();

		################ ACTUALIZAMOS EXISTENCIA DEL PRODUCTO EN ALMACEN ################
		$sql = " UPDATE productos set "
			  ." existencia = ? "
			  ." WHERE "
			  ." codproducto = '".limpiar($detalle[$i]['txtCodigo'])."' AND codsucursal = '".limpiar($_POST["codsucursal"])."';
			   ";
		$stmt = $this->dbh->prepare($sql);
		$stmt->bindParam(1, $existencia);
		$cantventa = limpiar($detalle[$i]['cantidad']);
		$existencia = $existenciabd-$cantventa;
		$stmt->execute();

		################ ACTUALIZAMOS LOS DATOS DEL PRODUCTO EN KARDEX ##############
		$sql3 = " UPDATE kardex set "
		      ." salidas = ?, "
		      ." stockactual = ? "
			  ." WHERE "
			  ." codproceso = '".limpiar($_POST["codventa"])."' and codproducto = '".limpiar($detalle[$i]['txtCodigo'])."' AND codsucursal = '".limpiar($_POST["codsucursal"])."';
			   ";
		$stmt = $this->dbh->prepare($sql3);
		$stmt->bindParam(1, $salidas);
		$stmt->bindParam(2, $existencia);
		
		$salidas = limpiar($detalle[$i]['cantidad']+$cantidad);
		$stmt->execute();
	       }
        }
        ####################### DESTRUYO LA VARIABLE DE SESSION #####################
	    unset($_SESSION["CarritoVenta"]);
        $this->dbh->commit();

        ############ SUMO LOS IMPORTE DE PRODUCTOS CON IVA ##############
		$sql3 = "SELECT SUM(valorneto) AS valorneto, SUM(valorneto2) AS valorneto2 FROM detalleventas WHERE codventa = '".limpiar($_POST["codventa"])."' AND codsucursal = '".limpiar($_POST["codsucursal"])."' AND ivaproducto = 'SI'";
			foreach ($this->dbh->query($sql3) as $row3)
			{
				$this->p[] = $row3;
			}
			$subtotalivasi = ($row3['valorneto']== "" ? "0.00" : $row3['valorneto']);
			$subtotalivasi2 = ($row3['valorneto2']== "" ? "0.00" : $row3['valorneto2']);

		############ SUMO LOS IMPORTE DE PRODUCTOS SIN IVA ##############
		$sql4 = "SELECT SUM(valorneto) AS valorneto, SUM(valorneto2) AS valorneto2 FROM detalleventas WHERE codventa = '".limpiar($_POST["codventa"])."' AND codsucursal = '".limpiar($_POST["codsucursal"])."' AND ivaproducto = 'NO'";
			foreach ($this->dbh->query($sql4) as $row4)
			{
				$this->p[] = $row4;
			}
			$subtotalivano = ($row4['valorneto']== "" ? "0.00" : $row4['valorneto']);
			$subtotalivano2 = ($row4['valorneto2']== "" ? "0.00" : $row4['valorneto2']);

        ############ ACTUALIZO LOS TOTALES EN LA COTIZACION ##############
		$sql = " UPDATE ventas SET "
		." codcliente = ?, "
		." subtotalivasi = ?, "
		." subtotalivano = ?, "
		." totaliva = ?, "
		." descuento = ?, "
		." totaldescuento = ?, "
		." totalpago = ?, "
		." totalpago2 = ?, "
		." montodevuelto = ? "
		." WHERE "
		." codventa = ? AND codsucursal = ?;
		";
		$stmt = $this->dbh->prepare($sql);
		$stmt->bindParam(1, $codcliente);
		$stmt->bindParam(2, $subtotalivasi);
		$stmt->bindParam(3, $subtotalivano);
		$stmt->bindParam(4, $totaliva);
		$stmt->bindParam(5, $descuento);
		$stmt->bindParam(6, $totaldescuento);
		$stmt->bindParam(7, $totalpago);
		$stmt->bindParam(8, $totalpago2);
		$stmt->bindParam(9, $montodevuelto);
		$stmt->bindParam(10, $codventa);
		$stmt->bindParam(11, $codsucursal);

		$codcliente = limpiar($_POST["codcliente"]);
		$iva = $_POST["iva"]/100;
		$totaliva = number_format($subtotalivasi*$iva, 2, '.', '');
		$descuento = limpiar($_POST["descuento"]);
		$txtDescuento = $_POST["descuento"]/100;
		$total = number_format($subtotalivasi+$subtotalivano+$totaliva, 2, '.', '');
		$totaldescuento = number_format($total*$txtDescuento, 2, '.', '');
		$totalpago = number_format($total-$totaldescuento, 2, '.', '');
		$totalpago2 = number_format($subtotalivasi2+$subtotalivano2, 2, '.', '');
		$montodevuelto = number_format($totalpago > $_POST["pagado"] ? "0.00" : $_POST["pagado"]-$totalpago, 2, '.', '');
		$codventa = limpiar($_POST["codventa"]);
		$codsucursal = limpiar($_POST["codsucursal"]);
		$tipodocumento = limpiar($_POST["tipodocumento"]);
		$tipopago = limpiar($_POST["tipopago"]);
		$observaciones = limpiar($_POST["observaciones"]);
		$fecha = date("Y-m-d h:i:s");
		$stmt->execute();

    ################## AGREGAMOS O QUITAMOS LA DIFERENCIA EN CAJA ###############
    if (limpiar($_POST["tipopago"]=="CONTADO") && $totalpagobd != $totalpago){

        $sql = "SELECT ingresos FROM arqueocaja WHERE codcaja = '".limpiar($_POST["codcaja"])."' AND statusarqueo = 1";
        foreach ($this->dbh->query($sql) as $row)
        {
            $this->p[] = $row;
        }
        $ingreso = ($row['ingresos']== "" ? "0.00" : $row['ingresos']);

        $sql = "UPDATE arqueocaja set "
        ." ingresos = ? "
        ." WHERE "
        ." codcaja = ? AND statusarqueo = 1;
        ";
        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(1, $TxtTotal);
        $stmt->bindParam(2, $codcaja);

        $TxtTotal = number_format(($totalpagobd>$totalpago ? $ingreso-($totalpagobd-$totalpago) : $ingreso+($totalpago-$totalpagobd)), 2, '.', '');
        $codcaja = limpiar($_POST["codcaja"]);
        $stmt->execute();
    }
    ################# AGREGAMOS O QUITAMOS LA DIFERENCIA EN CAJA ###################

    ############## AGREGAMOS O QUITAMOS LA DIFERENCIA EN CAJA ##################
    if (limpiar($_POST["tipopago"]=="CREDITO") && $totalpagobd != $totalpago) {

        $sql = "SELECT creditos FROM arqueocaja WHERE codcaja = '".limpiar($_POST["codcaja"])."' AND statusarqueo = 1";
        foreach ($this->dbh->query($sql) as $row)
        {
            $this->p[] = $row;
        }
        $credito = ($row['creditos']== "" ? "0.00" : $row['creditos']);

        $sql = " UPDATE arqueocaja SET "
        ." creditos = ? "
        ." where "
        ." codcaja = ? and statusarqueo = 1;
        ";
        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(1, $TxtTotal);
        $stmt->bindParam(2, $codcaja);

        $TxtTotal = number_format(($totalpagobd>$totalpago ? $credito-($totalpagobd-$totalpago) : $credito+($totalpago-$totalpagobd)), 2, '.', '');
        $codcaja = limpiar($_POST["codcaja"]);
        $stmt->execute(); 

		$sql = "UPDATE creditosxclientes set"
		." montocredito = ? "
		." where "
		." codcliente = ? AND codsucursal = ?;
		";
		$stmt = $this->dbh->prepare($sql);
		$stmt->bindParam(1, $montocredito);
		$stmt->bindParam(2, $codcliente);
		$stmt->bindParam(3, $codsucursal);

        $montocredito = number_format(($totalpagobd>$totalpago ? $montoactual-($totalpagobd-$totalpago) : $montoactual+($totalpago-$totalpagobd)), 2, '.', '');
		$codcliente = limpiar($_POST["codcliente"]);
		$codsucursal = limpiar($_POST["codsucursal"]);
		$stmt->execute(); 
    }
    ############## AGREGAMOS O QUITAMOS LA DIFERENCIA EN CAJA ##################

    ########################## ENVIO DE EMAIL AL CLIENTE ##########################
        /*$smtp=new PHPMailer();
        $smtp->SMTPOptions = array(
        	'ssl' => array(
        		'verify_peer' => false,
        		'verify_peer_name' => false,
        		'allow_self_signed' => true
        	)
        );

        # Indicamos que vamos a utilizar un servidor SMTP
        $smtp->IsSMTP();

        # Definimos el formato del correo con UTF-8
        $smtp->CharSet="UTF-8";

        # autenticación contra nuestro servidor smtp
        $smtp->Port = 465;
        $smtp->IsSMTP(); // use SMTP
        $smtp->SMTPAuth   = true;
        $smtp->SMTPSecure = 'ssl';						// enable SMTP authentication
        $smtp->Host       = "smtp.gmail.com";			// sets MAIL as the SMTP server
        $smtp->Username   = "elsaiya@gmail.com";	// MAIL username
        $smtp->Password   = "rubencito18633174";			// MAIL password

        # datos de quien realiza el envio
        //$smtp->From       = "elsaiya@gmail.com"; // from mail
        $smtp->From       = "elsaiya@gmail.com"; // from mail
        $smtp->FromName   = $_SESSION["cuitsucursal"]." - ".$_SESSION["razonsocial"]; // from mail name

        # Indicamos las direcciones donde enviar el mensaje con el formato
        #   "correo"=>"nombre usuario"
        # Se pueden poner tantos correos como se deseen

        # establecemos un limite de caracteres de anchura
        $smtp->WordWrap   = 50; // set word wrap
        $smtp->Timeout = 30;   
        $smtp->IsHTML(true);

        # NOTA: Los correos es conveniente enviarlos en formato HTML y Texto para que
        # cualquier programa de correo pueda leerlo.

        # Definimos el contenido HTML del correo
        $contenidoHTML="<head>";
        $contenidoHTML.="<meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\">";
        $contenidoHTML.="</head><body>";
        $contenidoHTML.="<b>DETALLE DE ".limpiar($tipodocumento)."</b>";
        $contenidoHTML.="<p><b>Tipo de Documento</b>: ".limpiar($tipodocumento)."</p>";
        $contenidoHTML.="<p><b>Tipo de Pago: </b>".limpiar($tipopago)."</p>";
        $contenidoHTML.="<p><b>Fecha de Actualización: </b>".limpiar($fecha)."</p>";
        $contenidoHTML.="<p><b>Total Gravado: </b>".$subtotalivasi."</p>";
        $contenidoHTML.="<p><b>Total Exento: </b>".$subtotalivano."</p>";
        $contenidoHTML.="<p><b>Total Iva (".limpiar($_POST["iva"])."%): </b>".$totaliva."</p>";
        $contenidoHTML.="<p><b>Descto Global (".limpiar($_POST["descuento"])."%): </b>".$totaldescuento."</p>";
        $contenidoHTML.="<p><b>Total Pagado: </b>".$totalpago."</p>";
        $contenidoHTML.="<p><b>Observaciones: </b>".$nota = ($observaciones == '' ? "*********" : $observaciones)."</p>";
        $contenidoHTML.="</body>\n";

        # Definimos el contenido en formato Texto del correo
        $contenidoTexto= " Contenido en formato texto";
        $contenidoTexto.="\n\n";

        # Definimos el subject
        $smtp->Subject= " DETALLE DE COMPRA Nº ".$codventa." - FECHA MODIFICADO ".date("d-m-Y");

        # Indicamos el contenido
        $smtp->AltBody=$contenidoTexto; //Text Body
        $smtp->MsgHTML($contenidoHTML); //Text body HTML

        # Adjuntamos el archivo al correo.
        //$smtp->AddAttachment("fotos/logo.png", "felicitaciones.png");

	    #Email de cliente 
	    $nombre = $nomcliente;
	    $email = $emailcliente;

	    $smtp->ClearAllRecipients();
	    $smtp->AddAddress($email,$nombre);
	    $smtp->Send();*/
        ######################### ENVIO DE EMAIL AL CLIENTE ###########################


echo "<span class='fa fa-check-square-o'></span> LOS DETALLES DE PRODUCTOS FUERON AGREGADOS A LA VENTA EXITOSAMENTE <a href='reportepdf?codventa=".encrypt($codventa)."&codsucursal=".encrypt($codsucursal)."&tipo=".encrypt($tipodocumento)."' class='on-default' data-placement='left' data-toggle='tooltip' data-original-title='Imprimir Documento' target='_black' rel='noopener noreferrer'><font color='black'><strong>IMPRIMIR REPORTE</strong></font color></a></div>";

echo "<script>window.open('reportepdf?codventa=".encrypt($codventa)."&codsucursal=".encrypt($codsucursal)."&tipo=".encrypt($tipodocumento)."', '_blank');</script>";
	exit;
	}
}
########################### FUNCION AGREGAR DETALLES VENTAS ##########################

########################### FUNCION ELIMINAR DETALLES VENTAS ###########################
public function EliminarDetallesVentas()
{
	
	self::SetNames();
	if ($_SESSION["acceso"]=="administradorS") {

        ############ CONSULTO TOTAL ACTUAL ##############
		$sql = "SELECT codcaja, codcliente, tipopago, totalpago FROM ventas WHERE codventa = '".limpiar(decrypt($_GET["codventa"]))."' AND codsucursal = '".limpiar(decrypt($_GET["codsucursal"]))."'";
		foreach ($this->dbh->query($sql) as $row)
		{
			$this->p[] = $row;
		}
		$cajabd = $row['codcaja'];
		$clientebd = $row['codcliente'];
		$tipopagobd = $row['tipopago'];
		$totalpagobd = $row['totalpago'];

		################### VERIFICO MONTO DE CREDITO DEL CLIENTE ######################
		$sql = "SELECT montocredito FROM creditosxclientes WHERE codcliente = '".$clientebd."' AND codsucursal = '".limpiar(decrypt($_GET["codsucursal"]))."'";
		foreach ($this->dbh->query($sql) as $row)
		{
			$this->p[] = $row;
		}
		$monto = (empty($row['montocredito']) ? "0.00" : $row['montocredito']);


		$sql = "SELECT * FROM detalleventas WHERE codventa = ? AND codsucursal = ?";
		$stmt = $this->dbh->prepare($sql);
		$stmt->execute(array(decrypt($_GET["codventa"]),decrypt($_GET["codsucursal"])));
		$num = $stmt->rowCount();
		if($num > 1)
		{

			$sql = "SELECT codproducto, cantventa, precioventa, ivaproducto, descproducto FROM detalleventas WHERE coddetalleventa = ? and codsucursal = ?";
			$stmt = $this->dbh->prepare($sql);
			$stmt->execute(array(decrypt($_GET["coddetalleventa"]),decrypt($_GET["codsucursal"])));
			$num = $stmt->rowCount();

			if($row = $stmt->fetch(PDO::FETCH_ASSOC))
			{
				$p[] = $row;
			}
			$codproducto = $row['codproducto'];
			$cantidadbd = $row['cantventa'];
			$precioventabd = $row['precioventa'];
			$ivaproductobd = $row['ivaproducto'];
			$descproductobd = $row['descproducto'];

			$sql2 = "SELECT existencia FROM productos WHERE codproducto = ? AND codsucursal = ?";
			$stmt = $this->dbh->prepare($sql2);
			$stmt->execute(array($codproducto,decrypt($_GET["codsucursal"])));
			$num = $stmt->rowCount();

			if($row = $stmt->fetch(PDO::FETCH_ASSOC))
			{
				$p[] = $row;
			}
			$existenciabd = $row['existencia'];

			############ ACTUALIZAMOS LA EXISTENCIA DE PRODUCTO EN ALMACEN ############
			$sql = "UPDATE productos SET "
			." existencia = ? "
			." WHERE "
			." codproducto = ? AND codsucursal = ?;
			";
			$stmt = $this->dbh->prepare($sql);
			$stmt->bindParam(1, $existencia);
			$stmt->bindParam(2, $codproducto);
			$stmt->bindParam(3, $codsucursal);

			$existencia = limpiar($existenciabd+$cantidadbd);
			$codsucursal = limpiar(decrypt($_GET["codsucursal"]));
			$stmt->execute();


		    ######## REGISTRAMOS LOS DATOS DEL PRODUCTO ELIMINADO EN KARDEX ##########
			$query = "INSERT INTO kardex values (null, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?); ";
			$stmt = $this->dbh->prepare($query);
			$stmt->bindParam(1, $codventa);
			$stmt->bindParam(2, $codcliente);
			$stmt->bindParam(3, $codproducto);
			$stmt->bindParam(4, $movimiento);
			$stmt->bindParam(5, $entradas);
			$stmt->bindParam(6, $salidas);
			$stmt->bindParam(7, $devolucion);
			$stmt->bindParam(8, $stockactual);
			$stmt->bindParam(9, $ivaproducto);
			$stmt->bindParam(10, $descproducto);
			$stmt->bindParam(11, $precio);
			$stmt->bindParam(12, $documento);
			$stmt->bindParam(13, $fechakardex);		
			$stmt->bindParam(14, $codsucursal);

			$codventa = limpiar(decrypt($_GET["codventa"]));
			$codcliente = limpiar((decrypt($_GET["codcliente"])== "" ? "0" : decrypt($_GET["codcliente"])));
			$movimiento = limpiar("DEVOLUCION");
			$entradas= limpiar("0");
			$salidas = limpiar("0");
			$devolucion = limpiar($cantidadbd);
			$stockactual = limpiar($existenciabd+$cantidadbd);
			$precio = limpiar($precioventabd);
			$ivaproducto = limpiar($ivaproductobd);
			$descproducto = limpiar($descproductobd);
			$documento = limpiar("DEVOLUCION VENTA: ".decrypt($_GET["codventa"]));
			$fechakardex = limpiar(date("Y-m-d"));
			$codsucursal = limpiar(decrypt($_GET["codsucursal"]));
			$stmt->execute();

			$sql = "DELETE FROM detalleventas WHERE coddetalleventa = ? AND codsucursal = ?";
			$stmt = $this->dbh->prepare($sql);
			$stmt->bindParam(1,$coddetalleventa);
			$stmt->bindParam(2,$codsucursal);
			$coddetalleventa = decrypt($_GET["coddetalleventa"]);
			$codsucursal = decrypt($_GET["codsucursal"]);
			$stmt->execute();

		    ############ CONSULTO LOS TOTALES DE COTIZACIONES ##############
			$sql2 = "SELECT iva, descuento FROM ventas WHERE codventa = ? AND codsucursal = ?";
			$stmt = $this->dbh->prepare($sql2);
			$stmt->execute(array(decrypt($_GET["codventa"]),decrypt($_GET["codsucursal"])));
			$num = $stmt->rowCount();

			if($row = $stmt->fetch(PDO::FETCH_ASSOC))
			{
				$paea[] = $row;
			}
			$iva = $paea[0]["iva"]/100;
			$descuento = $paea[0]["descuento"]/100;

            ############ SUMO LOS IMPORTE DE PRODUCTOS CON IVA ##############
			$sql3 = "SELECT SUM(valorneto) AS valorneto, SUM(valorneto2) AS valorneto2 FROM detalleventas WHERE codventa = '".limpiar(decrypt($_GET["codventa"]))."' AND codsucursal = '".limpiar(decrypt($_GET["codsucursal"]))."' AND ivaproducto = 'SI'";
			foreach ($this->dbh->query($sql3) as $row3)
			{
				$this->p[] = $row3;
			}
			$subtotalivasi = ($row3['valorneto']== "" ? "0.00" : $row3['valorneto']);
			$subtotalivasi2 = ($row3['valorneto2']== "" ? "0.00" : $row3['valorneto2']);

		    ############ SUMO LOS IMPORTE DE PRODUCTOS SIN IVA ##############
			$sql4 = "SELECT SUM(valorneto) AS valorneto, SUM(valorneto2) AS valorneto2 FROM detalleventas WHERE codventa = '".limpiar(decrypt($_GET["codventa"]))."' AND codsucursal = '".limpiar(decrypt($_GET["codsucursal"]))."' AND ivaproducto = 'NO'";
			foreach ($this->dbh->query($sql4) as $row4)
			{
				$this->p[] = $row4;
			}
			$subtotalivano = ($row4['valorneto']== "" ? "0.00" : $row4['valorneto']);
			$subtotalivano2 = ($row4['valorneto2']== "" ? "0.00" : $row4['valorneto2']);

            ############ ACTUALIZO LOS TOTALES EN LA COTIZACION ##############
			$sql = " UPDATE ventas SET "
			." subtotalivasi = ?, "
			." subtotalivano = ?, "
			." totaliva = ?, "
			." totaldescuento = ?, "
			." totalpago = ?, "
			." totalpago2= ? "
			." WHERE "
			." codventa = ? AND codsucursal = ?;
			";
			$stmt = $this->dbh->prepare($sql);
			$stmt->bindParam(1, $subtotalivasi);
			$stmt->bindParam(2, $subtotalivano);
			$stmt->bindParam(3, $totaliva);
			$stmt->bindParam(4, $totaldescuento);
			$stmt->bindParam(5, $totalpago);
			$stmt->bindParam(6, $totalpago2);
			$stmt->bindParam(7, $codventa);
			$stmt->bindParam(8, $codsucursal);

			$totaliva= number_format($subtotalivasi*$iva, 2, '.', '');
			$total= number_format($subtotalivasi+$subtotalivano+$totaliva, 2, '.', '');
			$totaldescuento= number_format($total*$descuento, 2, '.', '');
			$totalpago= number_format($total-$totaldescuento, 2, '.', '');
			$totalpago2 = number_format($subtotalivasi+$subtotalivano, 2, '.', '');
			$codventa = limpiar(decrypt($_GET["codventa"]));
			$codsucursal = limpiar(decrypt($_GET["codsucursal"]));
			$stmt->execute();

	#################### QUITAMOS LA DIFERENCIA EN CAJA ####################
	if ($tipopagobd=="CONTADO"){

		$sql = "SELECT ingresos FROM arqueocaja WHERE codcaja = '".$cajabd."' AND statusarqueo = 1";
		foreach ($this->dbh->query($sql) as $row)
		{
			$this->p[] = $row;
		}
		$ingreso = ($row['ingresos']== "" ? "0.00" : $row['ingresos']);

		$sql = "UPDATE arqueocaja set "
		." ingresos = ? "
		." WHERE "
		." codcaja = ? AND statusarqueo = 1;
		";
		$stmt = $this->dbh->prepare($sql);
		$stmt->bindParam(1, $TxtTotal);
		$stmt->bindParam(2, $cajabd);

		$Calculo = number_format($totalpagobd-$totalpago, 2, '.', '');
		$TxtTotal = number_format($ingreso-$Calculo, 2, '.', '');
		$stmt->execute();
	}
    #################### QUITAMOS LA DIFERENCIA EN CAJA ####################
    
    ############## QUITAMOS LA DIFERENCIA EN CAJA ##################
	if ($tipopagobd=="CREDITO") {

		$sql = "SELECT creditos FROM arqueocaja WHERE codcaja = '".$cajabd."' AND statusarqueo = 1";
		foreach ($this->dbh->query($sql) as $row)
		{
			$this->p[] = $row;
		}
		$credito = ($row['creditos']== "" ? "0.00" : $row['creditos']);

		$sql = " UPDATE arqueocaja SET "
		    ." creditos = ? "
		    ." where "
		    ." codcaja = ? and statusarqueo = 1;
		    ";
		    $stmt = $this->dbh->prepare($sql);
		    $stmt->bindParam(1, $TxtTotal);
		    $stmt->bindParam(2, $cajabd);

		    $Calculo = number_format($totalpagobd-$totalpago, 2, '.', '');
		    $TxtTotal = number_format($credito-$Calculo, 2, '.', '');
		    $stmt->execute();

		$sql = "UPDATE creditosxclientes set"
		    ." montocredito = ? "
			." where "
			." codcliente = ? AND codsucursal = ?;
			";
			$stmt = $this->dbh->prepare($sql);
			$stmt->bindParam(1, $montocredito);
			$stmt->bindParam(2, $clientebd);
			$stmt->bindParam(3, $codsucursal);

			$montocredito = number_format($monto-$Calculo, 2, '.', '');
			$codsucursal = limpiar(decrypt($_GET["codsucursal"]));
			$stmt->execute(); 	
	}
    ############## QUITAMOS LA DIFERENCIA EN CAJA ##################

			echo "1";
			exit;

		} else {

			echo "2";
			exit;
		} 

	} else {
		
		echo "3";
		exit;
	}	
}
######################## FUNCION ELIMINAR DETALLES VENTAS ########################

####################### FUNCION ELIMINAR VENTAS ########################
public function EliminarVentas()
	{
	
	self::SetNames();
	if ($_SESSION["acceso"]=="administradorS") {

        ############ CONSULTO TOTAL ACTUAL ##############
		$sql = "SELECT codcaja, codcliente, tipopago, totalpago FROM ventas WHERE codventa = '".limpiar(decrypt($_GET["codventa"]))."' AND codsucursal = '".limpiar(decrypt($_GET["codsucursal"]))."'";
		foreach ($this->dbh->query($sql) as $row)
			{
				$this->p[] = $row;
			}
		$cajabd = $row['codcaja'];
		$clientebd = $row['codcliente'];
		$tipopagobd = $row['tipopago'];
		$totalpagobd = $row['totalpago'];

		################### VERIFICO MONTO DE CREDITO DEL CLIENTE ######################
		$sql = "SELECT montocredito FROM creditosxclientes WHERE codcliente = '".$clientebd."' AND codsucursal = '".limpiar(decrypt($_GET["codsucursal"]))."'";
		foreach ($this->dbh->query($sql) as $row)
		{
			$this->p[] = $row;
		}
        $monto = (empty($row['montocredito']) ? "0.00" : $row['montocredito']);

	$sql = "SELECT * FROM detalleventas WHERE codventa = '".limpiar(decrypt($_GET["codventa"]))."' AND codsucursal = '".limpiar(decrypt($_GET["codsucursal"]))."'";

		$array=array();

	foreach ($this->dbh->query($sql) as $row)
		{
			$this->p[] = $row;

			$codproducto = $row['codproducto'];
			$cantidadbd = $row['cantventa'];
			$precioventabd = $row['precioventa'];
			$ivaproductobd = $row['ivaproducto'];
			$descproductobd = $row['descproducto'];

			$sql2 = "SELECT existencia FROM productos WHERE codproducto = ? AND codsucursal = ?";
			$stmt = $this->dbh->prepare($sql2);
			$stmt->execute( array($codproducto,decrypt($_GET["codsucursal"])));
			$num = $stmt->rowCount();

			if($row = $stmt->fetch(PDO::FETCH_ASSOC))
			{
				$p[] = $row;
			}
			$existenciabd = $row['existencia'];

			########### ACTUALIZAMOS LA EXISTENCIA DE PRODUCTO EN ALMACEN ##############
			$sql = "UPDATE productos SET "
			." existencia = ? "
			." WHERE "
			." codproducto = ? AND codsucursal = ?;
			";
			$stmt = $this->dbh->prepare($sql);
			$stmt->bindParam(1, $existencia);
			$stmt->bindParam(2, $codproducto);
			$stmt->bindParam(3, $codsucursal);

			$existencia = limpiar($existenciabd+$cantidadbd);
			$codsucursal = limpiar(decrypt($_GET["codsucursal"]));
			$stmt->execute();

		    ########## REGISTRAMOS LOS DATOS DEL PRODUCTO ELIMINADO EN KARDEX ##########
			$query = "INSERT INTO kardex values (null, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?); ";
			$stmt = $this->dbh->prepare($query);
			$stmt->bindParam(1, $codventa);
			$stmt->bindParam(2, $codcliente);
			$stmt->bindParam(3, $codproducto);
			$stmt->bindParam(4, $movimiento);
			$stmt->bindParam(5, $entradas);
			$stmt->bindParam(6, $salidas);
			$stmt->bindParam(7, $devolucion);
			$stmt->bindParam(8, $stockactual);
			$stmt->bindParam(9, $ivaproducto);
			$stmt->bindParam(10, $descproducto);
			$stmt->bindParam(11, $precio);
			$stmt->bindParam(12, $documento);
			$stmt->bindParam(13, $fechakardex);		
			$stmt->bindParam(14, $codsucursal);

			$codventa = limpiar(decrypt($_GET["codventa"]));
		    $codcliente = limpiar((decrypt($_GET["codcliente"])== "" ? "0" : decrypt($_GET["codcliente"])));
			$movimiento = limpiar("DEVOLUCION");
			$entradas= limpiar("0");
			$salidas = limpiar("0");
			$devolucion = limpiar($cantidadbd);
			$stockactual = limpiar($existenciabd+$cantidadbd);
			$precio = limpiar($precioventabd);
			$ivaproducto = limpiar($ivaproductobd);
			$descproducto = limpiar($descproductobd);
			$documento = limpiar("DEVOLUCION VENTA: ".decrypt($_GET["codventa"]));
			$fechakardex = limpiar(date("Y-m-d"));
			$codsucursal = limpiar(decrypt($_GET["codsucursal"]));
			$stmt->execute();
		}

	#################### QUITAMOS LA DIFERENCIA EN CAJA ####################
	if ($tipopagobd=="CONTADO"){

		$sql = "SELECT ingresos FROM arqueocaja WHERE codcaja = '".$cajabd."' AND statusarqueo = 1";
		foreach ($this->dbh->query($sql) as $row)
		{
			$this->p[] = $row;
		}
		$ingreso = ($row['ingresos']== "" ? "0.00" : $row['ingresos']);

		$sql = "UPDATE arqueocaja set "
		    ." ingresos = ? "
		    ." WHERE "
		    ." codcaja = ? AND statusarqueo = 1;
		    ";
		    $stmt = $this->dbh->prepare($sql);
		    $stmt->bindParam(1, $TxtTotal);
		    $stmt->bindParam(2, $cajabd);

            $TxtTotal = number_format($ingreso-$totalpagobd, 2, '.', '');
		    $stmt->execute();
	}
    #################### QUITAMOS LA DIFERENCIA EN CAJA ####################

    ############## QUITAMOS LA DIFERENCIA EN CAJA ##################
	if ($tipopagobd=="CREDITO") {

		$sql = "SELECT creditos FROM arqueocaja WHERE codcaja = '".$cajabd."' AND statusarqueo = 1";
		foreach ($this->dbh->query($sql) as $row)
		{
			$this->p[] = $row;
		}
		$credito = ($row['creditos']== "" ? "0.00" : $row['creditos']);

		$sql = " UPDATE arqueocaja SET "
		    ." creditos = ? "
		    ." where "
		    ." codcaja = ? and statusarqueo = 1;
		    ";
		    $stmt = $this->dbh->prepare($sql);
		    $stmt->bindParam(1, $TxtTotal);
		    $stmt->bindParam(2, $cajabd);

		    $TxtTotal = number_format($credito-$totalpagobd, 2, '.', '');
		    $stmt->execute();

		$sql = "UPDATE creditosxclientes set"
		    ." montocredito = ? "
			." where "
			." codcliente = ? AND codsucursal = ?;
			";
			$stmt = $this->dbh->prepare($sql);
			$stmt->bindParam(1, $montocredito);
			$stmt->bindParam(2, $clientebd);
			$stmt->bindParam(3, $codsucursal);

			$montocredito = number_format($monto-$totalpagobd, 2, '.', '');
			$codsucursal = limpiar(decrypt($_GET["codsucursal"]));
			$stmt->execute(); 	
	}
    ############## QUITAMOS LA DIFERENCIA EN CAJA ##################

			$sql = "DELETE FROM ventas WHERE codventa = ? AND codsucursal = ?";
			$stmt = $this->dbh->prepare($sql);
			$stmt->bindParam(1,$codventa);
			$stmt->bindParam(2,$codsucursal);
			$codventa = decrypt($_GET["codventa"]);
			$codsucursal = decrypt($_GET["codsucursal"]);
			$stmt->execute();

			$sql = "DELETE FROM detalleventas WHERE codventa = ? AND codsucursal = ?";
			$stmt = $this->dbh->prepare($sql);
			$stmt->bindParam(1,$codventa);
			$stmt->bindParam(2,$codsucursal);
			$codventa = decrypt($_GET["codventa"]);
			$codsucursal = decrypt($_GET["codsucursal"]);
			$stmt->execute();

			echo "1";
			exit;

		} else {

			echo "2";
			exit;
		}
	}
######################### FUNCION ELIMINAR VENTAS ############################

######################### FUNCION LISTAR VENTAS DIARIAS ###########################
public function BuscarVentasDiarias()
{
	self::SetNames();
	if($_SESSION['acceso'] == "administradorS") {

	$sql = "SELECT 
	ventas.idventa, 
	ventas.tipodocumento, 
	ventas.codventa, 
	ventas.codserie, 
	ventas.codautorizacion, 
	ventas.codcaja, 
	ventas.codcliente, 
	ventas.subtotalivasi, 
	ventas.subtotalivano, 
	ventas.iva, 
	ventas.totaliva, 
	ventas.descuento, 
	ventas.totaldescuento, 
	ventas.totalpago, 
	ventas.totalpago2, 
	ventas.tipopago, 
	ventas.formapago, 
	ventas.montopagado, 
	ventas.montodevuelto, 
	ventas.fechavencecredito, 
	ventas.fechapagado,
	ventas.statusventa, 
	ventas.fechaventa,
	ventas.observaciones,  
	ventas.codsucursal, 
	sucursales.documsucursal, 
	sucursales.cuitsucursal, 
	sucursales.razonsocial,
	sucursales.documencargado,
	sucursales.dniencargado,
	sucursales.nomencargado,
	usuarios.dni,
	usuarios.nombres,
	cajas.nrocaja,
	cajas.nomcaja,
	clientes.documcliente, 
	clientes.dnicliente, 
	clientes.nomcliente, 
	documentos.documento,
	documentos2.documento AS documento2, 
	documentos3.documento AS documento3,
	mediospagos.mediopago,
	SUM(detalleventas.cantventa) AS articulos 
	FROM (ventas LEFT JOIN detalleventas ON detalleventas.codventa = ventas.codventa)
	INNER JOIN cajas ON ventas.codcaja = cajas.codcaja 
	LEFT JOIN sucursales ON ventas.codsucursal = sucursales.codsucursal 
	LEFT JOIN documentos ON sucursales.documsucursal = documentos.coddocumento
	LEFT JOIN documentos AS documentos2 ON sucursales.documencargado = documentos2.coddocumento 
	LEFT JOIN clientes ON ventas.codcliente = clientes.codcliente
	LEFT JOIN documentos AS documentos3 ON clientes.documcliente = documentos3.coddocumento
	LEFT JOIN mediospagos ON ventas.formapago = mediospagos.codmediopago 
	LEFT JOIN usuarios ON ventas.codigo = usuarios.codigo 
	WHERE ventas.codsucursal = '".limpiar($_SESSION["codsucursal"])."' 
	AND DATE_FORMAT(ventas.fechaventa,'%d-%m-%Y') = '".date("d-m-Y")."' 
	GROUP BY detalleventas.codventa";
	foreach ($this->dbh->query($sql) as $row)
		{
			$this->p[] = $row;
		}
		    return $this->p;
			$this->dbh=null;

	} else {

	$sql = "SELECT 
	ventas.idventa, 
	ventas.tipodocumento, 
	ventas.codventa, 
	ventas.codserie, 
	ventas.codautorizacion, 
	ventas.codcaja, 
	ventas.codcliente, 
	ventas.subtotalivasi, 
	ventas.subtotalivano, 
	ventas.iva, 
	ventas.totaliva, 
	ventas.descuento, 
	ventas.totaldescuento, 
	ventas.totalpago, 
	ventas.totalpago2, 
	ventas.tipopago, 
	ventas.formapago, 
	ventas.montopagado, 
	ventas.montodevuelto, 
	ventas.fechavencecredito, 
	ventas.fechapagado,
	ventas.statusventa, 
	ventas.fechaventa,
	ventas.observaciones,  
	ventas.codsucursal, 
	sucursales.documsucursal, 
	sucursales.cuitsucursal, 
	sucursales.razonsocial,
	sucursales.documencargado,
	sucursales.dniencargado,
	sucursales.nomencargado,
	usuarios.dni,
	usuarios.nombres,
	cajas.nrocaja,
	cajas.nomcaja,
	clientes.documcliente, 
	clientes.dnicliente, 
	clientes.nomcliente, 
	documentos.documento,
	documentos2.documento AS documento2, 
	documentos3.documento AS documento3,
	mediospagos.mediopago,
	SUM(detalleventas.cantventa) AS articulos 
	FROM (ventas LEFT JOIN detalleventas ON detalleventas.codventa = ventas.codventa)
	INNER JOIN cajas ON ventas.codcaja = cajas.codcaja 
	LEFT JOIN sucursales ON ventas.codsucursal = sucursales.codsucursal 
	LEFT JOIN documentos ON sucursales.documsucursal = documentos.coddocumento
	LEFT JOIN documentos AS documentos2 ON sucursales.documencargado = documentos2.coddocumento 
	LEFT JOIN clientes ON ventas.codcliente = clientes.codcliente
	LEFT JOIN documentos AS documentos3 ON clientes.documcliente = documentos3.coddocumento
	LEFT JOIN mediospagos ON ventas.formapago = mediospagos.codmediopago 
	LEFT JOIN usuarios ON ventas.codigo = usuarios.codigo 
	WHERE ventas.codigo = '".limpiar($_SESSION["codigo"])."' AND ventas.codsucursal = '".limpiar($_SESSION["codsucursal"])."' AND DATE_FORMAT(ventas.fechaventa,'%d-%m-%Y') = '".date("d-m-Y")."' GROUP BY detalleventas.codventa";
	foreach ($this->dbh->query($sql) as $row)
		{
			$this->p[] = $row;
		}
		    return $this->p;
			$this->dbh=null;
	}
}
###################### FUNCION LISTAR VENTAS DIARIAS ######################

###################### FUNCION BUSQUEDA VENTAS POR CAJAS ###########################
	public function BuscarVentasxCajas() 
	{
		self::SetNames();
		$sql ="SELECT 
		ventas.idventa, 
		ventas.tipodocumento, 
		ventas.codventa, 
		ventas.codserie, 
		ventas.codautorizacion, 
		ventas.codcaja, 
		ventas.codcliente, 
		ventas.subtotalivasi, 
		ventas.subtotalivano, 
		ventas.iva, 
		ventas.totaliva, 
		ventas.descuento, 
		ventas.totaldescuento, 
		ventas.totalpago, 
		ventas.totalpago2, 
		ventas.tipopago, 
		ventas.formapago, 
		ventas.montopagado, 
		ventas.montodevuelto, 
		ventas.fechavencecredito,
	    ventas.fechapagado, 
		ventas.statusventa, 
		ventas.fechaventa, 
	    ventas.observaciones, 
		ventas.codsucursal,
		cajas.nrocaja,
		cajas.nomcaja,
		sucursales.documsucursal, 
		sucursales.cuitsucursal, 
		sucursales.razonsocial,
		sucursales.documencargado,
		sucursales.dniencargado,
		sucursales.nomencargado,
	    usuarios.dni,
	    usuarios.nombres,
		clientes.documcliente,
		clientes.dnicliente, 
		clientes.nomcliente, 
		clientes.tlfcliente, 
		clientes.id_provincia, 
		clientes.id_departamento, 
		clientes.direccliente, 
		clientes.emailcliente,
		documentos.documento,
		documentos2.documento AS documento2, 
		documentos3.documento AS documento3,
		provincias.provincia,
		departamentos.departamento,
		mediospagos.mediopago,
		SUM(detalleventas.cantventa) as articulos 
		FROM (ventas LEFT JOIN detalleventas ON detalleventas.codventa=ventas.codventa)
		LEFT JOIN cajas ON ventas.codcaja = cajas.codcaja 
		LEFT JOIN mediospagos ON ventas.formapago = mediospagos.codmediopago 
		LEFT JOIN sucursales ON ventas.codsucursal = sucursales.codsucursal 
		LEFT JOIN documentos ON sucursales.documsucursal = documentos.coddocumento
		LEFT JOIN documentos AS documentos2 ON sucursales.documencargado = documentos2.coddocumento 
		LEFT JOIN clientes ON ventas.codcliente = clientes.codcliente
		LEFT JOIN documentos AS documentos3 ON clientes.documcliente = documentos3.coddocumento
		LEFT JOIN provincias ON clientes.id_provincia = provincias.id_provincia 
		LEFT JOIN departamentos ON clientes.id_departamento = departamentos.id_departamento
		LEFT JOIN usuarios ON ventas.codigo = usuarios.codigo
		WHERE ventas.codsucursal = ? AND ventas.codcaja = ? AND DATE_FORMAT(ventas.fechaventa,'%Y-%m-%d') >= ? AND DATE_FORMAT(ventas.fechaventa,'%Y-%m-%d') <= ? GROUP BY detalleventas.codventa";
		$stmt = $this->dbh->prepare($sql);
		$stmt->bindValue(1, trim(decrypt($_GET['codsucursal'])));
		$stmt->bindValue(2, trim(decrypt($_GET['codcaja'])));
		$stmt->bindValue(3, trim(date("Y-m-d",strtotime($_GET['desde']))));
		$stmt->bindValue(4, trim(date("Y-m-d",strtotime($_GET['hasta']))));
		$stmt->execute();
		$num = $stmt->rowCount();
		if($num==0)
		{
	echo "<div class='alert alert-danger'>";
	echo "<button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>";
	echo "<center><span class='fa fa-info-circle'></span> NO SE ENCONTRARON VENTAS PARA LA CAJA SELECCIONADA</center>";
	echo "</div>";		
	exit;
		}
		else
		{
			while($row = $stmt->fetch(PDO::FETCH_ASSOC))
			{
				$this->p[]=$row;
			}
			return $this->p;
			$this->dbh=null;
		}
	}
###################### FUNCION BUSQUEDA VENTAS POR CAJAS ###########################

###################### FUNCION BUSQUEDA VENTAS POR FECHAS ###########################
	public function BuscarVentasxFechas() 
	{
		self::SetNames();
		$sql ="SELECT 
		ventas.idventa, 
		ventas.tipodocumento, 
		ventas.codventa, 
		ventas.codserie, 
		ventas.codautorizacion, 
		ventas.codcaja, 
		ventas.codcliente, 
		ventas.subtotalivasi, 
		ventas.subtotalivano, 
		ventas.iva, 
		ventas.totaliva, 
		ventas.descuento, 
		ventas.totaldescuento, 
		ventas.totalpago, 
		ventas.totalpago2, 
		ventas.tipopago, 
		ventas.formapago, 
		ventas.montopagado, 
		ventas.montodevuelto, 
		ventas.fechavencecredito, 
	    ventas.fechapagado,
		ventas.statusventa, 
		ventas.fechaventa, 
	    ventas.observaciones, 
		ventas.codsucursal,
		sucursales.documsucursal, 
		sucursales.cuitsucursal, 
		sucursales.razonsocial,
		sucursales.documencargado,
		sucursales.dniencargado,
		sucursales.nomencargado,
	    usuarios.dni,
	    usuarios.nombres,
		clientes.documcliente,
		clientes.dnicliente, 
		clientes.nomcliente, 
		clientes.tlfcliente, 
		clientes.id_provincia, 
		clientes.id_departamento, 
		clientes.direccliente, 
		clientes.emailcliente,
		documentos.documento,
		documentos2.documento AS documento2, 
		documentos3.documento AS documento3,
		provincias.provincia,
		departamentos.departamento,
		mediospagos.mediopago,
		SUM(detalleventas.cantventa) as articulos 
		FROM (ventas LEFT JOIN detalleventas ON detalleventas.codventa=ventas.codventa)
		LEFT JOIN mediospagos ON ventas.formapago = mediospagos.codmediopago 
		LEFT JOIN sucursales ON ventas.codsucursal = sucursales.codsucursal 
		LEFT JOIN documentos ON sucursales.documsucursal = documentos.coddocumento
		LEFT JOIN documentos AS documentos2 ON sucursales.documencargado = documentos2.coddocumento 
		LEFT JOIN clientes ON ventas.codcliente = clientes.codcliente
		LEFT JOIN documentos AS documentos3 ON clientes.documcliente = documentos3.coddocumento
		LEFT JOIN provincias ON clientes.id_provincia = provincias.id_provincia 
		LEFT JOIN departamentos ON clientes.id_departamento = departamentos.id_departamento
		LEFT JOIN usuarios ON ventas.codigo = usuarios.codigo
		 WHERE ventas.codsucursal = ? AND ventas.tenant_id = 1 AND DATE_FORMAT(ventas.fechaventa,'%Y-%m-%d') >= ? AND DATE_FORMAT(ventas.fechaventa,'%Y-%m-%d') <= ? GROUP BY detalleventas.codventa";
		$stmt = $this->dbh->prepare($sql);
		$stmt->bindValue(1, trim(decrypt($_GET['codsucursal'])));
		$stmt->bindValue(2, trim(date("Y-m-d",strtotime($_GET['desde']))));
		$stmt->bindValue(3, trim(date("Y-m-d",strtotime($_GET['hasta']))));
		$stmt->execute();
		$num = $stmt->rowCount();
		if($num==0)
		{
	echo "<div class='alert alert-danger'>";
	echo "<button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>";
	echo "<center><span class='fa fa-info-circle'></span> NO SE ENCONTRARON VENTAS PARA EL RANGO DE FECHA INGRESADO</center>";
	echo "</div>";		
	exit;
		}
		else
		{
			while($row = $stmt->fetch(PDO::FETCH_ASSOC))
			{
				$this->p[]=$row;
			}
			return $this->p;
			$this->dbh=null;
		}
	}
###################### FUNCION BUSQUEDA VENTAS POR FECHAS ###########################

############################ FIN DE CLASE VENTAS #############################





































###################################### CLASE CREDITOS ###################################

####################### FUNCION REGISTRAR PAGOS A CREDITOS ##########################
	public function RegistrarPago()
	{
		self::SetNames();

		$sql = "SELECT * FROM arqueocaja INNER JOIN cajas ON arqueocaja.codcaja = cajas.codcaja INNER JOIN usuarios ON cajas.codigo = usuarios.codigo WHERE usuarios.codigo = ?";
		$stmt = $this->dbh->prepare($sql);
		$stmt->execute(array($_SESSION["codigo"]));
		$num = $stmt->rowCount();
		if($num==0)
		{
			echo "1";
			exit;
	    }
	    else if(empty($_POST["codcliente"]) or empty($_POST["codventa"]) or empty($_POST["montoabono"]))
		{
			echo "2";
			exit;
		} 
		else if($_POST["montoabono"] > $_POST["totaldebe"])
		{
			echo "3";
			exit;

		} else {

		################### VERIFICO MONTO DE CREDITO DEL CLIENTE ######################
		$sql = "SELECT montocredito FROM creditosxclientes WHERE codcliente = '".limpiar($_POST['codcliente'])."' AND codsucursal = '".limpiar(decrypt($_POST["codsucursal"]))."'";
		foreach ($this->dbh->query($sql) as $row)
		{
			$this->p[] = $row;
		}
        $monto = (empty($row['montocredito']) ? "0.00" : $row['montocredito']);
        ################### VERIFICO MONTO DE CREDITO DEL CLIENTE ######################

        ################### OBTENGO TOTAL PAGADO DE CREDITO EN VENTA Y SUCURSAL ######################
        $sql2 = "SELECT SUM(montoabono) AS abonopagado FROM abonoscreditosventas WHERE codventa = '".limpiar($_POST['codventa'])."' AND codsucursal = '".limpiar(decrypt($_POST["codsucursal"]))."'";
		foreach ($this->dbh->query($sql2) as $row2)
		{
			$this->p[] = $row2;
		}
        //$abonopagado = (empty($row2['abonopagado']) ? "0.00" : $row2['abonopagado']);
        $abonopagado = ($row2['abonopagado']== "" ? "0.00" : $row2['abonopagado']);
        ################### OBTENGO TOTAL PAGADO DE CREDITO EN VENTA Y SUCURSAL ######################

		$query = "INSERT INTO abonoscreditosventas values (null, ?, ?, ?, ?, ?, ?); ";
		$stmt = $this->dbh->prepare($query);
		$stmt->bindParam(1, $codcaja);
		$stmt->bindParam(2, $codventa);
		$stmt->bindParam(3, $codcliente);
		$stmt->bindParam(4, $montoabono);
		$stmt->bindParam(5, $fechaabono);
		$stmt->bindParam(6, $codsucursal);

		$codcaja = limpiar($_POST["codcaja"]);
		$codventa = limpiar($_POST["codventa"]);
		$codcliente = limpiar($_POST["codcliente"]);
		$montoabono = limpiar($_POST["montoabono"]);
		$fechaabono = limpiar(date("Y-m-d h:i:s"));
		$codsucursal = limpiar(decrypt($_POST["codsucursal"]));
		$stmt->execute();

		$sql = "UPDATE ventas set "
			." creditopagado = ? "
			." WHERE "
			." codventa = ? and codsucursal = ?;
			";
			$stmt = $this->dbh->prepare($sql);
			$stmt->bindParam(1, $creditopagado);
			$stmt->bindParam(2, $codventa);
			$stmt->bindParam(3, $codsucursal);

			$creditopagado = number_format($abonopagado+$_POST["montoabono"], 2, '.', '');
			$codventa = limpiar($_POST["codventa"]);
			$codsucursal = limpiar(decrypt($_POST["codsucursal"]));
			$stmt->execute();
	
    ############# AGREGAMOS EL INGRESO DE PAGOS DE CREDITOS A CAJA ##############
		$sql = "SELECT ingresos FROM arqueocaja WHERE codcaja = '".limpiar($_POST["codcaja"])."' AND statusarqueo = 1";
		foreach ($this->dbh->query($sql) as $row)
		{
			$this->p[] = $row;
		}
		$ingreso = ($row['ingresos']== "" ? "0.00" : $row['ingresos']);

		$sql = "UPDATE arqueocaja set "
		." ingresos = ? "
		." WHERE "
		." codcaja = ? AND statusarqueo = 1;
		";
		$stmt = $this->dbh->prepare($sql);
		$stmt->bindParam(1, $txtTotal);
		$stmt->bindParam(2, $codcaja);

		$txtTotal = number_format($_POST["montoabono"]+$ingreso, 2, '.', '');
		$codcaja = limpiar($_POST["codcaja"]);
		$stmt->execute();

		$sql = "UPDATE creditosxclientes set"
		." montocredito = ? "
		." where "
		." codcliente = ? AND codsucursal = ?;
		";
		$stmt = $this->dbh->prepare($sql);
		$stmt->bindParam(1, $montocredito);
		$stmt->bindParam(2, $codcliente);
		$stmt->bindParam(3, $codsucursal);

        $montocredito = number_format($monto-$_POST["montoabono"], 2, '.', '');
		$codcliente = limpiar($_POST["codcliente"]);
		$codsucursal = limpiar(decrypt($_POST["codsucursal"]));
		$stmt->execute(); 
	################ AGREGAMOS EL INGRESO DE PAGOS DE CREDITOS A CAJA ##############

	############## ACTUALIZAMOS EL STATUS DE LA FACTURA ##################
		if($_POST["montoabono"] == $_POST["totaldebe"]) {

			$sql = "UPDATE ventas set "
			." creditopagado = ?, "
			." statusventa = ?, "
			." fechapagado = ? "
			." WHERE "
			." codventa = ? and codsucursal = ?;
			";
			$stmt = $this->dbh->prepare($sql);
			$stmt->bindParam(1, $creditopagado);
			$stmt->bindParam(2, $statusventa);
			$stmt->bindParam(3, $fechapagado);
			$stmt->bindParam(4, $codventa);
			$stmt->bindParam(5, $codsucursal);

			$creditopagado = number_format($abonopagado+$_POST["montoabono"], 2, '.', '');
			$statusventa = limpiar("PAGADA");
			$fechapagado = limpiar(date("Y-m-d"));
			$codventa = limpiar($_POST["codventa"]);
			$codsucursal = limpiar(decrypt($_POST["codsucursal"]));
			$stmt->execute();
		}
    ############## ACTUALIZAMOS EL STATUS DE LA FACTURA ##################

		
echo "<span class='fa fa-check-square-o'></span> EL ABONO AL CR&Eacute;DITO DE VENTA HA SIDO REGISTRADO EXITOSAMENTE <a href='reportepdf?codventa=".encrypt($codventa)."&codsucursal=".encrypt($codsucursal)."&tipo=".encrypt("TICKETCREDITO")."' class='on-default' data-placement='left' data-toggle='tooltip' data-original-title='Imprimir Documento' target='_black' rel='noopener noreferrer'><font color='black'><strong>IMPRIMIR TICKET</strong></font color></a></div>";

echo "<script>window.open('reportepdf?codventa=".encrypt($codventa)."&codsucursal=".encrypt($codsucursal)."&tipo=".encrypt("TICKETCREDITO")."', '_blank');</script>";
	exit;
   }
}
########################## FUNCION REGISTRAR PAGOS A CREDITOS ####################

###################### FUNCION LISTAR CREDITOS ####################### 
public function ListarCreditos()
{
	self::SetNames();
	$sql = "SELECT 
	ventas.idventa,
	ventas.tipodocumento,
	ventas.codventa, 
	ventas.totalpago, 
	ventas.tipopago,
	ventas.statusventa,
	ventas.fechaventa, 
	ventas.fechavencecredito,
	ventas.fechapagado,
	ventas.observaciones,
	ventas.codsucursal, 
	sucursales.documsucursal, 
	sucursales.cuitsucursal, 
	sucursales.razonsocial,
	sucursales.documencargado,
	sucursales.dniencargado,
	sucursales.nomencargado,
	clientes.codcliente,
	clientes.documcliente, 
	clientes.dnicliente, 
	clientes.nomcliente, 
	clientes.tlfcliente, 
	abonoscreditosventas.codventa as codigo, 
	abonoscreditosventas.fechaabono, 
	documentos.documento,
	documentos2.documento AS documento2, 
	documentos3.documento AS documento3,
	SUM(abonoscreditosventas.montoabono) AS abonototal 
	FROM (ventas INNER JOIN clientes ON ventas.codcliente = clientes.codcliente)
	LEFT JOIN abonoscreditosventas ON ventas.codventa = abonoscreditosventas.codventa
	LEFT JOIN sucursales ON ventas.codsucursal = sucursales.codsucursal 
	LEFT JOIN documentos ON sucursales.documsucursal = documentos.coddocumento
	LEFT JOIN documentos AS documentos2 ON sucursales.documencargado = documentos2.coddocumento 
	LEFT JOIN documentos AS documentos3 ON clientes.documcliente = documentos3.coddocumento
	WHERE ventas.tipopago ='CREDITO' GROUP BY ventas.codventa ORDER BY ventas.idventa DESC";
	foreach ($this->dbh->query($sql) as $row)
	{
		$this->p[] = $row;
	}
	return $this->p;
	$this->dbh=null;
}
###################### FUNCION LISTAR CREDITOS ####################### 

############################ FUNCION ID CREDITOS #################################
	public function CreditosPorId()
	{
		self::SetNames();
		$sql = " SELECT 
		ventas.idventa, 
		ventas.tipodocumento, 
		ventas.codventa, 
		ventas.codserie, 
		ventas.codautorizacion, 
		ventas.codcaja, 
		ventas.codcliente, 
		ventas.subtotalivasi, 
		ventas.subtotalivano, 
		ventas.iva, 
		ventas.totaliva, 
		ventas.descuento, 
		ventas.totaldescuento, 
		ventas.totalpago, 
		ventas.totalpago2, 
		ventas.tipopago, 
		ventas.formapago, 
		ventas.montopagado, 
		ventas.montodevuelto, 
		ventas.fechavencecredito, 
	    ventas.fechapagado,
		ventas.statusventa, 
		ventas.fechaventa,
	    ventas.observaciones,  
		ventas.codsucursal,
		sucursales.documsucursal,
		sucursales.cuitsucursal,
		sucursales.razonsocial,
		sucursales.direcsucursal,
		sucursales.correosucursal,
		sucursales.tlfsucursal,
		sucursales.documencargado,
		sucursales.dniencargado,
		sucursales.nomencargado,
		sucursales.tlfencargado,
		sucursales.llevacontabilidad,
		cajas.nrocaja,
		cajas.nomcaja,
		clientes.documcliente,
		clientes.dnicliente, 
		clientes.nomcliente, 
		clientes.tlfcliente, 
		clientes.id_provincia, 
		clientes.id_departamento, 
		clientes.direccliente, 
		clientes.emailcliente,
		documentos.documento,
		documentos2.documento AS documento2, 
		documentos3.documento AS documento3,
		mediospagos.mediopago,
		usuarios.dni, 
		usuarios.nombres,
		provincias.provincia,
		departamentos.departamento,
		SUM(montoabono) AS abonototal
		FROM (ventas INNER JOIN sucursales ON ventas.codsucursal = sucursales.codsucursal)
		LEFT JOIN abonoscreditosventas ON ventas.codventa = abonoscreditosventas.codventa
		LEFT JOIN documentos ON sucursales.documsucursal = documentos.coddocumento
		LEFT JOIN documentos AS documentos2 ON sucursales.documencargado = documentos2.coddocumento 
		LEFT JOIN clientes ON ventas.codcliente = clientes.codcliente
		LEFT JOIN documentos AS documentos3 ON clientes.documcliente = documentos3.coddocumento
		LEFT JOIN provincias ON clientes.id_provincia = provincias.id_provincia 
		LEFT JOIN departamentos ON clientes.id_departamento = departamentos.id_departamento 
		LEFT JOIN cajas ON abonoscreditosventas.codcaja = cajas.codcaja
		LEFT JOIN mediospagos ON ventas.formapago = mediospagos.codmediopago 
		LEFT JOIN usuarios ON cajas.codigo = usuarios.codigo/**/
		WHERE ventas.codventa = ? AND ventas.codsucursal = ? GROUP BY abonoscreditosventas.codventa";
		$stmt = $this->dbh->prepare($sql);
		$stmt->execute(array(decrypt($_GET["codventa"]),decrypt($_GET["codsucursal"])));
		$num = $stmt->rowCount();
		if($num==0)
		{
			echo "";
		}
		else
		{
			if($row = $stmt->fetch(PDO::FETCH_ASSOC))
			{
				$this->p[] = $row;
			}
			return $this->p;
			$this->dbh=null;
		}
	}
############################ FUNCION ID CREDITOS #################################
	
########################### FUNCION VER DETALLES VENTAS #######################
public function VerDetallesAbonos()
{
	self::SetNames();
	$sql = "SELECT * FROM abonoscreditosventas INNER JOIN ventas ON abonoscreditosventas.codventa = ventas.codventa LEFT JOIN cajas ON abonoscreditosventas.codcaja = cajas.codcaja WHERE abonoscreditosventas.codventa = ? AND abonoscreditosventas.codsucursal = ?";	
	$stmt = $this->dbh->prepare($sql);
	$stmt->bindValue(1, trim(decrypt($_GET["codventa"])));
	$stmt->bindValue(2, trim(decrypt($_GET["codsucursal"])));
	$stmt->execute();
	$num = $stmt->rowCount();
	if($num==0)
	{
		echo "";
	}
	else
	{
		while($row = $stmt->fetch(PDO::FETCH_ASSOC))
			{
				$this->p[]=$row;
			}
			return $this->p;
			$this->dbh=null;
		}
	}
########################## FUNCION VER DETALLES VENTAS ###########################

###################### FUNCION BUSQUEDA CREDITOS POR CLIENTES ###########################
	public function BuscarCreditosxClientes() 
	{
		self::SetNames();
		$sql = "SELECT 
	ventas.codventa,
	ventas.tipodocumento, 
	ventas.totalpago, 
	ventas.tipopago,
	ventas.statusventa,
	ventas.fechaventa, 
	ventas.fechavencecredito,
	ventas.fechapagado,
	ventas.observaciones,
	ventas.codsucursal, 
	sucursales.documsucursal, 
	sucursales.cuitsucursal, 
	sucursales.razonsocial,
	sucursales.documencargado,
	sucursales.dniencargado,
	sucursales.nomencargado,
	clientes.codcliente,
	clientes.documcliente, 
	clientes.dnicliente, 
	clientes.nomcliente, 
	clientes.tlfcliente, 
	abonoscreditosventas.codventa as codigo, 
	abonoscreditosventas.fechaabono, 
	documentos.documento,
	documentos2.documento AS documento2, 
	documentos3.documento AS documento3,
	SUM(abonoscreditosventas.montoabono) AS abonototal  
	FROM (ventas INNER JOIN clientes ON ventas.codcliente = clientes.codcliente)
	LEFT JOIN abonoscreditosventas ON ventas.codventa = abonoscreditosventas.codventa
	LEFT JOIN sucursales ON ventas.codsucursal = sucursales.codsucursal 
	LEFT JOIN documentos ON sucursales.documsucursal = documentos.coddocumento
	LEFT JOIN documentos AS documentos2 ON sucursales.documencargado = documentos2.coddocumento 
	LEFT JOIN documentos AS documentos3 ON clientes.documcliente = documentos3.coddocumento
		WHERE ventas.codsucursal = ? AND ventas.codcliente = ? AND ventas.tipopago ='CREDITO' GROUP BY ventas.codventa";
		$stmt = $this->dbh->prepare($sql);
		$stmt->bindValue(1, trim(decrypt($_GET['codsucursal'])));
		$stmt->bindValue(2, trim($_GET['cliente']));
		$stmt->execute();
		$num = $stmt->rowCount();
		if($num==0)
		{
	echo "<div class='alert alert-danger'>";
	echo "<button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>";
	echo "<center><span class='fa fa-info-circle'></span> NO SE ENCONTRARON CREDITOS PARA EL CLIENTE INGRESADO</center>";
	echo "</div>";		
	exit;
		}
		else
		{
			while($row = $stmt->fetch(PDO::FETCH_ASSOC))
			{
				$this->p[]=$row;
			}
			return $this->p;
			$this->dbh=null;
		}
	}
###################### FUNCION BUSQUEDA CREDITOS POR CLIENTES ###########################

###################### FUNCION BUSQUEDA CREDITOS POR FECHAS ###########################
	public function BuscarCreditosxFechas() 
	{
		self::SetNames();
		$sql = "SELECT 
	ventas.codventa, 
	ventas.tipodocumento,
	ventas.totalpago, 
	ventas.tipopago,
	ventas.statusventa,
	ventas.fechaventa, 
	ventas.fechavencecredito,
	ventas.fechapagado,
	ventas.observaciones,
	ventas.codsucursal, 
	sucursales.documsucursal, 
	sucursales.cuitsucursal, 
	sucursales.razonsocial,
	sucursales.documencargado,
	sucursales.dniencargado,
	sucursales.nomencargado,
	clientes.codcliente,
	clientes.documcliente, 
	clientes.dnicliente, 
	clientes.nomcliente, 
	clientes.tlfcliente, 
	abonoscreditosventas.codventa as codigo, 
	abonoscreditosventas.fechaabono, 
	documentos.documento,
	documentos2.documento AS documento2, 
	documentos3.documento AS documento3,
	SUM(abonoscreditosventas.montoabono) AS abonototal  
	FROM (ventas INNER JOIN clientes ON ventas.codcliente = clientes.codcliente)
	LEFT JOIN abonoscreditosventas ON ventas.codventa = abonoscreditosventas.codventa
	LEFT JOIN sucursales ON ventas.codsucursal = sucursales.codsucursal 
	LEFT JOIN documentos ON sucursales.documsucursal = documentos.coddocumento
	LEFT JOIN documentos AS documentos2 ON sucursales.documencargado = documentos2.coddocumento 
	LEFT JOIN documentos AS documentos3 ON clientes.documcliente = documentos3.coddocumento
	WHERE ventas.codsucursal = ? AND DATE_FORMAT(ventas.fechaventa,'%Y-%m-%d') >= ? AND DATE_FORMAT(ventas.fechaventa,'%Y-%m-%d') <= ?
	 AND ventas.tipopago ='CREDITO' GROUP BY ventas.codventa";
		$stmt = $this->dbh->prepare($sql);
		$stmt->bindValue(1, trim(decrypt($_GET['codsucursal'])));
		$stmt->bindValue(2, trim(date("Y-m-d",strtotime($_GET['desde']))));
		$stmt->bindValue(3, trim(date("Y-m-d",strtotime($_GET['hasta']))));
		$stmt->execute();
		$num = $stmt->rowCount();
		if($num==0)
		{
	echo "<div class='alert alert-danger'>";
	echo "<button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>";
	echo "<center><span class='fa fa-info-circle'></span> NO SE ENCONTRARON CREDITOS PARA EL RANGO DE FECHA INGRESADO</center>";
	echo "</div>";		
	exit;
		}
		else
		{
			while($row = $stmt->fetch(PDO::FETCH_ASSOC))
			{
				$this->p[]=$row;
			}
			return $this->p;
			$this->dbh=null;
		}
	}
###################### FUNCION BUSQUEDA CREDITOS POR FECHAS ###########################

###################### FUNCION BUSQUEDA CREDITOS POR DETALLES ###########################
	public function BuscarCreditosxDetalles() 
	{
		self::SetNames();
		$sql = "SELECT 
	ventas.codventa, 
	ventas.tipodocumento,
	ventas.totalpago,
	ventas.creditopagado AS abonototal, 
	ventas.tipopago,
	ventas.statusventa,
	ventas.fechaventa, 
	ventas.fechavencecredito,
	ventas.fechapagado,
	ventas.observaciones,
	ventas.codsucursal, 
	sucursales.documsucursal, 
	sucursales.cuitsucursal, 
	sucursales.razonsocial,
	sucursales.documencargado,
	sucursales.dniencargado,
	sucursales.nomencargado,
	clientes.codcliente,
	clientes.documcliente, 
	clientes.dnicliente, 
	clientes.nomcliente, 
	clientes.tlfcliente,
	documentos.documento,
	documentos2.documento AS documento2, 
	documentos3.documento AS documento3,
	GROUP_CONCAT(detalleventas.cantventa, ' | ', detalleventas.producto SEPARATOR '<br>') AS detalles
    FROM (ventas LEFT JOIN clientes ON ventas.codcliente = clientes.codcliente)
	LEFT JOIN detalleventas ON ventas.codventa = detalleventas.codventa
    LEFT JOIN sucursales ON ventas.codsucursal = sucursales.codsucursal 
	LEFT JOIN documentos ON sucursales.documsucursal = documentos.coddocumento
	LEFT JOIN documentos AS documentos2 ON sucursales.documencargado = documentos2.coddocumento 
	LEFT JOIN documentos AS documentos3 ON clientes.documcliente = documentos3.coddocumento
	WHERE ventas.codsucursal = ? AND ventas.codcliente = ? AND DATE_FORMAT(ventas.fechaventa,'%Y-%m-%d') >= ? AND DATE_FORMAT(ventas.fechaventa,'%Y-%m-%d') <= ?
	 AND ventas.tipopago ='CREDITO' GROUP BY ventas.codventa";
		$stmt = $this->dbh->prepare($sql);
		$stmt->bindValue(1, trim(decrypt($_GET['codsucursal'])));
		$stmt->bindValue(2, trim($_GET['cliente']));
		$stmt->bindValue(3, trim(date("Y-m-d",strtotime($_GET['desde']))));
		$stmt->bindValue(4, trim(date("Y-m-d",strtotime($_GET['hasta']))));
		$stmt->execute();
		$num = $stmt->rowCount();
		if($num==0)
		{
	echo "<div class='alert alert-danger'>";
	echo "<button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>";
	echo "<center><span class='fa fa-info-circle'></span> NO SE ENCONTRARON CREDITOS PARA TU BUSQUEDA REALIZADA</center>";
	echo "</div>";		
	exit;
		}
		else
		{
			while($row = $stmt->fetch(PDO::FETCH_ASSOC))
			{
				$this->p[]=$row;
			}
			return $this->p;
			$this->dbh=null;
		}
	}
###################### FUNCION BUSQUEDA CREDITOS POR DETALLES ###########################

###################################### CLASE CREDITOS ###################################


















########################## FUNCION PARA GRAFICOS #################################

########################## FUNCION GRAFICO POR SUCURSALES ##########################
public function GraficoxSucursal()
{
	self::SetNames();
    $sql = "SELECT 
    sucursales.codsucursal id,
	sucursales.razonsocial,
    pag.sumcompras,
    pag2.sumcotizacion,
    pag3.sumventas
     FROM
       sucursales
     LEFT JOIN
       ( SELECT
           codsucursal, SUM(totalpagoc) AS sumcompras         
           FROM compras WHERE DATE_FORMAT(fechaemision,'%Y') = '".date("Y")."' GROUP BY codsucursal) pag ON pag.codsucursal = sucursales.codsucursal  
     LEFT JOIN
       ( SELECT
           codsucursal, SUM(totalpago) AS sumcotizacion
         FROM cotizaciones WHERE DATE_FORMAT(fechacotizacion,'%Y') = '".date("Y")."' GROUP BY codsucursal) pag2 ON pag2.codsucursal = sucursales.codsucursal 
     LEFT JOIN
       ( SELECT
           codsucursal, SUM(totalpago) AS sumventas
         FROM ventas WHERE DATE_FORMAT(fechaventa,'%Y') = '".date("Y")."' GROUP BY codsucursal) pag3 ON pag3.codsucursal = sucursales.codsucursal GROUP BY id";
	foreach ($this->dbh->query($sql) as $row)
	{
		$this->p[] = $row;
	}
	return $this->p;
	$this->dbh=null;
}
########################## FUNCION GRAFICO POR SUCURSALES ###########################

########################### FUNCION SUMA DE COTIZACIONES ############################
public function SumaCotizaciones()
{
	self::SetNames();

	$sql ="SELECT  
	MONTH(fechacotizacion) mes, 
	SUM(totalpago) totalmes
	FROM cotizaciones 
	WHERE codsucursal = '".limpiar($_SESSION["codsucursal"])."' AND YEAR(fechacotizacion) = '".date('Y')."' AND MONTH(fechacotizacion) GROUP BY MONTH(fechacotizacion) ORDER BY 1";
	foreach ($this->dbh->query($sql) as $row)
	{
		$this->p[] = $row;
	}
	return $this->p;
	$this->dbh=null;
 
 } 
########################### FUNCION SUMA DE COTIZACIONES #############################

########################### FUNCION SUMA DE COMPRAS #################################
 public function SumaCompras()
{
	self::SetNames();

	$sql ="SELECT  
	MONTH(fecharecepcion) mes, 
	SUM(totalpagoc) totalmes
	FROM compras 
	WHERE codsucursal = '".limpiar($_SESSION["codsucursal"])."' AND YEAR(fecharecepcion) = '".date('Y')."' AND MONTH(fecharecepcion) GROUP BY MONTH(fecharecepcion) ORDER BY 1";
	foreach ($this->dbh->query($sql) as $row)
	{
		$this->p[] = $row;
	}
	return $this->p;
	$this->dbh=null;
 
 } 
########################### FUNCION SUMA DE COMPRAS #################################

########################### FUNCION SUMA DE VENTAS #################################
 public function SumaVentas()
{
	self::SetNames();

	$sql ="SELECT  
	MONTH(fechaventa) mes, 
	SUM(totalpago) totalmes
	FROM ventas 
	WHERE codsucursal = '".limpiar($_SESSION["codsucursal"])."' AND YEAR(fechaventa) = '".date('Y')."' AND MONTH(fechaventa) GROUP BY MONTH(fechaventa) ORDER BY 1";
	foreach ($this->dbh->query($sql) as $row)
	{
		$this->p[] = $row;
	}
	return $this->p;
	$this->dbh=null;
 
 }
########################### FUNCION SUMA DE VENTAS #################################

########################### FUNCION PRODUCTOS 5 MAS VENDIDOS ############################
	public function ProductosMasVendidos()
	{
		self::SetNames();

	if ($_SESSION['acceso'] == "administradorG") {

	$sql = "SELECT productos.codproducto, productos.producto, productos.codmarca, detalleventas.descproducto, detalleventas.precioventa, productos.existencia, marcas.nommarca, modelos.nommodelo, ventas.fechaventa, sucursales.cuitsucursal, sucursales.razonsocial, SUM(detalleventas.cantventa) as cantidad FROM (ventas LEFT JOIN detalleventas ON ventas.codventa=detalleventas.codventa) LEFT JOIN sucursales ON ventas.codsucursal=sucursales.codsucursal LEFT JOIN productos ON detalleventas.codproducto=productos.codproducto LEFT JOIN marcas ON marcas.codmarca=productos.codmarca LEFT JOIN modelos ON modelos.codmodelo=productos.codmodelo GROUP BY detalleventas.codproducto, detalleventas.precioventa, detalleventas.descproducto ORDER BY productos.codproducto ASC LIMIT 5";
	foreach ($this->dbh->query($sql) as $row)
	{
		$this->p[] = $row;
	}
	return $this->p;
	$this->dbh=null;

     } else {

       $sql = "SELECT 
       productos.codproducto, 
       productos.producto,
       SUM(detalleventas.cantventa) as cantidad 
       FROM (ventas LEFT JOIN detalleventas ON ventas.codventa=detalleventas.codventa) 
       LEFT JOIN sucursales ON ventas.codsucursal=sucursales.codsucursal 
       LEFT JOIN productos ON detalleventas.codproducto=productos.codproducto 
       WHERE ventas.codsucursal = '".limpiar($_SESSION["codsucursal"])."' 
       AND YEAR(ventas.fechaventa) = '".date('Y')."' 
       GROUP BY productos.codproducto, productos.producto 
       ORDER BY productos.codproducto ASC LIMIT 5";


	foreach ($this->dbh->query($sql) as $row)
	{
		$this->p[] = $row;
	}
	return $this->p;
	$this->dbh=null;
   }
}
########################## FUNCION 5 PRODUCTOS MAS VENDIDOS ###########################

########################## FUNCION SUMAR VENTAS POR USUARIOS ##########################
	public function VentasxUsuarios()
	{
		self::SetNames();
     $sql = "SELECT usuarios.codigo, usuarios.nombres, SUM(ventas.totalpago) as total FROM (usuarios INNER JOIN ventas ON usuarios.codigo=ventas.codigo) WHERE ventas.codsucursal = '".limpiar($_SESSION["codsucursal"])."' AND YEAR(ventas.fechaventa) = '".date('Y')."' GROUP BY usuarios.codigo";
	foreach ($this->dbh->query($sql) as $row)
	{
		$this->p[] = $row;
	}
	return $this->p;
	$this->dbh=null;
}
########################## FUNCION SUMAR VENTAS POR USUARIOS #########################


########################## FUNCION PARA CONTAR REGISTROS ###########################
public function ContarRegistros()
	{
      self::SetNames();
if($_SESSION['acceso'] == "administradorG") {

$sql = "SELECT
(SELECT COUNT(codsucursal) FROM sucursales) AS sucursales,
(SELECT COUNT(codigo) FROM usuarios) AS usuarios,
(SELECT COUNT(codproducto) FROM productos) AS productos,
(SELECT COUNT(codcliente) FROM clientes) AS clientes,
(SELECT COUNT(codproveedor) FROM proveedores) AS proveedores,

(SELECT COUNT(codproducto) FROM productos WHERE existencia <= stockoptimo) AS poptimo,
(SELECT COUNT(codproducto) FROM productos WHERE existencia <= stockmedio) AS pmedio,
(SELECT COUNT(codproducto) FROM productos WHERE existencia <= stockminimo) AS pminimo,

(SELECT COUNT(codproducto) FROM productos WHERE fechaoptimo != '0000-00-00' AND fechaoptimo <= '".date("Y-m-d")."') AS foptimo,
(SELECT COUNT(codproducto) FROM productos WHERE fechamedio != '0000-00-00' AND fechamedio <= '".date("Y-m-d")."') AS fmedio,
(SELECT COUNT(codproducto) FROM productos WHERE fechaminimo != '0000-00-00' AND fechaminimo <= '".date("Y-m-d")."') AS fminimo,

(SELECT COUNT(idcompra) FROM compras WHERE tipocompra = 'CREDITO' AND statuscompra = 'PENDIENTE' AND fechavencecredito <= '".date("Y-m-d")."') AS creditoscomprasvencidos,
(SELECT COUNT(idventa) FROM ventas WHERE tipopago = 'CREDITO' AND statusventa = 'PENDIENTE' AND fechavencecredito <= '".date("Y-m-d")."') AS creditosventasvencidos";

		foreach ($this->dbh->query($sql) as $row)
		{
			$this->p[] = $row;
		}
		return $this->p;
		$this->dbh=null;

     } else {

$sql = "SELECT
(SELECT COUNT(codigo) FROM usuarios WHERE codsucursal = '".$_SESSION["codsucursal"]."') AS usuarios,
(SELECT COUNT(codproducto) FROM productos WHERE codsucursal = '".$_SESSION["codsucursal"]."') AS productos,
(SELECT COUNT(codcliente) FROM clientes) AS clientes,
(SELECT COUNT(codproveedor) FROM proveedores) AS proveedores,
(SELECT COUNT(idcotizacion) FROM cotizaciones WHERE codsucursal = '".$_SESSION["codsucursal"]."') AS cotizaciones,
(SELECT COUNT(idcompra) FROM compras WHERE codsucursal = '".$_SESSION["codsucursal"]."') AS compras,
(SELECT COUNT(idventa) FROM ventas WHERE codsucursal = '".$_SESSION["codsucursal"]."') AS ventas,

(SELECT COUNT(codproducto) FROM productos WHERE existencia <= stockoptimo AND existencia > stockmedio AND codsucursal = '".$_SESSION["codsucursal"]."') AS poptimo,
(SELECT COUNT(codproducto) FROM productos WHERE existencia <= stockmedio AND existencia > stockminimo AND codsucursal = '".$_SESSION["codsucursal"]."') AS pmedio,
(SELECT COUNT(codproducto) FROM productos WHERE existencia <= stockminimo AND codsucursal = '".$_SESSION["codsucursal"]."') AS pminimo,


(SELECT COUNT(codproducto) FROM productos WHERE fechaoptimo != '0000-00-00' AND '".date("Y-m-d")."' <= fechaoptimo AND '".date("Y-m-d")."' > fechamedio AND codsucursal = '".$_SESSION["codsucursal"]."') AS foptimo,
(SELECT COUNT(codproducto) FROM productos WHERE fechamedio != '0000-00-00' AND fechamedio <= '".date("Y-m-d")."' AND '".date("Y-m-d")."' > fechaminimo AND codsucursal = '".$_SESSION["codsucursal"]."') AS fmedio,
(SELECT COUNT(codproducto) FROM productos WHERE fechaminimo != '0000-00-00' AND fechaminimo <= '".date("Y-m-d")."' AND codsucursal = '".$_SESSION["codsucursal"]."') AS fminimo,

(SELECT COUNT(idcompra) FROM compras WHERE tipocompra = 'CREDITO' AND statuscompra = 'PENDIENTE' AND fechavencecredito <= '".date("Y-m-d")."' AND codsucursal = '".$_SESSION["codsucursal"]."') AS creditoscomprasvencidos,
(SELECT COUNT(idventa) FROM ventas WHERE tipopago = 'CREDITO' AND statusventa = 'PENDIENTE' AND fechavencecredito <= '".date("Y-m-d")."' AND codsucursal = '".$_SESSION["codsucursal"]."') AS creditosventasvencidos";

		foreach ($this->dbh->query($sql) as $row)
		{
			$this->p[] = $row;
		}
		return $this->p;
		$this->dbh=null;
	}
}
######################## FUNCION PARA CONTAR REGISTROS #############################

########################## FUNCION PARA GRAFICOS #################################




}
############## TERMINA LA CLASE LOGIN ######################
?>