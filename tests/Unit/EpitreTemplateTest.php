<?php

use BlackpigCreatif\Epitre\Tests\Fixtures\TestConfirmationTemplate;

it('returns the correct key', function () {
    $template = new TestConfirmationTemplate;

    expect($template->getKey())->toBe('test.confirmation');
});

it('returns the correct label', function () {
    $template = new TestConfirmationTemplate;

    expect($template->getLabel())->toBe('Test Confirmation');
});

it('returns the correct view path', function () {
    $template = new TestConfirmationTemplate;

    expect($template->getView())->toBe('mail.epitre.test-confirmation');
});

it('returns the correct tokens array', function () {
    $template = new TestConfirmationTemplate;

    expect($template->getTokens())->toBe([
        '{name}' => 'The recipient name',
        '{msg}' => 'The message content',
    ]);
});

it('resolves token values from data', function () {
    $template = new TestConfirmationTemplate;

    expect($template->resolve(['name' => 'Alice', 'msg' => 'Hello']))->toBe([
        '{name}' => 'Alice',
        '{msg}' => 'Hello',
    ]);
});
