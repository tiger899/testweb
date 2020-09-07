<?php

error_reporting(0);
date_default_timezone_set('Asia/Ho_Chi_Minh');
@define('_lib', './admin/lib/');
@define('_source', './sources/');
@define('_template', './templates/');

if (!isset($_SESSION['lang'])) {
    $_SESSION['lang'] = 'vi';
}
$lang = $_SESSION['lang'];

include_once _lib . "config.php";
include_once _lib . "constant.php";
include_once _lib . "functions.php";
include_once _lib . "functions_giohang.php";
include_once _lib . "class.database.php";
$d = new database($config['database']);
require_once _source . "lang.php";
include_once _lib . "file_requick.php";

// Setting
$d->reset();
$sql = "select * from table_setting";
$d->query($sql);
$row_setting = $d->fetch_array();
$ten_format = format_text_sitemap($row_setting['ten']);

header("Content-Type: application/xml; charset=utf-8");
echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">';
echo '<url><loc>' . $config_url_http . 'index/</loc><lastmod>' . date('c', time()) . '</lastmod><changefreq>daily</changefreq><priority>0.1</priority></url>';
echo '<url><loc>' . $config_url_http . 'gioi-thieu/</loc><lastmod>' . date('c', time()) . '</lastmod><changefreq>daily</changefreq><priority>0.1</priority></url>';
echo '<url><loc>' . $config_url_http . 'san-pham/</loc><lastmod>' . date('c', time()) . '</lastmod><changefreq>daily</changefreq><priority>0.1</priority></url>';
echo '<url><loc>' . $config_url_http . 'dich-vu/</loc><lastmod>' . date('c', time()) . '</lastmod><changefreq>daily</changefreq><priority>0.1</priority></url>';
echo '<url><loc>' . $config_url_http . 'tuyen-dung/</loc><lastmod>' . date('c', time()) . '</lastmod><changefreq>daily</changefreq><priority>0.1</priority></url>';
echo '<url><loc>' . $config_url_http . 'tin-tuc/</loc><lastmod>' . date('c', time()) . '</lastmod><changefreq>daily</changefreq><priority>0.1</priority></url>';
echo '<url><loc>' . $config_url_http . 'lien-he/</loc><lastmod>' . date('c', time()) . '</lastmod><changefreq>daily</changefreq><priority>0.1</priority></url>';

function create_sitemap($type = '', $table = '', $level = '', $tail = '', $time = '', $changefreq = '', $priority = '', $lang = 'vi', $orderby = '') {
    global $d, $sitemap, $config_url_http;

    if ($level != "")
        $table = $table . "_" . $level;

    $d->reset();
    $sql = "select ten$lang,id,tenkhongdau,ngaytao from #_$table where type='" . $type . "' order by '" . $orderby . "' desc";
    $d->query($sql);
    $sitemap = $d->result_array();

    for ($i = 0; $i < count($sitemap); $i++) {
        echo '<url>';
        echo '<loc>' . $config_url_http . $sitemap[$i]['tenkhongdau'] . $tail . '</loc>';
        echo '<changefreq>' . $changefreq . '</changefreq>';
        echo '<lastmod>' . date($time, $sitemap[$i]['ngaytao']) . '</lastmod>';
        echo '<priority>' . $priority . '</priority>';
        echo '</url>';
    }
}

/* Sản phẩm và danh mục */
create_sitemap("san-pham", "product", "list", "/", "c", "daily", "1", $lang, "stt,id");
create_sitemap("san-pham", "product", "cat", "/", "c", "daily", "1", $lang, "stt,id");
create_sitemap("san-pham", "product", "", "", "c", "daily", "1", $lang, "stt,id");

/* Bài Viết */
create_sitemap("dich-vu", "news", "", "", "c", "daily", "1", $lang, "stt,id");
create_sitemap("tuyen-dung", "news", "", "", "c", "daily", "1", $lang, "stt,id");
create_sitemap("tin-tuc", "news", "", "", "c", "daily", "1", $lang, "stt,id");

/* Kết Thúc Tạo Sitemap */
echo '</urlset>';
?>