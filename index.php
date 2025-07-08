<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            text-align: center;
            padding: 50px;
        }
        input {
            padding: 10px;
            font-size: 16px;
            margin: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        button {
            padding: 10px 20px;
            font-size: 16px;
            background-color: blue;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
    </style>
</head>
<body>

    <h2>Ingreso al Sistema</h2>
    
    <form action="send_code.php" method="POST">
        <label for="email">Correo Electrónico:</label>
        <input type="email" id="email" name="email" placeholder="Introduce tu correo" required><br><br>
        <button type="submit">Enviar Código</button>
    </form>

</body>
</html>
