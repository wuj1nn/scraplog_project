<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_page('price', '../');

if($_SERVER['REQUEST_METHOD'] !== 'POST'){
    header('Location: ../price.php');
    exit;
}
csrf_check('../price.php');

$buy = $_POST['buy'] ?? [];
$sell = $_POST['sell'] ?? [];
$codes = $pdo->query('SELECT code FROM categories')->fetchAll(PDO::FETCH_COLUMN);
$update = $pdo->prepare('UPDATE categories SET buy_price = ?, sell_price = ? WHERE code = ?');

$pdo->beginTransaction();
foreach($codes as $code){
    if(!isset($buy[$code], $sell[$code])) continue;
    $b = (float)$buy[$code];
    $s = (float)$sell[$code];
    if($b < 0 || $s < 0 || $b > 1000000 || $s > 1000000){
        $pdo->rollBack();
        set_flash('err', 'Prices must be between 0 and 1,000,000.');
        header('Location: ../price.php');
        exit;
    }
    $update->execute([round($b, 2), round($s, 2), $code]);
}
$pdo->commit();

set_flash('ok', 'Prices saved and applied to totals.');
header('Location: ../price.php');
exit;
