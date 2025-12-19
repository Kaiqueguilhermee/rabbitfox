<?php
/**
 * Drakon Webhook Standalone Test
 * URL: https://a49000.win/drakon_api_test.php
 * 
 * Este arquivo testa se o servidor consegue processar requisições Drakon
 * sem depender do Laravel routing
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Log request for debugging
$logFile = dirname(__DIR__) . '/storage/logs/drakon-direct.log';
$logDir = dirname($logFile);

if (!is_dir($logDir)) {
    @mkdir($logDir, 0755, true);
}

$logEntry = [
    'timestamp' => date('Y-m-d H:i:s'),
    'method' => $_SERVER['REQUEST_METHOD'] ?? 'UNKNOWN',
    'remote_addr' => $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN',
    'request_uri' => $_SERVER['REQUEST_URI'] ?? 'UNKNOWN',
    'get' => $_GET,
    'post' => $_POST,
    'raw_body' => file_get_contents('php://input'),
    'headers' => function_exists('getallheaders') ? getallheaders() : [],
];

@file_put_contents($logFile, json_encode($logEntry, JSON_PRETTY_PRINT) . "\n\n", FILE_APPEND);

// Parse request
$method = $_POST['method'] ?? $_GET['method'] ?? null;

// Test response
$response = [
    'test_file' => 'drakon_api_test.php',
    'status' => 'OK',
    'message' => 'Standalone test endpoint is working',
    'timestamp' => date('Y-m-d H:i:s'),
    'received_method' => $method,
    'request_logged' => file_exists($logFile),
    'server_info' => [
        'php_version' => PHP_VERSION,
        'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
        'document_root' => $_SERVER['DOCUMENT_ROOT'] ?? 'Unknown',
        'remote_addr' => $_SERVER['REMOTE_ADDR'] ?? 'Unknown',
    ],
];

// If method is provided, simulate webhook response
if ($method) {
    switch ($method) {
        case 'account_details':
            $response = [
                'user_id' => '1',
                'email' => 'test@example.com',
                'name_jogador' => 'Test User'
            ];
            break;
            
        case 'user_balance':
            $response = [
                'status' => 1,
                'balance' => 100.00
            ];
            break;
            
        case 'transaction_bet':
        case 'transaction_win':
            $response = [
                'status' => 1,
                'balance' => 95.50
            ];
            break;
            
        case 'refund':
        case 'cancel':
            $response = [
                'status' => true,
                'balance' => 100.00
            ];
            break;
            
        default:
            $response = [
                'status' => false,
                'error' => 'INVALID_METHOD',
                'received_method' => $method
            ];
    }
}

http_response_code(200);
echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
exit;
