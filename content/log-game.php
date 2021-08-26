<?php
// Default is that ignore scoring is deactivated
$ignore_scoring = 0;
$one_day_ago = TIMESTAMP-(3600*24);

/*
CREATE TABLE games_logging (
id INT(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
user_won INT(10) UNSIGNED NOT NULL DEFAULT 0,
user_lost INT(10) UNSIGNED NOT NULL DEFAULT 0,
timestamp INT(10) UNSIGNED NOT NULL DEFAULT 0,
length INT(10) UNSIGNED NOT NULL DEFAULT 0,
ignore_scoring SMALLINT(5) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
*/


if (isset($_POST['hash'])) {
    if (!isset($_POST['user_won']) || !is_numeric($_POST['user_won'])) {
        sendEmail(SECRET_DEBUG_EMAIL, 'ERROR: user_won not set or not numeric', '');
        die('ERROR: user_won not set or not numeric');
    }
    if (!isset($_POST['user_lost']) || !is_numeric($_POST['user_lost'])) {
        sendEmail(SECRET_DEBUG_EMAIL, 'ERROR: user_lost not set or not numeric', '');
        die('ERROR: user_lost not set or not numeric');
    }
    if (!isset($_POST['length']) || !is_numeric($_POST['length'])) {
        sendEmail(SECRET_DEBUG_EMAIL, 'ERROR: length not set or not numeric', '');
        die('ERROR: length not set or not numeric');
    }
    if ($_POST['user_won'] == 0) die('ERROR: user_won cannot be 0');
    //if ($_POST['user_lost'] == 0) die('ERROR: user_lost cannot be 0'); // The bot is 0
    if ($_POST['length'] == 0) die('ERROR: length cannot be 0');
    if ($_POST['hash'] != md5($_POST['user_won'].$_POST['user_lost'].$_POST['length'].SECRET_SALT_LOG_GAME)) {
        sendEmail(SECRET_DEBUG_EMAIL, 'ERROR: hash is incorrect', '');
        die('ERROR: hash is incorrect');
    }

    if ($_POST['user_won'] == 3 || $_POST['user_lost'] == 3) {
        die('ERROR: User 3 is only a test account. No logging done.');
    }

    if (!in_array($_SERVER['HTTP_CF_CONNECTING_IP'], SECRET_SERVER_IP_ARRAY)) {
        sendEmail(SECRET_DEBUG_EMAIL, 'ERROR: request from wrong IP', '');
        die('ERROR: request from wrong IP.');
    }

    /*
    user_won = User id that won the game
    user_lost = User id that lost the game
    length = Game length in seconds
    */
    $pdo = PDOWrap::getInstance();

    if($_POST['user_lost'] == 0) { // 0 is the bot
        $pdo->run("UPDATE users SET bot_win_fastest_time = ".TIMESTAMP.", bot_win_fastest_length = ? WHERE id = ? AND (bot_win_fastest_length = 0 OR bot_win_fastest_length > ?);", [$_POST['length'], $_POST['user_won'], $_POST['length']]);

    } else {

        // Ignore scoring if game lasted less than 60 seconds.
        if ( $_POST['length'] < 60 ) {
            $ignore_scoring = 3;
        }

        // Ignore scoring if have already won 2 times against this opponent last 24 hours
        $row = $pdo->run("SELECT COUNT(*) as win_count_same_day FROM games_logging WHERE `user_won` = ? AND `user_lost` = ? AND `timestamp` > ?;", [$_POST['user_won'], $_POST['user_lost'], $one_day_ago])->fetch();
        if ($row['win_count_same_day'] > 2) {
            $ignore_scoring = 5;
        }

        // Ignore scoring if winner already has 3000 more rating than looser
        $accunt_row_winner = $pdo->run("SELECT * FROM users WHERE id = ? LIMIT 1", [$_POST['user_won']])->fetch();
        $monthly_rating_winner = calculateRating($accunt_row_winner['monthly_win_count'], $accunt_row_winner['monthly_loss_count'])['rating'];
        if ($monthly_rating_winner > 3000) {
            $accunt_row_looser = $pdo->run("SELECT monthly_win_count, monthly_loss_count FROM users WHERE id = ? LIMIT 1", [$_POST['user_lost']])->fetch();
            $monthly_rating_looser = calculateRating($accunt_row_looser['monthly_win_count'], $accunt_row_looser['monthly_loss_count'])['rating'];
            if ($monthly_rating_winner-3000 > $monthly_rating_looser) {
                $ignore_scoring = 4;
            }
        }

        $pdo->run("INSERT INTO games_logging (`user_won`, `user_lost`, `timestamp`, `length`, `ignore_scoring`) VALUES (?, ?, ?, ?, ?);", [$_POST['user_won'], $_POST['user_lost'], TIMESTAMP, $_POST['length'], $ignore_scoring]);

        if ($ignore_scoring === 0) {
            $pdo->run("UPDATE users SET monthly_win_count = monthly_win_count+1, quarterly_win_count = quarterly_win_count+1 WHERE id = ?;", [$_POST['user_won']]);
            $pdo->run("UPDATE users SET monthly_loss_count = monthly_loss_count+1, quarterly_loss_count = quarterly_loss_count+1 WHERE id = ?;", [$_POST['user_lost']]);
        }
    }

    die('SUCCESS: All has been correctly logged');
}

$title_tag = 'Log game | TheSpaceWar.com';
require(ROOT.'view/head.php');
?>

<h1>Log game</h1>

<?php

if (isset($_POST['log_game'])) {
    if ($logged_in == []) die('Login to see this page');
    $_POST['username'] = trim($_POST['username']);

    if (!is_numeric(str_replace('-', '', $_POST['date'])) || strtotime($_POST['date']) == false) {
        echo '<div class="error">The date was written in the wrong format. <a href="javascript: history.go(-1);">Go back and correct</a>.</div>';
    } elseif (!ctype_alnum($_POST['username']) || strlen($_POST['username']) < 3 || strlen($_POST['username']) > 30) {
        echo '<div class="error">The username specified is invalid. <a href="javascript: history.go(-1);">Go back and correct</a>.</div>';
    } elseif ($_POST['username'] == $logged_in['username']) {
        echo '<div class="error">The username specified as winner is yourself. You cannot play versus yourself in this game. <a href="javascript: history.go(-1);">Go back and correct</a>.</div>';
    } else {
        $pdo = PDOWrap::getInstance();
        $row = $pdo->run("SELECT id, monthly_win_count, monthly_loss_count FROM users WHERE username = ? LIMIT 1", [$_POST['username']])->fetch();
        if (!isset($row['id'])) {
            echo '<div class="error">There is no account with the specified username. <a href="javascript: history.go(-1);">Go back and correct</a>.</div>';
        } else {
            $timestamp = strtotime($_POST['date']);
            $user_won = $row['id'];
            $user_lost = $logged_in['id'];

            // Ignore scoring if have already won 2 times against this opponent last 24 hours
            $row2 = $pdo->run("SELECT COUNT(*) as win_count_same_day FROM games_logging WHERE `user_won` = ? AND `user_lost` = ? AND (`timestamp` = ? OR `timestamp` > ?);", [$user_won, $user_lost, $timestamp, $one_day_ago])->fetch();
            if ($row2['win_count_same_day'] > 2) {
                $ignore_scoring = 5;
            }

            // Ignore scoring if winner already has 3000 more rating than looser
            $monthly_rating_winner = calculateRating($row['monthly_win_count'], $row['monthly_loss_count'])['rating'];
            if ($monthly_rating_winner > 3000) {
                $accunt_row_looser = $pdo->run("SELECT monthly_win_count, monthly_loss_count FROM users WHERE id = ? LIMIT 1", [$user_lost])->fetch();
                $monthly_rating_looser = calculateRating($accunt_row_looser['monthly_win_count'], $accunt_row_looser['monthly_loss_count'])['rating'];
                if ($monthly_rating_winner-3000 > $monthly_rating_looser) {
                    $ignore_scoring = 4;
                }
            }

            $pdo->run("INSERT INTO games_logging (`user_won`, `user_lost`, `timestamp`, `ignore_scoring`) VALUES (?, ?, ?, ?);", [$user_won, $user_lost, $timestamp, $ignore_scoring]);

            if ($ignore_scoring === 0) {
                $extra_sql_winner = '';
                $extra_sql_looser = '';
                if (date('m', $timestamp) == date('m')) {
                    $extra_sql_winner = ' monthly_win_count = monthly_win_count+1, ';
                    $extra_sql_looser = ' monthly_loss_count = monthly_loss_count+1, ';
                }

                $pdo->run("UPDATE users SET ".$extra_sql_winner." quarterly_win_count = quarterly_win_count+1 WHERE id = ?;", [$row['id']]);
                $pdo->run("UPDATE users SET ".$extra_sql_looser." quarterly_loss_count = quarterly_loss_count+1 WHERE id = ?;", [$logged_in['id']]);
            }
            echo '<div class="good">Game has been logged, thank you. <a href="/account/">Go back</a>.</div>';
        }
    }
}
