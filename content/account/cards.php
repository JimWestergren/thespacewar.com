<?php

if ($logged_in === []) {
    require(ROOT.'view/login.php');
    require(ROOT.'view/footer.php');
    die();
}


$title_tag = 'Your Online Cards | TheSpaceWar.com';
require(ROOT.'view/head.php');

?>


<h1>Your Online Cards</h1>

<p>This page is SUGGESTION AND PLANNING ONLY. Not for public release. Not linked to from anywhere.</p>

<p>If you are reading this please send your comments to Jim on Discord with your opinions.</p>

<h2>Normal Cards</h2>

<p>All players has an unlimited amount of normal cards available for free and can create any deck.</p>

<p>Decks that contain any normal card is a normal deck.<br>Each game you win using a normal deck earns you 5 credits.</p>

<h2>Silver Cards</h2>

<p>Spend 5 credits to purchase a random silver card (5% chance it is a gold card). Same as normal cards but with a silver frame.</p>

<p>Decks that only consist of silver cards or higher is a Silver Deck.<br>Each game you win using a Silver Deck earns you 25 credits.</p>

<p>Three copies of the same silver card can either be converted to a Gold version of the same card or any other silver card.</p>

<h2>Gold Cards</h2>

<p>Spend 25 credits to purchase a random gold card. Same as normal cards but with a gold frame.</p>

<p>Decks that only consist of gold cards or higher is a Gold Deck.<br>Each game you win using a Gold Deck earns you 100 credits.</p>

<p>Three copies of the same gold card can either be converted to a Diamond version of the same card or any other gold card.</p>

<p>Gold cards can be dusted for 15 credits.</p>


<h2>💎 Diamond Cards</h2>

<p>Same as normal cards but with a diamond frame.</p>

<p>Decks that only consist of diamond cards is a Diamond Deck.<br>Each game you win using a Diamond Deck earns you 500 credits.</p>

<p>Three copies of the same diamond card can be converted to any other diamond card.</p>


<p>Can be minted as a Non-fungible token (NFT) and sold by you for real crypto money (ETH) on the blockchain. Maximum 5 can be minted per month per account. Can be bought on the blockchain as well.</p>

<h2>FAQ</h2>

<h3>Can I get rich playing The Space War?</h3>
<p>If you are a good regular player and have some luck you will probably be able to earn a good chunk of money.</p>

<h3>What About Rarity of Cards?</h3>
<p>With "random card" is meant the same as if you would mix all the cards of the preset decks together and pick 1 card at random. Meaning that for example there is a higher chance to receive Drone (15 copies) than for example Titan (1 copy). The mixing is reset for each card you buy, even if you buy several the same time.</p>


<style>
.frame {width: 209px;overflow: hidden;border: 15px solid transparent;border-image:url(http://develop.innova-cube.com/thespacewar/border-2.png) 25 round;display:inline-block;}
.frame img {height:305px;width:219px;margin-left: -5px;margin-top: -5px;margin-bottom: -5px;}

</style>

<div class="frame"><img src="https://images.thespacewar.com/card-78.jpg"></div>
<div class="frame"><img src="https://images.thespacewar.com/card-78.jpg"></div>
<div class="frame"><img src="https://images.thespacewar.com/card-78.jpg"></div>