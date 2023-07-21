<?php

try{
    $dbCo = new PDO("mysql:host=localhost;dbname=accounts;charset=utf8mb4", "root", "");
    $dbCo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE,PDO::FETCH_ASSOC);
}
catch (Exception $e) {
    die("Unable to connect to the database.".$e->getMessage());
}