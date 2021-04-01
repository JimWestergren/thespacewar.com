<?php

error_reporting(-1);
ini_set('display_errors', '1');

define('TIMESTAMP', time());
define('ROOT', '/var/www/thespacewar.com/');
define('URL', urldecode(ltrim(parse_url($_SERVER['REQUEST_URI'])['path'], '/')));
// domain.com/URL_CONSTANT

include(ROOT.'../other/secret.php');
include(ROOT.'include/functions.php');
include(ROOT.'include/PDOWrap.php');


// https://thespacewar.com/images.thespacewar.com:443 ??
if($_SERVER['HTTP_HOST'] != 'thespacewar.com' || strpos($_SERVER['REQUEST_URI'], ':')) {
    header("HTTP/1.1 301 Moved Permanently");
    header("Location: https://thespacewar.com/");
    die();
}

$internal_redirects = [
    'users/1-Jim' => 'users/Jim',
    'users/2-Alvin' => 'users/Alvin',
    'users/3-Viper3' => 'users/Viper3',
    'users/4-agge' => 'users/agge',
    'users/5-Kaah' => 'users/Kaah',
    'users/6-rhuanco' => 'users/rhuanco',
    'users/7-augustTestDimohax463' => 'users/augustTestDimohax463',
    'users/8-augustalex' => 'users/augustalex',
    'account' => 'account/',
    'login' => 'account/',
    'cards/orders-cancelled' => 'cards/cancel',
];


if(isset($internal_redirects[URL])) {
    header("HTTP/1.1 301 Moved Permanently");
    header("Location: https://thespacewar.com/".$internal_redirects[URL]);
    die();
}

if (isset($_GET['deck']) && !in_array($_GET['deck'], ['1', '2', '3', '4'])) {
    header("HTTP/1.1 301 Moved Permanently");
    header("Location: https://thespacewar.com/".URL);
    die();
}

// Save referrer in a cookie and redirect
if(isset($_GET['referrer'])) {
    set_cookie('referrer', $_GET['referrer'], TIMESTAMP+(3600*24*60));
    header("HTTP/1.1 301 Moved Permanently");
    header("Location: https://thespacewar.com/".URL);
    die();
}

// Save cookie with referring domain
if(!isset($_COOKIE['referrer']) && isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER'] != '') {
    $referring_domain = parse_url(str_replace('www.', '', strtolower($_SERVER['HTTP_REFERER'])), PHP_URL_HOST);
    if ($referring_domain == 'play.thespacewar.com' && strpos($_SERVER['HTTP_REFERER'], '?fbclid=')) {
        $referring_domain = 'facebook.com';
    }
    if ($referring_domain != 'thespacewar.com') {
        set_cookie('referrer', $referring_domain, TIMESTAMP+(3600*24*60));
        $_COOKIE['referrer'] = $referring_domain;
    }
}


$logged_in = isLoggedIn();


if (URL == '') {
    include(ROOT.'content/home.php');
    include(ROOT.'view/footer.php');
    die();
}

if (URL == 'sitemap.xml') {
    include(ROOT.'sitemap.php');
    die();
}

// User pages
if (strpos(URL, 'users/', 0) === 0) {
    include(ROOT.'view/users.php');
    include(ROOT.'view/footer.php');
    die();
}

// Commander pages
if (strpos(URL, 'commanders/', 0) === 0) {
    include(ROOT.'view/commander.php');
    include(ROOT.'view/footer.php');
    die();
}


if (substr(URL, -1) === '/') {
    // Page ends with /
    $url_to_check = rtrim(URL, '/')."-";
} else {
    $url_to_check = URL;
}

// The principal routing
if( file_exists( ROOT.'content/'.$url_to_check.'.php' ) ) {
    if ( substr( $url_to_check, 0, 6 ) === 'cards/' ) {
        $slug = substr( $url_to_check, 6 );
        echo displayCard( $slug );
    }
    include(ROOT.'content/'.$url_to_check.'.php');
    include(ROOT.'view/footer.php');
    die();
}

dieWith404('<p>This page does not exist</p>');
