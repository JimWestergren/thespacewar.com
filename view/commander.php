<?php

$commander_data = commanderData();
$slug = str_replace('commanders/', '', URL);

if (!isset($commander_data[$slug])) {
    dieWith404('<p>This page does not exist</p>');
}

$data = $commander_data[$slug];
$title_tag = 'Commander Card: '.$data['name'].' | TheSpaceWar.com';
require(ROOT.'view/head.php');

?>

<h1><?=$data['name']?></h1>

<div class="title"><?=$data['title']?></div>

<img src="https://images.thespacewar.com/commander-<?=$data['id']?>.png" class="extra-big">

<div class="rules"><?=$data['rules']?></div>

<div class="lore"><?=$data['lore']?></div>

<?php if ($data['name'] == 'Nicia Satu') { ?>
    <h2>Rule Clarification</h2>
    <p>Commander is always selected directly after drawing the start hand. If you choose a green defense card that is in your hand, put it on the table and draw a new card. Otherwise select a defense card from your draw pile and then shuffle your draw pile.</p>
<?php } ?>


<?=displayCardNFTinfo('commander-'.$slug)?>


<h2>Changelog During Playtesting</h2>

<ul>
    <?php if ( $data['name'] == 'Dr. Stein' ) { ?>
        <li>Nerf in February 2023: Perfect Plan ability replaced with draw a card.</li>
        <li>Nerf in August 2021: Perfect Plan ability can now only be used once per turn.</li>
    <?php } ?>
    <?php if ( $data['name'] == 'Frank Johnson' ) { ?>
        <li>Nerf in June 2020: Maximum reduced from 11 to 10.</li>
    <?php } ?>
    <?php if ( $data['name'] == 'Nicia Satu' ) { ?>
        <li>Buff in May 2022: "any shield" changed to "any green defense card".</li>
        <li>Buff in December 2020: "Energy Shield" changed to "any shield".</li>
        <li>Buff in August 2019: Added that you begin with Energy Shield in play.</li>
    <?php } ?>
    <?php if ( $data['name'] == 'Capt. Shera Kinson' ) { ?>
        <li>Buff in September 2021: The card used to be "Use 3 actions to take the Starship card from anywhere to your hand."</li>
    <?php } ?>
    <?php if ( in_array( $data['name'], ['Liana Henders', 'Zyre'] ) ) { ?>
        <li>January 2020: Created.</li>
    <?php } elseif ( in_array( $data['name'], ['Capt. Shera Kinson', 'Capt. Wayne McCarter', 'Zuuls', 'Naalox', 'Staux', 'Crakux'] ) ) { ?>
        <li>July 2020: Created.</li>
    <?php } else { ?>
        <li>June 2019: Created.</li>
    <?php } ?>
</ul>

<h2>Other Commanders</h2>

<?php
foreach ($commander_data as $slug2 => $data) {
    if ($slug == $slug2 || $slug2 == '') continue;
    echo '<a href="/commanders/'.$slug2.'"><img src="https://images.thespacewar.com/commander-'.$data['id'].'.png"></a>';
}

