<h2>Cart</h2>
<?php if (isset($cart) && count($cart)): ?>
  <p>the raw cart: <?php print_r($cart) ?></p>
  <form action="<?php echo Uri::create('root/clearcart') ?>">
    <button type='submit'>clear cart</button>
  </form>
<?php else: ?>
  <h3>empty cart</h3>
<?php endif ?>
