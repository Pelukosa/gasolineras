<!DOCTYPE html>
<html lang="es">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <meta http-equiv="X-UA-Compatible" content="ie=edge">
   <title>Document</title>
</head>

<body>
   <?php

   $servername = "127.0.0.1:3307";
   $username = "root";
   $password = "";
   $dbname = "gasolineras";

   // Create connection
   $conn = new mysqli($servername, $username, $password, $dbname);
   // Check connection
   if ($conn->connect_error) {
      die("Connection failed: " . $conn->connect_error);
   }
   $columns = array("POSTAL_CODE", "DIRECCION", "HORARIO", "LATITUD", "LOCALIDAD", "LONGITUD", "MARGEN", "MUNICIPIO", "PRECIO_BIODIESEL", "PRECIO_BIOETANOL", "PRECIO_GAS_NATURAL_COMPRIMIDO", "PRECIO_GAS_NATURAL_LICUADO", "PRECIO_GASES_LICUADOS_PETROLEO", "PRECIO_GASOLEO_A", "PRECIO_GASOLEO_B", "PRECIO_GASOLINA_95", "PRECIO_GASOLINA_98", "PRECIO_NUEVO_GASOLEO_A", "PROVINCIA", "REMISION", "ROTULO", "TIPO_VENTA", "PERCENT_BIOETANOL", "PERCENT_ESTER_METILICO", "ID_EESS", "ID_MUNICIPIO", "ID_PROVINCIA", "ID_CCAA");
   $col = "" . implode(",", $columns) . "";


   //API URL
   $url = 'https://sedeaplicaciones.minetur.gob.es/ServiciosRESTCarburantes/PreciosCarburantes/EstacionesTerrestres/';

   //create a new cURL resource
   $ch = curl_init($url);


   curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));

   //return response instead of outputting
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

   //execute the POST request
   $result = curl_exec($ch);

   //close cURL resource
   curl_close($ch);

   $result = json_decode($result);
   $result = (array) $result;
   $fecha = (string) $result["Fecha"];
   $result = (array) $result["ListaEESSPrecio"];

   $trunc = "TRUNCATE TABLE gasolineras";
   $conn->query($trunc);
   //print("<pre>".print_r($result,true)."</pre>");
   $sql = "INSERT INTO gasolineras (FECHA) VALUES ($fecha)";
   $conn->query($sql);

   foreach ($result as $v) {
      $v = (array) $v;
      $v = array_values($v);
      $val = "'" . implode("','", $v) . "'";
      $sql = "INSERT INTO gasolineras ($col) VALUES ($val)";
      $conn->query($sql);
   }


   $conn->close();

   /*$columns = implode(", ", array_keys($insData));
   $escaped_values = array_map('mysql_real_escape_string', array_values($insData));
   $values  = implode(", ", $escaped_values);
   $sql = "INSERT INTO `fbdata`($columns) VALUES ($values)";*/


   ?>
</body>

</html>