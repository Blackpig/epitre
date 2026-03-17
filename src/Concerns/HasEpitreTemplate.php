<?php

namespace BlackpigCreatif\Epitre\Concerns;

use BlackpigCreatif\Epitre\Epitre;
use BlackpigCreatif\Epitre\Models\EmailTemplate;
use BlackpigCreatif\Epitre\Support\EpitreTemplate;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

trait HasEpitreTemplate
{
    public function envelope(): Envelope
    {
        $template = app(Epitre::class)->find($this->epitreKey);
        $record = EmailTemplate::where('key', $this->epitreKey)->first();
        $locale = app()->getLocale();

        $subject = $this->resolveSubject($template, $record, $locale);

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        $template = app(Epitre::class)->find($this->epitreKey);
        $record = EmailTemplate::where('key', $this->epitreKey)->first();
        $locale = app()->getLocale();

        $storedBody = $record?->getTranslation('body', $locale, false);

        if ($record && ! empty($storedBody)) {
            $resolved = $template->resolve($this->epitreData());
            $html = str_replace(array_keys($resolved), array_values($resolved), $storedBody);

            if ($layout = $template->getLayout()) {
                return new Content(markdown: $layout, with: array_merge($this->epitreData(), ['body' => $html]));
            }

            return new Content(htmlString: $html);
        }

        return new Content(view: $template->getView(), with: $this->epitreData());
    }

    private function resolveSubject(?EpitreTemplate $template, ?EmailTemplate $record, string $locale): string
    {
        $storedSubject = $record?->getTranslation('subject', $locale, false);

        if ($record && ! empty($storedSubject) && $template) {
            $resolved = $template->resolve($this->epitreData());

            return str_replace(array_keys($resolved), array_values($resolved), $storedSubject);
        }

        return $template?->getLabel() ?? $this->epitreKey;
    }
}
