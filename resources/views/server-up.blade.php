<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    {{-- <link rel="icon" href="https://akhdaar.com/wp-content/uploads/2025/02/cropped-01-03-02-32x32.png" sizes="32x32"> --}}
    <title>LARAVEL 11 TEMPLATE</title>
    <style>
        body {
            min-height: 100vh;
            font-family: sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #eee;
        }

        .box {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            text-transform: uppercase;
            color: #333;
            max-width: 90%;
            width: 460px;
            background-color: #ccc;
            text-align: center;
            padding: 20px 16px;
            border-radius: 8px;
            box-shadow: 0px 4px 20px 2px #0087189c;
        }

        h1 {
            position: relative;
            width: fit-content;
        }

        h1::after {
            content: "";
            position: absolute;
            left: -12%;
            top: 50%;
            transform: translateY(-60%);
            width: 10px;
            height: 10px;
            background-color: red;
            border-radius: 50%;
            z-index: 1000;
        }

        h1::before {
            content: "";
            position: absolute;
            left: -15%;
            top: 50%;
            width: 18px;
            height: 18px;
            background-color: #eeeeeea8;
            border: 2px dashed rgb(0, 102, 9);
            border-radius: 50%;
            animation: spin 1.6s infinite ease-in-out;
            animation-direction: alternate-reverse
        }

        @keyframes spin {
            0% {
                transform: translateY(-55%) rotate(0deg);
            }

            100% {
                transform: translateY(-55%) rotate(359deg);
            }
        }
    </style>
</head>

<body>
    <div class="box">
        <h1>System Up</h1>
    </div>
</body>

</html>
