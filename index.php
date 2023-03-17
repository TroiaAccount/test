<?php

include_once("functions.php");
error_reporting(E_ALL & ~E_WARNING);


function autoloadControllers($className)
{
    include_once("controllers/$className.php");
}

function autoloadModels($className)
{
    include_once("models/$className.php");
}
spl_autoload_register("autoloadControllers");
spl_autoload_register("autoloadModels");

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

// если в API
$helpers = new Helpers;
if (isset($route['api']) && $route['api'] == true) {
    if (isset($route['auth']) && $route['auth'] == true) {

        $user = $helpers->getUser();
        if ($user->id == null) {

            header('Content-Type: application/json');
            echo json_encode(['status' => false, 'errors' => "Access denied"]);
            http_response_code(403);
            exit;
        }

        $method = $route['method'];
        $controller = $route['controller'];
        $controller = new $controller;
        $response = $controller->$method();
        header('Content-Type: application/json');
        echo $response;
        exit;
    }
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
    <?php

    // загрузка соответствующей страницы
    if ($route['auth'] == true) {

        $user = $helpers->getUser();
        if ($user->id == null) {
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
                            <a class="nav-link active" aria-current="page" href="<?= $helpers->getUrl('api/logout') ?>">Logout</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" aria-current="page" href="#">API TOKEN: <?= $user->api_token ?></a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    <?php
    }
    ?>
    <div class="container">
        <?php
        $method = $route['method'];
        $controller = $route['controller'];
        $controller = new $controller;
        $response = $controller->$method();
        echo $response;
        ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap/dist/js/bootstrap.min.js"></script>
    <script src="<?= $helpers->getUrl('assets/js/main.js') ?>"></script>
</body>

</html>