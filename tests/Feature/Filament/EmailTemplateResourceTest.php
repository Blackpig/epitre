<?php

use BlackpigCreatif\Epitre\Epitre;
use BlackpigCreatif\Epitre\Filament\Resources\EmailTemplateResource;
use BlackpigCreatif\Epitre\Filament\Resources\EmailTemplateResource\Pages\EditEmailTemplate;
use BlackpigCreatif\Epitre\Filament\Resources\EmailTemplateResource\Pages\ListEmailTemplates;
use BlackpigCreatif\Epitre\Models\EmailTemplate;
use BlackpigCreatif\Epitre\Tests\Fixtures\TestConfirmationTemplate;
use Workbench\App\Models\User;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Livewire\livewire;

beforeEach(function () {
    app()->forgetInstance(Epitre::class);
    app()->singleton(Epitre::class);
    app(Epitre::class)->register(TestConfirmationTemplate::class);

    $this->actingAs(User::factory()->create());
});

// List page

it('renders the list page without error when no templates are registered', function () {
    app()->forgetInstance(Epitre::class);
    app()->singleton(Epitre::class);

    livewire(ListEmailTemplates::class)
        ->assertOk();
});

it('renders the list page with registered templates', function () {
    livewire(ListEmailTemplates::class)
        ->assertOk();
});

it('shows "Using default" badge for a template without a DB record', function () {
    livewire(ListEmailTemplates::class)
        ->assertTableColumnStateSet('status', 'Using default', 'test.confirmation');
});

it('shows "Customised" badge for a template with a DB record', function () {
    EmailTemplate::factory()->create([
        'key' => 'test.confirmation',
        'subject' => ['en' => 'Hello'],
        'body' => ['en' => '<p>Body</p>'],
    ]);

    livewire(ListEmailTemplates::class)
        ->assertTableColumnStateSet('status', 'Customised', 'test.confirmation');
});

it('hides the reset action for a template without a DB record', function () {
    $template = EmailTemplate::firstOrNew(['key' => 'test.confirmation']);

    livewire(ListEmailTemplates::class)
        ->assertTableActionHidden('resetToDefault', $template);
});

it('shows the reset action for a template with a DB record', function () {
    $template = EmailTemplate::factory()->create([
        'key' => 'test.confirmation',
        'subject' => ['en' => 'Hello'],
        'body' => ['en' => '<p>Body</p>'],
    ]);

    livewire(ListEmailTemplates::class)
        ->assertTableActionVisible('resetToDefault', $template);
});

// Reset to default action

it('deletes the DB record when reset to default is called', function () {
    $template = EmailTemplate::factory()->create([
        'key' => 'test.confirmation',
        'subject' => ['en' => 'Hello'],
        'body' => ['en' => '<p>Body</p>'],
    ]);

    livewire(ListEmailTemplates::class)
        ->callTableAction('resetToDefault', $template)
        ->assertNotified();

    assertDatabaseMissing('epitre_email_templates', ['key' => 'test.confirmation']);
});

// Edit page

it('loads the edit page for a key with no DB record', function () {
    livewire(EditEmailTemplate::class, ['record' => 'test.confirmation'])
        ->assertOk();
});

it('loads the edit page for a key with an existing DB record', function () {
    EmailTemplate::factory()->create([
        'key' => 'test.confirmation',
        'subject' => ['en' => 'Hello'],
        'body' => ['en' => '<p>Body</p>'],
    ]);

    livewire(EditEmailTemplate::class, ['record' => 'test.confirmation'])
        ->assertOk();
});

it('creates a DB record when saving a template with no existing record', function () {
    livewire(EditEmailTemplate::class, ['record' => 'test.confirmation'])
        ->fillForm([
            'subject' => 'Welcome {name}',
            'body' => '<p>Hello {name}</p>',
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    assertDatabaseHas('epitre_email_templates', ['key' => 'test.confirmation']);
});

it('updates the DB record when saving a template that already exists', function () {
    EmailTemplate::factory()->create([
        'key' => 'test.confirmation',
        'subject' => ['en' => 'Original'],
        'body' => ['en' => '<p>Original</p>'],
    ]);

    livewire(EditEmailTemplate::class, ['record' => 'test.confirmation'])
        ->fillForm([
            'subject' => 'Updated subject',
            'body' => '<p>Updated body</p>',
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    assertDatabaseHas('epitre_email_templates', ['key' => 'test.confirmation']);
});

it('redirects to the list page after saving', function () {
    livewire(EditEmailTemplate::class, ['record' => 'test.confirmation'])
        ->fillForm([
            'subject' => 'Hello',
            'body' => '<p>Body</p>',
        ])
        ->call('save')
        ->assertRedirect(EmailTemplateResource::getUrl('index'));
});
