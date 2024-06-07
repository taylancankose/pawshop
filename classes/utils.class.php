<?php
include_once 'classes/db.class.php';
?>
<?php
// Import PHPMailer classes into the global namespace
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Load Composer's autoloader
require 'vendor/autoload.php';
?>
<?php

class Utils extends Db
{
    public function isLoggedIn()
    {
        if (isset($_SESSION["loggedIn"]) && $_SESSION["loggedIn"] === true) {
            return true;
        } else {
            return false;
        }
    }

    public function isAdmin()
    {
        if ($this->isLoggedIn() && isset($_SESSION["user_type"]) && $_SESSION["user_type"] === "admin") {
            return true;
        } else {
            return false;
        }
    }

    public function control_input($data)
    {
        $data = htmlspecialchars($data);
        $data = stripslashes($data);

        return $data;
    }

    public function uploadImage($file)
    {
        $message = "";
        $uploadOK = 1;
        $fileTempPath = $file["tmp_name"];
        $fileName = $file["name"];
        $fileSize = $file["size"];
        $maxfileSize = ((1024 * 1024) * 1);
        $fileExtensions = array("jpg", "jpeg", "png");
        $file_path = "./uploads/";

        if ($fileSize > $maxfileSize) {
            $message = "File size is too big";
            $uploadOK = 0;
        }
        $fileName_arr = pathinfo($fileName);
        $fileName_without_extension = $fileName_arr['filename'];
        $file_extention = isset($fileName_arr['extension']) ? $fileName_arr['extension'] : '';

        if (!in_array($file_extention, $fileExtensions)) {
            $message .= "Undefined file extension";
            $message .= "Please upload only: " . implode(", ", $fileExtensions);
        }
        $new_file_name = md5(time() . $fileName_without_extension) . '.' . $file_extention;
        $final_path = $file_path . $new_file_name;
        if ($uploadOK == 0) {
            $message .= "Dosya yüklenemedi";
        } else {
            if (move_uploaded_file($fileTempPath, $final_path)) {
                $message .= "dosya yüklendi.";
            }
        }

        return array(
            "isSuccess" => $uploadOK,
            "message" => $message,
            "image" => $new_file_name
        );
    }

    public function sendOTP($email, $username, $otp)
    {
        $mail = new PHPMailer(true);
        $env = parse_ini_file('.env');

        $mailHost = $env["MAIL_HOST"];
        $mailPassword = $env["MAIL_PASSWORD"];
        $mailUsername = $env["MAIL_USERNAME"];
        $mailPort = $env["MAIL_PORT"];

        try {
            // Server settings
            $mail->SMTPDebug = 0;            // Debug mod kapalı redirect için kapatmak lazım
            $mail->isSMTP();                                  // SMTP Kullanarak Gönder
            $mail->Host       = $mailHost;             // SMTP Host
            $mail->SMTPAuth   = true;                         // SMTP Doğrulaması
            $mail->Username   = $mailUsername;  // SMTP Kullanıcı Adı
            $mail->Password   = $mailPassword;          // SMTP Şifre
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // SSL/TLS Ayarı
            $mail->Port       = $mailPort;                          // PORT Ayarı
            $mail->CharSet    = 'UTF-8';                      // Karakter seti
            $mail->setLanguage('tr', '/optional/path/to/language/directory/'); // Dil Ayarı

            // Recipients
            $mail->setFrom($mailUsername, 'Pawshop');
            $mail->addAddress($email, $username); // Alıcı E-Posta - İsim
            $mail->addReplyTo($mailUsername, 'Pawshop'); // Alıcı cevapla dediğinde mailin gideceği adres


            // Content
            $mail->isHTML(true); // E-Posta HTML
            $mail->Subject = 'Welcome to the Pawshop';
            $mail->Body    = '  <body
        style="
          margin: 0;
          background: #ffffff;
          font-size: 14px;
        "
      >
        <div
          style="
            max-width: 680px;
            margin: 0 auto;
            padding: 45px 30px 60px;
            background: #f4f7ff;
            background-repeat: no-repeat;
            background-size: 800px 452px;
            background-position: top center;
            font-size: 14px;
            color: #434343;
          "
        >
          <main>
            <div
              style="
                margin: 0;
                margin-top: 70px;
                padding: 92px 30px 115px;
                background: #ffffff;
                border-radius: 30px;
                text-align: center;
              "
            >
              <div style="width: 100%; max-width: 489px; margin: 0 auto;">
                <h1
                  style="
                    margin: 0;
                    font-size: 24px;
                    font-weight: 500;
                    color: #1f1f1f;
                  "
                >
                  Your OTP
                </h1>
                <p
                  style="
                    margin: 0;
                    margin-top: 17px;
                    font-size: 16px;
                    font-weight: 500;
                  "
                >
                  Hey  ' . htmlspecialchars($username) . ',
                </p>
                <p
                  style="
                    margin: 0;
                    margin-top: 17px;
                    font-weight: 500;
                    letter-spacing: 0.56px;
                  "
                >
                  Thank you for choosing PawShop Company. Use the following OTP
                  to complete the procedure to change your email address. Do not share this code with others, including PawShop
                  employees.
                </p>
                <p
                  style="
                    margin: 0;
                    margin-top: 60px;
                    font-size: 40px;
                    font-weight: 600;
                    letter-spacing: 25px;
                    color: #ba3d4f;
                  "
                >
                ' . htmlspecialchars($otp) . '
                </p>
              </div>
            </div>
    
            <p
              style="
                max-width: 400px;
                margin: 0 auto;
                margin-top: 90px;
                text-align: center;
                font-weight: 500;
                color: #8c8c8c;
              "
            >
              Need help? Ask at
              <a
                href="mailto:sy_bf@hotmail.com"
                style="color: #499fb6; text-decoration: none;"
                >sy_bf@hotmail.com</a
              >
            </p>
          </main>
    
          <footer
            style="
              width: 100%;
              max-width: 490px;
              margin: 20px auto 0;
              text-align: center;
              border-top: 1px solid #e6ebf1;
            "
          >
            <p
              style="
                margin: 0;
                margin-top: 40px;
                font-size: 16px;
                font-weight: 600;
                color: #434343;
              "
            >
              PawShop Company
            </p>
            </div>
            <p style="margin: 0; margin-top: 16px; color: #434343;">
              Copyright © 2024 Company. All rights reserved.
            </p>
          </footer>
        </div>
      </body>';
            $mail->AltBody = 'Welcome to the PawShop Company. Here is your OTP: ' . htmlspecialchars($otp) . '';

            $mail->send();
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    }

    public function verifyOTP($otp,$user_id){
        $sql = "SELECT * FROM users WHERE id = :user_id";
        $stmt = $this->connect()->prepare($sql);
        $stmt->execute(['user_id' => $user_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user) {
            $sql = "UPDATE users SET verified = 1 WHERE id = :user_id";
            $stmt = $this->connect()->prepare($sql);
            return $stmt->execute(['user_id' => $user_id]);
        } else {
            return false;
        }
    }
}
