<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/data.php';
require_page('sale', '../');

if($_SERVER['REQUEST_METHOD'] !== 'POST'){
    header('Location: ../sale.php');
    exit;
}
csrf_check('../sale.php');

$id = (int)($_POST['item'] ?? 0);
$kg = round((float)($_POST['kg'] ?? 0), 2);
$buyer = clip($_POST['buyer'] ?? '', 100);

if($kg <= 0 || $kg > 100000){
    set_flash('err', 'Enter a quantity above 0.');
    header('Location: ../sale.php');
    exit;
}

$pdo->beginTransaction();
$stmt = $pdo->prepare('SELECT i.id, i.category_id, i.name, i.sell_price, c.short_name
    FROM items i JOIN categories c ON c.id = i.category_id WHERE i.id = ? FOR UPDATE');
$stmt->execute([$id]);
$it = $stmt->fetch();

if(!$it){
    $pdo->rollBack();
    set_flash('err', 'Pick a valid material.');
} elseif((float)$it['sell_price'] <= 0){
    $pdo->rollBack();
    set_flash('err', 'No selling price set for ' . item_label($it['short_name'], $it['name']) . ' yet. Ask the owner to set it in the price legend.');
} elseif($kg > item_stock($pdo, $id) + 0.001){
    $pdo->rollBack();
    set_flash('err', 'Not enough stock. Only ' . item_stock($pdo, $id) . ' kg of ' . item_label($it['short_name'], $it['name']) . ' on hand.');
} else {
    $pdo->prepare('INSERT INTO sales (category_id, item_id, kg, price_per_kg, buyer, user_id) VALUES (?, ?, ?, ?, ?, ?)')
        ->execute([$it['category_id'], $it['id'], $kg, $it['sell_price'], $buyer, current_user()['id']]);
    $pdo->commit();
    set_flash('ok', 'Sale recorded: ' . $kg . ' kg of ' . item_label($it['short_name'], $it['name']) . ' for ₱' . number_format($kg * (float)$it['sell_price'], 2) . '.');
}
header('Location: ../sale.php');
exit;
