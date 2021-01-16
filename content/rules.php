<?php
$title_tag = 'How to Play / Rules | TheSpaceWar.com';
require(ROOT.'view/head.php');
?>

<h1>How to Play / Rules</h1>

<p class="no-print"><strong>Recommendation</strong>: See our <a href="/videos">videos</a> and <a href='https://play.thespacewar.com/'>play online in your browser</a> for free against the bot before playing versus real players.<br>
<a href="#" onclick="window.print();return false;">Print</a> this document and have it accessible when you play offline.<br>
The rules of this game is the same for offline play, online play, casual play and tournament play.</p>

<hr>

<p>You are a commander of a space station in war with another commander and space station. The crew on your station can build spaceships, missiles, defense shields and other things which can attack the enemy station in various ways.</p>
<p>If your station gets damaged, you have to flip that many station cards and if you have no station cards left to flip you lose the game.</p>

<h2>Preparation</h2>
<p>Each player has a deck of 60 cards, called the draw pile, and it is located on the left. Below it is the discard pile.</p>
<p>Each player places the three Station Rule Cards in sequence on the right side (see image below).</p>
<p>Decide randomly which player will choose who starts the game.</p>
<p>Each player shuffle their draw pile and draws 6 cards to their hand. Decide who will start the game. The person that will not start the game draws one additional card.</p>
<p>Each player choose 3 cards from his or her hand that will form the initial station cards of that player (first time players can choose cards that has the highest number at the top left on the cards). Place those 3 chosen cards face down next to the Station Rule Cards so that there is 1 face down card in each of the 3 rows.</p>
<p>The person that will not start the game places 1 additional station card in the row he/she chooses. Now both players has 3 cards remaining in the hand.</p>
<p>Select one of the commanders in secret and place it face down on the left of the draw pile. When both players are ready, the commanders are flipped and revealed at the same time and the game begins.</p>

<h2>Station Cards</h2>

<table style="text-align: center;margin:auto;">
    <tr><td><img src="https://images.thespacewar.com/station-card-top.jpg" style="height:100px;"></td><td style="padding-left: 50px;">In your draw phase, draw:<br><strong>1</strong> card<br>for each card in this station row.</td></tr>
    <tr><td><img src="https://images.thespacewar.com/station-card-middle.jpg" style="height:100px;"></td><td style="padding-left: 50px;">In your action phase, receive:<br><strong>2</strong> actions<br>for each card in this station row.</td></tr>
    <tr><td><img src="https://images.thespacewar.com/station-card-bottom.jpg" style="height:100px;"></td><td style="padding-left: 50px;">In your discard phase, keep:<br><strong>3</strong> cards<br>for each card in this station row.</td></tr>
</table>
<p>More explanation in <strong>The Turn</strong>.</p>

<h2>Table Setup</h2>
<p>There are two zones. Home zone on your part of the table and the enemy zone on the opponents part of the table.</p>

<h2>The Cards</h2>
<p>At the top left on the cards is a number representing how many actions it cost to play a card. Cards that cost 0 can be played in any moment, even in the opponents turn.</p>

<ul>
    <li><h3>Spaceship <span style="font-weight: 400;">cards are blue</span></h3>
    <p>They can do 1 attack and 1 move per turn (exceptions exist if written on the card). Can always attack an enemy card in the same zone. Cannot move the first turn they enter play, but can after the first turn either move to the enemy zone or from enemy zone to home zone. Cannot attack the enemy station the same turn that they moved to the enemy zone.</p></li>
    <li><h3>Duration <span style="font-weight: 400;">cards are violet</span></h3>
    <p>They stay in the game each turn until the owner decides to discard them. Cost actions each turn, see <strong>The Turn</strong>.</p></li>
    <li><h3>Event <span style="font-weight: 400;">cards are orange</span></h3>
    <p>They make something happen and are placed directly in the discard pile after they have been played.</p></li>
    <li><h3>Missile <span style="font-weight: 400;">cards are red</span></h3>
    <p>They are destroyed after an attack. Does not need to stay a turn in the enemy zone before attacking the enemy station (spaceships needs to do this). If the missile is not slow it does not need to move to the enemy zone as it can after the first turn damage any target including the enemy station from your home zone.</p></li>
    <li><h3>Defense <span style="font-weight: 400;">cards are green</span></h3>
    <p>They stay in your home zone and cannot move. Shields protect your station (it has to be destroyed first before the station can be attacked).</p></li>
    <li><h3>Commander <span style="font-weight: 400;">cards</span></h3>
    <p>They provide a certain power or ability for the player. Cannot be attacked or destroyed.</p></li>
</ul>

<h2>The Turn</h2>
<p>Each turn consists of the following steps. It follows the Station Rule Cards from top to bottom:</p>
<ol>
    <li><strong>Draw phase</strong>: you must draw 1 card for each station card in the first row.</li>
    <li><strong>Action phase</strong>: you receive 2 actions for each station card in the second row. If you have any duration card in play decide which ones to keep by paying their action costs and which ones to instead move to your discard pile. Spend your actions to play cards from your hand. If you have less than the maximum of 8 station cards you may put down 1 card from your hand face down as a new station card in one of the 3 station rows. Actions cannot be saved for the next turn.</li>
    <li><strong>Discard phase</strong>: if you have more cards in hand than 3 times the number of station cards in the third row discard to the amount of cards allowed. For example having 1 station card in the last row allows you to keep 3 cards in your hand, 2 station cards allows you to keep 6.</li>
    <li><strong>Attack phase</strong>: move and/or attack with each spaceship, missile or cannon one by one and in the order you choose.</li>
</ol>

<h2>Attacks</h2>
<p>You attack with your spaceships, missiles and cannons in the attack phase.</p>
<p>You can only attack enemies in the same zone. </p>
<p>Your spaceship, missile or cannon can always attack an enemy card in your home zone, even on it's first turn.</p>
<p>When you attack another card, the card receives damage equal to the attack value (red) of the attacker. If damage is equal to or more than the defense value (green) the card is destroyed and moved to the discard pile. The attacker does not risk getting damage when it is attacking and damage is not automatically healed. Use a die to keep track of damage.</p>
<p>If you attack the enemy station you flip that many face down station cards of the opponent and if he/she has no more station cards to flip you win the game.</p>

<h2>Flipped Station Cards</h2>
<p>Station cards that has been flipped so that they are no longer face down can be played, but they are not considered to be on the hand. So for example 0 cost cards can be played at any moment, other cards can be played in your action phase.</p>
<p>Flipped station cards are still counted as normal (drawing, actions, hand size).</p>

<h2>Empty Draw Pile</h2>
<p>If you have no cards left in your draw pile at the beginning of your draw phase (before drawing cards) your station gets 3 damage (opponent flips 3 of your station cards). </p>
<p>If you need to draw but cannot, then simply ignore the draw requirement.</p>

<h2>Repair</h2>
<p>There are a few special spaceships that can repair. Just as attacking you can only repair a target in the same zone. If a spaceship is paralyzed, the paralyze will be removed when repaired (in additional to repair of damage). A repair ship can repair itself (but not if it is paralyzed).</p>


<h2>Definitions</h2>
<p><strong>Counter</strong> means that the card played from the opponent does not enter play and goes straight to the discard pile. All the text on the countered card are ignored and any actions that was spent playing the countered card has been lost.</p>
<p><strong>Discard</strong> means to move cards from your hand to the discard pile (if there are enough cards in the hand).</p>

<h2>Additional Rules</h2>
<ul>
    <li>For the offline game: At any moment you can say "pause" or raise your hand and then the opponent are not allowed to continue play until you say "ok". You can use this to think if you want to counter a card, play a 0-cost card and so forth. You are not allowed to play several cards very quickly in a way that makes the opponent not able to counter or react. In the online game you can always react to things that has happened in the last 10 seconds.</li>
    <li>Optional: In the constructed format each player plays with their own unique deck. The complete rules for the constructed format can be seen on the page <a href='/constructed'>Constructed Play</a>.</li>
    <li>Edge case: The newest duration card that enters the game overwrites the rule of any conflicting duration card on the table. (for example when both players have played <a href='/cards/neutralization'>Neutralization</a> then only the latest one has the ability "Remove from play to destroy another duration card.")</li>
</ul>
