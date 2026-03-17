<?php

namespace BlackpigCreatif\Epitre;

use BlackpigCreatif\Epitre\Commands\MakeTemplateCommand;
use BlackpigCreatif\Epitre\Testing\TestsEpitre;
use Filament\Support\Assets\Asset;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Facades\FilamentIcon;
use Illuminate\Filesystem\Filesystem;
use Livewire\Features\SupportTesting\Testable;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class EpitreServiceProvider extends PackageServiceProvider
{
    public static string $name = 'epitre';

    public static string $viewNamespace = 'epitre';

    public function configurePackage(Package $package): void
    {
        $package->name(static::$name)
            ->hasCommands($this->getCommands())
            ->hasInstallCommand(function (InstallCommand $command) {
                $command
                    ->publishConfigFile()
                    ->publishMigrations()
                    ->askToRunMigrations()
                    ->askToStarRepoOnGitHub('blackpigcreatif/epitre');
            });

        $configFileName = $package->shortName();

        if (file_exists($package->basePath("/../config/{$configFileName}.php"))) {
            $package->hasConfigFile();
        }

        if (file_exists($package->basePath('/../database/migrations'))) {
            $package->hasMigrations($this->getMigrations());
        }

        if (file_exists($package->basePath('/../resources/lang'))) {
            $package->hasTranslations();
        }

        if (file_exists($package->basePath('/../resources/views'))) {
            $package->hasViews(static::$viewNamespace);
        }
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(Epitre::class);
    }

    public function packageBooted(): void
    {
        FilamentAsset::register(
            $this->getAssets(),
            $this->getAssetPackageName()
        );

        FilamentAsset::registerScriptData(
            $this->getScriptData(),
            $this->getAssetPackageName()
        );

        FilamentIcon::register($this->getIcons());

        if (app()->runningInConsole()) {
            foreach (app(Filesystem::class)->files(__DIR__ . '/../stubs/') as $file) {
                $this->publishes([
                    $file->getRealPath() => base_path("stubs/epitre/{$file->getFilename()}"),
                ], 'epitre-stubs');
            }
        }

        Testable::mixin(new TestsEpitre);
    }

    protected function getAssetPackageName(): ?string
    {
        return 'blackpigcreatif/epitre';
    }

    /** @return array<Asset> */
    protected function getAssets(): array
    {
        return [];
    }

    /** @return array<class-string> */
    protected function getCommands(): array
    {
        return [
            MakeTemplateCommand::class,
        ];
    }

    /** @return array<string> */
    protected function getIcons(): array
    {
        return [];
    }

    /** @return array<string, mixed> */
    protected function getScriptData(): array
    {
        return [];
    }

    /** @return array<string> */
    protected function getMigrations(): array
    {
        return [
            'create_epitre_email_templates_table',
        ];
    }
}
