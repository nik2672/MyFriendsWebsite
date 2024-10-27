<?php
// start sessionn
    session_start();
include 'settings.php';

// ensure the user is logged in checking session varaible is set 'user_email'
if (!isset($_SESSION['user_email'])) {
    header("Location: login.php"); //if the user is not logged in user sent to login.php
    exit();
}

// store logged in users email and user profile to $userEmail and $profileName email 
$userEmail = $_SESSION['user_email'];
$profileName = $_SESSION['user_profile'];

// get the logged-in user's ID form the 'friends' table via query, '?' is replaced with the $userEmail 
$userQuery = "SELECT friend_id FROM friends WHERE friend_email = ?";
    $stmt = $conn->prepare($userQuery); //stmt prepare
$stmt->bind_param("s", $userEmail); //'s' specifys data type as string
$stmt->execute();//stmt executed
$result = $stmt->get_result(); //result fo the stmr stored in $result
$userData = $result->fetch_assoc(); //gets teh associative row as array 
$userId = $userData['friend_id']; //value fo the friend_id clumn from $userData asscoative array retrievde and assgined to $userID varaible 
$stmt->close();

// checks wheather add_friends button is set or pressed
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_friend'])) {
    $friendId = $_POST['friend_id']; //assign value fo friend_id from the form to $friendID
    
     // add new row to myfriends table with values for friend_id1 and friend_id2 
    $addFriendQuery = "INSERT INTO myfriends (friend_id1, friend_id2) VALUES (?, ?)";
    $stmt = $conn->prepare($addFriendQuery); 
    $stmt->bind_param("ii", $userId, $friendId); //'ii' specified both ids are integers
    $stmt->execute();
    $stmt->close();

    // update the number of friends for current user $userID and new friend $friendID updating teh num_of_friends collumn and incrementing count by 1
    $updateNumFriends = "UPDATE friends SET num_of_friends = num_of_friends + 1 WHERE friend_id IN (?, ?)";
    $stmt = $conn->prepare($updateNumFriends); //prepare stmt
    $stmt->bind_param("ii", $userId, $friendId); 
    $stmt->execute();
    $stmt->close();
}

// implementing pagination feature 
$limit = 10; // max users per page set to 10 as specified in assign pdf
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1; //checks page throguh url using GET allows user to navigate between pages, if no page specified defaults to page one.
$offset = ($page - 1) * $limit; //sets teh page offset for isntace if user is on page 1 then it will start from the first record the offset is 0, or if user is on page 2 offset is 10 etc. 

// query find friends who are NOT currently friends with current user
// 'where friend_id !=' ensures user doesnt see themselves in the profile list
// 'friend_id NOT IN' excludes teh users who are already friends with users throguh using subquery selecting IDs of users who ARE friends with current user


$nonFriendsQuery = "SELECT friend_id, profile_name 
                    FROM friends 
                    WHERE friend_id != ? 
                    AND friend_id NOT IN (SELECT friend_id2 FROM myfriends WHERE friend_id1 = ?) 
                    LIMIT ? OFFSET ?";
$stmt = $conn->prepare($nonFriendsQuery);
$stmt->bind_param("iiii", $userId, $userId, $limit, $offset);// two user ids are present as one is used for teh main condition and other is used for the subquery.
$stmt->execute();
$nonFriendsResult = $stmt->get_result();//store result of stmt  

// query calcaultes the toal number of non friend users for the current user. total count is importnat as it help calcualte the total number of pages needed for paginated table
$totalNonFriendsQuery = "SELECT COUNT(*) as total_non_friends 
                         FROM friends 
                         WHERE friend_id != ? 
                         AND friend_id NOT IN (SELECT friend_id2 FROM myfriends WHERE friend_id1 = ?)";
$stmt = $conn->prepare($totalNonFriendsQuery);
$stmt->bind_param("ii", $userId, $userId);
$stmt->execute();
$stmt->bind_result($totalNonFriends); //binds results of teh query to $totalNonFriends
$stmt->fetch(); //values are retrieved from teh result set usign fetch()
$stmt->close();

$totalPages = ceil($totalNonFriends / $limit); // Calculate total pages

// calcualte the total number of friends so it is displayed upon login 
//query uses COUNT(*) function to count all rows in myfriends table where teh friend_id1 matches the logged in user ID
$totalFriendsQuery = "SELECT COUNT(*) as total_friends FROM myfriends WHERE friend_id1 = ?";
$stmt = $conn->prepare($totalFriendsQuery);
$stmt->bind_param("i", $userId);
$stmt->execute();
$stmt->bind_result($totalFriends); //binds result of the query 
$stmt->fetch(); //fetch() extracts teh value 
$stmt->close();

// calculate mutual friends feature, throguh creating a function getMutualFriends
// $conn made and id of logged in user $userID and other user $otherUserID passed as perameters 
//mf1 refers to $userID and mf2 refers to $otherUserID
//two instances of myfreind table joined if friend_id2 in mf1 is equal to friend_id2 in mf2, meaning both users mf1.friend_id1 and mf2.friend_id1 share a COMMON FRIEND with the same ID eg friend_id2 the mutual friend between two users.   
function getMutualFriends($conn, $userId, $otherUserId) {
    $mutualFriendsQuery = "SELECT COUNT(*) as mutual_count 
                           FROM myfriends mf1 
                           JOIN myfriends mf2 
                           ON mf1.friend_id2 = mf2.friend_id2 
                           WHERE mf1.friend_id1 = ? AND mf2.friend_id1 = ?";
    $stmt = $conn->prepare($mutualFriendsQuery);
    $stmt->bind_param("ii", $userId, $otherUserId);
    $stmt->execute();
    $stmt->bind_result($mutualCount);
    $stmt->fetch();
    $stmt->close();
    return $mutualCount;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Friends page</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body>
    <div class="outer-container"><!--outer contianer class-->
        <div class="container mt-5"> <!--container class with 'mt-5' margin top-->
            <h1 class="text-center mb-4">My Friend System</h1>
            <h2 class="text-center mb-4"><?php echo $profileName; ?>'s Add Friend Page</h2> <!--echo profile name of logged in user as heading -->
            <p class="text-center">Total number of friends is <?php echo $totalFriends; ?></p> <!-- print the 'total friend count'-->
            <form method="POST" action="friendadd.php">
                  <table class="table"><!-- create table with bootrsrap class for easy styling-->
                    <thead>
                        <tr>
                            <th>Profile Name</th> <!--collumnn headers made-->
                            <th>Mutual Friends</th> 
                            <th>Action</th>
                        </tr>
                   </thead>
                    <tbody> <!-- generate the table that displays list of user who are NOT friend with logged in user -->
                        <?php while ($nonFriend = $nonFriendsResult->fetch_assoc()): ?><!-- while loop iteartes through $nonfriendsresult and fetches each row assgining it to $nonFriend variable-->
                        <tr>
                            <td><?php echo htmlspecialchars($nonFriend['profile_name']); ?></td> <!-- start a new table row dispalying the profile_name of the non friend -->
                            <td><?php echo getMutualFriends($conn, $userId, $nonFriend['friend_id']); ?></td> <!-- creates a new cell dispplaying the count of mutual friends between the logged in user by calling teh getMutualFriend() function made earlier-->
                            <td><!-- form including add friend button, type= 'hidden' is set such that it stores the friend_id of the non friend allowing the form to send to friendadd.php without being visable to the user-->
                                <form method="POST" action="friendadd.php">
                                    <input type="hidden" name="friend_id" value="<?php echo $nonFriend['friend_id']; ?>">
                                    <button type="submit" name="add_friend" class="btn btn-success">Add as friend</button> <!--friend add button -->
                                </form>

                            </td>

                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>

            </form>

            <!-- all links related to pagination -->
            <nav aria-label="page navigation">
                <ul class="pagination justify-content-center"> <!-- bootstrap calss used, 'justify-content-center' used to center all the links-->
                    <?php if ($page > 1): ?> <!-- check if current page is greater than 1, if it is greater than one means the user can move back to first page-->
                    <li class="page-item"><!--page-item bootrsrtap class along with the pagination class above helps create teh pagination bar with previous button -->
                        <a class="page-link" href="?page=<?php echo $page - 1; ?>">Previous</a> <!-- create link to set teh page one before the current page number-->
                    </li>
                        <?php endif; ?> <!--generate a link for each page throguh looping from page 1 to the $totalPages -->
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <!-- creates list item for each page applying boostrap styling (page-item), 'active' used to add to the list element to visually show the current page-->
                    <li class="page-item <?php if ($i == $page) echo 'active'; ?>"> 
                        <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a><!--set url to incldue query (?page=) with the page number represented by $i variable -->
                    </li>
                    <?php endfor; ?> <!-- end the for loop geenrating the page numb links-->
                    <!-- checks wheather curretn page number $page is less than total numb of pages $totalPages-->
                        <?php if ($page < $totalPages): ?>
                    <li class="page-item"> <!-- pagination navigation with page number and 'next' button-->
                        <!-- when next button clicked the user is directed to the next page by incrementing by + 1-->
                        <a class="page-link" href="?page=<?php echo $page + 1; ?>">Next</a>
                    </li>
                    <?php endif; ?>
                    
                </ul>

            </nav>

            <div class="text-center mt-3"> <!-- margin set to top, text centered-->
                 <!-- friendlist back button and logout button-->
                <a href="friendlist.php" class="btn btn-link">Friend List</a>
                <a href="logout.php" class="btn btn-link">Log out</a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-NkdVkyYjIqqISuOr4Ry07N5gzw6EmgVpAsERJ7SdrGboKxONlDBKY6+3MnmwGjkv" crossorigin="anonymous"></script>
</body>
</html>

<?php
$conn->close();
?>
