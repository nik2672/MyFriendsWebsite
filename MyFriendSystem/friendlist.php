<?php
// start teh session 
session_start();
include 'settings.php';

// checks fi teh user is logged in by checkign session variable 'user_email' if not logged in redirect to login page
if (!isset($_SESSION['user_email'])) {
    header("Location: login.php");
    exit();
}

// retrieve the logged in users id based on the email stored in session. 
$userEmail = $_SESSION['user_email'];
//query retreives the friend_id from friends tables based on friend_email
$userQuery = "SELECT friend_id FROM friends WHERE friend_email = ?";
$stmt = $conn->prepare($userQuery);//prep stmt
//bind $userEmail value to the '?' holder 's' indicating a string
$stmt->bind_param("s", $userEmail);
$stmt->execute();
//store the result of query to $result variable
$result = $stmt->get_result();
//store single row from result to $userData as associative array usign fetch_assoc() funciton 
$userData = $result->fetch_assoc();
$userId = $userData['friend_id']; //extracts friend id from $userData assigning it to $userID 
$stmt->close();

// unfriend button feature
// checks if the unfriend button has been pressed submitted using POST method
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['unfriend'])) {
    //retrieve friend id that must be unfriended from the form input
    $friendId = $_POST['friend_id'];

    // query wil remove friendship between $userid and specified friend_id, it checks the row where friend_id1 and friend_id2 are friends deleting the row if true.
    $unfriendQuery = "DELETE FROM myfriends WHERE (friend_id1 = ? AND friend_id2 = ?) OR (friend_id1 = ? AND friend_id2 = ?)";
    $stmt = $conn->prepare($unfriendQuery);
    //userId and friendID binded TWICE to ensure both possible friendhsip connections checked and deleted if exist for isntace delete either (user1 is friends with user 2) and (user 2  is friends with user 1) 'iiii' indicates all four are integers
    $stmt->bind_param("iiii", $userId, $friendId, $friendId, $userId);

    // unfriend query executed and checked if sucessful using if , friendship will be deleted
    if ($stmt->execute()) {
        // after unfreinding the number of friends for both users is updated by decreasing the count of each user by 1 using query
        $updateNumFriends = "UPDATE friends SET num_of_friends = num_of_friends - 1 WHERE friend_id IN (?, ?)";
        $stmt = $conn->prepare($updateNumFriends);
        $stmt->bind_param("ii", $userId, $friendId); //userId and FriendId allows query to update teh friend count for both users at the same time
        $stmt->execute();
    } else {
        echo "query could not be executed: " . $conn->error;
    }

    $stmt->close();

    // was having isseus with the form resubmitting so user will renavigate to friendlist.php helped prevent this issue
    header("Location: friendlist.php");
    exit();
}

// retrieve the current list of user's friends, query selects teh friend_id and prfoile_name of teh friends from friends table, 'f' for freinds table. 
// the frends table and my friends table is joined baed on teh condition friend_id in freinds table matches friend_id2 in myfriends table hence f.friend_id = mf.friend_id2
// the WHERE query part will filter the rows to return only the friends of loggedin user from myfriends table
$friendsQuery = "SELECT f.friend_id, f.profile_name FROM friends f JOIN myfriends mf ON f.friend_id = mf.friend_id2 WHERE mf.friend_id1 = ?";
$stmt = $conn->prepare($friendsQuery);
$stmt->bind_param("i", $userId);
$stmt->execute();
$friendsResult = $stmt->get_result(); //query result stored in $friendResult

// counts number of friends in the result ($friendsResult) through counting number of rows (num_rows)  
$totalFriends = $friendsResult->num_rows;

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Friend List in the My Friend System</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body>
    <div class="outer-container"> <!--outer container class -->
        <div class="container mt-5">  <!-- contianer class with margin 5 on top-->
            <h1 class="text-center mb-4">My Friend System</h1>  <!--text centered and margin added to the bottom  -->
            <h2 class="text-center mb-4"><?php echo $userEmail; ?>'s Friend List Page</h2>
            <p class="text-center">Total number of friends is <?php echo $totalFriends; ?></p> <!--total number of friends printed using $totalFriends variable where counted rows were stored -->

            <table class="table"> <!-- bootstrap class 'table' provides stlying-->
                <thead>

                    <tr>
                        <th>Profile Name</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody><!--display the list of freinds providing an option to unfriend each one, the while loop will generate one table row <tr> for each friend placing their profile_name -->

                    <?php while ($friend = $friendsResult->fetch_assoc()): ?>
                    <tr> <!-- specialchars added for sake of security for isntance special characters like '>' cant be used-->
                        <td><?php echo htmlspecialchars($friend['profile_name']); ?></td>
                        <td> <!--form usese post method and when teh form is submitted it sends teh from data back to same page friendlist.php for processing such as removing a friend from friends list.-->
                            <form method="POST" action="friendlist.php">
                                <input type="hidden" name="friend_id" value="<?php echo $friend['friend_id']; ?>">
                                <button type="submit" name="unfriend" class="btn btn-danger">Unfriend</button> <!-- unfriend button, bootstrap styling used to make teh button red 'btn-danger' -->
                            </form>

                        </td>
                    </tr>

                    <?php endwhile; ?> <!-- end the while loop iterating throguh the friends-->
                </tbody>
            </table>


            <div class="text-center mt-3"> <!-- 'add friends' 'logout' buttons created using and styled using bootstrap, buttons are centered and margit added to the top-->
                 <a href="friendadd.php" class="btn btn-link">Add Friends</a>
                <a href="logout.php" class="btn btn-link">Log out</a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-NkdVkyYjIqqISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous"></script>
</body>
</html>

<?php
// Close the connection
$conn->close();
?>
