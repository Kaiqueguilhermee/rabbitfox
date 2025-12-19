<?php
// Diagnóstico do servidor
header('Content-Type: application/json');

$info = [
    'status' => 'OK',
    'timestamp' => date('Y-m-d H:i:s'),
    'server' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
    'php_version' => PHP_VERSION,
    'document_root' => $_SERVER['DOCUMENT_ROOT'] ?? 'Unknown',
    'script_filename' => $_SERVER['SCRIPT_FILENAME'] ?? 'Unknown',
    'request_uri' => $_SERVER['REQUEST_URI'] ?? 'Unknown',
    'laravel_path' => realpath(__DIR__ . '/../'),
    'routes_web_exists' => file_exists(__DIR__ . '/../routes/web.php'),
    'can_execute_artisan' => is_executable(__DIR__ . '/../artisan'),
];

// Tentar executar artisan route:list
if ($info['can_execute_artisan']) {
    $output = [];
    $return_var = 0;
    exec('cd ' . escapeshellarg(__DIR__ . '/../') . ' && php artisan route:list --path=drakon 2>&1', $output, $return_var);
    $info['routes_drakon'] = implode("\n", $output);
    $info['artisan_exit_code'] = $return_var;
}

echo json_encode($info, JSON_PRETTY_PRINT);
