<?php
//Classe de VISTA encarregada de formatejar la sortida de dades

class Vista
{
    public function mostrarError ($missatge)
    {
        echo "<table bgcolor=grey align=center border = 1 cellpadding = 10>";
        echo "<tr><td><br><h2> $missatge </h2><br><br></td></tr>";
        echo "</table>";		
    }

    public function mostrarCapsalera($titol)
    {
        echo ('<!DOCTYPE HTML><html>
                    <head>
                        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                        <title> GESTIÓ DE PISTES DE PÀDEL </title>
                    </head>
                    <body>
                        <center>
                        <br> <h1>' . $titol . '</h1><br><br>');
    }

    public function mostrarMissatge ($missatge)
    {
        echo "<table bgcolor=#ffffb7 align=center border = 1 cellpadding = 10>";
        echo "<tr><td><br><h2> $missatge </h2><br><br></td></tr>";
        echo "</table>";		
    }

    public function mostrarPeu()
    {
        echo ('<br><a href="index.html"> Tornar </a></center></body></html>');
    }

    private function formatData($data)
    {
        //Canvia el format a dia-mes-any hora:minut:segon
        date_default_timezone_set("europe/Madrid");
        $dataOk = date_create($data);
        $dataOk = date_format($dataOk,'d-m-Y H:i:s'); 
        return $dataOk;
    }



 
    public function mostrarLlistatHabitacions ($llistatHabitacions)
    {
        $res="<table border=1><tr bgcolor='lightgray'>
                            <th>codi</th>
                            <th>tipus</th>
                            <th>preuNit</th>
                            <th>descripcio</th>";
                        
        foreach ($llistaSocis as $habitacio)
        {
            $res = $res . "<tr>";
            $codi = $habitacio["codi"];
            $tipus = $habitacio["tipus"];
            $preuNit = $habitacio["preuNit"];
            $descripcio = $habitacio["descripcio"];
            
            $res = $res . "<td>$codi</td>";
            $res = $res . "<td>$tipus</td>";
            $res = $res . "<td>$preuNit</td>";
            $res = $res . "<td>$descripcio</td>";
            $res = $res . "</tr>";
        }
        $res = $res . "</table>";
        echo ($res);
    }

/**************************************************************************************/

}
