<?php
# forgot_password.php
// This page allows a customer to reset their password, if forgotten.
require ('includes/config.inc.php');
$page_title = 'Forgot Your Password';
include ('includes/header.html');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require (MYSQL);

    // Assume nothing:
    $uid = FALSE;

    // Validate the email address...
    if (!empty($_POST['email'])) {

        // Check for the existence of that email address...
        $q = 'SELECT customer_id FROM customers WHERE email="' . mysqli_real_escape_string($dbc, $_POST['email']) . '"';
        $r = mysqli_query($dbc, $q) or trigger_error("Query: $q\n<br />MySQL Error: " . mysqli_error($dbc));

        if (mysqli_num_rows($r) == 1) { // Retrieve the customer ID:
            list($uid) = mysqli_fetch_array($r, MYSQLI_NUM);
        } else { // No database match made.
            echo '<p class="error">The submitted email address does not match those on file!</p>';
        }
    } else { // No email!
        echo '<p class="error">You forgot to enter your email address!</p>';
    } // End of empty($_POST['email']) IF.

    if ($uid) { // If everything's OK.
        // Create a new, random password:

        function RandomString($length) {
            $original_string = array_merge(range(0, 9), range('a', 'z'), range('A', 'Z'));
            $original_string = implode("", $original_string);
            return substr(str_shuffle($original_string), 0, $length);
        }

        $p = RandomString(20);
        //$time = time();
        //$chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvqxyz01234567890";
        //$chars = str_shuffle($chars);
        //$p = substr(md5(uniqid(rand(), true)), 6, 20);
        //$p = substr(md5(RandomString(20)),6,20);

        // Update the database:
        $q = "UPDATE customers SET pass=SHA1('$p') WHERE customer_id=$uid LIMIT 1";
        $r = mysqli_query($dbc, $q) or trigger_error("Query: $q\n<br />MySQL Error: " . mysqli_error($dbc));

        if (mysqli_affected_rows($dbc) == 1) { // If it ran OK.
            // Send an email:
            $body = "Your password to log into Casey's Chocolate Store has been temporarily changed to '$p'. Please log in using this password and this email address. Then you may change your password to something more familiar.";
            $from = new SendGrid\Email(null, "admin@caseybennignton.com");
            $to = new SendGrid\Email(null, $_POST["email"]);
            $subject = "Your temporary password.";
            $content = new SendGrid\Content("text/plain", $body);
            $mail = new SendGrid\Mail($from, $subject, $to, $content);

            $apiKey = getenv('SENDGRID_API_KEY');
            $sg = new \SendGrid($apiKey);

            $response = $sg->client->mail()->send()->post($mail);
            // mail($_POST['email'], 'Your temporary password.', $body, 'From: admin@caseybennington.com');

            // Print a message and wrap up:
            echo '<h1>Your password has been changed. You will receive the new, temporary password at the email address with which you registered. Once you have logged in with this password, you may change it by clicking on the "Change Password" link.</h1>';
            mysqli_close($dbc);
            include ('includes/footer.html');
            exit(); // Stop the script.
        } else { // If it did not run OK.
            echo '<p class="error">Your password could not be changed due to a system error. We apologize for any inconvenience.</p>';
        }
    } else { // Failed the validation test.
        echo '<p class="error">Please try again.</p>';
    }

    mysqli_close($dbc);
} // End of the main Submit conditional.
?>

<h1>Reset Your Password</h1>
<p>Enter your email address below and your password will be reset.</p>
<form class="forms" id="forgot_pw" action="forgot_password.php" method="post">
    <fieldset>
        <p><label for="email">Email Address:</label><input class="required email" required title="Please enter a valid email address." type="email" name="email" id="email" size="20" maxlength="60" value="<?php if (isset($_POST['email'])) echo $_POST['email']; ?>" /></p>
    </fieldset>
    <div align="center"><input type="submit" name="submit" value="Reset My Password" /></div>
</form>

<?php include ('includes/footer.html'); ?>
