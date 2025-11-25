<?php
header("Content-Type: text/html;charset=utf-8");

if (isset($_POST["opcio"]))
{
	$opcio = $_POST["opcio"];
	switch ($opcio)
	{
		case "llistarHabitacions":
			include_once("llistarHabitacions.html");
			break;
            
		case "iniciSessio":
			include_once("inicisessio.html");
			break;

		case "registrarUsuari":
			include_once("registrarusuari.html");
			break;

		default:
			echo "<br>ERROR: Opció no disponible<br>";
	}
}
?>