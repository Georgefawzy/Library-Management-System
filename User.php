<?php
class User {
    public $username;
    public $password;
    public $email;

    public function __construct($username, $password, $email) {
        $this->username = $username;
        $this->password = password_hash($password, PASSWORD_DEFAULT); // Hashing the password for security
        $this->email = $email;
    }

    // Method to validate if the email already exists
    public static function isEmailRegistered($email, $users) {
        foreach ($users as $user) {
            if ($user['email'] === $email) {
                return true; // Email already registered
            }
        }
        return false; // Email not registered
    }
}
?>
