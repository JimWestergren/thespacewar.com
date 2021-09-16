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


<?php if ( isset( $_GET['delete_game'] ) ) {

    $_GET['delete_game'] = (int) $_GET['delete_game'];

    $games_logging_row = $pdo->run("SELECT * FROM games_logging WHERE id = ".$_GET['delete_game']." AND `user_won` = ".$logged_in['id']." LIMIT 1;")->fetch();
    if ( isset( $games_logging_row['id'] ) ) {
        $pdo->run("DELETE FROM games_logging WHERE id = ".$_GET['delete_game'].";");
        $pdo->run("UPDATE users SET monthly_win_count = monthly_win_count-1, quarterly_win_count = quarterly_win_count-1 WHERE id = ?;", [$logged_in['id']]);
        $pdo->run("UPDATE users SET monthly_loss_count = monthly_loss_count-1, quarterly_loss_count = quarterly_loss_count-1 WHERE id = ?;", [$games_logging_row['user_lost']]);
        echo '<div class="good">Your win has been removed.</div>';
    } else {
        echo '<div class="error">Your win has not been removed.</div>';
    }
    // Redirect back to account:
    echo "<script>setTimeout(function(){ window.location.href= '/account/';}, 3000);</script>";
    die();
} ?>

<p style="float: right">[ <a href="/account/edit">Edit Account</a> ]<br>[ <a href="/logout">Logout</a> ]<?php if($logged_in['id'] === 1) { echo '<br>[ <a href="/account/admin">Admin</a> ]';} ?><p>

<p>Logged in as <a href="/users/<?=$accunt_row['username']?>"><strong><?=$accunt_row['username']?></strong></a><br>
Representing: <img loading=lazy src="https://staticjw.com/redistats/images/flags/<?=$accunt_row['country']?>.gif"> <?=countryArray()[strtoupper($accunt_row['country'])]?><br>

<p>Rating Score this month: <strong style="font-size:24px;"><?=$rating?></strong></p>


<h2>Play Online</h2>

<p>First time playing? Please see <a href="/videos" target="_blank">our videos</a> how to play first. It will be much easier for you.</p>

<p class="mobile-only" style="color:red;">Does not work good at all in mobile phones, play using desktop or tablet.</p>

<p><a href="https://play.thespacewar.com/" class='big-button'>Start to play</a><br>
Read this first: alpha testing for desktop and tablets with focus on the browsers Chrome and Firefox, mobile phone does not work good yet. If the game hangs reload the page and please send us a bug report.</p>

<p>Make sure to join our new <a href="https://discord.gg/tv3DXqj" target="_blank">Discord</a> if you have questions, reporting bugs and so forth. Email also works: <a href="mailto:info@thespacewar.com" target="_blank">info@thespacewar.com</a></p>

<!--<p>The winner of the quarterly contest will be awarded $100. See the <a href="https://thespacewar.com/leaderboard">Leaderboard</a>.</p>-->

<!--<p>Please <a href="https://forms.gle/NqFNyWurCAP9GaZCA" target="_blank">take our survey</a>, you are the best :)</p>-->



<h2>Your Referrers</h2>

<p>Your referral link: <code>https://thespacewar.com/?referrer=<?=$accunt_row['id']?></code></p>
<p>People registering using your referral link will receive 50 extra credits, and you will earn 10 + 10% of all your referrers. You can also earn real money, see the page <a href="/affiliate">Affiliate</a>.</p>

<h3>20 Latest Referrers:</h3>

<?php
$getLatestReferralsTable = getLatestReferralsTable( $logged_in['id'], 20 );
echo $getLatestReferralsTable['html_output'];


$total_credits = 0;

echo "<h2>📈 Your Credits (BETA)</h2>";

echo '<table>';

if ($accunt_row['id'] < 5000) {
    $total_credits += 200;
    echo '<tr><td>Bonus: 200 credits for being one of the first 5 000 to register account.</td><td>200</td></tr>';
}

if ( is_numeric( $accunt_row['referrer'] ) ) {
    $total_credits += 50;
    echo '<tr><td>Bonus: 50 credits for being referred by another user.</td><td>50</td></tr>';
}

$days_registered = round((TIMESTAMP-$accunt_row['regtime'])/(3600*24));
$total_credits += $days_registered;
echo '<tr><td>1 credit for each day you have been registered.</td><td>'.$days_registered.'</td></tr>';



echo '<tr><td>5 credits for verifying your email and subscribing to monthly newsletter.</td><td>';
if ($accunt_row['email_status'] > 1 && $accunt_row['newsletter'] == 1) {
    echo 5;
    $total_credits += 5;
} else {
    echo 0;
}
echo '</td></tr>';



echo '<tr><td>10 credits for winning over the bot.</td><td>';
if ($accunt_row['bot_win_fastest_time'] > 0) {
    echo 10;
    $total_credits += 10;
} else {
    echo 0;
}
echo '</td></tr>';


echo '<tr><td>5 credits for each game you have won versus people.</td><td>';
$row = $pdo->run("SELECT COUNT(*) as win_count FROM games_logging WHERE `user_won` = ".$accunt_row['id']." AND user_lost > 0 AND ignore_scoring = 0;")->fetch();
echo 5*$row['win_count'];
$total_credits += (5*$row['win_count']);
echo '</td></tr>';



echo '<tr><td>5 credits for making your own constructed deck.</td><td>';
$row = $pdo->run("SELECT COUNT(*) as deck_count FROM decks WHERE user_id = ?", [$logged_in['id']])->fetch();
if ($row['deck_count'] > 0) {
    echo 5;
    $total_credits += 5;
} else {
    echo 0;
}
echo '</td></tr>';


echo '<tr><td>10 credits for each referral.</td><td>';
echo 10*$getLatestReferralsTable['amount_of_referrers'];
$total_credits += (10*$getLatestReferralsTable['amount_of_referrers']);
echo '</td></tr>';


echo '<tr><td>10% of all credits earned from your referrals.</td><td>';
echo round( ( $getLatestReferralsTable['credits_earned_of_referrers']/10 ) );
$total_credits += round( ( $getLatestReferralsTable['credits_earned_of_referrers']/10 ) );
echo '</td></tr>';


$monthly_medals_score = 0;
$quarterly_medals_score = 0;
$winners_array = winnersArrayByUser($accunt_row['username']);
foreach ($winners_array as $key => $value) {
    if (strpos($value['period'], 'Quarter')) {
        if ($value['position'] == '🏆') {
            $quarterly_medals_score += 600;
        } elseif ($value['position'] == '🥈') {
            $quarterly_medals_score += 300;
        } elseif ($value['position'] == '🥉') {
            $quarterly_medals_score += 150;
        }
    } else {
        if ($value['position'] == '🏆') {
            $monthly_medals_score += 100;
        } elseif ($value['position'] == '🥈') {
            $monthly_medals_score += 50;
        } elseif ($value['position'] == '🥉') {
            $monthly_medals_score += 25;
        }
    } 
}

echo '<tr><td>100/50/25 credits for each monthly Gold/Silver/Bronze medal.</td><td>';
echo $monthly_medals_score;
$total_credits += $monthly_medals_score;
echo '</td></tr>';


echo '<tr><td>600/300/150 credits for each quarterly Gold/Silver/Bronze medal.</td><td>';
echo $quarterly_medals_score;
$total_credits += $quarterly_medals_score;
echo '</td></tr>';

echo '<tr><td>Credits awarded from <a href="/tournaments">official tournaments</a>.</td><td>';
$amount = 0;
$tournaments_array = getTournamentArrayByUser($accunt_row['username']);
foreach ($tournaments_array as $key => $value) {
    $amount += $value['award'];
}
echo $amount;
$total_credits += $amount;
echo '</td></tr>';

echo '<tr><td>1000 credits for each <a href="/first-edition">First Edition NFT</a> you own.</td><td>';
$amount_of_nft = count(getFirstEditionNFTbyUser($logged_in['id']));
echo 1000*$amount_of_nft;
$total_credits += (1000*$amount_of_nft);
echo '</td></tr>';




echo '<tr><td style="text-align:right">Total Earned:</td><td>';
echo '<strong>'.$total_credits.'</strong>';
echo '</td></tr>';

$pdo->run("UPDATE users SET credits_earned = ? WHERE id = ?", [$total_credits, $accunt_row['id']]);

?>

</table>


<p>In the future you will be able to use credits to buy cool cosmetic items, enter offical tournaments and play with your constructed decks.</p>


<h2>Winning over our AI bot</h2>

<?php if ($accunt_row['bot_win_fastest_time'] > 0) {
    echo '<p>Your fastest win is '.$accunt_row['bot_win_fastest_length'].' seconds on the '.date('Y-m-d', $accunt_row['bot_win_fastest_time']).'.</p>';
} else {
    echo '<p>You did not win over the bot yet.</p>';
} ?>


<h2>30 Latest Games Played</h2>

<?php
$scoring_ignored_reasons = scoringIgnoredReasons();

$result = $pdo->run("SELECT * FROM games_logging WHERE `user_won` = ".$accunt_row['id']." OR `user_lost` = ".$accunt_row['id']." AND user_lost > 0 ORDER BY `timestamp` DESC, id DESC LIMIT 30;")->fetchAll();
foreach($result as $row) {
    $a['id'] = $row['id'];
    $a['date'] = date('Y-m-d', $row['timestamp']);
    $a['delete'] = '';
    if ($row['length'] > 0) {
        $a['length'] = gmdate("i:s", $row['length']);
    } else {
        $a['length'] = 'Offline';
    }
    if ($row['user_won'] == $accunt_row['id']) {
        if ($row['ignore_scoring'] > 0) {
            $a['status'] = '<span title="'.$scoring_ignored_reasons[$row['ignore_scoring']]['desc'].'">(won)</span>';
        } else {
            $a['status'] = '<span style="color:green">won</span>';
            if ( date( 'Y-m', $row['timestamp'] ) == date( 'Y-m' ) ) {
                $a['delete'] = ' (<a onclick="return confirm(\'Are you sure you want to remove your win against USERNAME on the '.$a['date'].'?\')" href="/account/?delete_game='.$a['id'].'">delete</a>)';
            }
        }
        $a['versus'] = $row['user_lost'];
        $users[] = $row['user_lost'];
    } else {
        if ($row['ignore_scoring'] > 0) {
            $a['status'] = '<span title="'.$scoring_ignored_reasons[$row['ignore_scoring']]['desc'].'">(lost)</span>';
        } else {
            $a['status'] = '<span style="color:red">lost</span>';
        }
        $a['versus'] = $row['user_won'];
        $users[] = $row['user_won'];
    }
    $array[] = $a;
}

if (isset($array)) {
    echo '<table>';
    echo '<tr><th>Date</th><th>Status</th><th>Versus</th><th>Length</th></tr>';

    $result = $pdo->run("SELECT * FROM users WHERE `id` IN (".implode(",", array_unique($users)).");")->fetchAll();
    foreach($result as $row) {
        $user[$row['id']] = $row;
    }

    foreach($array as $a) {
        if ( $a['delete'] != '' ) {
            $a['delete'] = str_replace( 'USERNAME', $user[$a['versus']]['username'], $a['delete'] );
        }
        echo '<tr><td>'.$a['date'].'</td><td>'.$a['status'].'</td><td><a href="/users/'.$user[$a['versus']]['username'].'">'.$user[$a['versus']]['username'].'</a> <img loading=lazy src="https://staticjw.com/redistats/images/flags/'.$user[$a['versus']]['country'].'.gif"> '.$user[$a['versus']]['credits_earned'].' '.$a['delete'].'</td><td>'.$a['length'].'</td></tr>';
    }
    echo '</table>';
} else {
    echo '<p>None yet :(</p>';
}
?>



<h2>Deck Building</h2>

<p><a href="/account/deck" class='big-button'>Create your Deck</a></p>


<h2>Log offline play</h2>

<p>Did you win? Then have the looser to login and log the match here for it to be recorded.</p>

<style>
    table.log-offline {border: 2px solid #666;background-color: #1c1c1c;padding: 2px 50px;margin: 20px auto;font-size: 17px;width: 600px;max-width: 90%}
    table.log-offline input {width:150px;}
    @media (max-width: 900px) { /* Mobile */
        table.log-offline {padding: 2px 10px;}
        table.log-offline input {width:100px;}
    }
</style>

<form action='/log-game' method='post'>
    <table cellpadding="10" class="log-offline">
        <tr><td>
    <label>Winner is:</label><br>
    <input type="text" name="username" required minlength="3" maxlength="30" pattern="[a-zA-Z0-9]+" placeholder='Username' value="<?=$a['username'] ?? ''?>" title="Numbers or letters only. Minimum 3 characters.">
    </td><td>
    <label>Date played:</label><br>
    <input type="text" name="date" required length="10" pattern="[0-9-]+" value="<?= date('Y-m-d') ?>" title="Write the date in the format yyyy-mm-dd" style="width:100px;">
    </td><td>
    <label>&nbsp;</label><br>
    <input type="submit" name="log_game" value="Save" style="width:70px;">
    </td></tr>
    </table>
</form>

<p>It is of course also perfectly fine to play casually without logging the matches. Please agree before starting to play if the match should be logged or not.</p>


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

