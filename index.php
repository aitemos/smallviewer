<?php
require_once "./_includes/bootstrap.inc.php";
session_start();
$_SESSION["logged"]=false;
$sql = "SELECT * FROM employee WHERE login=:login";
$stmt = DB::getConnection()->prepare($sql);
$stmt->bindParam(':login',$_POST['name']);
$stmt -> execute();
$login = $stmt->fetch();
    if($_POST['name'] == $login->login && hash("sha256",$_POST['password']) == $login->password){
        $_SESSION["logged"]= true;
        $_SESSION["admin"]= $login->admin;
        $_SESSION["surname"]=$login->surname;
        $_SESSION["name"]= $login->name;
        $_SESSION["id"]=$login->employee_id;
    }

if($_SESSION["logged"]) { echo "Přihlášen";header('Location: menu'); exit;}
else{echo "Špatné heslo"; header('Location: login.php');exit;}

