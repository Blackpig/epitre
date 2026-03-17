<?php

namespace BlackpigCreatif\Epitre\Filament\Resources\EmailTemplateResource\Pages;

use BlackpigCreatif\Epitre\Epitre;
use BlackpigCreatif\Epitre\Filament\Resources\EmailTemplateResource;
use BlackpigCreatif\Epitre\Models\EmailTemplate;
use BlackpigCreatif\Epitre\Support\EpitreTemplate;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use LaraZeus\SpatieTranslatable\Resources\Pages\ListRecords\Concerns\Translatable;

class ListEmailTemplates extends ListRecords
{
    use Translatable;

    protected static string $resource = EmailTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    public function getTableRecordKey(Model | array $record): string
    {
        if (is_array($record)) {
            return parent::getTableRecordKey($record);
        }

        return (string) $record->key;
    }

    protected function resolveTableRecord(?string $key): Model | array | null
    {
        if ($key === null) {
            return null;
        }

        return $this->getTableRecords()->first(fn (EmailTemplate $record): bool => $record->key === $key);
    }

    public function getTableRecords(): Collection
    {
        return collect(app(Epitre::class)->allInstances())
            ->map(fn (EpitreTemplate $template): EmailTemplate => EmailTemplate::firstOrNew(['key' => $template->getKey()]));
    }
}
