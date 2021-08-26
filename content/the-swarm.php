<?php
$title_tag = 'The Swarm | TheSpaceWar.com';
require(ROOT.'view/head.php');
?>

<h1>The Swarm</h1>

<div class="cards">

<p>The Swarm is the second deck for The Space War. It has been play tested around 60 times so far. More easy to play and more fun but still keeping the strategic aspect. The Swarm wins around 50% of the games versus the The Terrans deck depending on the skill of the person playing.</p>

<p><em>Just known as "The Swarm" this alien race is very fast and agressive and can quickly overwhelm any opponent.</em></p>


<h2>Commanders</h2>

<?php
$commander_data = commanderData();
foreach ($commander_data as $commander_slug => $commander) {
    if ($commander['deck'] != 2) continue;
    echo "<a href='/commanders/".$commander_slug."'><img loading=lazy src='https://images.thespacewar.com/commander-".$commander['id'].".png'></a>";
}
?>

<p>The small number in the bottom middle of each card indicates the amount of copies of the card. There are 15 copies of Drone in the deck.</p>

<h2>Spaceship Cards</h2>

<?=cardImage('drone')?>
<?=cardImage('drone-leader')?>
<?=cardImage('repair-ship')?>
<?=cardImage('paralyzer')?>
<?=cardImage('carrier')?>
<?=cardImage('fusion-ship')?>

<h2>Event Cards</h2>

<?=cardImage('revive-procedure')?>
<?=cardImage('sacrifice')?>
<?=cardImage('destroy-duration')?>

<h2>Duration Cards</h2>

<?=cardImage('disturbing-signals')?>
<?=cardImage('nitro')?>
<?=cardImage('toxic-gas')?>
<?=cardImage('base-command-link')?>
<?=cardImage('collision-skill')?>
<?=cardImage('time-warp')?>

<h2>Missile Cards</h2>

<?=cardImage('alien-missile')?>
<?=cardImage('acid-projectile')?>

<h2>Defense Cards</h2>

<?=cardImage('shield')?>

<p>Complete card list in table format can be found <a href='/card-list'>here</a>.</p>

</div>