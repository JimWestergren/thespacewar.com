<?php
$title_tag = 'The Space War Expandable Card Game';
require(ROOT.'view/head.php');
?>

<div class="showcase-cards">
    <?=cardImage('fast-missile')?>
    <?=cardImage('trigger-happy-joe')?>
    <?=cardImage('the-dark-destroyer')?>
    <?=cardImage('luck')?>
    <?=cardImage('time-warp')?>
</div>


<?php if ($logged_in == []) { ?>
    <div style="max-width:400px;margin:30px auto 20px auto;text-align:center ">
    <p>Play for FREE in your browser:</p>
    <form method="post" action="/register">
        <input type="text" name="username" required minlength="3" maxlength="30" pattern="[a-zA-Z0-9]+" placeholder='Username' title="Numbers or letters only. Minimum 3 characters." style="font-size:20px;">
        <input type="submit" name="check" style="font-size:20px;" value="Play!">
    </form>
    </div>

    <h3 style="margin:10px auto 0 auto;text-align: center;border:2px solid #555;padding:14px;display: table;background-color:#1e1e1e85">🚀 The first 5000 users receives 200 credits free 🚀</h3>

<?php } ?>

<div style="padding: 50px 30px 1px 30px;font-size: 19px;">
    <p><strong>The Space War</strong> is a new fast-paced strategic 2 player card game.</p>

    <p>Better than Hearthstone? Better than Magic? Hell yeah!! 💪😅</p>

    <p>All cards included directly for free 👍.</p>

    <p>More like chess and not a pay-to-win game 😄.</p>

    <p>The game is played the same offline or online in the browser (desktop, tablet or phone).</p>

    <p>No booster packs, no RNG cards, no dead cards and no resource cards.</p>

    <p>Choose between 3 preset decks or mix the cards and make your own deck.</p>

</div>

<blockquote>
    <p>The way the stations work, allowing you to customize and manipulate your actions, is very, very clever. I also like how it functions as your life meter, meaning there is no need for health counters.</p>
    <cite>- Anonymous Cardboard Edison Award Judge</cite>
 </blockquote>

<blockquote>
    <p>Very elegant design with solid core and interesting core mechanics.</p>
    <cite>- Suzanne Zinsli, Cardboard Edison Award Judge</cite>
 </blockquote>


<hr>

<h2>Latest active players</h2>

<div style="font-size: 15px;line-height: 17px;">
<?php
if (apcu_exists('home:latest_active_players')) {
    $html = apcu_fetch('home:latest_active_players');
} else {
    $pdo = PDOWrap::getInstance();
    $result = $pdo->run("SELECT * FROM users ORDER BY lastlogintime DESC LIMIT 50;")->fetchAll();
    $html = '';
    foreach($result as $row) {
        $html .= '<nobr>'.$row['username'].' <img src="https://staticjw.com/redistats/images/flags/'.$row['country'].'.gif"></nobr> | ';
    }
    $html = trim($html, ' | ');
    apcu_store('home:latest_active_players', $html, 60*2);
    
}
echo $html;
?>
</div>


<h2>5 minute intro video</h2>


<div class='embed-container' style="margin:40px 0;"><iframe src='https://www.youtube-nocookie.com/embed/BLWPF9-y958' frameborder='0' allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe></div>

<h2>Video Transcript</h2>

<p>What is The Space War card game and why do I think it is an amazing game?</p>

<p>Hi I am Jim and I am the creator of this new card game -  The Space War.</p>

<p>I have been playing card games on and off the last 25 years - for example Magic, Hearthstone, Dominion, Doomtrooper and many others. I also play a lot of modern board games.</p>

<p>In the summer of 2018 I started working on my own card game which in my opinion is much better than other similar games.</p>

<ul>
    <li>Strategic 2 player card game, around 20-40 minutes to play</li>
    <li>All cards included for free</li>
    <li>More like chess in the way that both players has access to the same cards - it is not a pay-to-win game.</li>
    <li>The game is played the same offline or online
    <li>Free to play online in your web browser at thespacewar.com</li>
    <li>Print and play offline for free.</li>
    <li>Focus on how your play your cards instead of which cards you have</li>
    <li>Choose between 3 preset decks or mix the cards to make your own unique deck</li>
    <li>Each card can be played in several ways but the game is still simple and smooth to play</li>
    <li>Skill based with focus on competitive play, leaderboard on the site.</li>
    <li>Player interaction with counters and reactions </li>
    <li>Tension and excitement without any runaway leader problem</li>
    <li>No booster packs</li>
    <li>No grinding</li>
    <li>No RNG cards</li>
    <li>No dead cards</li>
    <li>No resource cards</li>
    <li>New interesting game mechanic, I will show you</li>
</ul>

<p>[ Showing and explaining the new game mechanic that makes this game different than other similar games ]</p>

<p>[ Quick demo of online play ]</p>

<p>[ Quick demo of offline play ]</p>

<hr>


<blockquote>
    <p>Card games is a big part of my life and I have played many different games the last 25 years including Magic the Gathering, Hearthstone, Dominion, RftG, Star Realms, Doomtrooper, different classic card games and other CCGs but The Space War is what I enjoy most to play in a 2 player game ... by far.</p>
    <p>We have more than 60 modern board games and I just asked my 10 year old son what is his favorite game of all time and he answered The Space War without any hesitation. I think the same. We have played the game on a regular basis for 2 years now.</p>
    <cite>- Jim Westergren</cite>
 </blockquote>



<h2>External Links</h2>

<p><a href="https://discord.gg/tv3DXqj" target="_blank">Discord</a> | <a href='https://www.facebook.com/TheSpaceWarCardGame' target="_blank">Facebook Page</a> | <a href='https://www.facebook.com/groups/thespacewar' target="_blank">Facebook Group</a> | <a href='https://twitter.com/The_Space_War' target="_blank">Twitter</a> | <a href='https://boardgamegeek.com/boardgame/310172/space-war' target="_blank">BGG</a> | <a href="https://www.youtube.com/channel/UCe2kq-IX7zl2wYGK0bT0ucA" target="_blank">YouTube</a> | <a href="https://www.reddit.com/r/TheSpaceWar/" target="_blank">Reddit</a> <!--| <a href="https://join.skype.com/oYehB3TCSl8b" target="_blank">Skype</a>--></p>




<!--
<br><br>

<iframe src="https://discord.com/widget?id=711625253582798852&theme=dark" width="350" height="500" allowtransparency="true" frameborder="0"></iframe>
-->

