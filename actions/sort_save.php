<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_page('sort', '../');

if($_SERVER['REQUEST_METHOD'] !== 'POST'){
    header('Location: ../sort.php');
    exit;
}
csrf_check('../sort.php');

$code = $_POST['category'] ?? '';
$kg = round((float)($_POST['kg'] ?? 0), 2);
$source = clip($_POST['source'] ?? '', 100);
$note = clip($_POST['note'] ?? '', 255);

$stmt = $pdo->prepare('SELECT id, short_name FROM categories WHERE code = ?');
$stmt->execute([$code]);
$cat = $stmt->fetch();

if(!$cat || $kg <= 0 || $kg > 100000){
    set_flash('err', 'Pick a category and enter a weight above 0.');
} else {
    $pdo->prepare('INSERT INTO stock_entries (category_id, kg, source, note, user_id) VALUES (?, ?, ?, ?, ?)')
        ->execute([$cat['id'], $kg, $source, $note, current_user()['id']]);
    set_flash('ok', 'Added ' . $kg . ' kg of ' . $cat['short_name'] . ' to storage.');
}
header('Location: ../sort.php');
exit;
