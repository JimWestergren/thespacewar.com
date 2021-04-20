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

<p style="float: right">[ <a href="/account/edit">Edit Account</a> ]<br>[ <a href="/logout">Logout</a> ]<p>

<p>Logged in as <a href="/users/<?=$accunt_row['username']?>"><strong><?=$accunt_row['username']?></strong></a><br>
Representing: <img src="https://staticjw.com/redistats/images/flags/<?=$accunt_row['country']?>.gif"> <?=countryArray()[strtoupper($accunt_row['country'])]?><br>

<p>Rating Score this month: <strong style="font-size:24px;"><?=$rating?></strong></p>


<h2>Play Online</h2>

<p>First time playing? Please see <a href="/videos" target="_blank">our videos</a> how to play first. It will be much easier for you.</p>

<p><a href="https://play.thespacewar.com/" class='big-button'>Start to play</a><br>
Read this first: alpha testing for desktop and tablets with focus on the browsers Chrome and Firefox, mobile phone does not work good yet. If the game hangs reload the page and please send us a bug report.</p>

<p>Make sure to join our new <a href="https://discord.gg/tv3DXqj" target="_blank">Discord</a> if you have questions, reporting bugs and so forth. Email also works: <a href="mailto:info@thespacewar.com" target="_blank">info@thespacewar.com</a></p>

<!--<p>The winner of the quarterly contest will be awarded $100. See the <a href="https://thespacewar.com/leaderboard">Leaderboard</a>.</p>-->

<!--<p>Please <a href="https://forms.gle/NqFNyWurCAP9GaZCA" target="_blank">take our survey</a>, you are the best :)</p>-->



<h2>Your Referrers</h2>

<p>Your referrer URL: <code>https://thespacewar.com/?referrer=<?=$accunt_row['id']?></code></p>


<h3>30 Latest Referrers:</h3>

<p>
<?php
$amount_of_referrers = 0;
$credits_earned_of_referrers = 0;

$result = $pdo->run("SELECT * FROM users WHERE `referrer` = ?;", [$accunt_row['id']])->fetchAll();
if (count($result) > 0) {
    foreach($result as $row) {
        echo '<a href="/users/'.$row['username'].'">'.$row['username'].'</a> <img src="https://staticjw.com/redistats/images/flags/'.$row['country'].'.gif"> | Credits Earned: '.$row['credits_earned'].'<br>';
        $amount_of_referrers++;
        $credits_earned_of_referrers += $row['credits_earned'];
    } 
} else {
    echo "<p>None yet.";
}
?>
</p>


<?php

$total_credits = 0;

echo "<h2>📈 Your Credits (BETA)</h2>";

echo '<table>';

if ($accunt_row['id'] < 5000) {
    $total_credits += 200;
    echo '<tr><td>200 credits for being one of the first 5 000 to register account.</td><td>200</td></tr>';
}


$days_registered = round((TIMESTAMP-$accunt_row['regtime'])/(3600*24));
$total_credits += $days_registered;
echo '<tr><td>1 credit for each day you have been registered.</td><td>'.$days_registered.'</td></tr>';



echo '<tr><td>5 credits for verifying your email.</td><td>';
if ($accunt_row['email_status'] > 1) {
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
echo 10*$amount_of_referrers;
$total_credits += (10*$amount_of_referrers);
echo '</td></tr>';


echo '<tr><td>10% of all credits earned from your referrals.</td><td>';
echo round( ( $credits_earned_of_referrers/10 ) );
$total_credits += round( ( $credits_earned_of_referrers/10 ) );
echo '</td></tr>';



$quarterly_gold_medals = 0;
$quarterly_silver_medals = 0;
$monthly_gold_medals = 0;
$monthly_silver_medals = 0;
$winners_array = winnersArrayByUser($accunt_row['username']);
if ($winners_array != []) {
    foreach ($winners_array as $key => $value) {
        if (strpos($value['period'], 'Quarter')) {
            if ($value['position'] == '🏆') {
                $quarterly_gold_medals++;
            } elseif ($value['position'] == '🥈') {
                $quarterly_silver_medals++;
            }
        } else {
            if ($value['position'] == '🏆') {
                $monthly_gold_medals++;
            } elseif ($value['position'] == '🥈') {
                $monthly_silver_medals++;
            }
        } 
    }
}

echo '<tr><td>50 credits for each monthly silver medal.</td><td>';
echo 50*$monthly_silver_medals;
$total_credits += (50*$monthly_silver_medals);
echo '</td></tr>';

echo '<tr><td>100 credits for each monthly gold medal.</td><td>';
echo 100*$monthly_gold_medals;
$total_credits += (100*$monthly_gold_medals);
echo '</td></tr>';

echo '<tr><td>300 credits for each quarterly silver medal.</td><td>';
echo 300*$quarterly_silver_medals;
$total_credits += (300*$quarterly_silver_medals);
echo '</td></tr>';

echo '<tr><td>600 credits for each quarterly gold medal.</td><td>';
echo 600*$quarterly_gold_medals;
$total_credits += (600*$quarterly_gold_medals);
echo '</td></tr>';

echo '<tr><td>1000 credits for each <a href="/first-edition">First Edition NFT</a> you own.</td><td>';
$amount_of_nft = count(getFirstEditionNFTbyUser($logged_in['id']));
echo 1000*$amount_of_nft;
$total_credits += (1000*$amount_of_nft);
echo '</td></tr>';




echo '<tr><td style="text-align:right">Total Saved:</td><td>';
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

$result = $pdo->run("SELECT * FROM games_logging WHERE `user_won` = ".$accunt_row['id']." OR `user_lost` = ".$accunt_row['id']." AND user_lost > 0 ORDER BY `timestamp` DESC LIMIT 30;")->fetchAll();
foreach($result as $row) {
    $a['id'] = $row['id'];
    $a['date'] = date('Y-m-d', $row['timestamp']);
    if ($row['length'] > 0) {
        $a['length'] = gmdate("i:s", $row['length']);
    } else {
        $a['length'] = 'Offline';
    }
    if ($row['user_won'] == $accunt_row['id']) {
        if ($row['ignore_scoring'] > 0) {
            $a['status'] = '<span title="'.$scoring_ignored_reasons[$row['ignore_scoring']].'">(won)</span>';
        } else {
            $a['status'] = '<span style="color:green">won</span>';
        }
        $a['versus'] = $row['user_lost'];
        $users[] = $row['user_lost'];
    } else {
        if ($row['ignore_scoring'] > 0) {
            $a['status'] = '<span title="'.$scoring_ignored_reasons[$row['ignore_scoring']].'">(lost)</span>';
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
        echo '<tr><td>'.$a['date'].'</td><td>'.$a['status'].'</td><td><a href="/users/'.$user[$a['versus']]['username'].'">'.$user[$a['versus']]['username'].'</a> <img src="https://staticjw.com/redistats/images/flags/'.$user[$a['versus']]['country'].'.gif"> '.$user[$a['versus']]['credits_earned'].'</td><td>'.$a['length'].'</td></tr>';
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

