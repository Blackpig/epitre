<?php

namespace BlackpigCreatif\Epitre;

use BlackpigCreatif\Epitre\Filament\Resources\EmailTemplateResource;
use Filament\Contracts\Plugin;
use Filament\Panel;
use LaraZeus\SpatieTranslatable\SpatieTranslatablePlugin;

class EpitrePlugin implements Plugin
{
    protected ?string $navigationGroup = null;

    protected ?string $resourceClass = EmailTemplateResource::class;

    public function navigationGroup(?string $group): static
    {
        $this->navigationGroup = $group;

        return $this;
    }

    public function getNavigationGroup(): ?string
    {
        return $this->navigationGroup;
    }

    public function getId(): string
    {
        return 'epitre';
    }

    public function register(Panel $panel): void
    {
        if (! $panel->hasPlugin('spatie-translatable')) {
            $panel->plugin(
                SpatieTranslatablePlugin::make()
                    ->defaultLocales($this->resolveDefaultLocales())
            );
        }

        $panel->resources([
            $this->resourceClass,
        ]);
    }

    public function boot(Panel $panel): void
    {
        $plugin = $panel->getPlugin('spatie-translatable');

        if (empty($plugin->getDefaultLocales())) {
            $plugin->defaultLocales($this->resolveDefaultLocales());
        }
    }

    protected function resolveDefaultLocales(): array
    {
        $configured = config('app.locales');

        if (is_array($configured) && count($configured) > 0) {
            return array_keys($configured);
        }

        return [app()->getLocale()];
    }

    public static function make(): static
    {
        return app(static::class);
    }

    public static function get(): static
    {
        /** @var static $plugin */
        $plugin = filament(app(static::class)->getId());

        return $plugin;
    }

    public function resource(string $resourceClass): static
    {
        $this->resourceClass = $resourceClass;

        return $this;
    }
}
