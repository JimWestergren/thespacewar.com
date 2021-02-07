<?php
if ($logged_in === []) {
    require(ROOT.'view/login.php');
    require(ROOT.'view/footer.php');
    die();
}

$pdo = PDOWrap::getInstance();
$accunt_row = $pdo->run("SELECT * FROM users WHERE id = ? LIMIT 1", [$logged_in['id']])->fetch();
$ip = IpToNumberWithCountry($_SERVER['HTTP_CF_CONNECTING_IP']);
$pdo->run("UPDATE users SET ip_latest = ?, lastlogintime = ? WHERE id = ?", [$ip, TIMESTAMP, $accunt_row['id']]);
$rating = calculateRating($accunt_row['monthly_win_count'], $accunt_row['monthly_loss_count'])['rating'];
//$bonus = calculateBonus($accunt_row['id'], $rating, $accunt_row['bot_win_fastest_length']);

// Rating has been updated, we update the cookie
if ($logged_in['rating'] != $rating) {
    $_COOKIE['loggedin'] = setLoginCookie($accunt_row, $rating);
}

$title_tag = 'Account | TheSpaceWar.com';
require(ROOT.'view/head.php');
?>

<h1>Your Account</h1>

<?php if ($accunt_row['email_status'] < 2) {
    echo '<div class="error">Your email is not verified, click the link in the email</div>';
} ?>

<p style="float: right">[ <a href="/account/edit">Edit Account</a> ]<p>

<p>Logged in as <strong><?=$accunt_row['username']?></strong> (<a href="/users/<?=$accunt_row['username']?>">your public stat page</a>)<br>
Representing: <img src="https://staticjw.com/redistats/images/flags/<?=$accunt_row['country']?>.gif"> <?=countryArray()[strtoupper($accunt_row['country'])]?><br>

<p>Rating Score this month: <strong style="font-size:24px;"><?=$rating?></strong></p>

<hr>

<h2>Play Online</h2>

<p>First time playing? Please see <a href="https://thespacewar.com/videos" target="_blank">our videos</a> how to play first. It will be much easier for you.</p>

<p><a href="https://play.thespacewar.com/" class='big-button'>Start to play</a><br>
Read this first: alpha testing for desktop and tablets with focus on the browsers Chrome and Firefox, mobile phone does not work good yet. If the game hangs reload the page and please send us a bug report.</p>

<p>Make sure to join our new <a href="https://discord.gg/tv3DXqj" target="_blank">Discord</a> if you have questions, reporting bugs and so forth. Email also works: <a href="mailto:info@thespacewar.com" target="_blank">info@thespacewar.com</a></p>

<!--<p>The winner of the quarterly contest will be awarded $100. See the <a href="https://thespacewar.com/leaderboard">Leaderboard</a>.</p>-->

<!--<p>Please <a href="https://forms.gle/NqFNyWurCAP9GaZCA" target="_blank">take our survey</a>, you are the best :)</p>-->

<hr>

<h2>Winning over our AI bot</h2>

<?php if ($accunt_row['bot_win_fastest_time'] > 0) {
    echo '<p>Your fastest win is '.$accunt_row['bot_win_fastest_length'].' seconds on the '.date('Y-m-d', $accunt_row['bot_win_fastest_time']).'.</p>';
} else {
    echo '<p>You did not win over the bot yet.</p>';
} ?>

<hr>

<h2>Your Referrers</h2>

<p>Your referrer URL: <code>https://thespacewar.com/?referrer=<?=$accunt_row['id']?></code></p>

<!--
<p>Your referral bonus score this month: <strong><?=calculateBonus($accunt_row['id'], $rating, 0)?></strong>. It is calculated as a sum of all the ratings (without bonus) of the referrals but maximum 25% of your rating.</p>
-->

<h3>30 Latest Referrers:</h3>

<p>
<?php
$amount_of_referrers = 0;
$result = $pdo->run("SELECT * FROM users WHERE `referrer` = ?;", [$accunt_row['id']])->fetchAll();
if (count($result) > 0) {
    foreach($result as $row) {
        echo '<a href="/users/'.$row['username'].'">'.$row['username'].'</a> <img src="https://staticjw.com/redistats/images/flags/'.$row['country'].'.gif"> '.calculateRating($row['monthly_win_count'], $row['monthly_loss_count'])['rating'].'<br>';
        $amount_of_referrers++;
    } 
} else {
    echo "<p>None yet.";
}
?>
</p>


<hr>

<h2>Your Progress To Master of The Galaxy</h2>

<?php
$ip = IpToNumberWithCountry($_SERVER['HTTP_CF_CONNECTING_IP']);
$unique_win_count = 0;
$unique_player_count = 0;

$result = $pdo->run("SELECT `user_lost`, `user_won` FROM games_logging WHERE (`user_won` = ".$accunt_row['id']." OR `user_lost` = ".$accunt_row['id'].") AND user_lost > 0 AND length > 0")->fetchAll();
foreach($result as $row) {
    $users_won_over[$row['user_lost']] = $row['user_lost'];
    $users_played_with[$row['user_lost']] = $row['user_lost'];
    $users_played_with[$row['user_won']] = $row['user_won'];
}
unset($users_won_over[$accunt_row['id']]);
unset($users_played_with[$accunt_row['id']]);
if (isset($users_won_over)) {
    $row = $pdo->run("SELECT COUNT(*) as unique_win_count FROM users WHERE `id` IN (".implode(",", $users_won_over).") AND ip != ? AND ip_latest != ?;", [$ip, $ip])->fetch();
    $unique_win_count = $row['unique_win_count'];
}
if (isset($users_played_with)) {
    $row = $pdo->run("SELECT COUNT(*) as unique_player_count FROM users WHERE `id` IN (".implode(",", $users_played_with).") AND ip != ? AND ip_latest != ?;", [$ip, $ip])->fetch();
    $unique_player_count = $row['unique_player_count'];
}



$has_won_quarterly_medal = false;
$has_won_monthly_medal = false;
$winners_array = winnersArrayByUser($accunt_row['username']);
if ($winners_array != []) {
    foreach ($winners_array as $key => $value) {
        if (strpos($value['period'], 'Quarter')) {
            $has_won_quarterly_medal = true;
        } else {
            $has_won_monthly_medal = true;
        } 
    }
}


$row = $pdo->run("SELECT COUNT(*) as deck_count FROM decks WHERE user_id = ?", [$logged_in['id']])->fetch();
$deck_count = $row['deck_count'];


?>

<ul>
    <li>Verify your email <?php if ($accunt_row['email_status'] > 1) {echo "<span class='big-checkmark'>✔</span>";} ?></li>
    <li>Win over the bot <?php if ($accunt_row['bot_win_fastest_time'] > 0) {echo "<span class='big-checkmark'>✔</span>";} ?></li>
    <li>Win over another player online <?php if ($unique_win_count > 0) {echo "<span class='big-checkmark'>✔</span>";} ?></li>
    <li>Refer 1 other player <?php if ($amount_of_referrers > 0) {echo "<span class='big-checkmark'>✔</span>";} ?></li>
    <li>Make you own deck <?php if ($deck_count > 0) {echo "<span class='big-checkmark'>✔</span>";} ?></li>
    <li>Play versus at least 5 different players online <?php if ($unique_player_count > 4) {echo "<span class='big-checkmark'>✔</span>";} ?></li>
    <li>Win over 3 different players online <?php if ($unique_win_count > 2) {echo "<span class='big-checkmark'>✔</span>";} ?></li>
    <li>Win a monthly medal <?php if ($has_won_monthly_medal) {echo "<span class='big-checkmark'>✔</span>";} ?></li>
    <li>Win a quarterly medal <?php if ($has_won_quarterly_medal) {echo "<span class='big-checkmark'>✔</span>";} ?></li>
    <li>Refer 5 other players <?php if ($amount_of_referrers > 4) {echo "<span class='big-checkmark'>✔</span>";} ?></li>
    <li>Play versus at least 10 different players online <?php if ($unique_player_count > 9) {echo "<span class='big-checkmark'>✔</span>";} ?></li>
</ul>

<hr>


<h2>30 Latest Games Played</h2>

<p>
<?php
$result = $pdo->run("SELECT * FROM games_logging WHERE `user_won` = ".$accunt_row['id']." OR `user_lost` = ".$accunt_row['id']." AND user_lost > 0 ORDER BY `timestamp` DESC LIMIT 30;")->fetchAll();
foreach($result as $row) {
    $a['date'] = date('Y-m-d', $row['timestamp']);
    if ($row['length'] > 0) {
        $a['length'] = gmdate("i:s", $row['length']).' minutes';
    } else {
        $a['length'] = 'offline game';
    }
    if ($row['user_won'] == $accunt_row['id']) {
        $a['status'] = '<span style="color:green">won</span>';
        $a['versus'] = $row['user_lost'];
        $users[] = $row['user_lost'];
    } else {
        $a['status'] = '<span style="color:red">lost</span>';
        $a['versus'] = $row['user_won'];
        $users[] = $row['user_won'];
    }
    $array[] = $a;
}

if (isset($array)) {
    $result = $pdo->run("SELECT * FROM users WHERE `id` IN (".implode(",", array_unique($users)).");")->fetchAll();
    foreach($result as $row) {
        $user[$row['id']] = $row;
    }

    foreach($array as $a) {
        echo $a['date'].' '.$a['status'].' versus <a href="/users/'.$user[$a['versus']]['username'].'">'.$user[$a['versus']]['username'].'</a> <img src="https://staticjw.com/redistats/images/flags/'.$user[$a['versus']]['country'].'.gif"> '.calculateRating($user[$a['versus']]['monthly_win_count'], $user[$a['versus']]['monthly_loss_count'])['rating'].' ('.$a['length'].')<br>';
    }
}
?>
</p>

<hr>
<h2>Deck Building</h2>

<p><a href="/account/deck" class='big-button'>Create your Deck</a></p>


<hr>
<h2>Log offline play</h2>

<p>Did you win? Then have the looser to login and log the match here for it to be recorded.</p>

<form action='/log-game' method='post'>
    <table cellpadding="10">
        <tr><td>
    <label>Username of the winner:</label><br>
    <input type="text" name="username" required minlength="3" maxlength="30" pattern="[a-zA-Z0-9]+" placeholder='Username' value="<?=$a['username'] ?? ''?>" title="Numbers or letters only. Minimum 3 characters." style="width:150px;">
    </td><td>
    <label>Date played:</label><br>
    <input type="text" name="date" required length="10" pattern="[0-9-]+" value="<?= date('Y-m-d') ?>" title="Write the date in the format yyyy-mm-dd" style="width:100px;">
    </td><td>
    <label>&nbsp;</label><br>
    <input type="submit" name="log_game" value="Save">
    </td></tr>
    </table>
</form>

<p>It is of course also perfectly fine to play casually without logging the matches. Please agree before starting to play if the match should be logged or not.</p>

<hr>
<h2>Support The Space War</h2>

<p>Please help me with this game. Some things you can do:</p>

<ul>
    <li>Use your referrer URL (see above) to get other people to register on this site.</li>
    <li>Join our <a href="https://discord.gg/tv3DXqj" target="_blank">Discord</a> and be part of the discussion. Give some feedback, report bugs, find players to play against etc.</li>
    <li>Follow the game on <a href='https://www.facebook.com/TheSpaceWarCardGame' target="_blank">Facebook</a>.</li>
    <li>Follow the game on <a href='https://twitter.com/The_Space_War' target="_blank">Twitter</a>.</li>
    <li>Contribute to our page on <a href='https://boardgamegeek.com/boardgame/310172/space-war' target="_blank">BGG</a> and our <a href='https://boardgamegeek.com/thread/2437571/wip-space-war-2-player-card-game' target="_blank">WIP thread</a>.</li>
    <li>Subscribe to our <a href="https://www.youtube.com/channel/UCe2kq-IX7zl2wYGK0bT0ucA" target="_blank">YouTube channel</a>.</li>
    <li>Join our <a href="https://www.reddit.com/r/TheSpaceWar/" target="_blank">Reddit</a> and contribute some content.</li>
</ul>

<p>Thanks!</p>

