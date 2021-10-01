<!DOCTYPE html>
<html lang="en">
<head>
    <title>Free Printing of The Space War Cards | Print and play</title>
    <style>
    body {padding: 0;margin: 0;}
    .active {font-weight: bold;}

    img {
        page-break-inside: avoid;
        -webkit-region-break-inside: avoid;
        position: relative;
        page-break-before: auto;
        page-break-after: auto;
        display: block;
        height: 88mm;
        width: 63mm;
        float: left;
        image-rendering: auto;
        image-rendering: crisp-edges;
        image-rendering: pixelated;
    }
    p {margin:20px;font-family: verdana;font-size: 12px;}
    @media print { 
       .print {visibility:hidden;}
       p {padding: 0;margin: 0;height:0;}
    }
    </style>
</head>
<body>

<p class="print"><?php if (!isset($_GET['card_list'])) { ?>Here you can print the deck of your choice (60 cards), the 3 Station Rule Cards and the Commanders.<br><?php } ?>
This text will not be visible in the printing.<br>
You need 2 decks to play properly.<br>
Go to "settings -> print" in your browser to preview. Print at 100% and remember to turn off any "fit to page" options in your print settings.<br>
It should be 9 cards per printed page for A4 size otherwise try to decrease the margins in your print settings.<br>
Each card when cut is 63mm x 88mm (same as a MtG card).<br><br>
<?php if (!isset($_GET['card_list'])) { ?>
    Choose deck to print:
    <?=(isset($_GET['deck']) && $_GET['deck'] == 2 ? '<span class="active">The Swarm</span>' : "<a href='/print/cards?deck=2'>The Swarm</a>") ?> (easy to play),
    <?=(isset($_GET['deck']) && $_GET['deck'] == 1 ? '<span class="active">The Terrans</span>' : "<a href='/print/cards?deck=1'>The Terrans</a>") ?> (normal),
    <?=(isset($_GET['deck']) && $_GET['deck'] == 3 ? '<span class="active">United Stars</span>' : "<a href='/print/cards?deck=3'>United Stars</a>") ?> (advanced)<br><br>
    <a href="/">← GO BACK</a>
<?php } ?>
</p>

<?php if (isset($_GET['card_list'])) {
    if (isset($_GET['commander']) && is_numeric($_GET['commander'])) {
        echo '<img loading=lazy src="https://images.thespacewar.com/commander-'.$_GET['commander'].'.png">';
    }
    $card_list = explode(',', $_GET['card_list']);
    foreach ($card_list as $card_id) {
        $card_id = trim($card_id);
        if (!is_numeric($card_id)) continue;
        echo "<img loading=lazy src='https://images.thespacewar.com/card-".$card_id.".jpg'>";
    }

} ?>


<?php if (!isset($_GET['deck'])) die(); ?>

<img loading=lazy src="https://images.thespacewar.com/station-card-top.jpg">
<img loading=lazy src="https://images.thespacewar.com/station-card-middle.jpg">
<img loading=lazy src="https://images.thespacewar.com/station-card-bottom.jpg">

<?php

$commander_data = commanderData();
$_GET['deck'] = (int) $_GET['deck'];
if ( $_GET['deck'] > 3 ) $_GET['deck'] = 1;


foreach ($commander_data as $commander_slug => $commander) {
    if ( $commander['deck'] != $_GET['deck'] ) continue;
    echo '<img loading=lazy src="https://images.thespacewar.com/commander-'.$commander['id'].'.png">';
}


$array = getCardData();

foreach ($array as $key => $value) {
    if ($_GET['deck'] != $value['deck_id']) continue;
    for ($i=0; $i < $value['copies']; $i++) { 
        echo "<img loading=lazy src='https://images.thespacewar.com/card-".$value['id'].".jpg'>";
    }
}
?>

</body>
</html>