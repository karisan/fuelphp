<!-- 載入 template 範例 -->
<div class="row">
    <div class="span16">
        <?php echo render("mylink"); ?>
    </div>
</div>
<div class="row">
        <table border="1" class="table table-striped table-bordered table-hover">
            <thead><tr><th colspan="4">使用者管理</th></tr></thead>
            <thead><tr><th>帳號</th><th>Email</th><th>權限</th><th>管理</th></tr></thead>
            <?php foreach ($data as $rows): ?>
            <tr>
                <td><?php echo $rows['username']; ?></td>
                <td><?php echo $rows['email']; ?></td>
                <td><?php echo $rows['level']; ?></td>
                <td>
                <?php echo Html::anchor('root/edit_user?id='.$rows['id'],html_tag('i', array('class' => 'icon-edit'),'').' 修改')?>&nbsp;&nbsp;
                <?php echo Html::anchor('root/reset_user_pass?id='.$rows['id'],html_tag('i', array('class' => 'icon-refresh'),'').' 重設密碼')?>&nbsp;&nbsp;
                <?php echo Html::anchor('root/do_del_user?id='.$rows['id'],html_tag('i', array('class' => 'icon-remove'),'').' 刪除')?>&nbsp;&nbsp;
                </td>
            </tr>
            <?php endforeach ?>
        </table>
</div>
