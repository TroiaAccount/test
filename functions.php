<?php

class Helpers
{

    protected $conn;

    public function __construct()
    {
        $this->conn = new mysqli('127.0.0.1', 'root', '', 'test');
        if ($this->conn->connect_error) {
            die('Ошибка подключения (' . $this->conn->connect_errno . ') ' . $this->conn->connect_error);
        }
    }

    public function generateToken()
    {
        $token = md5(time() . rand() . time() . rand() . time() . rand());
        $login = new Login;
        $get = $login->where(['token' => $token])->first();
        if ($get != null) {
            $token = $this->generateToken($token);
        }
        return $token;
    }

    public function getUser()
    {
        $token = $_SESSION['token'] ?? null;
        $user = null;
        if ($token != null) {
            
            $login = new Login;
            $get = $login->where(['token' => $token])->first();
            if ($get != null) {
                $userModel = new User;
                $user = (object) $userModel->where(['id' => $get['user_id']])->first();
            }
        }
        if(isset($_GET['token'])){
            $userModel = new User;
            $user = (object) $userModel->where(['api_token' => $_GET['token']])->first();
        }
        return $user;
    }

    static public function getUrl($path)
    {
        if (substr($path, 0, 1) != "/") {
            $path = "/$path";
        }
        $domain = $_SERVER['HTTP_HOST'];
        return "http://$domain$path";
    }

    public function validate($params, $method = "POST")
    {
        $response = ['status' => false, 'errors' => []];

        if ($_SERVER["REQUEST_METHOD"] != $method) {
            $response['errors']['method'] = "This route use only POST method";
        }
        $request = $_POST;
        if($method == "GET"){
            $request = $_GET;
        }
        foreach ($params as $key => $param) {
            $key = trim($key);
            foreach ($param as $setting) {
                $settingName = $setting;
                if (stripos($setting, 'min:') !== false || stripos($setting, 'max:') !== false || stripos($setting, 'exists:')) {
                    $setting = explode(":", $setting);
                    $settingName = $setting[0];
                }
                switch (trim($settingName)) {
                    case "required":
                        if (!isset($request[$key]) && $request[$key] == "" && $request[$key] == null) {
                            $response['errors'][] = "$key is required";
                        }
                        break;
                    case "email":
                        if (!filter_var($request[$key], FILTER_VALIDATE_EMAIL)) {
                            $response['errors'][] = "$key invalid";
                        }
                        break;
                    case "confirmate":
                        if (!isset($request["confirm_$key"]) || $request["confirm_$key"] != $request[$key]) {
                            $response['errors'][] = "$key don`t confirmate";
                        }
                        break;
                    case "min":
                        if (strlen($request[$key]) < $setting[1]) {
                            $response['errors'][] = "$key less than the minimum value";
                        }
                        break;
                    case "max":
                        if (strlen($request[$key]) > $setting[1]) {
                            $response['errors'][] = "$key greater than the maximum value";
                        }
                        break;
                    case "string":
                        if (!is_string($request[$key])) {
                            $response['errors'][] = "$key must be a string";
                        }
                        break;
                    case "is_array":
                        if(!is_array($request[$key])){
                            $response['errors'][] = "$key must be a array";
                        }
                    break;
                    case "exists":
                        $getParams = $setting[1];
                        $getParams = explode(',', $setting[1]);
                        $action = "=";
                        if(is_string($request[$key])){
                            $action = "LIKE";
                        }
                        $find = $this->conn->query("SELECT * FROM " . $getParams[0] . " WHERE " . $getParams[1] . " $action '" . $request[$key] . "'")->fetch_assoc();
                        if($find == null){
                            $response['errors'][] = "$key dont exists in " . $getParams[0] . "table";
                        }
                    break;
                }
            }
        }

        if (count($response['errors']) > 0) {
            echo json_encode($response);
            header("Content-Type: application/json");
            exit;
        }
    }

    public function __destruct()
    {
        mysqli_close($this->conn);
    }
}
