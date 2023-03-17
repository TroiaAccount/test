<?php


class UserController extends User
{

    public function registerPage(){
        include 'views/register.php';
    }
    public function loginPage(){
        include 'views/login.php';
    }

    public function login()
    {
        $this->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'min:6', 'max:12']
        ]);
        $email = $_POST['email'];
        $password = $_POST['password'];
        $response = ['status' => false, 'errors' => null];
        $result = User::where(['email' => $email])->first();
        if($result != null){
            if (password_verify($password, $result['password'])) {
                $generateToken = $this->generateToken();
                $_SESSION['token'] = $generateToken;
                $ip = $_SERVER['REMOTE_ADDR'];
                $login = new Login;
                $login->insert(['token' => $generateToken, 'user_id' => $result['id'], 'ip_address' => $ip]);

                $response = ['status' => true, 'data' => "Success"];
            } else {
                $response['errors'] = ["Invalid email or password!"];
            }
        } else {
            $response['errors'] = ["Invalid email or password!"];
        }


        return json_encode($response);
    }

    private function generateApiToken(){
        $token = md5(time() . rand() . time() . rand() . time() . rand() . time());
        $find = User::where(['api_token' => $token])->first();
        if($find != null) {
            $token = $this->generateApiToken();
        }
        return $token;
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


        $result = User::where(['email' => $email])->first();

        if ($result != null) {
            $response['errors'] = "This email is already registered";
        } else {
            // Хешування пароля перед збереженням у базі даних
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $api_token = $this->generateApiToken();
            // Збереження даних у базі даних
            User::insert(['email' => $email, 'password' => $hashed_password, 'api_token' => $api_token]);
            $response = ['status' => true, 'data' => "Registration successful!"];
        }


        return json_encode($response);
    }

    public function logout(){
        $getUser = $this->getUser();
        $login = new Login;
        $mySession = $_SESSION['token'];
        $login->where(['token' => $mySession, 'user_id' => $getUser->id])->delete();
        unset($_SESSION['token']);
        return header("Location: /login");
    }
}
