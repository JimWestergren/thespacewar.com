<?php
$title_tag = 'Popular Cards | TheSpaceWar.com';
require(ROOT.'view/head.php');

?>

<h1>Popular Cards</h1>

<?php

$pdo = PDOWrap::getInstance();


$cards_data = getCardData();
foreach ( $cards_data as $key => $value ) {
    $value['count'] = 0;
    $value['decks_count'] = 0;
    $cards_count[$value['id']] = $value;
}


$commander_data = commanderData();
foreach ( $commander_data as $commander_slug => $value ) {
    $value['count'] = 0;
    $value['slug'] = $commander_slug;
    $commanders_count[$value['id']] = $value;
}


$result = $pdo->run("SELECT id FROM users ORDER BY credits_earned DESC LIMIT 100;")->fetchAll();
foreach( $result as $row ) {
    $users_with_most_credits_earned[$row['id']] = $row['id'];
}



$deck_count = 0;
$months_ago = time()-(3600*24*30*10);
$comma_separated = implode(",", $users_with_most_credits_earned);
$result = $pdo->run("SELECT * FROM decks WHERE time_saved > ".$months_ago." AND user_id IN (".$comma_separated.");")->fetchAll();
foreach( $result as $row ) {
    $deck_count++;
    $commanders_count[$row['commander']]['count']++;
    $cards = json_decode($row['cards'], true);    
    foreach ( $cards as $card_id => $count ) {
        $cards_count[$card_id]['count'] += $count;
        $cards_count[$card_id]['decks_count'] ++;
    }
}


// Sort by count https://stackoverflow.com/a/22393663
usort($cards_count, function ($a, $b) {
    return ( $a['count'] < $b['count'] ? 1 : -1 );
});
usort($commanders_count, function ($a, $b) {
    return ( $a['count'] < $b['count'] ? 1 : -1 );
});

?>

<p>We look at the decks updated within the latest 10 months but only those by the 100 users with most credits earned.<br> We get <strong><?=$deck_count?></strong> decks and this page list the total count of cards in those decks.</p>

<h2>Why do we do so many years of playtesting?</h2>
<ul>
    <li>We don't want to ban, restrict or change cards after we mass print them.</li>
    <li>There will be a lot or tournaments. We don't want any specific kind of deck to dominate the meta for a long time. If a certain deck is dominating there has to be a way to build decks that are good against it. An important part of the meta game will be to try to find out which decks will be popular in upcoming tournaments.</li>
    <li>We will not be able to release new cards very often which can balance the meta. Perhaps once every 1-2 years compared to 4 times per year as it is done by Magic the Gathering.</li>
    <li>We want all the cards to be useful in the constructed format.</li>
</ul>


<?php
$comments['capt-shera-kinson'] = 'Buff in September 2021';
$comments['dr-stein'] = 'Nerf in February 2023, Nerf in August 2021';
$comments['frank-johnson'] = 'Nerf in June 2020';
$comments['nicia-satu'] = 'Buff in May 2022, Buff in December 2020, Buff in August 2019';
$comments['alien-missile'] = 'Buff in August 2020';
$comments['ambush'] = 'Buff in August 2021, Buff in March 2021';
$comments['carrier'] = 'Nerf in 2020';
$comments['death-ray'] = 'Buff in August 2020';
$comments['defense-cannon'] = 'Buff in September 2021, Buff in July 2021';
$comments['destiny-decided'] = 'Changed in May 2019';
$comments['destroy-duration'] = 'Nerf in February 2023, Nerf in September 2021';
$comments['discovery'] = 'Buff in August 2019';
$comments['disturbing-sensor'] = 'Nerf in April 2019';
$comments['drone'] = '10 copies allowed, all other cards max 3 copies';
$comments['drone-leader'] = 'Buff in March 2021';
$comments['duplication'] = 'Nerf in March 2021';
$comments['emp-missile'] = 'Buff in August 2021';
$comments['fate'] = 'Nerf in October 2021';
$comments['fatal-error'] = 'Nerf in May 2020';
$comments['full-attack'] = 'Nerf in January 2020';
$comments['fusion-ship'] = 'Nerf in August 2021';
$comments['grand-opportunity'] = 'Buff in September 2021, Buff in April 2019';
$comments['luck'] = 'Nerf in October 2021, Nerf in March 2021';
$comments['mega-shield'] = 'Nerf in March 2021';
$comments['meteor-shower'] = 'Nerf in August 2021';
$comments['missiles-launched'] = 'Buff in September 2021';
$comments['neutralization'] = 'Buff in January 2021, Buff in November 2019';
$comments['nuclear-missile'] = 'Buff in August 2020';
$comments['over-capacity'] = 'Buff in January 2020';
$comments['pursuiter'] = 'Nerf in April 2019';
$comments['repair-shield'] = 'Nerf in March 2022';
$comments['return'] = 'Buff in March 2021';
$comments['sabotage'] = 'Buff in August 2019';
$comments['sacrifice'] = 'Buff in 2020';
$comments['starhunter'] = 'Nerf in March 2021, Nerf in January 2021';
$comments['starship'] = 'Fully changed in March 2021';
$comments['station-repair'] = 'Buff in June 2021';
$comments['target-missed'] = 'Buff in July 2021';
$comments['the-dark-destroyer'] = 'Fully changed in May 2019';
$comments['the-exterminator'] = 'Changed in May 2019';
$comments['the-liberator'] = 'Buff in February 2021';
$comments['the-shade'] = 'Buff in April 2019';
$comments['titan'] = 'Nerf in January 2020';
$comments['toxic-gas'] = 'Nerf in 2020';

echo '<style>img {display:none;height:350px;} .img-hover:hover img, .img-hover2:hover img {display:block;position:absolute;margin-left:300px;margin-top:-120px;} .img-hover2:hover img {margin-left:-100px;}</style>';

echo '<h2>Commanders</h2>';
foreach ( $commanders_count as $value ) {
    echo '<div class="img-hover">'.$value['count'].': <a href="/commanders/'.$value['slug'].'">'.$value['name'].'</a>';
    if ( isset( $comments[$value['slug']] ) ) {
        echo ' ('.$comments[$value['slug']].')';
    }
    echo '<img src="https://images.thespacewar.com/commander-'.$value['id'].'.png"></div>';
}

echo '<h2>Cards</h2>';
foreach ( $cards_count as $card_id => $value ) {
    echo '<div class="img-hover2">'.$value['count'].' cards in '.$value['decks_count'].' decks: <a href="/cards/'.$value['slug'].'">'.$value['name'].'</a>';
    if ( isset( $comments[$value['slug']] ) ) {
        echo ' ('.$comments[$value['slug']].')';
    }
    echo '<img src="https://images.thespacewar.com/card-'.$value['id'].'.jpg"></div>';
}