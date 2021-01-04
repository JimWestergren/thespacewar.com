<?php
$title_tag = 'Play The Space War | TheSpaceWar.com';
require(ROOT.'view/head.php');
?>

<h1>Play</h1>

<h2>Play online for free</h2>

<p>Nothing to download, play directly in your browser for free.</p>

<?php if ($logged_in != []) { ?>
    <p>Go to <a href='/account/'>your account</a>.</p>
<?php } else { ?>
    <p>This is alpha testing of the online version, there might still be some bugs.</p>
    <div style="max-width:400px;margin:30px auto 20px auto;">
    <p>Create your free account:</p>
    <form method="post" action="/register">
        <input type="text" name="username" required minlength="3" maxlength="30" pattern="[a-zA-Z0-9]+" placeholder='Username' title="Numbers or letters only. Minimum 3 characters.">
        <input type="submit" name="check" value="Create">
    </form>
    </div>
<?php } ?>


<h2>Order the physical version</h2>
<p>The game has to be fully tested and finalized first before the printing can be done. Once the printing is done you will be able to order the game on amazon.com.</p>

<h2>Print & Play</h2>
<p>You can print and play the game now. What you need:</p>
<ul>
    <li>Card sleeves in 2 different colors with cardboard cards behind them (for example MtG and Pokemon cards).</li>
    <li>2 printed copies of the game deck, <a href="/print/cards" target="_blank">print on this link</a>.</li>
</ul>

<h2>Play with the creator</h2>
<p>You can come to Marbella in Spain and play with the game designer Jim and take part of the game testing. <a href="/contact">Contact him</a>.</p>
