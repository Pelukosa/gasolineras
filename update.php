<?php

include 'conn/conn.php';

$url = 'https://sedeaplicaciones.minetur.gob.es/ServiciosRESTCarburantes/PreciosCarburantes/EstacionesTerrestres/';

$ch = curl_init($url);


curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));


curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$result = curl_exec($ch);
curl_close($ch);

$result = json_decode($result);
$result = (array) $result;
$fecha = (string) $result["Fecha"];
$result = (array) $result["ListaEESSPrecio"];

$trunc = "TRUNCATE TABLE gasolineras";
$conn->query($trunc);
//print("<pre>".print_r($result,true)."</pre>");


$columns = array(
   "POSTAL_CODE",
   "DIRECCION",
   "HORARIO",
   "LATITUD",
   "LOCALIDAD",
   "LONGITUD",
   "MARGEN",
   "MUNICIPIO",
   "PRECIO_BIODIESEL",
   "PRECIO_BIOETANOL",
   "PRECIO_GAS_NATURAL_COMPRIMIDO",
   "PRECIO_GAS_NATURAL_LICUADO",
   "PRECIO_GASES_LICUADOS_PETROLEO",
   "PRECIO_GASOLEO_A",
   "PRECIO_GASOLEO_B",
   "PRECIO_GASOLINA_95",
   "PRECIO_GASOLINA_98",
   "PRECIO_NUEVO_GASOLEO_A",
   "PROVINCIA",
   "REMISION",
   "ROTULO",
   "TIPO_VENTA",
   "PERCENT_BIOETANOL",
   "PERCENT_ESTER_METILICO",
   "ID_EESS",
   "ID_MUNICIPIO",
   "ID_PROVINCIA",
   "ID_CCAA"
);
$col = "" . implode(",", $columns) . "";

foreach ($result as $v) {
   $v->Latitud = str_replace(",", ".", $v->Latitud);
   $lng = "Longitud (WGS84)";
   $v->$lng = str_replace(",", ".", $v->$lng);
   //print("<pre>".print_r($v,true)."</pre>");
   $v = (array) $v;
   $v = array_values($v);
   $val = "'" . implode("','", $v) . "'";
   $sql = "INSERT INTO gasolineras ($col) VALUES ($val)";
   $conn->query($sql);
}
$now = date("Y-m-d H:i:s");
echo $now;
$sqlDate = "UPDATE gasolineras SET FECHA = '$now'";
$conn->query($sqlDate);

$sqlMedia = "SELECT PRECIO_GASOLEO_A FROM gasolineras";
$data = $conn->query($sqlMedia);

$media = 0;
$i = 0;
foreach ($data as $row) {
   if ($row["PRECIO_GASOLEO_A"] != 0 && $row["PRECIO_GASOLEO_A"] != "") {
      $media = floatval($media) + floatval($row["PRECIO_GASOLEO_A"]);
      $i++;
   }
}
$media = $media / $i;
echo "</br>" . $media;
$conn->close();
