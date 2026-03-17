<?php

use BlackpigCreatif\Epitre\Epitre;
use BlackpigCreatif\Epitre\Models\EmailTemplate;
use BlackpigCreatif\Epitre\Tests\Fixtures\TestConfirmationMailable;
use BlackpigCreatif\Epitre\Tests\Fixtures\TestConfirmationTemplate;

beforeEach(function () {
    app()->forgetInstance(Epitre::class);
    app()->singleton(Epitre::class);
    app(Epitre::class)->register(TestConfirmationTemplate::class);
});

it('falls back to the Blade view when no DB record exists', function () {
    $mailable = new TestConfirmationMailable('Alice', 'Hello');
    $content = $mailable->content();

    expect($content->view)->toBe('mail.epitre.test-confirmation')
        ->and($content->htmlString)->toBeNull();
});

it('passes epitreData as view data when falling back', function () {
    $mailable = new TestConfirmationMailable('Alice', 'Hello');
    $content = $mailable->content();

    expect($content->with)->toBe(['name' => 'Alice', 'msg' => 'Hello']);
});

it('returns an htmlString when a DB record exists with body content', function () {
    EmailTemplate::factory()->create([
        'key' => 'test.confirmation',
        'body' => ['en' => '<p>Hello {name}</p>'],
        'subject' => ['en' => 'Hi {name}'],
    ]);

    $mailable = new TestConfirmationMailable('Alice', 'Hello');
    $content = $mailable->content();

    expect($content->htmlString)->not->toBeNull()
        ->and($content->view)->toBeNull();
});

it('replaces body tokens with resolved values', function () {
    EmailTemplate::factory()->create([
        'key' => 'test.confirmation',
        'body' => ['en' => '<p>Hello {name}, your message: {msg}</p>'],
        'subject' => ['en' => 'Hi'],
    ]);

    $mailable = new TestConfirmationMailable('Alice', 'Hello there');
    $content = $mailable->content();

    expect($content->htmlString)->toBe('<p>Hello Alice, your message: Hello there</p>');
});

it('replaces subject tokens with resolved values', function () {
    EmailTemplate::factory()->create([
        'key' => 'test.confirmation',
        'subject' => ['en' => 'Hello {name}'],
        'body' => ['en' => '<p>Body</p>'],
    ]);

    $mailable = new TestConfirmationMailable('Alice', 'Hello');
    $envelope = $mailable->envelope();

    expect($envelope->subject)->toBe('Hello Alice');
});

it('falls back to the template label when no DB record exists', function () {
    $mailable = new TestConfirmationMailable('Alice', 'Hello');
    $envelope = $mailable->envelope();

    expect($envelope->subject)->toBe('Test Confirmation');
});

it('does not replace partial token matches', function () {
    EmailTemplate::factory()->create([
        'key' => 'test.confirmation',
        'body' => ['en' => '<p>{name} and {named}</p>'],
        'subject' => ['en' => 'Hi'],
    ]);

    $mailable = new TestConfirmationMailable('Alice', 'Hello');
    $content = $mailable->content();

    expect($content->htmlString)->toBe('<p>Alice and {named}</p>');
});
