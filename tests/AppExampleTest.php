<?php


namespace App\Tests;

use App\Block\CurrencyCommisions;
use App\Block\FileGetApiService;
use PHPUnit\Framework\TestCase;

class AppExampleTest extends TestCase
{


    public function testAppJPY(): void
    {
        $fileService = $this->createMock(FileGetApiService::class);
        $rates = '{"rates":{"JPY":2}}';
        $fileService->expects($this->any())->method('getUrlResults')->willReturnMap([['https://lookup.binlist.net/45717360',
            '{"number":{},"scheme":"visa","type":"credit","brand":"Visa Classic","country":{"numeric":"392","alpha2":"JP","name":"Japan","emoji":"🇯🇵","currency":"JPY","latitude":36,"longitude":138},"bank":{"name":"Credit Saison Co., Ltd."}}'],
            ['https://api.exchangeratesapi.io/latest',$rates]]);
        $app = new CurrencyCommisions($fileService);
        $app->setRates();
        $result = $app->proceedBin('{"bin":"45717360","amount":"100.00","currency":"JPY"}');
        $this->assertEquals(1,$result);
    }

    public function testAppEUR(): void
    {
        $fileService = $this->createMock(FileGetApiService::class);
        $rates = '{"rates":{"EUR":2}}';
        $fileService->expects($this->any())->method('getUrlResults')->willReturnMap([['https://lookup.binlist.net/45717360',
            '{"number":{},"scheme":"visa","type":"credit","brand":"Visa Classic","country":{"numeric":"392","alpha2":"JP","name":"Japan","emoji":"🇯🇵","currency":"EUR","latitude":36,"longitude":138},"bank":{"name":"Credit Saison Co., Ltd."}}'],
            ['https://api.exchangeratesapi.io/latest',$rates]]);
        $app = new CurrencyCommisions($fileService);
        $app->setRates();
        $result = $app->proceedBin('{"bin":"45717360","amount":"120.00","currency":"EUR"}');
        $this->assertEquals(1.2,$result);
    }

    public function testAppFail(): void
    {
        $fileService = $this->createMock(FileGetApiService::class);
        $rates = '{"rates":{"JPY":0}}';
        $fileService->expects($this->any())->method('getUrlResults')->willReturnMap([['https://lookup.binlist.net/45717360',
            '{"number":{},"scheme":"visa","type":"credit","brand":"Visa Classic","country":{"numeric":"392","alpha2":"JP","name":"Japan","emoji":"🇯🇵","currency":"JPY","latitude":36,"longitude":138},"bank":{"name":"Credit Saison Co., Ltd."}}'],
            ['https://api.exchangeratesapi.io/latest',$rates]]);
        $app = new CurrencyCommisions($fileService);
        $app->setRates();
        $this->expectException(\Exception::class);
        $app->proceedBin('{"bin":"45717360","amount":"120.00","currency":"JPY"}');
    }

}