<?php

session_start();
require 'db.php';

$result = $mysqli->query("SELECT * FROM bakeryitems");
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Shop</title></head>
<body>
<h1>Bakery Items</h1>
<?php while($row = $result->fetch_assoc()): ?>
  <div style="border:1px solid #ccc; padding:10px; width:300px; margin:10px; display:inline-block;">
    <img src="<?php echo htmlspecialchars($row['Image']); ?>" style="width:280px;height:200px;object-fit:cover;"><br>
    <strong><?php echo htmlspecialchars($row['Name']); ?></strong><br>
    LKR <?php echo number_format($row['Price'],2); ?><br>
    <form method="post" action="add_to_cart.php">
      <input type="hidden" name="item_id" value="<?php echo $row['ItemID']; ?>">
      <input type="number" name="qty" value="1" min="1" style="width:60px;">
      <button type="submit">Add to Cart</button>
    </form>
  </div>
<?php endwhile; ?>
<p><a href="cart.php">View Cart</a></p>
</body>
</html>
