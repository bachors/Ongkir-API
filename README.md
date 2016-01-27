# Create API ongkir on your own website
PHP Class untuk mendapatkan ongkir Tiki dan JNE dalam format JSON langsung melalui web tiki-online.com &amp; jne.co.id dengan menggunakan cURL dan simple html dom.<h2>Install</h2><pre>&lt;?php

// Include library ongkir
require("lib/ongkir.php");

// Create objek ongkir(dari, ke, berat)
$ongkir = new ongkir('bandung', 'surabaya', 5);

// Menampilkan ongkir JNE
echo $ongkir-&gt;jne();

/* Menampilkan ongkir TIKI
echo $ongkir-&gt;tiki();
*/

/* Menampilkan error
echo $ongkir-&gt;errorcoy("Message");
*/

?&gt;</pre>
