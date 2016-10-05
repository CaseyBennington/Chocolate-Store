<?php
// Number of records to show per page:
$display = 10;
// Determine where in the database to start returning results...
if (isset($_GET['s']) && is_numeric($_GET['s'])) {
    $start = $_GET['s'];
} else {
    $start = 0;
}
// Determine how many pages there are...
if (isset($_GET['p']) && is_numeric($_GET['p'])) { // Already been determined.
    $pages = $_GET['p'];
} else { // Need to determine.
    // Count the number of records:
    if ($page_title == 'Product Administration') {
        $q = "SELECT COUNT(product_id) FROM products";
        $r = @mysqli_query($dbc, $q);
        $row = @mysqli_fetch_array($r, MYSQLI_NUM);
        $records = $row[0];
        // Calculate the number of pages...
        if ($records > $display) { // More than 1 page.
            $pages = ceil($records / $display);
        } else {
            $pages = 1;
        }
    } else if ($page_title == 'Orders Administration') {
        $q = "SELECT COUNT(order_id) FROM orders";
        $r = @mysqli_query($dbc, $q);
        $row = @mysqli_fetch_array($r, MYSQLI_NUM);
        $records = $row[0];
        // Calculate the number of pages...
        if ($records > $display) { // More than 1 page.
            $pages = ceil($records / $display);
        } else {
            $pages = 1;
        }
    } else {
        $q = "SELECT COUNT(customer_id) FROM customers";
        $r = @mysqli_query($dbc, $q);
        $row = @mysqli_fetch_array($r, MYSQLI_NUM);
        $records = $row[0];
        // Calculate the number of pages...
        if ($records > $display) { // More than 1 page.
            $pages = ceil($records / $display);
        } else {
            $pages = 1;
        }
    }
} // End of p IF.

if ($page_title == 'Product Administration') {
    // Make the query:
    $q = "SELECT product_name, size, price, quantity, image_name, product_id FROM products ORDER BY product_id LIMIT $start, $display";
    $r = @mysqli_query($dbc, $q); // Run the query.
} else if ($page_title == 'Orders Administration') {
    // Make the query:
    $q = "SELECT order_id, customer_id, total, order_date FROM orders ORDER BY $order_by LIMIT $start, $display";
    $r = @mysqli_query($dbc, $q); // Run the query. 
} else {
    // Make the query:
    $q = "SELECT customer_id, last_name, first_name, street, city, state, zip, email, customer_level, active, registration_date FROM customers ORDER BY $order_by LIMIT $start, $display";
    $r = @mysqli_query($dbc, $q); // Run the query. 
}

/* DEBUG TODO: What do I do here if mysqli_query() totally fails?
 * If I don't check for r==0 then I get a message.  If I do leave it in I get
 * a notice that r can't be converted to an int.
 */
if (mysqli_affected_rows($dbc) == 0) {
    echo '<p class="error">Sorry, we could not find any entries.</p>';
} else if (mysqli_num_rows($r) > 0) {
    if ($page_title == 'Administration') {
        // Table header:
        if ($page_title == ('Sort the Current Customers') || ($page_title == 'Search for Current Customers')) {
            echo '<table class="admin" cellspacing="0" cellpadding="5" width="75%">    
	          <tr>
	          <th><a href="view_userssort.php?sort=ln">Last Name</a></th>
	          <th><a href="view_userssort.php?sort=fn">First Name</a></th>
	          <th align="left">Address</th>
	          <th><a href="view_userssort.php?sort=ct">City</a></th>
	          <th><a href="view_userssort.php?sort=st">State</a></th>
	          <th><a href="view_userssort.php?sort=zp">Zip</a></th>
	          </tr>';
        } else {
            echo '<table class="admin" cellspacing="0" cellpadding="5" width="75%"><tr>';
            if ($page_title == 'Edit/Delete Current Customers') {
                echo '<th>Edit</th>
		      <th>Delete</th>';
            }
            echo '<th>Last Name</th>
	          <th>First Name</th>
	          <th>Address</th>
	          <th>City</th>
	          <th>State</th>
	          <th>Zip</th>
                  <th>Email</th>
                  <th>Access Level</th>
                  <th>Activated User</th>
                  <th>Registration Date</th>
	          </tr>';
        } // end of admin table headers
    } else if ($page_title == 'Product Administration') {
        // Table header:
        if ($page_title == ('Sort the Current Products') || ($page_title == 'Search for Current Products')) {
            echo '<table class="admin" cellspacing="0" cellpadding="5" width="75%">    
	          <tr>
	          <th><a href="view_productssort.php?sort=ln">Last Name</a></th>
	          <th><a href="view_productssort.php?sort=fn">First Name</a></th>
	          <th><a href="view_productssort.php?sort=ct">City</a></th>
	          <th><a href="view_productssort.php?sort=st">State</a></th>
	          <th><a href="view_productssort.php?sort=zp">Zip</a></th>
	          </tr>';
        } else {
            echo '<table class="admin" cellspacing="0" cellpadding="5" width="75%"><tr>';
            echo '<th>Product ID</th>
                  <th>Product Name</th>
	          <th>Size</th>
	          <th>Price</th>
	          <th>Quantity</th>
	          <th>Picture</th>
	          </tr>';
        }
    } // end of product table headers
    else {
        // Table header:
        if ($page_title == ('Sort the Current Orders') || ($page_title == 'Search for Current Orders')) {
            echo '<table class="admin" cellspacing="0" cellpadding="5" width="75%">    
	          <tr>
	          <th><a href="view_orderssort.php?sort=ln">Last Name</a></th>
	          <th><a href="view_orderssort.php?sort=fn">First Name</a></th>
	          <th><a href="view_orderssort.php?sort=ct">City</a></th>
	          <th><a href="view_orderssort.php?sort=st">State</a></th>
	          <th><a href="view_orderssort.php?sort=zp">Zip</a></th>
	          </tr>';
        } else {
            echo '<table class="admin" cellspacing="0" cellpadding="5" width="75%"><tr>';
            echo '<th>Order Number</th>
	          <th>Customer Number</th>
	          <th>Total</th>
	          <th>Order Date</th>
	          </tr>';
        }
    } // end of order table headers
    // Fetch and print all the records....
    if ($page_title == 'Administration') {
        while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
            echo '<tr>';
            if ($page_title == 'Edit/Delete Current Customers') {
                echo '<td align="left"><a href="edit_customer.php?id=' . $row['customer_id'] . '">Edit</a></td>
		      <td align="left"><a href="delete_customer.php?id=' . $row['customer_id'] . '">Delete</a></td>';
            }
            echo '<td align="left">' . $row['last_name'] . '</td>';
            echo '<td align="left">' . $row['first_name'] . '</td>
	          <td align="left">' . $row['street'] . '</td>
	          <td align="left">' . $row['city'] . '</td>
	          <td align="left">' . $row['state'] . '</td>
	          <td align="left">' . $row['zip'] . '</td>
                  <td align="left">' . $row['email'] . '</td>
                  <td align="left">' . $row['customer_level'] . '</td>
                  <td align="left">' . $row['active'] . '</td>
                  <td align="left">' . $row['registration_date'] . '</td>
	          </tr>';
        } // End of WHILE loop. 
    } else if ($page_title == 'Product Administration') {
        while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
            echo '<tr>';
            echo '<td align="left"><a href="edit_products.php?id=' . $row['product_id'] . '">' . $row['product_id'] . '</a></td>';
            echo '<td align="left">' . $row['product_name'] . '</td>
                  <td align="left">' . $row['size'] . '</td>
	          <td align="left">$' . $row['price'] . '</td>
	          <td align="left">' . $row['quantity'] . '</td>';
            if ($image = @getimagesize("../images/" . $row['product_id'])) {
                echo "<td align=\"left\"><img src=\"..\show_image.php?image=" . $row['product_id'] . "&name=" . urlencode($row['image_name']) . "\" width=\"95\" height=\"95\" alt=\"{$row['product_name']}\" /></td>";
            } else {
                echo "<td>No image available.</td>";
            }
            echo '</tr>';
        } // End of WHILE loop.
    } else {
        while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
            echo '<tr>';
            echo '<td align="left">' . $row['order_id'] . '</td>';
            echo '<td align="left">' . $row['customer_id'] . '</td>
	          <td align="left">$' . $row['total'] . '</td>
	          <td align="left">' . $row['order_date'] . '</td>
	          </tr>';
        } // End of WHILE loop.
    }
    echo '</table>';
    mysqli_free_result($r);
    mysqli_close($dbc);

    // Make the links to other pages, if necessary.
    if ($pages > 1) {
        echo '<br /><p class ="pagination">';
        $current_page = ($start / $display) + 1;

        // If it's not the first page, make a Previous button:
        if ($current_page != 1) {
            echo '<a href="' . $myscript . '?s=' . ($start - $display) . '&p=' . $pages . '">Previous</a> ';
        }

        // Make all the numbered pages:
        for ($i = 1; $i <= $pages; $i++) {
            if ($i != $current_page) {
                echo '<a href="' . $myscript . '?s=' . (($display * ($i - 1))) . '&p=' . $pages . '">' . $i . '</a> ';
            } else {
                echo $i . ' ';
            }
        } // End of FOR loop.
        // If it's not the last page, make a Next button:
        if ($current_page != $pages) {
            echo '<a href="' . $myscript . '?s=' . ($start + $display) . '&p=' . $pages . '">Next</a>';
        }
        echo '</p>'; // Close the paragraph.
    } // End of links section.
} else {  // End if r>0, no records returned.
    echo '<p class="error">There are currently no entries.</p>';
}
?>