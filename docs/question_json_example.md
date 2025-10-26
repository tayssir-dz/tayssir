# Question JSON Upload Examples

This document provides examples of valid JSON structures for each question type that can be uploaded using the JSON Upload feature in the Questions manager.

## General Structure

Each question JSON should have this basic structure:

```json
{
  "question": "Your question text here",
  "hint": [
    {
      "value": "Hint text",
      "is_latex": false
    }
  ],
  "explanation_text": "Explanation about the answer",
  "question_type": "one_of_the_valid_types",
  "scope": "exercice|lesson",
  "direction": "rtl|ltr|inherit",
  "question_is_latex": false,
  "explanation_text_is_latex": false,
  "options": {}
}
```

The `options` object structure depends on the question type as shown in the examples below.

## Question Types Examples

### 1. True or False

```json
{
  "question": "Is the Earth round?",
  "hint": [
    {
      "value": "Think about what you've learned in geography class",
      "is_latex": false
    }
  ],
  "explanation_text": "The Earth is approximately spherical, though it bulges at the equator.",
  "question_type": "true_or_false",
  "scope": "lesson",
  "direction": "ltr",
  "question_is_latex": false,
  "explanation_text_is_latex": false,
  "options": {
    "correct": true
  }
}
```

### 2. Multiple Choice

```json
{
  "question": "Which of these is a prime number?",
  "explanation_text": "A prime number is a natural number greater than 1 that is not a product of two natural numbers other than 1 and itself.",
  "question_type": "multiple_choices",
  "scope": "exercice",
  "direction": "ltr",
  "question_is_latex": false,
  "explanation_text_is_latex": false,
  "options": {
    "choices": [
      {
        "option": "4",
        "is_correct": false,
        "option_is_latex": false
      },
      {
        "option": "11",
        "is_correct": true,
        "option_is_latex": false
      },
      {
        "option": "15",
        "is_correct": false,
        "option_is_latex": false
      },
      {
        "option": "21",
        "is_correct": false,
        "option_is_latex": false
      }
    ]
  }
}
```

### 3. Fill in the Blanks

```json
{
  "question": "Complete the sentence with appropriate words",
  "explanation_text": "Paris is the capital of France and is famous for the Eiffel Tower.",
  "question_type": "fill_in_the_blanks",
  "scope": "exercice",
  "direction": "ltr",
  "question_is_latex": false,
  "explanation_text_is_latex": false,
  "options": {
    "paragraph": "[1] is the capital of [2] and is famous for the [3].",
    "blanks": [
      {
        "correct_word": "Paris",
        "position": 1
      },
      {
        "correct_word": "France",
        "position": 2
      },
      {
        "correct_word": "Eiffel Tower",
        "position": 3
      }
    ],
    "suggestions": [
      "Paris",
      "London",
      "France",
      "Italy",
      "Eiffel Tower",
      "Big Ben"
    ]
  }
}
```

### 4. Pick the Intruder

```json
{
  "question": "Which of these doesn't belong in the group?",
  "explanation_text": "Apple, banana, and orange are fruits, while carrot is a vegetable.",
  "question_type": "pick_the_intruder",
  "scope": "lesson",
  "direction": "ltr",
  "question_is_latex": false,
  "explanation_text_is_latex": false,
  "options": {
    "words": [
      {
        "word": "Apple",
        "is_intruder": false,
        "word_is_latex": false
      },
      {
        "word": "Banana",
        "is_intruder": false,
        "word_is_latex": false
      },
      {
        "word": "Carrot",
        "is_intruder": true,
        "word_is_latex": false
      },
      {
        "word": "Orange",
        "is_intruder": false,
        "word_is_latex": false
      }
    ]
  }
}
```

### 5. Match with Arrows

```json
{
  "question": "Match each country with its capital city",
  "explanation_text": "Each country has a unique capital city as listed.",
  "question_type": "match_with_arrows",
  "scope": "lesson",
  "direction": "ltr",
  "question_is_latex": false,
  "explanation_text_is_latex": false,
  "options": {
    "pairs": [
      {
        "first": "France",
        "second": "Paris",
        "first_is_latex": false,
        "second_is_latex": false
      },
      {
        "first": "Japan",
        "second": "Tokyo",
        "first_is_latex": false,
        "second_is_latex": false
      },
      {
        "first": "Egypt",
        "second": "Cairo",
        "first_is_latex": false,
        "second_is_latex": false
      },
      {
        "first": "Australia",
        "second": "Canberra",
        "first_is_latex": false,
        "second_is_latex": false
      }
    ]
  }
}
```

## LaTeX Examples

For questions that include mathematical formulas, you can set the `question_is_latex`, `explanation_text_is_latex`, or the specific content's LaTeX indicator to `true`.

### Example with LaTeX:

```json
{
  "question": "Solve the equation: \\( x^2 + 5x + 6 = 0 \\)",
  "explanation_text": "Using the quadratic formula: \\( x = \\frac{-b \\pm \\sqrt{b^2-4ac}}{2a} \\) where a=1, b=5, c=6. This gives x=-2 or x=-3.",
  "question_type": "multiple_choices",
  "scope": "exercice",
  "direction": "ltr",
  "question_is_latex": true,
  "explanation_text_is_latex": true,
  "options": {
    "choices": [
      {
        "option": "\\( x = -1, x = -6 \\)",
        "is_correct": false,
        "option_is_latex": true
      },
      {
        "option": "\\( x = -2, x = -3 \\)",
        "is_correct": true,
        "option_is_latex": true
      },
      {
        "option": "\\( x = 2, x = 3 \\)",
        "is_correct": false,
        "option_is_latex": true
      },
      {
        "option": "\\( x = 1, x = 6 \\)",
        "is_correct": false,
        "option_is_latex": true
      }
    ]
  }
}
```

## Uploading Multiple Questions

You can upload multiple questions at once by wrapping them in a JSON array:

```json
[
  {
    "question": "First question...",
    "question_type": "true_or_false",
    ...
  },
  {
    "question": "Second question...",
    "question_type": "multiple_choices",
    ...
  }
]
```

## Required Fields

The following fields are required for all question types:
- `question`
- `question_type`
- `scope`
- `direction`
- `options` (structure depends on question type)

Optional fields:
- `hint`
- `explanation_text`
- `question_is_latex` (defaults to false)
- `explanation_text_is_latex` (defaults to false)