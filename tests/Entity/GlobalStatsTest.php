<?php

namespace App\Tests\Entity;

use App\Entity\GlobalStats;
use PHPUnit\Framework\TestCase;

class GlobalStatsTest extends TestCase
{
    public function testIncrementValue()
    {
        $globalStats = new GlobalStats();
        $globalStats->setValue("0");
        $this->assertEquals("0", $globalStats->getValue());

        $globalStats->incrementValue();
        $this->assertEquals("1", $globalStats->getValue());

        $globalStats->incrementValue(4);
        $this->assertEquals("5", $globalStats->getValue());
    }
}
