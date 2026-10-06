<?php
session_start();
date_default_timezone_set('Asia/Manila');

$STAFF_PAGES = ['dashboard', 'sort', 'sale', 'storage'];

function e($s){
    return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
}

function clip($s, $n){
    $s = trim((string)$s);
    return function_exists('mb_substr') ? mb_substr($s, 0, $n) : substr($s, 0, $n);
}

function current_user(){
    return $_SESSION['user'] ?? null;
}

function can_access($role, $page){
    global $STAFF_PAGES;
    return $role === 'admin' || in_array($page, $STAFF_PAGES);
}

function require_page($page, $prefix = ''){
    header('Cache-Control: no-store, no-cache, must-revalidate');
    $user = current_user();
    if(!$user){
        header('Location: ' . $prefix . 'login.php');
        exit;
    }
    if(!can_access($user['role'], $page)){
        header('Location: ' . $prefix . 'dashboard.php');
        exit;
    }
}

function set_flash($type, $msg){
    $_SESSION['flash'] = [$type, $msg];
}

function pull_flash(){
    $f = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $f;
}

function csrf_token(){
    if(empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(16));
    return $_SESSION['csrf'];
}

function csrf_field(){
    return '<input type="hidden" name="csrf" value="' . csrf_token() . '">';
}

function csrf_check($back){
    if(!hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')){
        set_flash('err', 'Session expired. Please try again.');
        header('Location: ' . $back);
        exit;
    }
}
