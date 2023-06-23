<?php


function isLoggedIn() : array
{
    if (!isset($_COOKIE['loggedin'])) return [];

    $cookie = explode(':', $_COOKIE['loggedin']);

    if ($cookie[4] != md5($cookie[0].$cookie[1].$cookie[2].$cookie[3].SECRET_SALT_LOGIN_COOKIE)) {
        // cookie is invalid, ignore it
        return [];
    }

    return [
        'id' => (int) $cookie[0],
        'username' => $cookie[1],
        'country' => $cookie[2],
        'rating' => $cookie[3],
    ];

}

function sendEmail(string $to, string $subject_line, string $message) : bool
{
    $header = "From: The Space War <info@thespacewar.com>\n";
    $header .= "Content-Type: text/plain; charset=utf-8\n";
    $message .= "\n\nBest regards,\nThe Space War\nhttps://thespacewar.com";
    return mail($to, $subject_line, $message, $header);
}


// Written by Jim 2018-05-17. Also supports ipv6
function IpToNumberWithCountry(string $ip) : string
{
    $ip_to_country = strtolower($_SERVER['HTTP_CF_IPCOUNTRY']);
    $md5 = md5($ip);
    return $ip_to_country.":".hexdec(substr($md5, 0, 5));
}

function dieWith404(string $html) : void
{
    header("HTTP/1.1 404 Not Found");
    $title_tag = '404 | TheSpaceWar.com';
    include(ROOT.'view/head.php');
    echo $html;
    include(ROOT.'view/footer.php');
    die();
}

function getCardData() : array
{

    if (apcu_exists('getCardData2')) {
       return apcu_fetch('getCardData2');
    }

    $type = [
        'blue' => 'Spaceship',
        'red' => 'Missile',
        'green' => 'Defense',
        'violet' => 'Duration',
        'orange' => 'Event',
    ];
    $deck = [
        0 => '',
        1 => 'The Terrans',
        2 => 'The Swarm',
        3 => 'United Stars',
    ];

    // Instant speed, but has a delay. Updates has to be commited
    $json = file_get_contents('/var/www/play.thespacewar.com/server/card/rawCardData.cache.json');
    $time_to_cache = 3600*3; // 3 hours

    // Failing for some reason
    if (substr_count($json, '"id":') < 20) {
        // Takes 0.8 seconds to fetch!
        // This is from the original server
        $json = file_get_contents('https://admin.thespacewar.com/services/api/cards?deck=all');
        $time_to_cache = 3600*12; // 12 hours

        $json = json_decode($json);

        foreach ($json as $k => $v) {
            $slug = nameToSlug($v->name);
            $cards_array[$slug] = [
                'id' => (int) $v->id,
                'slug' => $slug,
                'name' => $v->name,
                'cost' => $v->price,
                'type' => $type[$v->type_card],
                'attack' => $v->attack,
                'defense' => $v->defense,
                'copies' => $v->number_copies,
                'text' => $v->detail,
                'artist' => $v->author,
                'deck_id' => $v->deck,
                'deck_name' => $deck[$v->deck],
            ];
        }
        apcu_store('getCardData', $cards_array, $time_to_cache); // 3 hours
        return $cards_array;
    }

    $json = json_decode($json);

    foreach ($json->data->regular as $k => $v) {
        $slug = nameToSlug($v->name);
        $cards_array[$slug] = [
            'id' => (int) $v->id,
            'slug' => $slug,
            'name' => $v->name,
            'cost' => $v->price,
            'type' => $type[$v->type_card],
            'attack' => $v->attack,
            'defense' => $v->defense,
            'copies' => $v->number_copies,
            'text' => $v->detail,
            'artist' => $v->author,
            'deck_id' => $v->deck,
            'deck_name' => $deck[$v->deck],
        ];
    }
    foreach ($json->data->theSwarm as $k => $v) {
        $slug = nameToSlug($v->name);
        $cards_array[$slug] = [
            'id' => (int) $v->id,
            'slug' => $slug,
            'name' => $v->name,
            'cost' => $v->price,
            'type' => $type[$v->type_card],
            'attack' => $v->attack,
            'defense' => $v->defense,
            'copies' => $v->number_copies,
            'text' => $v->detail,
            'artist' => $v->author,
            'deck_id' => $v->deck,
            'deck_name' => $deck[$v->deck],
        ];
    }
    foreach ($json->data->unitedStars as $k => $v) {
        $slug = nameToSlug($v->name);
        $cards_array[$slug] = [
            'id' => (int) $v->id,
            'slug' => $slug,
            'name' => $v->name,
            'cost' => $v->price,
            'type' => $type[$v->type_card],
            'attack' => $v->attack,
            'defense' => $v->defense,
            'copies' => $v->number_copies,
            'text' => $v->detail,
            'artist' => $v->author,
            'deck_id' => $v->deck,
            'deck_name' => $deck[$v->deck],
        ];
    }


    apcu_store('getCardData2', $cards_array, 3600*3); // 3 hours
    return $cards_array;
}


function displayCard(string $slug) : string
{
    global $logged_in;

    $a = getCardData()[$slug] ?? [];

    if ( !isset( $a['name'] ) ) {
        dieWith404('<p>This page does not exist</p>');
    }

    $title_tag = $a['name'].' | TheSpaceWar.com';
    require(ROOT.'view/head.php');

    $return = '<h1>'.$a['name'].'</h1>';
    $return .= '<img src="https://images.thespacewar.com/card-'.$a['id'].'.jpg" class="big">
    <table>
        <tr>
            <th>Name</th>
            <td>'.$a['name'].'</td>
        </tr>
        <tr>
            <th>Cost</th>
            <td>'.$a['cost'].'</td>
        </tr>
        <tr>
            <th>Type</th>
            <td>'.$a['type'].'</td>
        </tr>';
    if ($a['type'] != 'Event' && $a['type'] != 'Duration') {
        $return .= '
        <tr>
            <th>Attack</th>
            <td>'.$a['attack'].'</td>
        </tr>
        <tr>
            <th>Defense</th>
            <td>'.$a['defense'].'</td>
        </tr>';
    }
    $return .= '
        <tr>
            <th>Deck</th>
            <td>'.$a['deck_name'].'</td>
        </tr>
        <tr>
            <th>Copies</th>
            <td>'.$a['copies'].'</td>
        </tr>
        <tr>
            <th>Text</th>
            <td>'.$a['text'].'</td>
        </tr>
        <tr>
            <th>Artist</th>
            <td>'.$a['artist'].'</td>
        </tr>
    </table>';

    $return .= '<div style="clear:both"></div>';

    $nft_first_edition = getNFTFirstEdition();

    if ( !isset($nft_first_edition[$slug] ) ) return $return;
    if ( $nft_first_edition[$slug]['token_id'] === '' ) return $return;
    
    if ( apcu_exists( 'first_edition_owner:'.$slug ) ) {
        $owner = (int) apcu_fetch( 'first_edition_owner:'.$slug );
        $owner_opensea = apcu_fetch( 'first_edition_owner_opensea:'.$slug );
    } else {
        $owner = $nft_first_edition[$slug]['owner'];
        $owner_opensea = $nft_first_edition[$slug]['owner_opensea'];
    }

    if ( $owner == 0 && $owner_opensea === '' ) {
        $nft_info = '<a href="https://opensea.io/assets/0x495f947276749ce646f68ac8c248420045cb7b5e/'.$nft_first_edition[$slug]['token_id'].'" target="_blank">Buy it now</a> (<a href="/first-edition">info</a>)';
    } elseif ( $owner == 0 ) {
        $nft_info = 'Owned by '.$owner_opensea.'<br><a href="https://opensea.io/assets/0x495f947276749ce646f68ac8c248420045cb7b5e/'.$nft_first_edition[$slug]['token_id'].'" target="_blank">Submit offer</a> (<a href="/first-edition">info</a>)';
    } else {
        $pdo = PDOWrap::getInstance();
        $row = $pdo->run("SELECT username, country FROM users WHERE id = ? LIMIT 1", [$owner])->fetch();
        $nft_info = 'Owned by <a href="/users/'.$row['username'].'" style="padding-right:20px;background:url(https://staticjw.com/redistats/images/flags/'.$row['country'].'.gif) no-repeat center right;">'.$row['username'].'</a><br><a href="https://opensea.io/assets/0x495f947276749ce646f68ac8c248420045cb7b5e/'.$nft_first_edition[$slug]['token_id'].'" target="_blank">Submit offer</a> (<a href="/first-edition">info</a>)';
    }

    $return = str_replace('</table>', '<tr><th>NFT</th><td>'.$nft_info.'</td></tr></table>', $return);

    return $return;

}
/* 
Explanation of Score:
It is the same as Win Rate, but with an exception if less than a certain amount of wins has been achieved the score is decreased.
This is for two reasons: a) with few plays the win rate is not certain. b) we want to encourage people to play many times.
Elo and Glick rating system does not encourage play.
Written by Jim Westergren
https://docs.google.com/spreadsheets/d/1sm-vk09IHmVi3d9wlMdgqqwAyhG2AJm9umPYMqzFCTs/edit#gid=0
*/
function calculateRating(int $win_count, int $loss_count) : array
{
    $amount_of_wins_before_using_win_rate = 20;
    $score_multiplier = 100;

    $ret['games_played'] = $win_count+$loss_count;
    if ($win_count == 0) {
        $ret['win_rate'] = 0;
        $ret['rating'] = 0;
    } else {
        $ret['win_rate'] = round(($win_count/$ret['games_played'])*100);
        $ret['rating'] = (int) $ret['win_rate']*(min($win_count,$amount_of_wins_before_using_win_rate)/$amount_of_wins_before_using_win_rate)*$score_multiplier;
    }
    return $ret;
}


/* Not used anymore
function calculateBonus(int $user_id, int $rating, int $bot_win_fastest_length, string $period = 'monthly') : int
{
    $pdo = PDOWrap::getInstance();
    $bonus = 0;
    $result = $pdo->run("SELECT * FROM users WHERE referrer = ?;", [$user_id])->fetchAll();
    foreach($result as $row) {
        // 10% of the rating of all the referrers
        $bonus += (int) round(calculateRating($row[$period.'_win_count'], $row[$period.'_loss_count'])['rating']/10);
    }
    // Bonus cannot be more than 25% of the rating
    $bonus = (int) round(min($bonus, $rating/4));

    if ($bot_win_fastest_length > 0) {
        $bonus += 50;
    }

    return (int) $bonus;

}
*/

function set_cookie(string $name, string $value, int $expires) : void
{
    setcookie($name, $value, [
            'expires' => $expires,
            'path' => '/',
            'domain' => 'thespacewar.com',
            'secure' => true,
            'httponly' => false,
            'samesite' => 'None',
        ]
    );
}

function setLoginCookie(array $a, int $rating) : string
{
    $cookie_value = $a['id'].':'.$a['username'].':'.$a['country'].':'.$rating.':'.md5($a['id'].$a['username'].$a['country'].$rating.SECRET_SALT_LOGIN_COOKIE);
    set_cookie('loggedin', $cookie_value, TIMESTAMP+(3600*24*20));
    return $cookie_value;
}

function winnersArray() : array
{
    return [
        'February 2022' => ['first_username' => 'Jim', 'first_country' => 'se', 'second_username' => 'Alvin', 'second_country' => 'se'],
        'January 2022' => ['first_username' => 'Alvin', 'first_country' => 'se', 'second_username' => 'Jim', 'second_country' => 'se'],
        'Fourth Quarter 2021' => ['first_username' => 'Alvin', 'first_country' => 'se', 'second_username' => 'datar345', 'second_country' => 'bo', 'third_username' => 'Kaah', 'third_country' => 'bo'],
        'December 2021' => ['first_username' => 'datar345', 'first_country' => 'bo'],
        'November 2021' => ['first_username' => 'Alvin', 'first_country' => 'se', 'second_username' => 'Kaah', 'second_country' => 'bo'],
        'Third Quarter 2021' => ['first_username' => 'Alvin', 'first_country' => 'se', 'second_username' => 'Kaah', 'second_country' => 'bo', 'third_username' => 'Jim', 'third_country' => 'se'],
        'September 2021' => ['first_username' => 'Alvin', 'first_country' => 'se', 'second_username' => 'Jim', 'second_country' => 'se', 'third_username' => 'Kaah', 'third_country' => 'bo'],
        'August 2021' => ['first_username' => 'Alvin', 'first_country' => 'se', 'second_username' => 'Jim', 'second_country' => 'se', 'third_username' => 'Galactico', 'third_country' => 'bo'],
        'July 2021' => ['first_username' => 'Alvin', 'first_country' => 'se', 'second_username' => 'Kaah', 'second_country' => 'bo', 'third_username' => 'Jim', 'third_country' => 'se'],
        'Second Quarter 2021' => ['first_username' => 'Alvin', 'first_country' => 'se', 'second_username' => 'Jim', 'second_country' => 'se', 'third_username' => 'Coolfor', 'third_country' => 'ru'],
        'June 2021' => ['first_username' => 'Alvin', 'first_country' => 'se', 'second_username' => 'Jim', 'second_country' => 'se', 'third_username' => 'Coolfor', 'third_country' => 'ru'],
        'May 2021' => ['first_username' => 'Alvin', 'first_country' => 'se', 'second_username' => 'Jim', 'second_country' => 'se'],
        'April 2021' => ['first_username' => 'Jim', 'first_country' => 'se', 'second_username' => 'Alvin', 'second_country' => 'se', 'third_username' => 'Luna', 'third_country' => 'bo'],
        'First Quarter 2021' => ['first_username' => 'Alvin', 'first_country' => 'se', 'second_username' => 'Jim', 'second_country' => 'se'],
        'March 2021' => ['first_username' => 'Alvin', 'first_country' => 'se', 'second_username' => 'Demencia', 'second_country' => 'ar'],
        'February 2021' => ['first_username' => 'Alvin', 'first_country' => 'se', 'second_username' => 'Jim', 'second_country' => 'se'],
        'January 2021' => ['first_username' => 'Jim', 'first_country' => 'se', 'second_username' => 'TWRWMOM', 'second_country' => 'br'],
        'Fourth Quarter 2020' => ['first_username' => 'Jim', 'first_country' => 'se', 'second_username' => 'Alvin', 'second_country' => 'se'],
        'December 2020' => ['first_username' => 'Jim', 'first_country' => 'se', 'second_username' => 'Alvin', 'second_country' => 'se'],
        'November 2020' => ['first_username' => 'Jim', 'first_country' => 'se', 'second_username' => 'Alvin', 'second_country' => 'se'],
        'Third Quarter 2020' => ['first_username' => 'Alvin', 'first_country' => 'se', 'second_username' => 'Jim', 'second_country' => 'se'],
        'August 2020' => ['first_username' => 'Jim', 'first_country' => 'se', 'second_username' => 'MasterCrakux', 'second_country' => 'ar'],
        'July 2020' => ['first_username' => 'Alvin', 'first_country' => 'se', 'second_username' => 'Luna', 'second_country' => 'bo'],
        'Second Quarter 2020' => ['first_username' => 'Alvin', 'first_country' => 'se', 'second_username' => 'Jim', 'second_country' => 'se'],
        'June 2020' => ['first_username' => 'Alvin', 'first_country' => 'se', 'second_username' => 'Jim', 'second_country' => 'se'],
        'May 2020' => ['first_username' => 'Alvin', 'first_country' => 'se', 'second_username' => 'Jim', 'second_country' => 'se'],
        'April 2020' => ['first_username' => 'Alvin', 'first_country' => 'se', 'second_username' => 'Jim', 'second_country' => 'se'],
    ];
}

function winnersArrayByUser(string $user) : array
{
    $a['Jim'][] = ['period' => 'February 2022', 'position' => '🏆'];
    $a['Alvin'][] = ['period' => 'February 2022', 'position' => '🥈'];
    $a['Alvin'][] = ['period' => 'January 2022', 'position' => '🏆'];
    $a['Jim'][] = ['period' => 'January 2022', 'position' => '🥈'];
    $a['Alvin'][] = ['period' => 'Fourth Quarter 2021', 'position' => '🏆'];
    $a['datar345'][] = ['period' => 'Fourth Quarter 2021', 'position' => '🥈'];
    $a['Kaah'][] = ['period' => 'Fourth Quarter 2021', 'position' => '🥉'];
    $a['datar345'][] = ['period' => 'December 2021', 'position' => '🏆'];
    $a['Alvin'][] = ['period' => 'November 2021', 'position' => '🏆'];
    $a['Kaah'][] = ['period' => 'November 2021', 'position' => '🥈'];
    $a['Alvin'][] = ['period' => 'Third Quarter 2021', 'position' => '🏆'];
    $a['Kaah'][] = ['period' => 'Third Quarter 2021', 'position' => '🥈'];
    $a['Jim'][] = ['period' => 'Third Quarter 2021', 'position' => '🥉'];
    $a['Alvin'][] = ['period' => 'September 2021', 'position' => '🏆'];
    $a['Jim'][] = ['period' => 'September 2021', 'position' => '🥈'];
    $a['Kaah'][] = ['period' => 'September 2021', 'position' => '🥉'];
    $a['Alvin'][] = ['period' => 'August 2021', 'position' => '🏆'];
    $a['Jim'][] = ['period' => 'August 2021', 'position' => '🥈'];
    $a['Galactico'][] = ['period' => 'August 2021', 'position' => '🥉'];
    $a['Alvin'][] = ['period' => 'July 2021', 'position' => '🏆'];
    $a['Kaah'][] = ['period' => 'July 2021', 'position' => '🥈'];
    $a['Jim'][] = ['period' => 'July 2021', 'position' => '🥉'];
    $a['Alvin'][] = ['period' => 'Second Quarter 2021', 'position' => '🏆'];
    $a['Jim'][] = ['period' => 'Second Quarter 2021', 'position' => '🥈'];
    $a['Coolfor'][] = ['period' => 'Second Quarter 2021', 'position' => '🥉'];
    $a['Alvin'][] = ['period' => 'June 2021', 'position' => '🏆'];
    $a['Jim'][] = ['period' => 'June 2021', 'position' => '🥈'];
    $a['Coolfor'][] = ['period' => 'June 2021', 'position' => '🥉'];
    $a['Alvin'][] = ['period' => 'May 2021', 'position' => '🏆'];
    $a['Jim'][] = ['period' => 'May 2021', 'position' => '🥈'];
    $a['Jim'][] = ['period' => 'April 2021', 'position' => '🏆'];
    $a['Alvin'][] = ['period' => 'April 2021', 'position' => '🥈'];
    $a['Luna'][] = ['period' => 'April 2021', 'position' => '🥉'];
    $a['Alvin'][] = ['period' => 'First Quarter 2021', 'position' => '🏆'];
    $a['Jim'][] = ['period' => 'First Quarter 2021', 'position' => '🥈'];
    $a['Alvin'][] = ['period' => 'March 2021', 'position' => '🏆'];
    $a['Demencia'][] = ['period' => 'March 2021', 'position' => '🥈'];
    $a['Alvin'][] = ['period' => 'February 2021', 'position' => '🏆'];
    $a['Jim'][] = ['period' => 'February 2021', 'position' => '🥈'];
    $a['Jim'][] = ['period' => 'January 2021', 'position' => '🏆'];
    $a['TWRWMOM'][] = ['period' => 'January 2021', 'position' => '🥈'];
    $a['Jim'][] = ['period' => 'Fourth Quarter 2020', 'position' => '🏆'];
    $a['Alvin'][] = ['period' => 'Fourth Quarter 2020', 'position' => '🥈'];
    $a['Jim'][] = ['period' => 'December 2020', 'position' => '🏆'];
    $a['Alvin'][] = ['period' => 'December 2020', 'position' => '🥈'];
    $a['Jim'][] = ['period' => 'November 2020', 'position' => '🏆'];
    $a['Alvin'][] = ['period' => 'November 2020', 'position' => '🥈'];
    $a['Alvin'][] = ['period' => 'Third Quarter 2020', 'position' => '🏆'];
    $a['Jim'][] = ['period' => 'Third Quarter 2020', 'position' => '🥈'];
    $a['Jim'][] = ['period' => 'August 2020', 'position' => '🏆'];
    $a['MasterCrakux'][] = ['period' => 'August 2020', 'position' => '🥈'];
    $a['Alvin'][] = ['period' => 'July 2020', 'position' => '🏆'];
    $a['Luna'][] = ['period' => 'July 2020', 'position' => '🥈'];
    $a['Alvin'][] = ['period' => 'Second Quarter 2020', 'position' => '🏆'];
    $a['Jim'][] = ['period' => 'Second Quarter 2020', 'position' => '🥈'];
    $a['Alvin'][] = ['period' => 'June 2020', 'position' => '🏆'];
    $a['Jim'][] = ['period' => 'June 2020', 'position' => '🥈'];
    $a['Alvin'][] = ['period' => 'May 2020', 'position' => '🏆'];
    $a['Jim'][] = ['period' => 'May 2020', 'position' => '🥈'];
    $a['Alvin'][] = ['period' => 'April 2020', 'position' => '🏆'];
    $a['Jim'][] = ['period' => 'April 2020', 'position' => '🥈'];

    if (isset($a[$user])) return $a[$user];

    return [];
}

function getTournamentArrayByUser( string $user ) : array
{
    $a['Alvin'][] = ['info' => 'Tournament #1<br>September 2021 in Bolivia', 'position' => '🏆', 'award' => 500];
    $a['Kaah'][] = ['info' => 'Tournament #1<br>September 2021 in Bolivia', 'position' => '🥈', 'award' => 250];
    $a['Jim'][] = ['info' => 'Tournament #1<br>September 2021 in Bolivia', 'position' => '🥉', 'award' => 125];

    if (isset($a[$user])) return $a[$user];

    return [];
}

function leaderboardTable(string $period = 'monthly') : string
{
    $pdo = PDOWrap::getInstance();
    $html = '<h3 style="text-align: center;margin-top:45px;margin-bottom:-10px;font-size:25px;">Top 30 - '.ucfirst($period).'</h3>';
    $html .= '<div style="overflow-x:auto;"><table>';
    $html .= '<tr><th>Username</th><th>Wins</th><th>Losses</th><th>Win Rate</th><th>Rating Score</th></tr>';
    $result = $pdo->run("SELECT * FROM users ORDER BY ".$period."_win_count DESC LIMIT 100;")->fetchAll();
    foreach($result as $row) {
        $rating = calculateRating($row[$period.'_win_count'], $row[$period.'_loss_count']);
        $total = $rating['rating'];

        // Skip these
        if ($total <= 0) continue;

        // Method to avoid overwriting
        $rand = rand(1000,9999);
        $key = ($total*1000000)+$rand;
        $a[$key] = '<tr>
        <td style="white-space: nowrap"><a href="/users/'.$row['username'].'">'.$row['username'].'</a> <img src="https://staticjw.com/redistats/images/flags/'.$row['country'].'.gif"></td>
        <td style="text-align:center;">'.$row[$period.'_win_count'].'</td>
        <td style="text-align:center;">'.$row[$period.'_loss_count'].'</td>
        <td style="text-align:center;">'.round($rating['win_rate']).'%</td>
        <td style="text-align:center;"><strong>'.$total.'</strong></td>
        </tr>';
    }

    if (!isset($a)) return '';

    // Sort array by key, total score
    krsort($a);

    foreach ($a as $key => $value) {
        $html .= $value;
    }
    $html .= '</table></div>';

    return $html;
}

function countryArray() : array
{
    // I commented out some small ones
    return [
        'AF' => 'Afghanistan',
        'AX' => 'Åland Islands',
        'AL' => 'Albania',
        'DZ' => 'Algeria',
        'AS' => 'American Samoa',
        'AD' => 'Andorra',
        'AO' => 'Angola',
        'AI' => 'Anguilla',
        'AQ' => 'Antarctica',
        'AG' => 'Antigua and Barbuda',
        'AR' => 'Argentina',
        'AM' => 'Armenia',
        'AW' => 'Aruba',
        'AU' => 'Australia',
        'AT' => 'Austria',
        'AZ' => 'Azerbaijan',
        'BS' => 'Bahamas',
        'BH' => 'Bahrain',
        'BD' => 'Bangladesh',
        'BB' => 'Barbados',
        'BY' => 'Belarus',
        'BE' => 'Belgium',
        'BZ' => 'Belize',
        'BJ' => 'Benin',
        'BM' => 'Bermuda',
        'BT' => 'Bhutan',
        //'BO' => 'Bolivia, Plurinational State of',
        'BO' => 'Bolivia',
        //'BQ' => 'Bonaire, Sint Eustatius and Saba',
        'BQ' => 'Bonaire',
        'BA' => 'Bosnia and Herzegovina',
        'BW' => 'Botswana',
        'BV' => 'Bouvet Island',
        'BR' => 'Brazil',
        //'IO' => 'British Indian Ocean Territory',
        'BN' => 'Brunei Darussalam',
        'BG' => 'Bulgaria',
        'BF' => 'Burkina Faso',
        'BI' => 'Burundi',
        'KH' => 'Cambodia',
        'CM' => 'Cameroon',
        'CA' => 'Canada',
        'CV' => 'Cape Verde',
        'KY' => 'Cayman Islands',
        'CF' => 'Central African Republic',
        'TD' => 'Chad',
        'CL' => 'Chile',
        'CN' => 'China',
        'CX' => 'Christmas Island',
        //'CC' => 'Cocos (Keeling) Islands',
        'CO' => 'Colombia',
        'KM' => 'Comoros',
        'CG' => 'Congo',
        //'CD' => 'Congo, the Democratic Republic of the',
        'CK' => 'Cook Islands',
        'CR' => 'Costa Rica',
        //'CI' => 'Côte d'Ivoire',
        'HR' => 'Croatia',
        'CU' => 'Cuba',
        'CW' => 'Curaçao',
        'CY' => 'Cyprus',
        'CZ' => 'Czech Republic',
        'DK' => 'Denmark',
        'DJ' => 'Djibouti',
        'DM' => 'Dominica',
        'DO' => 'Dominican Republic',
        'EC' => 'Ecuador',
        'EG' => 'Egypt',
        'SV' => 'El Salvador',
        'GQ' => 'Equatorial Guinea',
        'ER' => 'Eritrea',
        'EE' => 'Estonia',
        'ET' => 'Ethiopia',
        //'FK' => 'Falkland Islands (Malvinas)',
        'FO' => 'Faroe Islands',
        'FJ' => 'Fiji',
        'FI' => 'Finland',
        'FR' => 'France',
        //'GF' => 'French Guiana',
        //'PF' => 'French Polynesia',
        //'TF' => 'French Southern Territories',
        'GA' => 'Gabon',
        'GM' => 'Gambia',
        'GE' => 'Georgia',
        'DE' => 'Germany',
        'GH' => 'Ghana',
        'GI' => 'Gibraltar',
        'GR' => 'Greece',
        'GL' => 'Greenland',
        'GD' => 'Grenada',
        'GP' => 'Guadeloupe',
        'GU' => 'Guam',
        'GT' => 'Guatemala',
        'GG' => 'Guernsey',
        'GN' => 'Guinea',
        'GW' => 'Guinea-Bissau',
        'GY' => 'Guyana',
        'HT' => 'Haiti',
        //'HM' => 'Heard Island and McDonald Islands',
        //'VA' => 'Holy See (Vatican City State)',
        'HN' => 'Honduras',
        'HK' => 'Hong Kong',
        'HU' => 'Hungary',
        'IS' => 'Iceland',
        'IN' => 'India',
        'ID' => 'Indonesia',
        //'IR' => 'Iran, Islamic Republic of',
        'IR' => 'Iran',
        'IQ' => 'Iraq',
        'IE' => 'Ireland',
        'IM' => 'Isle of Man',
        'IL' => 'Israel',
        'IT' => 'Italy',
        'JM' => 'Jamaica',
        'JP' => 'Japan',
        'JE' => 'Jersey',
        'JO' => 'Jordan',
        'KZ' => 'Kazakhstan',
        'KE' => 'Kenya',
        'KI' => 'Kiribati',
        //'KP' => "Korea, Democratic People's Republic of",
        'KP' => "Korea (North)",
        //'KR' => 'Korea, Republic of',
        'KR' => 'Korea (South)',
        'KW' => 'Kuwait',
        'KG' => 'Kyrgyzstan',
        //'LA' => "Lao People's Democratic Republic",
        'LV' => 'Latvia',
        'LB' => 'Lebanon',
        'LS' => 'Lesotho',
        'LR' => 'Liberia',
        'LY' => 'Libya',
        'LI' => 'Liechtenstein',
        'LT' => 'Lithuania',
        'LU' => 'Luxembourg',
        'MO' => 'Macao',
        //'MK' => 'Macedonia, the former Yugoslav Republic of',
        'MG' => 'Madagascar',
        'MW' => 'Malawi',
        'MY' => 'Malaysia',
        'MV' => 'Maldives',
        'ML' => 'Mali',
        'MT' => 'Malta',
        'MH' => 'Marshall Islands',
        'MQ' => 'Martinique',
        'MR' => 'Mauritania',
        'MU' => 'Mauritius',
        'YT' => 'Mayotte',
        'MX' => 'Mexico',
        //'FM' => 'Micronesia, Federated States of',
        //'MD' => 'Moldova, Republic of',
        'MD' => 'Moldova',
        'MC' => 'Monaco',
        'MN' => 'Mongolia',
        'ME' => 'Montenegro',
        'MS' => 'Montserrat',
        'MA' => 'Morocco',
        'MZ' => 'Mozambique',
        'MM' => 'Myanmar',
        'NA' => 'Namibia',
        'NR' => 'Nauru',
        'NP' => 'Nepal',
        'NL' => 'Netherlands',
        'NC' => 'New Caledonia',
        'NZ' => 'New Zealand',
        'NI' => 'Nicaragua',
        'NE' => 'Niger',
        'NG' => 'Nigeria',
        'NU' => 'Niue',
        //'NF' => 'Norfolk Island',
        //'MP' => 'Northern Mariana Islands',
        'NO' => 'Norway',
        'OM' => 'Oman',
        'PK' => 'Pakistan',
        'PW' => 'Palau',
        //'PS' => 'Palestinian Territory, Occupied',
        'PA' => 'Panama',
        'PG' => 'Papua New Guinea',
        'PY' => 'Paraguay',
        'PE' => 'Peru',
        'PH' => 'Philippines',
        'PN' => 'Pitcairn',
        'PL' => 'Poland',
        'PT' => 'Portugal',
        'PR' => 'Puerto Rico',
        'QA' => 'Qatar',
        'RE' => 'Réunion',
        'RO' => 'Romania',
        //'RU' => 'Russian Federation',
        'RU' => 'Russia',
        'RW' => 'Rwanda',
        //'BL' => 'Saint Barthélemy',
        //'SH' => 'Saint Helena, Ascension and Tristan da Cunha',
        //'KN' => 'Saint Kitts and Nevis',
        //'LC' => 'Saint Lucia',
        //'MF' => 'Saint Martin (French part)',
        //'PM' => 'Saint Pierre and Miquelon',
        //'VC' => 'Saint Vincent and the Grenadines',
        'WS' => 'Samoa',
        'SM' => 'San Marino',
        'ST' => 'Sao Tome and Principe',
        'SA' => 'Saudi Arabia',
        'SN' => 'Senegal',
        'RS' => 'Serbia',
        'SC' => 'Seychelles',
        'SL' => 'Sierra Leone',
        'SG' => 'Singapore',
        //'SX' => 'Sint Maarten (Dutch part)',
        'SK' => 'Slovakia',
        'SI' => 'Slovenia',
        //'SB' => 'Solomon Islands',
        'SO' => 'Somalia',
        'ZA' => 'South Africa',
        //'GS' => 'South Georgia and the South Sandwich Islands',
        'SS' => 'South Sudan',
        'ES' => 'Spain',
        'LK' => 'Sri Lanka',
        'SD' => 'Sudan',
        'SR' => 'Suriname',
        'SJ' => 'Svalbard and Jan Mayen',
        'SZ' => 'Swaziland',
        'SE' => 'Sweden',
        'CH' => 'Switzerland',
        'SY' => 'Syrian Arab Republic',
        'TW' => 'Taiwan, Province of China',
        'TJ' => 'Tajikistan',
        //'TZ' => 'Tanzania, United Republic of',
        'TZ' => 'Tanzania',
        'TH' => 'Thailand',
        'TL' => 'Timor-Leste',
        'TG' => 'Togo',
        'TK' => 'Tokelau',
        'TO' => 'Tonga',
        'TT' => 'Trinidad and Tobago',
        'TN' => 'Tunisia',
        'TR' => 'Turkey',
        'TM' => 'Turkmenistan',
        'TC' => 'Turks and Caicos Islands',
        'TV' => 'Tuvalu',
        'UG' => 'Uganda',
        'UA' => 'Ukraine',
        'AE' => 'United Arab Emirates',
        'GB' => 'United Kingdom',
        'US' => 'United States',
        //'UM' => 'United States Minor Outlying Islands',
        'UY' => 'Uruguay',
        'UZ' => 'Uzbekistan',
        'VU' => 'Vanuatu',
        //'VE' => 'Venezuela, Bolivarian Republic of',
        'VE' => 'Venezuela',
        'VN' => 'Viet Nam',
        //'VG' => 'Virgin Islands, British',
        //'VI' => 'Virgin Islands, U.S.',
        'WF' => 'Wallis and Futuna',
        'EH' => 'Western Sahara',
        'YE' => 'Yemen',
        'ZM' => 'Zambia',
        'ZW' => 'Zimbabwe',
        'XX' => 'Planet Earth',
    ];

}


function commanderData() : array
{
    return [
        'liana-henders' => [
            'id' => 0,
            'deck' => 1,
            'name' => 'Liana Henders',
            'title' => 'Solution Finder',
            'rules' => 'Spend 2 actions to draw a card.',
            'lore' => 'Liana is famous for always coming up with solutions and having answers ready for whatever situation that might come up in warfare. She has done extremely well in recent battles and is eager to join and win The Space War.',
        ],
        'frank-johnson' => [
            'id' => 1,
            'deck' => 1,
            'name' => 'Frank Johnson',
            'title' => 'Station Expert',
            'rules' => 'Your maximum amount of station cards is increased from 8 to 10.',
            'lore' => 'Worked as Senior Architect for more than 20 years before being promoted to Commander. Famous for having figured out a secret method to make the space station big and powerful. He is certain that the winner of the war will be determined by the one with the most powerful station.',
        ],
        'keve-bakins' => [
            'id' => 2,
            'deck' => 1,
            'name' => 'Keve Bakins',
            'title' => 'Expert Organizer',
            'rules' => 'You can freely move around your station cards in your action phase.',
            'lore' => '"What matters in a war is not strength and power but to be able to swiftly reorganize the space station resources and quickly adapt to situations."',
        ],
        'nicia-satu' => [
            'id' => 3,
            'deck' => 1,
            'name' => 'Nicia Satu',
            'title' => 'Master of Endurance',
            'rules' => 'Begin the game with any green defense card in play.<br>You do not receive 3 damage if your draw pile is empty in your draw phase.',
            'lore' => '"We are determined to keep fighting this war until the very end, we will never give up. We will fight until our enemy is fully exhausted, and then we will keep fighting strong as long as it takes. And I do not fear The Miller."',
        ],
        'general-jackson' => [
            'id' => 4,
            'deck' => 1,
            'name' => 'General Jackson',
            'title' => 'Strict Commander',
            'rules' => 'You may take 1 damage to your station (opponent flips one of your station cards) to receive 2 extra action points, in your action phase.',
            'lore' => 'Known for getting production targets done on time with any means necessary. According to him nothing is impossible, and he does not accept failure. He is confident that he will win the war. Accused of abuse by several members of his staff, investigation is pending.',
        ],
        'dr-stein' => [
            'id' => 5,
            'deck' => 3,
            'name' => 'Dr. Stein',
            'title' => 'Expert Planner',
            'rules' => '<ul style="text-align:left;"><li style="margin-bottom:10px;">You can discard a card to draw a card up to 3 times in your action phase.</li><li>You may take a damage to your station to draw a card.</li></ul>',
            'lore' => 'Dr. Stein has with his extreme intelligence the mind and focus to figure out exactly how to win the war. Once he has a strategy in mind he does not care about the sacrifices required to carry out The True Plan.',
        ],
        'the-miller' => [
            'id' => 6,
            'deck' => 1,
            'name' => 'The Miller',
            'title' => 'Artificial Intelligence',
            'rules' => 'At any time instead of drawing a card you can force the opponent to discard the top 2 cards from their draw pile.',
            'lore' => 'The Miller is feared across the galaxy. This robot with artificial intelligence has figured out and built advanced technology to cause considerable drain and damage to any space station in a way that nobody can comprehend. How he got his alias is unknown.',
        ],
        'zuuls' => [
            'id' => 7,
            'deck' => 2,
            'name' => 'Zuuls',
            'title' => 'Swarm Specialist',
            'rules' => 'In your first action phase in the game:<br>Search your draw pile for 3 Drone cards and put them into play.',
            'lore' => 'There is a commander known as Zuuls. According to the history book he has only been to war one time, and that one time was the shortest war ever fought.',
        ],
        'crakux' => [
            'id' => 8,
            'deck' => 2,
            'name' => 'Crakux',
            'title' => 'Extreme Anger',
            'rules' => 'Your Drones have +1 attack.',
            'lore' => 'Crakux, the leader of the Zoleons, has sworn together with his Drones to hunt down and destroy every single one of his enemies. As everybody probably knows by now, the home planet of the Zoleons was recently destroyed in the last war, a tragedy.',
        ],
        'naalox' => [
            'id' => 9,
            'deck' => 2,
            'name' => 'Naalox',
            'title' => 'Regeneration',
            'rules' => 'Use 2 actions to either put into play a Drone from your discard pile or heal 1 station damage. This ability can be used max 2 times per turn.',
            'lore' => 'According to the rumor Naalox can bring back things from the dead. Many have attempted to go to war against him but none has ever managed to destroy his fleet and station.',
        ],
        'staux' => [
            'id' => 10,
            'deck' => 2,
            'name' => 'Staux',
            'title' => 'Acid Master',
            'rules' => 'Spend 2 actions to find an Acid Projectile from anywhere and put it into play.',
            'lore' => 'Known simply as Staux. According to a mission briefing from the only known survivor that has seen him: "I saw him with my own eyes. The only thing I can say if you see him is: abort your mission and run!"',
        ],
        'capt-shera-kinson' => [
            'id' => 11,
            'deck' => 3,
            'name' => 'Capt. Shera Kinson',
            'title' => 'Always Ready',
            'rules' => 'Spend 10 actions to find Starship from your draw pile and put it into play.',
            'lore' => '"I am fully prepared and ready to go out in this war at any moment."',
        ],
        'capt-wayne-mccarter' => [
            'id' => 12,
            'deck' => 3,
            'name' => 'Capt. Wayne McCarter',
            'title' => 'Combat Specialist',
            'rules' => 'Your Starship is not slow.<br>When attacking with the Starship, make an additional attack with 1 damage after the ordinary attack.',
            'lore' => '"I am looking forward to going out in this war and I will completely destroy any enemies spotted on my radar."',
        ],
        'zyre' => [
            'id' => 13,
            'deck' => 3,
            'name' => 'Zyre',
            'title' => 'Energy Expert',
            'rules' => 'You receive 3 actions for each station card in the second row (instead of 2). Your maximum amount of station cards is 7 (instead of 8).',
            'lore' => '"With the amount of energy that I control there is no way I will loose this war."',
        ],
    ];

}

function scoringIgnoredReasons() : array
{
    return [
        1 => ['active' => false, 'desc' => 'Winner already won twice same day against same opponent.'],
        2 => ['active' => false, 'desc' => 'Winner already had more than 2000 monthly rating score compared to the opponent.'],
        3 => ['active' => true, 'desc' => 'The online game lasted less than 60 seconds.'],
        4 => ['active' => true, 'desc' => 'Winner already had more than 3000 monthly rating score compared to the opponent.'],
        5 => ['active' => true, 'desc' => 'Winner already won 3 times same day against same opponent.'],
    ];
}


function nameToSlug(string $name) : string
{
    $slug = strtolower($name);

    // Lazy hack
    if ($slug === 'déjà vu') return 'deja-vu';

    $slug = str_replace(' ', '-', $slug);

    return $slug;
}

function cardImage(string $slug) : string
{
    if ( substr( $slug, 0, 10 ) === 'commander-' ) {
        $slug = substr( $slug, 10 );
        $a = commanderData()[$slug] ?? [];
        if ( $a === [] ) return '';
        return '<a href="/commanders/'.$slug.'"><img src="https://images.thespacewar.com/commander-'.$a['id'].'.png"></a>';
    }

    $a = getCardData()[$slug] ?? [];

    if ( $a === [] ) return '';

    return '<a href="/cards/'.$slug.'"><img src="https://images.thespacewar.com/card-'.$a['id'].'.jpg" alt="Card: '.$a['name'].'"></a>';

}

function getRandomCard() : array
{
    $normal_cards = getCardData();
    foreach ($normal_cards as $normal_card) {
        // 3 preset decks, each having 60 cards = 180 cards.
        for ($i=0; $i < $normal_card['copies']; $i++) { 
            $new_array[] = $normal_card;
        }
    }

    $commander_cards = commanderData();
    foreach ($commander_cards as $commander_card) {
        // For the sake of not having colissions, we make those IDs start at 10000
        $commander_card['id'] = $commander_card['id'] + 10000;
        $new_array[] = $commander_card;
    }

    $random_card = array_rand($new_array, 1);

    return $new_array[$random_card];
}

function purchaseCard( int $user_id, int $frame_type, int $card_id ) : void
{
    global $pdo;

    $row_exist = $pdo->run("SELECT id FROM framed_cards WHERE user_id = ? AND card_id = ? AND frame_type = ?", [$user_id, $card_id, $frame_type])->fetch();
    if ( isset( $row_exist['id'] ) ) {
        $pdo->run("UPDATE framed_cards SET amount = amount+1 WHERE id = ?", [$row_exist['id']]);
    } else {
        $pdo->run("INSERT INTO framed_cards (`user_id`, `card_id`, `frame_type`, `amount`) VALUES (?, ?, ?, ?);", [$user_id, $card_id, $frame_type, 1]);
    }
}

function getRareCards() : array
{
    $array = getCardData();

    foreach ($array as $key => $value) {
        if ($value['copies'] == 1) {
            $rare_cards[] = $value['id'];
        }
    }

    // Also the commanders:
    for ($i=9999; $i < 10050; $i++) { 
        $rare_cards[] = $i;
    }

    return $rare_cards;
}


function getCardImageURL( int $card_id ) : string
{
    if ( $card_id >= 10000 ) {
        $card_id = (int) $card_id - 10000;
        return 'https://images.thespacewar.com/commander-'.$card_id.'.png';
    }

    // Else:
    return 'https://images.thespacewar.com/card-'.$card_id.'.jpg';

}


function getWeeklyOffer( int $week_number ) : array
{
    if ( apcu_exists( 'weekly-offer-'.$week_number ) ) {
        $weekly_offer = apcu_fetch( 'weekly-offer-'.$week_number );
    } else {
        $random_card = getRandomCard();
        $random_cost = mt_rand( 15, 40 );
        $weekly_offer = $random_card['id'].'###'.$random_card['name'].'###'.$random_cost;
        apcu_store( 'weekly-offer-'.$week_number, $weekly_offer, 3600*24*7 );
    }

    $weekly_offer = explode( '###', $weekly_offer );

    return [
        'id' => (int) $weekly_offer[0],
        'name' => $weekly_offer[1],
        'cost' => (int) $weekly_offer[2],
    ];

}


function getLatestReferralsTable( int $user_id, int $limit ) : array
{
    $a['amount_of_referrers'] = 0;
    $a['credits_earned_of_referrers'] = 0;
    $a['html_output'] = '';

    $pdo = PDOWrap::getInstance();

    $result = $pdo->run("SELECT username, country, credits_earned, regtime, lastlogintime FROM users WHERE `referrer` = ? ORDER BY regtime DESC LIMIT ".$limit.";", [$user_id])->fetchAll();
    if ( count( $result ) == 0 ) {
        $a['html_output'] .= "<p>None yet.</p>";
        return $a;
    } 

    $a['html_output'] .= '<table><tr><th>Registration Date</th><th>User</th><th>Credits Earned</th><th>Last Logged In</th></tr>';

    foreach($result as $row) {
        $a['html_output'] .= '<tr><td>'.date('Y-m-d', $row['regtime']).'</td><td><a href="/users/'.$row['username'].'">'.$row['username'].'</a> <img src="https://staticjw.com/redistats/images/flags/'.$row['country'].'.gif"></td><td>'.$row['credits_earned'].'</td><td>'.date('Y-m-d', $row['lastlogintime']).'</td></tr>';
        $a['amount_of_referrers']++;
        $a['credits_earned_of_referrers'] += $row['credits_earned'];
    } 

    $a['html_output'] .= "</table>";

    return $a;

}

