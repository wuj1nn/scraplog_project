<?php
require_once __DIR__ . '/db.php';
$user = current_user();
$role = $user['role'];
$titles = [
    'dashboard' => 'Dashboard', 'manage' => 'Manage storage', 'remove' => 'Storage removal', 'sale' => 'Record sale',
    'storage' => 'Current storage', 'analytics' => 'Analytics', 'earnings' => 'Earnings', 'prices' => 'Price board',
];
$icons = [
    'dashboard' => '<rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/>',
    'manage' => '<path d="M3 13l2.5-8h13L21 13"/><path d="M3 13v6a1 1 0 001 1h16a1 1 0 001-1v-6"/><path d="M3 13h5l1.2 2.5h5.6L16 13h5"/>',
    'remove' => '<path d="M4 7h16"/><path d="M9 7V4h6v3"/><path d="M6 7l1 13h10l1-13"/><path d="M10 11v6M14 11v6"/>',
    'sale' => '<path d="M5 3h14v18l-3-2-2 2-2-2-2 2-2-2-3 2z"/><path d="M9 8h6M9 12h6"/>',
    'storage' => '<path d="M12 3l9 5-9 5-9-5z"/><path d="M3 12.5l9 5 9-5"/><path d="M3 17l9 5 9-5"/>',
    'analytics' => '<path d="M4 20V11M12 20V5M20 20v-7"/><path d="M2 20h20"/>',
    'earnings' => '<circle cx="12" cy="12" r="9"/><path d="M9.5 17V7.5H13a2.6 2.6 0 010 5.2H9.5"/><path d="M8 10.5h6.5"/>',
    'prices' => '<path d="M3 12V4h8l10 10-8 8z"/><circle cx="7.5" cy="8.5" r="1.4"/>',
];
$nav = [
    'OVERVIEW' => ['dashboard'],
    'OPERATIONS' => ['manage', 'remove', 'sale'],
    'INSIGHTS' => ['storage', 'analytics', 'earnings'],
    'MARKET' => ['prices'],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>ScrapLog — <?= $titles[$page] ?></title>
<link rel="icon" href="assets/images/SCRAPLOG_nobg.png">
<script src="assets/js/theme.js"></script>
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body data-role="<?= e($role) ?>" data-page="<?= e($page) ?>" data-user="<?= e($user['name']) ?>">

<div id="screenApp">

  <aside class="navPanel" id="navPanel">
    <button type="button" class="navToggle" id="burgerBtn" aria-label="Expand or collapse menu" aria-expanded="false">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 6l6 6-6 6"/></svg>
    </button>
    <div class="navScroll">
      <a class="navLogo" href="<?= home_for($role) ?>" title="ScrapLog"><img class="navLogoImg" src="assets/images/SCRAPLOG_nobg.png" alt=""><span class="logoWord lbl">ScrapLog</span></a>
<?php foreach($nav as $group => $pages):
    $visible = array_filter($pages, function($p) use ($role){ return can_access($role, $p) && ($p !== 'prices' || $role === 'customer'); });
    if(!$visible) continue; ?>

      <div class="navGroupLabel"><span class="lbl"><?= $group ?></span></div>
<?php foreach($visible as $p): ?>
      <a class="navLink<?= $p === $page ? ' active' : '' ?>" href="<?= $p ?>.php" title="<?= $titles[$p] ?>"><span class="ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><?= $icons[$p] ?></svg></span><span class="lbl"><?= $titles[$p] ?></span></a>
<?php endforeach; endforeach; ?>

      <div class="navFoot">
        <div class="avatar"><?= e($user['initials']) ?></div>
        <div class="lbl"><div class="whoName"><?= e($user['name']) ?></div><span class="whoRole"><?= e($user['label']) ?></span></div>
      </div>
    </div>
  </aside>

  <div class="navScrim" id="navScrim"></div>

  <div class="shell">
    <div class="topheader">
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

    <main class="main">
      <div class="topbar">
        <div>
          <div class="crumb">Home / <span id="crumb"><?= $titles[$page] ?></span></div>
          <h1 class="pageTitle" id="pageTitle"><?= $titles[$page] ?></h1>
        </div>
        <div class="topbarActions">
          <span class="pill">&#9638; <?= date('M Y') ?></span>
<?php if($role !== 'customer' && $page !== 'manage'): ?>
          <a class="btnCta" href="manage.php">+ New entry</a>
<?php endif; ?>
        </div>
      </div>
<?php if($flash = pull_flash()): ?>
      <div class="flash show<?= $flash[0] === 'err' ? ' err' : '' ?>"><?= e($flash[1]) ?></div>
<?php endif; ?>

