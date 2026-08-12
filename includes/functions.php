<?php

function e($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function redirect(string $path): never
{
    header('Location: ' . BASE_URL . $path);
    exit;
}

function flash(string $type, string $message): void
{
    $_SESSION['flash'][] = [$type, $message];
}

function get_flashes(): array
{
    $flashes = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);

    return $flashes;
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

function verify_csrf(): void
{
    if (
        $_SERVER['REQUEST_METHOD'] === 'POST'
        && !hash_equals(
            $_SESSION['csrf'] ?? '',
            $_POST['csrf_token'] ?? ''
        )
    ) {
        http_response_code(419);
        exit('Invalid CSRF token.');
    }
}

function old(string $key, string $default = ''): string
{
    return e($_POST[$key] ?? $default);
}

function money($amount): string
{
    return CURRENCY . number_format((float) $amount, 2);
}

function status_badge(string $status): string
{
    $map = [
        'active' => 'success',
        'inactive' => 'secondary',
        'pending' => 'warning',
        'confirmed' => 'primary',
        'processing' => 'info',
        'shipped' => 'primary',
        'delivered' => 'success',
        'cancelled' => 'danger',
        'blocked' => 'danger',
    ];

    $class = $map[$status] ?? 'secondary';

    return '<span class="badge text-bg-' . $class . '">'
        . e(ucfirst($status))
        . '</span>';
}

function current_user(PDO $pdo): ?array
{
    static $user = false;

    if ($user !== false) {
        return $user;
    }

    if (empty($_SESSION['user_id'])) {
        return $user = null;
    }

    $statement = $pdo->prepare(
        'SELECT u.*, r.name AS role_name
         FROM users u
         JOIN roles r ON r.id = u.role_id
         WHERE u.id = ?
         LIMIT 1'
    );

    $statement->execute([(int) $_SESSION['user_id']]);

    return $user = $statement->fetch() ?: null;
}

function user_role(PDO $pdo): string
{
    return current_user($pdo)['role_name'] ?? '';
}

function is_logged_in(PDO $pdo): bool
{
    return current_user($pdo) !== null;
}

function login_user(array $user): void
{
    session_regenerate_id(true);
    $_SESSION['user_id'] = (int) $user['id'];
}

function logout_user(): void
{
    $_SESSION = [];

    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();

        setcookie(
            session_name(),
            '',
            [
                'expires' => time() - 42000,
                'path' => $params['path'],
                'domain' => $params['domain'],
                'secure' => $params['secure'],
                'httponly' => $params['httponly'],
            ]
        );
    }

    session_destroy();
}

function permission_names(PDO $pdo, int $roleId): array
{
    $statement = $pdo->prepare(
        'SELECT p.name
         FROM permissions p
         JOIN role_permissions rp ON rp.permission_id = p.id
         WHERE rp.role_id = ?'
    );

    $statement->execute([$roleId]);

    return $statement->fetchAll(PDO::FETCH_COLUMN);
}

function has_permission(PDO $pdo, string $permission): bool
{
    $user = current_user($pdo);

    if (!$user) {
        return false;
    }

    if ($user['role_name'] === 'admin') {
        return true;
    }

    static $cache = [];

    $roleId = (int) $user['role_id'];

    if (!isset($cache[$roleId])) {
        $cache[$roleId] = array_flip(
            permission_names($pdo, $roleId)
        );
    }

    return isset($cache[$roleId][$permission]);
}

function require_login(PDO $pdo): void
{
    if (!is_logged_in($pdo)) {
        redirect('/login.php');
    }
}

function require_permission(PDO $pdo, string $permission): void
{
    require_login($pdo);

    if (!has_permission($pdo, $permission)) {
        http_response_code(403);
        include __DIR__ . '/403.php';
        exit;
    }
}

function client_id_for_user(PDO $pdo, int $userId): ?int
{
    $statement = $pdo->prepare(
        'SELECT id FROM clients WHERE user_id = ?'
    );

    $statement->execute([$userId]);

    $value = $statement->fetchColumn();

    return $value === false ? null : (int) $value;
}

function employee_id_for_user(PDO $pdo, int $userId): ?int
{
    $statement = $pdo->prepare(
        'SELECT id FROM employees WHERE user_id = ?'
    );

    $statement->execute([$userId]);

    $value = $statement->fetchColumn();

    return $value === false ? null : (int) $value;
}

function get_or_create_cart(PDO $pdo, int $clientId): int
{
    $statement = $pdo->prepare(
        'SELECT id FROM carts WHERE client_id = ?'
    );

    $statement->execute([$clientId]);

    $id = $statement->fetchColumn();

    if ($id) {
        return (int) $id;
    }

    $statement = $pdo->prepare(
        'INSERT INTO carts (client_id) VALUES (?)'
    );

    $statement->execute([$clientId]);

    return (int) $pdo->lastInsertId();
}

function cart_count(PDO $pdo, int $clientId): int
{
    $statement = $pdo->prepare(
        'SELECT COALESCE(SUM(ci.quantity), 0)
         FROM cart_items ci
         JOIN carts c ON c.id = ci.cart_id
         WHERE c.client_id = ?'
    );

    $statement->execute([$clientId]);

    return (int) $statement->fetchColumn();
}

function product_price(array $product): float
{
    return max(
        0,
        (float) $product['selling_price']
            * (1 - ((float) $product['discount'] / 100))
    );
}

function safe_back(string $fallback = '/client/products.php'): never
{
    $url = $_SERVER['HTTP_REFERER'] ?? '';
    $path = parse_url($url, PHP_URL_PATH) ?? '';

    redirect($path !== '' ? $path : $fallback);
}
