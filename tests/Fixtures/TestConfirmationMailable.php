<?php

namespace BlackpigCreatif\Epitre\Tests\Fixtures;

use BlackpigCreatif\Epitre\Concerns\HasEpitreTemplate;
use Illuminate\Mail\Mailable;

class TestConfirmationMailable extends Mailable
{
    use HasEpitreTemplate;

    protected string $epitreKey = 'test.confirmation';

    public function __construct(
        public readonly string $name,
        public readonly string $msg,
    ) {}

    protected function epitreData(): array
    {
        return [
            'name' => $this->name,
            'msg' => $this->msg,
        ];
    }
}
