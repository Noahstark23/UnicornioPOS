@echo off
echo ==========================================
echo 🦄 INICIANDO UNICORNIO POS (MODO DEV) 🦄
echo ==========================================
echo.

echo 1. Deteniendo contenedores anteriores...
docker-compose -f docker-compose.dev.yml down

echo.
echo 2. Construyendo y levantando nuevos contenedores...
docker-compose -f docker-compose.dev.yml up -d --build

echo.
echo ==========================================
echo ✅ ESTADO: ACTIVO
echo.
echo 🌍 Web App:     http://localhost
echo 🗄️  PHPMyAdmin:  http://localhost:8080
echo 🔌 Base Datos:  localhost:3306 (User: root / Pass: root)
echo.
echo Presiona cualquier tecla para cerrar esta ventana...
pause
