<?php

use BlackpigCreatif\Epitre\Tests\FilamentTestCase;
use BlackpigCreatif\Epitre\Tests\TestCase;

uses(TestCase::class)->in('Unit', 'Feature/HasEpitreTemplateTest.php', 'Feature/MakeTemplateCommandTest.php');
uses(FilamentTestCase::class)->in('Feature/Filament');
