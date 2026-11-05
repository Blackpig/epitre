<?php

use BlackpigCreatif\Epitre\Epitre;
use BlackpigCreatif\Epitre\Models\EmailTemplate;
use BlackpigCreatif\Epitre\Tests\Fixtures\TestConfirmationMailable;
use BlackpigCreatif\Epitre\Tests\Fixtures\TestConfirmationMarkdownTemplate;
use BlackpigCreatif\Epitre\Tests\Fixtures\TestConfirmationTemplate;
use BlackpigCreatif\Epitre\Tests\Fixtures\TestConfirmationTranslatedSubjectTemplate;
use BlackpigCreatif\Epitre\Tests\Fixtures\TestConfirmationWithLayoutTemplate;
use Illuminate\Support\Facades\View;

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

// Layout

it('uses the layout as a markdown view when a layout is set', function () {
    app()->forgetInstance(Epitre::class);
    app()->singleton(Epitre::class);
    app(Epitre::class)->register(TestConfirmationWithLayoutTemplate::class);

    EmailTemplate::factory()->create([
        'key' => 'test.confirmation',
        'body' => ['en' => '<p>Hello {name}</p>'],
        'subject' => ['en' => 'Hi'],
    ]);

    $mailable = new TestConfirmationMailable('Alice', 'Hello');
    $content = $mailable->content();

    expect($content->markdown)->toBe('mail.layouts.test-layout')
        ->and($content->htmlString)->toBeNull()
        ->and($content->view)->toBeNull();
});

it('passes the resolved body and epitreData to the layout', function () {
    app()->forgetInstance(Epitre::class);
    app()->singleton(Epitre::class);
    app(Epitre::class)->register(TestConfirmationWithLayoutTemplate::class);

    EmailTemplate::factory()->create([
        'key' => 'test.confirmation',
        'body' => ['en' => '<p>Hello {name}</p>'],
        'subject' => ['en' => 'Hi'],
    ]);

    $mailable = new TestConfirmationMailable('Alice', 'Hello');
    $content = $mailable->content();

    expect($content->with['body'])->toBe('<p>Hello Alice</p>')
        ->and($content->with['name'])->toBe('Alice');
});

it('falls back to htmlString when no layout is set even if a record exists', function () {
    EmailTemplate::factory()->create([
        'key' => 'test.confirmation',
        'body' => ['en' => '<p>Hello</p>'],
        'subject' => ['en' => 'Hi'],
    ]);

    $mailable = new TestConfirmationMailable('Alice', 'Hello');
    $content = $mailable->content();

    expect($content->htmlString)->not->toBeNull()
        ->and($content->view)->toBeNull();
});

it('falls back to the view as Markdown when the template is marked markdown', function () {
    app()->forgetInstance(Epitre::class);
    app()->singleton(Epitre::class);
    app(Epitre::class)->register(TestConfirmationMarkdownTemplate::class);

    $content = (new TestConfirmationMailable('Alice', 'Hello'))->content();

    expect($content->markdown)->toBe('mail.epitre.test-confirmation-markdown')
        ->and($content->view)->toBeNull()
        ->and($content->with)->toBe(['name' => 'Alice', 'msg' => 'Hello']);
});

it('renders a mail Markdown view when no DB record exists', function () {
    View::addLocation(dirname(__DIR__) . '/views');

    app()->forgetInstance(Epitre::class);
    app()->singleton(Epitre::class);
    app(Epitre::class)->register(TestConfirmationMarkdownTemplate::class);

    $html = (new TestConfirmationMailable('Alice', 'Thanks for writing'))->render();

    expect($html)->toContain('Hello Alice')
        ->toContain('Thanks for writing');
});

it('uses the template default subject when no stored subject exists for the locale', function () {
    app()->forgetInstance(Epitre::class);
    app()->singleton(Epitre::class);
    app(Epitre::class)->register(TestConfirmationTranslatedSubjectTemplate::class);

    EmailTemplate::factory()->create([
        'key' => 'test.confirmation',
        'subject' => ['en' => 'Stored English subject'],
    ]);

    app()->setLocale('fr');

    expect((new TestConfirmationMailable('Alice', 'Hello'))->envelope()->subject)->toBe('Merci');
});

it('prefers a stored subject over the template default subject', function () {
    app()->forgetInstance(Epitre::class);
    app()->singleton(Epitre::class);
    app(Epitre::class)->register(TestConfirmationTranslatedSubjectTemplate::class);

    EmailTemplate::factory()->create([
        'key' => 'test.confirmation',
        'subject' => ['en' => 'Stored English subject'],
    ]);

    expect((new TestConfirmationMailable('Alice', 'Hello'))->envelope()->subject)->toBe('Stored English subject');
});
