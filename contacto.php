<?php
    include('configuracion.php');

    $mensajeConfirmacion = "";
    if(isset($_POST["enviar"])){
        $nombre = trim($_POST["nombre"] ?? "");
        $apellido = trim($_POST["apellido"] ?? "");
        $correo = trim($_POST["correo"] ?? "");
        $mensaje = trim($_POST["mensaje"] ?? "");

        if($nombre === "" || $apellido === "" || $correo === "" || $mensaje === ""){
            $mensajeConfirmacion = "Por favor completa todos los campos.";
        }
        elseif(str_word_count($mensaje) > 1500){
            $mensajeConfirmacion = "El mensaje no puede tener más de 1500 palabras.";
        } 
        else {
            $secretKey = "6Lf6BBQsAAAAAA8Pt9-L_0teRgsuGN7gkPJmdJZU";
            $captcha = $_POST["g-recaptcha-response"] ?? "";

            if(!$captcha){ 
                $mensajeConfirmacion = "Porfavor Verifica el Captcha.";
            } else {
                $url='https://www.google.com/recaptcha/api/siteverify';
                $data=[
                    'secret'=>$secretKey,
                    'response'=>$captcha
                ];
                $options=[
                    'http'=>[
                        'method'=>'POST',
                        'header'=>"Content-type:application/x-www-form-urlencoded\r\n",
                        'content'=>http_build_query($data)
                    ]
                ];
                $context=stream_context_create($options);
                $response=file_get_contents($url, false, $context);
                $responseKeys=json_decode($response, true);

                if(!$responseKeys["success"]){
                    $mensajeConfirmacion = "Error: Captcha Inválido.";
                } else {

                    $stmt = $conexion->prepare("INSERT INTO usuarios(nombre, apellido, correo, mensaje) VALUES (?, ?, ?, ?)");
                    $stmt->bind_param("ssss", $nombre, $apellido, $correo, $mensaje);
                    if($stmt->execute()){
                        $mensajeConfirmacion = "Gracias por tu mensaje, $nombre. ¡Se envió correctamente!";
                    } else {
                        $mensajeConfirmacion = "Error al enviar.";
                    }

                    $stmt->close();
                    
                }
            }
        }
    }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <link rel="stylesheet" href="styles.css">
    <title>Contacto</title>
</head>
<body>
    <header>
        <nav class="navbar navbar-expand-lg navbar-custom fixed-top">
            <div class="container">
                <img class="me-2" src="imagenes/imagen1.png" alt="Logo del Sitio">
                <h5>
                    <strong>Mi Sitio Personal</strong>
                </h5>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto fw-bold">
                        <li class="nav-item"> <a class="nav-link fw-bold" href="index.html">Inicio</a></li>
                        <li class="nav-item"><a class="nav-link fw-bold" href="contacto.php">Contacto</a></li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <div class="container mt-5 pt-4">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="form-card">
                    <h3 class="titulo">
                        <img src="imagenes/imagen3.jpg" alt="Imagen de Contactos">
                        Contáctame
                    </h3>

                    <?php if($mensajeConfirmacion !== ""): ?>
                        <div class="alert alert-info mt-3"><?=$mensajeConfirmacion ?></div>
                    <?php endif; ?>

                    <form action="" method="POST">
                        <label class="form-label" for="nombre">Nombre:</label>
                        <input class="form-control" type="text" name="nombre" required>

                        <label class="form-label mt-3" for="apellido">Apellido:</label>
                        <input class="form-control" type="text" name="apellido" required>

                        <label class="form-label mt-3" for="correo">Correo:</label>
                        <input class="form-control" type="email" name="correo" required>

                        <label class="form-label mt-3" for="mensaje">Mensaje:</label>
                        <textarea name="mensaje" class="form-control" rows="5" required></textarea>

                        <div>
                            <div class="g-recaptcha" data-sitekey="6Lf6BBQsAAAAAKnlRAikcVO72AooBmqTDL142_Kw"></div>
                        </div>

                        <button type="submit" name="enviar" id="entrar" class="btn btn-success w-100 mt-4">Enviar</button>
                    </form>
                </div>
            </div> 
        </div>
    </div>
    <footer class="footer-custom text-center text-dark py-3 mt-5">
        <p><strong>@Aagudelo - Mi Sitio Personal 2025</strong></p>
    </footer>
</body>
</html>