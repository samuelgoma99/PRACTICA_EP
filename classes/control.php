<?php
header("Content-Type: text/html;charset=utf-8");

//Classe de CONTROLADOR
include_once ("habitacio.php");
include_once ("reserva.php");

class Control
{
    
    function __construct()
    {
        // Res aquí
    }
    //// CASOS D'US DE PRIMER NIVELL //////
    public function llistatHabitacions()
	{
		$res = "";
		$s = new Habitacio();
		$res = $s->llistatHabitacions();
		return($res);
	}
}
