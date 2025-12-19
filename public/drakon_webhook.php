<?php
// Webhook Drakon - Integração real com Laravel
require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

header('Content-Type: application/json');

try {
    // Criar request do Symfony
    $request = Illuminate\Http\Request::capture();
    
    // Processar através do Laravel
    $response = $kernel->handle($request);
    
    // Se chegou aqui mas response é 404, processar manualmente
    if ($response->getStatusCode() === 404) {
        // Processar webhook manualmente
        $input = json_decode(file_get_contents('php://input'), true);
        $method = $input['method'] ?? $_POST['method'] ?? null;
        
        if (!$method) {
            echo json_encode([
                'status' => 1,
                'message' => 'Drakon webhook active via PHP',
                'timestamp' => date('c')
            ]);
            exit;
        }
        
        // Chamar o controller diretamente
        $controller = new \App\Http\Controllers\Api\DrakonController();
        $laravelRequest = \Illuminate\Http\Request::create('/drakon_api', 'POST', $input);
        $result = $controller->webhook($laravelRequest);
        echo $result->getContent();
    } else {
        echo $response->getContent();
    }
    
    $kernel->terminate($request, $response);
    
} catch (\Exception $e) {
    echo json_encode([
        'status' => 0,
        'error' => $e->getMessage()
    ]);
}
