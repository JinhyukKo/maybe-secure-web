<?php
    if(!isset($_SESSION['username'])){
        header("Location: /auth/login.php");
    }
?>