# Gym Management System (XAMPP-ready)

## Overview
A simple Gym Management System written in PHP (PDO) + MySQL, styled with Tailwind CSS and inspired by shadcn UI aesthetics. Features:
- Admin & Receptionist login
- Member CRUD (add, edit)
- Membership renewal with plans
- AJAX search and pagination
- CSV export & printable report (Save as PDF via browser)
- CSRF protection, prepared statements, action logging

## Installation (XAMPP)
1. Copy `gym_mgmt` folder into `htdocs` (or place project files directly). If using a different folder, update `BASE_URL` in `config.php`.
2. Start Apache and MySQL from XAMPP.
3. Import `db.sql` into phpMyAdmin. **Important:** Replace the placeholder hash in `db.sql` for the admin password.
   - To create a password hash in PHP: `<?php echo password_hash('admin123', PASSWORD_DEFAULT); ?>`
4. Adjust DB credentials in `config.php` if needed.
5. Open `http://localhost/gym_mgmt/index.php` and login.

## Security notes
- Uses prepared statements and CSRF tokens.
- Basic server-side validation is implemented.
- Action logging stored in `logs` table.
- For production: use HTTPS, stronger session cookie settings, rate-limiting, and stricter input/output sanitization.

## Extending
- Add PDF library (FPDF/DOMPDF) to generate binary PDFs automatically.
- Integrate payments for online renewals.
- Add role management UI and password reset flows.
