<?php
header("Content-Type: text/html;charset=utf-8");

//Classe de CONTROLADOR
include_once ("habitacio.php");
include_once ("usuari.php");

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
    public function iniciarSessio($DNI, $password)
    {
        $res = "";
        $s = new Usuari($DNI);
        $res = $s->iniciarSessio($DNI, $password);
        return($res);
    }
    public function registrarUsuari($DNI,$nom,$address, $password, $password_confirm, $tel, $email, $foto){
        $res = "";
        $s = new Usuari($DNI);
        $res = $s->registrarUsuari($DNI,$address, $password, $password_confirm, $tel, $email);
        if ($res != "") return($res);
        
    
        $res = $s->inserirDadesUsuari($DNI, $password);
        if ($res != "") return($res);
        $res = $s->inserirDadesClient($DNI,$nom,$address, $tel, $email, $foto);
        return($res);
    }

    public function ferReserva($codiHabitacio, $dataInici, $dataFi, $DNIClient){
        $res = "";
        $s = new Reserva($codiHabitacio, $dataInici, $dataFi, $DNIClient);
        $res = $s->ferReserva($codiHabitacio, $dataInici, $dataFi, $DNIClient);
        return($res);
    }
}
?>