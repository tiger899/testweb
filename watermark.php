<?php
    @session_start();

    @define ( '_template' , './templates/');
    @define ( '_source' , './sources/');
    @define ( '_lib' , './admin/lib/');
  
    if(!isset($_SESSION['lang']))
    {
        $_SESSION['lang']='vi';
    }
    $lang=$_SESSION['lang'];
  
    include_once _lib."config.php";
    include_once _lib."constant.php";
    include_once _lib."functions.php";
    include_once _lib."class.database.php";
  
    $d = new database($config['database']);

    $d->reset();
    $sql="select thumb from #_photo ";
    if($_GET['kind']=='detail') $kind='watermark-chitiet';
    else $kind='watermark';
    $sql.="where type='".$kind."' and hienthi>0 and act='photo_static'";
    $d->query($sql);
    $dongdau = $d->fetch_array();

    if(isset($_GET['src'])) 
    {
        if($dongdau['thumb']!='')
        {
            $wmi = 'upload/photo/'.$dongdau['thumb'];
        }
        else
        {
            $wmi = "assets/images/watermark.png";
        }
        if(!is_dir('upload/cache/'))
        {
            mkdir('upload/cache/',0755);
        }
        $imge = $img = 'upload/cache/'.uniqid().".".strtolower(pathinfo( $_GET['src'], PATHINFO_EXTENSION ));
        $bas = $config_url_http;
        $ourl = $bas.'timthumb.php?';
        $ourl .= attach_param_link();
        file_put_contents($img, file_get_contents(myUrlEncode($ourl)));
        createwmimage($img,$wmi,param('p',5),param('o',100));
    }

    function myUrlEncode($string) 
    {
        $entities = array('%21', '%2A', '%27', '%28', '%29', '%3B', '%3A', '%40', '%26', '%3D', '%2B', '%24', '%2C', '%2F', '%3F', '%25', '%23', '%5B', '%5D');
        $replacements = array('!', '*', "'", "(", ")", ";", ":", "@", "&", "=", "+", "$", ",", "/", "?", "%", "#", "[", "]");
        return str_replace($entities, $replacements, urlencode($string));
    }

    function attach_param_link() 
    {
        $url = '';
        $param = array("src","w","h","q","a","zc","f","s","cc","ct");
        foreach ($param as $value) 
        {
            if (isset ($_GET[$value])) {
                $url = $url."&".$value."=".$_GET[$value];
            }
        }
        return $url;
    }

    function resize_dimensions($goal_width,$goal_height,$width,$height) 
    { 
        $return = array('width' => $width, 'height' => $height);
        if ($width/$height > $goal_width/$goal_height && $width > $goal_width) 
        { 
            $return['width'] = $goal_width; 
            $return['height'] = $goal_width/$width * $height; 
        }
        else if ($height > $goal_height) 
        { 
            $return['width'] = $goal_height/$height * $width; 
            $return['height'] = $goal_height; 
        } 
        return $return; 
    }

    function param($property, $default = '') 
    {
        if (isset ($_GET[$property])) 
        {
            return $_GET[$property];
        } 
        else 
        {
            return $default;
        }
    }

    function checkimg($name) 
    {
        $ext =  strtolower(pathinfo( $name, PATHINFO_EXTENSION ));
        if($ext=='jpg' || $ext=='jpeg')
        $val = imagecreatefromjpeg($name);
        else if($ext=='png')
        $val = imagecreatefrompng($name);
        else if($ext=='gif')
        $val = imagecreatefromgif($name);
        return $val;
    }

    function createwmimage($s,$t,$p,$o) 
    {
        $main_img       = $s; // main big photo / picture
        $watermark_img  = $t; // use GIF or PNG, JPEG has no tranparency support
        $padding        = $p; // distance to border in pixels for watermark image
        $opacity        = $o; // image opacity for transparent watermark

        $watermark  = checkimg($watermark_img);
        $image      = checkimg($main_img);

        if(!$image || !$watermark) die("Error: main image or watermark could not be loaded!");

        $watermark_size     = getimagesize($watermark_img);
        $watermark_width    = $watermark_size[0];  
        $watermark_height   = $watermark_size[1];
        $image_size     = getimagesize($main_img);
        unlink($main_img);

        $img_width = $image_size[0];
        $img_height = $image_size[1];

        if(isset($_GET['twper'])) 
        {
            $thumb_perwidth = ($_GET['twper'])/100;
        } 
        else 
        {
            $thumb_perwidth = 0.5;
        }

        if(isset($_GET['thper'])) 
        {
            $thumb_perheight = ($_GET['thper'])/100;
        } 
        else
        {
            $thumb_perheight = 0.5;
        }

        $wmdim = resize_dimensions($img_width*$thumb_perwidth,$img_height*$thumb_perheight,$watermark_width,$watermark_height);
        $watermark_width =  $wmdim['width'];
        $watermark_height = $wmdim['height'];
        $watermark  = checkimg($watermark_img);
        $new_image = imagecreatetruecolor ( $watermark_width, $watermark_height );
        imagealphablending($new_image , false);
        imagesavealpha($new_image , true);
        imagecopyresampled ( $new_image, $watermark, 0, 0, 0, 0, $watermark_width, $watermark_height, imagesx ( $watermark ), imagesy ( $watermark ) );
        $watermark = $new_image;
        $dest_x = $_GET['img_x'];
        $dest_y = $_GET['img_y'];
        imagecopy($image, $watermark, $dest_x, $dest_y, 0, 0, $watermark_width, $watermark_height);
        header("content-type: image/jpeg");
        imagejpeg($image);
        imagedestroy($image);
        imagedestroy($watermark);
    }
?>