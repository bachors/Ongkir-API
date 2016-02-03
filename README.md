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

?&gt;</pre><h3>Screenshot</h3><p><img src="https://scontent-sin1-1.xx.fbcdn.net/hphotos-xpt1/v/l/t1.0-9/12654434_10205669481767653_5671888254269929619_n.jpg?oh=965ce672c61b969f36e9f27bdc4ca309&oe=572F9B2D"/><img src="https://scontent-sin1-1.xx.fbcdn.net/hphotos-xpf1/v/t1.0-9/12642668_10205669474647475_3497164853928419275_n.jpg?oh=d1b17fd2a2e62577456c254da4b5723a&oe=57396B4E"/></p><h3>Thanks for contribution. <a href="https://github.com/ncaneldiee/php-ongkir">Ongkir API</a>  by <a href="https://github.com/ncaneldiee/">ncaneldiee</a></h3>
