<h2>Login</h2>

<form action="<?php echo Uri::create('validate/auth') ?>" 
      method="post" autocomplete="off">
  <table>
    <tr>
      <td>user:</td>
      <td><input type="text" name="username" autofocus="on"
                 value="<?php echo $username ?>" /></td>
    </tr>
    <tr>
      <td>password:</td>
      <td><input type="password" name="password" /></td>
    </tr>
    <tr>
      <td></td>
      <td><button type="submit">Access</button></td>
    </tr>
  </table>
</form>
<h3 id="response"><?php echo $message ?></h3>