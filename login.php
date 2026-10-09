<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/db.php';

if(current_user()){
    header('Location: dashboard.php');
    exit;
}

$error = '';
$username = '';

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $username = trim($_POST['user'] ?? '');
    $password = $_POST['pass'] ?? '';
    $stmt = $pdo->prepare('SELECT id, username, password_hash, name, role FROM users WHERE username = ?');
    $stmt->execute([$username]);
    $u = $stmt->fetch();
    if($u && password_verify($password, $u['password_hash'])){
        session_regenerate_id(true);
        $_SESSION['user'] = [
            'id' => (int)$u['id'], 'username' => $u['username'], 'role' => $u['role'], 'name' => $u['name'],
            'initials' => strtoupper(substr($u['name'], 0, 2)), 'label' => $u['role'] === 'admin' ? 'Owner' : 'Employee',
        ];
        header('Location: dashboard.php');
        exit;
    }
    $error = 'Wrong username or password.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>ScrapLog — Log in</title>
<script>document.documentElement.dataset.theme = localStorage.getItem('theme') || 'light';</script>
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="loginSplit">

  <section class="loginPane">
    <a class="loginBack" href="index.php">&larr; Back</a>
    <label class="themeSwitch loginTheme" title="Toggle dark mode">
      <input type="checkbox" id="themeToggle">
      <span class="themeTrack"><span class="themeKnob"></span></span>
    </label>

    <div class="loginForm">
      <img class="loginMobileLogo" src="assets/images/SCRAPLOG_nobg.png" alt="ScrapLog">
      <h1>Welcome back</h1>
      <p class="loginSub">Log in to check today's storage, prices, and earnings.</p>
      <form method="post" action="login.php">
        <div class="field"><label for="user">Username</label><input id="user" name="user" type="text" value="<?= htmlspecialchars($username) ?>" placeholder="Enter username" autocomplete="username" autofocus required></div>
        <div class="field"><label for="pass">Password</label><input id="pass" name="pass" type="password" placeholder="Enter password" autocomplete="current-password" required></div>
        <div class="loginErr"><?= htmlspecialchars($error) ?></div>
        <button class="btnGold" type="submit">Log in</button>
      </form>
      <div class="loginFoot">&copy; 2026 ScrapLog</div>
    </div>
  </section>

  <section class="loginHero">
    <span class="heroShape shape1"></span>
    <span class="heroShape shape2"></span>
    <span class="heroShape shape3"></span>
    <span class="heroShape shape4"></span>
    <img src="assets/images/SCRAPLOG_nobg.png" alt="ScrapLog">
    <h2>Every kilo, <span>accounted for.</span></h2>
    <p>Log incoming scrap, record sales, and watch your storage and earnings update in real time.</p>
    <div class="heroChips"><span>Live stock</span><span>Price legend</span><span>Sales &amp; analytics</span></div>
  </section>

</div>

<script>
const themeToggle = document.getElementById('themeToggle');
themeToggle.checked = document.documentElement.dataset.theme === 'dark';
themeToggle.addEventListener('change', function(){
  let theme = themeToggle.checked ? 'dark' : 'light';
  document.documentElement.dataset.theme = theme;
  localStorage.setItem('theme', theme);
});
</script>
</body>
</html>