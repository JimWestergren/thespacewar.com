<?php
$title_tag = 'Full list of cards | TheSpaceWar.com';
require(ROOT.'view/head.php');
?>

<h1>The Cards</h1>

<p>Below are the cards belonging to the The Terrans deck.</p>

<h2>Additional decks</h2>
<ul>
    <li><a href='/the-swarm'>The Swarm</a> is a very fast and agressive an alien race which can quickly overwhelm any opponent.</li>
    <li><a href='/united-stars'>United Stars</a> is an alliance formed by a group of advanced developed planets. They use powerful advanced technology including a deadly starship.</li>
</ul>

<p>Complete card list in table format can be found <a href='/card-list'>here</a>.</p>

<div class="cards">

<h2>Commanders</h2>
<p>You choose your commander at the beginning of the game after you have established your start hand. Each commander gives you a unique power or ability.</p>

<?php
$commander_data = commanderData();
foreach ($commander_data as $commander_slug => $commander) {
    if ($commander['deck'] != 1) continue;
    echo "<a href='/commanders/".$commander_slug."'><img loading=lazy src='https://images.thespacewar.com/commander-".$commander['id'].".png'></a>";
}
?>

<h2>Spaceship Cards</h2>
<p>These can move and attack. Sometimes they have special abilities.</p>
<?=cardImage('the-dark-destroyer')?>
<?=cardImage('the-exterminator')?>
<?=cardImage('stormfalcon')?>
<?=cardImage('titan')?>
<?=cardImage('pursuiter')?>
<?=cardImage('trigger-happy-joe')?>
<?=cardImage('thunderbolt')?>
<?=cardImage('the-gladiator')?>
<?=cardImage('the-shade')?>
<?=cardImage('hunter')?>
<?=cardImage('small-repair-ship')?>
<?=cardImage('big-repair-ship')?>
<?=cardImage('battlestar')?>
<?=cardImage('disturbing-sensor')?>
<?=cardImage('deadly-sniper')?>
<?=cardImage('the-liberator')?>

<h2>Event Cards</h2>
<p>Playing an event card means that something happens a single time. The card gets placed directly in the discard pile.</p>
<?=cardImage('excellent-work')?>
<?=cardImage('supernova')?>
<?=cardImage('target-missed')?>
<?=cardImage('missiles-launched')?>
<?=cardImage('perfect-plan')?>
<?=cardImage('grand-opportunity')?>
<?=cardImage('luck')?>
<?=cardImage('fatal-error')?>
<?=cardImage('discovery')?>
<?=cardImage('sabotage')?>

<h2>Duration Cards</h2>
<p>These cards stay on the table and alters the game in some way. The action cost of the card has to be paid for each turn for it to continue be in play.</p>
<?=cardImage('full-attack')?>
<?=cardImage('good-karma')?>
<?=cardImage('neutralization')?>
<?=cardImage('over-capacity')?>
<?=cardImage('avoid')?>
<?=cardImage('destiny-decided')?>

<h2>Missile Cards</h2>
<p>More powerful and quicker than spaceships but they are destroyed when hitting their target.</p>
<?=cardImage('explosive-missile')?>
<?=cardImage('fast-missile')?>
<?=cardImage('emp-missile')?>
<?=cardImage('nuclear-missile')?>

<h2>Defense Cards</h2>
<p>These defends your space station, they cannot move.</p>
<?=cardImage('energy-shield')?>
<?=cardImage('defense-cannon')?>

<h2>Station Rule Cards</h2>
<p>These are for remembering the rules and have no function in the game.</p>
<img loading=lazy src="https://images.thespacewar.com/station-card-top.jpg">
<img loading=lazy src="https://images.thespacewar.com/station-card-middle.jpg">
<img loading=lazy src="https://images.thespacewar.com/station-card-bottom.jpg">
</div>



