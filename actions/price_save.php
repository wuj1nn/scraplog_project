<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_page('price', '../');

if($_SERVER['REQUEST_METHOD'] !== 'POST'){
    header('Location: ../manage.php');
    exit;
}
csrf_check('../manage.php');

$sell = $_POST['sell'] ?? [];
$offset = $_POST['offset'] ?? [];
if(!is_array($sell) || !is_array($offset)){
    set_flash('err', 'Could not read the prices. Please try again.');
    header('Location: ../manage.php');
    exit;
}

$ids = $pdo->query('SELECT id FROM items')->fetchAll(PDO::FETCH_COLUMN);
$update = $pdo->prepare('UPDATE items SET sell_price = ?, markup_pct = ?, buy_price = ? WHERE id = ?');

$pdo->beginTransaction();
foreach($ids as $id){
    if(!isset($sell[$id], $offset[$id])) continue;
    if(!is_numeric($sell[$id]) || !is_numeric($offset[$id])){
        $pdo->rollBack();
        set_flash('err', 'Prices and offsets must be numbers.');
        header('Location: ../manage.php');
        exit;
    }
    $s = (float)$sell[$id];
    $m = (float)$offset[$id];
    if($s < 0 || $s > 1000000 || $m < 0 || $m > 300){
        $pdo->rollBack();
        set_flash('err', 'Sell price must be 0 to 1,000,000 and the offset 0% to 300%.');
        header('Location: ../manage.php');
        exit;
    }
    $update->execute([round($s, 2), round($m, 2), round($s / (1 + $m / 100), 2), $id]);
}
$pdo->commit();

set_flash('ok', 'Prices saved and applied to totals.');
header('Location: ../manage.php');
exit;
