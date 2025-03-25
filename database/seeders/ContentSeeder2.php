<?php

namespace Database\Seeders;

use App\Models\Chapter;
use App\Models\Division;
use App\Models\Material;
use App\Models\Question;
use App\Models\Unit;
use App\Models\Subscription;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;

class ContentSeeder2 extends Seeder
{
    private $arabicFaker;

    public function __construct()
    {
        $this->arabicFaker = Faker::create('ar_SA');
    }

    public function run(): void
    {
        $faker = Faker::create();

        // Check if guest subscription exists, otherwise create it
        $this->ensureGuestSubscription();

        $content = json_decode(file_get_contents(database_path('seeders/json/Content2.json')), true);

        DB::transaction(function () use ($content, $faker) {
            foreach ($content['divisions'] as $divisionData) {
                $division = Division::firstOrCreate(
                    ['name' => $divisionData['name']],
                    ['description' => $divisionData['description']]
                );

                foreach ($divisionData['materials'] as $materialData) {
                    $material = new Material([
                        'name' => $materialData['name'],
                        'code' => $materialData['code'],
                        'color' => $materialData['color'],
                        'secondary_color' => $materialData['secondary_color'] ?? null,
                        'description' => $materialData['description'],
                    ]);

                    $division->materials()->save($material);

                    foreach ($materialData['units'] as $unitData) {
                        $unit = new Unit([
                            'name' => $unitData['name'],
                            'description' => $unitData['description']
                        ]);

                        $material->units()->save($unit);

                        // Attach guest subscription to unit
                        $unit->subscriptions()->attach([
                            Subscription::GUEST_ID,
                        ]);

                        foreach ($unitData['chapters'] as $chapterData) {
                            $chapter = new Chapter([
                                'name' => $chapterData['name'],
                                'description' => $chapterData['description']
                            ]);

                            $unit->chapters()->save($chapter);

                            // Attach guest subscription to chapter
                            $chapter->subscriptions()->attach([
                                Subscription::GUEST_ID,
                            ]);

                            // Only generate questions if the chapter was just created
                            if ($chapter->wasRecentlyCreated) {
                                $this->generateQuestionsForChapter($chapter, 10); // Exactly 10 questions per chapter
                            }
                        }
                    }
                }
            }
        });
    }

    private function ensureGuestSubscription(): void
    {
        // Create guest subscription if it doesn't exist
        Subscription::firstOrCreate(
            ['id' => Subscription::GUEST_ID],
            [
                'name' => 'مجاني',
                'description' => 'اشتراك مجاني يتيح الوصول إلى المحتوى الأساسي',
                'price' => 0,
                'ending_date' => null,
            ]
        );
    }

    public function generateQuestionsForChapter(Chapter $chapter, int $numQuestions): void
    {
        for ($i = 0; $i < $numQuestions; $i++) {
            $questionType = $this->getRandomQuestionType();
            $questionScope = $this->getRandomQuestionScope();
            $options = $this->generateQuestionOptions($questionType);

            if ($questionType === 'true_or_false') {
                $question = $this->createTrueOrFalseQuestion($questionScope);
                $chapter->questions()->attach($question->id);
            } else {
                $question = Question::create([
                    'question' => rtrim($this->arabicFaker->realText(50), '.') . '؟',
                    'question_type' => $questionType,
                    'scope' => $questionScope,
                    'options' => $options,
                    'hint' => rand(0, 1) ? rtrim($this->arabicFaker->realText(30), '.') : null,
                ]);
                $chapter->questions()->attach($question->id);
            }
        }
    }

    private function createTrueOrFalseQuestion(string $questionScope)
    {
        $faker = Faker::create('ar_SA');

        return Question::create([
            'scope' => $questionScope,
            'question' => $faker->sentence() . '؟',
            'question_type' => 'true_or_false',
            'options' => ['correct' => $faker->boolean()],
            'hint' => $faker->optional()->sentence(),
            'explanation_text' => $faker->optional(0.7)->paragraph(),
        ]);
    }

    private function getRandomQuestionScope(): string
    {
        $scopes = [
            "exercice",
            'lesson'
        ];
        return $scopes[array_rand($scopes)];
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
                $blanks = [];
                $paragraph = rtrim($this->arabicFaker->realText(100), '.');
                $words_array = explode(' ', $paragraph);

                if (count($words_array) < $numBlanks) {
                    $numBlanks = count($words_array);
                }

                $positions = array_rand($words_array, $numBlanks);
                if (!is_array($positions)) {
                    $positions = [$positions];
                }

                // Generate suggestions (original words plus some additional options)
                $suggestions = [];

                foreach ($positions as $index => $pos) {
                    $word = $words_array[$pos];
                    $position = $index + 1;
                    $blanks[] = [
                        'correct_word' => $word,
                        'position' => $position
                    ];
                    $words_array[$pos] = "[$position]";
                    $suggestions[] = $word;
                }

                // Add some extra suggestions
                $extraSuggestions = rand(2, 4);
                for ($i = 0; $i < $extraSuggestions; $i++) {
                    $suggestions[] = rtrim($this->arabicFaker->word(), '.');
                }

                // Shuffle suggestions
                shuffle($suggestions);

                return [
                    'paragraph' => implode(' ', $words_array),
                    'blanks' => $blanks,
                    'suggestions' => $suggestions
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
