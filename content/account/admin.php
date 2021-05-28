<?php
if ($logged_in === [] || $logged_in['id'] != 1) {
    require(ROOT.'view/login.php');
    require(ROOT.'view/footer.php');
    die();
}

$pdo = PDOWrap::getInstance();

$title_tag = 'Admin | TheSpaceWar.com';
require(ROOT.'view/head.php');


//$pdo->run("ALTER TABLE users ADD credits_earned INT(10) UNSIGNED NOT NULL DEFAULT 0 AFTER newsletter;");

/*
$pdo->run("DELETE FROM games_logging WHERE id = 131;");
$pdo->run("UPDATE users SET monthly_win_count = monthly_win_count-1, quarterly_win_count = quarterly_win_count-1 WHERE username = ?;", ['Luna']);
$pdo->run("UPDATE users SET monthly_loss_count = monthly_loss_count-1, quarterly_loss_count = quarterly_loss_count-1 WHERE username = ?;", ['Alvin']);
*/

/*
// MONTHLY
$pdo->run("UPDATE users SET monthly_win_count = 0;");
$pdo->run("UPDATE users SET monthly_loss_count = 0;");

// QUARTERLY
$pdo->run("UPDATE users SET quarterly_win_count = 0;");
$pdo->run("UPDATE users SET quarterly_loss_count = 0;");



$pdo->run("CREATE TABLE decks (
id INT(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
user_id INT(10) UNSIGNED NOT NULL DEFAULT 0,
deck_name VARCHAR(255) NOT NULL DEFAULT '',
commander SMALLINT(5) NOT NULL DEFAULT 0,
card_count SMALLINT(5) NOT NULL DEFAULT 0,
time_saved INT(10) UNSIGNED NOT NULL DEFAULT 0,
time_used INT(10) UNSIGNED NOT NULL DEFAULT 0,
cards VARCHAR(5000) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;");


//$pdo->run("ALTER TABLE users ADD twitter VARCHAR(255) NOT NULL DEFAULT '' AFTER newsletter;");



//$pdo->run("UPDATE users SET referrer = '' WHERE username = 'Alvin';");
/*




// Script to rebuild quarterly leaderboard
$result = $pdo->run("SELECT * FROM games_logging WHERE `timestamp` > 1593586800;")->fetchAll();
foreach($result as $row) {

    $pdo->run("UPDATE users SET quarterly_win_count = quarterly_win_count+1 WHERE id = ?;", [$row['user_won']]);
    $pdo->run("UPDATE users SET quarterly_loss_count = quarterly_loss_count+1 WHERE id = ?;", [$row['user_lost']]);
}

$pdo->run("UPDATE users SET monthly_win_count = 3 WHERE id = 2;");
$pdo->run("UPDATE users SET monthly_loss_count = 3 WHERE id = 1;");


$pdo->run("ALTER TABLE users ADD bot_win_fastest_time INT(10) UNSIGNED NOT NULL DEFAULT 0 AFTER quarterly_loss_count;");
$pdo->run("ALTER TABLE users ADD bot_win_fastest_length INT(10) UNSIGNED NOT NULL DEFAULT 0 AFTER bot_win_fastest_time;");
*/

// Show info for updating the getNFTFirstEdition array:
getUpdateInfoNFTFirstEdition();

$thirty_days_ago = TIMESTAMP-(3600*24*30);

?>
<style>table {font-size: 16px}</style>

<h1>Admin</h1>

<p>Active users: <strong><?= $pdo->run("SELECT count(id) as count FROM users WHERE lastlogintime > ".$thirty_days_ago)->fetch()['count'] ?></strong> (logged in latest 30 days)</p>

<p>How many have won over bot : <strong><?= $pdo->run("SELECT count(id) as count FROM users WHERE bot_win_fastest_length > 0")->fetch()['count'] ?></strong></p>



<h2>50 Latest Games Played</h2>

<p>
<?php
$result = $pdo->run("SELECT * FROM games_logging ORDER BY `timestamp` DESC LIMIT 50;")->fetchAll();
foreach($result as $row) {
    $a['date'] = date('Y-m-d', $row['timestamp']);
    if ($row['length'] > 0) {
        $a['length'] = gmdate("i:s", $row['length']).' minutes';
    } else {
        $a['length'] = 'offline';
    }
    $a['status'] = '<span style="color:green">won</span>';
    $a['user_won'] = $row['user_won'];
    $a['user_lost'] = $row['user_lost'];
    $users[] = $row['user_won'];
    $users[] = $row['user_lost'];
    $array[] = $a;
}

if (isset($array)) {
    $result = $pdo->run("SELECT * FROM users WHERE `id` IN (".implode(",", array_unique($users)).");")->fetchAll();
    foreach($result as $row) {
        $user[$row['id']] = $row;
    }

    foreach($array as $a) {
        echo $a['date'].' <a href="/users/'.$user[$a['user_won']]['username'].'">'.$user[$a['user_won']]['username'].'</a> <img src="https://staticjw.com/redistats/images/flags/'.$user[$a['user_won']]['country'].'.gif"> '.$a['status'].' versus <a href="/users/'.$user[$a['user_lost']]['username'].'">'.$user[$a['user_lost']]['username'].'</a> <img src="https://staticjw.com/redistats/images/flags/'.$user[$a['user_lost']]['country'].'.gif"> ('.$a['length'].')<br>';
    }
}
?>
</p>


<h2>50 Latest Logged in Players</h2>

<table cellpadding="7">
    <tr><th>Login</th><th>User</th><th>Referrer</th><th>Quarterly Score</th><th>Newsletter</th><th>Email Status</th></tr>
<?php
$result = $pdo->run("SELECT * FROM users ORDER BY lastlogintime DESC LIMIT 50;")->fetchAll();
$html = '';
foreach($result as $row) {
    echo '<tr><td>'.date('Y-m-d', $row['lastlogintime']).'</td><td class="nobr">'.$row['id'].', '.$row['username'].' <img src="https://staticjw.com/redistats/images/flags/'.$row['country'].'.gif"></td><td>'.$row['referrer'].'</td><td>'.calculateRating($row['quarterly_win_count'], $row['quarterly_loss_count'])['rating'].'</td><td>'.$row['newsletter'].'</td><td>'.$row['email_status'].'</td></tr>';
}
?>
</table>


<h2>100 Latest Registrations</h2>

<table cellpadding="7">
    <tr><th>Reg time</th><th>User</th><th>Referrer</th><th>Winning over bot</th><th>Newsletter</th><th>Email Status</th></tr>
<?php
$result = $pdo->run("SELECT * FROM users ORDER BY regtime DESC LIMIT 100;")->fetchAll();
$html = '';
foreach($result as $row) {
    echo '<tr><td>'.date('Y-m-d', $row['regtime']).'</td><td class="nobr">'.$row['username'].' <img src="https://staticjw.com/redistats/images/flags/'.$row['country'].'.gif"></td><td>'.$row['referrer'].'</td><td>'.$row['bot_win_fastest_length'].'</td><td>'.$row['newsletter'].'</td><td>'.$row['email_status'].'</td></tr>';
}
?>
</table>


<h2>Data for the NFTs:</h2>

<?php

$cards = getNFTFirstEdition();
foreach ($cards as $slug => $value) {

    if ($value['token_id'] != '') continue;

    $nft_code = $slug.':'.$value['nft_id'].':';
    $nft_code .= sha1( $slug.$value['nft_id'].SECRET_SALT_NFT_CODE );
    $simple_hash = substr( md5( $nft_code ), -15 );

    if ( substr( $slug, 0, 10 ) === 'commander-' ) {
        $slug2 = substr( $slug, 10 );
        $card_name = commanderData()[$slug2]['name'] ?? '';
        $card_url = 'https://thespacewar.com/commanders/'.$slug2;
    } else {
        $card_name = getCardData()[$slug]['title'] ?? '';
        $card_url = 'https://thespacewar.com/cards/'.$slug;
    }

    //echo $slug.":<br>img_id: ".$value['img_id']."<br>price: ".$value['price']."<br>nft_code: ".$nft_code."<br>img_filename: ".$slug."-".$simple_hash.".png<br>title: ".$card_name." • Card ".$value['nft_id']." of 102 (Physical Signed Card + NFT)<br><br>";
    echo $card_name." • Card ".$value['nft_id']." of 102 (Physical Signed Card + NFT)<br>".$card_url."<br>".$nft_code."<br>Price: ".$value['price']."<br><br>";
}

