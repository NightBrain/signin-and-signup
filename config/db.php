<?php

    //ระบบการเชื่อฐานข้อมูล SQL
    $servername = "localhost";
    $username = "root";
    $password = "rootroot";
    
    //สถานะการเชื่อต่อ
    try {
        $conn = new PDO("mysql:host=$servername;dbname=registration-system", $username, $password);
        // set the PDO error mode to exception
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        //echo "Connected successfully";
    } catch(PDOException $e) {
        echo "Connection failed: " . $e->getMessage();
    }
?>