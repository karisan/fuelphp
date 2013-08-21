<?php
$valid = Session::get('valid');
?>
<ul>
    <!--
  <li><?php echo Html::anchor('.', 'Home') ?></li>
  -->
    <li><?php echo Html::anchor('welcome', '首頁') ?></li>
    <?php if (isset($valid)): ?>
        <li><?php echo Html::anchor('root/adduser', '新增使用者'); ?></li>
        <li><?php echo Html::anchor('root/show_user', '使用者管理'); ?></li>
        <li><?php echo Html::anchor('root/show_log', '顯示Log記錄'); ?></li>
        <li><?php echo Html::anchor('root/show_log_cache', '顯示Log記錄(Cache)'); ?></li>
        <li><?php echo Html::anchor('root/show_log2', '顯示Log記錄(Ajax過濾)'); ?></li>
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
    範例帳號：<br>
    bob:bar<br>
    dave:barfoo (admin)<br>
</ul>
