<?php
//begin the session inclduing credentials form settings for db connection 
session_start();
include 'settings.php';

//create empty strings for error message variables used later
$errorEmail = $errorProfile = $errorPassword = '';
$enterEmail = $inputProfileName = '';
$enterPassword = $enterConfirmPassword = '';

//check if sigup form submitted via POST method, store user 'email', 'profile' etc into variables use later 
if ($_SERVER["REQUEST_METHOD"] == "POST") {
      $enterEmail = $_POST['email'];
    $inputProfileName = $_POST['profileName'];
     $enterPassword = $_POST['password'];
    $enterConfirmPassword = $_POST['confirmPassword'];
    
    //simialr to login.php checks if informaiton entered by user is correct e.g email, password, set true until proven false
    $isValid = true;
    //check if email entered is valid throguh using inbuilt function filter_var() comparing it 'Filter_validate_email' constant used to check proper email format
    if (!filter_var($enterEmail, FILTER_VALIDATE_EMAIL)) {
        $errorEmail = "Invalid email format.";
        $isValid = false; //if not valid $isValid is set to flase
    } else { 
        // if email is valid check if email already exists in db
        //query will check friends table for record mathcing email with '?' placehoolder for entered email
         $emailCheckQuery = "SELECT * FROM friends WHERE friend_email = ?";
        $stmt = $conn->prepare($emailCheckQuery); //used prepare stmt
        $stmt->bind_param("s", $enterEmail); //bind email specified as string 's' to stmt
        $stmt->execute();
        $result = $stmt->get_result(); //result of the query stored into $result variable

        //if there is at least one row in the $result the condition is set to true meaning email already exists.
         if ($result->num_rows > 0) {
            $errorEmail = "Sorry email is already registered in database.";
            $isValid = false; //set to false if email is not correct
        }
         $stmt->close(); //close the $stmt for best practice and prevent errors

    }

    // check if inputted profile name entered is empty, inbuilt function preg_match used to check input against pattern such that only letters allowed, I allowed spaces as it enables users to enter first and last name clearly with spaces.
     if (empty($inputProfileName) || !preg_match("/^[a-zA-Z\s]+$/", $inputProfileName)) {
        $errorProfile = "Profile name must only contain letters and spaces and can't be blank.";
        $isValid = false; //set to false if profile name is not valid
    }

    // check if password inputted is empty, confirm password field is empty and if the password and confirmed password match suign != operator. 
     if (empty($enterPassword) || empty($enterConfirmPassword) || $enterPassword !== $enterConfirmPassword) {
        $errorPassword = "Passwords entered must match and cannot be empty.";
        $isValid = false; //if any condition violated set $isValid to false.
    }

    // insert user entered values into the db. ensures '$isValid = true' first as user inputs will be correct and prevents incorrect values from are inserted into the database, session vairables (emial, profile) are stored before the user is redirected to friendadd.php
     if ($isValid) {
        // current date assigned to $currentDate var using inbuilt date function
        $currentDate = date('Y-m-d');
        //query to insert new record using new user infomation, '?' used as placeholders and num_of_friends is always set to zero as specified by assign doc
         $insertSQL = "INSERT INTO friends (friend_email, password, profile_name, date_started, num_of_friends) 
                      VALUES (?, ?, ?, ?, 0)";
        $stmt = $conn->prepare($insertSQL); 
        
        // store the password to the stmt specified as strings 's' 
        // also removed password hashing as I ran into issues and not required in assignment doc
          $stmt->bind_param("ssss", $enterEmail, $enterPassword, $inputProfileName, $currentDate);
        //once query statement executes session varables are stored and user is redirected to firendadd.php page
        if ($stmt->execute()) { 
            // set the session variables and redirect only if data was inserted into db 
            $_SESSION['user_email'] = $enterEmail;
            $_SESSION['user_profile'] = $inputProfileName;
            header('Location: friendadd.php'); 
             exit();
        } else {
            echo "Could not intert into the databse: " . $stmt->error;//user 'error' property to describe error occured
        }
        $stmt->close();

    }


}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>sign up page</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons"> 
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

</head>

<body>
    <div class="container mt-5">
        <h1 class="text-center mb-4">
            <span class="material-icons" style="font-size: 50px; color: green;">person_add</span> Welcome to My Friends System
        </h1>
        <h2 class="text-center mb-4">Registration Page</h2>

        <!--sign up form for user t enter 'emial', 'profileName', 'pass' and 'confirm pass' form data sent to itself 'signup.php' via post method -->
        <form action="signup.php" method="POST" class="shadow p-4 rounded bg-white"> <!--bootstrap styling adding padding 'p-4' and shadow effect -->
            <div class="mb-3">  <!-- bootstrap margin added to bottom, user email -->
                <label for="email" class="form-label">Email:</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($enterEmail); ?>" class="form-control" required>

                <div class="text-danger"><?php echo $errorEmail; ?></div>
            </div>

              <div class="mb-3"> <!-- user profile name-->
                <label for="profileName" class="form-label">Profile Name:</label>
                <input type="text" name="profileName" value="<?php echo htmlspecialchars($inputProfileName); ?>" class="form-control" required>

                <div class="text-danger"><?php echo $errorProfile; ?></div>
            </div>

            <div class="mb-3"> <!-- user password -->
                <label for="password" class="form-label">Password:</label>
                <input type="password" name="password" class="form-control" required>

                <div class="text-danger"><?php echo $errorPassword; ?></div>
            </div>

               <div class="mb-3"> <!-- user confirm password-->
                <label for="confirmPassword" class="form-label">Confirm Password:</label>
                <input type="password" name="confirmPassword" class="form-control" required>
            </div>

               <div class="d-flex justify-content-between"> <!--'d-flex' and justrify-conent used to make 'register' and 'clear' buttons seperate-->
                <button type="submit" class="btn btn-success">Register</button>
                <button type="reset" class="btn btn-secondary">Clear</button>
            </div>
          </form>

        <div class="text-center mt-3"> <!--cernter the home button also with bootstrap styling 'btn-link' -->
            <a href="index.php" class="btn btn-link">Home</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-NkdVkyYjIqqISuOr4Ry07N5gzw6EmgVpAsERJ7SdrGboKxONlDBKY6+3MnmwGjkv" crossorigin="anonymous"></script>
</body>

</html>