<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class DseService
{
    public function getLatest()
    {
        return Cache::remember('dse_latest', 300, function () {

            $url = "https://dsebd.org/latest_share_price_scroll_l.php";
            $response = Http::get($url);

            if (!$response->ok()) return [];

            $html = $response->body();

            return $this->parseHtml($html);
        });
    }

    private function parseHtml($html)
    {
        $data = [];
        libxml_use_internal_errors(true);
        $dom = new \DOMDocument();
        $dom->loadHTML($html);

        $xpath = new \DOMXPath($dom);
        $rows = $xpath->query("//table[contains(@class,'fixedHeader')]//tr");

        foreach ($rows as $index => $row) {
            if ($index === 0) continue;

            $cols = $row->getElementsByTagName('td');

            if ($cols->length > 10) {
                $aTag = $cols->item(1)->getElementsByTagName('a')->item(0); // <a> tag in 2nd td
                $symbol = $aTag ? trim($aTag->nodeValue) : '';
                $link = $aTag ? trim($aTag->getAttribute('href')) : '';
                if ($link) {
                    $link = 'https://dsebd.org/' . $link;
                }

                $symbol = trim($cols->item(1)?->textContent ?? '');
                $ltp    = trim($cols->item(2)?->textContent ?? '');
                $high   = trim($cols->item(3)?->textContent ?? '');
                $low    = trim($cols->item(4)?->textContent ?? '');
                $closep = trim($cols->item(5)?->textContent ?? '');
                $ycp    = trim($cols->item(6)?->textContent ?? '');
                $change = trim($cols->item(7)?->textContent ?? '');
                $trade  = trim($cols->item(8)?->textContent ?? '');
                $value  = trim($cols->item(9)?->textContent ?? '');
                $volume = trim($cols->item(10)?->textContent ?? '');

                if ($symbol) {
                    $data[] = [
                        'link' => $link,
                        'symbol' => $symbol,
                        'ltp'    => $ltp,
                        'high'   => $high,
                        'low'    => $low,
                        'closep' => $closep,
                        'ycp'    => $ycp,
                        'change' => $change,
                        'trade'  => $trade,
                        'value'  => $value,
                        'volume' => $volume,
                    ];
                }
            }
        }

        return $data;
    }
}
