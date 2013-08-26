<!-- 載入 template 範例 -->
<div class="row">
    <div class="span16">
        <?php echo render('mylink'); ?>
    </div>
</div>
<div class="row">
    <div class="span16">
        <form id="myform" action="show_log_detail" method="post">
        <table>
            <tr>
                <td>編號: <input style="width:100px;" id="id" type="text"></div></td>
                <td>帳號: <input style="width:100px;" id="username" type="text"></div></td>
                <td>時間: <input style="width:100px;" id="time" type="text"></div></td>
                <td>IP: <input style="width:100px;" id="ip" type="text"></div></td>
                <td>動作: <input style="width:100px;" id="action" type="text"></div></td>
                <td>狀態: <input style="width:100px;" id="status" type="text"></div></td>
                <td>RUL: <input style="width:100px;" id="url" type="text"></div></td>
                <td>詳細： <input style="width:100px;" id="info" type="text"></div></td>
            </tr>
        </table>
        <input id="q_id" type="hidden" name="id"/>
        <input id="q_str" type="hidden" name="q_str"/>
        </form>
    </div>
</div>
<div class="row">
        <table border="1" class="table table-striped table-bordered table-hover">
            <thead><tr><th colspan="8">Log記錄</th></tr></thead>
            <thead><tr><th width="15">編號</th><th width="20">帳號</th><th>時間</th><th>IP</th>
                <th>動作</th><th>狀態</th><th>RUL</th><th>詳細</th></tr></thead>
            <tbody id="results"></tbody>
        </table>
</div>
