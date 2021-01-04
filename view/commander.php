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
    <p>Commander is always selected directly after drawing the start hand. If you choose a shield card (green) that is in your hand, put it on the table and draw a new card. Otherwise select a shield card from your draw pile and then shuffle your draw pile.</p>
<?php } ?>


<h2>Other Commanders</h2>

<?php
foreach ($commander_data as $slug2 => $data) {
    if ($slug == $slug2 || $slug2 == '') continue;
    echo '<a href="/commanders/'.$slug2.'"><img src="https://images.thespacewar.com/commander-'.$data['id'].'.png"></a>';
}

