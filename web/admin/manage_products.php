<?php
    # admin.php
    // This is the admin page for the site.
    // Include the configuration file:
    require ('../includes/config.inc.php');

    // Set the page title and include the HTML header:
    $myscript = 'manage_products.php';
    $page_title = 'Product Administration';
    include ('../includes/header.html');
    // load any page, check if logged in. if logged in, redirect to index. if not direct to login.
    if ($_SESSION['customer_level'] != 1) {
        // check if the customer is an administrator:
        $url = BASE_URL . 'index.php'; // Define the URL.
        ob_end_clean(); // Delete the buffer.
        header("Location: $url");
    }

    // Welcome the cutstomer (by name if they are logged in):
    echo '<h1>Welcome to the administration';
    echo ", {$_SESSION['first_name']}";
    echo '!</h1>';
    require ("../" . MYSQL);

    $q = 'SELECT COUNT(product_id) FROM products';
    $r = @mysqli_query($dbc, $q);
    $row = @mysqli_fetch_array($r, MYSQLI_NUM);
    $num = $row[0];
    echo "<!-- row=$row -->";
    echo '<h3 class="update">Products</h3>';

    include ('../includes/admin_nav.html');

    echo "<p class=usercount>There are currently $num product(s). <a id=\"add_product\" href=\"add_product.php\" title=\"Add Product\"><span>Add Product</span></a></p>\n";


    $order_by = 'product_name'; // Define this var for table.products.inc.php.
    $sort = 'pn';  // Define this var for table.inc.products.php.

    include ('../includes/table.inc.php');
    include ('../includes/footer_admin.html');
?>
