<?php
$title_tag = 'Full Card List | TheSpaceWar.com';
require(ROOT.'view/head.php');

$table_head = '<tr><th>Deck</th><th>Copies</th><th>Image</th><th>Name</th><th>Cost</th><th>Type</th><th>Attack</th><th>Defense</th><th>Text</th></tr>';
?>

<script src="//code.jquery.com/jquery-1.11.3.min.js"></script>
<script src="//cdn.datatables.net/1.10.10/js/jquery.dataTables.min.js"></script>
<link rel="stylesheet" href="//cdn.datatables.net/1.10.7/css/jquery.dataTables.min.css">


<script>$(document).ready( function () {$('#result_table').DataTable({"order": [ 0, 'asc' ],"pageLength": 10,"lengthChange": true,"searching": true, "lengthMenu": [ 8, 10, 50, 500],"paging": true,"info": false});} );</script>



<style>
    .wrap {width:1240px;}
    table {margin: auto;width:100% !important;}
    table tr {background-color: black !important;}
    table tr:nth-child(odd) { background: #1c1c1c !important;}
    td {padding:10px;text-align: center;font-size:16px}
    input, textarea, select {padding: 3px;margin-bottom: 15px;width: 180px;}
    select {width: 50px;}
    .dataTables_wrapper .dataTables_length, .dataTables_wrapper .dataTables_filter, .dataTables_wrapper .dataTables_paginate {color:white !important;}
    .dataTables_wrapper .dataTables_paginate .paginate_button {color:white !important}
</style>
<h1>Card List</h1>

<p>See also the page: <a href='/popular-cards'>Popular Cards</a>.</p>

<p>Display cards from deck: 
    <?=(isset($_GET['deck']) && $_GET['deck'] == 1 ? '<span class="active">The Terrans</span>' : "<a href='/card-list?deck=1'>The Terrans</a>") ?>,
    <?=(isset($_GET['deck']) && $_GET['deck'] == 2 ? '<span class="active">The Swarm</span>' : "<a href='/card-list?deck=2'>The Swarm</a>") ?>,
    <?=(isset($_GET['deck']) && $_GET['deck'] == 3 ? '<span class="active">United Stars</span>' : "<a href='/card-list?deck=3'>United Stars</a>") ?>,
    <?=(!isset($_GET['deck']) ? '<span class="active">All cards</span>' : "<a href='/card-list'>All cards</a>") ?></p>

<table id="result_table">

<thead><?=$table_head?></thead>

<?php
$array = getCardData();
$count_cards['The Terrans'] = 0;
$count_cards['The Swarm'] = 0;
$count_cards['United Stars'] = 0;

foreach ($array as $key => $value) {
    if (isset($_GET['deck']) && $_GET['deck'] != $value['deck_id']) continue;

    if (in_array($value['type'], ['Event', 'Duration'])) {
        $value['attack'] = '';
        $value['defense'] = '';
    }
    echo "<tr><td>".$value['deck_name']."</td><td>".$value['copies']."</td><td>";
    if (true) {
        echo "<a href='/cards/".$value['slug']."'><img loading=lazy src='https://images.thespacewar.com/card-".$value['id'].".jpg' style='height:80px;'></a>";
    } else if ($value['artist'] != ''){
        echo "<img loading=lazy src='https://images.thespacewar.com/card-".$value['id'].".jpg' style='height:80px;'>";
    }

    if ($value['cost'] == 'X') {
        $data_order['cost'] = 100;
    } else {
        $data_order['cost'] = $value['cost'];
    }
    if ($value['attack'] == 'X') {
        $data_order['attack'] = 100;
    } else {
        $data_order['attack'] = $value['attack'];
    }
    if ($value['defense'] == 'X') {
        $data_order['defense'] = 100;
    } else {
        $data_order['defense'] = $value['defense'];
    }
    echo "</td><td>".$value['name']."</td><td data-order='".$data_order['cost']."'>".$value['cost']."</td><td>".$value['type']."</td><td data-order='".$data_order['attack']."'>".$value['attack']."</td><td data-order='".$data_order['defense']."'>".$value['defense']."</td><td style='font-size:15px'>".$value['text']."</td></tr>";
    $count_cards[$value['deck_name']] += $value['copies'];
}


?>

<tfoot><?=$table_head?></tfoot>

</table>

<hr>

<p>Control counting of deck sizes: The Terrans = <?=$count_cards['The Terrans']?>, The Swarm = <?=$count_cards['The Swarm']?>, United Stars = <?=$count_cards['United Stars']?>.</p>
