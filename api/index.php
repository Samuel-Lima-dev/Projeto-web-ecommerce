<?php

require_once __DIR__ . '/../vendor/autoload.php';


$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();


require_once __DIR__ . '/core/database.php';


$controller = $_GET['controller'] ?? 'produto';
$action = $_GET['action'] ?? 'listar';

$controllerFile = __DIR__ . "/app/controllers/{$controller}.php";

if (file_exists($controllerFile)) {
    require_once $controllerFile;
    $controllerClass = ucfirst($controller) . 'Controller';

    if (class_exists($controllerClass)) {
        $obj = new $controllerClass();

        if (method_exists($obj, $action)) {
            $obj->$action();
        } else {
            echo "Ação não encontrada: " . $action;
        }
    } else {
        echo "Controller não encontrado: " . $controllerClass;
    }
} else {
    echo "Arquivo de controller não encontrado: " . $controllerFile;
}
?>
