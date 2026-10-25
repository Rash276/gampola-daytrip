<?php
declare(strict_types=1);

// Update these values for your local MySQL installation (XAMPP defaults shown).
const DB_HOST = '127.0.0.1';
const DB_NAME = 'gampola_daytrip';
const DB_USER = 'root';
const DB_PASS = '';
const APP_NAME = 'Gampola Daytrip';

function db(): PDO {
    static $pdo = null;
    if ($pdo instanceof PDO) return $pdo;
    $pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4', DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
    return $pdo;
}

function e(?string $value): string { return htmlspecialchars($value ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
function is_admin(): bool { return isset($_SESSION['admin_id']); }
function require_admin(): void { if (!is_admin()) { header('Location: admin.php'); exit; } }
function csrf_token(): string { return $_SESSION['csrf'] ??= bin2hex(random_bytes(32)); }
function verify_csrf(): void {
    if (!hash_equals($_SESSION['csrf'] ?? '', (string)($_POST['csrf'] ?? ''))) {
        http_response_code(419); exit('Your form has expired. Please go back and try again.');
    }
}
function flash(string $message, string $type = 'success'): void { $_SESSION['flash'] = [$message, $type]; }
function take_flash(): ?array { $f = $_SESSION['flash'] ?? null; unset($_SESSION['flash']); return $f; }
function categories(): array { return db()->query('SELECT id, name FROM categories ORDER BY name')->fetchAll(); }
function place_by_id(int $id): ?array {
    $q = db()->prepare('SELECT p.*, c.name AS category FROM places p JOIN categories c ON c.id=p.category_id WHERE p.id=?');
    $q->execute([$id]); return $q->fetch() ?: null;
}

if (session_status() !== PHP_SESSION_ACTIVE) session_start();
