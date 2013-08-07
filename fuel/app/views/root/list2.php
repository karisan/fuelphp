<h2>Book List: Paginate</h2>

<div id="page_bar">
  Pages: &nbsp;
  <?php for ($page = 0; $page < $numpages; ++$page): ?>
    <a class="<?php echo $page == $curr_page ? 'sel' : '' ?>" 
       href="?page=<?php echo $page ?>"><?php echo $page + 1 ?></a>
     <?php endfor ?>
</div>

<table>
  <tr class='fields'>
    <td>
      <?php echo Html::anchor("root/changeorder?order=quantity", 'quantity')?>
    </td>
    <td>
      <?php echo Html::anchor("root/changeorder?order=title", 'title')?>
    </td>
  </tr>
  <?php foreach ($books as $book): ?>
    <tr>
      <td><?php echo $book->quantity ?></td>
      <td>
       <?php echo Html::anchor("root/details?id=$book->id",$book->title)
       ?>
      </td>
    </tr>
<?php endforeach ?>
</table>