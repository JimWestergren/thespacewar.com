<?php
$title_tag = 'The Space War Card Game';
require(ROOT.'view/head.php');
?>


<div class="cards" id='home-image'><a href="/cards/the-dark-destroyer"><img src="https://images.thespacewar.com/card-2.jpg"></a></div>

<p><strong>The Space War</strong> is a new fast-paced strategic 2 player card game.</p>

<p>Better than Hearthstone? Better than Magic? Hell yeah says the creator.</p>

<p>All cards included for free. More like chess and not a "pay to win game".</p>

<?php if ($logged_in == []) { ?>
    <div style="max-width:400px;margin:30px auto 20px auto;">
    <p>Enter username to play for free directly in your browser:</p>
    <form method="post" action="/register">
        <input type="text" name="username" required minlength="3" maxlength="30" pattern="[a-zA-Z0-9]+" placeholder='Username' title="Numbers or letters only. Minimum 3 characters.">
        <input type="submit" name="check" value="Play">
    </form>
    </div>

    <h3 style="margin-bottom: 20px;">The first 5000 registered users that wins a game over another human will unlock Pro Account 5 years for free (estimated value $70).</h3>

<?php } ?>


<hr>

<p>The game is played the same offline or online in the browser.</p>

<p>It uses several new interesting game mechanics, both players has access to the same cards and all cards are included in the game.</p>

<p>Each card can be played in several ways but the game is still simple and smooth to play.</p>

<blockquote><em>"Card games is a big part of my life and I have played many different games the last 25 years including Magic the Gathering, Hearthstone, Dominion, RftG, Star Realms, Doomtrooper, different classic card games and other CCGs but The Space War is what I enjoy most to play in a 2 player game ... by far. We have more than 60 modern board games and I just asked my 10 year old son what is his favorite game of all time and he answered The Space War without any hesitation. I think the same. We have played the game on a regular basis for 2 years now."</em> - Jim Westergren</blockquote>



<h2>External Links</h2>

<p><a href="https://discord.gg/tv3DXqj" target="_blank">Discord</a> | <a href='https://www.facebook.com/TheSpaceWarCardGame' target="_blank">Facebook Page</a> | <a href='https://www.facebook.com/groups/thespacewar' target="_blank">Facebook Group</a> | <a href='https://twitter.com/The_Space_War' target="_blank">Twitter</a> | <a href='https://boardgamegeek.com/boardgame/310172/space-war' target="_blank">BGG</a> | <a href="https://www.youtube.com/channel/UCe2kq-IX7zl2wYGK0bT0ucA" target="_blank">YouTube</a> | <a href="https://www.reddit.com/r/TheSpaceWar/" target="_blank">Reddit</a> <!--| <a href="https://join.skype.com/oYehB3TCSl8b" target="_blank">Skype</a>--></p>


<h2>Latest active players</h2>

<div style="font-size: 16px;line-height: 20px">
<?php
if (apcu_exists('home:latest_active_players')) {
    echo apcu_fetch('home:latest_active_players');
} else {
    $pdo = PDOWrap::getInstance();
    $result = $pdo->run("SELECT * FROM users ORDER BY lastlogintime DESC LIMIT 50;")->fetchAll();
    $html = '';
    foreach($result as $row) {
        $html .= '<nobr>'.$row['username'].' <img src="https://staticjw.com/redistats/images/flags/'.$row['country'].'.gif"></nobr> | ';
    }
    $html = trim($html, ' | ');
    apcu_store('home:latest_active_players', $html, 60*2);
    echo $html;
}
?>
</div>

<!--
<br><br>

<iframe src="https://discord.com/widget?id=711625253582798852&theme=dark" width="350" height="500" allowtransparency="true" frameborder="0"></iframe>
-->



</div>
