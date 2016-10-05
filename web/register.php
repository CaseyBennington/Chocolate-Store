<?php
# register.php
// This is the registration page for the site.
require ('includes/config.inc.php');
$page_title = 'Register';
include ('includes/header.html');

if ($_SERVER['REQUEST_METHOD'] == 'POST') { // Handle the form.
    // Need the database connection:
    require (MYSQL);

    // Trim all the incoming data:
    $trimmed = array_map('trim', $_POST);

    // Assume invalid values:
    $fn = $ln = $sa = $c = $st = $z = $e = $p = FALSE;

    // Check for a first name:
    if (preg_match('/^[A-Z \'.-]{1,20}$/i', $trimmed['first_name'])) {
        $fn = mysqli_real_escape_string($dbc, $trimmed['first_name']);
    } else {
        echo '<p class="error">Please enter your first name!</p>';
    }

    // Check for a last name:
    if (preg_match('/^[A-Z \'.-]{1,40}$/i', $trimmed['last_name'])) {
        $ln = mysqli_real_escape_string($dbc, $trimmed['last_name']);
    } else {
        echo '<p class="error">Please enter your last name!</p>';
    }

    // Check for an email address:
    if (filter_var($trimmed['email'], FILTER_VALIDATE_EMAIL)) {
        $e = mysqli_real_escape_string($dbc, $trimmed['email']);
    } else {
        echo '<p class="error">Please enter a valid email address!</p>';
    }

    // Check for a password and match against the confirmed password:
    if (preg_match('/^\w{6,}$/', $trimmed['password1'])) {
        if ($trimmed['password1'] == $trimmed['password2']) {
            $p = mysqli_real_escape_string($dbc, $trimmed['password1']);
        } else {
            echo '<p class="error">Your password did not match the confirmed password!</p>';
        }
    } else {
        echo '<p class="error">Please enter a valid password!</p>';
    }

    if ($fn && $ln && $e && $p) { // If everything's OK...
        // Make sure the email address is available:
        $q = "SELECT customer_id FROM customers WHERE email='$e'";
        $r = mysqli_query($dbc, $q) or trigger_error("Query: $q\n<br />MySQL Error: " . mysqli_error($dbc));

        if (mysqli_num_rows($r) == 0) { // Available.
            // Create the activation code:
            $a = md5(uniqid(rand(), true));

            // Add the customer to the database:
            $q = "INSERT INTO customers (email, pass, first_name, last_name, active, registration_date) VALUES ('$e', SHA1('$p'), '$fn', '$ln', '$a', NOW() )";
            $r = mysqli_query($dbc, $q) or trigger_error("Query: $q\n<br />MySQL Error: " . mysqli_error($dbc));

            if (mysqli_affected_rows($dbc) == 1) { // If it ran OK.
                // Send the email:
                $body = "Thank you for registering at Casey's Chocolate Store. To activate your account, please click on this link:\n\n";
                $body .= BASE_URL . 'activate.php?x=' . urlencode($e) . "&y=$a";

                // $to = $trimmed['email'];
                $subject = "Registration Confirmation";

                // $headers = "From: webmaster@caseybennignton.com\r\n";
                // $headers .= "MIME-Version: 1.0\r\n";
                // $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
                // mail($to, $subject, $body, $headers);

                $from = new SendGrid\Email(null, "webmaster@caseybennignton.com");
                // $subject = "Hello World from the SendGrid PHP Library!";
                $to = new SendGrid\Email(null, $trimmed['email']);
                $content = new SendGrid\Content("text/plain", $body);
                $mail = new SendGrid\Mail($from, $subject, $to, $content);

                $apiKey = getenv('SENDGRID_API_KEY');
                $sg = new \SendGrid($apiKey);

                $response = $sg->client->mail()->send()->post($mail);

                // Finish the page:
                echo '<h1>Thank you for registering! A confirmation email has been sent to your address. Please click on the link in that email in order to activate your account.</h1>';
                include ('includes/footer.html'); // Include the HTML footer.
                exit(); // Stop the page.
            } else { // If it did not run OK.
                echo '<p class="error">You could not be registered due to a system error. We apologize for any inconvenience.</p>';
            }
        } else { // The email address is not available.
            echo '<p class="error">That email address has already been registered. If you have forgotten your password, use the link at right to have your password sent to you.</p>';
        }
    } else { // If one of the data tests failed.
        echo '<p class="error">Please try again.</p>';
    }

    mysqli_close($dbc);
} // End of the main Submit conditional.
?>

<h1>Register</h1>
<form class="forms" id="register_form" action="register.php" method="post">
    <fieldset>
        <p><label for="first_name">First Name:</label><input class="required" pattern="\w+" required placeholder="Joe" title="Please enter your first name." type="text" name="first_name" id="first_name" size="20" maxlength="20" value="<?php if (isset($trimmed['first_name'])) echo $trimmed['first_name']; ?>" /></p>
        <p><label for="last_name">Last Name:</label><input class="required" pattern="\w+" required placeholder="Smith" title="Please enter your last name." type="text" name="last_name" id="last_name" size="20" maxlength="40" value="<?php if (isset($trimmed['last_name'])) echo $trimmed['last_name']; ?>" /></p>
        <p><label for="email">Email Address:</label><input class="required email" required placeholder="joesmith@gmail.com" title="Please enter a valid email address." type="email" name="email" id="email" size="30" maxlength="60" value="<?php if (isset($trimmed['email'])) echo $trimmed['email']; ?>" /> </p>
        <p><label for="password1">Password:</label><input class="required" required pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,}" title="Please enter a valid password." type="password" name="password1" id="password1" size="20" maxlength="20" value="<?php if (isset($trimmed['password1'])) echo $trimmed['password1']; ?>" /></p><small>Use only letters, numbers, and the underscore. Must be at least 6 characters long.</small>
        <p><label for="password2">Confirm Password:</label><input class="required" required pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,}" title="Please enter your valid password again." type="password" name="password2" id="password2" size="20" maxlength="20" value="<?php if (isset($trimmed['password2'])) echo $trimmed['password2']; ?>" /></p>
    </fieldset>
    <div align="center"><input type="submit" name="submit" value="Register" /></div>
</form>
<?php include ('includes/footer.html'); ?>
