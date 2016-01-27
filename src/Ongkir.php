<?php
namespace Bachor;

use DiDom\Document;
use DiDom\Query;

class Ongkir
{
    private $from;

    private $to;

    private $weight;

    public function __construct($from, $to, $weight)
    {
        $this->from = $from;
        $this->to = $to;
        $this->weight = $weight;
    }

    public function jne()
    {
        $from = Curl::get('http://www.jne.co.id/server/server_city_from.php?term=' . $this->from);
        $from = Json::decode($from);

        $to = Curl::get('http://www.jne.co.id/server/server_city.php?term=' . $this->to);
        $to = Json::decode($to);

        if ($from == null || $to == null) {
            return $this->error('Wrong data');
        } else {
            foreach($from as $a) {
                if (mb_strtolower($a->label) == mb_strtolower($this->from)) {
                    $from = $a;
                }
            }

            foreach($to as $b) {
                if (mb_strtolower($b->label) == mb_strtolower($this->to)) {
                    $to = $b;
                }
            }

            $data = [
                'origin' => $from->code,
                'dest' => $to->code,
                'weight' => $this->weight,
                'originlabel' => $from->label,
                'destlabel' => $to->label
            ];

            $curl = Curl::post('http://www.jne.co.id/getDetailFare.php', $data);

            if ($curl == 'offline') {
                return $this->error('Server ' . $curl);
            } else {
                $document = new Document($curl);

                if (count($table = $document->find('//table[contains(@class, "table")]', Query::TYPE_XPATH)) !== 0) {

                    $temp = [];
                    foreach ($table[1]->find('td') as $key => $value) {
                        $temp[] = $value->text();
                    }
                    $temp = array_chunk($temp, 6);

                    $data = [];
                    foreach ($temp as $key => $value) {
                        $data[] = [
                            'service' => $value[0],
                            'cost' => str_replace(',', '', $value[4])
                        ];
                    }

                    unset($temp);

                    return $this->success('jne', $data);
                } else {
                    return $this->error('Wrong data');
                }
            }
        }
    }

    public function tiki()
    {
        $data = [
            'get_ori' => $this->from,
            'get_des' => $this->to,
            'get_wgdom' => $this->weight,
            'submit' => 'Check'
        ];

        $curl = Curl::post('http://www.tiki-online.com/?cat=KgfdshfF7788KHfskF', $data);

        if ($curl == 'offline') {
            return $this->error('Server ' . $curl);
        } else {
            $document = new Document($curl);

            if (count($table = $document->find('//table[contains(@cellpadding, 4)]', Query::TYPE_XPATH)) !== 0) {
                $temp = [];
                foreach ($table[0]->find('td') as $key => $value) {
                    if ($key > 1) {
                        array_push($temp, str_replace('- ', '', $value->text()));
                    }
                }

                $data = [];
                while (count($temp)) {
                    list($service, $cost) = array_splice($temp, 0, 2);

                    $data[] = [
                        'service' => trim(preg_replace('/\([^)]+\)/', '', $service)),
                        'cost' => str_replace(',', '', $cost)
                    ];
                }

                unset($temp);

                return $this->success('tiki', $data);
            } else {
                return $this->error('Wrong data');
            }
        }
    }

    private function error($message)
    {
        return Json::encode([
            'status' => 'error',
            'message' => $message
        ]);
    }

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
