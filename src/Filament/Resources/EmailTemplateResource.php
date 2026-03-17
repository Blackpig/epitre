<?php

namespace BlackpigCreatif\Epitre\Filament\Resources;

use BlackpigCreatif\Epitre\Epitre;
use BlackpigCreatif\Epitre\Filament\Resources\EmailTemplateResource\Pages\EditEmailTemplate;
use BlackpigCreatif\Epitre\Filament\Resources\EmailTemplateResource\Pages\ListEmailTemplates;
use BlackpigCreatif\Epitre\Models\EmailTemplate;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\View;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use LaraZeus\SpatieTranslatable\Resources\Concerns\Translatable;

class EmailTemplateResource extends Resource
{
    use Translatable;

    protected static ?string $model = EmailTemplate::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-envelope';

    protected static ?string $navigationLabel = 'Email Templates';

    protected static ?string $pluralModelLabel = 'Email Templates';

    protected static ?string $modelLabel = 'Email Template';

    protected static ?string $recordTitleAttribute = 'key';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Grid::make()->columns(4)->columnSpanFull()->schema([
                Section::make()
                    ->columnSpan(3)
                    ->schema([
                        TextInput::make('subject')
                            ->label('Subject')
                            ->nullable()
                            ->columnSpanFull(),

                        RichEditor::make('body')
                            ->label('Body')
                            ->toolbarButtons([
                                'bold',
                                'italic',
                                'underline',
                                'strike',
                                'link',
                                'bulletList',
                                'orderedList',
                                'blockquote',
                                'undo',
                                'redo',
                            ])
                            ->nullable()
                            ->columnSpanFull(),
                    ]),

                Section::make()
                    ->columnSpan(1)
                    ->schema([
                        View::make('epitre::filament.token-list'),
                    ]),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->paginated(false)
            ->emptyStateHeading('No templates registered')
            ->emptyStateDescription('Use Epitre::register() in your service provider.')
            ->columns([
                TextColumn::make('label')
                    ->label('Template')
                    ->state(fn (EmailTemplate $record): string => app(Epitre::class)->find($record->key)?->getLabel() ?? $record->key)
                    ->searchable(false)
                    ->sortable(false),

                TextColumn::make('key')
                    ->label('Key')
                    ->copyable()
                    ->searchable(false)
                    ->sortable(false),

                TextColumn::make('status')
                    ->label('Status')
                    ->state(fn (EmailTemplate $record): string => $record->exists ? 'Customised' : 'Using default')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Customised' => 'success',
                        default => 'gray',
                    }),

                TextColumn::make('updated_at')
                    ->label('Last edited')
                    ->since()
                    ->placeholder('Never')
                    ->sortable(false),
            ])
            ->recordActions([
                EditAction::make(),

                Action::make('resetToDefault')
                    ->label('Reset to default')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->color('danger')
                    ->visible(fn (EmailTemplate $record): bool => $record->exists)
                    ->requiresConfirmation()
                    ->modalHeading('Reset to default?')
                    ->modalDescription('This will delete your saved changes and restore the Blade view default. This cannot be undone.')
                    ->modalSubmitActionLabel('Yes, reset it')
                    ->action(function (EmailTemplate $record): void {
                        $record->delete();
                    })
                    ->successNotificationTitle('Template reset to default'),
            ])
            ->headerActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListEmailTemplates::route('/'),
            'edit' => EditEmailTemplate::route('/{record}/edit'),
        ];
    }
}
