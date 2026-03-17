<?php

use BlackpigCreatif\Epitre\Epitre;
use BlackpigCreatif\Epitre\Tests\Fixtures\TestConfirmationTemplate;

beforeEach(function () {
    app()->forgetInstance(Epitre::class);
    app()->singleton(Epitre::class);
});

it('stores a registered class string', function () {
    app(Epitre::class)->register(TestConfirmationTemplate::class);

    expect(app(Epitre::class)->all())->toContain(TestConfirmationTemplate::class);
});

it('returns all registered class strings', function () {
    app(Epitre::class)->register(TestConfirmationTemplate::class);

    expect(app(Epitre::class)->all())->toBe([TestConfirmationTemplate::class]);
});

it('returns an instance for a matching key', function () {
    app(Epitre::class)->register(TestConfirmationTemplate::class);

    $found = app(Epitre::class)->find('test.confirmation');

    expect($found)->toBeInstanceOf(TestConfirmationTemplate::class);
});

it('returns null for an unregistered key', function () {
    expect(app(Epitre::class)->find('does.not.exist'))->toBeNull();
});

it('returns one instance per registered class', function () {
    app(Epitre::class)->register(TestConfirmationTemplate::class);

    $instances = app(Epitre::class)->allInstances();

    expect($instances)->toHaveCount(1)
        ->and($instances[0])->toBeInstanceOf(TestConfirmationTemplate::class);
});

it('throws when registering a class that does not extend EpitreTemplate', function () {
    app(Epitre::class)->register(stdClass::class);
})->throws(InvalidArgumentException::class);
