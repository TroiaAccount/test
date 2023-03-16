<?php
include_once "config.php";
include_once("functions.php");
error_reporting(E_ALL & ~E_WARNING);


function autoload($className){
    include_once("controllers/$className.php");
}

spl_autoload_register("autoload");

session_start();
include_once('routes.php');

// получение запрошенного URL-адреса
$request_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
// определение запрошенного маршрута
$route = $routes[$request_uri] ?? null;

// если маршрут не был найден, отображается страница 404 ошибки
if ($route == null) {
    header('Location: login');
    exit();
}
// если маршрут обращается в API
if (isset($route['controller'])) {
    header('Content-Type: application/json');
    $response = call_user_func([new $route['controller']($conn), $route['method']]);
    echo $response;
    exit;
}

$helpers = new Helpers($conn);

// загрузка соответствующей страницы
if ($route['auth'] == true) {
    $user = $helpers->getUser();
    if ($user == null) {
        
        header('Location: login');
        exit();
    }
?>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">My site</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="#">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
<?php

}
?>
<!DOCTYPE html>
<html lang="en">
<?php include("partials/head.php") ?>

<script>
    toastr.options = {
        "positionClass": "toast-bottom-right",
    };
</script>

<body>
    <div class="container">
        <?php include($route['file']); ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap/dist/js/bootstrap.min.js"></script>
    <script src="<?= $helpers->getUrl('assets/js/main.js') ?>"></script>
</body>

</html>
<?php

mysqli_close($conn);
?>