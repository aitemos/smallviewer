<?php
session_start();
$_SESSION["logged"]=false;
$_SESSION["admin"]=false;
header('Location: login.php');
exit();

