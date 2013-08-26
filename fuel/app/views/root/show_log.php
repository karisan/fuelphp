<!-- 載入 template 範例 -->
<div class="row">
    <div class="span16">
        <?php echo render("mylink"); ?>
    </div>
</div>
<div class="row">
        <table border="1" class="table table-striped table-bordered table-hover">
            <thead><tr><th colspan="8">Log記錄</th></tr></thead>
            <thead><tr><th width="15">編號</th><th width="20">帳號</th><th>時間</th><th>IP</th><th>動作</th><th>狀態</th><th>RUL</th><th>詳細</th></tr></thead>
            <?php foreach ($data as $rows): ?>
            <?php
            /*
            if ($rows['status']=='S') {
                $tmp_status = '成功';
            } elseif ($rows['status']=='F') {
                $tmp_status = '失敗';
            } elseif ($rows['status']=='S') {
                $tmp_status = '資訊';
            } else {
                $tmp_status = '其它';
            }*/
            ?>
            <tr id="log_<?php echo $rows['id']; ?>" onclick="window.location='<?php echo Uri::create('root/show_log_detail?id='.$rows['id']); ?>'">
                <td><?php echo $rows['id']; ?></td>
                <td><?php echo substr($rows['username'], 0, 15); ?></td>
                <td><?php echo $rows['time']; ?></td>
                <td><?php echo $rows['ip']; ?></td>
                <td><?php echo substr($rows['action'], 0, 20); ?></td>
                <td><?php echo $rows['status']; ?></td>
                <td><?php echo substr($rows['url'], 0, 50); ?></td>
                <td><?php echo substr($rows['info'], 0, 50); ?></td>
            </tr>
            <?php endforeach ?>
        </table>
</div>
