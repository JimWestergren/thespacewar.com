<?php

$username = substr(URL, 6);

if (!ctype_alnum($username)) {
    dieWith404('<p>This page does not exist</p>');
}

$pdo = PDOWrap::getInstance();

// We have an option here to only selecy by id and doing 301 to correct URL
// This is in case a user changes username, BUT then scrapers could scrape and looping each id and taking data, I decided to not do that
$row = $pdo->run("SELECT * FROM users WHERE username = ? LIMIT 1", [$username])->fetch();
if (!isset($row['id'])) {
    dieWith404('<p>There is no such user.</p>');
}

$title_tag = 'User '.$row['username'].' | TheSpaceWar.com';
include(ROOT.'view/head.php');

$monhtly = calculateRating($row['monthly_win_count'], $row['monthly_loss_count']);
$quarterly = calculateRating($row['quarterly_win_count'], $row['quarterly_loss_count']);

?>

<p>
    <strong><?=$row['username']?></strong> is representing <img src="https://staticjw.com/redistats/images/flags/<?=$row['country']?>.gif"> <?=countryArray()[strtoupper($row['country'])]?>
    <?php if ($row['credits_earned'] > 0) {
        echo '<br>Total Credits Earned: <strong>'.$row['credits_earned'].'</strong>';
    } ?>
</p>

<table>
    <tr><th>Period</th><th>Wins</th><th>Losses</th><th>Win Rate</th><th>Rating</th></tr>
    <tr><td>This Month</td><td><?=$row['monthly_win_count']?></td><td><?=$row['monthly_loss_count']?></td><td><?=$monhtly['win_rate']?>%</td><td><strong><?=$monhtly['rating']?></strong></td></tr>
    <tr><td>This Quarter</td><td><?=$row['quarterly_win_count']?></td><td><?=$row['quarterly_loss_count']?></td><td><?=$quarterly['win_rate']?>%</td><td><?=$quarterly['rating']?></td></tr>
</table>

<p>Registration time: <?=date('Y-m-d', $row['regtime'])?><br>
Last login time: <?=date('Y-m-d', $row['lastlogintime'])?><br>
Winning over the bot: <?php if ($row['bot_win_fastest_time'] > 0) {echo 'Fastest is <strong>'.$row['bot_win_fastest_length'].'</strong> seconds on the '.date('Y-m-d', $row['bot_win_fastest_time']).'.'; } else {echo 'no';}
if ($row['referrer'] != '') {
    if (is_numeric($row['referrer'])) {
        $a = $pdo->run("SELECT * FROM users WHERE id = ? LIMIT 1", [$row['referrer']])->fetch();
        echo '<br>Referrer: '.$a['username'].' <img src="https://staticjw.com/redistats/images/flags/'.$a['country'].'.gif">';
    } else {
        echo "<br>Referrer: ".$row['referrer'];
    }
}
echo '</p>';

if ($row['credits_earned'] > 1000) {
    $first_edition_nfts = getFirstEditionNFTbyUser((int) $row['id']);
    if ( $first_edition_nfts ) {
        echo '<h2>Owner of the following First Edition NFTs</h2>';
        echo '<div class="cards">';
        foreach ($first_edition_nfts as $card_slug) {
            echo cardImage($card_slug);
        }
        echo '</div>';
        echo '<p>More info: <a href="/first-edition">First Edition Cards as NFT</a>.</p>';
    }
}


$winners_array = winnersArrayByUser($row['username']);
if ($winners_array != []) {
    $output = '';
    foreach ($winners_array as $key => $value) {
        $output .= '<li>'.$value['period'].': '.$value['position'].'</li>';
    }
    echo '<h2>Trophies</h2><ul>'.$output.'</ul>';
}

$html = '';
$result = $pdo->run("SELECT * FROM games_logging, users WHERE `user_won` = ".$row['id']." AND ignore_scoring = 0 AND user_lost > 0 AND users.id = user_lost ORDER BY `timestamp` DESC LIMIT 5;")->fetchAll();
foreach($result as $row2) {
    if ($row2['length'] > 0) {
        $a['length'] = gmdate("i:s", $row2['length']).' minutes';
    } else {
        $a['length'] = 'offline game';
    }
    $html .= '<li>'.date('Y-m-d', $row2['timestamp']).' versus '.$row2['username'].' <img src="https://staticjw.com/redistats/images/flags/'.$row2['country'].'.gif"> ('.$a['length'].')</li>';
}

if ($html != '') {
    echo '<h2>Latest Wins</h2><ul>'.$html.'</ul>';
}


if ($row['twitter'] != '') {
    echo "<p>Twitter: <a href='https://twitter.com/".$row['twitter']."' target='_blank'>@".$row['twitter']."</a></p>";
}

?>

<p style="text-align: center"><a href='/'>← [back]</a></p>
