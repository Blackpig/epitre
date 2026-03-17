<?php

namespace BlackpigCreatif\Epitre\Tests\Fixtures;

use BlackpigCreatif\Epitre\Support\EpitreTemplate;

class TestConfirmationTemplate extends EpitreTemplate
{
    protected string $key = 'test.confirmation';

    protected string $label = 'Test Confirmation';

    protected string $view = 'mail.epitre.test-confirmation';

    protected array $tokens = [
        '{name}' => 'The recipient name',
        '{msg}' => 'The message content',
    ];

    public function resolve(array $data): array
    {
        return [
            '{name}' => $data['name'] ?? '',
            '{msg}' => $data['msg'] ?? '',
        ];
    }
}
