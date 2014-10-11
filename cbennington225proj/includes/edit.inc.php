<?php
// Check if the form has been submitted:
if (isset($_POST['submitted'])) {

    if (!$_POST['submit']) {
        if ($_POST['DELETE']) {
            if (isset($_POST['id'])) {
                $id = $_POST['id'];
                $q = "DELETE FROM products WHERE product_id='$id'";
                $r = mysqli_query($dbc, $q) or die(mysql_error()); // Run the query.
                $num = @mysqli_num_rows($r);
                // if ($num ==1) {
                if ($q) {
                    header('location:manage_products.php');
                }
            } else {
                header('location:manage_products.php');
            }
        }
    }

    $errors = array();

    // Check for a product name:
    if (empty($_POST['product_name'])) {
        $errors[] = 'You forgot to enter your product name.';
    } else {
        $productname = mysqli_real_escape_string($dbc, trim($_POST['product_name']));
    }

    // Check for an image:
    if (is_uploaded_file($_FILES['image']['tmp_name'])) {

        // Create a temporary file name:
        $temp = '../images/' . md5($_FILES['image']['name']);

        // Move the file over:
        if (move_uploaded_file($_FILES['image']['tmp_name'], $temp)) {

            echo '<p class="update">The file has been uploaded!</p>';

            // Set the $i variable to the image's name:
            $image = $_FILES['image']['name'];
        } else { // Couldn't move the file over.
            $errors[] = 'The file could not be moved.';
            $temp = $_FILES['image']['tmp_name'];
        }
    } else { // No uploaded file.
        //$errors[] = 'No file was uploaded.';
        $temp = NULL;
        echo '<p class="update">No image was uploaded.</p>';
    }

    // Check for an price:
    if (empty($_POST['price'])) {
        $errors[] = 'You forgot to enter your product price.';
    } else {
        $price = mysqli_real_escape_string($dbc, trim($_POST['price']));
    }

    // Check for an size:
    if (empty($_POST['size'])) {
        $errors[] = 'You forgot to enter your product size.';
    } else {
        $size = mysqli_real_escape_string($dbc, trim($_POST['size']));
    }

    // Check for an quantity:
    if (empty($_POST['quantity'])) {
        $errors[] = 'You forgot to enter your product quantity.';
    } else {
        $quantity = mysqli_real_escape_string($dbc, trim($_POST['quantity']));
    }

    if (empty($errors)) { // If everything's OK.
        //  Test for unique email address:
        $q = "SELECT product_name FROM products WHERE product_id='$id'";
        $r = @mysqli_query($dbc, $q);
        if (mysqli_num_rows($r) == 1) {

            // Make the query:
            if (isset($image)) {
                $q = "UPDATE products SET product_name='$productname', image_name='$image', price='$price', size='$size', quantity='$quantity' WHERE product_id=$id LIMIT 1";
            } else {
                $q = "UPDATE products SET product_name='$productname', price='$price', size='$size', quantity='$quantity' WHERE product_id=$id LIMIT 1";
            }
            $r = @mysqli_query($dbc, $q);
            if (mysqli_affected_rows($dbc) == 1) { // If it ran OK.
                // Print a message:
                ?>
                <p class="update">Thank you!</p>
                <p class="update">The product has been updated.</p><br />
                <p class="update">Changes are shown</p>
                <script type="text/javascript">
                    var f = document.getElementById("editForm");
                    f.firstname.disabled = true;
                </script>
                <?php
                if (isset($image)) {
                    rename($temp, "../images/$id");
                }

                // Clear $_POST:
                $_POST = array();
            } else if ($r == TRUE) {
                echo '<p class="update">Nothing to update, no changes were made.</p>';
            } else { // If it did not run OK.
                echo "<!-- r=$r -->\n";
                echo '<p class="error">The user could not be edited due to a system error. We apologize for any inconvenience.</p>'; // Public message.
                echo '<p>' . mysqli_error($dbc) . '<br />Query: ' . $q . '</p>'; // Debugging message.
            }
        } else { // Already registered.
            echo '<p class="error">Product ' . $productname . " product_id=" . $id . " has never been entered.</p>";
        }
    }
} else {
    $q = "SELECT * FROM products WHERE product_id='$id';";
    $r = mysqli_query($dbc, $q); // Run the query.
    $num = @mysqli_num_rows($r);
    if ($num > 0) { // The person exists
        $row = mysqli_fetch_array($r, MYSQLI_ASSOC);
        $productname = $row['product_name'];
        //$image = $row['image'];
        $price = $row['price'];
        $size = $row['size'];
        $quantity = $row['quantity'];
    } else {
        $error = '<p class="error">This page has been accessed in error.</p>';
    }
    // Delete the uploaded file if it still exists:
    if (isset($temp) && file_exists($temp) && is_file($temp)) {
        unlink($temp);
    }
}  // End of submit conditional.
// Always show the form...

if (empty($errors)) { // If everything's OK.

    /*
     * N E W 
     * F O R M
     */
    ?> 
    <form class="product_form" id="editForm" enctype="multipart/form-data" action="<?php echo $myscript; ?>" method="post">
        <fieldset><legend>Edit the form to update a product in the catalog:</legend>
            <input type="hidden" name="MAX_FILE_SIZE" value="5242880" />
            <div>
                <label for="email">Product Name:</label>
                <input class="required" required type="text" name="product_name" id="product_name" size="30" maxlength="60" value="<?php if (isset($productname)) echo $productname; ?>" />
            </div>
            <div>
                <label for="email">Image:</label>
                <input type="file" name="image" id="image" /><small>Enter nothing here to keep same image.</small>
            </div>
            <div>
                <label for="email">Price:</label>
                <input class="required" required title="Please enter a positive number." type="number" name="price" id="price" size="10" maxlength="10" min="0" max="999999" step=".01" value="<?php if (isset($price)) echo $price; ?>" /><small>Do not include the dollar sign or commas.</small>
            </div>
            <div>
                <label for="email">Size:</label>
                <input class="required" required type="text" name="size" id="size" size="30" maxlength="60" value="<?php if (isset($size)) echo $size; ?>" />
            </div>
            <div>
                <label for="email">Quantity:</label>
                <input class="required" required title="Please enter a positive whole number." type="number" name="quantity" id="quantity" size="10" min="0" max="999999" minlength ="1" maxlength="10" value="<?php if (isset($quantity)) echo $quantity; ?>" />
            </div>
            <div class="btn_parent" align="center">
                <div class="product_edit_btn" align="center"><input type="submit" name="submit" value="Submit" /></div>
                <div class="product_edit_btn" align="center"><input type="submit" name="DELETE" value="Delete" /></div>
            </div>
            <?php
            echo '<input type="hidden" name="submitted" value="TRUE" />
	              <input type="hidden" name="id" value="' . $id . '" />';
            ?></fieldset>
    </form>
    <?php
} else { // Not a valid user ID.
    echo '<p class="error">The following error(s) occurred:<br />';
    foreach ($errors as $msg) { // Print each error.
        echo " - $msg<br />\n";
    }
    echo '</p><p>Please try again.</p>';
}

mysqli_close($dbc);
?>