<?php
$title_tag = 'Register | TheSpaceWar.com';
if ($logged_in) {
    header("Location: /account/");
    die();
}

$country_array = countryArray();

if (isset($_POST['check'])) {
    $_POST['username'] = trim($_POST['username']);
    if (ctype_alnum($_POST['username']) && strlen($_POST['username']) > 2 && strlen($_POST['username']) < 30) {
        $pdo = PDOWrap::getInstance();
        $row = $pdo->run("SELECT id FROM users WHERE username = ? LIMIT 1", [$_POST['username']])->fetch();
        if (!isset($row['id'])) {
            $username_available = true;
        } else {
            $username_available = false;
        }
    }
}

if (isset($_POST['register'])) {
    $_POST['password'] = trim($_POST['password']);
    $_POST['password2'] = trim($_POST['password2']);
    $_POST['email'] = strtolower(trim($_POST['email']));
    $_POST['username'] = trim($_POST['username']);

    $a['newsletter'] = (int) isset($_POST['newsletter']);
    $a['regtime'] = TIMESTAMP;
    $a['lastlogintime'] = TIMESTAMP;
    $a['referrer'] = $_COOKIE['referrer'] ?? '';
    $a['ip'] = IpToNumberWithCountry($_SERVER['HTTP_CF_CONNECTING_IP']);
    $a['ip_latest'] = $a['ip'];
    $a['email_status'] = 0;
    if (isset($country_array[$_POST['country']])) {
        $a['country'] = strtolower($_POST['country']);
    } else {
        $a['country'] = 'xx';
    }
    if ($_POST['password'] != $_POST['password2']) {
        $errors[] = 'Your password was not the same as your repeat password.';
    } else {
        $password_form = $_POST['password'];
        $a['password'] = password_hash($_POST['password'], PASSWORD_BCRYPT);
    }
    if (!ctype_alnum($_POST['username'])) {
        $errors[] = 'Your username contains invalid characters.';
    } elseif (strlen($_POST['username']) < 3 || strlen($_POST['username']) > 15) {
        $errors[] = 'Your username is too short or too long.';
    } else {
        $pdo = PDOWrap::getInstance();
        $row = $pdo->run("SELECT id FROM users WHERE username = ? LIMIT 1", [$_POST['username']])->fetch();
        if (isset($row['id'])) {
            $errors[] = 'I am sorry but there is already an account with the username '.$_POST['username'].', choose another username.';
        } else {
            $a['username'] = $_POST['username'];
        }
    }

    if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Invalid email.';
    } else {
        $row = $pdo->run("SELECT id FROM users WHERE email = ? LIMIT 1", [$_POST['email']])->fetch();
        if (isset($row['id'])) {
            $errors[] = 'There is aleady an account with the email '.$_POST['email'].', try to <a href="/account/">login</a> instead.';
        } else {
            $a['email'] = $_POST['email'];
        }
    }

    if (!isset($errors)) {

        $pdo = PDOWrap::getInstance();

        $pdo->run("INSERT INTO users (username, country, regtime, lastlogintime, referrer, ip, ip_latest, email, email_status, password, newsletter) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);", [$a['username'], $a['country'], $a['regtime'], $a['lastlogintime'], $a['referrer'], $a['ip'], $a['ip_latest'], $a['email'], $a['email_status'], $a['password'], $a['newsletter']]);

        $a['id'] = $pdo->lastInsertId();
        $hash = substr(md5($a['email'].SECRET_SALT_VALIDATE_EMAIL), 10, 12);
        $email_message = "My Magnificent Supreme Galactic Emperor ".$a['username'].",\n\n";
        $email_message .= "Your action are needed to click the following link to validate your email:\n";
        $email_message .= "https://thespacewar.com/validate?id=".$a['id']."&hash=".$hash."\n\n";
        $email_message .= "We are forever grateful and welcome you to this war, may the god of Saturn be with you.";
        sendEmail($a['email'], 'Validate your email', $email_message);

        $rating = 0;
        $_COOKIE['loggedin'] = setLoginCookie($a, $rating);
        $logged_in = isLoggedIn();

        require(ROOT.'view/head.php');
        echo '<div class="good">Your account has been created and you are logged in.<br>Click the link in the email that was sent to '.$a['email'].', to validate your email.</div>';
        include(ROOT.'view/footer.php');
        die();
    }


}

require(ROOT.'view/head.php');


if (isset($errors)) {
    foreach ($errors as $error) {
        echo '<div class="error">'.$error.'</div>';
    }
}
?>

<h1>Create Free Account</h1>

<p>You will be able to play The Space War Online for free in your browser once you register.</p>

<p>The first 5000 registered users that wins a game over another human will unlock Pro Account 5 years for free (estimated value $70).</p>

<form method="post" action="/register">
<?php if (isset($username_available) && $username_available) { ?>
    <div class="good">The username <strong><?=$_POST['username']?></strong> is currently available. Create your account now.</div>
    <input type="hidden" name="username" value="<?=$_POST['username']?>">
<?php } else { ?>
    <label>Username/Alias/Name:</label><br>
    <?php if (isset($username_available) && $username_available == false) { ?>
        (The username <strong><?=$_POST['username']?></strong> is already taken).<br>
    <?php } ?>
    <input type="text" name="username" required minlength="3" maxlength="15" pattern="[a-zA-Z0-9]+" placeholder='Username/Alias/Name' value="<?=$a['username'] ?? ''?>" title="Numbers or letters only. Minimum 3 characters."><br>
<?php } ?>
<label>Email:</label><br>
<input type="email" name="email" required placeholder='Email' value='<?=$a['email'] ?? ''?>'><br>
<label>Password:</label><br>
<input type="password" name="password" required minlength="5" placeholder='Password' value='<?=$password_form ?? ''?>' title="Minimum 5 characters."><br>
<label>Repeat password:</label><br>
<input type="password" name="password2" required minlength="5" placeholder='Repeat password' value='<?=$password_form ?? ''?>' title="Minimum 5 characters."><br>
<label>Receive monthly emails about tournaments, prices etc:</label><br>
<input type="checkbox" name='newsletter' value="1" <?php if (isset($_POST['newsletter'])) echo 'checked' ?>><br>

<label>Country you want to represent:</label><br>
<select name='country'>
<?php

foreach ($country_array as $code => $name) {
    echo "<option value='".$code."'";
    if (isset($_POST['country']) && $_POST['country'] === $code) {
        echo ' selected';
    } elseif ($_SERVER['HTTP_CF_IPCOUNTRY'] === $code && !isset($_POST['country'])) {
        echo ' selected';
    }
    echo ">".$name."</option>";
}
?>
</select><br>
<input type="submit" name="register" value="Create Account" style="margin:auto;display: block;">
</form>


<?php

/*
CREATE TABLE users (
id INT(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
username VARCHAR(255) NOT NULL DEFAULT '',
country VARCHAR(255) NOT NULL DEFAULT '',
regtime INT(10) UNSIGNED NOT NULL DEFAULT 0,
lastlogintime INT(10) UNSIGNED NOT NULL DEFAULT 0,
referrer VARCHAR(255) NOT NULL DEFAULT '',
ip VARCHAR(255) NOT NULL DEFAULT '',
ip_latest VARCHAR(255) NOT NULL DEFAULT '',
email VARCHAR(255) NOT NULL DEFAULT '',
password VARCHAR(255) NOT NULL DEFAULT '',
newsletter SMALLINT(5) NOT NULL DEFAULT 0,
monthly_win_count INT(10) UNSIGNED NOT NULL DEFAULT 0,
monthly_loss_count INT(10) UNSIGNED NOT NULL DEFAULT 0,
quarterly_win_count INT(10) UNSIGNED NOT NULL DEFAULT 0,
quarterly_loss_count INT(10) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;


*/





//$pdo->run("ALTER TABLE users ADD monthly_loss_count INT(10) UNSIGNED NOT NULL DEFAULT 0 AFTER monthly_win_count;");
//$pdo->run("ALTER TABLE users ADD quarterly_win_count INT(10) UNSIGNED NOT NULL DEFAULT 0 AFTER monthly_loss_count;");
//$pdo->run("ALTER TABLE users ADD quarterly_loss_count INT(10) UNSIGNED NOT NULL DEFAULT 0 AFTER quarterly_win_count;");





