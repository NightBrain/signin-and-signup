<?php 
    //แจ้งเตือน ERROR ตอนสมัคร
    session_start();
    require_once 'config/db.php';

    //รับข้อมูลจากฟอร์ม เมื่อกดปุ่ม Sign Up
    if (isset($_POST['signup'])) {
        $firstname = $_POST['firstname'];
        $lastname = $_POST['lastname'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $c_password = $_POST['c_password'];
        $urole = 'user'; //จัดการ admin/user

        //เช็คการกรอกข้อมูลของฟอร์ม Sign Up ว่าเป็นค่าว่างหรือเปล่า
        if (empty($firstname)) {
            $_SESSION['error'] = 'กรุณากรอกชื่อ';
            header("location: index.php");
        } else if (empty($lastname)) {
            $_SESSION['error'] = 'กรุณากรอกนามสกุล';
            header("location: index.php");
        } else if (empty($email)) {
            $_SESSION['error'] = 'กรุณากรอกอีเมล';
            header("location: index.php");
        } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { //เช็ครูปแบบอีเมลว่าถูกต้องหรือไม่
            $_SESSION['error'] = 'รูปแบบอีเมลไม่ถูกต้อง';
            header("location: index.php");
        } else if (empty($password)) {
            $_SESSION['error'] = 'กรุณากรอกรหัสผ่าน';
            header("location: index.php");
        } else if (strlen($_POST['password']) > 20 ||  strlen($_POST['password']) < 5) { //เช็คความ สั้น-ยาว ของรหัสผ่าน
            $_SESSION['error'] = 'รหัสผ่านต้องมีความยาวระหว่าง 5 ถึง 20 ตัวอักษร';
            header("location: index.php");
        } else if (empty($c_password)) {
            $_SESSION['error'] = 'กรุณายืนยันรหัสผ่าน';
            header("location: index.php");
        } else if ($password != $c_password) { //เช็คว่ารหัสผ่านตรงกันไหม
            $_SESSION['error'] = 'รหัสผ่านไม่ตรงกัน';
            header("location: index.php");
        } else { 
            try { 
                //เช็คว่ามีอีเมลซ้ำกันในระบบหรือไม่
                $check_email = $conn->prepare("SELECT email FROM users WHERE email = :email");
                $check_email->bindparam("email", $email);
                $check_email->execute();
                $row = $check_email->fetch(PDO::FETCH_ASSOC);

                if ($row['email'] == $email) {
                    $_SESSION['warning'] = "อีเมลนี้ถูกใช้ไปแล้ว <a href='signin.php'>คลิ๊กที่นี่</a> เพื่อเข้าสู่ระบบ";
                    header("location: index.php");
                } else if  (!isset($_SESSION['error'])) { //เพิ่มข้อมูลไปยังฐานข้อมูล
                    $passwordHash = password_hash($password, PASSWORD_DEFAULT); // เข้ารหัส ก่อนที่จะเก็บไปในฐานข้อมูล
                    $stmt = $conn->prepare("INSERT INTO users(firstname, lastname, email, password, urole) 
                                            VALUES(:firstname, :lastname, :email, :password, :urole)");
                    $stmt->bindParam(":firstname", $firstname);
                    $stmt->bindParam(":lastname", $lastname);
                    $stmt->bindParam(":email", $email);
                    $stmt->bindParam(":password", $passwordHash);
                    $stmt->bindParam(":urole", $urole);
                    $stmt->execute();
                    //แจ้งเตือนสมัครเสร็จ
                    $_SESSION['success'] = "สมัครสมาชิกเรียบร้อยแล้ว! <a href='signin.php' class='alert-link'>คลิ๊กที่นี่</a> เพื่อเข้าสู่ระบบ";
                    header("location: index.php");
                } else {
                    //แจ้งเตือนมีข้อผิดพลาดบางอย่าง
                    $_SESSION['error'] = "มีบางอย่างผิดพลาด";
                    header("location: index.php");
                }

            } catch(PDOException $e) {
                echo $e->getMessage();
            }

        }
    }


?>