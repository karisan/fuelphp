<div class="row">
    <div class="span16">
        <?php echo Form::open(array('action' => 'root/doadduser', 'method' => 'post', 'id' => 'myform','name' => 'myform')); ?>
        <p>       <?php echo Form::label('使用者名稱', 'username'); ?>
            <?php echo Form::input('username', Input::post('username', isset($username) ? $username : '')); ?>    </p>

        <p>       <?php echo Form::label('密碼', 'password'); ?>
            <?php echo Form::password('password'); ?>    </p>

        <p>       <?php echo Form::label('再次輸入密碼', 'repassword'); ?>
            <?php echo Form::password('repassword'); ?>    </p>

        <p>       <?php echo Form::label('Email', 'email'); ?>
            <?php echo Form::input('email', Input::post('email', isset($email) ? $email : '')); ?>    </p>

        <p>       <?php echo Form::label('管理者', 'level'); ?>
            <?php
            if (isset($level)) {
                $tmp_check = $level;
            } else {
                $tmp_check = '0';
            }
            ?>
            <?php echo Form::radio('level', '1',$tmp_check=='1' ? true:false); ?>    </p>
        <p>       <?php echo Form::label('一般', 'level'); ?>
            <?php echo Form::radio('level', '0',$tmp_check=='0' ? true:false); ?>    </p>

        <div class="actions">
            <?php echo Form::submit(); ?>
            <?php echo Form::submit('Cancel','Cancel'); ?>
            <?php echo Form::hidden(Config::get('security.csrf_token_key'), Security::fetch_token()); ?>
        </div>
        <?php echo Form::close(); ?>
        <?php if(isset($messages) and count($messages)>0): ?>
            <div class="message">
                <ul>
                    <?php
                    foreach($messages as $msg)
                    {
                        echo '<li>', $msg,'</li>';
                    }
                    ?>
                </ul>
            </div>
        <?php endif; ?>
    </div>
</div>
