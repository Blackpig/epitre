@php
    $record = $getLivewire()->record;
    $tokens = [];

    if ($record && $record->key) {
        $template = app(\BlackpigCreatif\Epitre\Epitre::class)->find($record->key);
        $tokens = $template?->getTokens() ?? [];
    }
@endphp

<div class="text-sm font-semibold text-gray-950 dark:text-white mb-4">Available Tokens</div>

@if (empty($tokens))
    <p class="text-sm text-gray-500">No tokens defined for this template.</p>
@else
    <dl>
        @foreach ($tokens as $token => $description)
            <dt class="font-mono text-sm font-medium text-gray-950 dark:text-white">{{ $token }}</dt>
            <dd class="text-sm text-gray-500 mt-0.5 mb-3">{{ $description }}</dd>
        @endforeach
    </dl>
@endif
