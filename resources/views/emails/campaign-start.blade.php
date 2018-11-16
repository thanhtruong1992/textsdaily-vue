<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <style>
        h3, p {
            padding: 0;
            margin: 0;
        }
        .bg-email {
            background-color: #8DBD6A;
            width: 100%;
            min-height: 100px;
            display: flex;
        }
        .popup {
            width: 450px;
            min-height: 150px;
            background-color: #fff;
            text-align: center;
            margin: auto;
            margin-top: 25px;
            margin-bottom: 25px;
        }
        .campaign-name {
            font-size: 18px;
            font-family: Arial;
            color: #555;
            padding: 20px 0;
        }
        @media screen and (max-width: 900px) {
            .popup {
                width: 400px;
                min-height: 150px;
                background-color: #fff;
                text-align: center;
                margin: auto;
                margin-top: 25px;
                margin-bottom: 25px;
            }
        }
        @media screen and (max-width: 768px) {
            .popup {
                width: 350px;
                min-height: 150px;
                background-color: #fff;
                text-align: center;
                margin: auto;
                margin-top: 25px;
                margin-bottom: 25px;
            }
        }
        @media screen and (max-width: 667px) {
            .popup {
                width: 300px;
                min-height: 150px;
                background-color: #fff;
                text-align: center;
                margin: auto;
                margin-top: 25px;
                margin-bottom: 25px;
            }
        }
        @media screen and (max-width: 480px) {
            .popup {
                width: 290px;
                min-height: 150px;
                background-color: #fff;
                text-align: center;
                margin: auto;
                margin-top: 25px;
                margin-bottom: 25px;
            }
        }
        @media screen and (max-width: 320px) {
            .popup {
                width: 260px;
                min-height: 150px;
                background-color: #fff;
                text-align: center;
                margin: auto;
                margin-top: 25px;
                margin-bottom: 25px;
            }
        }
    </style>
</head>
<body>
    <div class="bg-email">
        <div class="popup">
            <h3 class="campaign-name">{{ $object->name }}</h3>
            <p>
                There has been status change for your campaign: <br />
                {{ $object->name }}
            </p>
            <p style="margin-bottom: 25px;">
                <strong>{{ $object->content }}</strong>
            </p>
        </div>
    </div>
</body>
</html>
