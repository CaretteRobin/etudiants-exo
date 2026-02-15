<?php

namespace Test;

use PHPUnit\Framework\TestCase;

use App\User;
use App\MusicBand;

class UserMusicBandTest extends TestCase
{
    public function testBasicLaptop()
    {
        $albert = new User('Albert Mudhat');
        $michelle = new User('Michelle Ectron');
        $yves = new User('Yves Haigé');


        $band = new MusicBand('Daft PHPunk');

        $band->attach($albert);
        $band->attach($michelle);
        $band->attach($yves);

        $band->detach($yves);

        $band->addNewConcertDate('19/11/2027.', 'Bercy');

        $this->assertFalse($albert->isNotified());
        $this->assertTrue($michelle->isNotified());
        $this->assertTrue($yves->isNotified());
    }

}