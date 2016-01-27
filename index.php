<?php

// Include library ongkir
require("lib/ongkir.php");

// Create objek ongkir(dari, ke, berat)
$ongkir = new ongkir('bandung', 'surabaya', 5);

// Menampilkan ongkir JNE
echo $ongkir->jne();

/* Menampilkan ongkir TIKI
echo $ongkir->tiki();
*/

/* Menampilkan error
echo $ongkir->errorcoy("Message");
*/

?>
