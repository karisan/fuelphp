<h2>Book List: Variable Height Scroll</h2>

<table>
  <tr class='fields'>
    <td>
      <?php echo Html::anchor(
        "/root/changeorder?order=quantity", 'quantity')
      ?>
    </td>
    <td>
      <?php echo Html::anchor(
        "/root/changeorder?order=title", 'title')
      ?>
    </td>
  </tr>
</table>
<div id="display-container">
  <div id="display">
    <table>
      <?php foreach ($books as $book): ?>
        <tr>
          <td><?php echo $book->quantity ?></td>
          <td>
            <?php echo 
            Html::anchor("/root/details?id=$book->id", $book->title)
            ?>
          </td>
        </tr>
      <?php endforeach ?>
    </table>
  </div><!-- display -->
</div><!-- display-container -->
