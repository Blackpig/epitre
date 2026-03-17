<?php

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\File;

beforeEach(function () {
    $files = app(Filesystem::class);

    foreach ([
        app_path('BlackpigCreatif/Epitre/Templates/ContactConfirmationTemplate.php'),
        resource_path('views/mail/epitre/contact-confirmation.blade.php'),
        resource_path('views/mail/layouts/epitre.blade.php'),
    ] as $path) {
        if ($files->exists($path)) {
            $files->delete($path);
        }
    }
});

it('generates the template class at the correct path', function () {
    $this->artisan('epitre:make-template', ['name' => 'ContactConfirmation'])
        ->assertSuccessful();

    expect(app_path('BlackpigCreatif/Epitre/Templates/ContactConfirmationTemplate.php'))->toBeFile();
});

it('generates the Blade view at the correct path', function () {
    $this->artisan('epitre:make-template', ['name' => 'ContactConfirmation'])
        ->assertSuccessful();

    expect(resource_path('views/mail/epitre/contact-confirmation.blade.php'))->toBeFile();
});

it('generates a class that extends EpitreTemplate', function () {
    $this->artisan('epitre:make-template', ['name' => 'ContactConfirmation'])
        ->assertSuccessful();

    $contents = File::get(app_path('BlackpigCreatif/Epitre/Templates/ContactConfirmationTemplate.php'));

    expect($contents)->toContain('extends EpitreTemplate');
});

it('sets the correct key in the generated class', function () {
    $this->artisan('epitre:make-template', ['name' => 'ContactConfirmation'])
        ->assertSuccessful();

    $contents = File::get(app_path('BlackpigCreatif/Epitre/Templates/ContactConfirmationTemplate.php'));

    expect($contents)->toContain("'contact-confirmation'");
});

it('sets the correct view path in the generated class', function () {
    $this->artisan('epitre:make-template', ['name' => 'ContactConfirmation'])
        ->assertSuccessful();

    $contents = File::get(app_path('BlackpigCreatif/Epitre/Templates/ContactConfirmationTemplate.php'));

    expect($contents)->toContain("'mail.epitre.contact-confirmation'");
});

it('outputs the registration reminder', function () {
    $this->artisan('epitre:make-template', ['name' => 'ContactConfirmation'])
        ->expectsOutputToContain('Epitre::register(ContactConfirmationTemplate::class)')
        ->assertSuccessful();
});

it('warns and skips the Blade view when it already exists', function () {
    $viewPath = resource_path('views/mail/epitre/contact-confirmation.blade.php');
    File::makeDirectory(dirname($viewPath), 0755, true, true);
    File::put($viewPath, '<p>Existing content</p>');

    $this->artisan('epitre:make-template', ['name' => 'ContactConfirmation'])
        ->expectsOutputToContain('already exists')
        ->assertSuccessful();

    expect(File::get($viewPath))->toBe('<p>Existing content</p>');
});

it('sets the layout path in the generated class', function () {
    $this->artisan('epitre:make-template', ['name' => 'ContactConfirmation'])
        ->assertSuccessful();

    $contents = File::get(app_path('BlackpigCreatif/Epitre/Templates/ContactConfirmationTemplate.php'));

    expect($contents)->toContain("'mail.layouts.epitre'");
});

it('creates the shared layout view when it does not exist', function () {
    $this->artisan('epitre:make-template', ['name' => 'ContactConfirmation'])
        ->assertSuccessful();

    expect(resource_path('views/mail/layouts/epitre.blade.php'))->toBeFile();
});

it('does not overwrite the layout view when it already exists', function () {
    $layoutPath = resource_path('views/mail/layouts/epitre.blade.php');
    File::makeDirectory(dirname($layoutPath), 0755, true, true);
    File::put($layoutPath, '<custom>existing layout</custom>');

    $this->artisan('epitre:make-template', ['name' => 'ContactConfirmation'])
        ->assertSuccessful();

    expect(File::get($layoutPath))->toBe('<custom>existing layout</custom>');
});

it('fails when the template class already exists', function () {
    $classPath = app_path('BlackpigCreatif/Epitre/Templates/ContactConfirmationTemplate.php');
    File::makeDirectory(dirname($classPath), 0755, true, true);
    File::put($classPath, '<?php');

    $this->artisan('epitre:make-template', ['name' => 'ContactConfirmation'])
        ->assertFailed();
});
