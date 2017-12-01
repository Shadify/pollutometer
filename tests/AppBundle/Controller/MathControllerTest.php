<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MathControllerTest extends WebTestCase
{
    public function testAQIMath()
    {
        $CO = 1;
        $bpLowCO = 0;
        $bpHiCO = 4;
        $bpLowIndex = 0;
        $bpHiIndex = 50;
        $AQI = ((50 - 0)/(4 - 0))*(1-0)+0;
        $this->assertEquals(12.5, $AQI);
    }
}