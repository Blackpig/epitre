<?php

namespace BlackpigCreatif\Epitre\Models;

use BlackpigCreatif\Epitre\Database\Factories\EmailTemplateFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class EmailTemplate extends Model
{
    use HasFactory;
    use HasTranslations;

    protected $table = 'epitre_email_templates';

    protected $fillable = [
        'key',
        'subject',
        'body',
    ];

    public array $translatable = ['subject', 'body'];

    public function getRouteKeyName(): string
    {
        return 'key';
    }

    protected static function newFactory(): EmailTemplateFactory
    {
        return EmailTemplateFactory::new();
    }
}
