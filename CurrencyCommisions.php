<?php

namespace App\Block;


class CurrencyCommisions
{

    private $service;
    public $binApiUrl = 'https://lookup.binlist.net/';
    public $rateApiUrl = 'https://api.exchangeratesapi.io/latest';
    private $rates = [];

    const COUNTRIES_CODE = ['AT', 'BE', 'BG', 'CY', 'CZ', 'DE', 'DK', 'EE', 'ES', 'FI', 'FR', 'GR', 'HR', 'HU', 'IE', 'IT', 'LT', 'LU', 'LV', 'MT', 'NL', 'PO', 'PT', 'RO', 'SE', 'SI', 'SK'];
    const EUR_CODE = 'EUR';

    public function __construct(FileGetApiService $service)
    {
        $this->service = $service;
    }

    public function proceedBin($rowString):float {
        $row = @json_decode($rowString, true);
        $value = array_values($row);
        $bin = $value[0];
        $amount = (float)$value[1];
        $currency = $value[2];

        $binResults = $this->service->getUrlResults($this->binApiUrl.$bin);
        $result = json_decode($binResults, true);

        $isEuComission = (in_array($result['country']['alpha2'], self::COUNTRIES_CODE)) ? 0.01 : 0.02;

        $rate = $this->rates[$currency];
        $amountResult = 0;
        if ($currency == self::EUR_CODE || $rate == 0) {
            $amountResult = $amount;
        }
        if ($currency != self::EUR_CODE && $rate == 0) {
            throw new \Exception('Division by zero');
        }
        if ($currency != self::EUR_CODE || $rate > 0) {
            $amountResult = $amount / $rate;
        }

        return $amountResult * $isEuComission;
    }

    public function setRates() {
        try {
            $rates = $this->service->getUrlResults($this->rateApiUrl);
            $this->rates = json_decode($rates, true)['rates'];
        } catch (\Exception $e) {
            throw new \Exception('Problem with api rates. Message - '.$e->getMessage());
        }
    }

    public function run($file) {
        try {
            $this->setRates();
            foreach (explode("\n", $file) as $row) {
                if (!empty($row)) {
                    $rowResult = $this->proceedBin($row);
                    echo $rowResult;
                    print "\n";
                }
            }
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }
}