<?php

$imagelink = url('floorimages/Forth.jpg' );
//$imagelink = public_path('');


//dd($imagelink)
?>
<!DOCTYPE html>
<html>
<head>
    <title>Forth Floor</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f1f1f1;
        }
        img {
            max-width: 100%;
            max-height: 100%;
        }

    </style>
</head>
<body>

    <img src="{{$imagelink}}" alt="logo">
                    
</body>
</html>
