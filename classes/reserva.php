<?php
include_once("taccesbd.php");

class Reserva
{
    private $abd;

    function __construct()
    {
        $this->abd = new TAccesbd();
    }

    function __destruct()
    {
        if (isset($this->abd)) {
            unset($this->abd);
        }
    }

    public function llistatDiariReserves($estat)
    {
        $res = array();
        $this->abd->connectarBD();

        // Escapar el valor para evitar SQL Injection
        $estat = $this->abd->escaparDada($estat);

        // SQL con nombres reales de columnas y tablas
        $sql = "
                SELECT R.numReserva, R.dniCliente, C.nom, R.codiHabitacio,
                    R.dataEntrada, R.dataSortida, R.preuTotal, R.estat,
                    H.tipus, H.preuNit
                FROM reserves R
                INNER JOIN clients C ON C.DNI = R.dniCliente
                INNER JOIN HABITACIO H ON R.codiHabitacio = H.codi
                WHERE R.estat = '$estat'
            ";


        if ($this->abd->consultaSQL($sql)) {

            $i = 0;
            while ($this->abd->consultaFila() != null) {

                $res[$i]["numReserva"]    = $this->abd->consultaDada("numReserva");
                $res[$i]["dniCliente"]    = $this->abd->consultaDada("dniCliente");
                $res[$i]["nom"]           = $this->abd->consultaDada("nom");
                $res[$i]["codiHabitacio"] = $this->abd->consultaDada("codiHabitacio");
                $res[$i]["tipus"]         = $this->abd->consultaDada("tipus");
                $res[$i]["preuNit"]       = $this->abd->consultaDada("preuNit");
                $res[$i]["dataEntrada"]   = $this->abd->consultaDada("dataEntrada");
                $res[$i]["dataSortida"]   = $this->abd->consultaDada("dataSortida");
                $res[$i]["preuTotal"]     = $this->abd->consultaDada("preuTotal");
                $res[$i]["estat"]         = $this->abd->consultaDada("estat");

                $i++;
            }


            $this->abd->tancarConsulta();
        }

        $this->abd->desconnectarBD();
        return $res;
    }

    public function checkinReserva($numReserva)
    {
        $this->abd->connectarBD();
        $numReserva = $this->abd->escaparDada($numReserva);

        // Depuración
        echo "Número de reserva recibido: $numReserva<br>";

        $sql = "UPDATE reserves SET estat = 'OCUPADA' WHERE numReserva = $numReserva";
        echo "SQL ejecutado: $sql<br>";

        $res = $this->abd->consultaSQL($sql);

        if (!$res) {
            $error = $this->abd->missatgeError();
            $this->abd->desconnectarBD();
            return "Error al realitzar el check-in: $error";
        }

        $filas = $this->abd->filesAfectades();
        $this->abd->desconnectarBD();

        if ($filas > 0) {
            return "Check-in realitzat correctament per la reserva número $numReserva.";
        } else {
            return "No s'ha pogut realitzar el check-in: la reserva pot no existir o ja està ocupada.";
        }
    }

}
?>
