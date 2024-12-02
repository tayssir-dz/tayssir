<?php

namespace App\Filament\Resources\ChapterResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Tabs;
use File;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class QuestionsRelationManager extends RelationManager
{
    public static function getModelLabel(): string
    {
        return __('custom.models.question');
    }

    public static function getPluralModelLabel(): string
    {
        return __('custom.models.questions');
    }
    public static function getTitle($ownerRecord, string $pageClass): string
    {
        return __('custom.models.questions');
    }
    protected static string $relationship = 'questions';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Question creation')
                    ->tabs([
                        Tabs\Tab::make(__('custom.models.question.tabs.infos'))
                            ->schema([
                                Textarea::make('question')->required()->label(__('custom.models.question.question')),
                                TextInput::make('points')->required()->numeric()->rule(['min' => 1])->label(__('custom.models.question.points')),
                                TextInput::make('hint')->label(__('custom.models.question.hint')),
                            ]),
                        Tabs\Tab::make(__('custom.models.question.tabs.assets'))
                            ->schema([
                                Group::make()->schema([
                                    SpatieMediaLibraryFileUpload::make('question_image')
                                        ->collection('question_image')
                                        ->image()
                                        ->label(__('custom.models.question.question_image'))
                                        ->imageEditor(),
                                    SpatieMediaLibraryFileUpload::make('explaination_asset')
                                        ->collection('explaination_asset')
                                        ->label(__('custom.models.question.explaination_asset'))
                                        ->imageEditor(),
                                    SpatieMediaLibraryFileUpload::make('hint_image')
                                        ->collection('hint_image')
                                        ->label(__('custom.models.question.hint_image'))
                                        ->imageEditor(),
                                ]),
                            ]),
                        Tabs\Tab::make('question_type')->label(__('custom.models.question.type'))
                            ->schema([
                                Forms\Components\Select::make('question_type')
                                    ->options([
                                        'multiple_choices' => __('custom.models.question.types.multiple_choices'),
                                        'fill_in_the_blanks' => __('custom.models.question.types.fill_in_the_blanks'),
                                        'pick_the_intruder' => __('custom.models.question.types.pick_the_intruder'),
                                        'true_or_false' => __('custom.models.question.types.true_or_false'),
                                        'match_with_arrows' => __('custom.models.question.types.match_with_arrows'),
                                    ])
                                    ->required()
                                    ->live()
                                    ->label(__('custom.models.question.type'))
                                    ->afterStateUpdated(function (callable $set) {
                                        $set('options', null);
                                    }),

                                // Multiple choices options
                                Repeater::make('options')
                                    ->schema([
                                        TextInput::make('option')
                                            ->required()
                                            ->minLength(1)
                                            ->label(__('custom.models.question.option')),
                                        Toggle::make('is_correct')
                                            ->inline(false)
                                            ->label(__('custom.models.question.option.iscorrect')),
                                    ])
                                    ->columns(2)
                                    ->minItems(2)
                                    ->maxItems(6)
                                    ->columnSpanFull()
                                    ->label(__('custom.models.question.options'))
                                    ->visible(fn ($get) => $get('question_type') === 'multiple_choices')
                                    ->live()
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        if (!is_array($state)) return;

                                        // Ensure at least one option is marked as correct
                                        $hasCorrectOption = false;
                                        foreach ($state as $option) {
                                            if (isset($option['is_correct']) && $option['is_correct']) {
                                                $hasCorrectOption = true;
                                                break;
                                            }
                                        }

                                        if (!$hasCorrectOption && count($state) > 0) {
                                            $state[0]['is_correct'] = true;
                                            $set('options', $state);
                                        }
                                    }),

                                // Fill in the blanks options
                                Group::make([
                                    Textarea::make('options.paragraph')
                                        ->label(__('custom.models.question.fill_blank.paragraph'))
                                        ->required()
                                        ->helperText(__('custom.models.question.fill_blank.paragraph_help')),
                                    
                                    Repeater::make('options.answers')
                                        ->schema([
                                            TextInput::make('word')
                                                ->required()
                                                ->label(__('custom.models.question.fill_blank.word')),
                                            TextInput::make('placeholder')
                                                ->required()
                                                ->label(__('custom.models.question.fill_blank.placeholder'))
                                                ->prefix('[')
                                                ->suffix(']')
                                                ->maxLength(2)
                                                ->numeric(),
                                        ])
                                        ->defaultItems(1)
                                        ->columns(2)
                                        ->minItems(1)
                                        ->maxItems(5)
                                        ->label(__('custom.models.question.fill_blank.words'))
                                        ->collapsible()
                                ])
                                ->visible(fn ($get) => $get('question_type') === 'fill_in_the_blanks')
                                ->columnSpanFull(),

                                // Pick the intruder options
                                Repeater::make('options')
                                    ->schema([
                                        TextInput::make('word')
                                            ->required()
                                            ->minLength(1)
                                            ->label(__('custom.models.question.word')),
                                        Toggle::make('is_intruder')
                                            ->inline(false)
                                            ->label(__('custom.models.question.word.is_intruder')),
                                    ])
                                    ->columns(2)
                                    ->minItems(3)
                                    ->maxItems(8)
                                    ->columnSpanFull()
                                    ->label(__('custom.models.question.words'))
                                    ->visible(fn ($get) => $get('question_type') === 'pick_the_intruder')
                                    ->live()
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        if (!is_array($state)) return;

                                        // Ensure exactly one word is marked as intruder
                                        $intruderCount = 0;
                                        foreach ($state as $option) {
                                            if (isset($option['is_intruder']) && $option['is_intruder']) {
                                                $intruderCount++;
                                            }
                                        }

                                        if ($intruderCount !== 1 && count($state) > 0) {
                                            // Reset all to false and set the first one as intruder
                                            foreach ($state as &$option) {
                                                $option['is_intruder'] = false;
                                            }
                                            $state[0]['is_intruder'] = true;
                                            $set('options', $state);
                                        }
                                    }),

                                // True or False option
                                Group::make([
                                    Hidden::make('options.type')->default('true_false'),
                                    Select::make('options.correct')
                                        ->options([
                                            true => __('custom.models.question.true_false.true'),
                                            false => __('custom.models.question.true_false.false'),
                                        ])
                                        ->required()
                                        ->label(__('custom.models.question.true_false.correct_answer'))
                                ])
                                ->visible(fn ($get) => $get('question_type') === 'true_or_false')
                                ->columnSpanFull(),

                                // Match with arrows options
                                Repeater::make('options')
                                    ->schema([
                                        TextInput::make('first')
                                            ->required()
                                            ->minLength(1)
                                            ->label(__('custom.models.question.duo.first')),
                                        TextInput::make('second')
                                            ->required()
                                            ->minLength(1)
                                            ->label(__('custom.models.question.duo.second')),
                                    ])
                                    ->columns(2)
                                    ->minItems(2)
                                    ->maxItems(6)
                                    ->columnSpanFull()
                                    ->label(__('custom.models.question.duos'))
                                    ->visible(fn ($get) => $get('question_type') === 'match_with_arrows'),
                            ]),
                    ]),
            ])->columns(1);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('question')
            ->columns([
                Tables\Columns\TextColumn::make('question')->label(__('custom.models.question.question')),
                Tables\Columns\TextColumn::make('points')->label(__('custom.models.question.points'))
                    ->badge()
                    ->color("success"),
                Tables\Columns\TextColumn::make('question_type')
                    ->label(__('custom.models.question.type'))
                    ->badge()
                    ->color("gray")




            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
