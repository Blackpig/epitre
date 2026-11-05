<?php

namespace BlackpigCreatif\Epitre\Tests\Fixtures;

class TestConfirmationMarkdownTemplate extends TestConfirmationTemplate
{
    protected string $view = 'mail.epitre.test-confirmation-markdown';

    protected bool $markdown = true;
}
