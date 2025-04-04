<?php

namespace App\Filament\Resources\ChapterResource\RelationManagers;

use App\Enums\ContentDirection;
use App\Enums\QuestionScope;
use App\Enums\QuestionType;
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
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Model;
use Log;

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
                                Forms\Components\Group::make()
                                    ->schema([
                                        Forms\Components\Textarea::make('question')
                                            ->rows(1)
                                            ->required()
                                            ->columnSpan(8)
                                            ->label(__('custom.models.question.question')),
                                        Forms\Components\Toggle::make('question_is_latex')
                                            ->label(__('custom.models.question.is_latex'))
                                            ->inline(false)
                                            ->columnSpan(2)
                                            ->default(false),
                                    ])
                                    ->columns(10),

                                Forms\Components\Select::make('direction')->native(false)
                                    ->options(ContentDirection::class)
                                    ->enum(ContentDirection::class)
                                    ->default(ContentDirection::INHERIT)
                                    ->required()
                                    ->label(__('custom.direction.label')),

                                Forms\Components\Select::make('scope')
                                    ->enum(QuestionScope::class)
                                    ->options(QuestionScope::class)
                                    ->required()
                                    ->default(QuestionScope::LESSON)
                                    ->label(__('custom.models.question.scope')),

                                Forms\Components\Repeater::make('hint')
                                    ->schema([
                                        Forms\Components\Group::make()
                                            ->schema([
                                                Forms\Components\Textarea::make('value')
                                                    ->rows(1)
                                                    ->columnSpan(8)
                                                    ->label(__('custom.models.question.hint')),
                                                Forms\Components\Toggle::make('is_latex')
                                                    ->label(__('custom.models.question.is_latex'))
                                                    ->inline(false)
                                                    ->columnSpan(2)
                                                    ->default(false),
                                            ])
                                            ->columns(10),
                                    ])
                                    ->defaultItems(0)
                                    ->label(__('custom.models.question.hint')),

                                Forms\Components\Group::make()
                                    ->schema([
                                        Forms\Components\Textarea::make('explanation_text')
                                            ->rows(1)
                                            ->columnSpan(8)
                                            ->label(__('custom.models.question.explanation_text')),
                                        Forms\Components\Toggle::make('explanation_text_is_latex')
                                            ->label(__('custom.models.question.is_latex'))
                                            ->inline(false)
                                            ->columnSpan(2)
                                            ->default(false),
                                    ])
                                    ->columns(10),
                            ]),
                        Forms\Components\Tabs\Tab::make(__('custom.models.question.tabs.assets'))
                            ->schema([
                                Forms\Components\Group::make()->schema([
                                    Forms\Components\SpatieMediaLibraryFileUpload::make('question_image')
                                        ->collection('image')
                                        ->multiple(false)
                                        ->image()
                                        ->label(__('custom.models.question.question_image'))
                                        ->imageEditor(),
                                    Forms\Components\SpatieMediaLibraryFileUpload::make('explaination_asset')
                                        ->collection('explaination_asset')
                                        ->multiple(false)
                                        ->label(__('custom.models.question.explaination_asset')),
                                    Forms\Components\SpatieMediaLibraryFileUpload::make('hint_image')
                                        ->collection('hint_image')
                                        ->multiple(false)
                                        ->label(__('custom.models.question.hint_image'))
                                        ->imageEditor(),
                                ]),
                            ]),
                        Forms\Components\Tabs\Tab::make('question_type')->label(__('custom.models.question.type'))
                            ->schema([
                                Forms\Components\Select::make('question_type')
                                    ->enum(QuestionType::class)
                                    ->options(QuestionType::class)
                                    ->required()
                                    ->live()
                                    ->label(__('custom.models.question.type'))
                                    ->afterStateUpdated(function (callable $set, $state) {
                                        if ($state === QuestionType::TRUE_OR_FALSE->value) {
                                            $set('options', ['correct' => false]);
                                        } elseif ($state === QuestionType::FILL_IN_THE_BLANKS->value) {
                                            $set('options', [
                                                'paragraph' => '',
                                                'blanks' => [],
                                                'suggestions' => []
                                            ]);
                                        } else {
                                            $set('options', null);
                                        }
                                    }),

                                // Question type specific components
                                TrueOrFalse::make(),
                                MultipleChoice::make(),
                                FillInTheBlanks::make(),
                                PickTheIntruder::make(),
                                MatchWithArrows::make(),
                            ]),
                    ])
            ])->columns(1);
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (!isset($data['options'])) {
            $data['options'] = [];
        }

        $questionType = match ($data['question_type']) {
            QuestionType::TRUE_OR_FALSE->value => TrueOrFalse::class,
            QuestionType::MULTIPLE_CHOICES->value => MultipleChoice::class,
            QuestionType::FILL_IN_THE_BLANKS->value => FillInTheBlanks::class,
            QuestionType::PICK_THE_INTRUDER->value => PickTheIntruder::class,
            QuestionType::MATCH_WITH_ARROWS->value => MatchWithArrows::class,
            default => null,
        };

        if ($questionType) {
            $questionType::saveFormState($this->record, $data);
        }

        return $data;
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Ensure options is set before creation
        $questionType = match ($data['question_type']) {
            QuestionType::TRUE_OR_FALSE->value => TrueOrFalse::class,
            QuestionType::MULTIPLE_CHOICES->value => MultipleChoice::class,
            QuestionType::FILL_IN_THE_BLANKS->value => FillInTheBlanks::class,
            QuestionType::PICK_THE_INTRUDER->value => PickTheIntruder::class,
            QuestionType::MATCH_WITH_ARROWS->value => MatchWithArrows::class,
            default => null,
        };

        if ($questionType) {
            $data['options'] = $questionType::getDefaultOptions($data);
        }

        return $data;
    }

    public function table(Table $table): Table
    {
        return $table
            ->paginated(false)
            ->reorderable('sort')
            ->recordTitleAttribute('question')
            ->columns([
                // Tables\Columns\IconColumn::make('Rtl')->boolean(),

                // Tables\Columns\TextColumn::make('id'),
                Tables\Columns\TextColumn::make('sort'),
                Tables\Columns\TextColumn::make('question')->label(__('custom.models.question.question')),
                Tables\Columns\TextColumn::make('question_type')
                    ->label(__('custom.models.question.type'))
                    ->badge()
                    ->formatStateUsing(fn(QuestionType $state) => $state->getLabel())
                    ->color(fn(QuestionType $state) => $state->getColor()),
                Tables\Columns\TextColumn::make('scope')
                    ->label(__('custom.models.question.scope'))
                    ->badge()
                    ->formatStateUsing(fn(QuestionScope $state) => $state->getLabel())
                    ->color(fn(QuestionScope $state) => $state->getColor()),

                Tables\Columns\TextColumn::make('points')
                    ->label(__('custom.models.question.points'))
                    ->badge(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                ]),
            ]);
    }
}
