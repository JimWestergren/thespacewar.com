<?php
$title_tag = 'Supernova | TheSpaceWar.com';
require(ROOT.'view/head.php');
?>

<h1>Supernova</h1>

<p>The most expensive and powerful card in the game. The card most commonly fetched with Perfect Plan.</p>

<?= displayCard(15) ?>

<h2>What cards can stop Supernova?</h2>

<a href="/cards/avoid"><img src="https://images.thespacewar.com/card-34.jpg"></a>
<a href="/cards/destiny-decided"><img src="https://images.thespacewar.com/card-64.jpg"></a>

<h2>Rule Clarifications</h2>

<ul>
    <li>You are not allowed to play Supernova if you your station has 3 or less remaining life.</li>
</ul>
