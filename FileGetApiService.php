<?php

namespace App\Block;

class FileGetApiService
{
    public function getUrlResults($url):string {
        $result = @file_get_contents($url);
        if (!$result) {
            throw new \Exception('Problem with api. Url'.$url);
        }
        return $result;
    }
}