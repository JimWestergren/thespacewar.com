<?php
$title_tag = 'Leaderboard | TheSpaceWar.com';
require(ROOT.'view/head.php');

?>

<h1>Leaderboard</h1>

<p>Please note: The Space War has not yet officially launched. There are only a few players in this testing phase.</p>

<!--<p>An award of $100 will be paid with paypal to the player with the highest total points of the Quarterly rating (not counting Alvin and Jim).</p>-->


<?=leaderboardTable('monthly'); ?>

<?=leaderboardTable('quarterly'); ?>

<h2 style='margin-top: 70px;'>Past winners</h2>

<p>
<?php 
$winners_array = winnersArray();

foreach ($winners_array as $key => $value) {
    echo ''.$key.': 🏆 <a href="/users/'.$value['first_username'].'">'.$value['first_username'].'</a> <img src="https://staticjw.com/redistats/images/flags/'.$value['first_country'].'.gif"> | 🥈 <a href="/users/'.$value['second_username'].'">'.$value['second_username'].'</a> <img src="https://staticjw.com/redistats/images/flags/'.$value['second_country'].'.gif"><br>';
}

?>
</p>
<h2>How the Rating Score is calculated</h2>

<!--<p>Total Score includes the referral bonus and the 50 bonus if you win over the bot.</p>-->

<p>This is the formula used:<br>
<code>win_rate*(min(win_count,20)/20)*100</code><br>
(Basically win rate but win count is used if less than 20 wins).</p>

<!--<p>Referral bonus: <code>min(sum_of_all_ratings_of_referreals/10, current_rating/4))</code> (10% of the sum of all the ratings of the referrals but maximum 25% of the rating of the user).</p>-->
