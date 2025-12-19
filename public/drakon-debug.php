<?php
/**
 * Standalone Drakon Debug Script
 * Access: https://a49000.win/drakon-debug.php
 * 
 * This file bypasses Laravel routing to diagnose webhook issues
 */

header('Content-Type: application/json');

$response = [
    'status' => 'OK',
    'timestamp' => date('Y-m-d H:i:s'),
    'server_info' => [
        'php_version' => PHP_VERSION,
        'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
        'document_root' => $_SERVER['DOCUMENT_ROOT'] ?? 'Unknown',
        'script_filename' => __FILE__,
        'request_uri' => $_SERVER['REQUEST_URI'] ?? 'Unknown',
        'request_method' => $_SERVER['REQUEST_METHOD'] ?? 'Unknown',
        'remote_addr' => $_SERVER['REMOTE_ADDR'] ?? 'Unknown',
        'http_host' => $_SERVER['HTTP_HOST'] ?? 'Unknown',
        'https' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'Yes' : 'No',
    ],
    'laravel_paths' => [
        'base_path' => dirname(__DIR__),
        'public_path' => __DIR__,
        'bootstrap_exists' => file_exists(dirname(__DIR__) . '/bootstrap/app.php'),
        'routes_web_exists' => file_exists(dirname(__DIR__) . '/routes/web.php'),
        'routes_api_exists' => file_exists(dirname(__DIR__) . '/routes/api.php'),
        'controller_exists' => file_exists(dirname(__DIR__) . '/app/Http/Controllers/Api/DrakonController.php'),
        'htaccess_exists' => file_exists(__DIR__ . '/.htaccess'),
        'index_exists' => file_exists(__DIR__ . '/index.php'),
    ],
    'file_permissions' => [
        'public_dir' => substr(sprintf('%o', fileperms(__DIR__)), -4),
        'index_php' => file_exists(__DIR__ . '/index.php') ? substr(sprintf('%o', fileperms(__DIR__ . '/index.php')), -4) : 'N/A',
        'htaccess' => file_exists(__DIR__ . '/.htaccess') ? substr(sprintf('%o', fileperms(__DIR__ . '/.htaccess')), -4) : 'N/A',
    ],
    'test_endpoints' => [
        'this_debug_file' => 'https://' . ($_SERVER['HTTP_HOST'] ?? 'a49000.win') . '/drakon-debug.php',
        'laravel_webhook' => 'https://' . ($_SERVER['HTTP_HOST'] ?? 'a49000.win') . '/drakon_api',
        'laravel_test' => 'https://' . ($_SERVER['HTTP_HOST'] ?? 'a49000.win') . '/drakon_api/test',
        'api_webhook' => 'https://' . ($_SERVER['HTTP_HOST'] ?? 'a49000.win') . '/api/drakon_api',
        'webhook_alt' => 'https://' . ($_SERVER['HTTP_HOST'] ?? 'a49000.win') . '/webhook/drakon',
    ],
    'request_data' => [
        'get' => $_GET,
        'post' => $_POST,
        'raw_input' => file_get_contents('php://input'),
        'headers' => function_exists('getallheaders') ? getallheaders() : 'getallheaders() not available',
    ],
    'htaccess_content' => file_exists(__DIR__ . '/.htaccess') ? file_get_contents(__DIR__ . '/.htaccess') : 'File not found',
];

// Test if we can bootstrap Laravel
try {
    $bootstrapPath = dirname(__DIR__) . '/bootstrap/app.php';
    if (file_exists($bootstrapPath)) {
        require_once dirname(__DIR__) . '/vendor/autoload.php';
        $app = require_once $bootstrapPath;
        
        $response['laravel_bootstrap'] = 'SUCCESS';
        $response['laravel_version'] = $app->version();
        
        // Try to get routes
        try {
            $routes = [];
            foreach ($app->make('router')->getRoutes() as $route) {
                $uri = $route->uri();
                if (strpos($uri, 'drakon') !== false) {
                    $routes[] = [
                        'method' => implode('|', $route->methods()),
                        'uri' => $uri,
                        'action' => $route->getActionName(),
                        'name' => $route->getName(),
                    ];
                }
            }
            $response['registered_routes'] = $routes;
            $response['total_drakon_routes'] = count($routes);
        } catch (\Exception $e) {
            $response['routes_error'] = $e->getMessage();
        }
        
    } else {
        $response['laravel_bootstrap'] = 'FAILED - bootstrap file not found';
    }
} catch (\Exception $e) {
    $response['laravel_bootstrap'] = 'ERROR: ' . $e->getMessage();
    $response['laravel_error_trace'] = $e->getTraceAsString();
}

echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
exit;
