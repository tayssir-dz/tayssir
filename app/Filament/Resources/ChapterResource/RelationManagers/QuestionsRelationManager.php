<?php

namespace App\Filament\Resources\ChapterResource\RelationManagers;

use App\Filament\Resources\ChapterResource\RelationManagers\Question_types\FillInTheBlanks;
use App\Filament\Resources\ChapterResource\RelationManagers\Question_types\MatchWithArrows;
use App\Filament\Resources\ChapterResource\RelationManagers\Question_types\MultipleChoice;
use App\Filament\Resources\ChapterResource\RelationManagers\Question_types\PickTheIntruder;
use App\Filament\Resources\ChapterResource\RelationManagers\Question_types\TrueOrFalse;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

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
                Forms\Components\Tabs::make('Question creation')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make(__('custom.models.question.tabs.infos'))
                            ->schema([
                                Forms\Components\Textarea::make('question')
                                    ->required()
                                    ->label(__('custom.models.question.question')),
                                Forms\Components\TextInput::make('points')
                                    ->required()
                                    ->numeric()
                                    ->rule(['min' => 1])
                                    ->label(__('custom.models.question.points')),
                                Forms\Components\TextInput::make('hint')
                                    ->label(__('custom.models.question.hint')),
                            ]),
                        Forms\Components\Tabs\Tab::make(__('custom.models.question.tabs.assets'))
                            ->schema([
                                Forms\Components\Group::make()->schema([
                                    Forms\Components\SpatieMediaLibraryFileUpload::make('question_image')
                                        ->collection('question_image')
                                        ->image()
                                        ->label(__('custom.models.question.question_image'))
                                        ->imageEditor(),
                                    Forms\Components\SpatieMediaLibraryFileUpload::make('explaination_asset')
                                        ->collection('explaination_asset')
                                        ->label(__('custom.models.question.explaination_asset'))
                                        ->imageEditor(),
                                    Forms\Components\SpatieMediaLibraryFileUpload::make('hint_image')
                                        ->collection('hint_image')
                                        ->label(__('custom.models.question.hint_image'))
                                        ->imageEditor(),
                                ]),
                            ]),
                        Forms\Components\Tabs\Tab::make('question_type')->label(__('custom.models.question.type'))
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

                                // Question type specific components
                                TrueOrFalse::make(),
                                MultipleChoice::make(),
                                // FillInTheBlanks::make(),
                                // PickTheIntruder::make(),
                                // MatchWithArrows::make(),
                            ]),
                    ])
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
