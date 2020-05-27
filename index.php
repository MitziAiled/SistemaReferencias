<!-----------------------------------------------------------------------------------------------------------------------------------
                                                            LÓGICA BACKEND
------------------------------------------------------------------------------------------------------------------------------------>
<?php
session_start();

if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    header("location: src/dashboard.php");
    exit;
}

require_once "config.php";

$correo_electronico = $password = "";
$correo_electronico_err = $password_err = "";

if($_SERVER["REQUEST_METHOD"] == "POST"){
    /********************************************************************************************
     *                              VALIDA CREDENCIALES INGRESADAS
     *******************************************************************************************/
    if(empty(trim($_POST["correo_electronico"]))){
        $correo_electronico_err = "Por favor ingresa tu correo electrónico.";
    } else{
        $correo_electronico = trim($_POST["correo_electronico"]);
    }
    
    if(empty(trim($_POST["password"]))){
        $password_err = "Por favor ingresa tu contraseña.";
    } else{
        $password = trim($_POST["password"]);
    }
    
    /********************************************************************************************
     *                                      LOGGEA A USUARIO
     *******************************************************************************************/
    if(empty($correo_electronico_err) && empty($password_err)){
        $correo_electronico_inicio = substr(trim($_POST["correo_electronico"]), 0, strpos(trim($_POST["correo_electronico"]), '@'));
        $sql = "SELECT u.*, ru.rol_id FROM usuario AS u INNER JOIN rol_usuario AS ru WHERE u.usuario_id = ru.usuario_id AND u.estado = 1  AND u.correo_electronico LIKE '%".$correo_electronico_inicio."%' LIMIT 1";

        if($stmt = $pdo->prepare($sql)){
            if($stmt->execute()){
                if($stmt->rowCount() == 1){
                    if($row = $stmt->fetch()){
                        $usuario_id = $row["usuario_id"];
                        $nombre = $row["nombre"];
                        $primer_apellido = $row["primer_apellido"];
                        $segundo_apellido = $row["segundo_apellido"];
                        $rol_id = $row["rol_id"];
                        $sexo = $row["sexo"];
                        $hashed_password = password_hash($row["password"], PASSWORD_BCRYPT);

                        $dominio = substr(trim($_POST["correo_electronico"]), strpos(trim($_POST["correo_electronico"]), "@") + 1);
                        if ($dominio == "itleon.edu.mx" || $dominio == "leon.tecnm.mx") {
                            
                            $correo_electronico = $row["correo_electronico"];
                            if(password_verify($password, $hashed_password)){
                                session_start();
                                
                                $_SESSION["loggedin"] = true;
                                $_SESSION["usuario_id"] = $usuario_id;
                                $_SESSION["correo_electronico"] = $correo_electronico;
                                $_SESSION["rol_id"] = $rol_id;
                                $_SESSION["sexo"] = $sexo;
    
                                if (!empty($segundo_apellido)){
                                    $nombre_completo = $nombre." ".$primer_apellido." ".$segundo_apellido;
                                } else {
                                    $nombre_completo = $nombre." ".$primer_apellido;
                                }
                                $_SESSION["nombre_completo"] = $nombre_completo;
    
                                header("location: src/dashboard.php");
                            } else{
                                $password_err = "La contraseña ingresada no es valida.";
                            }
                        } else {
                            $correo_electronico_err = "No se encontró una cuenta registrada con ese correo.";
                        }
                    }
                } else{
                    $correo_electronico_err = "No se encontró una cuenta registrada con ese correo.";
                }
            } else{
                alert("El proceso no se pudo ejecutar. Intenta más tarde.");
            }
            unset($stmt);
        }
    }
    unset($pdo);
}
?>

<!-----------------------------------------------------------------------------------------------------------------------------------
                                                            LÓGICA FRONTEND
------------------------------------------------------------------------------------------------------------------------------------>
<!DOCTYPE html>
<html lang="es">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <!-- Fonts and icons -->
    <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700|Roboto+Slab:400,700|Material+Icons" />
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css">
    <!-- Favicon -->
    <link rel="icon" href="static/favicon.ico">
    <!-- Material Kit CSS -->
    <link href="assets/css/material-dashboard.css?v=2.1.2" rel="stylesheet" />

    <style>
        body {
            background-image: url('static/bg-login.jpeg');
            background-repeat: no-repeat;
            background-attachment: fixed;
            background-size: cover;
            height:100%;
        }
    </style>
    
    <title>Inicio de sesión</title>
</head>

<body>
    <nav class="navbar navbar-expand-sm bg-primary sticky-top">
        <div class="container">
            <a class="navbar-brand" href="index.php">Referencias</a>
            <div class="collapse navbar-collapse" id="navbarNavDropdown">
                <ul class="navbar-nav">
                    <li class="nav-item active">
                        <a class="nav-link" href="index.php">Inicio de sesión<span class="sr-only">(current)</span></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="src/registro.php">Registro</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="src/referencias/generar_ref_ext.php">Generador de referencia</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container h-100">
        <div class="row h-100 justify-content-center align-items-center">
            <div class="col-5 shadow-lg p-3 bg-white rounded">
                <div>
                    <img style="display:block; margin-left:auto; margin-right:auto;pointer-events:none;" src="static/icon-login.png">
                    <h2 class="h2 text-center">Inicio de sesión</h4>
                    <p class="text-center">Por favor ingresa tus datos para iniciar sesión.</p>
                </div>
                <div class="card-body">
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                        <div class="form-row justify-content-around">
                            <div class="form-group col-md-10 <?php echo (!empty($correo_electronico_err)) ? : ''; ?>">
                                <label>Correo electrónico</label>
                                <input type="text" name="correo_electronico" class="form-control" value="<?php echo $correo_electronico; ?>">
                                <?php
                                if(!empty($correo_electronico_err)){?>
                                  <small style="padding:5px;" class="alert alert-danger" role="alert"><?php echo $correo_electronico_err; ?></small>
                                <?php }
                                ?>
                            </div>
                        </div>
                        <div class="form-row justify-content-around">
                            <div class="form-group col-md-10 <?php echo (!empty($password_err)) ? : ''; ?>">
                                <label>Contraseña</label>
                                <input type="password" name="password" class="form-control">
                                <?php
                                if(!empty($password_err)){?>
                                  <small style="padding:5px;" class="alert alert-danger" role="alert"><?php echo $password_err; ?></small>
                                <?php }
                                ?>
                            </div>
                        </div>
                        <div class="form-row justify-content-around">
                            <div class="form-group text-center col-md-6 col-xs-4">
                                <input style="white-space:normal;" type="submit" class="btn btn-primary btn-block" value="Iniciar sesión">
                            </div>
                        </div>
                        <p class="text-center">¿No tienes cuenta? <a href="src/registro.php">Regístrate</a>.</p>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>