<?php require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';
require_permission($pdo, 'manage_categories');
verify_csrf();
$id = (int)($_POST['id'] ?? 0);
if ($id) {
    $st = $pdo->prepare("DELETE FROM categories WHERE id=?");
    $st->execute([$id]);
    flash('success', 'Category deleted.');
}
redirect('/admin/categories/index.php');
