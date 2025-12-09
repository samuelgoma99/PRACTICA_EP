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
        $this->numErrors=$this->getNumErrors($DNI); // No m'agrada
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
            return("ERROR: L'usuari NO existeix");
        }
        $bloquejat = $this->usuariBloquejat($DNI);
        if ($bloquejat == TRUE) 
        {
            return("ERROR: L'usuari està bloquejat");
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
                return("ERROR: L'usuari està bloquejat");
            }
        }
        $this->reiniciarErrorsLogin($DNI);
        $this->canviarEstatUsuari($DNI, "Autenticat");
        $res = $this->consultaTipusClient($DNI);
        return($res);

    }

    public function existeixUsuari($DNI)
    {
        $res = FALSE;
        $this->abd->connectarBD();
        $SQL = "select count(*) from usuaris where DNI = '$DNI'";
        $res = $this->abd->consultaUnica($SQL);
        $this->abd-> desconnectarBD();
        return $res;
    }

    function usuariBloquejat($DNI)
    {
        $res = FALSE;
        $this->abd->connectarBD();
        $SQL = "select numErrors from usuaris where DNI = '$DNI'";
        $numErrors = $this->abd->consultaUnica($SQL);
        $this->abd-> desconnectarBD();
        $res = ($numErrors >= 3);
        return($res);

    }

    function comprovarCredencials($DNI, $password)
    {
        $res = FALSE;
        $this->abd->connectarBD();
        $SQL = "select count(*) from usuaris where DNI = '$DNI' and contrasenya = '$password'";
        $count = $this->abd->consultaUnica($SQL);
        $this->abd-> desconnectarBD();
        $res = ($count == 1);
        return $res;
    }

    function incrementarErrorsLogin($DNI)
    {
        $res = FALSE;
        $numErrors = $this->getNumErrors($DNI) + 1;
        $this->abd->connectarBD();
        $SQL = "update usuaris set numErrors = '$numErrors' where DNI = '$DNI'";
        $res = $this->abd->consultaSQL($SQL);
        $this->abd-> desconnectarBD();
        return $res;
    }

    function getNumErrors($DNI) 
    {
        $this->abd->connectarBD();
        $SQL = "select numErrors from usuaris where DNI = '$DNI'";
        $valor = $this->abd->consultaUnica($SQL);
        $this->abd-> desconnectarBD();
        return $valor;
    }

    public function canviarEstatUsuari($DNI, $nouEstat) 
    {
        $res = FALSE;
        $this->abd->connectarBD();
        $SQL = "update usuaris set estat = '$nouEstat' where DNI = '$DNI'";
        $res = $this->abd->consultaSQL($SQL);
        $this->abd-> desconnectarBD();
        return $res;
    }

    public function reiniciarErrorsLogin($DNI) 
    {
        $res = FALSE;
        $this->abd->connectarBD();
        $SQL = "update usuaris set numErrors = 0 where DNI = '$DNI'";
        $res = $this->abd->consultaSQL($SQL);
        $this->abd-> desconnectarBD();
        return $res;
    }

    function consultaTipusClient($DNI) 
    {
        $res = FALSE;
        $this->abd->connectarBD();
        $SQL = "select tipus from usuaris where DNI = '$DNI'";
        $res = $this->abd->consultaUnica($SQL);
        $this->abd->desconnectarBD();
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
        
        $emplenat = $this->comprovarDadesObligatòries($DNI, $address, $password, $password_confirm);
        if ($emplenat == FALSE)
        {
            return("Falten dades obligatories");
        }
        
        $vPass = $this->validarContrasenya($password, $password_confirm);
        if ($vPass == FALSE){
            return "Les contrasenyes no coincideixen";
        }
        
        $vDni = $this->validarDni($DNI);
        if ($vDni == FALSE){
            return "El DNI es invalid";
        }
        
        $vTel = $this->validarTel($tel);
        if ($vTel == FALSE){
            return "El telèfon ha de tenir 9 dígits";
        }
        
        $vEmail = $this->validarEmail($email);
        if ($vEmail == FALSE){
            return "El email no es valid";
        }
        
        return($res);  // Retorna vacío si todo es correcto
    }

    function comprovarDadesObligatòries($DNI,$address,$password,$password_confirm){
        if (empty($DNI) || empty($address) || empty($password) || empty($password_confirm)) {
            return FALSE;
        }
        return TRUE;
    }

    function validarContrasenya($password,$password_confirm){
        if ($password !== $password_confirm) {
            return FALSE;
        }
        return TRUE;
    }

    function validarDni($dni){
        if (!preg_match("/^[0-9]{8}[A-Za-z]$/", $dni)) {
            return FALSE;
        }
        return TRUE;
    }

    function validarTel($tel){
        if (!empty($tel) && !preg_match("/^[0-9]{9}$/", $tel)) {
            return FALSE;
        }
        return TRUE;
    }

    function validarEmail($email){
        if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return FALSE;
        }
        return TRUE;
    }

    public function inserirDadesUsuari($DNI, $password){
        $res = "";
        $this->abd->connectarBD();
        
        try {
            $dni_escapat = $this->abd->escaparDada($DNI);
            $password_escapat = $this->abd->escaparDada($password);
            
            $sql_usuaris = "INSERT INTO USUARIS (DNI, contrasenya, estat, numErrors, tipus) VALUES ('$dni_escapat', '$password_escapat', 'NO AUTENTICAT', 0, 'CLIENT')";
            
            if (!$this->abd->consultaSQL($sql_usuaris)) {
                throw new Exception("Error al registrar usuari: " . $this->abd->missatgeError());
            }
            
        } catch (Exception $e) {
            $res = $e->getMessage();
        }

        $this->abd->desconnectarBD();
        return $res;
    }
    
    public function inserirDadesClient($DNI, $nom, $address, $tel, $email, $foto = ''){
        $res = "";
        $this->abd->connectarBD();
        
        try {
            // Escapar datos para seguridad
            $dni_escapat = $this->abd->escaparDada($DNI);
            $nom_escapat = $this->abd->escaparDada($nom);
            $address_escapat = $this->abd->escaparDada($address);
            $tel_escapat = $this->abd->escaparDada($tel);
            $email_escapat = $this->abd->escaparDada($email);
            $foto_escapat = $this->abd->escaparDada($foto);
            
            // SQL INSERT CON fotografia
            $sql_clients = "INSERT INTO CLIENTS (DNI, nom, adreça, telefon, email, fotografia) VALUES ('$dni_escapat', '$nom_escapat', '$address_escapat', '$tel_escapat', '$email_escapat', '$foto_escapat')";
            
            // Ejecutar SQL
            if (!$this->abd->consultaSQL($sql_clients)) {
                throw new Exception("Error al registrar dades del client: " . $this->abd->missatgeError());
            }
            
        } catch (Exception $e) {
            $res = $e->getMessage();
        }

        $this->abd->desconnectarBD();
        return $res;
    }
}
?>
