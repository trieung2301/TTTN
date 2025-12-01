<?php 
if (!function_exists('isActive')) {
    function isActive($actionName, $currentAction) {
        return $actionName === $currentAction ? 'active' : '';
    }
}
$current_action = $_GET['action'] ?? 'homeAdmin'; 
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        body { min-height: 100vh; display: flex; flex-direction: column; }
        .wrapper { display: flex; flex: 1; }
        .sidebar { width: 250px; background-color: #343a40; min-height: 100vh; position: sticky; top: 0; }
        .main-content { flex: 1; padding: 20px; }
        .sidebar .nav-link { color: rgba(255, 255, 255, 0.75); }
        .sidebar .nav-link.active { color: #fff; background-color: rgba(255, 255, 255, 0.1); }
    </style>
</head>
<body>