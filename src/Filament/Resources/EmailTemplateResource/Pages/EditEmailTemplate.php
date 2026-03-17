<?php

namespace BlackpigCreatif\Epitre\Filament\Resources\EmailTemplateResource\Pages;

use BlackpigCreatif\Epitre\Filament\Resources\EmailTemplateResource;
use BlackpigCreatif\Epitre\Models\EmailTemplate;
use Filament\Resources\Pages\EditRecord;
use LaraZeus\SpatieTranslatable\Actions\LocaleSwitcher;
use LaraZeus\SpatieTranslatable\Resources\Pages\EditRecord\Concerns\Translatable;
use Livewire\Attributes\Locked;

class EditEmailTemplate extends EditRecord
{
    use Translatable;

    protected static string $resource = EmailTemplateResource::class;

    #[Locked]
    public string $templateKey = '';

    protected function getHeaderActions(): array
    {
        return [
            LocaleSwitcher::make(),
        ];
    }

    protected function resolveRecord(int | string $key): EmailTemplate
    {
        $this->templateKey = (string) $key;

        return EmailTemplate::firstOrNew(['key' => $key]);
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (! $this->getRecord()->key) {
            $data['key'] = $this->templateKey;
        }

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
