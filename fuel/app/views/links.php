<?php
$valid = Session::get('valid');
?>
<ul>
  <!--
  <li><?php echo Html::anchor('.', 'Home') ?></li>
  -->
  <li><?php echo Html::anchor('hello', 'Home') ?></li>
  <li><?php echo Html::anchor('root/list1', 'List1') ?></li>
  <li><?php echo Html::anchor('root/list2', 'List2') ?></li>
  <li><?php echo Html::anchor('root/list3', 'List3') ?></li>
  <li><?php echo Html::anchor('root/list4', 'List4') ?></li>
  <li><?php echo Html::anchor('root/cart', 'Cart') ?></li>
  <?php if (isset($valid)): ?>
    <li><?php echo Html::anchor('books/add', 'Add Book'); ?></li>
  <?php endif ?>  
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
