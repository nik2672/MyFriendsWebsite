
<?php
// being session using credentials in settings
session_start();
include 'settings.php';
//create empty string for error message variables to be used later 
$errorEmail = $errorPassword = '';
$enterEmail = '';
//submit email, pass word variables via POST method
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $enterEmail = $_POST['email'];

    $enterPassword = $_POST['password'];
    
    //check if form format is valid, set to true until updated false e.g (email not correct format/email not found in databse)

    $isValid = true;

    //if statement, inbuilt function !filter_var takes email entered comparing it to inbuilt constant ('FILTER_VALIDATE_EMAIL'), return false if not valid.
    if (!filter_var($enterEmail, FILTER_VALIDATE_EMAIL)) {
        $errorEmail = "Invalid email format.";
        $isValid = false;
    } else {
        //query databse to check if $enterEmail using '?' as palcholder exists in db
        $emailCheckQuery = "SELECT * FROM friends WHERE friend_email = ?";
        //binded the entered email ($emailEmail) as a string ('s') to query statement
        $stmt = $conn->prepare($emailCheckQuery);
        $stmt->bind_param("s", $enterEmail);
        $stmt->execute();
        $result = $stmt->get_result();
        //store the submitted statement into $result var, if no rows found in 'freinds table' print error message that email isnt found setting $isValid false
            if ($result->num_rows == 0) {
            $errorEmail = "The email isnt found in our database.";
            $isValid = false;
        } else {
            // if email found check password, inbuilt function 'fetch_assoc()' extracts/converts the row info to arrary from db storing into $user, collumn such as password ($user('password')) can be checked.
            $user = $result->fetch_assoc();
            if ($enterPassword !== $user['password']) {
                $errorPassword = "Wrong password.";
                $isValid = false;
            }


        }
        $stmt->close();


    }

    // if $isValid true (meaning correct email/pass) redirect to friendlist.php, user email and user profile accessed via $user is stored in session for use in 'friendlist' page
    if ($isValid) {
        $_SESSION['user_email'] = $enterEmail;
        $_SESSION['user_profile'] = $user['profile_name'];
        header('Location: friendlist.php');

        exit();

    }

}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log In - My Friend System</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body>
    <div class="outer-container">  <!-- cotnainer class for css styling -->
        <div class="container mt-5">  <!-- set top margin to five using bootstrap -->
           <h1 class="text-center mb-4">  <!-- center and set margin bottom to four-->
            <span class="material-icons" style="font-size: 50px; color: green;">login</span> My Friend System Log in Page
        </h1>

        <!-- add shadow and padding usign bootstrap classes to form for modern look -->
        <!-- form submitted via post method-->
            <form action="login.php" method="POST" class="shadow p-4 rounded bg-white">
                 <div class="mb-3"> <!--margin bottom size 3 -->
                    <label for="email" class="form-label">Email:</label> <!-- 'form-label class for bootstrap styling' -->
                    <input type="email" name="email" value="<?php echo htmlspecialchars($enterEmail); ?>" class="form-control" required><!--'htmlsepcialchars' used to remove special characters for good practice -->
                    <!-- display error message if email doesnt exist db or incorrect-->
                    <div class="text-danger"><?php echo $errorEmail; ?></div>
                </div>

                <div class="mb-3"> <!-- set bottom margin value to 3 -->
                       <label for="password" class="form-label">Password:</label>
                    <!-- class set to form-control for bootstrap styling -->
                    <input type="password" name="password" class="form-control" required>
                    <!-- show error message if password doesnt exist in db-->
                    <div class="text-danger"><?php echo $errorPassword; ?></div>
                </div>
<!-- 'd-flex' and 'justify-content-between helps set login, clear buttons opposite ends-->
                <div class="d-flex justify-content-between">
                    <button type="submit" class="btn btn-success">Log In</button>
                    <button type="reset" class="btn btn-secondary">Clear</button>
                </div>
            </form>
<!-- button to index page set, class 'btn btn-link' quickly styles button via boostrap-->
            <div class="text-center mt-3">
                <a href="index.php" class="btn btn-link">Home</a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-NkdVkyYjIqqISuOr4Ry07N5gzw6EmgVpAsERJ7SdrGboKxONlDBKY6+3MnmwGjkv" crossorigin="anonymous"></script>
</body>
</html>