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

<table>


<?php 
$winners_array = winnersArray();
foreach ($winners_array as $key => $value) {
    echo '<tr><td>'.$key.'</td><td class="nobr">🏆 <a href="/users/'.$value['first_username'].'">'.$value['first_username'].'</a> <img src="https://staticjw.com/redistats/images/flags/'.$value['first_country'].'.gif"></td>';
    if ( isset( $value['second_username'] ) ) {
        echo '<td class="nobr">🥈 <a href="/users/'.$value['second_username'].'">'.$value['second_username'].'</a> <img src="https://staticjw.com/redistats/images/flags/'.$value['second_country'].'.gif"></td>';
    } else {
        echo '<td></td>';
    }
    if ( isset( $value['third_username'] ) ) {
        echo '<td class="nobr">🥉 <a href="/users/'.$value['third_username'].'">'.$value['third_username'].'</a> <img src="https://staticjw.com/redistats/images/flags/'.$value['third_country'].'.gif"></td>';
    } else {
        echo '<td></td>';
    }
    echo '</tr>';
}

?>
</table>


<h2>Most Credits Earned</h2>

<table>
<tr><th>Username</th><th>Credits Earned</th></tr>

<?php
$pdo = PDOWrap::getInstance();
$result = $pdo->run("SELECT * FROM users ORDER BY credits_earned DESC LIMIT 15;")->fetchAll();
foreach($result as $row) {
    echo '<tr>
    <td class="nobr"><a href="/users/'.$row['username'].'">'.$row['username'].'</a> <img src="https://staticjw.com/redistats/images/flags/'.$row['country'].'.gif"></td>
    <td><strong>'.$row['credits_earned'].'</strong></td>
    </tr>';
}
?>

</table>


<h2>How the Rating Score is calculated</h2>

<p>This is the formula used:<br>
<code>win_rate*(min(win_count,20)/20)*100</code><br>
(Basically win rate but win count is also used if less than 20 wins).</p>

<h3>Scoring of a match is ignored if:</h3>

<ul>

<?php
foreach (scoringIgnoredReasons() as $reason) {

    if ( $reason['active'] === false ) continue;
    
    echo "<li>".$reason['desc']."</li>";
}
?>
</ul>

