<!-- 載入 template 範例 -->
<div class="row">
    <div class="span16">
        <?php echo render('mylink'); ?>
    </div>
</div>
<?php echo Form::open(array('method' => 'post', 'id' => 'myform')); ?>
<div class="row">
    <table>
        <tr><td width="200"><h2>Log 詳細資料</h2></td>
            <td width="70"><?php if (isset($new_id)) { ?><a>
                    <div id="record_new" value="<?php echo $new_id; ?>">新一筆</div></a><?php } ?></td>
            <td width="70"><?php if (isset($old_id)) { ?><a>
                    <div id="record_old" value="<?php echo $old_id; ?>">舊一筆</div></a><?php } ?></td>
            <td width="370"><?php if ($q_msg != '') { ?>
                    <b>搜尋條件：<?php echo $q_msg; ?></b>
                    <a><div id="clean">清除條件</div></a>
                <?php } ?></td>
        </tr>
    </table>
</div>
<div class="row">
    <?php
    if ($data->status == 'S') {
        $tmp_status = '成功';
    } elseif ($data->status == 'F') {
        $tmp_status = '失敗';
    } elseif ($data->status == 'S') {
        $tmp_status = '資訊';
    } else {
        $tmp_status = '其它';
    }
    ?>
    <table border="1" class="table table-bordered table-hover table-condensed">
        <tr><td>編號:</td><td><?php echo $data->id ?></td></tr>
        <tr><td>帳號:</td><td><?php echo $data->username ?></td></tr>
        <tr><td>時間:</td><td><?php echo $data->time ?></td></tr>
        <tr><td>IP:</td><td><?php echo $data->ip ?></td></tr>
        <tr><td>動作:</td><td><?php echo $data->action ?></td></tr>
        <tr><td>狀態:</td><td><?php echo $tmp_status ?></td></tr>
        <tr><td>URL:</td><td><?php echo $data->url ?></td></tr>
        <tr><td>詳細:</td><td><pre><?php echo $data->info ?></pre></td></tr>
        <tr><td colspan="2"><?php echo Form::submit(null, '回上頁', array('id' => 'back')); ?></td></tr>
    </table>
</div>
<?php echo Form::hidden('id', $data->id, array('id' => 'q_id')); ?>
<?php if (isset($q_str)) { echo Form::hidden('q_str', $q_str, array('id' => 'q_str')); } ?>
<?php echo Form::close(); ?>
