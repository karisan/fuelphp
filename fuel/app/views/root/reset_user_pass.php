<div class="row">
    <div>
        <div class="actions">

            <?php echo Form::open(array('action' => 'root/do_reset_user_pass',
                'method' => 'post', 'id' => 'myform','name' => 'myform')); ?>

            <p>       <?php echo Form::label('使用者名稱', 'username'); ?>
                <?php echo Form::label($data->username, 'username'); ?>    </p>

            <p>       <?php echo Form::label('密碼', 'password'); ?>
                <?php echo Form::password('password'); ?>    </p>

            <input type="hidden" name="id" value="<?php echo $data->id; ?>">

            <div class="actions">
                <?php echo Form::submit(); ?>
                <?php echo Form::submit('Cancel','Cancel'); ?>
            </div>
            <?php echo Form::close(); ?>
        <?php if(isset($messages) and count($messages)>0) { ?>
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
        <?php } ?>
        </div>
    </div>
</div>
