<?php

namespace BlackpigCreatif\Epitre\Tests\Fixtures;

class TestConfirmationWithLayoutTemplate extends TestConfirmationTemplate
{
    protected ?string $layout = 'mail.layouts.test-layout';
}
