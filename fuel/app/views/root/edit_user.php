<div class="row">
    <div>
        <?php echo Form::open(array('action' => 'root/do_edit_user',
                                    'method' => 'post', 'id' => 'myform','name' => 'myform')); ?>

        <p>       <?php echo Form::label('使用者名稱', 'username'); ?>
            <?php echo Form::label($data->username, 'username'); ?>    </p>

        <p>       <?php echo Form::label('Email', 'email'); ?>
            <?php echo Form::input('email', Input::post('email', isset($data) ? $data->email : '')); ?>    </p>

        <p>       <?php echo Form::label('管理者', 'level'); ?>
            <?php
            $level = $data->level;
            if (isset($level)) {
                $tmp_check = $level;
            } else {
                $tmp_check = '0';
            }
            ?>
            <?php echo Form::radio('level', '1',$tmp_check=='1' ? true:false); ?>    </p>
        <p>       <?php echo Form::label('一般', 'level'); ?>
            <?php echo Form::radio('level', '0',$tmp_check=='0' ? true:false); ?>    </p>
        <input type="hidden" name="id" value="<?php echo $data->id; ?>">

        <div class="actions">
            <?php echo Form::submit(); ?>
            <?php echo Form::submit('Cancel','Cancel'); ?>
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