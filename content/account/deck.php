<?php


function generate_print_url(int $commander, array $cards) : string
{
    $print_url = 'https://thespacewar.com/print/cards?commander='.$commander.'&card_list=';
    foreach ($cards as $card_id => $count) {
        for ($i=0; $i < $count; $i++) {
            $print_url .= $card_id.',';
        }
    }
    return $print_url;
}

function cards_as_json(array $cards_data, int &$total_cards) : string
{
    $a = [];

    foreach ($cards_data as $card => $value) {
        $value['id'] = (int) $value['id'];
        if ($_POST['cardcount-'.$value['id']] == 0) continue;
        $a[$value['id']] = (int) $_POST['cardcount-'.$value['id']];
        $total_cards += $a[$value['id']];
    }

    return json_encode($a);
}

function show_cards_by_type(array $cards_data, array $cards, string $type) : void
{
    global $framed_cards;
    
    foreach ($cards_data as $card => $value) {

        if ($value['type'] != $type) continue;

        echo "<div class='card'>";
            echo '<div class="frames-data">';
            if ( isset( $framed_cards[$value['id']][1] ) ) {
                echo '<span style="color:#C0C0C0" title="You have '.$framed_cards[$value['id']][1].' in Silver frame">';
                for ($i=0; $i < $framed_cards[$value['id']][1]; $i++) { 
                    echo '•';
                }
                echo '</span>';
            }
            if ( isset( $framed_cards[$value['id']][2] ) ) {
                echo '<span style="color:#FFD700" title="You have '.$framed_cards[$value['id']][2].' in Gold frame">';
                for ($i=0; $i < $framed_cards[$value['id']][2]; $i++) { 
                    echo '•';
                }
                echo '</span>';
            }
            if ( isset( $framed_cards[$value['id']][3] ) ) {
                echo '<span style="color:#b9f2ff" title="You have '.$framed_cards[$value['id']][3].' in Diamond frame">';
                for ($i=0; $i < $framed_cards[$value['id']][3]; $i++) { 
                    echo '•';
                }
                echo '</span>';
            }
            echo '&nbsp;</div>';
            echo "<a href='/cards/".$value['slug']."' target='_blank'><img src=\"https://images.thespacewar.com/card-".$value['id'].".jpg\"></a>";
            echo "<div><a href='javascript:void(0)' onclick=\"javascript:addRemoveCard(".$value['id'].", 'remove')\" id='remove-".$value['id']."' style='display:none;'>-</a></div>";
            echo "<div><span id='count-".$value['id']."'>";
            echo $cards[$value['id']] ?? 0;
            echo "</span></div>";
            echo "<div><a href='javascript:void(0)' onclick=\"javascript:addRemoveCard(".$value['id'].", 'add')\" id='add-".$value['id']."'>+</a></div>";
            echo "<input type='hidden' name='cardcount-".$value['id']."' value='";
            echo $cards[$value['id']] ?? 0;
            echo "' id='cardcount-".$value['id']."'>";
        echo "</div>";
    }
}

function get_framed_cards( int $logged_in_id ) : array
{
    $pdo = PDOWrap::getInstance();

    $framed_cards = [];

    $result = $pdo->run("SELECT card_id, frame_type, amount FROM framed_cards WHERE `user_id` = ? AND amount > 0;", [$logged_in_id])->fetchAll();
    foreach($result as $row) {
        $framed_cards[$row['card_id']][$row['frame_type']] = $row['amount'];
    }

    return $framed_cards;
}

//////////////// End of functions ///////////////////

if ($logged_in === []) {
    require(ROOT.'view/login.php');
    require(ROOT.'view/footer.php');
    die();
}

$pdo = PDOWrap::getInstance();


if (isset($_GET['id'])) {
    $_GET['id'] = (int) $_GET['id'];
    $row = $pdo->run("SELECT * FROM decks WHERE id = ? AND user_id = ? LIMIT 1", [$_GET['id'], $logged_in['id']])->fetch();

    if (!isset($row['id'])) {
        header("Location: /account/deck");
        die();
    }
    $edit_deck = true;
    $deck_id = (int) $row['id'];
    $cards = json_decode($row['cards'], true);
    $form_action_url = '/account/deck?id='.$deck_id; // Getting rid of the #edit after submit

    $cookie_value['deck_id'] = $deck_id;
    $cookie_value['deck_name'] = $row['deck_name'];
    $cookie_value['commander'] = (int) $row['commander'];
    $cookie_value['card_count'] = (int) $row['card_count'];
    $cookie_value['cards'] = $cards;
    $cards_json = json_encode($cards);
    $cookie_value['hash'] = md5($deck_id . $row['commander'] . $cards_json . SECRET_SALT_CONSTRUCTED_DECK);
    $cookie_value = json_encode($cookie_value);
    set_cookie('constructed_deck', $cookie_value, TIMESTAMP+(3600*24*100));

    if (isset($_POST['delete_deck'])) {
        $pdo->run("DELETE FROM decks WHERE id = ? AND user_id = ?", [$deck_id, $logged_in['id']]);
        header("Location: /account/deck");
        die();
    }

} else {
    $edit_deck = false;
    $cards = [];
    $form_action_url = '/account/deck?create';
}


$commander_data = commanderData();

$cards_data = getCardData();
// Sort by cost https://stackoverflow.com/a/22393663
usort($cards_data, function ($a, $b) {
    return ( $a['cost'] < $b['cost'] ? 1 : -1 );
});



if (isset($_POST['submit'])) {

    $_POST['commander'] = (int) $_POST['commander'];

    $total_cards = 0;
    $cards_as_json = cards_as_json($cards_data, $total_cards);


    $_POST['public'] = isset($_POST['public']) ? 1 : 0;

    if ($edit_deck) {
        $pdo->run("UPDATE decks SET deck_name = ?, commander = ".$_POST['commander'].", card_count = ".$total_cards.", time_saved = ".TIMESTAMP.", public = ".$_POST['public'].", cards = ? WHERE id = ".$deck_id." AND user_id = ".$logged_in['id'].";", [$_POST['deck_name'], $cards_as_json]);
    } else {
        $pdo->run("INSERT INTO decks (user_id, deck_name, commander, card_count, time_saved, public, time_used, cards) VALUES (?, ?, ?, ?, ?, ?, ?, ?);", [$logged_in['id'], $_POST['deck_name'], $_POST['commander'], $total_cards, TIMESTAMP, $_POST['public'], 0, $cards_as_json]);
        $deck_id = $pdo->lastInsertId();
    }

    header("Location: /account/deck?id=".$deck_id);
    die();
}


$title_tag = 'Deck Building | TheSpaceWar.com';
require(ROOT.'view/head.php');

$framed_cards = get_framed_cards( $logged_in['id'] );

?>

<?php require(ROOT.'view/deck-style.php'); ?>
<style>
    .card-selection .card {
        width:143px;
        margin-right: 30px;
        display:inline-table;
        text-align:center;
        font-size: 31px;
        font-family: courier;
        font-weight: bold;
        margin-bottom: 30px;
    }
    .card-selection .card div {
        display:inline-block;
        padding: 0 10px 0 10px;
    }
    .card-selection .card a {
        color:#fff;
        text-decoration: none;
        background: none;
        transition: none;
    }
    .card-selection img {
        height:200px;
    }
    .card-selection .card:hover {
        transform:scale(2);
        transition: all 0.2s ease;
        background-color: #000;
    }
    .card-selection .card:hover div {
        width: 20px;
    }
    .bottom-bar {
        font-size::17px;
        position:fixed;
        right:0;
        bottom:0;
        padding:5px 15px;
        color:#333;
        background:#f3f3ff;
    }
    .selected-commander {
        padding:10px;
        border:3px dashed #b7c9ff;
    }
</style>

<?php

if ($edit_deck) {

    echo "<p>[ <a href='/account/deck'>← Back to your decks</a> ]</p>";

    echo "<h1>Your Deck</h1>";

    echo "<p style='text-align:right;'>";
    if (isset($row['public']) && $row['public'] == 1) {
        echo "<a href='/deck?id=".$deck_id."' target='_blank'>Public link to this deck</a> | ";
    }
    echo "<a href='#edit'>Edit</a> your deck or <a href='".generate_print_url($row['commander'], $cards)."' target='_blank'>print this deck</a>.</p>";

    display_deck($row, $cards_data, $cards);

    echo "<h1 id='edit'>Edit your deck</h1>";


} elseif (!isset($_GET['id']) && !isset($_GET['create'])) {

    echo "<h1>Your Decks</h1>";

    echo "<p>READ THIS: At the moment you cannot play with your constructed deck online, it will be possible in a few months.<br>In the meanwhile you can create decks and print and play them offline. <a href='/constructed'>Rules for Constructed Play</a>.</p>";

    echo "<p style='text-align:center;'><a href='/account/deck?create' class='big-button'>Create New Deck</a></p>";

    $result = $pdo->run("SELECT * FROM decks WHERE user_id = ? ORDER BY time_saved DESC", [$logged_in['id']])->fetchAll();
    foreach($result as $row) {
        echo "<div style='clear:both;height:120px;margin-bottom:20px;'><img src='https://images.thespacewar.com/commander-".$row['commander'].".png' style='height:100px;float:left;margin-right:20px;'>";
        echo "<h3><a href='/account/deck?id=".$row['id']."'>".$row['deck_name']."</a></h3>";
        $visibility = $row['public'] == 1 ? '<span style="color:#6a6;">Public</span>' : '<span style="color:#a66;">Private</span>';
        echo "<p> ".$row['card_count']." cards, last edited: ".date('Y-m-d', $row['time_saved'])." | Visibility: ".$visibility."</p></div>";
    }

    require(ROOT.'view/footer.php');
    die();

} else {

    echo "<p>[ <a href='/account/deck'>← Back to your decks</a> ]</p>";

    echo "<h1>Create New Deck</h1>";

}


?>

<p>This is for playing in the constructed play format (coming soon online).</p>

<ul>
    <li>Maximum of 3 copies of each card except Drone which you can have 10 copies of.</li>
    <li>Select any commander for your deck.</li>
    <li>Deck size must be between 50 and 70 cards.</li>
    <li>The dots above the cards indicates if you have copies with Silver, Gold or Diamond frame. See more <a href="/account/your-cards">on this page</a>.</li>
</ul>


<hr>

<script>

// Make anchor link to go higher up
// https://stackoverflow.com/a/17535094
window.addEventListener("hashchange", function () {
    window.scrollTo(window.scrollX, window.scrollY - 100);
});

var deckCount = <?=$total_cards ?? 0 ?>;

function addRemoveCard(id, action) {
    var commander = document.getElementById('commander').value;
    var cardCount = document.getElementById('count-' + id).innerHTML;
    if (action == 'add') {
        cardCount ++;
        deckCount ++;
        var x = document.getElementById('remove-' + id);
        x.style.display = "inline";
    }
    if (action == 'remove') {
        cardCount --;
        deckCount --;
        var x = document.getElementById('add-' + id);
        x.style.display = "inline";
    }
    document.getElementById('count-' + id).innerHTML = cardCount;
    document.getElementById('cardcount-' + id).value = cardCount;
    document.getElementById('deckCount').innerHTML = deckCount;

    if (deckCount > 49 && deckCount < 71) {
        document.getElementById('deckCount').style.color = 'green';
        if (commander > 0) {
            document.getElementById('save-button').disabled = false;
        }
    }
    if (deckCount > 70 || deckCount < 50) {
        document.getElementById('deckCount').style.color = 'red';
        document.getElementById('save-button').disabled = true;
    }
    if (id == 78 && cardCount == 10) { // Drone
        var x = document.getElementById('add-' + id);
        x.style.display = "none";
    //} else if (id == 6 && cardCount == 1) { // Fast Missile
    //    var x = document.getElementById('add-' + id);
    //    x.style.display = "none";
    } else if (id != 78 && cardCount == 3) {
        var x = document.getElementById('add-' + id);
        x.style.display = "none";
    }
    if (cardCount == 0) {
        var x = document.getElementById('remove-' + id);
        x.style.display = "none";
    }
    return true 
}

function chooseCommander(id) {
    // Remove all, https://stackoverflow.com/a/22270709
    var elems = document.querySelectorAll(".selected-commander");
    [].forEach.call(elems, function(el) {
        el.classList.remove("selected-commander");
    });

    var element = document.getElementById("commander-" + id);
    element.classList.add("selected-commander");

    document.getElementById('commander').value = id;

    if (deckCount > 49 && deckCount < 71) {
        document.getElementById('save-button').disabled = false;
    }

    return true 
}
</script>


<form method="post" action="<?= $form_action_url ?>">

<div class='bottom-bar'>
    Cards selected: <strong style="color:red;" id="deckCount"><?=$total_cards ?? 0 ?></strong><br>
    Requirement: 50-70<br>
    <input type="submit" name='submit' id='save-button' disabled value="Save deck">
</div>

<p>Deck name:
<input type="text" style="width:300px" name="deck_name" required maxlength="25" pattern="[a-zA-Z0-9]+" value="<?=$row['deck_name'] ?? ''?>" title="Only alphanumerical characters please."> (only a-zA-Z0-9)
<br>
<label style="cursor:pointer;"><input type="checkbox" name="public" value="1" <?= (isset($row['public']) && $row['public'] == 1) ? 'checked' : '' ?> style="width:auto;margin-bottom:0px;"> Make this deck public</label>
</p>

<div class="card-selection">

    <h2>Select 1 commander</h2>
    <?php
    foreach ($commander_data as $commander_slug => $commander) {
        echo "<div class='card'>";
        $frame_id = $commander['id']+10000;
        echo '<div class="frames-data">';
        if ( isset( $framed_cards[$frame_id][1] ) ) {
            echo '<span style="color:#C0C0C0" title="You have '.$framed_cards[$frame_id][1].' in Silver frame">';
            for ($i=0; $i < $framed_cards[$frame_id][1]; $i++) { 
                echo '•';
            }
            echo '</span>';
        }
        if ( isset( $framed_cards[$frame_id][2] ) ) {
            echo '<span style="color:#FFD700" title="You have '.$framed_cards[$frame_id][2].' in Gold frame">';
            for ($i=0; $i < $framed_cards[$frame_id][2]; $i++) { 
                echo '•';
            }
            echo '</span>';
        }
        if ( isset( $framed_cards[$frame_id][3] ) ) {
            echo '<span style="color:#b9f2ff" title="You have '.$framed_cards[$frame_id][3].' in Diamond frame">';
            for ($i=0; $i < $framed_cards[$frame_id][3]; $i++) { 
                echo '•';
            }
            echo '</span>';
        }
        echo '&nbsp;</div>';
        echo "<a href='javascript:void(0)' onclick='javascript:chooseCommander(".$commander['id'].")'><img id='commander-".$commander['id']."'  src='https://images.thespacewar.com/commander-".$commander['id'].".png'></a></div>";
    }
    ?>

    <input type='hidden' value='<?=$row['commander'] ?? 0 ?>' name='commander' id='commander'>

    <h2>Spaceship Cards</h2>
    <?php show_cards_by_type($cards_data, $cards, 'Spaceship') ?>

    <h2>Event Cards</h2>
    <?php show_cards_by_type($cards_data, $cards, 'Event') ?>

    <h2>Duration Cards</h2>
    <?php show_cards_by_type($cards_data, $cards, 'Duration') ?>

    <h2>Missile Cards</h2>
    <?php show_cards_by_type($cards_data, $cards, 'Missile') ?>

    <h2>Defense Cards</h2>
    <?php show_cards_by_type($cards_data, $cards, 'Defense') ?>


</div>

</form>


<?php if ($edit_deck) { ?>

    <script>

    var element = document.getElementById("commander-<?= $row['commander']?>");
    element.classList.add("selected-commander");

    document.getElementById('deckCount').style.color = 'green';
    document.getElementById('save-button').disabled = false;

    <?php foreach ($cards as $card_id => $card_count) {
        if (($card_id == 78 && $card_count == 10) || ($card_id != 78 && $card_count == 3)) {
            echo "var x = document.getElementById('add-' + ".$card_id.");";
            echo "x.style.display = 'none';";
        }
        echo "var x = document.getElementById('remove-' + ".$card_id.");";  
        echo "x.style.display = 'inline';";
        } ?>

    </script>
    
    <div style="margin-top: 50px; text-align: center; border-top: 1px solid #333; padding-top: 30px;">
        <form method="post" action="<?= $form_action_url ?>" onsubmit="return confirm('Are you sure you want to delete this deck (<?= $row['deck_name'] ?>)? This action cannot be undone.');">
            <input type="submit" name="delete_deck" value="Delete this deck" style="background-color: darkred; color: white; border: 2px solid red; font-size: 16px; padding: 5px 15px; cursor: pointer;">
        </form>
    </div>

<?php } ?>



