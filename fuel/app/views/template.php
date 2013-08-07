<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <title><?php if (isset($page_title)) echo $page_title ?></title>
    <?php echo Asset::css(array('layout.css')) ?>
    <?php if (isset($css_files)) echo Asset::css($css_files)?> 
    <?php if (isset($style)) echo $style?>
    <?php echo Asset::js(array('jquery-1.6.4.min.js')) ?>
    <?php if (isset($js_files)) echo Asset::js($js_files)?>
    <?php if (isset($script)) echo $script?> 
  </head>
  <body>
    <div id="container">
      <div id="header"><?php echo render("header") ?></div>
      <div id="navigation"><?php echo render("links") ?></div>
      <div id="content"><!-- content -->
        <?php echo $content ?>
      </div><!-- content -->
    </div><!-- container -->
  </body>
</html>