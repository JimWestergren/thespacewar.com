<?php
$title_tag = 'Logged out | TheSpaceWar.com';

// Remove the cookie
setcookie('loggedin', '', ['expires' => 1, 'path' => '/', 'domain' => 'thespacewar.com', 'secure' => true, 'httponly' => false, 'samesite' => 'None']);

$logged_in = [];

include(ROOT.'view/head.php');

echo '<p>You have been logged out. <a href="/">Go back</a>.</p>';
