<?php

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\File;

beforeEach(function () {
    $files = app(Filesystem::class);
    $classPath = app_path('BlackpigCreatif/Epitre/Templates/ContactConfirmationTemplate.php');
    $viewPath = resource_path('views/mail/epitre/contact-confirmation.blade.php');

    if ($files->exists($classPath)) {
        $files->delete($classPath);
    }

    if ($files->exists($viewPath)) {
        $files->delete($viewPath);
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

it('fails when the template class already exists', function () {
    $classPath = app_path('BlackpigCreatif/Epitre/Templates/ContactConfirmationTemplate.php');
    File::makeDirectory(dirname($classPath), 0755, true, true);
    File::put($classPath, '<?php');

    $this->artisan('epitre:make-template', ['name' => 'ContactConfirmation'])
        ->assertFailed();
});
