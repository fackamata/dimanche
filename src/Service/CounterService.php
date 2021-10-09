<?php

namespace App\Service;

use App\Entity\Annonce;
use App\Entity\Conseil;


class CounterService
{

public function countView(int $nbVue): int
    {   
        
        $nbVueAfter = $nbVue + 1;
        return $nbVueAfter;
    }
}