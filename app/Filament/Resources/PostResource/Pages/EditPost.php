<?php

namespace App\Filament\Resources\PostResource\Pages;

use App\Filament\Resources\PostResource;
use App\Services\GroqBlogService;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditPost extends EditRecord
{
    protected static string $resource = PostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('regenerate_ai')
                ->label('✨ Rewrite / Expand with AI')
                ->icon('heroicon-o-arrow-path')
                ->color('warning')
                ->modalHeading('AI Content Rewriter & Expander')
                ->modalDescription('Generate a fresh, in-depth technical draft for this topic.')
                ->modalSubmitActionLabel('Replace Content in Editor')
                ->form([
                    Forms\Components\TextInput::make('topic')
                        ->label('Topic / Title')
                        ->default(fn () => $this->record->title)
                        ->required()
                        ->columnSpanFull(),

                    Forms\Components\TextInput::make('primary_keywords')
                        ->label('Focus Keywords')
                        ->default(fn () => is_array($this->record->tags) ? implode(', ', $this->record->tags) : '')
                        ->columnSpanFull(),

                    Forms\Components\Select::make('tone')
                        ->label('Tone & Depth')
                        ->options([
                            'Authoritative Technical' => 'Authoritative Technical',
                            'Practical Field Guide' => 'Practical Field Guide',
                            'Cost & Value Engineering' => 'Cost & Value Engineering',
                        ])
                        ->default('Authoritative Technical')
                        ->required(),
                ])
                ->action(function (array $data) {
                    try {
                        /** @var GroqBlogService $service */
                        $service = app(GroqBlogService::class);
                        $generated = $service->generate($data);

                        $this->form->fill([
                            'title' => $generated['title'],
                            'slug' => $this->record->slug, // keep existing slug to preserve URLs
                            'meta_description' => $generated['meta_description'],
                            'content' => $generated['content'],
                            'tags' => array_unique(array_merge($this->record->tags ?? [], $generated['tags'])),
                            'status' => $this->record->status,
                            'source' => 'ai-edited',
                            'author' => $this->record->author,
                            'published_at' => $this->record->published_at,
                        ]);

                        Notification::make()
                            ->title('Content Rewritten with AI!')
                            ->body('Review the changes in the editor and click Save Changes.')
                            ->success()
                            ->send();
                    } catch (\Throwable $e) {
                        Notification::make()
                            ->title('Rewrite Failed')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),

            Actions\Action::make('preview_live')
                ->label('View on Site')
                ->icon('heroicon-o-arrow-top-right-on-square')
                ->color('gray')
                ->url(fn (): string => 'http://localhost:3000/blog/' . $this->record->slug)
                ->openUrlInNewTab(),

            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
