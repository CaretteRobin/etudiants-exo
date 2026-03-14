<?php

namespace Test;

use PHPUnit\Framework\TestCase;

use App\MusicBand;
use App\User;

class UserMusicBandTest extends TestCase
{
    public function testBasicLaptop()
    {
        $albert = new User('Albert Mudhat');
        $michelle = new User('Michelle Ectron');
        $yves = new User('Yves Haige');

        $band = new MusicBand('Daft PHPunk');

        $band->attach($albert);
        $band->attach($michelle);
        $band->attach($yves);

        $band->detach($yves);

        $band->addNewConcertDate('19/11/2027.', 'Bercy');

        $this->assertTrue($albert->isNotified());
        $this->assertTrue($michelle->isNotified());
        $this->assertFalse($yves->isNotified());
    }
}
