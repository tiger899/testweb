<!DOCTYPE html>
<html lang="vi">
    <head><?php include _source . "head_main.php"; ?></head>
    <body onLoad="<?php if ($com == 'map') echo 'initialize()'; ?>">
        <?php
        include _source . "seo.php";
        include _template . "layout/header.php";
        include _template . "layout/menu.php";
        include _template . "layout/slide.php";
        if ($source != 'index') {
            include _template . "layout/crumbtrail.php";
        }
        ?>
        <div class="wrap-main w-clear">
            <?php include _template . $template . "_tpl.php"; ?>
        </div>
        <?php
        include _template . "layout/footer.php";
        include _template . "layout/chat_zalo.php";
        include _template . "layout/chat_fb.php";
        include _source . "head_js.php";
       
        ?>
    </body>
</html>