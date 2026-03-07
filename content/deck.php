<?php

$deck_id = (int) ($_GET['id'] ?? 0);

if ($deck_id === 0) {
    dieWith404('<p>This deck does not exist or has been removed.</p>');
}

$pdo = PDOWrap::getInstance();

// Fetch deck and creator info
$row = $pdo->run("
    SELECT decks.*, users.username 
    FROM decks 
    JOIN users ON decks.user_id = users.id 
    WHERE decks.id = ? LIMIT 1
", [$deck_id])->fetch();

if (!isset($row['id'])) {
    dieWith404('<p>This deck does not exist or has been removed.</p>');
}

if ($row['public'] == 0 && ($logged_in == [] || $logged_in['id'] != $row['user_id'])) {
    dieWith404('<p>This deck is private.</p>');
}

if (isset($_POST['copy_deck']) && $logged_in != []) {
    $new_deck_name = substr($row['deck_name'] . ' Copy', 0, 25);
    $pdo->run("INSERT INTO decks (user_id, deck_name, commander, card_count, time_saved, time_used, cards, public) VALUES (?, ?, ?, ?, ?, ?, ?, ?);", [$logged_in['id'], $new_deck_name, $row['commander'], $row['card_count'], TIMESTAMP, 0, $row['cards'], 0]);
    $new_deck_id = $pdo->lastInsertId();
    header("Location: /account/deck?id=" . $new_deck_id);
    die();
}

$cards = json_decode($row['cards'], true);

// Get game data
$commander_data = commanderData();
$cards_data = getCardData();
// Sort by cost https://stackoverflow.com/a/22393663
usort($cards_data, function ($a, $b) {
    return ( $a['cost'] < $b['cost'] ? 1 : -1 );
});


$title_tag = $row['deck_name'] . ' Deck by ' . $row['username'] . ' | TheSpaceWar.com';
require(ROOT.'view/head.php');

require(ROOT.'view/deck-style.php');

?>

<div style="text-align:center;margin-bottom:40px;">
    <h1>Deck: <?= htmlspecialchars($row['deck_name']) ?></h1>
    <p>Created by: <strong><a href="/users/<?= urlencode($row['username']) ?>"><?= htmlspecialchars($row['username']) ?></a></strong><br>
    Last updated: <?= date('Y-m-d', $row['time_saved']) ?></p>
    <?php if ($logged_in === []) { ?>
        <p>[ <a href="/register">Register</a> to build your own deck! ]</p>
    <?php } else { ?>
        <p>[ <a href="/account/deck">Go to your decks</a> ]</p>
        <?php if ($logged_in['id'] != $row['user_id']) { ?>
            <form method="post" action="" style="margin-top:20px;">
                <input type="submit" name="copy_deck" value="Copy this deck" class="big-button" style="padding:10px 20px;cursor:pointer;margin:auto;">
            </form>
        <?php } ?>
    <?php } ?>
</div>

<?php display_deck($row, $cards_data, $cards); ?>

