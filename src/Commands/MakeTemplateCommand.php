<?php

namespace BlackpigCreatif\Epitre\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class MakeTemplateCommand extends Command
{
    public $signature = 'epitre:make-template {name? : Template name without "Template" suffix (e.g. ContactConfirmation)}';

    public $description = 'Scaffold a new Épître template class and Blade view';

    public function __construct(protected Filesystem $files)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $name = $this->argument('name') ?? $this->ask('Template name? (e.g. ContactConfirmation)');

        $baseName = Str::studly(Str::beforeLast($name, 'Template') ?: $name);
        $className = $baseName . 'Template';
        $kebab = Str::kebab($baseName);
        $key = $kebab;
        $label = Str::headline($baseName);

        $classPath = app_path("BlackpigCreatif/Epitre/Templates/{$className}.php");
        $viewPath = resource_path("views/mail/epitre/{$kebab}.blade.php");

        if ($this->files->exists($classPath)) {
            $this->error("Template {$className} already exists at {$classPath}");

            return self::FAILURE;
        }

        $this->files->makeDirectory(dirname($classPath), 0755, true, true);

        $stub = $this->files->get(__DIR__ . '/../../stubs/epitre-template.stub');
        $stub = str_replace(
            ['{{ class }}', '{{ key }}', '{{ label }}', '{{ kebab }}'],
            [$className, $key, $label, $kebab],
            $stub
        );

        $this->files->put($classPath, $stub);
        $this->info("Template {$className} created at {$classPath}");

        if ($this->files->exists($viewPath)) {
            $this->warn("Blade view already exists at {$viewPath}, skipping.");
        } else {
            $this->files->makeDirectory(dirname($viewPath), 0755, true, true);
            $this->files->copy(__DIR__ . '/../../stubs/epitre-template-view.stub', $viewPath);
            $this->info("Blade view created at {$viewPath}");
        }

        $this->newLine();
        $this->line("Don't forget to register your template in your service provider:");
        $this->line("  Epitre::register({$className}::class);");

        return self::SUCCESS;
    }
}
