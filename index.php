<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_STRICT);
session_start();

function arrayColumn($array, $columnName)
{
    $output = array();
    foreach ($array as $key => $value) {
        $output[] = $value[$columnName];
    }

    return $output;
}

$products = array(
    array("name" => "Sledgehammer", "price" => 125.75),
    array("name" => "Axe", "price" => 190.50),
    array("name" => "Bandsaw", "price" => 562.13),
    array("name" => "Chisel", "price" => 12.9),
    array("name" => "Hacksaw", "price" => 18.45)
);

if (isset($_POST["add_to_cart"])) {
    if (isset($_SESSION["shopping_cart"])) {
        $itemName = arrayColumn($_SESSION["shopping_cart"], "product_name");

        if (!in_array($_GET["name"], $itemName)) {
			$productData = array(
                'product_name' => $_POST["hidden_name"],
                'product_price' => $_POST["price"],
                'product_quantity' => $_POST["quantity"],
			);
            $_SESSION["shopping_cart"][$_GET["name"]] = $productData;
        } else {
            $currentProductData = $_SESSION["shopping_cart"][$_GET["name"]];
            $currentProductData['product_quantity'] = $currentProductData['product_quantity'] + $_POST["quantity"];
            $updatedProductData = $currentProductData;

            $_SESSION["shopping_cart"][$_GET["name"]] = $updatedProductData;
        }
    } else {
        $productData = array(
            'product_name' => $_POST["hidden_name"],
            'product_price' => $_POST["price"],
            'product_quantity' => $_POST["quantity"],
        );
        $_SESSION["shopping_cart"][$_GET["name"]] = $productData;
    }
}

if (isset($_GET["action"])) {
    if ($_GET["action"] == "delete") {
        foreach ($_SESSION["shopping_cart"] as $productName => $productData) {
            if ($productData["product_name"] == $_GET["name"]) {
                unset($_SESSION["shopping_cart"][$productName]);
                echo '<script>window.location="index.php"</script>';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
	<head>
		<title>Shopping Cart</title>
		<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css" />
	</head>
	<body>
		<div class="container">
            <h2 class="text-center">Available Products</h2>

            <?php foreach ($products as $product) { ?>
            <div class="col-md-4">
                <form method="post" action="index.php?action=add&name=<?php echo $product["name"]; ?>">
                    <div class="well text-center">

                        <h4 class="text-info"><?php echo $product["name"]; ?></h4>
                        <h4 class="text-danger">&#36; <?php echo number_format($product["price"], 2); ?></h4>

                        <input type="number" name="quantity" value="1" min="1" max="100" class="form-control" />
                        <input type="hidden" name="hidden_name" value="<?php echo $product["name"]; ?>" />
                        <input type="hidden" name="price" value="<?php echo $product["price"]; ?>" />
                        <input type="submit" name="add_to_cart" class="btn btn-success" value="Add to Cart" />

                    </div>
                </form>
            </div>
            <?php } ?>

            <div style="clear:both"></div>
            <br />

            <h2 class="text-center">Shopping Cart</h2>

            <div class="table-responsive">
                <table class="table table-bordered">
                    <tr>
                        <th width="40%">Product Name</th>
                        <th width="10%">Price</th>
                        <th width="10%">Quantity</th>
                        <th width="20%">Total</th>
                        <th width="10%">Action</th>
                    </tr>

                    <?php
                    if (!empty($_SESSION["shopping_cart"])) {
                        $total = 0;

                        foreach ($_SESSION["shopping_cart"] as $productData) {
                    ?>
                    <tr>
                        <td><?php echo $productData["product_name"]; ?></td>
                        <td>&#36; <?php echo number_format($productData["product_price"], 2); ?></td>
                        <td><?php echo $productData["product_quantity"]; ?></td>
                        <td align="right">&#36; <?php echo number_format($productData["product_quantity"] * $productData["product_price"], 2);?></td>
                        <td align="center"><a href="index.php?action=delete&name=<?php echo $productData["product_name"]; ?>"><span class="btn btn-danger">Remove</span></a></td>
                    </tr>
                    <?php
                        $total = $total + ($productData["product_quantity"] * $productData["product_price"]);
                        }
                    ?>

                    <tr>
                        <td colspan="3" align="right">Total</td>
                        <td align="right">&#36; <?php echo number_format($total, 2); ?></td>
                        <td></td>
                    </tr>
                    <?php
                    }
                    ?>
                </table>
            </div>
        </div>
	</body>
</html>
