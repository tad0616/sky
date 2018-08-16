# sky
天空部落格文章抓取程式

此程式用來抓取所有文章標題、內文、日期、人氣，但不會去抓分類

先修改 sky.php 設定上方的網址以及MySQL資料庫的相關設定
然後分建立兩個資料表：

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

接著去瀏覽器跑 sky.php，跟著指示做即可
會將文章編號及連結存在 sky 資料表
文章標題、內文、日期、人氣存在 sky_pages 資料表
