<?php
/**
 * Drakon Webhook Handler
 * URL: https://a49000.win/webhook/drakon_api.php
 * Also handles: /webhook/drakon_api.php/drakon_api (when Drakon concatenates)
 */

header('Content-Type: application/json');

// Fix PATH_INFO when Drakon concatenates /drakon_api
if (isset($_SERVER['PATH_INFO'])) {
    $_SERVER['REQUEST_URI'] = preg_replace('#/drakon_api\.php/drakon_api#', '/drakon_api', $_SERVER['REQUEST_URI']);
}

// Log the request
$logFile = dirname(__DIR__, 2) . '/storage/logs/webhook-access.log';
@file_put_contents($logFile, date('Y-m-d H:i:s') . " - Request received from " . ($_SERVER['REMOTE_ADDR'] ?? 'unknown') . "\n", FILE_APPEND);

// Bootstrap Laravel
try {
    require dirname(__DIR__, 2) . '/vendor/autoload.php';
    $app = require_once dirname(__DIR__, 2) . '/bootstrap/app.php';
    
    // Force route to drakon_api
    $_SERVER['REQUEST_URI'] = '/drakon_api';
    
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    
    $response = $kernel->handle(
        $request = Illuminate\Http\Request::capture()
    );
    
    $response->send();
    $kernel->terminate($request, $response);
    
} catch (\Exception $e) {
    @file_put_contents($logFile, "ERROR: " . $e->getMessage() . "\n", FILE_APPEND);
    
    http_response_code(200);
    echo json_encode([
        'status' => true,
        'message' => 'Webhook endpoint active'
    ]);
}
exit;
