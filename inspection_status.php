<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status Selection</title>
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
            text-decoration: none;
            color: white;
            border-radius: 5px;
            font-size: 18px;
            transition: background-color 0.3s ease;
        }

        /* Button colors */
        .buttons a.new {
            background-color: #4CAF50; /* Green */
        }

        .buttons a.pending {
            background-color: #FFA500; /* Orange */
        }

        .buttons a.completed {
            background-color: #007BFF; /* Blue */
        }

        /* Hover effects */
        .buttons a:hover.new {
            background-color: #45a049;
        }

        .buttons a:hover.pending {
            background-color: #e69500;
        }

        .buttons a:hover.completed {
            background-color: #0069d9;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Select Status</h1>
        <div class="buttons">
            <a href="newinspection.php" class="new">New</a>
            <a href="pending.php" class="pending">Pending</a>
            <a href="completed.php" class="completed">Completed</a>
        </div>
    </div>
</body>
</html>
