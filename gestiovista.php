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
		case "Registrar-se":
		{
			// Recollida dades del formulari
			$DNI = isset($_POST['DNI']) ? $_POST['DNI'] : '';
			$nom = isset($_POST['name']) ? $_POST['name'] : '';
			$address = isset($_POST['Address']) ? $_POST['Address'] : '';
			$password = isset($_POST['Password']) ? $_POST['Password'] : '';
			$password_confirm = isset($_POST['Password_confirm']) ? $_POST['Password_confirm'] : '';
			$tel = isset($_POST['Tel']) ? $_POST['Tel'] : '';
			$email = isset($_POST['Email']) ? $_POST['Email'] : '';
			$foto = isset($_POST['foto']) ? $_POST['foto'] : '';

			$c = new Control();
			$res = $c->registrarUsuari($DNI,$nom,$address, $password, $password_confirm, $tel, $email, $foto);
				
			if ($res != ""){
				$v->mostrarCapsalera('');
				$v->mostrarMissatge($res);
				$v->mostrarPeu();				
			}else{
				$v->mostrarCapsalera('');
				$v->mostrarMissatge("Usuari registrat correctament");
				$v->mostrarPeu();	
			}
			break;
		}
    }
}
?>
