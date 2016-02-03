<?php

/*********************************************************************
PHP Class untuk mendapatkan ongkir Tiki dan JNE dalam format JSON
langsung melalui web tiki-online.com & jne.co.id
dengan menggunakan cURL dan simple html dom.

* Coded by Ican Bachors 2016.
* http://ibacor.com/
* Updates will be posted to this site.
*********************************************************************/

class Ongkir
{    
    
    private $dari, $ke, $kg, $user_agent;
    
    function __construct($dari, $ke, $kg, $user_agent = "Googlebot/2.1 (http://www.googlebot.com/bot.html)")
    {

		// Tanda * berarti memberi hak akses kesemua host/domain untuk mengkonsusmi data JSON ini via AJAX.
		// Jika sobat hanya ingin domain sobat saja yang bisa mengkonsusmi data JSON ini via AJAX tinggal rubah seperti ini:
		// header('Access-Control-Allow-Origin: http://domain-sobat.com');
        header('Access-Control-Allow-Origin: *');

        header('Content-Type: application/json');

		// Include library simple html dom
        require("simple_html_dom.php");
        
        $this->dari = $dari;        
        $this->ke = $ke;        
        $this->kg = $kg;        
        $this->user_agent = $user_agent;
        
    }

    
    ####################### TIKI ##############################
    function tiki()
    {

		// Menentukan parameter dan menjalankan printah cURL menggunakan method POST
        $url  = "http://www.tiki-online.com/?cat=KgfdshfF7788KHfskF";
        $post = "&get_ori=$this->dari&get_des=$this->ke&get_wgdom=$this->kg&submit=Check";
		$ngecurl = $this->mycurl($url, $post);
		
		// Jika situs yang di cURL lagi offline/maintenance maka akan menampilkan error message
		if($ngecurl == 'offline'){
			return $this->errorcoy('Server sedang '.$ngecurl);
		}else{
			$html  = str_get_html($ngecurl);

			// Manipulasi DOM menggunakan library simple html dom. Find table
			$table = $html->find('table[cellpadding=4]', 0);
			
			// Jika table ongkir kosong karena salah memasukan inputan maka akan menghasilkan output error
			if (empty($table)) {
				return $this->errorcoy('Terjadi kesalahan dalam penginputan');
			} else {
				
				// Membuat array. Find td from table
				$result = array();
				foreach ($table->find('td') as $td) {
					$data = str_replace('- ', '', strip_tags($td->innertext));
					array_push($result, $data);
				}
				$result2 = array();
				for ($i = 0; $i < count($result); $i++) {
					if ($i % 2 == 0 && $i != 0 && $i != 1) {
						$a       = $result[$i];
						$b       = $result[$i + 1];
						$data2   = array(
							"layanan" => $a,
							"tarif" => $b
						);
						array_push($result2, $data2);
					}
				}
					
				// Mengirim array untuk dijadikan JSON
				return $this->successcoy($this->dari, $this->ke, 'tiki', $result2);
			}
		}
    }
    ####################### END TIKI ##########################

    
    //******************** iBacor.com ***********************//

    
    ####################### JNE ###############################
    function jne()
    {
		
		// Untuk mendapatkan nama kota dan code kota (dari)
        $json_dari  = "http://www.jne.co.id/server/server_city_from.php?term=$this->dari";
        $json_daric = file_get_contents($json_dari);
        $hasil_dari = json_decode($json_daric);
        
		// Untuk mendapatkan nama kota dan code kota (ke)
        $json_ke  = "http://www.jne.co.id/server/server_city.php?term=$this->ke";
        $json_kec = file_get_contents($json_ke);
        $hasil_ke = json_decode($json_kec);        
		
		// Jika nama kota salah atau tidak tersedia maka akan menghasilkan output error
        if ($hasil_dari == null || $hasil_ke == null) {
            return $this->errorcoy('Nama kota tidak tersedia');
        } else {
			$daric = '';
			$darib = '';
			$kec = '';
			$keb = '';
			foreach($hasil_dari as $hdr){
				if($hdr->label == strtoupper($this->dari)){
					$daric .= $hdr->code;
					$darib .= $hdr->label;
				}
			}
			foreach($hasil_ke as $hke){
				if($hke->label == strtoupper($this->ke)){
					$kec .= $hke->code;
					$keb .= $hke->label;
				}
			}

            // Menentukan parameter dan menjalankan printah cURL menggunakan method POST
			$url   = "http://www.jne.co.id/getDetailFare.php";
            $post  = "origin=$daric&dest=$kec&weight=$this->kg&originlabel=$darib&destlabel=$keb";
			$ngecurl = $this->mycurl($url, $post);
			
			// Jika situs yang di cURL lagi offline/maintenance maka akan menampilkan error message
			if($ngecurl == 'offline'){
				return $this->errorcoy('Server sedang '.$ngecurl);
			}else{
				$html  = str_get_html($ngecurl);

				// Manipulasi DOM menggunakan library simple html dom. Fin table
				$table = $html->find('table[class=table]', 1);
			
				// Jika table ongkir kosong karena salah memasukan inputan maka akan menghasilkan output error
				if (empty($table)) {
					return $this->errorcoy('Terjadi kesalahan dalam penginputan');
				} else {
					
					// Membuat array. Find tr from table
					$result = array();
					foreach ($table->find('tr') as $this->key => $value) {
						if ($this->key != 0) {
							$data = $value->innertext;
							array_push($result, $data);
						}
					}
					
					// Membuat array. Find td from tr
					$result2 = array();
					foreach ($result as $s => $tr) {
						if ($s % 2 == 0) {
							$td      = explode('<td', $tr);
							$search  = array(
								">",
								" align=\"right\"",
								" align='center'",
								"\t",
								" "
							);
							$replace = array(
								"",
								"",
								"",
								"",
								""
							);
							$data2   = array(
								'layana' => str_replace($search, $replace, strip_tags($td[1])),
								'kiriman' => str_replace($search, $replace, strip_tags($td[2])),
								'tarif' => str_replace($search, $replace, strip_tags($td[5])),
								'etd' => str_replace($search, $replace, strip_tags($td[6]))
							);
							array_push($result2, $data2);
						}
					}
					
					// Mengirim array untuk dijadikan JSON
					return $this->successcoy($darib, $keb, 'jne', $result2);
				}
			}
        }
    }    
    ####################### END JNE ###########################
	
	   
    ####################### NGE cURL ##########################
	private function mycurl($url, $post)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_USERAGENT, $this->user_agent);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
		
		// Gagal ngecURL
        if(!$site = curl_exec($ch)){
			return 'offline';
		}
		
		// Sukses ngecURL
		else{
			return $site;
		}
	}   
    ####################### END cURL ##########################

    
	####################### OUTPUT JSON #######################
    private function successcoy($dari, $ke, $service, $array)
    {
        return json_encode(array(
            'status' => 'success',
            'service' => $service,
            'dari' => $dari,
            'ke' => $ke,
            'berat' => $this->kg,
            'ongkos' => $array
        ), JSON_PRETTY_PRINT);
    }
    
    function errorcoy($message)
    {
        return json_encode(array(
            'status' => 'error',
            'message' => $message
        ), JSON_PRETTY_PRINT);
    }
	############################################################
    
}

?>
