<?php
$host = 'localhost';
$dbname = 'cashcrew';
$username= 'root';
$password = '';

try{
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset+utf8", $username, $password);
    

}catch (PDOExeption $e) {
    die ("Database connection ERROR");
}

?>