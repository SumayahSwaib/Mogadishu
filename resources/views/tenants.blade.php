

<!DOCTYPE html>
<html>
<head>
    <title>Image Display</title>
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
    <img class="text-center img img-fluid rounded-circle" style="border-radius: 50%;"
                    src="{{ url('assets/logo-1.jpg') }}" alt="logo">
</body>
</html>