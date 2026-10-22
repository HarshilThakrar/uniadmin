<?php

namespace App\Filament\Resources\PostResource\Pages;

use App\Filament\Resources\PostResource;
use App\Services\GroqBlogService;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreatePost extends CreateRecord
{
    protected static string $resource = PostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('autofill_ai')
                ->label('✨ Generate & Fill Form with AI')
                ->icon('heroicon-o-sparkles')
                ->color('info')
                ->modalHeading('AI Article Generator (Groq Direct)')
                ->modalDescription('Generate an article using AI and populate the fields below for editing.')
                ->modalSubmitActionLabel('Generate & Autofill')
                ->form([
                    Forms\Components\TextInput::make('topic')
                        ->label('Engineering Topic')
                        ->placeholder('e.g. Tendon Elongation Measurement & Calibration in PT Slabs')
                        ->required()
                        ->columnSpanFull(),

                    Forms\Components\TextInput::make('primary_keywords')
                        ->label('Focus Keywords')
                        ->placeholder('e.g. hydraulic jack calibration, IS 1343, elongation tolerance')
                        ->columnSpanFull(),

                    Forms\Components\Grid::make(2)
                        ->schema([
                            Forms\Components\Select::make('target_audience')
                                ->label('Target Audience')
                                ->options([
                                    'Structural Consultants & Civil Engineers' => 'Structural Consultants & Civil Engineers',
                                    'Real Estate Developers & Project Heads' => 'Real Estate Developers & Project Heads',
                                    'EPC Contractors & Site In-Charges' => 'EPC Contractors & Site In-Charges',
                                ])
                                ->default('Structural Consultants & Civil Engineers')
                                ->required(),

                            Forms\Components\Select::make('tone')
                                ->label('Tone')
                                ->options([
                                    'Authoritative Technical' => 'Authoritative Technical',
                                    'Practical Field Guide' => 'Practical Field Guide',
                                    'Cost & Value Engineering' => 'Cost & Value Engineering',
                                ])
                                ->default('Authoritative Technical')
                                ->required(),
                        ]),
                ])
                ->action(function (array $data) {
                    try {
                        /** @var GroqBlogService $service */
                        $service = app(GroqBlogService::class);
                        $generated = $service->generate($data);

                        $this->form->fill([
                            'title' => $generated['title'],
                            'slug' => $generated['slug'],
                            'meta_description' => $generated['meta_description'],
                            'content' => $generated['content'],
                            'tags' => $generated['tags'],
                            'status' => 'draft',
                            'source' => 'ai',
                            'author' => 'Unified PT Team',
                            'published_at' => now(),
                        ]);

                        Notification::make()
                            ->title('Form Populated with AI Article!')
                            ->body('Review the generated content in the editor below and click Create when ready.')
                            ->success()
                            ->send();
                    } catch (\Throwable $e) {
                        Notification::make()
                            ->title('AI Autofill Failed')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
