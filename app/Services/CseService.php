<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class CseService
{
    public function getCseLatest()
    {
        return Cache::remember('cse_latest', 300, function () {

            $url = "https://www.cse.com.bd/market/current_price";
            $response = Http::withoutVerifying()->get($url);

            if (!$response->ok()) {
                return [];
            }

            return $this->parseHtmlCse($response->body());
        });
    }

    private function parseHtmlCse(string $html): array
    {
        $data = [];

        libxml_use_internal_errors(true);

        $dom = new \DOMDocument();
        $dom->loadHTML($html);

        $xpath = new \DOMXPath($dom);

        // Skip header row directly (better XPath)
        $rows = $xpath->query("//table[contains(@id,'dataTable')]//tr");

        foreach ($rows as $row) {

            $cells = $xpath->query(".//td", $row);

            if ($cells->length < 10) continue;

            // Extract values cleanly
            $symbol = trim($cells->item(1)->textContent);

            // Extract link
            $linkNode = $xpath->query(".//a", $cells->item(1));
            $link = $linkNode->length ? $linkNode->item(0)->getAttribute('href') : null;

            if ($link && !str_starts_with($link, 'http')) {
                $link = 'https://www.cse.com.bd/' . ltrim($link, '/');
            }

            // Numeric values (cleaned)
            $ltp    = $this->cleanNumber($cells->item(2)->textContent);
            $open   = $this->cleanNumber($cells->item(3)->textContent);
            $high   = $this->cleanNumber($cells->item(4)->textContent);
            $low    = $this->cleanNumber($cells->item(5)->textContent);
            $ycp    = $this->cleanNumber($cells->item(6)->textContent);
            $trade  = (int) $this->cleanNumber($cells->item(7)->textContent);
            $value  = $this->cleanNumber($cells->item(8)->textContent);
            $volume = (int) $this->cleanNumber($cells->item(9)->textContent);

            $data[] = [
                'link'   => $link ?: '#',
                'symbol' => $symbol,
                'ltp'    => $ltp,
                'open'   => $open,
                'high'   => $high,
                'low'    => $low,
                'ycp'    => $ycp,
                'trade'  => $trade,
                'value'  => $value,
                'volume' => $volume,

                // Derived field (very useful)
                'change' => round($ltp - $ycp, 2),
            ];
        }

        return $data;
    }

    private function cleanNumber(?string $value): float
    {
        if (!$value) return 0;

        return (float) str_replace(',', '', trim($value));
    }
}
