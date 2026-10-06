<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_page('sale', '../');

if($_SERVER['REQUEST_METHOD'] !== 'POST'){
    header('Location: ../sale.php');
    exit;
}
csrf_check('../sale.php');

$code = $_POST['category'] ?? '';
$kg = round((float)($_POST['kg'] ?? 0), 2);
$buyer = clip($_POST['buyer'] ?? '', 100);

if($kg <= 0 || $kg > 100000){
    set_flash('err', 'Enter a quantity above 0.');
    header('Location: ../sale.php');
    exit;
}

$pdo->beginTransaction();
$stmt = $pdo->prepare('SELECT c.id, c.short_name, c.sell_price,
        (SELECT COALESCE(SUM(kg), 0) FROM stock_entries WHERE category_id = c.id)
      - (SELECT COALESCE(SUM(kg), 0) FROM sales WHERE category_id = c.id) AS stock
    FROM categories c WHERE c.code = ? FOR UPDATE');
$stmt->execute([$code]);
$cat = $stmt->fetch();

if(!$cat){
    $pdo->rollBack();
    set_flash('err', 'Pick a valid material.');
} elseif((float)$cat['sell_price'] <= 0){
    $pdo->rollBack();
    set_flash('err', 'No selling price set for ' . $cat['short_name'] . ' yet. Ask the owner to set it in the price legend.');
} elseif($kg > (float)$cat['stock'] + 0.001){
    $pdo->rollBack();
    set_flash('err', 'Not enough stock. Only ' . round((float)$cat['stock'], 2) . ' kg of ' . $cat['short_name'] . ' on hand.');
} else {
    $pdo->prepare('INSERT INTO sales (category_id, kg, price_per_kg, buyer, user_id) VALUES (?, ?, ?, ?, ?)')
        ->execute([$cat['id'], $kg, $cat['sell_price'], $buyer, current_user()['id']]);
    $pdo->commit();
    set_flash('ok', 'Sale recorded: ' . $kg . ' kg of ' . $cat['short_name'] . ' for ₱' . number_format($kg * (float)$cat['sell_price'], 2) . '.');
}
header('Location: ../sale.php');
exit;
