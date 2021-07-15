<?php

if ($logged_in === []) {
    require(ROOT.'view/login.php');
    require(ROOT.'view/footer.php');
    die();
}


$title_tag = 'Your Online Cards | TheSpaceWar.com';
require(ROOT.'view/head.php');

$pdo = PDOWrap::getInstance();
$rare_cards = getRareCards();

?>

<style>
.frame {width:209px;overflow:hidden;border: 15px solid transparent;margin-right:20px;margin-bottom:20px;display:inline-block;}
.frame img {height:305px;width:219px;margin-left:-5px;margin-top:-5px;margin-bottom:-5px;}
.frame.silver {border-image:url(https://images.thespacewar.com/frame/silver.png) 15 round;}
.frame.gold {border-image:url(https://images.thespacewar.com/frame/gold.png) 15 round;}
.frame.diamond {border-image:url(https://images.thespacewar.com/frame/diamond.png) 15 round;}
.cardwrap {width:235px;font-weight:bold;text-align:center;display:inline-block;margin-right:20px;}

</style>

<?php

$accunt_row = $pdo->run("SELECT * FROM users WHERE id = ? LIMIT 1", [$logged_in['id']])->fetch();
$saldo = $accunt_row['credits_earned']-$accunt_row['credits_spent'];


if ( isset( $_POST['purchase'] ) && in_array( $_POST['purchase'], [1, 2] ) ) {
    echo '<h1>Your Purchase</h1>';

    if ( $_POST['purchase'] == 1 ) {
        if ( $saldo < 10 ) {
            echo "<div class='error'>You don't have enough credits.</div>";
        } else {
            $accunt_row['credits_spent'] += 10;
            $pdo->run("UPDATE users SET credits_spent = credits_spent+10 WHERE id = ?", [$logged_in['id']]);
            $pdo->run("INSERT INTO credits_spent (`user_id`, `timestamp`, `amount`, `description`) VALUES (?, ?, ?, ?);", [$logged_in['id'], TIMESTAMP, 10, '2 random silver cards']);
            $card_id = purchaseRandomCard( $logged_in['id'], 1 );
            echo '<div class="frame silver"><img src="'.getCardImageURL( $card_id ).'"></div>';
            $card_id = purchaseRandomCard( $logged_in['id'], 1 );
            echo '<div class="frame silver"><img src="'.getCardImageURL( $card_id ).'"></div>';
        }
    }
    if ( $_POST['purchase'] == 2 ) {
        if ( $saldo < 50 ) {
            echo "<div class='error'>You don't have enough credits.</div>";
        } else {
            $accunt_row['credits_spent'] += 50;
            $pdo->run("UPDATE users SET credits_spent = credits_spent+50 WHERE id = ?", [$logged_in['id']]);
            $pdo->run("INSERT INTO credits_spent (`user_id`, `timestamp`, `amount`, `description`) VALUES (?, ?, ?, ?);", [$logged_in['id'], TIMESTAMP, 50, 'Pack of 7 random silver cards and 1 random gold card.']);

            $card_id = purchaseRandomCard( $logged_in['id'], 1 );
            echo '<div class="frame silver"><img src="'.getCardImageURL( $card_id ).'"></div>';
            $card_id = purchaseRandomCard( $logged_in['id'], 1 );
            echo '<div class="frame silver"><img src="'.getCardImageURL( $card_id ).'"></div>';
            $card_id = purchaseRandomCard( $logged_in['id'], 1 );
            echo '<div class="frame silver"><img src="'.getCardImageURL( $card_id ).'"></div>';
            $card_id = purchaseRandomCard( $logged_in['id'], 1 );
            echo '<div class="frame silver"><img src="'.getCardImageURL( $card_id ).'"></div>';
            $card_id = purchaseRandomCard( $logged_in['id'], 1 );
            echo '<div class="frame silver"><img src="'.getCardImageURL( $card_id ).'"></div>';
            $card_id = purchaseRandomCard( $logged_in['id'], 1 );
            echo '<div class="frame silver"><img src="'.getCardImageURL( $card_id ).'"></div>';
            $card_id = purchaseRandomCard( $logged_in['id'], 1 );
            echo '<div class="frame silver"><img src="'.getCardImageURL( $card_id ).'"></div>';
            $card_id = purchaseRandomCard( $logged_in['id'], 2 );
            echo '<div class="frame gold"><img src="'.getCardImageURL( $card_id ).'"></div>';
        }
    }
    // Recalculate the saldo
    $saldo = $accunt_row['credits_earned']-$accunt_row['credits_spent'];
    echo '<hr>';
}


?>

<h1>Your Online Cards</h1>

<h2>Buy Cards</h2>

<p>Saldo: <?=$accunt_row['credits_earned']?> credits earned - <?=$accunt_row['credits_spent']?> credits spent = <?=$saldo?></p>


<form action="" method="post" id='buy1'>
<p><a href="#" onclick="document.getElementById('buy1').submit();" class='big-button'>Buy 2 random silver cards for 10 credits</a></p>
<input type="hidden" name="purchase" value="1">
</form>

<p>A pack consists of 7 random silver cards and 1 random gold card.</p>

<form action="" method="post" id='buy2'>
<p><a href="#" onclick="document.getElementById('buy2').submit();" class='big-button'>Buy a pack for 50 credits</a></p>
<input type="hidden" name="purchase" value="2">
</form>

<h2>Your Normal Cards</h2>

<p>All players has an unlimited amount of normal cards available for free and can create any deck.</p>

<p>Decks that contain any normal card is a normal deck.<br>Each game you win using a normal deck earns you <strong>5</strong> credits.</p>

<h2>Your Silver Cards</h2>

<p>Same as normal cards but with a silver frame.</p>

<p>Decks that only consist of silver cards or higher is a Silver Deck.<br>Each game you win using a Silver Deck earns you <strong>25</strong> credits (coming soon).</p>

<p>Three copies of the same silver card can either be converted to a Gold version of the same card or any other silver card (coming soon).</p>

<?php
$outputs = '';
$result = $pdo->run("SELECT * FROM framed_cards WHERE `user_id` = ? AND frame_type = 1 AND amount > 0 ORDER BY amount DESC;", [$logged_in['id']])->fetchAll();
foreach($result as $row) {
    $output = "<div class='cardwrap'>&nbsp;";
    if ($row['amount'] > 1) {
        $output .= "X ".$row['amount'];
    }
    if ( in_array( $row['card_id'], $rare_cards ) ) {
        $output .= " [ RARE ]";
    }
    $output .= '<div class="frame silver"><img src="'.getCardImageURL( $row['card_id'] ).'"></div></div>';
    if ( $row['card_id'] > 9999 ) { // We show commanders first
        echo $output;
    } else {
        $outputs .= $output;
    }
}
echo $outputs;
?>

<h2>Your Gold Cards</h2>

<p>Same as normal cards but with a gold frame.</p>

<p>Decks that only consist of gold cards or higher is a Gold Deck.<br>Each game you win using a Gold Deck earns you <strong>100</strong> credits (coming soon).</p>

<p>A gold card can be converted to any other silver card (coming soon).</p>

<p>Three copies of the same gold card can either be converted to a Diamond version of the same card or any other gold card (coming soon).</p>

<?php
$outputs = '';
$result = $pdo->run("SELECT * FROM framed_cards WHERE `user_id` = ? AND frame_type = 2 AND amount > 0 ORDER BY amount DESC;", [$logged_in['id']])->fetchAll();
foreach($result as $row) {
    $output = "<div class='cardwrap'>&nbsp;";
    if ($row['amount'] > 1) {
        $output .= "X ".$row['amount'];
    }
    if ( in_array( $row['card_id'], $rare_cards ) ) {
        $output .= " [ RARE ]";
    }
    $output .= '<div class="frame gold"><img src="'.getCardImageURL( $row['card_id'] ).'"></div></div>';
    if ( $row['card_id'] > 9999 ) { // We show commanders first
        echo $output;
    } else {
        $outputs .= $output;
    }
}
echo $outputs;
?>

<h2>Your 💎 Diamond Cards</h2>

<p>Same as normal cards but with a diamond frame.</p>

<p>Decks that only consist of diamond cards is a Diamond Deck.<br>Each game you win using a Diamond Deck earns you <strong>500</strong> credits (coming soon).</p>

<p>Three copies of the same diamond card can be converted to any other diamond card (coming soon).</p>

<p>In the future: Can be minted as a Non-fungible token (NFT) and sold by you for real crypto money (ETH) on the blockchain. Maximum 5 can be minted per month per account. Can be bought on the blockchain as well.</p>

<?php
$outputs = '';
$result = $pdo->run("SELECT * FROM framed_cards WHERE `user_id` = ? AND frame_type = 3 AND amount > 0 ORDER BY amount DESC;", [$logged_in['id']])->fetchAll();
foreach($result as $row) {
    $output = "<div class='cardwrap'>&nbsp;";
    if ($row['amount'] > 1) {
        $output .= "X ".$row['amount'];
    }
    if ( in_array( $row['card_id'], $rare_cards ) ) {
        $output .= " [ RARE ]";
    }
    $output .= '<div class="frame diamond"><img src="'.getCardImageURL( $row['card_id'] ).'"></div></div>';
    if ( $row['card_id'] > 9999 ) { // We show commanders first
        echo $output;
    } else {
        $outputs .= $output;
    }
}
echo $outputs;
?>

<h2>FAQ</h2>

<h3>Can I get rich playing The Space War?</h3>
<p>If you are a good regular player and have some luck you will probably be able to earn a good chunk of money.</p>

<h3>What About Rarity of Cards?</h3>
<p>With "random card" is meant the same as if you would mix all the cards of the preset decks together and pick 1 card at random. Meaning that for example there is a higher chance to receive Drone (15 copies) than for example Titan (1 copy). The mixing is reset for each card you receive, even if you receive several the same time.</p>

<?php
$result = $pdo->run("SELECT * FROM credits_spent WHERE `user_id` = ? ORDER BY id DESC;", [$logged_in['id']])->fetchAll();
if (count($result) > 0) {
    echo '<h2>Credits Spent</h2><table>';
    echo '<tr><td>Date</td><td>Amount</td><td>Info</td></tr>';
    foreach($result as $row) {
        echo '<tr><td>'.date('Y-m-d', $row['timestamp']).'</td><td>'.$row['amount'].'</td><td>'.$row['description'].'</td></tr>';
    }
    echo '</table>';
}
?>




