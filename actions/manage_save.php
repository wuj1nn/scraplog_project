<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/data.php';
require_page('manage', '../');

if($_SERVER['REQUEST_METHOD'] !== 'POST'){
    header('Location: ../manage.php');
    exit;
}
csrf_check('../manage.php');

$id = (int)($_POST['item'] ?? 0);
$kg = round((float)($_POST['kg'] ?? 0), 2);
$source = clip($_POST['source'] ?? '', 100);
$note = clip($_POST['note'] ?? '', 255);
$detail = clip($_POST['detail'] ?? '', 100);

$stmt = $pdo->prepare('SELECT i.id, i.category_id, i.name, i.is_other, c.short_name
    FROM items i JOIN categories c ON c.id = i.category_id WHERE i.id = ?');
$stmt->execute([$id]);
$it = $stmt->fetch();

if(!$it || $kg <= 0 || $kg > 100000){
    set_flash('err', 'Pick a category and enter a weight above 0.');
} elseif($it['is_other'] && $detail === ''){
    set_flash('err', 'Describe the item you are adding.');
} else {
    $detail = $it['is_other'] ? $detail : '';
    $pdo->prepare('INSERT INTO stock_entries (category_id, item_id, kg, source, note, detail, user_id) VALUES (?, ?, ?, ?, ?, ?, ?)')
        ->execute([$it['category_id'], $it['id'], $kg, $source, $note, $detail, current_user()['id']]);
    set_flash('ok', 'Added ' . $kg . ' kg of ' . item_label($it['short_name'], $it['name']) . ($detail !== '' ? ' (' . $detail . ')' : '') . ' to storage.');
}
header('Location: ../manage.php');
exit;
