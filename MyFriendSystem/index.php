<?php
// include the settings.php file including my host, user login information, etc.
    include 'settings.php';

// NOTE: 'friends' and 'myfriends' tables were created manually via putty; however, auto table creation is included in the script, and both tables were populated manually
    $tablesCreated = false;
$errorMSG = ""; // we initialize an empty variable $errorMSG

// create the 'friends' table IF it doesn't exist
$createFriendsTable = "CREATE TABLE IF NOT EXISTS friends (
    friend_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    friend_email VARCHAR(50) NOT NULL,
    password VARCHAR(20) NOT NULL,
    profile_name VARCHAR(30) NOT NULL,
    date_started DATE NOT NULL,
    num_of_friends INT UNSIGNED
)";

// create the 'myfriends' table IF it doesn't exist in the backend
$createMyFriendsTable = "CREATE TABLE IF NOT EXISTS myfriends (
    friend_id1 INT NOT NULL,
    friend_id2 INT NOT NULL,
    PRIMARY KEY (friend_id1, friend_id2)
)";

// queries and check for errors; if there's an issue connecting to the db, then an $errorMSG is printed
    if ($conn->query($createFriendsTable) === TRUE && $conn->query($createMyFriendsTable) === TRUE) {
    $tablesCreated = true;
    } else {
    $errorMSG = "Problem creating tables: " . $conn->error;
}

    $conn->close();
?>

<!DOCTYPE html>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- make the page responsive to different device widths -->
    <title>My Friends System index page</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons"><!-- imported google fonts for icosn for the 'login, about and signup' buttons-->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

</head>

<body>
    <div class="container"> <!-- container class created to help styling -->

        <header>
            <h1>Welcome to My Friends System</h1>
        </header>

          <p><strong>Name:</strong> </p>
         <p><strong>Student ID:</strong> </p>
         <p><strong>Email:</strong> </p>

        <p>

        </p>

        <!-- display success message only 'if' tables exist -->
        <?php if ($tablesCreated): ?>
            <p>SUCCESS MESSAGE: [Tables 'friends' and 'myfriends' exist and are populated]</p>
        <?php else: ?>
            <p><?php echo $errorMSG; ?></p>
        <?php endif; ?>

        <div class="link-container">
               <a href="signup.php">
                <span class="material-icons" >person_add</span> Sign-Up <!-- icosn added for each button using google fontts-->
            </a>
                <a href="login.php">
                <span class="material-icons">login</span> Log-In
            </a>
            <a href="about.php">
                <span class="material-icons">info</span> About
            </a>
        </div>

        <footer>
            <p>&copy; 2024 My Friends System Copywright (s104549772)</p> <!-- &copy enables copywright icon sign to appear-->
        </footer>
    </div>
</body>
</html>
