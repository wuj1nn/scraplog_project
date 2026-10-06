<?php
require_once __DIR__ . '/includes/auth.php';
$page = 'dashboard';
require_page($page);
$pageScripts = ['dashboard.js'];
include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/' . ($role === 'admin' ? 'dashboard_admin.php' : 'dashboard_staff.php');
include __DIR__ . '/includes/footer.php';
