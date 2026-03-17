<?php

namespace BlackpigCreatif\Epitre\Tests;

use Filament\Facades\Filament;

class FilamentTestCase extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Filament::setCurrentPanel(Filament::getPanel('epitre-test'));
    }
}
