<?php
namespace Bachor;

use DiDom\Document;
use DiDom\Query;

class Ongkir
{

    /**
     * Kota asal pengiriman.
     *
     * @var string
     */
    private $from;

    /**
     * Kota tujuan pengiriman.
     *
     * @var string
     */
    private $to;

    /**
     * Berat barang pengiriman.
     *
     * @var int
     */
    private $weight;

    /**
     * Konstruktor class ongkir.
     *
     * @param string $from
     * @param string $to
     * @param int $weight
     * @return void
     */
    public function __construct($from, $to, $weight)
    {
        $this->from = $from;
        $this->to = $to;
        $this->weight = $weight;
    }

    /**
     * Method untuk mendapatkan ongkos kirim jne.
     *
     * @return string
     */
    public function jne()
    {
        // Curl ke halaman jne untuk mendapatkan kota asal pengiriman
        $from = Curl::get('http://www.jne.co.id/server/server_city_from.php?term=' . $this->from);
        $from = Json::decode($from);

        // Curl ke halaman jne mendapatkan kota tujuan pengiriman
        $to = Curl::get('http://www.jne.co.id/server/server_city.php?term=' . $this->to);
        $to = Json::decode($to);

        // Jika nama kota salah atau tidak tersedia maka tampilkan pesan error
        if ($from == null || $to == null) {
            return $this->error('Wrong data');
        } else {
            // Compare terlebih dahulu hasil pencarian kota asal pengiriman dengan hasil inputan kota asal pengiriman
            // Untuk mendapatkan hasil yang sesuai seperti yang diinginkan
            foreach ($from as $a) {
                if (mb_strtolower($a->label) == mb_strtolower($this->from)) {
                    $from = $a;
                }
            }

            // Compare terlebih dahulu hasil pencarian kota tujuan pengiriman dengan hasil inputan kota tujuan pengiriman
            // Untuk mendapatkan hasil yang sesuai seperti yang diinginkan
            foreach ($to as $b) {
                if (mb_strtolower($b->label) == mb_strtolower($this->to)) {
                    $to = $b;
                }
            }

            // Set parameter untuk curl
            $data = [
                'origin' => $from->code,
                'dest' => $to->code,
                'weight' => $this->weight,
                'originlabel' => $from->label,
                'destlabel' => $to->label
            ];

            // Curl ke halaman jne untuk mendapatkan ongkos kirim
            $curl = Curl::post('http://www.jne.co.id/getDetailFare.php', $data);

            // Cek http status code dari curl, jika tidak 200 (ok) maka tampilkan pesan error
            if (Curl::info('http_code') !== 200) {
                return $this->error('Server error');
            } else {
                // Manipulasi DOM menggunakan library DiDOM
                $document = new Document($curl);

                // Cari table ongkir, jika table ongkir kosong karena salah memasukan inputan maka tampilkan pesan error
                if (count($table = $document->find('//table[contains(@class, "table")]', Query::TYPE_XPATH)) !== 0) {
                    // Simpan dan manipulasi isi table yang diinginkan kedalam array sementara
                    $temp = [];
                    foreach ($table[1]->find('td') as $key => $value) {
                        $temp[] = $value->text();
                    }
                    $temp = array_chunk($temp, 6);

                    // Olah kembali array sementara kedalam array baru untuk mendapatkan hasil yang diinginkan
                    $data = [];
                    foreach ($temp as $key => $value) {
                        $data[] = [
                            'service' => $value[0],
                            'cost' => str_replace(',', '', $value[4])
                        ];
                    }

                    unset($temp);

                    // Mengirimkan data
                    return $this->success('jne', $data);
                } else {
                    return $this->error('Wrong data');
                }
            }
        }
    }

    /**
     * Method untuk mendapatkan ongkos kirim tiki.
     *
     * @return string
     */
    public function tiki()
    {
        // Set parameter untuk curl
        $data = [
            'get_ori' => $this->from,
            'get_des' => $this->to,
            'get_wgdom' => $this->weight,
            'submit' => 'Check'
        ];

        // Curl ke halaman tiki untuk mendapatkan ongkos kirim
        $curl = Curl::post('http://www.tiki-online.com/?cat=KgfdshfF7788KHfskF', $data);

        // Cek http status code dari curl, jika tidak 200 (ok) maka tampilkan pesan error
        if (Curl::info('http_code') !== 200) {
            return $this->error('Server error');
        } else {
            // Manipulasi DOM menggunakan library DiDOM
            $document = new Document($curl);

            // Cari table ongkir, jika table ongkir kosong karena salah memasukan inputan maka tampilkan pesan error
            if (count($table = $document->find('//table[contains(@cellpadding, 4)]', Query::TYPE_XPATH)) !== 0) {
                // Simpan dan manipulasi isi table yang diinginkan kedalam array sementara
                $temp = [];
                foreach ($table[0]->find('td') as $key => $value) {
                    if ($key > 1) {
                        array_push($temp, str_replace('- ', '', $value->text()));
                    }
                }

                // Olah kembali array sementara kedalam array baru untuk mendapatkan hasil yang diinginkan
                $data = [];
                while (count($temp)) {
                    list($service, $cost) = array_splice($temp, 0, 2);

                    $data[] = [
                        'service' => trim(preg_replace('/\([^)]+\)/', '', $service)),
                        'cost' => str_replace(',', '', $cost)
                    ];
                }

                unset($temp);

                // Mengirimkan data
                return $this->success('tiki', $data);
            } else {
                return $this->error('Wrong data');
            }
        }
    }

    /**
     * Method untuk pesan error.
     *
     * @param string $message
     * @return string
     */
    private function error($message)
    {
        return Json::encode([
            'status' => 'error',
            'message' => $message
        ]);
    }

    /**
     * Method untuk pesan sukses.
     *
     * @param string $service
     * @param array $data
     * @return string
     */
    private function success($service, $data)
    {
        return Json::encode([
            'status' => 'success',
            'service' => $service,
            'from' => $this->from,
            'to' => $this->to,
            'weight' => $this->weight,
            'postage' => $data
        ]);
    }
}
