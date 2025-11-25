<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // recollida dades
    $dni = $_POST['DNI'] ?? '';
    $address = $_POST['Address'] ?? '';
    $password = $_POST['Password'] ?? '';
    $password_confirm = $_POST['Password_confirm'] ?? '';
    $tel = $_POST['Tel'] ?? '';
    $email = $_POST['Email'] ?? '';

    // comprobacións
    if ($password !== $password_confirm) {
        $error = "Les contrasenyes no coincideixen";
    }

    if (!preg_match("/^[0-9]{8}[A-Za-z]$/", $dni)) {
    $error = "El DNI es invalid";
    }

    // comprobacions adicionals (en principi no es necesari pero revisem per seguretat)
    if (empty($dni) || empty($address) || empty($password) || empty($password_confirm)) {
        $error = "Es necessari omplir tots els camps obligatoris";
    }

    // hashear contrasenya
    if (!isset($error)) {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
    }

    // enviar a BD

    // confirmació
    if (!isset($error)) {
        echo "Usuari registrat correctament";
    } else {
        echo $error;
    }
}
?>
