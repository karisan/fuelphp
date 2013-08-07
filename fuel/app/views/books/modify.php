<h2>Modify a Book</h2>

<form action="" method="post">
<table>
  <tr>
    <td>title:</td>
    <td>
      <input type="text" name="title" value="<?php echo $title ?>" />
      <span class='message'>
        <?php if (isset($errors['title'])) echo $errors['title']; ?>
      </span>
    </td>
  </tr>
  <tr>
    <td>qty:</td>
    <td>
      <input type="text" name="qty" value="<?php echo $qty ?>" />
      <span class='message'>
        <?php if (isset($errors['qty'])) echo $errors['qty']; ?>
      </span>
    </td>
  </tr>
  <tr>
    <td>binding:</td>
    <td>
      <?php foreach ($bindings as $value): ?>
      <input type="radio" name="binding" value="<?php echo $value?>" 
             <?php if ($value == $binding) echo "checked" ?>
             /> <?php echo $value?>
      <?php endforeach ?>
    </td>
  </tr>
  <tr>
    <td></td><td><button type='submit' name="doit">modify</button></td>
  </tr>
</table>
<input type="hidden" name="id" value="<?php echo $id?>" />
</form>

<h3 class='message'><?php echo $message ?></h3>