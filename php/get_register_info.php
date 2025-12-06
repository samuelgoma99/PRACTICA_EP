<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Recollida dades del formulari
    $dni = $_POST['DNI'] ?? '';
    $name = $_POST['name'] ?? '';
    $address = $_POST['Address'] ?? '';
    $password = $_POST['Password'] ?? '';
    $password_confirm = $_POST['Password_confirm'] ?? '';
    $tel = $_POST['Tel'] ?? '';
    $email = $_POST['Email'] ?? '';

    $error = null;
    $exito = null;

    // VALIDACIONS
    
    // validar camps obligatoris
    if (empty($dni) || empty($address) || empty($password) || empty($password_confirm)) {
        $error = "Es necessari omplir tots els camps obligatoris";
    }

    // validar DNI
    if ($error === null && !preg_match("/^[0-9]{8}[A-Za-z]$/", $dni)) {
        $error = "El DNI es invalid";
    }

    // validar contrasenyas
    if ($error === null && $password !== $password_confirm) {
        $error = "Les contrasenyes no coincideixen";
    }

    // validar tel
    if ($error === null && !empty($tel) && !preg_match("/^[0-9]{9}$/", $tel)) {
        $error = "El telèfon ha de tenir 9 dígits";
    }

    // validar email
    if ($error === null && !empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "El email no es valid";
    }

    // Dades verificades
    
    if ($error === null) {
        
        // Hashear contrasenya
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $password_hash_truncado = substr($password_hash, 0, 20);

        // Truncar camps per BD
        $nom_truncado = substr($name, 0, 20);
        $address_truncado = substr($address, 0, 20);
        $email_truncado = substr($email, 0, 20);

        $dni_numero = (int)substr($dni, 0, 8);
        $tel_numero = !empty($tel) ? (int)$tel : 0;

        // CONEXIÓN A BD
        $conexion = new mysqli("localhost", "root", "", "EP_PRA1");

        if ($conexion->connect_error) {
            $error = "Error de conexión: " . $conexion->connect_error;
        } else {
            
            $conexion->begin_transaction();

            try {
                
                // VERIFICAR DNI
                $sql_check = "SELECT DNI FROM USUARIS WHERE DNI = ?";
                $stmt_check = $conexion->prepare($sql_check);
                $stmt_check->bind_param("i", $dni_numero);
                $stmt_check->execute();
                $resultado = $stmt_check->get_result();

                if ($resultado->num_rows > 0) {
                    throw new Exception("El DNI ja està registrat");
                }
                $stmt_check->close();

                // INSERTAR EN USUARIS
                $sql_usuaris = "INSERT INTO USUARIS (DNI, contrasenya, estat, numErrors, tipus) 
                                VALUES (?, ?, 'NO AUTENTICAT', 0, 'CLIENT')";
                
                $stmt = $conexion->prepare($sql_usuaris);
                $stmt->bind_param("is", $dni_numero, $password_hash_truncado);

                if (!$stmt->execute()) {
                    throw new Exception("Error al registrar usuari: " . $stmt->error);
                }
                $stmt->close();

                // INSERTAR EN CLIENTS
                $sql_clients = "INSERT INTO CLIENTS (DNI, nom, adreça, telefon, email) 
                               VALUES (?, ?, ?, ?, ?)";
                
                $stmt2 = $conexion->prepare($sql_clients);
                $stmt2->bind_param("isssi", $dni_numero, $nom_truncado, $address_truncado, $tel_numero, $email_truncado);

                if (!$stmt2->execute()) {
                    throw new Exception("Error al registrar dades del client: " . $stmt2->error);
                }
                $stmt2->close();

                // CONFIRMAR
                $conexion->commit();
                $exito = "Usuari registrat correctament";

            } catch (Exception $e) {
                $conexion->rollback();
                $error = $e->getMessage();
            }

            $conexion->close();
        }
    }

    // mostrar resultat
    if ($exito !== null) {
        echo $exito;
    } elseif ($error !== null) {
        echo $error;
    }
}
?>
