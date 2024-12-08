<?php

namespace Database\Seeders;

use App\Models\Chapter;
use App\Models\Division;
use App\Models\Material;
use App\Models\Question;
use App\Models\Unit;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;

class ContentSeeder extends Seeder
{
    private $arabicFaker;

    public function __construct()
    {
        $this->arabicFaker = Faker::create('ar_SA');
    }

    public function run(): void
    {
        $faker = Faker::create();
        $content = json_decode(file_get_contents(database_path('seeders/json/Content.json')), true);

        DB::transaction(function () use ($content, $faker) {
            foreach ($content['divisions'] as $divisionData) {
                $division = Division::firstOrCreate(
                    ['name' => $divisionData['name']],
                    ['description' => $divisionData['description']]
                );

                foreach ($divisionData['materials'] as $materialData) {
                    $material = Material::create([
                        'name' => $materialData['name'],
                        'code' => $materialData['code'],
                        'color' => $materialData['color'],
                        'secondary_color' => $materialData['secondary_color'] ?? null,
                        'description' => $materialData['description'],
                        'division_id' => $division->id,
                    ]);

                    foreach ($materialData['units'] as $unitData) {
                        $unit = Unit::firstOrCreate(
                            [
                                'name' => $unitData['name'],
                                'material_id' => $material->id
                            ],
                            ['description' => $unitData['description']]
                        );

                        foreach ($unitData['chapters'] as $chapterData) {
                            $chapter = Chapter::firstOrCreate(
                                [
                                    'name' => $chapterData['name'],
                                    'unit_id' => $unit->id
                                ],
                                ['description' => $chapterData['description']]
                            );

                            // Only generate questions if the chapter was just created
                            if ($chapter->wasRecentlyCreated) {
                                $this->generateQuestionsForChapter($chapter);
                            }
                        }
                    }
                }
            }
        });
    }

    private function generateQuestionsForChapter(Chapter $chapter): void
    {
        $numQuestions = rand(5, 10);

        for ($i = 0; $i < $numQuestions; $i++) {
            $questionType = $this->getRandomQuestionType();
            $options = $this->generateQuestionOptions($questionType);

            if ($questionType === 'true_or_false') {
                $question = $this->createTrueOrFalseQuestion();
                $chapter->questions()->attach($question->id);
            } else {
                $question = Question::create([
                    'question' => rtrim($this->arabicFaker->realText(50), '.') . '؟',
                    'question_type' => $questionType,
                    'options' => $options,
                    'points' => rand(1, 5),
                    'hint' => rand(0, 1) ? rtrim($this->arabicFaker->realText(30), '.') : null,
                ]);
                $chapter->questions()->attach($question->id);
            }
        }
    }

    private function createTrueOrFalseQuestion()
    {
        $faker = Faker::create('ar_SA');

        return Question::create([
            'question' => $faker->sentence() . '؟',
            'question_type' => 'true_or_false',
            'options' => ['correct' => $faker->boolean()],
            'points' => $faker->numberBetween(1, 5),
            'hint' => $faker->optional()->sentence(),
        ]);
    }

    private function getRandomQuestionType(): string
    {
        $types = [
            'multiple_choices',
            'fill_in_the_blanks',
            'pick_the_intruder',
            'true_or_false',
            'match_with_arrows'
        ];

        return $types[array_rand($types)];
    }

    private function generateQuestionOptions(string $type): array
    {
        switch ($type) {
            case 'multiple_choices':
                $numOptions = rand(3, 4);
                $correctOption = rand(0, $numOptions - 1);
                $choices = [];

                for ($i = 0; $i < $numOptions; $i++) {
                    $choices[] = [
                        'option' => rtrim($this->arabicFaker->realText(20), '.'),
                        'is_correct' => ($i === $correctOption)
                    ];
                }
                return ['choices' => $choices];

            case 'fill_in_the_blanks':
                $numBlanks = rand(2, 3);
                $answers = [];
                $paragraph = rtrim($this->arabicFaker->realText(100), '.');
                $words_array = explode(' ', $paragraph);

                if (count($words_array) < $numBlanks) {
                    $numBlanks = count($words_array);
                }

                $positions = array_rand($words_array, $numBlanks);
                if (!is_array($positions)) {
                    $positions = [$positions];
                }

                foreach ($positions as $index => $pos) {
                    $word = $words_array[$pos];
                    $placeholder = $index + 1;
                    $answers[] = [
                        'word' => $word,
                        'placeholder' => $placeholder
                    ];
                    $words_array[$pos] = "[$placeholder]";
                }

                return [
                    'paragraph' => implode(' ', $words_array),
                    'answers' => $answers
                ];

            case 'pick_the_intruder':
                $numWords = rand(4, 5);
                $intruderIndex = rand(0, $numWords - 1);
                $words = [];

                for ($i = 0; $i < $numWords; $i++) {
                    $words[] = [
                        'word' => rtrim($this->arabicFaker->realText(15), '.'),
                        'is_intruder' => ($i === $intruderIndex)
                    ];
                }
                return ['words' => $words];

            case 'match_with_arrows':
                $numPairs = rand(2, 3);
                $pairs = [];

                for ($i = 0; $i < $numPairs; $i++) {
                    $pairs[] = [
                        'first' => rtrim($this->arabicFaker->realText(15), '.'),
                        'second' => rtrim($this->arabicFaker->realText(15), '.')
                    ];
                }
                return ['pairs' => $pairs];

            case 'true_or_false':
                return [
                    'correct' => (bool) rand(0, 1)
                ];

            default:
                return [];
        }
    }
}
