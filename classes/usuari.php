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
}
?>