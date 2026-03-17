# Epitre

[![Latest Version on Packagist](https://img.shields.io/packagist/v/blackpig/epitre.svg?style=flat-square)](https://packagist.org/packages/blackpig/epitre)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/blackpig/epitre/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/blackpig/epitre/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/blackpig/epitre/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/blackpig/epitre/actions?query=workflow%3A"Fix+PHP+code+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/blackpig/epitre.svg?style=flat-square)](https://packagist.org/packages/blackpig/epitre)

An editable email copy layer for Laravel Mailables, managed through a Filament v5 panel.

Epitre sits between your Mailable classes and their output. Each template has a Blade view as its default. Editors can override the subject and body per locale through the Filament panel without touching code. If no DB record exists, the Blade view is used as-is.

## Requirements

- PHP 8.2+
- Laravel 11+
- Filament 5.0+
- `spatie/laravel-translatable`
- `lara-zeus/spatie-translatable` (Filament translatable plugin)

---

## Installation

```bash
composer require blackpig-creatif/epitre
```

Run the migration:

```bash
php artisan migrate
```

Register the plugin in your `PanelProvider`:

```php
use BlackpigCreatif\Epitre\EpitrePlugin;

public function panel(Panel $panel): Panel
{
    return $panel
        ->plugins([
            EpitrePlugin::make(),
        ]);
}
```

Epitre will register `SpatieTranslatablePlugin` automatically if it is not already present. It reads your locale list from `config('app.locales')` if that key exists (expected format: `['en' => 'English', 'fr' => 'Francais']`), falling back to `app()->getLocale()`.

---

## Quick Start

### 1. Generate a template class

```bash
php artisan epitre:make-template ContactConfirmation
```

This creates:

- `app/BlackpigCreatif/Epitre/Templates/ContactConfirmationTemplate.php`
- `resources/views/mail/epitre/contact-confirmation.blade.php`

### 2. Define tokens and resolution logic

Open the generated class and fill in the tokens your template uses:

```php
namespace App\BlackpigCreatif\Epitre\Templates;

use BlackpigCreatif\Epitre\Support\EpitreTemplate;

class ContactConfirmationTemplate extends EpitreTemplate
{
    protected string $key = 'contact-confirmation';

    protected string $label = 'Contact Confirmation';

    protected string $view = 'mail.epitre.contact-confirmation';

    /** @var array<string, string> */
    protected array $tokens = [
        '{name}'    => 'The recipient\'s name',
        '{message}' => 'The message they submitted',
    ];

    /** @return array<string, string> */
    public function resolve(array $data): array
    {
        return [
            '{name}'    => $data['name'],
            '{message}' => $data['message'],
        ];
    }
}
```

### 3. Register the template in your service provider

```php
use BlackpigCreatif\Epitre\Facades\Epitre;
use App\BlackpigCreatif\Epitre\Templates\ContactConfirmationTemplate;

public function boot(): void
{
    Epitre::register(ContactConfirmationTemplate::class);
}
```

### 4. Wire up your Mailable

Add the `HasEpitreTemplate` trait and implement the two required members:

```php
use BlackpigCreatif\Epitre\Concerns\HasEpitreTemplate;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;

class ContactConfirmation extends Mailable
{
    use HasEpitreTemplate;

    protected string $epitreKey = 'contact-confirmation';

    public function __construct(
        public string $name,
        public string $message,
    ) {}

    public function epitreData(): array
    {
        return [
            'name'    => $this->name,
            'message' => $this->message,
        ];
    }

    public function attachments(): array
    {
        return [];
    }
}
```

The trait provides `envelope()` and `content()`. Do not implement those methods yourself.

---

## How Resolution Works

When a Mailable using `HasEpitreTemplate` is sent, Epitre resolves the content in this order:

**Subject:** If a DB record exists with a subject for the current locale, it is used with tokens replaced. Otherwise, the template `$label` is used as the subject.

**Body:** If a DB record exists with a body for the current locale, it is rendered as an HTML string with tokens replaced. Otherwise, the Blade view is rendered via `Content(view: ...)` with `epitreData()` passed as view data.

This means your Blade view is always the working default. Editors only override when they want to.

---

## Template Classes

All template classes extend `EpitreTemplate`:

```php
abstract class EpitreTemplate
{
    protected string $key;    // unique dot-notation or kebab identifier
    protected string $label;  // displayed in the Filament panel
    protected string $view;   // Blade view path for the default content
    protected array  $tokens; // '{token}' => 'Description for editors'

    abstract public function resolve(array $data): array;
}
```

The `$tokens` array is informational only. It is displayed in the Filament editor sidebar so editors know what substitutions are available. The `resolve()` method maps token strings to their runtime values given the data array from `epitreData()`.

---

## Filament Resource

Registering `EpitrePlugin` adds an **Email Templates** resource to your panel.

**List view** shows all registered templates with their current status:

| Status | Meaning |
|--------|---------|
| Using default | No DB record exists, Blade view is active |
| Customised | A DB record overrides the subject and/or body |

**Edit view** lets editors set the subject and body per locale. The sidebar shows the available tokens for that template. Saving creates or updates the DB record. Leaving a field empty falls back to the Blade view default.

**Reset to default** (visible only when a DB record exists) deletes the record and restores the Blade view default. Requires confirmation.

---

## Translation

Subject and body are stored as translatable JSON columns via `spatie/laravel-translatable`. The Filament editor uses the locale switcher from `lara-zeus/spatie-translatable` to manage per-locale content.

Configure your available locales in `config/app.php`:

```php
'locales' => [
    'en' => 'English',
    'fr' => 'Francais',
],
```

Epitre reads this at boot time to configure the translatable plugin. If you are already registering `SpatieTranslatablePlugin` yourself with locales set, Epitre will not overwrite them.

---

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for recent changes.

## Credits

- [Blackpig Creatif](https://github.com/blackpig)
- [All Contributors](../../contributors)

## License

MIT. See [LICENSE](LICENSE.md) for details.
