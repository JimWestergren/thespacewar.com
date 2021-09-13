<?php
if ($logged_in === []) {
    require(ROOT.'view/login.php');
    require(ROOT.'view/footer.php');
    die();
}

$pdo = PDOWrap::getInstance();
$a = $pdo->run("SELECT * FROM users WHERE id = ? LIMIT 1", [$logged_in['id']])->fetch();


$title_tag = 'Account Edit | TheSpaceWar.com';
require(ROOT.'view/head.php');
$country_array = countryArray();


if (isset($_POST['save'])) {
    $sql = '';
    $_POST['password'] = trim($_POST['password']);
    $_POST['new_password'] = trim($_POST['new_password']);
    $_POST['new_password2'] = trim($_POST['new_password2']);
    $_POST['email'] = strtolower(trim($_POST['email']));
    $_POST['username'] = trim($_POST['username']);

    $a['newsletter'] = (int) isset($_POST['newsletter']);

    if (isset($country_array[$_POST['country']])) {
        $a['country'] = strtolower($_POST['country']);
    } else {
        $a['country'] = 'xx';
    }
    if (!password_verify($_POST['password'], $a['password'])) {
        $errors[] = 'Your current password was not correct.';
    }

    if ($_POST['new_password'] != $_POST['new_password2']) {
        $errors[] = 'Your password was not the same as your repeat password.';
    } elseif ($_POST['new_password'] != '') {
        $a['password'] = password_hash($_POST['new_password'], PASSWORD_BCRYPT);
        $sql .= ", password = '".$a['password']."'";
    }
    if (!ctype_alnum($_POST['username'])) {
        $errors[] = 'Your username contains invalid characters.';
    } elseif (strlen($_POST['username']) < 3 || strlen($_POST['username']) > 15) {
        $errors[] = 'Your username is too short or too long.';
    } elseif ($_POST['username'] != $a['username']) {
        $row = $pdo->run("SELECT id FROM users WHERE username = ? AND id != ".$logged_in['id']." LIMIT 1", [$_POST['username']])->fetch();
        if (isset($row['id'])) {
            $errors[] = 'I am sorry but there is already an account with the username '.$_POST['username'].', choose another username.';
        } else {
            $a['username'] = $_POST['username'];
        }
    }

    if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Invalid email.';
    } elseif ($_POST['email'] != $a['email']) {
        $row = $pdo->run("SELECT id FROM users WHERE email = ? AND id != ".$logged_in['id']." LIMIT 1", [$_POST['email']])->fetch();
        if (isset($row['id'])) {
            $errors[] = 'There is aleady an account with the email '.$_POST['email'].', try to <a href="/account/">login</a> instead.';
        } else {
            $a['email'] = $_POST['email'];
            $sql .= ", email_status = 0, email = '".$a['email']."'";
            $send_email_confirmation = true;
        }
    }

    if (isset($_POST['referrer']) && $_POST['referrer'] != '') {
        if (!ctype_alnum($_POST['referrer'])) {
            $errors[] = 'Username of referrer contains invalid characters.';
        } elseif (strlen($_POST['referrer']) < 3 || strlen($_POST['referrer']) > 15) {
            $errors[] = 'Username of referrer is too short or too long.';
        } else {
            $row = $pdo->run("SELECT id, regtime FROM users WHERE username = ? AND id != ".$logged_in['id']." LIMIT 1", [$_POST['referrer']])->fetch();
            if (!isset($row['id'])) {
                $errors[] = 'There is no account with the username '.$_POST['referrer'].', correct the username of your referrer or leave this field empty.';
            } elseif ( $a['regtime']+(3600*24*5) < $row['regtime'] ) {
                $errors[] = 'The user '.$_POST['referrer'].' registered more than 5 days later than you - that user could not have referred you.';
            } else {
                $sql .= ", referrer = ".$row['id'];
            }
        }
    }

    if ($_POST['twitter'] != '') {
        if (!ctype_alnum(str_replace('_', '', $_POST['twitter']))) {
            $errors[] = 'Twitter username contains invalid characters.';
        } elseif (strlen($_POST['twitter']) < 3 || strlen($_POST['twitter']) > 15) {
            $errors[] = 'Twitter username is too short or too long.';
        } else {
            $a['twitter'] = $_POST['twitter'];
            $sql .= ", twitter = '".$_POST['twitter']."'";
        }
    } else {
        $a['twitter'] = $_POST['twitter'];
        $sql .= ", twitter = ''";
    }


    if (!isset($errors)) {

        if (isset($send_email_confirmation)) {
            $hash = substr(md5($a['email'].SECRET_SALT_VALIDATE_EMAIL), 10, 12);
            $email_message = "Hi ".$a['username'].",\n\n";
            $email_message .= "Click the following link to validate your new email:\n";
            $email_message .= "https://thespacewar.com/validate?id=".$a['id']."&hash=".$hash."\n\n";
            sendEmail($a['email'], 'Validate your new email', $email_message);
        }

        $pdo->run("UPDATE users SET username = ?, country = ?, newsletter = ? ".$sql." WHERE id = ?;", [$a['username'], $a['country'], $a['newsletter'], $logged_in['id']]);

        echo '<div class="good">The changes to your account has been changed. <a href="/account/">click here to go back to the account page</a>.</div>';
        include(ROOT.'view/footer.php');
        die();
    }
}

if (isset($errors)) {
    foreach ($errors as $error) {
        echo '<div class="error">'.$error.'</div>';
    }
}
?>

<h1>Account Edit</h1>

<form method="post" action="/account/edit">

<label>Username:</label><br>
<input type="text" name="username" required minlength="3" maxlength="15" pattern="[a-zA-Z0-9]+" placeholder='Username' value="<?=$a['username']?>" title="Numbers or letters only. Minimum 3 characters."><br>

<label>Email:</label><br>
<input type="email" name="email" required placeholder='Email' value='<?=$a['email']?>'><br>

<label>Current Password:</label><br>
<input type="password" name="password" required minlength="5" placeholder='Current password' value='<?=$_POST['password'] ?? ''?>' title="Minimum 5 characters."><br>

<label>New Password (write to change):</label><br>
<input type="password" name="new_password" minlength="5" placeholder='New password' value='<?=$_POST['new_password'] ?? ''?>' title="Minimum 5 characters."><br>
<label>Repeat password (write to change):</label><br>
<input type="password" name="new_password2" minlength="5" placeholder='Repeat new password' value='<?=$_POST['new_password2'] ?? ''?>' title="Minimum 5 characters."><br>
<label>Receive monthly emails about tournaments, prices etc:</label><br>
<input type="checkbox" name='newsletter' id="newsletter" onclick="switchSmiley()" value="1" <?php if ($a['newsletter'] == 1) echo 'checked' ?>> 
<?php if ($a['newsletter'] == 1) {
    echo '<span id="sad_smiley" style="display:none">😟 😢</span><span id="happy_smiley" style="display:inline">😃 👍</span>';
} else {
    echo '<span id="sad_smiley" style="display:inline">😟 😢</span><span id="happy_smiley" style="display:none">😃 👍</span>';
} ?>
<br>

<label>Country you want to represent:</label><br>
<select name='country'>
<?php

foreach ($country_array as $code => $name) {
    echo "<option value='".$code."'";
    if (isset($_POST['country']) && $_POST['country'] === $code) {
        echo ' selected';
    } elseif ($a['country'] === strtolower($code) && !isset($_POST['country'])) {
        echo ' selected';
    }
    echo ">".$name."</option>";
}
?>
</select><br>

<?php if (!is_numeric($a['referrer']) && ($a['referrer'] == '' || strpos($a['referrer'], '.'))) {
    // For example fbad1 is a refferer coming from facebook ads that we don't want overwritten ?>
    <label>Optional: Referrer (did another user tell you about The Space War?):</label><br>
    <input type="text" name="referrer" value="<?=$_POST['referrer'] ?? ''?>" minlength="3" maxlength="15" pattern="[a-zA-Z0-9]+" placeholder='Username of who told you about the game' title="Numbers or letters only. Minimum 3 characters."><br>
<?php  } ?>

<label>Optional: Your Twitter username:</label><br>
<input type="text" name="twitter" value="<?=$a['twitter'] ?>" minlength="3" maxlength="15" pattern="[a-zA-Z0-9_]+" placeholder='Twitter username' title="Not a valid Twitter username."><br>

<input type="submit" name="save" value="Save" style="width:300px;margin-top:40px;">
</form>


<script>
function switchSmiley() {
  var checkBox = document.getElementById("newsletter");
  var sad_smiley = document.getElementById("sad_smiley");
  var happy_smiley = document.getElementById("happy_smiley");
  if (checkBox.checked == true){
    sad_smiley.style.display = "none";
    happy_smiley.style.display = "inline";
  } else {
     sad_smiley.style.display = "inline";
     happy_smiley.style.display = "none";
  }
}
</script>
