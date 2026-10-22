<?php

namespace App\Filament\Resources\PostResource\Pages;

use App\Filament\Resources\PostResource;
use App\Models\Post;
use App\Services\GroqBlogService;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListPosts extends ListRecords
{
    protected static string $resource = PostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('view_public_blog')
                ->label('View Public Blog')
                ->icon('heroicon-o-arrow-top-right-on-square')
                ->color('gray')
                ->url('http://localhost:3000/blog')
                ->openUrlInNewTab(),

            Actions\Action::make('generate_ai_modal')
                ->label('✨ AI Write Blog')
                ->icon('heroicon-o-sparkles')
                ->color('primary')
                ->form([
                    Forms\Components\TextInput::make('topic')
                        ->label('Engineering Topic / Working Title')
                        ->placeholder('e.g. Unbonded vs Bonded PT Slab Deflection Criteria in Commercial Towers')
                        ->required()
                        ->columnSpanFull(),

                    Forms\Components\TextInput::make('primary_keywords')
                        ->label('Focus Keywords')
                        ->placeholder('e.g. PT slab design, IS 14268, tendon corrosion, deflection control')
                        ->columnSpanFull(),

                    Forms\Components\Grid::make(2)
                        ->schema([
                            Forms\Components\Select::make('target_audience')
                                ->label('Target Audience')
                                ->options([
                                    'Structural Consultants & Civil Engineers' => 'Structural Consultants & Civil Engineers',
                                    'Real Estate Developers & Project Heads' => 'Real Estate Developers & Project Heads',
                                    'EPC Contractors & Site In-Charges' => 'EPC Contractors & Site In-Charges',
                                    'Architecture & Project Management Firms' => 'Architecture & Project Management Firms',
                                ])
                                ->default('Structural Consultants & Civil Engineers')
                                ->required(),

                            Forms\Components\Select::make('tone')
                                ->label('Tone & Depth')
                                ->options([
                                    'Authoritative Technical' => 'Authoritative Technical (IS Codes & Structural Depth)',
                                    'Practical Field Guide' => 'Practical Field Guide (Site Stressing & Grouting Steps)',
                                    'Cost & Value Engineering' => 'Cost & Value Engineering (BOQ & Steel Savings)',
                                ])
                                ->default('Authoritative Technical')
                                ->required(),
                        ]),

                    Forms\Components\Grid::make(2)
                        ->schema([
                            Forms\Components\Select::make('model')
                                ->label('AI Model')
                                ->options([
                                    'openai/gpt-oss-120b' => 'GPT-OSS 120B (High Substance & Reasoning)',
                                    'openai/gpt-oss-20b' => 'GPT-OSS 20B (Fast Generation)',
                                    'qwen/qwen3.8-27b' => 'Qwen 3.8 27B (High Precision)',
                                ])
                                ->default('openai/gpt-oss-120b')
                                ->required(),

                            Forms\Components\Select::make('status')
                                ->label('Initial Status')
                                ->options([
                                    'draft' => 'Draft (Review before publishing)',
                                    'published' => 'Publish Immediately',
                                ])
                                ->default('draft')
                                ->required(),
                        ]),
                ])
                ->action(function (array $data) {
                    try {
                        /** @var GroqBlogService $service */
                        $service = app(GroqBlogService::class);
                        $generated = $service->generate($data);

                        $post = Post::create([
                            'title' => $generated['title'],
                            'slug' => $generated['slug'],
                            'meta_description' => $generated['meta_description'],
                            'content' => $generated['content'],
                            'tags' => $generated['tags'],
                            'status' => $data['status'] ?? 'draft',
                            'source' => 'ai',
                            'author' => 'Unified PT Team',
                            'published_at' => ($data['status'] ?? 'draft') === 'published' ? now() : null,
                        ]);

                        Notification::make()
                            ->title('Article Generated Successfully!')
                            ->body("Created '{$post->title}'")
                            ->success()
                            ->send();

                        return redirect(PostResource::getUrl('edit', ['record' => $post]));
                    } catch (\Throwable $e) {
                        Notification::make()
                            ->title('AI Generation Failed')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),

            Actions\CreateAction::make()
                ->label('New Post (Manual)'),
        ];
    }
}
