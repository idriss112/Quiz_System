<?php
require_once 'classes/Auth.php';

Auth::startSession();
Auth::logout(); // destroys session and unsets user

header("Location: login.php");
exit();
?>