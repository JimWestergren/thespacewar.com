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

    if (apcu_exists('getCardData')) {
       return apcu_fetch('getCardData');
    }

    $json = file_get_contents('https://admin.thespacewar.com/services/api/cards?deck=all');
    //$json = file_get_contents('/var/www/play.thespacewar.com/server/card/rawCardData.cache.json');

    // Failing for some reason
    if (substr_count($json, '"id":') < 20) return false;

    $json = json_decode($json);

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
    foreach ($json as $k => $v) {
        $cards_array[$v->id] = [
            'id' => $v->id,
            'title' => $v->name,
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

    apcu_store('getCardData', $cards_array, 3600*3); // 3 hours
    return $cards_array;
}

function displayCard(int $id) : string
{
    $a = getCardData()[$id];

    $r = '<img src="https://images.thespacewar.com/card-'.$a['id'].'.jpg" class="big">
    <table>
        <tr>
            <th>Title</th>
            <td>'.$a['title'].'</td>
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
        $r .= '
        <tr>
            <th>Attack</th>
            <td>'.$a['attack'].'</td>
        </tr>
        <tr>
            <th>Defense</th>
            <td>'.$a['defense'].'</td>
        </tr>';
    }
    $r .= '
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

    return $r;

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

function setLoginCookie(array $a, int $rating) : string
{
    $cookie_value = $a['id'].':'.$a['username'].':'.$a['country'].':'.$rating.':'.md5($a['id'].$a['username'].$a['country'].$rating.SECRET_SALT_LOGIN_COOKIE);
    setcookie('loggedin', $cookie_value, ['expires' => TIMESTAMP+3600*24*20, 'path' => '/', 'domain' => 'thespacewar.com', 'secure' => true, 'httponly' => false, 'samesite' => 'None']);
    return $cookie_value;
}

function winnersArray() : array
{
    return [
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



function leaderboardTable(string $period = 'monthly') : string
{
    $pdo = PDOWrap::getInstance();
    $html = '<h2 style="text-align: center">Top 30 - '.ucfirst($period).'</h2>';
    $html .= '<div style="overflow-x:auto;"><table cellpadding="9">';
    $html .= '<tr><th>Username</th><th>Wins</th><th>Losses</th><th>Win Rate</th><th>Rating</th><th>Total</th></tr>';
    $result = $pdo->run("SELECT * FROM users ORDER BY ".$period."_win_count DESC LIMIT 100;")->fetchAll();
    foreach($result as $row) {
        $rating = calculateRating($row[$period.'_win_count'], $row[$period.'_loss_count']);
        $bonus = calculateBonus((int) $row['id'], $rating['rating'], (int) $row['bot_win_fastest_length'], $period);
        $total = $rating['rating']+$bonus;

        // Skip these
        if ($total <= 50) continue;

        // Method to avoid overwriting
        $rand = rand(1000,9999);
        $key = ($total*1000000)+$rand;
        $a[$key] = '<tr>
        <td style="white-space: nowrap"><a href="/users/'.$row['username'].'">'.$row['username'].'</a> <img src="https://staticjw.com/redistats/images/flags/'.$row['country'].'.gif"></td>
        <td style="text-align:center;">'.$row[$period.'_win_count'].'</td>
        <td style="text-align:center;">'.$row[$period.'_loss_count'].'</td>
        <td style="text-align:center;">'.round($rating['win_rate']).'%</td>
        <td style="text-align:center;">'.$rating['rating'].'</td>
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
        'frank-johnson' => [
            'id' => 1,
            'name' => 'Frank Johnson',
            'title' => 'Station Expert',
            'rules' => 'Your maximum amount of station cards is increased from 8 to 10.',
            'lore' => 'Worked as Senior Architect for more than 20 years before being promoted to Commander. Famous for having figured out a secret method to make the space station big and powerful. He is certain that the winner of the war will be determined by the one with the most powerful station.',
        ],
        'keve-bakins' => [
            'id' => 2,
            'name' => 'Keve Bakins',
            'title' => 'Expert Organizer',
            'rules' => 'You can freely move around your station cards in your action phase.',
            'lore' => '"What matters in a war is not strength and power but to be able to swiftly reorganize the space station resources and quickly adapt to situations."',
        ],
        'nicia-satu' => [
            'id' => 3,
            'name' => 'Nicia Satu',
            'title' => 'Master of Endurance',
            'rules' => 'Begin the game with any shield in play.<br>You do not receive 3 damage if your draw pile is empty in your draw phase.',
            'lore' => '"We are determined to keep fighting this war until the very end, we will never give up. We will fight until our enemy is fully exhausted, and then we will keep fighting strong as long as it takes. And I do not fear The Miller."',
        ],
        'general-jackson' => [
            'id' => 4,
            'name' => 'General Jackson',
            'title' => 'Strict Commander',
            'rules' => 'You may take 1 damage to your station (opponent flips one of your station cards) to receive 2 extra action points, in your action phase.',
            'lore' => 'Known for getting production targets done on time with any means necessary. According to him nothing is impossible, and he does not accept failure. He is confident that he will win the war. Accused of abuse by several members of his staff, investigation is pending.',
        ],
        'dr-stein' => [
            'id' => 5,
            'name' => 'Dr. Stein',
            'title' => 'Expert Planner',
            'rules' => '<ul style="text-align:left;"><li style="margin-bottom:10px;">You can discard a card to draw a card up to 3 times in your action phase.</li><li>You may take 2 damage to your station to perform a Perfect Plan (take a card from anywhere to your hand).</li></ul>',
            'lore' => 'Dr. Stein has with his extreme intelligence the mind and focus to figure out exactly how to win the war. Once he has a strategy in mind he does not care about the sacrifices required to carry out The True Plan.',
        ],
        'the-miller' => [
            'id' => 6,
            'name' => 'The Miller',
            'title' => 'Artificial Intelligence',
            'rules' => 'At any time instead of drawing a card you can force the opponent to discard the top 2 cards from their draw pile.',
            'lore' => 'The Miller is feared across the galaxy. This robot with artificial intelligence has figured out and built advanced technology to cause considerable drain and damage to any space station in a way that nobody can comprehend. How he got his alias is unknown.',
        ],
        'zuuls' => [
            'id' => 7,
            'name' => 'Zuuls',
            'title' => 'Swarm Specialist',
            'rules' => 'In your first action phase in the game:<br>Search your draw pile for 3 Drone cards and place them in your home zone.',
            'lore' => 'There is a commander known as Zuuls. According to the history book he has only been to war one time, and that one time was the shortest war ever fought.',
        ],
        'crakux' => [
            'id' => 8,
            'name' => 'Crakux',
            'title' => 'Extreme Anger',
            'rules' => 'Your Drones have +1 attack.',
            'lore' => 'Crakux, the leader of the Zoleons, has sworn together with his Drones to hunt down and destroy every single one of his enemies. As everybody probably knows by now, the home planet of the Zoleons was recently destroyed in the last war, a tragedy.',
        ],
        'naalox' => [
            'id' => 9,
            'name' => 'Naalox',
            'title' => 'Regeneration',
            'rules' => 'Use 2 actions to either bring back a Drone from your discard to your home zone or heal 1 station damage. This ability can be used max 2 times per turn.',
            'lore' => 'According to the rumor Naalox can bring back things from the dead. Many have attempted to go to war against him but none has ever managed to destroy his fleet and station.',
        ],
        'staux' => [
            'id' => 10,
            'name' => 'Staux',
            'title' => 'Acid Master',
            'rules' => 'Spend 2 actions to find an Acid Projectile from anywhere and place it in your home zone.',
            'lore' => 'Known simply as Staux. According to a mission briefing from the only known survivor that has seen him: "I saw him with my own eyes. The only thing I can say if you see him is: abort your mission and run!"',
        ],
        'capt-shera-kinson' => [
            'id' => 11,
            'name' => 'Capt. Shera Kinson',
            'title' => 'Always Ready',
            'rules' => 'Use 3 actions to take the Starship card from anywhere to your hand.',
            'lore' => '"I am fully prepared and ready to go out in this war at any moment."',
        ],
        'capt-wayne-mccarter' => [
            'id' => 12,
            'name' => 'Capt. Wayne McCarter',
            'title' => 'Combat Specialist',
            'rules' => 'Your Starship is not slow.<br>When attacking with the Starship, make an additional attack with 1 damage after the ordinary attack.',
            'lore' => '"I am looking forward to going out in this war and I will completely destroy any enemies spotted on my radar."',
        ],
    ];

}
