# Flashcard JSON Upload Feature - Test Examples

## Test Data for Quick Testing

Copy and paste these JSON examples to test the flashcard upload feature:

### Example 1: Single Flashcard
```json
{
  "title": "What is the capital of Algeria?",
  "description": "Algeria is a country in North Africa. Its capital and largest city is Algiers, located on the Mediterranean coast."
}
```

### Example 2: Multiple Flashcards - Basic
```json
[
  {
    "title": "What is 2 + 2?",
    "description": "Basic arithmetic: 2 + 2 = 4"
  },
  {
    "title": "What is the chemical symbol for water?",
    "description": "Water's chemical formula is H2O (two hydrogen atoms and one oxygen atom)"
  },
  {
    "title": "Who painted the Mona Lisa?",
    "description": "The Mona Lisa was painted by Leonardo da Vinci during the Italian Renaissance (1503-1506)"
  }
]
```

### Example 3: Subject-Specific Flashcards (Mathematics)
```json
[
  {
    "title": "What is the quadratic formula?",
    "description": "x = (-b ± √(b²-4ac)) / 2a, used to solve quadratic equations of the form ax² + bx + c = 0"
  },
  {
    "title": "What is the area formula for a circle?",
    "description": "A = πr², where r is the radius of the circle"
  },
  {
    "title": "What is the Fibonacci sequence?",
    "description": "A sequence where each number is the sum of the two preceding ones: 0, 1, 1, 2, 3, 5, 8, 13, 21, 34..."
  }
]
```

### Example 4: Minimal Flashcards (Title Only)
```json
[
  {
    "title": "What is photosynthesis?"
  },
  {
    "title": "Who wrote '1984'?"
  },
  {
    "title": "What is the speed of light?"
  }
]
```

## Error Testing Examples

### Example 5: Invalid - Missing Title
```json
{
  "description": "This will fail validation because title is missing"
}
```

### Example 6: Invalid - Title Too Short
```json
{
  "title": "Hi",
  "description": "This will fail because title is less than 3 characters"
}
```

### Example 7: Mixed Valid and Invalid
```json
[
  {
    "title": "Valid flashcard",
    "description": "This one will work"
  },
  {
    "description": "This will fail - no title"
  },
  {
    "title": "Another valid one"
  }
]
```

The system will process valid flashcards and report errors for invalid ones.
