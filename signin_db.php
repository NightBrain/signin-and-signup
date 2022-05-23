<?php 
    //แจ้งเตือน ERROR ตอนเข้าสู่ระบบ
    session_start();
    require_once 'config/db.php';

    //รับข้อมูลจากฟอร์ม เมื่อกดปุ่ม Sign Up
    if (isset($_POST['signin'])) {
        $email = $_POST['email'];
        $password = $_POST['password'];
        

        //เช็คการกรอกข้อมูลของฟอร์ม Sign Up ว่าเป็นค่าว่างหรือเปล่า
        if (empty($email)) {
            $_SESSION['error'] = 'กรุณากรอกอีเมล';
            header("location: signin.php");
        } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { //เช็ครูปแบบอีเมลว่าถูกต้องหรือไม่
            $_SESSION['error'] = 'รูปแบบอีเมลไม่ถูกต้อง';
            header("location: signin.php");
        } else if (empty($password)) {
            $_SESSION['error'] = 'กรุณากรอกรหัสผ่าน';
            header("location: signin.php");
        } else if (strlen($_POST['password']) > 20 ||  strlen($_POST['password']) < 5) { //เช็คความ สั้น-ยาว ของรหัสผ่าน
            $_SESSION['error'] = 'รหัสผ่านต้องมีความยาวระหว่าง 5 ถึง 20 ตัวอักษร';
            header("location: signin.php");
        } else { 
            try { 
                //เช็คช้อมูลในระบบ
                $check_data = $conn->prepare("SELECT * FROM users WHERE email = :email");
                $check_data->bindparam("email", $email);
                $check_data->execute();
                $row = $check_data->fetch(PDO::FETCH_ASSOC);

                if ($check_data->rowCount() > 0) {

                    if ($email == $row['email']) {
                        if (password_verify($password, $row['password'])){
                            if ($row['urole'] == 'admin') { //เช็คบทบาทว่าใครล็อกอิน
                                $_SESSION['admin_login'] = $row['id'];
                                header("location: admin.php");
                            } else {
                                $_SESSION['user_login'] = $row['id'];
                                header("location: user.php");
                            } 
                        } else {
                            $_SESSION['error'] = 'รหัสผ่านผิด'; //เช็ครหัสผ่านในระบบ
                            header("location: signin.php");
                        }
                    } else {
                        $_SESSION['error'] = 'อีเมลผิด'; //เช็คอีเมลในระบบ
                        header("location: signin.php");
                    }
                } else {
                    //แจ้งเตือนไม่มีข้อมูลในระบบ
                    $_SESSION['error'] = "ไม่มีข้อมูลในระบบ";
                    header("location: signin.php");
                }

            } catch(PDOException $e) {
                echo $e->getMessage();
            }

        }
    }


?>