<?php
$title_tag = 'Weekly Offer | TheSpaceWar.com';
include(ROOT.'view/head.php');
?>

<style>
.frame {width:313px;overflow:hidden;border: 22px solid transparent;float:left;margin-right: 50px;margin-bottom: 50px;}
.frame img {height:458px;width:328px;margin-left:-8px;margin-top:-8px;margin-bottom:-8px;}
.frame.gold {border-image:url(https://images.thespacewar.com/frame/gold.png) 15 round;}
</style>

<h1>Weekly Offer</h1>

<?php

$week_number = (int) date( 'W' );
$weekly_offer = getWeeklyOffer( $week_number );
$rare_cards = getRareCards();

if ( isset( $_POST['purchase'] ) && $_POST['purchase'] == 1 ) {

    if ( $logged_in === [] ) {
        echo "<div class='error'>You first have to login to buy this offer.</div>";
    } else {

        $pdo = PDOWrap::getInstance();
        $accunt_row = $pdo->run( "SELECT * FROM users WHERE id = ? LIMIT 1", [$logged_in['id']] )->fetch();
        $saldo = $accunt_row['credits_earned']-$accunt_row['credits_spent'];

        if ( $saldo < $weekly_offer['cost'] ) {
            echo "<div class='error'>Sorry buy you don't have enough credits. You only have ".$saldo." credits left.</div>";

        } elseif ( $accunt_row['weekly_offer_used'] == $week_number ) {
            echo "<div class='error'>Sorry but you have already bought this offer.</div>";

        } else {
            $pdo->run( "UPDATE users SET weekly_offer_used = ?, credits_spent = credits_spent+".$weekly_offer['cost']." WHERE id = ?", [$week_number, $logged_in['id']] );
            $pdo->run( "INSERT INTO credits_spent (`user_id`, `timestamp`, `amount`, `description`) VALUES (?, ?, ?, ?);", [$logged_in['id'], TIMESTAMP, $weekly_offer['cost'], 'Weekly Offer: Gold framed '.$weekly_offer['name']] );
            purchaseCard( $logged_in['id'], 2, $weekly_offer['id'] );
            echo "<div class='good'>Congrats! You made a good purchase. <a href='/account/your-cards'>See your collection</a>.</div>";
        }
    }
}


echo '<div class="frame gold"><img loading=lazy src="'.getCardImageURL( $weekly_offer['id'] ).'"></div>';

echo '<p style="padding-top:50px;">The offer of the week is a gold framed '.$weekly_offer['name'].' for only <strong>'.$weekly_offer['cost'].'</strong> credits.</p>';

if ( in_array( $weekly_offer['id'], $rare_cards ) ) {
    echo '<p>This card is <strong>rare!</strong></p>';
}

?>

<form action="" method="post" id='buy1'>
<p><a href="#" onclick="document.getElementById('buy1').submit();" class='big-button'>Buy it now</a></p>
<input type="hidden" name="purchase" value="1">
</form>

<p style="margin-top:20px;">Limited 1 purchase per person.</p>

<p>The offer is automatically generated each week with a random card and a random cost between 15-40 credits. Check back regularly.</p>

<div style="clear:both;"></div>

<h2>What are Gold Cards?</h2>

<p>Silver, Gold and Diamond cards works the same as Normal cards when playing (this is not a pay-to-win game) but if you win with a deck composed of framed cards you earn extra credits. A gold card can be converted to any other silver card and three copies of the same gold card can either be converted to a Diamond version of the same card (which you can sell for real money) or any other gold card.</p>

<p>For more information see the page <a href='/account/your-cards'>Your Online Cards</a>.</p>

