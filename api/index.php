<?php
// Primeiro o autoload (composer)
require_once __DIR__ . '/../vendor/autoload.php';

// Carregar variáveis do .env
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

// Depois o arquivo de conexão com o banco
require_once __DIR__ . '/core/database.php';

// Depois tudo o que você já tinha
$controller = $_GET['controller'] ?? 'produto';
$action = $_GET['action'] ?? 'listar';

$controllerFile = __DIR__ . "/app/controllers/{$controller}_controller.php";

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
