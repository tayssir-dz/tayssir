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
                    $material = Material::firstOrCreate(
                        [
                            'name' => $materialData['name'],
                            'division_id' => $division->id
                        ],
                        [
                            'code' => $materialData['code'],
                            'color' => $materialData['color'],
                            'description' => $materialData['description'],
                        ]
                    );

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
            
            Question::create([
                'question' => $this->arabicFaker->sentence() . '؟',
                'question_type' => $questionType,
                'options' => $options,
                'points' => rand(1, 5),
                'hint' => rand(0, 1) ? $this->arabicFaker->sentence() : null,
                'chapter_id' => $chapter->id,
            ]);
        }
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
                $numOptions = rand(3, 5);
                $correctOption = rand(0, $numOptions - 1);
                $options = [];
                
                for ($i = 0; $i < $numOptions; $i++) {
                    $options[] = [
                        'option' => $this->arabicFaker->unique()->word(),
                        'is_correct' => ($i === $correctOption)
                    ];
                }
                $this->arabicFaker->unique(true);
                return $options;

            case 'fill_in_the_blanks':
                $numBlanks = rand(1, 3);
                $words = [];
                $paragraph = $this->arabicFaker->paragraph();
                
                for ($i = 0; $i < $numBlanks; $i++) {
                    $word = $this->arabicFaker->word();
                    $placeholder = $i + 1;
                    $words[] = [
                        'word' => $word,
                        'placeholder' => $placeholder
                    ];
                    $paragraph = preg_replace('/\b' . preg_quote($word, '/') . '\b/u', "[$placeholder]", $paragraph, 1);
                }
                
                return [
                    'paragraph' => $paragraph,
                    'answers' => $words
                ];

            case 'true_or_false':
                return [
                    'type' => 'true_false',
                    'correct' => (bool)rand(0, 1)
                ];

            case 'pick_the_intruder':
                $numWords = rand(4, 6);
                $intruderIndex = rand(0, $numWords - 1);
                $words = [];
                
                for ($i = 0; $i < $numWords; $i++) {
                    $words[] = [
                        'word' => $this->arabicFaker->unique()->word(),
                        'is_intruder' => ($i === $intruderIndex)
                    ];
                }
                $this->arabicFaker->unique(true);
                return $words;

            case 'match_with_arrows':
                $numPairs = rand(2, 4);
                $pairs = [];
                
                for ($i = 0; $i < $numPairs; $i++) {
                    $pairs[] = [
                        'first' => $this->arabicFaker->unique()->word(),
                        'second' => $this->arabicFaker->unique()->word()
                    ];
                }
                $this->arabicFaker->unique(true);
                return $pairs;

            default:
                return [];
        }
    }
}
