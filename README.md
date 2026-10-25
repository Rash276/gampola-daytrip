# Gampola Daytrip

A PHP and MySQL web application for discovering places around Gampola and building a simple one-day visit plan.

## Included features

- Browse 11 seeded local place records, with search, category and distance filters.
- View descriptions, opening information, estimated distance and time, fees, facilities, travel tips and an OpenStreetMap location.
- Add, remove and reorder places in a session-based day plan.
- Password-protected administrator sign-in and place create, update and delete screens.
- Responsive layout for desktop and mobile screens.

## Run with XAMPP

1. Copy this project folder into `C:\xampp\htdocs\gampola-daytrip`.
2. Start Apache and MySQL from the XAMPP Control Panel.
3. In phpMyAdmin, import `database.sql`. It creates the `gampola_daytrip` database and sample records.
4. Check the database settings at the top of `config.php`. XAMPP's default MySQL account is `root` with an empty password.
5. Open a terminal in the project folder and run `C:\xampp\php\php.exe setup_admin.php`. Choose an administrator username and a strong password when prompted.
6. Delete `setup_admin.php` after the administrator has been created.
7. Open `http://localhost/gampola-daytrip/` in a browser. The admin sign-in is at `http://localhost/gampola-daytrip/admin.php`.

## Database

The SQL schema uses three tables: `categories`, `places` and `admins`. Place records have a required category, text details, approximate distance, coordinates and practical visitor information. Server-side validation and prepared statements are used for administrator input and database queries. Administrator passwords are stored as PHP password hashes.

## Project data note

Seed records are based on the preliminary place list in the submitted proposal and SRS. Their coordinates, distances, fees, facilities and opening information are sample/approximate values for demonstration. Verify each attraction and replace uncertain information before presenting this as a public guide. The project planner is for selecting and ordering stops; it does not optimize travel routes or calculate travel time.

The map embed uses OpenStreetMap and needs an internet connection. Place information remains available if the map service is unavailable.

## Submission packaging

Include the PHP source, `assets/`, `includes/`, `database.sql`, this README and the design/report materials you create for your course submission. Exclude passwords, database exports containing real account data, and local environment files.
