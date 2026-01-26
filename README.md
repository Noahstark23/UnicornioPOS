# Unicornio POS 🦄
## Sistema Multisucursal para Gestión de Ventas e Inventario

[![PHP Version](https://img.shields.io/badge/PHP-8.2-blue.svg)](https://php.net)
[![MySQL](https://img.shields.io/badge/MySQL-8.0-orange.svg)](https://mysql.com)
[![License](https://img.shields.io/badge/license-Proprietary-red.svg)](LICENSE)

---

## 📋 Descripción

**Unicornio POS** es un sistema completo de punto de venta multisucursal diseñado para empresas que requieren gestión integral de:

- 💰 **Ventas y Facturación**
- 📦 **Inventario y Stock**
- 👥 **Clientes y CRM**
- 📊 **Reportes y Análisis**
- 💳 **Créditos y Cobranza**
- 🏪 **Multi-sucursal**
- 💵 **Control de Caja**

---

## 🚀 Estado del Proyecto

**Versión Actual**: 2.0 (Monolítico - En Transición a SaaS)

### ⚠️ Transformación SaaS en Progreso

Este proyecto está en proceso de transformación de una arquitectura monolítica a **SaaS multi-tenant**. Ver [`informe_ceo_saas.md`](https://github.com/Noahstark23/UnicornioPOS/blob/main/README.md) para detalles completos.

**Estado Multi-Tenancy**:
- ✅ 4 tablas migradas (`clientes`, `productos`, `ventas`, `usuarios`)
- ⚠️ 35+ tablas pendientes de migración
- 📝 SQL de migración disponible en [`bd-sql/migration_001_multitenant.sql`](bd-sql/migration_001_multitenant.sql)

---

## 🛠️ Stack Tecnológico

| Componente | Tecnología |
|------------|------------|
| **Backend** | PHP 8.2.12 |
| **Database** | MySQL 8.0 / MariaDB 10.4 |
| **Frontend** | HTML5, CSS3, JavaScript (jQuery) |
| **UI Framework** | Bootstrap 4 |
| **Server** | Apache (XAMPP) |
| **APIs** | REST (24 endpoints) |

---

## 📂 Estructura del Proyecto

```
unicornio/
├── api/                    # REST API endpoints
│   ├── credits_mobile.php  # Cobranza móvil
│   ├── guardar_venta.php   # Registro de ventas
│   ├── sale_details.php    # Detalles de ventas
│   └── ...
├── assets/                 # CSS, JS, imágenes
├── bd-sql/                 # Schemas y migraciones SQL
│   ├── migration_001_multitenant.sql
│   └── schema_clean.sql
├── class/                  # Clases PHP
│   ├── Client.php          # Gestión de clientes (multi-tenant)
│   └── class.php           # Clase principal
├── includes/               # Configuración
│   └── db.php              # Conexión PDO Singleton
├── fotos/                  # Archivos subidos
├── fpdf/                   # Biblioteca PDF
├── backups/                # Backups automáticos
├── index.php               # Login
├── panel.php               # Dashboard
├── menu.php                # Navegación
├── vender.php              # Punto de Venta
├── funciones.php           # Funciones auxiliares
└── README.md
```

---

## 🔧 Instalación

### Requisitos Previos

- PHP >= 8.2
- MySQL >= 8.0 o MariaDB >= 10.4
- Apache con mod_rewrite
- Composer (opcional)

### Instalación Local (XAMPP)

1. **Clonar el repositorio**
```bash
git clone https://github.com/Noahstark23/UnicornioPOS.git
cd UnicornioPOS
```

2. **Configurar base de datos**
```bash
# Importar schema base
mysql -u root -p < bd-sql/schema_clean.sql

# (Opcional) Aplicar migración multi-tenant
mysql -u root -p < bd-sql/migration_001_multitenant.sql
```

3. **Configurar conexión**
```php
// Copiar y editar includes/db.php
cp includes/db.php.example includes/db.php

// Editar credenciales
private $host = 'localhost';
private $db   = 'unicornio';
private $user = 'root';
private $pass = '';
```

4. **Iniciar servidor**
```bash
# Con XAMPP
# Copiar proyecto a C:\xampp\htdocs\unicornio
# Iniciar Apache y MySQL desde XAMPP Control Panel
# Acceder a http://localhost/unicornio
```

### Usuario por Defecto

- **Usuario**: ADMIN
- **Password**: ADMIN123

⚠️ **Cambiar credenciales inmediatamente en producción**

---

## 📡 APIs Disponibles

### Endpoints Principales

| Endpoint | Método | Descripción |
|----------|--------|-------------|
| `/api/credits_mobile.php?action=get_debtors` | GET | Lista clientes con deudas |
| `/api/credits_mobile.php?action=post_payment` | POST | Registrar pago de crédito |
| `/api/guardar_venta.php` | POST | Crear nueva venta |
| `/api/sale_details.php?id={id}` | GET | Detalles de venta |
| `/api/dashboard_data.php` | GET | Métricas del dashboard |
| `/api/productos.php?q={query}` | GET | Buscar productos |

### Ejemplo de Uso

```javascript
// Obtener deudores
fetch('/api/credits_mobile.php?action=get_debtors')
  .then(res => res.json())
  .then(data => console.log(data));

// Registrar pago
fetch('/api/credits_mobile.php?action=post_payment', {
  method: 'POST',
  headers: { 'Content-Type': 'application/json' },
  body: JSON.stringify({
    id_credito: 123,
    monto: 150.00,
    forma_pago: 'EFECTIVO'
  })
});
```

---

## 📊 Módulos Funcionales

### ✅ Implementados

- [x] **Ventas** - POS completo con multi-moneda
- [x] **Inventario** - Kardex valorizado, control de stock
- [x] **Clientes** - CRM básico, límite de crédito
- [x] **Créditos** - Sistema de abonos y cobranza
- [x] **Reportes** - Excel y PDF
- [x] **Multi-sucursal** - Gestión de múltiples ubicaciones
- [x] **Compras** - Gestión de proveedores y órdenes
- [x] **Caja** - Apertura, cierre, arqueos
- [x] **Usuarios** - Roles y permisos

### 🚧 En Desarrollo (Roadmap SaaS)

- [ ] **Sistema de Suscripciones** (Stripe integration)
- [ ] **Multi-tenancy Completo** (35+ tablas pendientes)
- [ ] **Autenticación JWT** (API stateless)
- [ ] **Panel de Admin SaaS**
- [ ] **Apps Móviles** (iOS/Android)
- [ ] **Facturación Electrónica** (CFDI, e-invoice)
- [ ] **Integraciones** (PayPal, Mercado Pago, QuickBooks)

---

## 🔒 Seguridad

### Implementado

- ✅ PDO con prepared statements (prevención SQL injection)
- ✅ Sesiones PHP seguras
- ✅ Validación de inputs
- ✅ Sanitización de datos
- ✅ Control de acceso por roles

### Pendiente (SaaS)

- ⚠️ JWT para APIs
- ⚠️ OAuth 2.0
- ⚠️ 2FA (Two-Factor Authentication)
- ⚠️ Rate limiting
- ⚠️ Logs de auditoría

---

## 📈 Roadmap

Ver [`informe_ceo_saas.md`](README.md) para roadmap completo de 12 meses.

### Q1 2026 (Actual)
- ✅ Análisis de código completo
- 🔄 Migración multi-tenant (4/40 tablas)
- 🔄 APIs REST modernizadas

### Q2 2026
- Sistema de suscripciones (Stripe)
- Multi-tenancy completo
- Autenticación JWT
- Migración a cloud (AWS/Azure)

### Q3 2026
- Panel de administración SaaS
- Integraciones de pago
- Apps móviles (React Native)

### Q4 2026
- Analytics avanzado
- ML para predicciones
- Lanzamiento SaaS beta

---

## 🤝 Contribución

Este es un proyecto propietario. Para contribuir:

1. Fork el proyecto
2. Crea una rama (`git checkout -b feature/AmazingFeature`)
3. Commit tus cambios (`git commit -m 'Add AmazingFeature'`)
4. Push a la rama (`git push origin feature/AmazingFeature`)
5. Abre un Pull Request

---

## 📄 Licencia

Este proyecto es **propietario**. Todos los derechos reservados.

Para licencias comerciales, contactar: [noel@example.com](mailto:noel@example.com)

---

## 📞 Soporte

- **Email**: support@unicorniopos.com
- **Issues**: [GitHub Issues](https://github.com/Noahstark23/UnicornioPOS/issues)
- **Docs**: [Documentación](https://docs.unicorniopos.com) (Próximamente)

---

## 🙏 Agradecimientos

- Equipo de desarrollo Unicornio
- Comunidad PHP
- Clientes beta testers

---

**Hecho con ❤️ para PYMEs en LATAM**

