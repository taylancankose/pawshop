<?php
include_once 'classes/db.class.php';
include_once 'classes/utils.class.php';

?>
<?php
// Import PHPMailer classes into the global namespace

?>
<?php

class Auth extends Db
{
    public function getUsers()
    {
      $utils = new Utils();
        if ($utils->isAdmin()) {
            $sql = "SELECT * FROM users";
            $stmt = $this->connect()->prepare($sql);
            $stmt->execute();

            return $stmt->fetchAll();
        } else {
            return [];
        }
    }

    public function registerUser(string $username, string $email, string $password)
    {
        // Check if the username already exists
        $sql_check_username = "SELECT COUNT(*) FROM users WHERE username = :username";
        $stmt_check_username = $this->connect()->prepare($sql_check_username);
        $stmt_check_username->execute(['username' => $username]);
        $username_exists = $stmt_check_username->fetchColumn();

        if ($username_exists > 0) {
            return "Username already exists";
        }

        // Check if the email already exists
        $sql_check_email = "SELECT COUNT(*) FROM users WHERE email = :email";
        $stmt_check_email = $this->connect()->prepare($sql_check_email);
        $stmt_check_email->execute(['email' => $email]);
        $email_exists = $stmt_check_email->fetchColumn();

        if ($email_exists > 0) {
            return "Email already exists";
        }

        $sql = "INSERT INTO users (username, email, password) VALUES (:username, :email, :password)";

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $this->connect()->prepare($sql);
        return $stmt->execute([
            'username' => $username,
            'email' => $email,
            'password' => $hashed_password,
        ]);
    }

    public function login($email, $password)
    {
        $user = $this->getUser($email);
        if ($user && password_verify($password, $user->password)) {
            return $user;
        } else {
            return false;
        }
    }

    public function getUser($email)
    {
        $sql = "SELECT * FROM users WHERE email=:email";
        $stmt = $this->connect()->prepare($sql);
        $stmt->execute(['email' => $email]);
        return $stmt->fetch();
    }

    public function updateUserType($user_id, $user_type){
        $sql = "UPDATE users SET user_type =:user_type WHERE id=:user_id";
        $stmt = $this->connect()->prepare($sql);
        return $stmt->execute(['user_id' => $user_id, 'user_type' => $user_type]);
    }

    public function getUserByUsername($username)
    {
        $sql = "SELECT * FROM users WHERE username=:username";
        $stmt = $this->connect()->prepare($sql);
        $stmt->execute(['username' => $username]);
        return $stmt->fetch();
    }

}
