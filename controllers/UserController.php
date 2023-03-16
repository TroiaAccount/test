<?php

class UserController extends Helpers
{

    public function login()
    {
        $this->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'min:6', 'max:12']
        ]);
        $email = $_POST['email'];
        $password = $_POST['password'];

        $result = $this->conn->query("SELECT * FROM users WHERE email='$email'");

        if ($result->num_rows($result) == 1) {
            $row = $result->fetch_assoc($result);
            if (password_verify($password, $row['password'])) {
                $generateToken = $this->generateToken();
                $_SESSION['token'] = $generateToken;
                $ip = $_SERVER['REMOTE_ADDR'];
                $this->conn->query("INSERT logins (token, user_id, ip_address) VALUES ('$generateToken', '" . $row['id'] . "', '$ip')");
                $response = ['status' => true, 'data' => "Success"];
            } else {
                $response['errors'] = "Invalid email or password!";
            }
        } else {
            $response['errors'] = "Invalid email or password!";
        }

        return json_encode($response);
    }

    public function register()
    {
        $this->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmate', 'min:6', 'max:12']
        ]);
        $response = ['status' => false, 'errors' => null];
        $email = $_POST["email"];
        $password = $_POST["password"];


        $result = $this->conn->query("SELECT * FROM users WHERE email='$email'");

        if ($result->num_rows($result) > 0) {
            $response['errors'] = "This email is already registered";
        } else {
            // Хешування пароля перед збереженням у базі даних
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Збереження даних у базі даних
            $sql = "INSERT INTO users (email, password) VALUES ('$email', '$hashed_password')";
            $this->conn->query($sql);
            $response = ['status' => true, 'data' => "Registration successful!"];
        }


        return json_encode($response);
    }
}
