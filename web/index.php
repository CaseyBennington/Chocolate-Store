<?php
# index.php
// This is the main page for the site.
// Include the configuration file:
require ('includes/config.inc.php');

// Set the page title and include the HTML header:
$page_title = 'Welcome to Casey\'s Chocolate!';
include ('includes/header.html');
// load any page, check if logged in. if logged in, redirect to index. if not direct to login.
if (!isset($_SESSION['first_name'])) {
    $url = BASE_URL . 'login.php'; // Define the URL.
    ob_end_clean(); // Delete the buffer.
    header("Location: $url");
}

// Welcome the cutstomer (by name if they are logged in):
echo '<h1>Welcome';
if (isset($_SESSION['first_name'])) {
    echo ", {$_SESSION['first_name']}";
}
echo '!</h1>';
require (MYSQL);
?>
<form class="forms" id="order_form" action = "checkout.php" method = "post">
    <h3>Casey's Chocolate Sales </h3>

    <!-- A bordered table for item orders -->
    <table border = "border">
        <!-- First, the column headings --> 
        <tr>
            <th>Product Name</th>
            <th>Product Picture</th>
            <th>Price</th>
            <th>Quantity</th>
            <th>Order</th>
        </tr>
        <!-- Now, the table data entries -->
        <?php
        $q = "SELECT * FROM products ORDER BY product_name";

        $r = mysqli_query($dbc, $q);
        while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
            echo "<tr>
            <th>{$row['product_name']} {$row['size']}</th>\n";

            if ($image = @getimagesize("images/" . $row['product_id'])) {
                echo "<td><img src=\"show_image.php?image=" . $row['product_id'] . "&name=" . urlencode($row['image_name']) . "\" width=\"95\" height=\"95\" alt=\"{$row['product_name']}\" /></td>";
            } else {
                echo "<td>No image available.</td>";
            }

            echo "<td>\${$row['price']}</td>
            <td>{$row['quantity']}</td>
            <td><input class=\"required digits order_nums\" required placeholder=\"1\" min=\"0\" max=\"999999\" pattern=\"^[0-9]*[0-9][0-9]*$\" title=\"Please enter a positive whole number.\" type = 'number'  name = {$row['product_id']} id={$row['product_id']} size ='6' /></td>
            </tr>\n";
        } // End of while loop.
        ?>
        <th>Total</th>
        <td></td>
        <td></td>
        <td></td>
        <td id="total"></td>
    </table>

    <?php
    //<!-- Select that customers information
    if (isset($_SESSION['customer_id'])) {
        $q = "SELECT * FROM customers WHERE customer_id='" . $_SESSION['customer_id'] . "'";
        $r = mysqli_query($dbc, $q);
        $row = mysqli_fetch_array($r, MYSQLI_ASSOC);
    }
    ?>
    <!-- A borderless table of text widgets for name and address -->
    <fieldset>
        <span>Customer Information:</span>
        <div>
            <label for="fName">First Name:</label>
            <input class="required" required placeholder="Joe" pattern="\w+" title="Please enter your first name." type = "text"  name = "fName" id="fName" size = "30" value ="<?php if (isset($row['first_name'])) echo $row['first_name']; ?>" />
        </div>
        <div>
            <label for="lName">Last Name:</label>  
            <input class="required" required placeholder="Smith" pattern="\w+" title="Please enter your last name." type = "text"  name = "lName" id="lName" size = "30" value ="<?php if (isset($row['last_name'])) echo $row['last_name']; ?>" />
        </div>
        <div>
            <label for="street">Street Address:</label>
            <input class="required" required placeholder="123 Anywhere Road" title="Please enter your street address." type = "text"  name = "street" id="street" size = "30" value ="<?php if (isset($row['street'])) echo $row['street']; ?>" />
        </div>
        <div>
            <label for="city">City:</label>
            <input class="required" required placeholder="San Diego" pattern="\w+" title="Please enter your city." type = "text"  name = "city" id="city" size = "30" value ="<?php if (isset($row['city'])) echo $row['city']; ?>" />
        </div>
        <div>
            <label for="state">State (2 letter):</label>
            <input class="required" required placeholder="CA" pattern="^(?i:A[LKSZRAEP]|C[AOT]|D[EC]|F[LM]|G[AU]|HI|I[ADLN]|K[SY]|LA|M[ADEHINOPST]|N[CDEHJMVY]|O[HKR]|P[ARW]|RI|S[CD]|T[NX]|UT|V[AIT]|W[AIVY])$" title="Please enter your state." type = "text"  name = "state" id="state" size = "2" value ="<?php if (isset($row['state'])) echo $row['state']; ?>" />
        </div>
        <div>
            <label for="zip">Zip:</label>
            <input class="required" required placeholder="92109" pattern="^\d{5}([\-]?\d{4})?$" title="Please enter your zip name." type = "text"  name = "zip" id="zip" size = "5" value ="<?php if (isset($row['zip'])) echo $row['zip']; ?>" />
        </div>
        <div>
            <label for="email">Email address:</label>
            <input class="required email" required placeholder="Enter a valid email address" title="Please enter your email address." type = "email"  name = "email" id="email" size = "30" value ="<?php if (isset($row['email'])) echo $row['email']; ?>" />
        </div>
        <!--  <div>
              <label for="pass1">Password:</label>
              <input type = "password" required placeholder="Enter a valid password" name = "pass1" id="pass1" size = "12" value ="" />
          </div>
          <div>
              <label for="pass2">Password (enter again):</label></td>
              <input type = "password" required name = "pass2" id="pass2" size = "12" value ="" />
          </div>
        -->
    </fieldset>

    <!-- The radio buttons for the payment method -->
    <h3>Payment Method:</h3>
    <div>
        <input class="required" required title="Please select your payment type." type = "radio" name = "payment" id="payment" value = "visa" checked = "checked" />Visa 
        <input type = "radio" name = "payment" id="payment" value = "mc" />Master Card 
        <input type = "radio" name = "payment" id="payment" value = "discover" />Discover
    </div>
    <label for="ccDate">Date:(mm/dd/yyyy)</label><input class="required date" required pattern="\d{1,2}/\d{1,2}/\d{4}" title="Please enter an expiration date." type ="date" name="ccDate" id ="ccDate" value =""/><br>
    <label for="ccNum">Card Number:</label><input class="required creditcard" required title="Please enter a valid credit card number." type = "text"  name = "ccNum" id="ccNum" value = "" /><br/>
    <p id="ccExample">(eg. 4111-1111-1111-1111)</p>
    <!-- The submit and reset buttons -->
    <p>
        <input type = "submit" value = "Submit Order" /> 
        <input type = "reset" value = "Clear Order Form" />
    </p>
</form>
<?php
mysqli_close($dbc);
include ('includes/footer.html');
?>