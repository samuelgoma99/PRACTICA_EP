<?php
//Classe de MODEL encarregada de la gestió de la taula USUARI de la base de dades
include_once ("taccesbd.php");
class Usuari
{
    private $DNI;
    private $password;
    private $numErrors;
    private $estat;
    private $tipus;
    

    private $abd;
   
    
    function __construct($DNI = null)
    {
        $this->abd = new TAccesbd(); 
        $this->numErrors=0;

        # REVISAR!
        if ($DNI != null) {
            $this->DNI = $DNI;
            $this->abd->connectarBD();
            $SQL = "select numErrors from usuaris where DNI = '$DNI'";
            $valor = $this->abd->consultaUnica($SQL);
            if ($valor !== null) {
                $this->numErrors = $valor;
            }
        }
    
    }

    function __destruct()
    {
        if (isset($this->abd))
        {
        unset($this->abd);
        }
    }

    public function iniciarSessio($DNI, $password)
    {
        $res = "";
        $existeix = $this->existeixUsuari($DNI);
        if ($existeix == FALSE)
        {
            return("L'usuari NO existeix");
        }
        $bloquejat = $this->usuariBloquejat($DNI);
        if ($bloquejat == TRUE) 
        {
            return("L'usuari està bloquejat");
        }
        $credencialsCoindiexen = $this->comprovarCredencials($DNI, $password);
        if ($credencialsCoindiexen == FALSE)
        {
            $this->numErrors += 1;
            $this->incrementarErrorsLogin($DNI);
            if ($this->numErrors < 3) {
                $intentsRestants = 3 - $this->numErrors;
                return ("ERROR: Login incorrecte: Queden '$intentsRestants' intents");
            } else 
            {
                $this->canviarEstatUsuari($DNI, "Bloquejat");
                return ("ERROR: Usuari bloquejat");
            }
        }
        $this->reiniciarErrorsLogin($DNI);
        $this->canviarEstatUsuari($DNI, "Autenticat");
        $res = $this->consultaTipusClient($DNI);
        return($res);

    }

    // SUB!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
    function existeixUsuari($DNI)
    {
        $res = FALSE;
        $this->abd->connectarBD();
        $SQL = "select count(*) from usuaris where DNI = '$DNI'";
        $res = $this->abd->consultaUnica($SQL);
        return $res;
    }

    function usuariBloquejat($DNI)
    {
        $res = FALSE;
        $this->abd->connectarBD();
        $SQL = "select numErrors from usuaris where DNI = '$DNI'";
        $numErrors = $this->abd->consultaUnica($SQL);
        $res = ($numErrors >= 3);
        return($res);

    }

    function comprovarCredencials($DNI, $password)
    {
        $res = FALSE;
        $this->abd->connectarBD();
        $SQL = "select count(*) from usuaris where DNI = '$DNI' and contrasenya = '$password'";
        $count = $this->abd->consultaUnica($SQL);
        $res = ($count == 1);
        return $res;
    }

    function incrementarErrorsLogin($DNI)
    {
        $res = FALSE;
        $this->abd->connectarBD();
        $numErrors = $this->numErrors;
        $SQL = "update usuaris set numErrors = '$numErrors' where DNI = '$DNI'";
        $res = $this->abd->consultaSQL($SQL);
        return $res;
    }

    // SUB!!!!!!!!!!!!!!!!!!!!!
    function canviarEstatUsuari($DNI, $nouEstat) 
    {
        $res = TRUE;
        return $res;
    }

    // SUB !!!!!!!!!!!!!!!!!!!!!!!!!!!!
    function reiniciarErrorsLogin($DNI) 
    {
        $res = TRUE;
        return $res;
    }

    function consultaTipusClient($DNI) 
    {
        $res = FALSE;
        $this->abd->connectarBD();
        $SQL = "select tipus from clients where DNI = '$DNI'";
        $res = $this->abd->consultaUnica($SQL);
        return $res;
    }

        // Registre ------------------------------

    public function registrarUsuari($DNI,$address, $password, $password_confirm, $tel, $email){
        $res = "";
        $existeix = $this->existeixUsuari($DNI);
        if ($existeix != FALSE)
        {
            return("L'usuari ja existeix");
        }
        $emplenat = $this->comprovarDadesObligatòries($DNI,$address,$Password,$Password_confirm);
        if ($emplenat == FALSE)
        {
            return("Falten dades obligatories");
        }
        $vPass = $this->validarContrasenya($password,$password_confirm);
        if ($vPass == FALSE){
            return "Les contrasenyes no coincideixen";
        }
        $vDni = $this->validarDni($dni);
        if ($vDni == FALSE){
            return "El DNI es invalid"
        }
        $vTel = $this->validarTel($tel);
        if ($vTel == FALSE){
            return "El telèfon ha de tenir 9 dígits"
        }
        $vEmail = $this->validarEmail($email);
        if ($vEmail == FALSE){
            return "El email no es valid"
        }
        return($res);
    }

    function comprovarDadesObligatòries($DNI,$address,$password,$password_confirm){
        if (empty($dni) || empty($address) || empty($password) || empty($password_confirm)) {
            $res = FALSE;
        }
        return $res;
    }

    function validarContrasenya($password,$password_confirm){
        if ($password !== $password_confirm) {
            $res = FALSE;
        }
        return $res;
    }

    function validarDni($dni){
        if (!preg_match("/^[0-9]{8}[A-Za-z]$/", $dni)) {
            $res = FALSE;
        }
        return $res;     
    }

    function validarTel($tel){
        if (!empty($tel) && !preg_match("/^[0-9]{9}$/", $tel)) {
            $res = FALSE;
        }
        return $res;
    }

    function validarEmail($email){
        if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $res = FALSE;
        }
        return $res;
    }

    public function inserirDadesUsuari($DNI, $password){
        $conexion->begin_transaction();
        try {
            $sql_usuaris = "INSERT INTO USUARIS (DNI, contrasenya, estat, numErrors, tipus) VALUES (?, ?, 'NO AUTENTICAT', 0, 'CLIENT')";
            
            $stmt = $conexion->prepare($sql_usuaris);
            $stmt->bind_param("is", $dni, $password);

            if (!$stmt->execute()) {
                throw new Exception("Error al registrar usuari: " . $stmt->error);
            }
            $stmt->close();
        }catch (Exception $e) {
            $conexion->rollback();
            $error = $e->getMessage();
        }

        $conexion->close();
    }
    
    public function inserirDadesClient($DNI,$nom,$address, $password, $tel, $email){
        try {
            // INSERTAR EN CLIENTS
            $sql_clients = "INSERT INTO CLIENTS (DNI, nom, adreça, telefon, email) VALUES (?, ?, ?, ?, ?)";
            
            $stmt2 = $conexion->prepare($DNI,$nom,$address, $password, $tel, $email);
            $stmt2->bind_param("isssi", $DNI, $nom, $address, $tel, $email);

            if (!$stmt2->execute()) {
                throw new Exception("Error al registrar dades del client: " . $stmt2->error);
            }
            $stmt2->close(); 
            $conexion->begin_transaction();
        }catch (Exception $e) {
            $conexion->rollback();
            return $e->getMessage();
        }

        $conexion->close();
        return $res
    }

}
?>