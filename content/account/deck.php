<?php

// I suppose functions should be moved to functions.php but these 
// are only used in this file and I like to have all related code on the same page.

function show_cards_by_type_saved_img(array $cards_data, array $cards, string $type) : int
{
    $card_count = 0;
    echo '<div class="saved">';
    foreach ($cards_data as $card => $value) {
        if ($value['type'] != $type) continue;
        if (!isset($cards[$value['id']])) continue;
        echo "<div>";
        for ($i=0; $i < $cards[$value['id']]; $i++) {
            echo "<img style='width:100px;";
            $card_count++;
            if ($i > 0) {
                $margin_top = min(46, 23*$i);
                echo "margin-left:-100px;margin-top:".$margin_top."px;position:absolute;";
            }
            echo "' src=\"https://images.thespacewar.com/card-".$value['id'].".jpg\">";
            if ($value['id'] == 78 && $cards[$value['id']] > 4) {
                echo "<div style='position:absolute;margin-left:-68px;margin-top:95px;font-weight:bold;background-color:black;padding:6px;height:23px;width:30px;'>".$cards[$value['id']]."</div>";
            }
        }
        echo "</div>";
    }
    echo '</div>';
    return $card_count;
}

function show_cards_by_type_saved_text(array $cards_data, array $cards, $type) : int
{
    $count = 0;
    foreach ($cards_data as $card => $value) {
        if ($value['type'] != $type) continue;
        if (!isset($cards[$value['id']])) continue;
        echo $cards[$value['id']]." ".$value['title']."<br>";
        $count += $cards[$value['id']];
    }
    return $count;
}

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
    foreach ($cards_data as $card => $value) {
        if ($value['type'] != $type) continue;
        echo "<div><img src=\"https://images.thespacewar.com/card-".$value['id'].".jpg\"><br>
            <div><a href='javascript:void(0)' onclick=\"javascript:addRemoveCard(".$value['id'].", 'remove')\" id='remove-".$value['id']."' style='display:none;'>-</a></div>
            <div><span id='count-".$value['id']."'>";
            echo $cards[$value['id']] ?? 0;
            echo "</span></div>
            <div><a href='javascript:void(0)' onclick=\"javascript:addRemoveCard(".$value['id'].", 'add')\" id='add-".$value['id']."'>+</a></div>
            <input type='hidden' name='cardcount-".$value['id']."' value='";
            echo $cards[$value['id']] ?? 0;
            echo "' id='cardcount-".$value['id']."'></div>";
    }
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


    if ($edit_deck) {
        $pdo->run("UPDATE decks SET deck_name = ?, commander = ".$_POST['commander'].", card_count = ".$total_cards.", time_saved = ".TIMESTAMP.", cards = ? WHERE id = ".$deck_id." AND user_id = ".$logged_in['id'].";", [$_POST['deck_name'], $cards_as_json]);
    } else {
        $pdo->run("INSERT INTO decks (user_id, deck_name, commander, card_count, time_saved, time_used, cards) VALUES (?, ?, ?, ?, ?, ?, ?);", [$logged_in['id'], $_POST['deck_name'], $_POST['commander'], $total_cards, TIMESTAMP, 0, $cards_as_json]);
        $deck_id = $pdo->lastInsertId();
    }

    header("Location: /account/deck?id=".$deck_id);
    die();
}


$title_tag = 'Deck Building | TheSpaceWar.com';
require(ROOT.'view/head.php');

?>

<style>
    <?php if (isset($_GET['id']) || isset($_GET['create'])) { ?>
    .wrap {
        width: 900px;
    }
    <?php } ?>
    h2 {
        text-align: center;
        margin-bottom:30px;
        font-size: 31px;
    }
    .card-selection div {
        height:280px;
        width:143px;
        margin-right: 30px;
        display:inline-table;
        text-align:center;
        font-size: 31px;
        font-family: courier;
        font-weight: bold;
    }
    .card-selection div div {
        height:15px;
        width:0;
        display:inline-block;
    }
    .card-selection div a {
        color:#fff;
        text-decoration: none;
        background: none;
    }

    .card-selection img {
        height:200px;
    }
    .card-selection img:hover {
        transform:scale(1.8);
        box-shadow:0px 0px 150px 10px #000;
        transition: all 0.2s ease;
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
    .saved {
        display: inline;
        margin-left:38px;
    }
    .saved div {
        display: inline-block;
        height:191px;
        width: 107px;
    }
    .saved div img:hover {
        transform:scale(2);
        box-shadow:0px 0px 150px 10px #000;
        transition: all 0.1s ease;
        z-index: 99999;
    }
</style>

<?php

if ($edit_deck) {

    echo "<p>[ <a href='/account/deck'>← Back to your decks</a> ]</p>";

    echo "<h1>Your Deck \"".$row['deck_name']."\"</h1>";

    echo "<p><a href='#edit'>Edit</a> your deck or <a href='".generate_print_url($row['commander'], $cards)."' target='_blank'>print this deck</a>.</p>";

    echo "<img style='width:100px;' src='https://images.thespacewar.com/commander-".$row['commander'].".png'>";

    show_cards_by_type_saved_img($cards_data, $cards, 'Spaceship');
    show_cards_by_type_saved_img($cards_data, $cards, 'Event');
    show_cards_by_type_saved_img($cards_data, $cards, 'Duration');
    show_cards_by_type_saved_img($cards_data, $cards, 'Missile');
    show_cards_by_type_saved_img($cards_data, $cards, 'Defense');

    $total_cards = 0;
    echo "<div style='margin:40px;font-family:monospace;font-size:18px;'>";
    echo '<br>--- Spaceship cards ---<br>';
    $count = show_cards_by_type_saved_text($cards_data, $cards, 'Spaceship');
    echo "= ".$count."<br>";
    $total_cards += $count;
    echo '<br>--- Event cards ---<br>';
    $count = show_cards_by_type_saved_text($cards_data, $cards, 'Event');
    echo "= ".$count."<br>";
    $total_cards += $count;
    echo '<br>--- Duration cards ---<br>';
    $count = show_cards_by_type_saved_text($cards_data, $cards, 'Duration');
    echo "= ".$count."<br>";
    $total_cards += $count;
    echo '<br>--- Missile cards ---<br>';
    $count = show_cards_by_type_saved_text($cards_data, $cards, 'Missile');
    echo "= ".$count."<br>";
    $total_cards += $count;
    echo '<br>--- Defense cards ---<br>';
    $count = show_cards_by_type_saved_text($cards_data, $cards, 'Defense');
    echo "= ".$count."<br>";
    $total_cards += $count;
    echo '____________________________<br>';
    echo $total_cards." total cards<br>";
    echo "</div>";

    echo "<h1 id='edit'>Edit your deck</h1>";


} elseif (!isset($_GET['id']) && !isset($_GET['create'])) {

    echo "<p>[ <a href='/account/'>← Back to Account</a> ]</p>";

    echo "<h1>Your Decks</h1>";

    echo "<p>READ THIS: At the moment you cannot play with your constructed deck online, it will be possible in a few months. In the meanwhile you can print and play offline. <a href='/constructed'>Rules for Constructed Play</a>.</p>";

    $result = $pdo->run("SELECT * FROM decks WHERE user_id = ? ORDER BY time_saved DESC", [$logged_in['id']])->fetchAll();
    foreach($result as $row) {
        echo "<div style='clear:both;height:120px;margin-bottom:20px;'><img src='https://images.thespacewar.com/commander-".$row['commander'].".png' style='height:100px;float:left;margin-right:20px;'>";
        echo "<h3><a href='/account/deck?id=".$row['id']."'>".$row['deck_name']."</a></h3>";
        echo "<p> ".$row['card_count']." cards, last edited: ".date('Y-m-d', $row['time_saved'])."</p></div>";
    }

    echo "<p style='text-align:center;'><a href='/account/deck?create' class='big-button'>Create New Deck</a></p>";

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
</ul>


<hr>

<script>

// Make anchor link to go higher up
// https://stackoverflow.com/a/17535094
window.addEventListener("hashchange", function () {
    window.scrollTo(window.scrollX, window.scrollY - 100);
});

var deckCount = <?=$row['card_count'] ?? 0 ?>;

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
    if (deckCount > 70) {
        document.getElementById('deckCount').style.color = 'red';
        document.getElementById('save-button').disabled = true;
    }
    if ((id == 78 && cardCount == 10) || (id != 78 && cardCount == 3)) {
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

    return true 
}
</script>


<form method="post" action="<?= $form_action_url ?>">

<div class='bottom-bar'>
    Cards selected: <strong style="color:red;" id="deckCount"><?=$row['card_count'] ?? 0 ?></strong><br>
    Requirement: 50-70<br>
    <input type="submit" name='submit' id='save-button' disabled value="Save deck">
</div>

<p>Deck name:
<input type="text" style="width:300px" name="deck_name" required maxlength="25" pattern="[a-zA-Z0-9]+" value="<?=$row['deck_name'] ?? ''?>" title="Only alphanumerical characters please."> (only a-zA-Z0-9)
</p>

<div class="card-selection">

    <h2>Select 1 commander</h2>
    <?php
    foreach ($commander_data as $commander_slug => $commander) {
        echo "<div style='height:220px'><a href='javascript:void(0)' onclick='javascript:chooseCommander(".$commander['id'].")'><img id='commander-".$commander['id']."'  src='https://images.thespacewar.com/commander-".$commander['id'].".png'></a></div>";
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

<?php } ?>



