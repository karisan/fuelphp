<h2>Login</h2>

<form action="<?php echo Uri::create('validate/auth') ?>" 
      method="post" autocomplete="off" class="form-horizontal">
    <div class="control-group">
        <label class="control-label" for="inputUsername">帳號</label>
        <div class="controls">
            <input type="text" name="username" autofocus="on" id="inputUsername"
                   placeholder="帳號" value="<?php echo $username ?>" />
        </div>
    </div>
    <div class="control-group">
        <label class="control-label" for="inputPassword">密碼</label>
        <div class="controls">
            <input type="password" name="password" placeholder="密碼" id="inputPassword" />
        </div>
    </div>
    <div class="control-group">
        <div class="controls">
            <button type="submit" class="btn">登入</button>
        </div>
    </div>
</form>

<h3 id="response"><?php echo $message ?></h3>
