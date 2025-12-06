<?php
//Classe de MODEL encarregada de la gestió de la taula Habitacio de la base de dades
include_once ("taccesbd.php");
class Habitacio
{
    private $codi;
    private $tipus;
    private $preuNit;
    private $descripcio;
    

    private $abd;
   
    
    function __construct()
    {
        $this->abd = new TAccesbd(); 
    
    }

    function __destruct()
    {
        if (isset($this->abd))
        {
        unset($this->abd);
        }
    }

    public function llistatHabitacions()
    {
        $res = array();
        $this->abd->connectarBD();
        $sql = "SELECT codi, tipus, preuNit, descripcio FROM habitacio WHERE codi <> '0'";
    
        if ($this->abd->consultaSQL($sql))
        {
            $fila = $this->abd->consultaFila();
            $i = 0;
            while ($fila != null)
            {
                $res[$i]["codi"] = $this->abd->consultaDada("codi");
                $res[$i]["tipus"] = $this->abd->consultaDada("tipus");
                $res[$i]["preuNit"] = $this->abd->consultaDada("preuNit");
                $res[$i]["descripcio"] = $this->abd->consultaDada("descripcio");
                
                $i++;
                $fila = $this->abd->consultaFila();
            }
            $this->abd->tancarConsulta();
        }
        else
        {
            echo "Error en la consulta: " . $this->abd->missatgeError();
        }
    
        $this->abd->desconnectarBD();
        return $res; 
    }

/*******************************************************************************/

   

}
