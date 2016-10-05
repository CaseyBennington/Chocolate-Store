<?php
    // This page is for editing a product record.
    // This page is accessed through manage_products.php.
    require ('../includes/config.inc.php');
    // Set the page title and include the HTML header:
    $myscript = 'edit_products.php';
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
    require (".." . MYSQL);

    // Check for a valid product ID, through GET or POST:
    if ((isset($_GET['id'])) && (is_numeric($_GET['id']))) { // From manage_inventory.php
        $id = $_GET['id'];
    } elseif ((isset($_POST['id'])) && (is_numeric($_POST['id']))) { // Form submission.
        $id = $_POST['id'];
    } else { // No valid ID, kill the script.
        echo '<p class="error">This page has been accessed in error.</p>';
        include ('../includes/footer_admin.html');
        exit();
    }

    echo '<h3 class="update">Edit a Product</h3>';
    include ('../includes/admin_nav.html');
    echo "<p class=usercount></p>\n";
    include ('../includes/edit.inc.php');
    include ('../includes/footer_admin.html');
?>
