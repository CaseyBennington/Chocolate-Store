<?php
# add_products.php
// This page allows the administrator to add a product.
// Include the configuration file:
require ('../includes/config.inc.php');

// Set the page title and include the HTML header:
$myscript = 'add_product.php';
$page_title = 'Add/Update a Product';
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

if ($_SERVER['REQUEST_METHOD'] == 'POST') { // Handle the form.
    // Validate the incoming data...
    $errors = array();

    // Check for a product name:
    if (!empty($_POST['product_name'])) {
        $pn = trim($_POST['product_name']);
    } else {
        $errors[] = 'Please enter the product\'s name!';
    }

    // Check for an image:
    if (is_uploaded_file($_FILES['image']['tmp_name'])) {

        // Create a temporary file name:
        $temp = '../images/' . md5($_FILES['image']['name']);

        // Move the file over:
        if (move_uploaded_file($_FILES['image']['tmp_name'], $temp)) {

            echo '<p class="update">The file has been uploaded!</p>';

            // Set the $i variable to the image's name:
            $i = $_FILES['image']['name'];
        } else { // Couldn't move the file over.
            $errors[] = 'The file could not be moved.';
            $temp = $_FILES['image']['tmp_name'];
        }
    } else { // No uploaded file.
        $errors[] = 'No file was uploaded.';
        $temp = NULL;
    }

    // Check for a size:
    if (!empty($_POST['size'])) {
        $s = trim($_POST['size']);
    } else {
        $errors[] = 'Please enter the product\'s size!';
    }

    // Check for a price:
    if (is_numeric($_POST['price']) && ($_POST['price'] > 0)) {
        $p = (float) $_POST['price'];
    } else {
        $errors[] = 'Please enter the product\'s price!';
    }

    // Check for a quantity:
    if (is_numeric($_POST['quantity']) && ($_POST['quantity'] > 0)) {
        $d = (float) $_POST['quantity'];
    } else {
        $errors[] = 'Please enter the product\'s quantity!';
    }

    if (empty($errors)) { // If everything's OK.
        // Add the product to the database:
        $q = 'INSERT INTO products (product_name, size, price, quantity, image_name) VALUES (?, ?, ?, ?, ?)';
        $stmt = mysqli_prepare($dbc, $q);
        mysqli_stmt_bind_param($stmt, 'ssdds', $pn, $s, $p, $d, $i);
        mysqli_stmt_execute($stmt);

        // Check the results...
        if (mysqli_stmt_affected_rows($stmt) == 1) {

            // Print a message:
            echo '<p class="update">The product has been added.</p>';

            // Rename the image:
            $id = mysqli_stmt_insert_id($stmt); // Get the product ID.
            rename($temp, "../images/$id");

            // Clear $_POST:
            $_POST = array();
        } else { // Error!
            echo '<p style="font-weight: bold; color: #C00">Your submission could not be processed due to a system error.</p>';
        }

        mysqli_stmt_close($stmt);
    } // End of $errors IF.
    // Delete the uploaded file if it still exists:
    if (isset($temp) && file_exists($temp) && is_file($temp)) {
        unlink($temp);
    }
} // End of the submission IF.
// New
// Check for any errors and print them:
if (!empty($errors) && is_array($errors)) {
    echo '<h1>Error!</h1>
	<p style="font-weight: bold; color: #C00">The following error(s) occurred:<br />';
    foreach ($errors as $msg) {
        echo " - $msg<br />\n";
    }
    echo 'Please reselect the product image and try again.</p>';
}

// Display the form...
?>
<h3 class="update">Add or Update a Product</h3>
<?php
include ('../includes/admin_nav.html');
echo "<p class=usercount></p>\n";
?>
<form class="product_form" enctype="multipart/form-data" action="add_product.php" method="post">
    <fieldset><legend>Fill out the form to add or update a product into the catalog:</legend>
        <input type="hidden" name="MAX_FILE_SIZE" value="5242880" />
        <div>
            <label for="email">Product Name:</label>
            <input class="required" required type="text" name="product_name" id="product_name" size="30" maxlength="60" value="<?php if (isset($_POST['product_name'])) echo htmlspecialchars($_POST['product_name']); ?>" />
        </div>
        <div>
            <label for="email">Image:</label>
            <input type="file" name="image" id="image" />
        </div>
        <div>
            <label for="email">Price:</label>
            <input class="required" required title="Please enter a positive number." type="number" name="price" id="price" size="10" maxlength="10" min="0" max="999999" step=".01" value="<?php if (isset($_POST['price'])) echo $_POST['price']; ?>" /> <small>Do not include the dollar sign or commas.</small>
        </div>
        <div>
            <label for="email">Size:</label>
            <input class="required" required type="text" name="size" id="size" size="30" maxlength="60" value="<?php if (isset($_POST['size'])) echo htmlspecialchars($_POST['size']); ?>" />
        </div>
        <div>
            <label for="email">Quantity:</label>
            <input class="required" required title="Please enter a positive whole number." type="number" name="quantity" id="quantity" size="10" min="0" max="999999" minlength ="1" maxlength="10"<?php if (isset($_POST['quantity'])) echo $_POST['quantity']; ?> />
        </div>
        <div align="center"><input type="submit" name="submit" value="Submit" /></div>
    </fieldset>
</form>

<?php
include ('../includes/footer_admin.html');
?>
