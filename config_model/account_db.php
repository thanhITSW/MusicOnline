<?php
    require_once('db.php');

    require("PHPMailer/src/PHPMailer.php");
    require("PHPMailer/src/SMTP.php");

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    function login($user, $pass) {
        $conn = get_connection();
        $sql = "select * from account where username = ?";

        $stm = $conn->prepare($sql);
        $stm->bind_param('s', $user);

        if(!$stm->execute()) {
            return array('code' => 1, 'error' => 'Can not execute command');
        }

        $result = $stm->get_result();

        if($result->num_rows == 0) {
            return array('code' => 1, 'error' => 'User does not exists');
        }
        
        $data = $result->fetch_assoc();

        $hashed_pass = $data['password'];

        if(!password_verify($pass, $hashed_pass)) {
            return array('code' => 2, 'error' => 'Invalid password');
        }
        else if($data['activated'] == 0){
            return array('code' => 3, 'error' => 'This account is not activated');
        }
        else {
            return array('code' => 0, 'error' => '', 'data' => $data);
        }
    }

    function is_email_exists($email) {
        $conn = get_connection();
        $sql = "select username from account where email = ?";

        $stm = $conn->prepare($sql);
        $stm->bind_param("s", $email);

        if(!$stm->execute()) {
            die("Query error: " . $stm->errno);
        }

        $result = $stm->get_result();
        
        if($result->num_rows > 0) {
            return true;
        }
        return false;
    }

    function register($user, $pass, $name, $email) {
        if(is_email_exists($email)) {
            return array('code' => 1, 'error' => 'Email exists');
        }

        $hash = password_hash($pass, PASSWORD_DEFAULT);
        $rand = random_int(0, 1000);
        $token = md5($user . '+' . $rand);

        $conn = get_connection();
        $sql = "insert into account(username, password, name, email, activate_token) values (?,?,?,?,?)";

        $stm = $conn->prepare($sql);
        $stm->bind_param('sssss', $user, $hash, $name, $email, $token);

        if(!$stm->execute()) {
            return array('code' => 2, 'error' => 'Can not execute command');
        }

        // send verification emmail
        send_activation_email($email, $token);

        return array('code' => 0, 'error' => 'Create account successful');
    }

    function send_activation_email($email, $token) {
        $mail = new PHPMailer(true);
        try {
            //Server settings
            $mail->SMTPDebug = 0;
            $mail->isSMTP();
            $mail->CharSet = 'UTF-8';
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'thanhtuancr1234@gmail.com';
            $mail->Password = 'myacakegkdivhvkk';
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;
        
            //Recipients
            $mail->setFrom('thanhtuancr1234@gmail.com', 'Thanh Tuan');
            $mail->addAddress($email, 'Pham Thanh Tuan');
            $mail->addCC('thanhtuancr1234@gmail.com');

            //Content
            $mail->isHTML(true);
            $mail->Subject = 'Xác minh tài khoản của bạn';
            $mail->Body = "Click <a href='http://localhost/activate.php?email=$email&token=$token'>vào đây</a> để xác minh tài khoản của bạn";
        
            $mail->send();

        } catch (Exception $e) {
            echo 'Message could not be sent. Mailer Error: ', $mail->ErrorInfo;
        }
    }

    function send_reset_email($email, $token) {
        $mail = new PHPMailer(true);
        try {
            //Server settings
            $mail->SMTPDebug = 0;
            $mail->isSMTP();
            $mail->CharSet = 'UTF-8';
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'thanhtuancr1234@gmail.com';
            $mail->Password = 'myacakegkdivhvkk';
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;
        
            //Recipients
            $mail->setFrom('thanhtuancr1234@gmail.com', 'Thanh Tuan');
            $mail->addAddress($email, 'Pham Thanh Tuan');
            $mail->addCC('thanhtuancr1234@gmail.com');

            //Content
            $mail->isHTML(true);
            $mail->Subject = 'Khôi phục mật khẩu của bạn';
            $mail->Body = "Click <a href='http://localhost/reset_password.php?email=$email&token=$token'>vào đây</a> để khôi phục mật khẩu của bạn";
        
            $mail->send();

        } catch (Exception $e) {
            echo 'Message could not be sent. Mailer Error: ', $mail->ErrorInfo;
        }
    }

    function active_account($email, $token) {
        $conn = get_connection();
        $sql = "select username from account where email = ? and activate_token = ? and activated = 0";

        $stm = $conn->prepare($sql);
        $stm->bind_param('ss', $email, $token);

        if(!$stm->execute()) {
            return array('code' => 1, 'error' => 'Can not execute command');
        }

        $result = $stm->get_result();

        if($result->num_rows == 0) {
            return array('code' => 2, 'error' => 'Email address or token not found');
        }

        //found
        $sql = "update account set activated = 1, activate_token = '' where email = ?";
        $stm = $conn->prepare($sql);
        $stm->bind_param('s', $email);

        if(!$stm->execute()) {
            return array('code' => 1, 'error' => 'Can not execute command');
        }

        return array('code' => 0, 'message' => 'Account activated');
    }

    //insert and update token
    function reset_password($email) {
        if(!is_email_exists($email)) {
            return array('code' => 1, 'error' => 'Email does not exist');
        }

        $conn = get_connection();

        $token = md5($email . '+' . random_int(1000, 2000));
        $sql = 'update reset_token set token = ? where email = ?';

        $stm = $conn->prepare($sql);
        $stm->bind_param('ss', $token, $email);

        if(!$stm->execute()) {
            return array('code' => 2, 'error' => 'Can not execute command');
        }

        if($stm->affected_rows == 0) {
            $exp = time() + 3600 * 24;

            $sql = 'insert into reset_token value (?,?,?)';
            $stm = $conn->prepare($sql);
            $stm->bind_param('ssi', $email, $token, $exp);

            if(!$stm->execute()) {
                return array('code' => 2, 'error' => 'Can not execute command');
            }
        }

        send_reset_email($email, $token);
        return array('code' => 0, 'message' => 'Reset password successful');
    }

    function update_password($email, $pass) {
        $conn = get_connection();

        $hash = password_hash($pass, PASSWORD_DEFAULT);

        $sql = "update account set password = ? where email = ?";

        $stm = $conn->prepare($sql);
        $stm->bind_param('ss', $hash, $email);

        if(!$stm->execute()) {
            return array('code' => 2, 'error' => 'Can not execute command');
        }

        $sql = "update reset_token set token = '', expire_on = 0 where email = ?";
        $stm = $conn->prepare($sql);
        $stm->bind_param('s', $email);

        if(!$stm->execute()) {
            return array('code' => 1, 'error' => 'Can not execute command');
        }

        return array('code' => 0, 'message' => 'Reseted password');
    }
?>