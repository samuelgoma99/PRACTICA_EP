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
        if ($this->abd->consultaSQL("SELECT codi, tipus, preuNit, descripcio FROM Habitacions where codi <> '0'"))
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
        $this->abd->desconnectarBD();
        return $res; 
    }
/*******************************************************************************/

   

}
