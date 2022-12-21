<?php
require_once('conf.php');

$error_log_dest = $error_log_dir . '/job.log';

logging('start');

$pdo = new PDO('sqlite:' . $db_path);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$stmt = $pdo->query("SELECT * FROM category where category_id != 0");
while ($row = $stmt->fetch()) {
    $category = $row['category_id'];
    $feed = getFeed($category);
    $link = $url_category . $category;
    $checked_at = $feed->xpath('/atom:feed/atom:updated/text()')[0];

    if ($checked_at <= $row['checked_at']) continue;

    if ($row['update_only']) {
        echo $category . "\n";
    } else {
        $entries = $feed->xpath('/atom:feed/atom:entry');
        $count = count($entries);

        for ($i = 0; $i < $count; ++$i) {
            $updated = $feed->xpath('/atom:feed/atom:entry/atom:updated/text()')[$i];

            if ($updated <= $row['checked_at']) continue;

            $content = $feed->xpath('/atom:feed/atom:entry/atom:title/text()')[$i];
            echo $content . "\n";
        }
    }
}

function getFeed($category) {
    global  $url_category;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_URL, $url_category . $category . '&feed=atom');
    $data = curl_exec($ch);
    curl_close($ch);
    $xml = simplexml_load_string($data, null, LIBXML_NOCDATA, 'atom', true);
    $xml->registerXPATHNamespace('atom', 'http://www.w3.org/2005/Atom');
    return $xml;
}

function logging($message) {
    global $error_log_type, $error_log_dest;
    $ts = date('c');
    error_log($ts . ' ' . $message . "\n", $error_log_type, $error_log_dest);
}
