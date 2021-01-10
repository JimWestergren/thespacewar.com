<?php
$title_tag = 'Login | TheSpaceWar.com';

if (isset($_POST['login'])) {
    $pdo = PDOWrap::getInstance();

    $_POST['username_or_email'] = trim($_POST['username_or_email']);

    if (filter_var($_POST['username_or_email'], FILTER_VALIDATE_EMAIL)) {
        $email = strtolower($_POST['username_or_email']);
        $row = $pdo->run("SELECT * FROM users WHERE email = ? LIMIT 1", [$email])->fetch();            
    } elseif (ctype_alnum($_POST['username_or_email'])) {
        $row = $pdo->run("SELECT * FROM users WHERE username = ? LIMIT 1", [$_POST['username_or_email']])->fetch();    
    } else {
        //echo 'Invalid email/username';
    }
    if (!isset($row['id'])) {
        //echo 'No account with this email/username';
    } elseif (!password_verify($_POST['password'], $row['password'])) {
        //echo 'Wrong password';
    } else {
        $rating = calculateRating($row['monthly_win_count'], $row['monthly_loss_count'])['rating'];
        $ip = IpToNumberWithCountry($_SERVER['HTTP_CF_CONNECTING_IP']);
        $pdo->run("UPDATE users SET ip_latest = ?, lastlogintime = ? WHERE id = ?", [$ip, TIMESTAMP, $row['id']]);
        setLoginCookie($row, $rating);
        header("Location: ".$_SERVER['REQUEST_URI']);
        die();
    }     
}

include(ROOT.'view/head.php');

?>

<h1>Login to access this page</h1>

<?php
if (isset($_POST['login'])) {
    echo '<div class="error">Invalid or wrong email/username/password ...</div>';
}
?>

<form method="post" action="">
<label>Username or email:</label><br>
<input type="text" name="username_or_email" required placeholder='Username or email'> (<a href='/register'>Register account</a>)<br>

<label>Password:</label><br>
<input type="password" name="password" required placeholder='Password'> (<a href='/forgot-password'>Forgot password?</a>)<br>


<input type="submit" name="login" value="Login"><br>
</form>
