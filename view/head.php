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

<?php // Start of Navigation https://cdpn.io/bokac/fullpage/EPEKeP ?>
.nav{-moz-box-sizing:border-box;-webkit-box-sizing:border-box;box-sizing:border-box;text-align:center;font-size:17px;text-transform:uppercase;margin:22px 0}
.nav:after,.nav:before{content:"";display:table}
.nav:after{clear:both}
.link-effect a{color:#d6d6d6;padding:10px 14px;position:relative;overflow:hidden;display:inline-block;-moz-transition:ease-out .3s;-o-transition:ease-out .3s;-webkit-transition:ease-out .3s;transition:ease-out .3s}
.link-effect a span::before{width:5px;height:5px;background:0 0;content:"";position:absolute;left:0;top:0;border-top:2px solid #fff;border-left:2px solid #fff;-moz-transition:.3s;-o-transition:.3s;-webkit-transition:.3s;transition:.3s;opacity:0}
.link-effect a span::after{width:5px;height:5px;background:0 0;content:"";position:absolute;right:0;bottom:0;border-right:2px solid #fff;border-bottom:2px solid #fff;-moz-transition:.3s;-o-transition:.3s;-webkit-transition:.3s;transition:.3s;opacity:0}
.link-effect a::before{width:5px;height:5px;background:0 0;content:"";position:absolute;right:0;top:0;border-right:2px solid #fff;border-top:2px solid #fff;-moz-transition:.3s;-o-transition:.3s;-webkit-transition:.3s;transition:.3s;opacity:0}
.link-effect a::after{width:5px;height:5px;background:0 0;content:"";position:absolute;left:0;bottom:0;border-left:2px solid #fff;border-bottom:2px solid #fff;-moz-transition:.3s;-o-transition:.3s;-webkit-transition:.3s;transition:.3s;opacity:0}.link-effect .active,.link-effect a:hover{color:#fff}
.link-effect .active::before,.link-effect a:hover::before{opacity:1;right:5px;top:5px}
.link-effect .active::after,.link-effect a:hover::after{opacity:1;left:5px;bottom:5px}
.link-effect .active span::before,.link-effect a:hover span::before{opacity:1;left:5px;top:5px}
.link-effect .active span::after,.link-effect a:hover span::after{opacity:1;right:5px;bottom:5px}
.link-effect a.active,.link-effect a:hover{text-decoration:none;background:0 0}
<?php // End of Navigation ?>

body {background-color: #000;color:#ddd;font-family:Verdana;font-size:17px;padding-top:30px;padding-bottom:50px;line-height: 25px; }
h1, .header h3 {text-align:center;text-transform:uppercase;letter-spacing:5px;margin-bottom:40px;}
.header h3 {font-size:30px;margin:10px auto -15px auto;letter-spacing:6px;}
.header h4 {font-size:15px;text-align:center;font-weight:normal;text-transform:uppercase;}
.wrap {margin:auto;width:800px;max-width:90%;background-color:black;padding:50px 120px;background:rgba(0,0,0,.85);border-radius:5px;}
.cards img {height:305px;width:219px;margin-right:10px;margin-bottom:10px;}
.cards h2 {clear:both;}
.cards th {text-align:right;}
/* Original size: width:430px;height:600px; */
img.big, img.extra-big {width:358px;height:500px;border:1px solid #333;filter:contrast(90%);}
img.big {float:left;margin-right:40px;margin-bottom:40px;}
img.extra-big {margin:auto;display:block;margin-top:20px;}
blockquote {background-color: #171717c7;padding:15px;border-left:5px solid #555;}
blockquote:hover {border-left:5px solid #aaa;background-color: #333333c7;}
blockquote p {font-size:18px;line-height: 23px;font-family: courier;font-style: italic;}
blockquote cite {font-size:14px;text-align: right;margin-top:-15px;display: block;font-style: normal;}
a {color:#eef;}
li {margin-bottom:15px;}
h2 {margin-bottom:15px;margin-top:50px;}
h3 {margin-bottom:5px;margin-top:15px;}
p {margin: 0 0 20px 0;}
code {background-color: #555;padding:3px 6px;}
a:hover {color:#2f4763;}
span.active {padding:5px 0;background-color:#555;}

table {border-spacing:0;width:100%;border:8px solid #666;margin:40px auto;margin:40px auto;}
table tr { background: #000;}
table tr:nth-child(odd) { background: #1c1c1c;}
table td, table th {padding:9px 10px;}
table th {text-align:left;}
.cards table {width:50%;}
input, textarea, select {padding: 7px;margin-bottom: 15px;}
input[type="text"], input[type="email"], input[type="password"], textarea, select {width: 50%;}
.print {display:none;}
hr {border:1px solid #2f4763}
.footer, .footer-cta {max-width:500px;margin:auto;text-align: center;}
.footer-cta {margin-top:40px;}
.footer {background-color: #00000087;padding:10px;margin-top:10px;border-radius:5px;}
.footer p {font-size: 14px;color:#ccc;line-height: 15px;margin:0;}

.commander .title {text-align:center;font-style:italic;margin-top:-25px;}
.commander .rules {font-size:19px;text-align:center;max-width:500px;margin:35px auto;}
.commander .lore {font-size:19px;font-style:italic;max-width:600px;margin:35px auto;}

.login {float:right;margin-bottom: 20px;}
.error {border:3px solid red;padding:10px;color:red;margin: 30px}
.good {border:3px solid #74d474;padding:10px;color:#74d474;margin: 30px}

.big-checkmark {color:green;font-weight: bold;font-size: 180%;}

a.big-button {font-size: 19px;background-color: green;text-decoration: none;padding: 5px 10px;border: 3px solid #7a7a7a;display: table;}
a.big-button:hover {text-decoration: underline;}

/* https://embedresponsively.com/ */
.embed-container { position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden; max-width: 100%; }
.embed-container iframe, .embed-container object, .embed-container embed { position: absolute; top: 0; left: 0; width: 100%; height: 100%; }


@media (max-width: 1400px) { /* 1366x768 */
  .cards img:hover {transform:scale(1.35);box-shadow:0px 0px 150px 10px #000;transition: all 0.1s ease;filter:contrast(90%);}
  img.big:hover, img.extra-big:hover {transform:none;box-shadow:none;transition:none;}
}

@media (min-width: 1000px) { /* Desktop, 1920x1080 */
  .header {display:none;}
  h2 {margin:50px 0 30px -35px;letter-spacing:1px;border-left:5px solid #b7b7b769;border-top:5px solid #b7b7b769;padding: 9px 0 0 14px}
  .logo {background: url(https://images.thespacewar.com/logo.png) center top no-repeat;height:91px;}
  body {background: url(https://images.thespacewar.com/the-space-war.jpg) #000 center top no-repeat fixed;-webkit-background-size: cover; -moz-background-size: cover; -o-background-size: cover; background-size: cover;}
  .cards img:hover {transform:scale(1.55);box-shadow:0px 0px 150px 10px #000;transition: all 0.1s ease;}
   img.big:hover, img.extra-big:hover {transform:none;}
   .nav {margin-top:20px;margin-bottom: 20px;}
   table {font-size: 19px;}
}

@media (max-width: 900px) { /* Mobile */
  h1 {letter-spacing:4px;font-size:28px;}
  .wrap{font-size:16px;padding:10px;}
  body {padding-top:5px;background:none;background-color: #000;}
  .cards img {width:150px;height:auto;}
  .cards img:hover {transform:none;box-shadow:none;transition:none;filter:none;}
  img.big, img.extra-big {max-width:90%;width:300px;height:auto;}
  .cards table {width:100%;}
  .sub-form {float:none;margin-top:00px}
  .nav {margin-top:-20px;margin-bottom: 20px;}
  table {font-size: 16px;}
  #home-image img {margin-right:20px;}
}

@media (max-width: 400px) { /* Small Mobile */
  .cards img {width:140px;}
  img.big, img.extra-big {width:90%;height:auto;float:none;margin-bottom:10px;}
}

@media print { 
   body {background:none;color:#000;padding:0px;margin:0px;font: 10pt Verdana, "Times New Roman", Times, serif;line-height: 1.3;}
   .header, .print {display:block;}
   .print {margin-top:50px;}
   .no-print {display:none;}
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

<section class="nav no-print">
  <nav class="link-effect">
    <a href="/" <?php if(URL == '') echo ' class="active" ' ?>><span>Home</span></a>
    <a href="/videos" <?php if(URL == 'videos') echo ' class="active" ' ?>><span>Videos</span></a>
    <a href="/play" <?php if(URL == 'play') echo ' class="active" ' ?>><span>Play</span></a>
    <a href="/cards/" <?php if(URL == 'cards/') echo ' class="active" ' ?>><span>The Cards</span></a>
    <a href="/rules" <?php if(URL == 'rules') echo ' class="active" ' ?>><span>How to play</span></a>
    <a href="/leaderboard" <?php if(URL == 'leaderboard') echo ' class="active" ' ?>><span>Leaderboard</span></a>
    <a href="/news" <?php if(URL == 'news') echo ' class="active" ' ?>><span>News</span></a>
  </nav>
</section>


<div class="login no-print">
  <?php if (substr(URL, 0, 8) == 'account/' && (!isset($logged_in) || $logged_in == [])) { ?>
    [ <a href="/register">Register an account</a> ]
  <?php } elseif (!isset($logged_in) || $logged_in == []) { ?>
    [ <a href="/account/">Login</a> ]
  <?php } elseif (substr(URL, 0, 8) == 'account/') { ?>
    [ <a href="/logout">Logout</a> ]
  <?php } else { ?>
    <strong><?=$logged_in['username']?></strong> <img src="https://staticjw.com/redistats/images/flags/<?=$logged_in['country']?>.gif"> | <a href="/account/">Account</a>
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
