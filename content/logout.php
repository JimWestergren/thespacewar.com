<?php
$title_tag = 'Logged out | TheSpaceWar.com';

// Remove the cookie
set_cookie('loggedin', '', 1);

$logged_in = [];

include(ROOT.'view/head.php');

echo '<p>You have been logged out. <a href="/">Go back</a>.</p>';
