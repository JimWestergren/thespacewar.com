<?php
$title_tag = 'Affiliate | TheSpaceWar.com';
include(ROOT.'view/head.php');
?>

<h1>Affiliate</h1>

<p>The Space War is a new card game similar to Hearthstone and Magic the Gathering but with a list of improvements. See more about the game <a href='/'>on the homepage</a>.</p>

<p>We offer a <strong>40% lifetime commission</strong> of all sales of your referrals. Register your free account and use your unique referral link when promoting this website.</p>

<p>People registering using your referral link will receive 50 credits which is nice for you to offer them. You will also receive credits (10 + 10%).</p>

<h2>Your referrers and your referral link</h2>

<?php

if ( $logged_in === [] ) {
    echo '<p>Please login to see this info.</p>';
} else {
    echo '<p>Your referral link: <code>https://thespacewar.com/?referrer='.$logged_in['id'].'</code></p>';
    echo '<h3>Your 50 Latest Referrers:</h3>';
    echo getLatestReferralsTable( $logged_in['id'], 50 )['html_output'];
}

?>

<h2>What kind of real money are we talking about?</h2>

<p>Currently the only real money is the selling of <a href="/first-edition">First Edition Cards as NFTs</a>. There will only ever exist 102 (one for each card) and there is no doubt that all of them are going to be sold and increase in value - the only question is how fast they will be sold and who will get the commission.</p>

<p><strong>Example</strong>: an NFT with the price 0.5 ETH is sold. 40% of that is currently $723 which will be paid by PayPal to the affiliate.</p>

<p>In the future there will be more real money sales, and the commission will cover that as well. The only exception is sales of physical products which will not be covered.</p>

<h2>If the referral did not use the referral link?</h2>

<p>Tell them to go to <a href="/account/edit">Account Edit</a> and fill in your username under the field "Referrer". </p>
