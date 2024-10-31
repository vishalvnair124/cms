<?php
session_start();
$status = $_GET['status'] ?? 'unknown';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Account Status</title>
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Background and font styling */
        body {
            background: linear-gradient(135deg, #007bff 0%, #6610f2 100%);
            font-family: Arial, sans-serif;
            color: #333;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
        }

        /* Container styling */
        .status-container {
            max-width: 500px;
            width: 100%;
            padding: 30px;
            border-radius: 8px;
            background-color: #ffffff;
            box-shadow: 0px 6px 20px rgba(0, 0, 0, 0.15);
            text-align: center;
        }

        /* Status header styling */
        .status-header {
            font-size: 1.75rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: #333;
        }

        /* Alert color styling */
        .alert {
            padding: 20px;
            margin-bottom: 1.5rem;
            border-radius: 8px;
            border: 1px solid transparent;
            font-size: 1.1rem;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #842029;
            border-color: #f5c2c7;
        }

        .alert-warning {
            background-color: #fff3cd;
            color: #664d03;
            border-color: #ffecb5;
        }

        .alert-info {
            background-color: #e0f2f1;
            color: #00796b;
            border-color: #b2dfdb;
        }

        /* Link styling */
        a {
            color: #007bff;
            text-decoration: underline;
        }

        a:hover {
            color: #0056b3;
            text-decoration: none;
        }

        /* Animation */
        .fade-in {
            animation: fadeIn 1s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }
    </style>
</head>

<body>
    <div class="status-container fade-in">
        <?php if ($status === 'unauthorized'): ?>
            <div class="alert alert-danger">
                <h4 class="status-header">Unauthorized Access</h4>
                <p>You must be logged in to access this page.</p>
            </div>
        <?php elseif ($status === 'pending'): ?>
            <div class="alert alert-warning">
                <h4 class="status-header">Account Approval Pending</h4>
                <p>Your account requires approval. Please contact support if this issue persists.</p>
            </div>
        <?php elseif ($status === 'removed'): ?>
            <div class="alert alert-danger">
                <h4 class="status-header">Account Removed</h4>
                <p>Your account has been removed from the system. Please contact support for further assistance.</p>
            </div>
        <?php elseif ($status === 'error'): ?>
            <div class="alert alert-danger">
                <h4 class="status-header">Unknown Status</h4>
                <p>There was an error checking your account status. Please try again later.</p>
            </div>
        <?php else: ?>
            <div class="alert alert-info">
                <h4 class="status-header">Unknown Status</h4>
                <p>Your account status is unclear. Please contact support for assistance.</p>
            </div>
        <?php endif; ?>
    </div>
</body>

</html>