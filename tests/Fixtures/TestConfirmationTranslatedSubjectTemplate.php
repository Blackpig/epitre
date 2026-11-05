<?php

namespace BlackpigCreatif\Epitre\Tests\Fixtures;

class TestConfirmationTranslatedSubjectTemplate extends TestConfirmationTemplate
{
    public function defaultSubject(): string
    {
        return app()->getLocale() === 'fr' ? 'Merci' : 'Thank you';
    }
}
