<?php
header("Content-Type: text/html;charset=utf-8");
include_once("./classes/control.php");
include_once("./classes/vista.php");

if (isset($_POST["opcio"]))
{
	
	$opcio = $_POST["opcio"];
	$v = new Vista();
	switch ($opcio)
	{			
        case "Inici sessió":
        {
            if (isset($_POST["DNI"]) and isset($_POST["Password"]))
			{
				$DNI = $_POST["DNI"];
				$Password = $_POST["Password"];	
				
				$c = new Control();
				$res = $c->iniciarSessio($DNI, $Password);
				
                # Això segurament s'ha de canviar
				$v->mostrarCapsalera('');
				$v->mostrarMissatge($res);
				$v->mostrarPeu();
			}
			else
			{
				$v->mostrarCapsalera('');
				$v->mostrarMissatge('Falten dades per informar');
				$v->mostrarPeu();
			}
			break;	
        }	
    }
}
?>