<?php
//天空部落格網址（請按照範例格式）
$site = "https://xxx.tian.yam.com/posts";
//最大頁數（看底下分頁最多分到幾頁）
$end_page = 10;
//MySQL資料庫位址
$DB_HOST = 'localhost';
//MySQL資料庫帳號
$DB_ID = 'root';
//MySQL資料庫密碼
$DB_PASS = '';
//MySQL資料庫名稱
$DB_NAME = '';

//接著建立資料表
/*
DROP TABLE IF EXISTS `sky`;
CREATE TABLE `sky` (
`id` int(11) unsigned NOT NULL,
`link` varchar(255) NOT NULL,
`page` tinyint(3) unsigned NOT NULL,
`ok` enum('0','1') NOT NULL,
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `sky_pages`;
CREATE TABLE `sky_pages` (
`id` int(10) unsigned NOT NULL,
`title` varchar(255) NOT NULL,
`content` mediumtext NOT NULL,
`create_date` datetime NOT NULL,
`count` int(10) unsigned NOT NULL,
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
 */

// ------------------------------以下勿動------------------------------
$db = new mysqli($DB_HOST, $DB_ID, $DB_PASS, $DB_NAME);
if ($db->connect_error) {
    die('DB Error:' . $db->connect_error);
}
$db->set_charset("utf8");

$op = isset($_GET['op']) ? $_GET['op'] : '';
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$i  = isset($_GET['i']) ? intval($_GET['i']) : 1;

switch ($op) {
    case 'page':
        get_all_page();
        break;

    case 'id':
        get_id($i);
        break;

    default:
        echo "步驟一：<a href='sky.php?op=id&i=1'>取得所有編號</a><br>
        步驟二：<a href='sky.php?op=page'>取得所有文章</a>";
        break;
}

//取得並紀錄所有文章編號
function get_id($i)
{
    global $db, $end_page;

    if ($i > $end_page) {
        header("location: sky.php?op=page");
    }

    $url  = "{$site}?page={$i}";
    $page = file_get_contents($url);
    $vv   = array();
    preg_match_all('/data-id=\"(.*?)\"/s', $page, $vv, PREG_SET_ORDER);
    foreach ($vv as $v) {
        $sql = "REPLACE INTO `sky` (`id`, `link`, `page`, `ok`) VALUES('{$v[1]}', '{$site}/{$v[1]}', $i , 0)";
        $db->query($sql) or die($db->error . $sql);
    }
    $i++;
    header("location: sky.php?op=id&i={$i}");
    exit;
}

//取得所有文章
function get_all_page()
{
    global $db;
    $sql    = "SELECT * FROM `sky` WHERE `ok`!='1' ORDER BY `page`";
    $result = $db->query($sql) or die($db->error . $sql);
    $all    = [];
    while ($page = $result->fetch_assoc()) {
        get_one_page($page['id'], $page['link']);
    }
    header("location: sky.php");
    exit;
}

//取得並寫入一篇文章
function get_one_page($id, $link)
{
    global $db;

    $page = file_get_contents($link);
    $vv   = array();
    preg_match_all('/<div class=\"post-title black inner\">(.*?)<div class=\"post-date gray text-right\">/s', $page, $vv, PREG_SET_ORDER);
    preg_match_all('/<h1>(.*?)<\/h1>/s', $vv[0][1], $v, PREG_SET_ORDER);
    $title = $db->real_escape_string($v[0][1]);

    preg_match_all('/<div class=\"post-date gray text-right\">(.*?)<\/div>/s', $page, $vv, PREG_SET_ORDER);
    $create_date = date('Y-m-d H:i:s', strtotime(str_replace(',', '', $vv[0][1])));

    preg_match_all('/<!-- post content -->(.*?)<!-- .\/post-content -->/s', $page, $vv, PREG_SET_ORDER);
    $content = $db->real_escape_string(trim($vv[0][1]));

    preg_match_all('/<span>([0-9]+)<\/span>/s', $page, $vv, PREG_SET_ORDER);
    $count = intval($vv[0][1]);

    $sql = "REPLACE INTO `sky_pages` (`id`, `title`, `content`, `create_date`, `count`) VALUES('{$id}', '{$title}', '{$content}' , '{$create_date}', '{$count}')";
    $db->query($sql) or die($db->error . $sql);
}
