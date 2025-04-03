<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UAC Inspection App</title>

    <!-- PWA Support -->
    <link rel="manifest" href="/uacinspectionapp/manifest.json">
    <meta name="theme-color" content="#007bff">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="apple-mobile-web-app-title" content="UAC Inspect">
    <link rel="apple-touch-icon" href="/uacinspectionapp/assets/icons/icon-152x152.png">

    <!-- CSS Files -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="/uacinspectionapp/assets/css/style.css">

    <!-- JavaScript Files -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="/uacinspectionapp/assets/js/main.js" defer></script>
    <script src="/uacinspectionapp/assets/js/install-prompt.js" defer></script>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="/uacinspectionapp/">
                <img src="/uacinspectionapp/assets/icons/logo.png" width="30" height="30" class="d-inline-block align-top mr-2" alt="UAC Logo">
                UAC Inspection
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <?php if($_SESSION['role'] === 'admin'): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="/uacinspectionapp/admin/dashboard.php">
                                    <i class="fas fa-tachometer-alt"></i> Dashboard
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="/uacinspectionapp/admin/manage_inspections.php">
                                    <i class="fas fa-clipboard-list"></i> Inspections
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="/uacinspectionapp/admin/manage_inspectors.php">
                                    <i class="fas fa-users"></i> Inspectors
                                </a>
                            </li>
                        <?php elseif($_SESSION['role'] === 'inspector'): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="/uacinspectionapp/inspector/dashboard.php">
                                    <i class="fas fa-tachometer-alt"></i> Dashboard
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="/uacinspectionapp/inspector/dashboard.php">
                                    <i class="fas fa-clipboard-check"></i> My Inspections
                                </a>
                            </li>
                        <?php endif; ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/uacinspectionapp/auth/logout.php">
                                <i class="fas fa-sign-out-alt"></i> Logout
                            </a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/uacinspectionapp/auth/login.php">
                                <i class="fas fa-sign-in-alt"></i> Login
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>