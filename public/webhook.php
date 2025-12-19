<?php
/**
 * Drakon Webhook Handler - Root Level
 * This file handles when Drakon appends /drakon_api to the base URL
 * URL configured in Drakon: https://a49000.win/webhook
 * Drakon will call: https://a49000.win/webhook/drakon_api
 */

header('Content-Type: application/json');

// Log the access for debugging
$logFile = __DIR__.'/../storage/logs/webhook-direct.log';
@file_put_contents($logFile, date('Y-m-d H:i:s') . " - Webhook accessed\n", FILE_APPEND);

// Bootstrap Laravel
try {
    require __DIR__.'/../vendor/autoload.php';
    $app = require_once __DIR__.'/../bootstrap/app.php';
    
    // Force the request to go to drakon_api route
    $_SERVER['REQUEST_URI'] = '/drakon_api';
    $_SERVER['PATH_INFO'] = '/drakon_api';
    
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    
    $response = $kernel->handle(
        $request = Illuminate\Http\Request::capture()
    );
    
    $response->send();
    $kernel->terminate($request, $response);
    
} catch (\Exception $e) {
    // If Laravel fails, log error and return basic response
    @file_put_contents($logFile, "ERROR: " . $e->getMessage() . "\n", FILE_APPEND);
    
    http_response_code(200);
    echo json_encode([
        'status' => true,
        'message' => 'Webhook received',
        'error' => $e->getMessage()
    ]);
}
exit;
