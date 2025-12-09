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
				$res = $c->iniciarSessio($DNI, $Password); // rebrà un string amb el tipus o un string amb l'error
				
				if ($res == "ADMINISTRADOR")
				{
					include_once("sessioIniciadaAdmin.html");
				} else if ($res == "CLIENT")
				{
					include_once("sessioIniciadaClient.html");
				}
				else{
					$v->mostrarCapsalera("");
					$v->mostrarError($res);
					$v->mostrarPeu();
				}
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