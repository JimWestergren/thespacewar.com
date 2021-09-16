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


echo '<p>We look at the decks updated within the latest 10 months but only those by the 100 users with most credits earned. We get <strong>'.$deck_count.'</strong> decks and here are the total count of cards in those decks:</p>';


echo '<h2>Commanders</h2>';
foreach ( $commanders_count as $commander_id => $value ) {
    echo $value['count'].': <a href="/commanders/'.$value['slug'].'">'.$value['name'].'</a><br>';
}

echo '<h2>Cards</h2>';
foreach ( $cards_count as $card_id => $value ) {
    echo $value['count'].' cards in '.$value['decks_count'].' decks: <a href="/cards/'.$value['slug'].'">'.$value['name'].'</a>';
    if ( $value['name'] === 'Drone' ) {
        echo ' (10 copies allowed, all other cards max 3 copies)';
    }
    echo '<br>';
}