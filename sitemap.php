<?php

function url($url, $last_modified) {
    $return = "  <url>\n";
    $return .= "      <loc>https://thespacewar.com/".$url."</loc>\n";
    if ($last_modified > 0) {
        $return .= "      <lastmod>".date("Y-m-d", $last_modified)."</lastmod>\n";
    }
    $return .= "  </url>\n";
    return $return;
}

header('Content-type: application/xml; charset="utf-8"',true);
echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
echo "<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n";


echo url('', filemtime(ROOT.'content/home.php'));
echo url('cards/', filemtime(ROOT.'content/cards-.php'));

$path = ROOT.'content/';
$files = scandir($path);
foreach($files as $file) {
    if (!is_file($path.$file) || strpos($file, '-.php') || strpos($file, ' ') || in_array($file, ['home.php', 'validate.php', 'log-game.php', 'logout.php', 'forgot-password.php'])) continue;
    echo url(substr($file, 0, -4), filemtime(ROOT.'content/'.$file));
}
$path = ROOT.'content/cards/';
$files = scandir($path);
foreach($files as $file) {
    if (!is_file($path.$file)) continue;
    echo url(substr('cards/'.$file, 0, -4), filemtime(ROOT.'content/cards/'.$file));
}

$commander_data = commanderData();
foreach ($commander_data as $slug => $data) {
    if ($slug == '') continue;
    echo url('commanders/'.$slug, 0);
}


echo "</urlset>";
