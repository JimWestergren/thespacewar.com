<?php
$title_tag = 'Validate Email | TheSpaceWar.com';
require(ROOT.'view/head.php');
?>

<h1>Validate Email</h1>

<?php

if (!isset($_GET['id']) || !isset($_GET['hash']) || !is_numeric($_GET['id'])) {
    echo '<div class="error">Invalid link</div>';
    include(ROOT.'view/footer.php');
    die();
}

$pdo = PDOWrap::getInstance();

$row = $pdo->run("SELECT id, email, email_status FROM users WHERE id = ? LIMIT 1", [$_GET['id']])->fetch();
if (!isset($row['id']) || $_GET['hash'] != substr(md5($row['email'].SECRET_SALT_VALIDATE_EMAIL), 10, 12)) {
    echo '<div class="error">Invalid link</div>';
    include(ROOT.'view/footer.php');
    die();
}

if ($row['email_status'] == 2) {
    echo '<div class="error">Email already verified.</div>';
    include(ROOT.'view/footer.php');
    die();
}

$pdo->run("UPDATE users SET email_status = 2 WHERE id = ? LIMIT 1", [$row['id']]);
echo '<div class="good">Your email is now verified</div>';

echo '<p>Go to <a href="/account/">your account</a>.</p>';
