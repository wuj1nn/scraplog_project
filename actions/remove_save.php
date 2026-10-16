<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/data.php';
require_page('remove', '../');

if($_SERVER['REQUEST_METHOD'] !== 'POST'){
    header('Location: ../remove.php');
    exit;
}
csrf_check('../remove.php');

$id = (int)($_POST['item'] ?? 0);
$kg = round((float)($_POST['kg'] ?? 0), 2);
$reason = (string)($_POST['reason'] ?? '');
$note = clip($_POST['note'] ?? '', 255);

if($kg <= 0 || $kg > 100000){
    set_flash('err', 'Enter a quantity above 0.');
    header('Location: ../remove.php');
    exit;
}
if(!in_array($reason, removal_reasons(), true)){
    set_flash('err', 'Pick a reason for the removal.');
    header('Location: ../remove.php');
    exit;
}
if($reason === 'Other' && $note === ''){
    set_flash('err', 'Type out the reason for removing it.');
    header('Location: ../remove.php');
    exit;
}

$pdo->beginTransaction();
$stmt = $pdo->prepare('SELECT i.id, i.category_id, i.name, i.buy_price, c.short_name
    FROM items i JOIN categories c ON c.id = i.category_id WHERE i.id = ? FOR UPDATE');
$stmt->execute([$id]);
$it = $stmt->fetch();

if(!$it){
    $pdo->rollBack();
    set_flash('err', 'Pick a valid material.');
} elseif($kg > item_stock($pdo, $id) + 0.001){
    $pdo->rollBack();
    set_flash('err', 'Not enough stock. Only ' . item_stock($pdo, $id) . ' kg of ' . item_label($it['short_name'], $it['name']) . ' on hand.');
} else {
    $pdo->prepare('INSERT INTO removals (category_id, item_id, kg, reason, note, price_per_kg, user_id) VALUES (?, ?, ?, ?, ?, ?, ?)')
        ->execute([$it['category_id'], $it['id'], $kg, $reason, $note, $it['buy_price'], current_user()['id']]);
    $pdo->commit();
    set_flash('ok', 'Removed ' . $kg . ' kg of ' . item_label($it['short_name'], $it['name']) . ' from storage (' . $reason . ').');
}
header('Location: ../remove.php');
exit;
