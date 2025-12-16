<?php
header("Content-Type: text/html;charset=utf-8");

if (isset($_POST["opcio"]))
{
	$opcio = $_POST["opcio"];
	switch ($opcio)
	{
		case "checkin":
			include_once("checkin.html");
			break;           
		case "checkout":
			include_once("checkout.html");
			break;
		default:
			echo "<br>ERROR: Opció no disponible<br>";
	}
}
?>