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



    //Si el segon paràmetre és TRUE, mostrarà botons de radio per a triar una serie
    public function mostrarLlistatSocis ($llistaSocis, $triar)
    {
        $res="<table border=1><tr bgcolor='lightgray'>
                            <th>DNI</th>
                            <th>Nom</th>
                            <th>Cognom</th>
                            <th>Nivell</th>";
        if ($triar)
        {
            $res = $res . "<th>Seleccionar</th>";
        }
        $res = $res . "</tr> ";
                        
        foreach ($llistaSocis as $soci)
        {
            $res = $res . "<tr>";
            $DNI = $soci["DNI"];
            $nom = $soci["nom"];
            $cognoms = $soci["cognoms"];
            $nivell = $soci["nivell"];
            
            $res = $res . "<td>$DNI</td>";
            $res = $res . "<td>$nom</td>";
            $res = $res . "<td>$cognoms</td>";
            $res = $res . "<td>$nivell</td>";
            if ($triar)
            {
                $res = $res . "<td><input type='radio' name='DNI' value='$DNI'></td>";
            }
        }
        $res = $res . "</table>";
        echo ($res);
    }

/**************************************************************************************/

}