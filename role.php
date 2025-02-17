<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Role Selection</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
        }

        .container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            flex-direction: column;
        }

        h1 {
            font-size: 36px;
            color: #333;
            margin-bottom: 40px;
        }

        .buttons {
            display: flex;
            gap: 20px;
        }

        .buttons a {
            display: inline-block;
            padding: 15px 30px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 18px;
            transition: background-color 0.3s ease;
        }

        .buttons a:hover {
            background-color: #45a049;
        }

        .buttons a.admin {
            background-color: #007BFF;
        }

        .buttons a.admin:hover {
            background-color: #0069d9;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Who are You?</h1>
        <div class="buttons">
            <a href="login.php" class="admin">Admin</a>
            <a href="inspectorlogin.php" class="inspector">Inspector</a>
        </div>
    </div>
</body>
</html>
