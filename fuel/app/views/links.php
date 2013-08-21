<?php
$valid = Session::get('valid');
?>
<ul>
  <li><?php echo Html::anchor('welcome', 'Home') ?></li>
    <li>
    <?php
    if (!isset($valid)):
      echo Html::anchor('validate/login', '登入');
    else:
      echo Html::anchor('validate/logout', "登出 (".$valid->user.")");
    endif;
    ?>
  </li>
</ul>
