<?php
session_start();

if($_SESSION["logged"])
{
    echo '<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
     <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
    <title>Database viewer</title>
</head>
<body>
<div class="container">
<a href="./profile.php"><i class="bi bi-person-circle"></i></a>  <a href="./logout.php"><i class="bi bi-box-arrow-left"></i></a>
    <ul class="list-unstyled">
        <li><a href="employee">Employee list</a></li>
        <li><a href="room">Room list<a/></li>
</ul>
</div></body>';
}else{
    echo "<div class='container'>please <a href='login.php'>login</a></div>";
}
