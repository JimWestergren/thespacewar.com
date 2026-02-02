<?php
$title_tag = 'Forgot Password | TheSpaceWar.com';

if ($logged_in) {
    header("Location: /account/");
    die();
}


include(ROOT.'view/head.php');


if (isset($_POST['reset'])) {

    $pdo = PDOWrap::getInstance();

    $_POST['email'] = strtolower(trim($_POST['email']));

    if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        echo '<div class="error">Invalid email</div>';
    } else {
        $row = $pdo->run("SELECT * FROM users WHERE email = ? LIMIT 1", [$_POST['email']])->fetch();             
    }
    if (!isset($row['id'])) {
        echo '<div class="error">No account with this email.</div>';

    } elseif ( get_cache('password_reset:email:'.$row['email'], 60*30) !== '') {
        echo '<div class="error">A password reset email has already been sent to '.$row['email'].' within the last 30 minutes.</div>';

    } elseif ( get_cache('password_reset:ip:'.$_SERVER['HTTP_CF_CONNECTING_IP'], 60*20) !== '' ) {
        echo '<div class="error">You have already done a password reset within the last 20 minutes. Please wait.</div>';

    } else {

        $limit = TIMESTAMP + (60*30);
        $hash = md5($row['email'].$row['password'].$limit.SECRET_SALT_PASSWORD_RESET);

        // store any non-empty value as a "flag"
        save_cache( 'password_reset:email:'.$row['email'], '1' );
        save_cache( 'password_reset:ip:'.$_SERVER['HTTP_CF_CONNECTING_IP'], '1' );

        $email_message  = "Your username is: ".$row['username']."\n\n";
        $email_message .= "Click the following link to reset your password:\n";
        $email_message .= "https://thespacewar.com/forgot-password?id=".$row['id']."&hash=".$hash."&limit=".$limit."\n\n";
        $email_message .= "The link expires in 30 minutes.";
        sendEmail($row['email'], 'Reset your password', $email_message);
        echo '<div class="good">A password reset email has now been sent to '.$row['email'].'.<br>Click the link in the email to change your password.<br>The link is valid for 30 minutes.</div>';
        include(ROOT.'view/footer.php');
        die();
    }

}


if (isset($_POST['save'])) {

    $_POST['password'] = trim($_POST['password']);
    $_POST['password2'] = trim($_POST['password2']);

    if (!is_numeric($_POST['id']) || !is_numeric($_POST['limit']) || TIMESTAMP > $_POST['limit']) {
        echo '<div class="error">Invalid link. <a href="/forgot-password">Try again</a>.</div>';
        include(ROOT.'view/footer.php');
        die();
    }

    if ($_POST['password'] != $_POST['password2']) {
        echo '<div class="error">Your password was not the same as your repeat password. <a href="/forgot-password?id='.$_POST['id'].'&hash='.$_POST['hash'].'&limit='.$_POST['limit'].'">Try again</a>.</div>';
        include(ROOT.'view/footer.php');
        die();
    }

    $pdo = PDOWrap::getInstance();

    $row = $pdo->run("SELECT id, email, password FROM users WHERE id = ? LIMIT 1", [$_POST['id']])->fetch();
    if (!isset($row['id']) || $_POST['hash'] != md5($row['email'].$row['password'].$_POST['limit'].SECRET_SALT_PASSWORD_RESET)) {
        echo '<div class="error">Invalid link</div>';
        include(ROOT.'view/footer.php');
        die();
    }
    
    $new_password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $pdo->run("UPDATE users SET password = ?, email_status = 2 WHERE id = ?", [$new_password, $_POST['id']]);

    echo '<div class="good">Your new password has been saved. <a href="/account/">login here</a>.</div>';
    include(ROOT.'view/footer.php');
    die();
}


?>

<form method="post" action="/forgot-password">

<?php if (isset($_GET['id'])) { ?>

    <h1>Change your password</h1>
    <label>New Password:</label><br>
    <input type="password" name="password" required minlength="5" placeholder='Password' value='' title="Minimum 5 characters."><br>
    <label>Repeat new password:</label><br>
    <input type="password" name="password2" required minlength="5" placeholder='Repeat password' value='' title="Minimum 5 characters."><br>
    <input type="hidden" name="id" value="<?=$_GET['id']?>">
    <input type="hidden" name="hash" value="<?=$_GET['hash']?>">
    <input type="hidden" name="limit" value="<?=$_GET['limit']?>">
    <input type="submit" name="save" value="Save new password">

<?php } else { ?>

    <h1>Forgot Password</h1>
    <label>Email:</label><br>
    <input type="email" name="email" required> <br>
    <input type="submit" name="reset" value="Send a password reset email">

<?php } ?>

</form>
</table>