<?php

    session_start();

    unset($_SESSION['logged_in']);
    unset($_SESSION['username']);

?>

<meta http-equiv="refresh" content="0;login.php">
