<!DOCTYPE html>
<html>
<head>
    <title><?= $title_tag ?></title>
    <link rel="shortcut icon" type="image/x-icon" href="https://images.thespacewar.com/favicon.ico">
    <meta charset="utf-8">
    <?php if ($title_tag != '404 | TheSpaceWar.com') { // XSS protection ?>
      <link rel="canonical" href="https://thespacewar.com/<?=URL?>">
    <?php } ?>
    <meta name="viewport" content="width=device-width, initial-scale=1">
<style>
body {background-color: #000;color:#ddd;font-family:Verdana;font-size:17px;padding-top:30px;padding-bottom:50px;line-height: 25px}
blockquote {font-size:15px;line-height: 23px}
h1, .header h3 {text-align:center;text-transform:uppercase;letter-spacing:5px;margin-bottom:40px;}
.header h3 {font-size:30px;margin:10px auto -15px auto;letter-spacing:6px;}
.header h4 {font-size:15px;text-align:center;font-weight:normal;text-transform:uppercase;}
.wrap {margin:auto;width:800px;max-width:90%;background-color:black;padding:50px 120px;background:rgba(0,0,0,.8)}
.cards img {height:305px;width:219px;margin-right:10px;margin-bottom:10px;}
.cards h2 {clear:both;}
.cards table {border:2px solid #2a3c53;border-bottom:none;}
.cards th {text-align:right;border-right:2px solid #2a3c53;}
.cards td {}
.cards th, .cards td {border-bottom:2px solid #2a3c53;padding:7px 10px;}
.footer {max-width:500px;margin:auto;margin-top:40px;text-align: center;}
img.big, img.extra-big {width:430px;height:600px;border:1px solid #333;filter:contrast(80%);}
img.big {float:left;margin-right:40px;margin-bottom:40px;}
img.extra-big {margin:auto;display:block;margin-top:20px;}
a {color:#eef;}
li {margin-bottom:15px;}
h2 {margin-bottom:15px;margin-top:35px;}
h3 {margin-bottom:5px;margin-top:15px;}
p {margin: 0 0 20px 0;}
code {background-color: #555;padding:3px 6px;}
a:hover {padding:5px 0;background-color:#555;}
span.active {padding:5px 0;background-color:#555;}
input, textarea, select {
    padding: 7px;
    margin-bottom: 15px;
    
}
input[type="text"], input[type="email"], input[type="password"], textarea, select {width: 50%;}

.print {display:none;}
hr {border:1px solid #2f4763}

ul#nav {text-align:center;margin:auto;display:table;padding:0;}
ul#nav li{display:inline-block;padding:10px 10px;font-size:17px;text-transform:uppercase;margin-bottom: 0;}

.commander .title {text-align:center;font-style:italic;margin-top:-25px;}
.commander .rules {font-size:19px;text-align:center;max-width:500px;margin:35px auto;}
.commander .lore {font-size:19px;font-style:italic;max-width:600px;margin:35px auto;}

.login {float:right;margin-bottom: 20px;}
.error {border:3px solid red;padding:10px;color:red;margin: 30px}
.good {border:3px solid #74d474;padding:10px;color:#74d474;margin: 30px}

table {margin:auto;max-width: 100%;}
#home-image img {float:left;max-width:50%;margin-right:40px;margin-bottom:15px;}
a.big-button {font-size: 19px;background-color: green;text-decoration: none;padding: 5px 10px;border: 3px solid #7a7a7a;display: table;}
a.big-button:hover {text-decoration: underline;}

/* https://embedresponsively.com/ */
.embed-container { position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden; max-width: 100%; }
.embed-container iframe, .embed-container object, .embed-container embed { position: absolute; top: 0; left: 0; width: 100%; height: 100%; }

@media (max-width: 1400px) { /* 1366x768 */
  img.big, img.extra-big {width:358px;height:500px;}
  .cards img:hover {transform:scale(1.35);box-shadow:0px 0px 150px 10px #000;transition: all 0.1s ease;}
  img.big:hover, img.extra-big:hover {transform:none;}
}

@media (min-width: 1000px) { /* 1920x1080 */
  .header {display:none;}
  .logo {background: url(https://images.thespacewar.com/logo.png) center top no-repeat;height:91px;}
  body {background: url(https://images.thespacewar.com/the-space-war.jpg) #000 center top no-repeat fixed;}
  .cards img:hover {transform:scale(1.55);box-shadow:0px 0px 150px 10px #000;transition: all 0.1s ease;}
   img.big:hover, img.extra-big:hover {transform:none;}
   ul#nav {margin-top:20px;margin-bottom: 20px;}
   table {font-size: 19px;}
}

@media (max-width: 900px) { /* Mobile */
  h1 {letter-spacing:4px;font-size:28px;}
  .wrap{font-size:16px;padding:10px;}
  body {padding-top:5px;background:none;background-color: #000;}
  .cards img {width:150px;height:auto;}
  img.big, img.extra-big {max-width:90%;width:300px;height:auto;}
  .cards img:hover {transform:none;box-shadow:none;transition:none;}
  .sub p {font-size:15px;max-width:90%}
  .sub-form {float:none;margin-top:00px}
  ul#nav {margin-top:-20px;margin-bottom: 20px;}
  ul#nav li{padding:5px;font-size:14px;}
  table {font-size: 16px;}
  #home-image img {margin-right:20px;}
}

@media (max-width: 400px) { /* Mobile */
  .cards img {width:140px;}
  img.big, img.extra-big {width:90%;height:auto;float:none;margin-bottom:10px;}
}

@media print { 
   body {background:none;color:#000;padding:0px;margin:0px;font: 10pt Verdana, "Times New Roman", Times, serif;line-height: 1.3;}
   .header, .print {display:block;}
   ul#nav,.sub,.no-print {display:none;}
   h1 {margin-top:-10px;}
   img {filter:contrast(100%) !important;}
}

</style>

</head>


<body>
<div class="header">
<h3>The Space War</h3>
<h4>Card Game</h4>
</div>


<div id='wrap' class="wrap">

<div class="logo"></div>


<ul id="nav">
    <li><?=(URL == '' ? '<span class="active">Home</span>' : '<a href="/">Home</a>') ?></li>
    <li><?=(URL == 'videos' ? '<span class="active">videos</span>' : '<a href="/videos">Videos</a>') ?></li>
    <li><?=(URL == 'play' ? '<span class="active">Play</span>' : '<a href="/play">Play</a>') ?></li>
    <li><?=(URL == 'cards/' ? '<span class="active">The Cards</span>' : '<a href="/cards/">The Cards</a>') ?></li>
    <li><?=(URL == 'rules' ? '<span class="active">How to play</span>' : '<a href="/rules">How to play</a>') ?></li>
    <li><?=(URL == 'leaderboard' ? '<span class="active">Leaderboard</span>' : '<a href="/leaderboard">Leaderboard</a>') ?></li>
    <li><?=(URL == 'news' ? '<span class="active">News</span>' : '<a href="/news">News</a>') ?></li>
</ul>



<div class="login no-print">
  <?php if (substr(URL, 0, 8) == 'account/' && (!isset($logged_in) || $logged_in == [])) { ?>
    [ <a href="/register">Register an account</a> ]
  <?php } elseif (!isset($logged_in) || $logged_in == []) { ?>
    [ <a href="/account/">Login</a> ]
  <?php } elseif (substr(URL, 0, 8) == 'account/') { ?>
    [ <a href="/logout">Logout</a> ]
  <?php } else { ?>
    Logged in as <strong><?=$logged_in['username']?></strong> <img src="https://staticjw.com/redistats/images/flags/<?=$logged_in['country']?>.gif"> <?=$logged_in['rating']?> | <a href="/account/">Account</a>
  <?php } ?>

</div>


<div style="clear:both;"></div>

<?php if (URL != 'cards/' && strpos(URL, 'cards/', 0) === 0) { ?>
  <div class="cards">
    <p><a href="/cards/">← CARD LIST</a></p>
    <div style="text-align:center;margin-bottom:-10px;">- CARD -</div>  
<?php } ?>

<?php if (URL != 'commanders/' && strpos(URL, 'commanders/', 0) === 0) { ?>
  <div class="cards commander">
    <p><a href="/cards/">← CARD LIST</a></p>
    <div style="text-align:center;margin-bottom:-10px;">- COMMANDER CARD -</div>
<?php } ?>
