<?php
# checkout.php
// This page inserts the order information into the table.
// This page would come after the billing process.
// This page assumes that the billing process worked (the money has been taken).
// Include the configuration file:
require ('includes/config.inc.php');
// Set the page title and include the HTML header:
$page_title = 'Order Confirmation';
include ('includes/header.html');
if (!isset($_SESSION['first_name'])) {
    $url = BASE_URL . 'login.php'; // Define the URL.
    ob_end_clean(); // Delete the buffer.
    header("Location: $url");
}

require (MYSQL);
// The customer is logged in:
$cid = $_SESSION['customer_id'];

// Turn autocommit off:
mysqli_autocommit($dbc, FALSE);

$q = "SELECT * FROM products ORDER BY product_name";
$r = mysqli_query($dbc, $q);
// This page calculates the order total: //////////////////
$total = 0;
$backOrdered = array();
while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
    if (is_numeric($_POST[$row['product_id']])) {
        $total += $_POST[$row['product_id']] * $row['price'];
    } else {
        mysqli_rollback($dbc);
        echo '<p>Your order quantity was not a valid number. Please try again.</p>';
        header('location:index.php');
    }

    //Update inventory
    $newQty = $row['quantity'] - $_POST[$row['product_id']];
    $q2 = "UPDATE products SET quantity = $newQty WHERE product_id = '" . $row['product_id'] . "'";
    $r2 = mysqli_query($dbc, $q2);
    if ($newQty < 0) {
        $backOrdered[$row['product_name']] = $row['product_name'] . " is on back order. This item will be shipped as soon as possible.";
    }
}

// Update the customer's information ///////////////////
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $errors = array(); // Initialize an error array.
    // Check for a first name:
    if (empty($_POST['fName'])) {
        $errors[] = 'You forgot to enter your first name.';
    } else {
        $fn = mysqli_real_escape_string($dbc, trim($_POST['fName']));
    }

    // Check for a last name:
    if (empty($_POST['lName'])) {
        $errors[] = 'You forgot to enter your last name.';
    } else {
        $ln = mysqli_real_escape_string($dbc, trim($_POST['lName']));
    }

    // Check for a street:
    if (empty($_POST['street'])) {
        $errors[] = 'You forgot to enter your street address.';
    } else {
        $s = mysqli_real_escape_string($dbc, trim($_POST['street']));
    }

    // Check for a city:
    if (empty($_POST['city'])) {
        $errors[] = 'You forgot to enter your city.';
    } else {
        $c = mysqli_real_escape_string($dbc, trim($_POST['city']));
    }

    // Check for a state:
    if (empty($_POST['state'])) {
        $errors[] = 'You forgot to enter your state.';
    } else {
        $state = mysqli_real_escape_string($dbc, trim($_POST['state']));
    }

    // Check for a zip:
    if (empty($_POST['zip'])) {
        $errors[] = 'You forgot to enter your zip.';
    } else {
        $z = mysqli_real_escape_string($dbc, trim($_POST['zip']));
    }

    // Check for an email address:
    if (empty($_POST['email'])) {
        $errors[] = 'You forgot to enter your email address.';
    } else {
        $e = mysqli_real_escape_string($dbc, trim($_POST['email']));
    }

    if (empty($errors)) { // If everything's OK.
        // Update the user in the database...
        // Make the query:
        $q = "UPDATE customers SET first_name = '$fn', last_name = '$ln', street = '$s', city = '$c', state = '$state', zip = '$z', email = '$e' WHERE customer_id = '$cid'";
        $r = mysqli_query($dbc, $q); // Run the query.
        if (mysqli_affected_rows($dbc) == 1) { // If it ran OK.
            // Print a message:
            echo '<h1>Thank you!</h1>
		  <p class="update">You\'re customer information was updated!</p><br>';
        } else if ($r == TRUE) {
            echo '<h1>Thank you!</h1><p class="update">You\'re customer information has not changed, no changes were made to your customer information.</p><br>';
        } else { // If it did not run OK.
            mysqli_rollback($dbc);
            // Public message:
            echo '<h1>System Error</h1>
			<p class="error">Your customer information could not be updated due to a system error. We apologize for any inconvenience.</p>'; // Public message.
            // Debugging message:
            echo '<p>' . mysqli_error($dbc) . '<br /><br />Query: ' . $q . '</p>'; // Debugging message.
            mysqli_close($dbc); // Close the database connection.
            // Include the footer and quit the script:
            include ('includes/footer.html');
            exit();
        } // End of if ($r) IF.
    } else { // Report the errors.
        mysqli_rollback($dbc);
        echo '<h1>Error!</h1>
		<p class="error">The following error(s) occurred:<br />';
        foreach ($errors as $msg) { // Print each error.
            echo " - $msg<br />\n";
        }
        echo '</p><p>Please try again.</p><p><br /></p>';
        mysqli_close($dbc); // Close the database connection.
        // Include the footer and quit the script:
        include ('includes/footer.html');
        exit();
    } // End of if (empty($errors)) IF.
}

// Add the order to the orders table...
$q = "INSERT INTO orders (customer_id, total) VALUES ($cid, $total)";
$r = mysqli_query($dbc, $q);
if (mysqli_affected_rows($dbc) == 1) {

    // Need the order ID:
    $oid = mysqli_insert_id($dbc);

    // Insert the specific order contents into the database...
    // Prepare the query:
    $q = "INSERT INTO order_contents (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($dbc, $q);
    mysqli_stmt_bind_param($stmt, 'iiid', $oid, $pid, $qty, $price);

    // Execute each query; count the total affected:
    $affected = 0;
    $num_rows = 0;
    $q = "SELECT * FROM products ORDER BY product_name";
    $r = mysqli_query($dbc, $q);
    while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
        $pid = $row['product_id'];
        $qty = (int) $_POST[$row['product_id']];
        $price = $row['price'];
        mysqli_stmt_execute($stmt);
        $affected += mysqli_stmt_affected_rows($stmt);
        $num_rows++;
    }

    // Close this prepared statement:
    mysqli_stmt_close($stmt);

    // Report on the success....
    if ($affected == $num_rows) { // Whohoo!
        // Commit the transaction:
        mysqli_commit($dbc);

        $q = "SELECT * FROM customers WHERE customer_id='" . $_SESSION['customer_id'] . "'";
        $r = mysqli_query($dbc, $q);
        $row = mysqli_fetch_array($r, MYSQLI_ASSOC);

        $q2 = "SELECT * FROM orders WHERE order_id=$oid";
        $r2 = mysqli_query($dbc, $q2);
        $row2 = mysqli_fetch_array($r2, MYSQLI_ASSOC);

        $timestamp = strtotime($row2['order_date']);
        $exDevDate1 = date('l, F j, Y', ($timestamp + (86400 * 7)));
        $exDevDate2 = date('l, F j, Y', ($timestamp + (86400 * 10)));

        $q3 = "SELECT order_contents.product_id, order_contents.quantity, order_contents.price, products.product_name FROM order_contents, products WHERE order_contents.product_id = products.product_id AND order_id=$oid";
        $r3 = mysqli_query($dbc, $q3);

        echo "<div id=\"receipt\">
<h1>Order Confirmation</h1>
<h4>Order #$oid<h4>

<h3>Hello " . $row['first_name'] . " " . $row['last_name'] . ",</h3>
";

        if (!empty($backOrdered)) {
            echo "<p>";
            foreach ($backOrdered as $x => $item) {
                echo "<br>" . $item;
            }
            echo "</p>";
        }

        echo"<br>
<p>Thank you for shopping with us. We'll let you know once your item(s) are shipped. Your estimated delivery date is below. If you would like to view the status of your order or make any changes to it, please visit Your Orders.</p>

<h3>Order Details</h3>
<p>Order #$oid
<br>
Placed on " . date('F j, Y', $timestamp) . "</p>
<br>

<p>Your estimated delivery date is:
<br>
<strong>$exDevDate1 - $exDevDate2</strong></p>
<br>

<p>Your order was sent to:
<br>
<strong>" . $row['first_name'] . " " . $row['last_name'] . "<br>
           " . $row['street'] . "<br>
           " . $row['city'] . ", " . $row['state'] . "  " . $row['zip'] . "</strong></p>
<br>

<p>Your order details are below:</p>
\n";
        if ($r3) {
            while ($row3 = mysqli_fetch_array($r3, MYSQLI_ASSOC)) {
                echo "<p>Product: " . $row3['product_name'] . "<br>
                  Quantity: " . $row3['quantity'] . "<br>
                  Price: $" . number_format($row3['price'], 2) . "<br>
                  Product Subtotal: $" . number_format(($row3['price'] * $row3['quantity']), 2) . "</p>
                  <br>";
            }
        }
        echo"<p>Your order total is:<br>
\$" . number_format($total, 2) . "</p>
<br>
";
        // Message to the customer:
        echo '<footer>Thank you for shopping with us.<br>Casey\'s Candy Store.<footer></div>';


        // Send emails and do whatever else.      ///////////////////////
        $body = "<html><head>"
                . "<style>#receipt {
                    padding:30px;
                    width: 60%;
                    font-size:1.3em;
                    box-shadow: 0 0 5px 2px rgba(0,0,0,.35);
                    text-shadow:
                        -1px -1px 0 #333,
                        1px -1px 0 #333,
                        -1px 1px 0 #333,
                        1px 1px 0 #333;
                    }
                    #receipt h3 {
                        padding-left:0px;
                        margin-left:0px;
                        margin-bottom:5px;
                    }
                    #receipt footer{
                        font-size:1em;
                    }</style></head><body>";
        $body .= "<table>";
        $body .= "<div id=\"receipt\">";
        $body .= "<h1>Order Confirmation</h1>";
        $body .= "<h4>Order #$oid<h4>";

        $body .= "<h3>Hello " . $row['first_name'] . " " . $row['last_name'] . ",</h3>";

        if (!empty($backOrdered)) {
            $body .= "<p>";
            foreach ($backOrdered as $x => $item) {
                $body .= "<br>" . $item;
            }
            $body .= "</p>";
        }

        $body .= "<br>";
        $body .= "<p>Thank you for shopping with us. We'll let you know once your item(s) are shipped. Your estimated delivery date is below. If you would like to view the status of your order or make any changes to it, please visit Your Orders.</p>";

        $body .= "<h3>Order Details</h3>";
        $body .= "<p>Order #$oid";
        $body .= "<br>";
        $body .= "Placed on " . date('F j, Y', $timestamp) . "</p>";
        $body .= "<br>";

        $body .= "<p>Your estimated delivery date is:";
        $body .= "<br>";
        $body .= "<strong>$exDevDate1 - $exDevDate2</strong></p>";
        $body .= "<br>";

        $body .= "<p>Your order was sent to:";
        $body .= "<br>";
        $body .= "<strong>" . $row['first_name'] . " " . $row['last_name'] . "<br>";
        $body .= $row['street'] . "<br>";
        $body .= $row['city'] . ", " . $row['state'] . "  " . $row['zip'] . "</strong></p>";
        $body .= "<br>";

        $body .= "<p>Your order details are below:</p>";
        $body .= "\n";
        if ($r3) {
            while ($row3 = mysqli_fetch_array($r3, MYSQLI_ASSOC)) {
                $body .= "<p>Product: " . $row3['product_name'] . "<br>";
                $body .= "Quantity: " . $row3['quantity'] . "<br>";
                $body .= "Price: $" . number_format($row3['price'], 2) . "<br>";
                $body .= "Product Subtotal: $" . number_format(($row3['price'] * $row3['quantity']), 2) . "</p>";
                $body .= "<br>";
            }
        }
        $body .= "<p>Your order total is:<br>";
        $body .= "$" . number_format($total, 2) . "</p>";
        $body .= "<br>";
        $body .= "<footer>Thank you for shopping with us.<br>Casey's Chocolate Store.<footer></div>";
        $body .= "</table>";
        $body .= "</body></html>";

        // $to = $_POST["email"];

        $subject = "Your recent order receipt from Casey's Candy Store.";

        // $headers = "From: webmaster@caseybennington.com\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
        // mail($to, $subject, $body, $headers);

        $from = new SendGrid\Email(null, "webmaster@caseybennington.com");
        $to = new SendGrid\Email(null, $_POST["email"]);
        $content = new SendGrid\Content("text/plain", $body);
        $mail = new SendGrid\Mail($from, $subject, $to, $content, $headers);

        $apiKey = getenv('SENDGRID_API_KEY');
        $sg = new \SendGrid($apiKey);

        $response = $sg->client->mail()->send()->post($mail);

    } else { // Rollback and report the problem.
        mysqli_rollback($dbc);
        echo '<p>Your order could not be processed due to a system error. You will be contacted in order to have the problem fixed. We apologize for the inconvenience.</p>';
        // Send the order information to the administrator.
    }
} else { // Rollback and report the problem.
    mysqli_rollback($dbc);
    echo '<p>Your order could not be processed due to a system error. You will be contacted in order to have the problem fixed. We apologize for the inconvenience.</p>';

    // Send the order information to the administrator.
}

    mysqli_close($dbc);
    include ('includes/footer.html');
?>
