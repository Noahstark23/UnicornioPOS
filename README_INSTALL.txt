UNICORNIO POS - FAST INSTALLATION GUIDE
=======================================

1. REQUIREMENTS
   - XAMPP with PHP 8.2+
   - MySQL/MariaDB

2. INSTALLATION STEP-BY-STEP
   A. Copy the 'unicornio' folder to 'c:\xampp\htdocs\'
   B. Open your browser and go to: http://localhost/unicornio/install_wizard.php
   C. Follow the Wizard:
      - Step 1: Checks Environment (Permissions & PHP)
      - Step 2: Database Setup (Enter 'root' user, empty password) -> This will import the database automatically.
      - Step 3: Company Setup (Enter Store Name, Address, Tax info)
      - Step 4: User Setup (Create your 3 Key Users)
        * This step will AUTO-OPEN the Cash Boxes for the Branch Admin and Cashier.
   
3. POST-INSTALL CHECK
   - Login with the 'Super Admin' users you created.
   - Go to 'Cajas / Turnos' -> 'Monitor de Cajas'.
   - Verify that 'Caja Principal' and 'Caja Ventanilla 1' are OPEN.

4. SECURITY
   - The installer automatically renames itself to '_INSTALLED_install_wizard.php.bak' after success.
   - Please DELETE this file permanently before going live.

=======================================
READY TO SELL!
