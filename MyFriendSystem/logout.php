<?php
//logout feature after user signed into accoutn
//session startt
session_start();

//clear session variable using unset()
session_unset();
//remove current session
session_destroy();

//set location back to the index.php page
header("Location: index.php");
exit();
?>
