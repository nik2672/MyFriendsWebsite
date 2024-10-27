<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About page</title>
    <link rel="stylesheet" href="styles.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body>
    <div class="outer-container">
        <div class="container mt-5">
        <h1 class="text-center mb-4">
   <span class="material-icons" style="font-size: 50px; color: green;">info</span> About My Friend System
</h1>

            <!-- about answers  -->
            <h2>What I have done:</h2>
            <ul>
                <li><strong>Tasks not attempted or not completed:</strong> All tasks completed including labs 10 and 11 completed except for labs (7-9).</li>
                <li><strong>Special features:</strong> Pagination for the Add Friend page and Mutual Friend Count feature. Additioanlly responsive design for all size screens implemented with help of bootstrap and media queries. Fade color transition effects for buttons. sql injection protection where I could for instance users cant enter special characters to access my database. </li>
                <li><strong>Challenges:</strong> Initially had issues registering new users into the database when i tried to implement password hashing password_hash() for security however decided to remove the feature as it wasnt a requirement for the assignment, also faced bugs with 'unfriend' feature button.</li>
                <li><strong>Improvements for the next time:</strong> Perhaps keeping functions in seperate php file making it easy to reuse and organise, add more features that will make the site more complete for isntance adding a feature that enables the users to interact with each other for instance 'direct messaging', website logo/favicon, adding more animations</li>
                <li><strong>Additional features:</strong> I implemented pagination and the ability to display mutual friends, which were not initially required but enhance the functionality of the application.</li>
            </ul>

            <!--  link to friendlist , friend add and index pages -->
            <h2>Related Pages:</h2>
            <ul>
                <li><a href="friendlist.php" class="btn btn-link">Friend List</a></li>
                <li><a href="friendadd.php" class="btn btn-link">Add Friends</a></li>
                <li><a href="index.php" class="btn btn-link">Home Page</a></li>
            </ul>

            <!--  list of my four screenshots consisting of discussion answers -->
            <h2>Discussion Board Screenshot:</h2>
            <p>Screenshots of FOUR different board responses attached below:</p>
	           <h5> <strong>Discussion Example One:</strong> </h5>
            <img src="images/ScreenshotOne.png" alt="Discussion Screenshot" class="img-fluid"/>
                <h5><strong> Discussion Example Two:</strong> </h5>
            <img src="images/ScreenshotTwo.png" alt="Discussion Screenshot" class="img-fluid"/>
                <h5> <strong>Discussion Example Three:</strong> </h5>
            <img src="images/ScreenshotThree.png" alt="Discussion Screenshot" class="img-fluid"/>
                <h5><strong> Discussion Example Four: </strong></h5>
            <img src="images/ScreenshotFour.png" alt="Discussion Screenshot" class="img-fluid"/>
            <div class="text-center mt-5">
                <a href="index.php" class="btn btn-success">Home Page</a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-NkdVkyYjIqqISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous"></script>
</body>
</html>
