<?php
require_once __DIR__ . '/db.php';
$user = current_user();
$role = $user['role'];
$titles = ['dashboard' => 'Dashboard', 'sort' => 'Sort materials', 'price' => 'Price legend', 'sale' => 'Record sale', 'storage' => 'Current storage', 'analytics' => 'Analytics', 'earnings' => 'Projected earnings'];
$nav = [
    'OVERVIEW' => [['dashboard', '&#8962;']],
    'OPERATIONS' => [['sort', '&#9636;'], ['price', '&#8369;'], ['sale', '&#9635;']],
    'INSIGHTS' => [['storage', '&#9637;'], ['analytics', '&#8599;&#xFE0E;'], ['earnings', '&#9678;']],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>ScrapLog — <?= $titles[$page] ?></title>
<link rel="icon" href="assets/images/SCRAPLOG_nobg.png">
<script>document.documentElement.dataset.theme = localStorage.getItem('theme') || 'light';</script>
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body data-role="<?= $role ?>" data-page="<?= $page ?>">

<div id="screenApp">

  <div class="topheader">
    <a class="brandLink" href="dashboard.php" title="ScrapLog"><img src="assets/images/SCRAPLOG_nobg.png" alt="ScrapLog"></a>
    <button class="burgerBtn" id="burgerBtn">&#9776;</button>
        <div class="headerRight">
      <label class="themeSwitch" title="Toggle dark mode">
        <input type="checkbox" id="themeToggle">
        <span class="themeTrack"><span class="themeKnob"></span></span>
      </label>
      <div class="profileMenu" id="profileMenu">
        <button type="button" class="headerAvatar" id="profileBtn" aria-haspopup="true" aria-expanded="false">
          <span class="avatar"><?= e($user['initials']) ?></span>
          <span class="whoText"><span class="whoName"><?= e($user['name']) ?></span><span class="whoRole"><?= e($user['label']) ?></span></span>
          <span class="caret">&#9662;</span>
        </button>
        <div class="profileDrop">
          <div class="profileInfo">
            <span class="avatar big"><?= e($user['initials']) ?></span>
            <span><span class="whoName"><?= e($user['name']) ?></span><span class="whoRole"><?= e($user['label']) ?></span></span>
          </div>
          <a class="dropItem" href="logout.php">Log out</a>
        </div>
      </div>
    </div>
  </div>

  <div class="navScrim" id="navScrim"></div>

  <aside class="navPanel" id="navPanel">
    <div class="navLogo"><img class="navLogoImg" src="assets/images/SCRAPLOG_nobg.png" alt=""><div class="logoWord">ScrapLog</div></div>
<?php foreach($nav as $group => $items):
    $visible = array_filter($items, function($i) use ($role){ return can_access($role, $i[0]); });
    if(!$visible) continue; ?>

    <div class="navGroupLabel"><?= $group ?></div>
<?php foreach($visible as $i): ?>
    <a class="navLink<?= $i[0] === $page ? ' active' : '' ?>" href="<?= $i[0] ?>.php"><span class="ico"><?= $i[1] ?></span> <?= $titles[$i[0]] ?></a>
<?php endforeach; endforeach; ?>

    <div class="navFoot">
      <div class="avatar"><?= $user['initials'] ?></div>
      <div><div class="whoName"><?= $user['name'] ?></div><span class="whoRole"><?= $user['label'] ?></span></div>
    </div>
  </aside>

  <main class="main">
    <div class="topbar">
      <div>
        <div class="crumb">Home / <span id="crumb"><?= $titles[$page] ?></span></div>
        <h1 class="pageTitle" id="pageTitle"><?= $titles[$page] ?></h1>
      </div>
      <div class="topbarActions">
        <span class="pill">&#9638; <?= date('M Y') ?></span>
<?php if($page !== 'sort'): ?>
        <a class="btnCta" href="sort.php">+ New entry</a>
<?php endif; ?>
      </div>    </div>
<?php if($flash = pull_flash()): ?>
    <div class="flash show<?= $flash[0] === 'err' ? ' err' : '' ?>"><?= e($flash[1]) ?></div>
<?php endif; ?>

