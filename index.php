<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inspection App</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            
        }

        .maindiv {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .homediv {
            text-align: center;
            background-color: #ffffff;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        #headinginspection {
            font-size: 36px;
            color: #333;
            margin-bottom: 20px;
        }

        #img1 {
            width: 150px;
            height: 150px;
            margin-bottom: 20px;
        }

        a {
            display: inline-block;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 18px;
            transition: background-color 0.3s ease;
        }

        a:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div class="maindiv">
        <div class="homediv">
            <h1 id="headinginspection">Inspection App</h1>
            <img id="img1" src="https://cdn-icons-png.freepik.com/256/11857/11857349.png?semt=ais_hybrid">
            <br>
            <a href="role.php">Get Started</a>
        </div>
    </div> 
</body>
</html>
