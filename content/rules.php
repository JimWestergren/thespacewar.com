<?php
$title_tag = 'How to Play / Rules | TheSpaceWar.com';
require(ROOT.'view/head.php');
?>

<h1>How to Play / Rules</h1>

<ul class="no-print">
  <li>We recommend that you watch our <a href="/videos">videos</a> and <a href="https://play.thespacewar.com/">play online in your browser</a> for free against the bot before playing versus real players.</li>
  <li>You can <a href="#" onclick="window.print();return false;">print</a> this document and keep it available during offline play.</li>
  <li>These rules apply to all forms of play (offline, online, casual, and tournament).</li>
</ul>

<h2>Goal of the Game</h2>
<p>
  You are a commander of a space station at war. Deploy spaceships, missiles, defenses, and events to break through the enemy and damage their station.
</p>
<p>
  When a station takes damage, the attacking player chooses and flips that many face-down station cards. If there are no face-down station cards left to flip, any remaining damage is dealt to the commander instead.
</p>
<p>
  Each commander has a number in the top-left corner representing its <strong>Life Points</strong>. If a commander’s Life Points are reduced to 0, that player loses the game.
</p>

<h2>Preparation</h2>
<ol>
  <li>Each player has a deck (the <strong>draw pile</strong>) on the left and a <strong>discard pile</strong> below it. Use dice to track damage, actions, and Life Points as needed.</li>
  <li>Place the three <strong>Station Rule Cards</strong> in sequence on the right side (see <strong>Station Cards</strong> below).</li>
  <li>Randomly decide which player gets to choose who starts the game.</li>
  <li>Shuffle the draw pile and draw 6 cards. The player who will not start draws 1 additional card (7 total).</li>
  <li>
    Choose 3 cards from the hand to become the initial station cards.
    (First-time players: choose cards with the highest top-left number.)
    Place those 3 chosen cards face down next to the Station Rule Cards so there is 1 face-down card in each of the 3 rows.
  </li>
  <li>The player who will not start places 1 additional station card face down in a row of their choice. Both players now have 3 cards remaining in hand.</li>
  <li>
    Secretly choose one commander and place it face down near the draw pile.
    When both players are ready, reveal commanders simultaneously.
    Then place a die on each commander set to its starting Life Points.
    The starting player takes the first turn.
  </li>
</ol>

<h2>Station Cards</h2>

<div class="station-rows">
  <div class="station-row">
    <img class="station-img" src="https://images.thespacewar.com/station-card-top.jpg" alt="Draw row station rule card">
    <div class="station-text">
      <div class="station-kicker">Draw phase</div>
      <div class="station-main">Draw <strong>1</strong> card</div>
      <div class="station-sub">per station card in this row.</div>
    </div>
  </div>

  <div class="station-row">
    <img class="station-img" src="https://images.thespacewar.com/station-card-middle.jpg" alt="Action row station rule card">
    <div class="station-text">
      <div class="station-kicker">Action phase</div>
      <div class="station-main">Gain <strong>2</strong> actions</div>
      <div class="station-sub">per station card in this row.</div>
    </div>
  </div>

  <div class="station-row">
    <img class="station-img" src="https://images.thespacewar.com/station-card-bottom.jpg" alt="Discard row station rule card">
    <div class="station-text">
      <div class="station-kicker">Discard phase</div>
      <div class="station-main">Hand size limit: <strong>3</strong> cards</div>
      <div class="station-sub">per station card in this row.</div>
    </div>
  </div>
</div>

<p>
  <strong>Note:</strong> A player may look at their own face-down station cards during their action phase.<br>
  More details about these phases appear in <strong>The Turn</strong> below.
</p>

<h2>Table Setup</h2>
<p>
  The table has two zones: <strong>Home Zone</strong> and the opponent’s <strong>Enemy Zone</strong>.
  Cards played enter the Home Zone.
</p>

<h2>The Cards</h2>
<p>
  The number in the top-left corner of a card is its <strong>action cost</strong>.
  Actions are spent to play cards during the action phase.
  Cards with a cost of <strong>0</strong> may be played at any time (including during an opponent’s turn).
  If a card’s text contradicts the rules of the game, the card’s text takes precedence.
</p>

<ul>
  <li>
    <h3>Spaceship <span style="font-weight:400;">(blue)</span></h3>
    <p>
      Spend actions to play a spaceship into the Home Zone.
      Each spaceship may normally perform one attack and one move each turn.
      A spaceship cannot move the turn it enters play.
      If a spaceship has been in play since the start of its controller’s turn, it may move from the Home Zone to the Enemy Zone, or vice versa.
      A spaceship may always attack an enemy card in the same zone (even the turn it enters play).
      To attack the enemy station, a spaceship must have been in the Enemy Zone since the start of the turn.
    </p>
  </li>

  <li>
    <h3>Missile <span style="font-weight:400;">(red)</span></h3>
    <p>
      Spend actions to play a missile into the Home Zone. Missiles can move and attack similarly to spaceships.
      A missile is <strong>destroyed after it attacks</strong>.
      Unlike spaceships, a missile does <strong>not</strong> need to spend a full turn in the Enemy Zone before it can attack the enemy station.
      If a missile is not Slow, it may (after its first turn in play) attack any target, including the enemy station, from the Home Zone.
    </p>
  </li>

  <li>
    <h3>Duration <span style="font-weight:400;">(violet)</span></h3>
    <p>
      Duration cards remain in play until their owner discards them.
      At the start of a player’s action phase, that player must either <strong>pay</strong> each duration card’s action cost to keep it, or discard it from play.
      (See <strong>The Turn</strong>.)
    </p>
  </li>

  <li>
    <h3>Event <span style="font-weight:400;">(orange)</span></h3>
    <p>
      Events resolve once when played, then are placed into the discard pile.
    </p>
  </li>

  <li>
    <h3>Defense <span style="font-weight:400;">(green)</span></h3>
    <p>
      Defense cards (such as shields and cannons) remain in the Home Zone and cannot move.
      <strong>Shields protect the station:</strong> a shield must be destroyed before the station can be attacked.
      Cannons attack during the attack phase like other attackers (see <strong>Attacks</strong>).
    </p>
  </li>

  <li>
    <h3>Commander <span style="font-weight:400;">(commander card)</span></h3>
    <p>
      Commanders provide an ongoing ability or bonus.
      Commanders cannot be attacked directly; damage is dealt to a commander only after that commander’s controller has no face-down station cards left to flip.
    </p>
  </li>
</ul>

<h2>The Turn</h2>
<p>
  Each turn follows the Station Rule Cards from top to bottom and ends with an attack phase:
</p>

<ol>
  <li>
    <strong>Draw phase:</strong>
    Draw 1 card for each station card in the first row. Drawing is mandatory.
  </li>

  <li>
    <strong>Action phase:</strong>
    Gain 2 actions for each station card in the second row.
    <ul>
      <li>
        Duration upkeep: For each duration card you control, pay its action cost to keep it or discard it from play.
      </li>
      <li>
        Spend actions to play cards from hand.
      </li>
      <li>
        Station management (optional):
        <ul>
          <li>Place one card from hand face down as a station card in any row (maximum <strong>7</strong> station cards total), or</li>
          <li>Move one station card from one row to another.</li>
        </ul>
      </li>
      <li>Unused actions are lost as the action phase ends.</li>
    </ul>
  </li>

  <li>
    <strong>Discard phase:</strong>
    If having more cards in hand than the hand size limit, discard down to that limit.
    The hand size limit is <strong>3 ×</strong> the number of station cards in their third row.
    <br><em>Example:</em> With 1 station card in the third row, the hand size limit is 3. With 2 cards, the limit is 6.
  </li>

  <li>
    <strong>Attack phase:</strong>
    One at a time, the player may move and/or attack with each spaceship, missile, or cannon they control, in any order.
  </li>
</ol>

<h2>Attacks</h2>
<p>
  Attacks are declared during the attack phase. An attacker may attack enemy cards in the same zone.
</p>
<p>
  Spaceships, missiles, and cannons may always attack an enemy card in the Home Zone (even if the attacker entered play this turn).
</p>
<p>
  When a card attacks, it deals damage equal to its attack value (red) to the target.
  If the total damage on a card is equal to or greater than its defense value (green), it is destroyed and placed into its owner’s discard pile.
  The attacker does not take damage from attacking.
  Damage remains on cards (use a die to track it).
</p>
<p>
  When the enemy station is attacked, the attacking player chooses and flips that many of the defending player’s face-down station cards. If the defending player cannot flip enough station cards, the remaining damage is dealt to that player’s commander (reducing Life Points).
</p>

<h2>Flipped Station Cards</h2>
<p>
  A flipped station card is face up and may be played as if it were in its owner’s hand (following normal timing rules):
  0-cost cards may be played at any time, and non-0-cost cards are played in the action phase.
</p>
<p>
  Flipped station cards are still station cards and still count for drawing, actions, and hand size limit.
</p>

<h2>Empty Draw Pile</h2>
<p>
  If a player’s draw pile is empty at the start of their draw phase, that player’s station takes <strong>3 damage</strong> (the opponent flips 3 of that player’s station cards).
  Any damage that cannot be applied to station cards is dealt to that player’s commander instead.
</p>
<p>
  If a player needs to draw but cannot, that draw is ignored.
</p>

<h2>Repair</h2>
<p>
  Some spaceships can repair. Repair works like attacking in terms of range: a ship can repair only a target in the same zone.
  Repair removes damage from the target, and if the target is paralyzed, repair also removes paralysis.
  A repair ship may repair itself unless it is paralyzed.
  Commanders cannot be repaired.
</p>

<h2>Reactions and Pace of Play</h2>
<p>
  Players must allow reasonable time for an opponent to respond to plays.
  A player may not play multiple cards rapidly in a way that prevents reactions such as counters or other 0-cost cards.
</p>
<p>
  <strong>Offline play:</strong> At any time, a player may say “pause” (or raise a hand or similar). The opponent must stop taking actions until told to continue.
</p>
<p>
  <strong>Online play:</strong> A player can always react to actions that occurred within the last 10 seconds. If several cards have been played during that time, the game will rewind.
</p>

<h2>Definitions / Keywords</h2>
<p><strong>Delayed</strong> – Ignore this card’s text box until the start of its owner’s next turn.</p>
<p><strong>Ability</strong> – An action a spaceship card may perform instead of attacking.</p>
<p><strong>Counter a card</strong> – When a card being played is countered, it does not enter play and is placed directly into the discard pile. Ignore its text; any action cost spent to play it is lost.</p>
<p><strong>Put into play</strong> – To place a card directly into a player’s Home Zone without paying its cost. Cards put into play do not come from the hand and cannot be countered.</p>
<p><strong>Discard</strong> – To move cards from a player’s hand to their discard pile. The owner chooses which cards are discarded unless stated to be random.</p>
<p><strong>Discard from play</strong> – To move one of a player’s own cards from the play area to their discard pile.</p>
<p><strong>From anywhere</strong> – A player may search their draw pile, discard pile, or station cards. If a player searches their draw pile, it must be shuffled afterward.</p>
<p><strong>No effect</strong> – Ignore all text on the cards text box. The card is still paid for, and duration cards with no effect are still handled normally during duration upkeep.</p>
<p><strong>Counters</strong> – Some cards gain counters. Use a die to track the number of counters on that card.</p>

<h2>Constructed Play (Optional)</h2>
<p>
  In Constructed, each player uses their own custom deck.
  Full Constructed rules are available here: <a href="/constructed">Constructed Play</a>.
</p>