<?php
require 'session.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Index Page</title>
    <link rel="stylesheet" href="./css/bootstrap.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Anek+Kannada:wght@500&family=Dosis:wght@200&family=PT+Serif+Caption&display=swap" rel="stylesheet">
    
    <style>
        body{
            font-family: 'Anek Kannada', sans-serif;
            font-family: 'Dosis', sans-serif;
            font-family: 'PT Serif Caption', serif;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="row mt-5">
            <div class="card">
                <div class="card-body">
                    <div class="col-md-10">
                        <img src="./images/tud.png" width="200" height="200" alt="logo"class="text-center">
                        <p class="h1 text-info">Welcome to Our Auth Site</p>
                        <p>Lorem ipsum dolor sit amet consectetur, adipisicing elit. Optio dignissimos culpa id explicabo maxime corrupti omnis minima, iusto accusantium. Omnis nemo quasi perspiciatis qui quaerat nostrum alias voluptates quis veniam.</p>
                    </div>
                    <div class="col-md-10 text-end">
                        <p class="h3">Get started:</p>
                        <a href="register.php" class="btn btn-primary btn-lg btn-block">Register</a>
                        <a href="login.php" class="btn btn-secondary btn-lg btn-block">Log In</a>
                    </div>
                </div>
            </div>
           
        </div>
    </div>
    <script src="./js/bootstrap.bundle.min.js"></script>
</body>
</html>
