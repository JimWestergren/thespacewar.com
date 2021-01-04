<?php
$title_tag = 'United Stars | TheSpaceWar.com';
require(ROOT.'view/head.php');

?>

<h1>United Stars</h1>

<div class="cards">

<p>United Stars is the third deck for The Space War. It has been play tested around 10 times so far. A bit more advanced to play with some interesting new strategies.</p>

<p><em>The United Stars is an alliance formed by a group of advanced developed planets close to the Orion constellation. They have not been seen in this sector for at least 80 years and nobody thought that they would travel so far to join The Space War. They use powerful advanced technology including a deadly starship.</em></p>


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

<img src="https://images.thespacewar.com/card-228.jpg">
<img src="https://images.thespacewar.com/card-225.jpg">
<img src="https://images.thespacewar.com/card-226.jpg">
<img src="https://images.thespacewar.com/card-224.jpg">
<img src="https://images.thespacewar.com/card-222.jpg">
<img src="https://images.thespacewar.com/card-223.jpg">
<img src="https://images.thespacewar.com/card-227.jpg">
<img src="https://images.thespacewar.com/card-244.jpg">

<h2>Event Cards</h2>

<img src="https://images.thespacewar.com/card-217.jpg">
<img src="https://images.thespacewar.com/card-213.jpg">
<img src="https://images.thespacewar.com/card-218.jpg">
<img src="https://images.thespacewar.com/card-216.jpg">
<img src="https://images.thespacewar.com/card-215.jpg">
<img src="https://images.thespacewar.com/card-219.jpg">
<img src="https://images.thespacewar.com/card-221.jpg">
<img src="https://images.thespacewar.com/card-209.jpg">
<img src="https://images.thespacewar.com/card-214.jpg">
<img src="https://images.thespacewar.com/card-231.jpg">
<img src="https://images.thespacewar.com/card-208.jpg">

<h2>Duration Cards</h2>

<img src="https://images.thespacewar.com/card-207.jpg">
<img src="https://images.thespacewar.com/card-230.jpg">
<img src="https://images.thespacewar.com/card-206.jpg">
<img src="https://images.thespacewar.com/card-210.jpg">
<img src="https://images.thespacewar.com/card-211.jpg">
<img src="https://images.thespacewar.com/card-248.jpg">

<h2>Missile Cards</h2>

<img src="https://images.thespacewar.com/card-220.jpg">
<img src="https://images.thespacewar.com/card-229.jpg">

<h2>Defense Cards</h2>

<!--<img src="https://images.thespacewar.com/card-203.jpg">-->
<img src="https://images.thespacewar.com/card-205.jpg">
<img src="https://images.thespacewar.com/card-204.jpg">

<p>Complete card list in table format can be found <a href='/card-list'>here</a>.</p>

</div>