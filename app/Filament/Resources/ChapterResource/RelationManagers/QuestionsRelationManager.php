<?php

namespace App\Filament\Resources\ChapterResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
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
                                Forms\Components\Select::make('question_type')->options([
                                    'multiple_choices' => __('custom.models.question.types.multiple_choices'),
                                    'fill_in_the_blanks' => __('custom.models.question.types.fill_in_the_blanks'),
                                    'pick_the_intruder' => __('custom.models.question.types.pick_the_intruder'),
                                    'true_or_false' => __('custom.models.question.types.true_or_false'),
                                    'match_with_arrows' => __('custom.models.question.types.match_with_arrows'),
                                ])->required()->live()->label(__('custom.models.question.type'))
                                    ->afterStateUpdated(function ($set) {
                                        $set("options", null);
                                    }),

                                // multiple choices
                                Repeater::make("options")->afterStateUpdated(function ($get, $set) {
                                    $options = $get("options");
                                    $options = array_map(function ($option) {
                                        return array_filter($option, function ($value) {
                                            return $value != null;
                                        });
                                    }, $options);
                                    $set("options", $options);
                                })
                                    ->label(__('custom.models.question.options'))
                                    ->schema([
                                        TextInput::make('option')->required()->minLength(3)->columnSpan(3)->label(__('custom.models.question.option')),
                                        Toggle::make('is_correct')->inline(false)->label(__('custom.models.question.option.iscorrect')),
                                    ])->visible(fn($get) => $get('question_type') === 'multiple_choices')
                                    ->columns(4)->reorderableWithDragAndDrop(false),

                                // fill in the blanks
                                Repeater::make("options")->schema([
                                    TextInput::make('fill_in_the_blanks')
                                ])->visible(fn($get) => $get('question_type') === 'fill_in_the_blanks'),

                                // pick the intruder
                                Repeater::make("options")->afterStateUpdated(function ($get, $set) {
                                    $options = $get("options");
                                    $options = array_map(function ($option) {
                                        return array_filter($option, function ($value) {
                                            return $value != null;
                                        });
                                    }, $options);
                                    $set("options", $options);
                                })
                                    ->label(__('custom.models.question.words'))
                                    ->schema([
                                        TextInput::make('option')->required()->minLength(3)->columnSpan(3)->label(__('custom.models.question.word')),
                                        Toggle::make('is_intruder')->inline(false)->label(__('custom.models.question.word.is_intruder')),
                                    ])->visible(fn($get) => $get('question_type') === 'pick_the_intruder')
                                    ->columns(4)->reorderableWithDragAndDrop(false),


                                ToggleButtons::make('options')
                                    ->afterStateUpdated(function ($get, $set) {
                                        $options = $get("options");
                                        $set("options", ["correct" => $options]);
                                    })
                                    ->label(__('custom.models.question.option.iscorrect'))
                                    ->grouped()
                                    ->options([
                                        'true' => 'صحيح',
                                        'false' => "خطا"
                                    ])
                                    ->inline()
                                    ->visible(fn($get) => $get('question_type') === 'true_or_false'),

                                Repeater::make("options")->afterStateUpdated(function ($get, $set) {
                                    $options = $get("options");
                                    $options = array_map(function ($option) {
                                        return array_filter($option, function ($value) {
                                            return $value != null;
                                        });
                                    }, $options);
                                    $set("options", $options);
                                })
                                    ->label(__('custom.models.question.duos'))
                                    ->schema([
                                        TextInput::make('first')
                                            ->required()
                                            ->minLength(3)
                                            ->columnSpan(2)
                                            ->label(__('custom.models.question.duo.first')),

                                        TextInput::make('second')
                                            ->required()
                                            ->minLength(3)
                                            ->columnSpan(2)
                                            ->label(__('custom.models.question.duo.second')),

                                    ])->visible(fn($get) => $get('question_type') === 'match_with_arrows')
                                    ->columns(4)->reorderableWithDragAndDrop(false),
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
