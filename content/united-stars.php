<?php
$title_tag = 'United Stars | TheSpaceWar.com';
require(ROOT.'view/head.php');

?>

<h1>United Stars</h1>

<div class="cards">

<p>United Stars is the third deck for The Space War. It has been play tested around 20 times so far. A bit more advanced to play with some interesting new strategies.</p>

<p><em>The United Stars is an alliance formed by a group of advanced developed planets close to the Orion constellation. They have not been seen in this sector for at least 80 years and nobody thought that they would travel so far to join The Space War. They use powerful advanced technology including a deadly starship.</em></p>

<!--<p><em>United Stars are all advanced robots, some of them in humanoid form. It is not known exactly which life forms created the Artificial Intelligence that made it possible for them to self develop, if this life form still exist or if it might be humans that did some illegal experiement in a far distant past. (work in progress)</em></p>-->


<h2>Commanders</h2>

<?php
$commander_data = commanderData();
foreach ($commander_data as $commander_slug => $commander) {
    if ($commander['deck'] != 3) continue;
    echo "<a href='/commanders/".$commander_slug."'><img src='https://images.thespacewar.com/commander-".$commander['id'].".png'></a>";
}
?>

<p>Many images are missing for the cards and the names are not finalized. The small number in the bottom middle of each card indicates the amount of copies of the card.</p>

<h2>Spaceship Cards</h2>

<?php
$cards_array = getCardData();
foreach ($cards_array as $key => $value) {
    if ( $value['deck_id'] != 3 || $value['type'] != 'Spaceship' ) continue;
    echo "<a href='/cards/".titleToSlug($value['title'])."'><img src='https://images.thespacewar.com/card-".$value['id'].".jpg'></a>";
} ?>

<h2>Event Cards</h2>

<?php
$cards_array = getCardData();
foreach ($cards_array as $key => $value) {
    if ( $value['deck_id'] != 3 || $value['type'] != 'Event' ) continue;
    echo "<a href='/cards/".titleToSlug($value['title'])."'><img src='https://images.thespacewar.com/card-".$value['id'].".jpg'></a>";
} ?>

<h2>Duration Cards</h2>

<?php
$cards_array = getCardData();
foreach ($cards_array as $key => $value) {
    if ( $value['deck_id'] != 3 || $value['type'] != 'Duration' ) continue;
    echo "<a href='/cards/".titleToSlug($value['title'])."'><img src='https://images.thespacewar.com/card-".$value['id'].".jpg'></a>";
} ?>


<h2>Missile Cards</h2>

<?php
$cards_array = getCardData();
foreach ($cards_array as $key => $value) {
    if ( $value['deck_id'] != 3 || $value['type'] != 'Missile' ) continue;
    echo "<a href='/cards/".titleToSlug($value['title'])."'><img src='https://images.thespacewar.com/card-".$value['id'].".jpg'></a>";
} ?>


<h2>Defense Cards</h2>

<?php
$cards_array = getCardData();
foreach ($cards_array as $key => $value) {
    if ( $value['deck_id'] != 3 || $value['type'] != 'Defense' ) continue;
    echo "<a href='/cards/".titleToSlug($value['title'])."'><img src='https://images.thespacewar.com/card-".$value['id'].".jpg'></a>";
} ?>

<p>Complete card list in table format can be found <a href='/card-list'>here</a>.</p>

</div>