<h2>Details</h2>
<table id="details">
  <tr><td>id:</td> <td><?php echo $book->id ?></td> </tr>
  <tr><td>title:</td><td><?php echo $book->title ?></td></tr>
  <tr><td>binding:</td> <td><?php echo $book->binding ?></td></tr>
  <tr><td>quantity:</td> <td><?php echo $book->quantity ?></td></tr>
  <tr>
    <td>created:</td> <td><?php echo date("r", $book->created_at) ?></td>
  </tr>
  <tr>
    <td>updated:</td> <td><?php echo date("r", $book->updated_at) ?></td>
  </tr>
</table>
<br />
<form action="<?php echo Uri::create('root/addtocart') ?>" method="post">
  <input type="hidden" name="id" value="<?php echo $book->id ?>" />
  <input type="submit" value="add to cart"/>
</form>

<?php if (isset($valid)): ?>
  <form action="<?php echo Uri::create('books/modify') ?>" 
        class="mod" method="post">
    <input type="hidden" name="id" value="<?php echo $book->id ?>" />
    <button type="submit" name="modify">Modify</button>
  </form>
  <form action="<?php echo Uri::create('books/delete') ?>" 
        method="post" class="mod">
    <input type="hidden" name="id" value="<?php echo $book->id ?>" />
    <button type="submit" name="delete">Delete</button>
  </form>
  <h3><?php echo $message ?></h3>
<?php endif ?>