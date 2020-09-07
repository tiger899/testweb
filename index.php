<?php

session_start();
date_default_timezone_set('Asia/Ho_Chi_Minh');
$session = session_id();
error_reporting(0);

@define('_lib', './admin/lib/');
@define('_source', './sources/');
@define('_template', './templates/');

include_once _lib . "config.php"; 
include_once _lib . "class.database.php"; 
$d = new database($config['database']);

// Setting
$d->reset();
$sql = "select * from table_setting";
$d->query($sql);
$row_setting = $d->fetch_array();

if ($_REQUEST['lang'] != '')
    $_SESSION['lang'] = $_REQUEST['lang'];
else if (!isset($_SESSION['lang']) && !isset($_REQUEST['lang']))
    $_SESSION['lang'] = $row_setting['lang_default'];
$lang = $_SESSION['lang'];

include_once _lib . "Mobile_Detect.php";
$detect = new Mobile_Detect;
$deviceType = ($detect->isMobile() ? ($detect->isTablet() ? 'tablet' : 'phone') : 'computer');

include_once _lib . "constant.php";
include_once _lib . "functions.php";
include_once _lib . "functions_giohang.php";
require_once _source . "lang.php";
include_once _lib . "file_requick.php";
include_once _source . "counter.php";
include_once _source . "useronline.php";
include_once _source . "allpage.php";

if ($_REQUEST['command'] == 'delete') {
    remove_product($_REQUEST['pid'], $_REQUEST['mau'], $_REQUEST['size']);
} else if ($_REQUEST['command'] == 'update') {
    update_product($_REQUEST['pid'], $_REQUEST['mau'], $_REQUEST['size'], $_REQUEST['mauold'], $_REQUEST['sizeold']);
} else if ($_REQUEST['command'] == 'clear') {
    unset($_SESSION['cart']);
    unset($_SESSION['coupon']);
}

/* Kiểm Tra Và Đăng Nhập Bằng Cookie Login */
if (isset($_COOKIE['iduser']) && $_COOKIE['iduser'] > 0)
    login_by_cookie($_COOKIE['iduser']);
include "main_desktop.php";
?>