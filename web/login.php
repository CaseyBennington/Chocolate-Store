<?php
# login.php
// This is the login page for the site.
require ('includes/config.inc.php');
$page_title = 'Login';
include ('includes/header.html');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require (MYSQL);

    // Validate the email address:
    if (!empty($_POST['email'])) {
        $e = mysqli_real_escape_string($dbc, $_POST['email']);
    } else {
        $e = FALSE;
        echo '<p class="error">You forgot to enter your email address!</p>';
    }

    // Validate the password:
    if (!empty($_POST['pass'])) {
        $p = mysqli_real_escape_string($dbc, $_POST['pass']);
    } else {
        $p = FALSE;
        echo '<p class="error">You forgot to enter your password!</p>';
    }

    if ($e && $p) { // If everything's OK.
        // Query the database:
        $q = "SELECT customer_id, first_name, customer_level FROM customers WHERE (email='$e' AND pass=SHA1('$p')) AND active IS NULL";
        $r = mysqli_query($dbc, $q) or trigger_error("Query: $q\n<br />MySQL Error: " . mysqli_error($dbc));

        if (@mysqli_num_rows($r) == 1) { // A match was made.
            // Register the values:
            $_SESSION = mysqli_fetch_array($r, MYSQLI_ASSOC);
            mysqli_free_result($r);
            mysqli_close($dbc);

            // Redirect the customer:
            $url = BASE_URL . 'index.php'; // Define the URL.
            ob_end_clean(); // Delete the buffer.
            header("Location: $url");
            exit(); // Quit the script.
        } else { // No match was made.
            echo '<p class="error">Either the email address and password entered do not match those on file or you have not yet activated your account.</p>';
        }
    } else { // If everything wasn't OK.
        echo '<p class="error">Please try again.</p>';
    }

    mysqli_close($dbc);
} // End of SUBMIT conditional.
?>
<div id="login_page">
    <h1>Login</h1>
    <form class="forms" id="login_form" action="login.php" method="post">
        <p>Your browser must allow cookies in order to log in.</p>
        <p>First time here? Please <a href="register.php" title="Register for the Site">Register</a>.</p>
        <fieldset>
            <p><label for="email">Email Address:</label><input class="required email" required title="Please enter a valid email address." type="email" name="email" id="email" size="20" maxlength="60" /></p>
            <p><label for="pass">Password:</label><input class="required" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,}" title="Please enter a valid password." type="password" required name="pass" id="pass" size="20" maxlength="20" /></p>
        </fieldset>   
        <div align="center"><input type="submit" name="submit" value="Login" /></div>
    </form>
</div>
<?php include ('includes/footer.html'); ?>