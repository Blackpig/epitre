<?php

namespace Workbench\App\Providers;

use BlackpigCreatif\Epitre\EpitrePlugin;
use Filament\Panel;
use Filament\PanelProvider;
use LaraZeus\SpatieTranslatable\SpatieTranslatablePlugin;

class FilamentServiceProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('epitre-test')
            ->path('admin')
            ->plugins([
                SpatieTranslatablePlugin::make()->defaultLocales(['en']),
                EpitrePlugin::make(),
            ])
            ->authGuard('web');
    }
}
