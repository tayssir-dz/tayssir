# Flashcard JSON Upload Examples

This document provides examples of valid JSON structures for flashcards that can be uploaded using the JSON Upload feature in the Flashcards manager.

## General Structure

Each flashcard JSON should have this basic structure:

```json
{
  "title": "Your flashcard title here",
  "description": "Your flashcard description here (optional)"
}
```

## Required Fields

The following field is required for all flashcards:
- `title` (string, minimum 3 characters, maximum 255 characters)

## Optional Fields

The following field is optional:
- `description` (string, maximum 1000 characters)

## Single Flashcard Example

```json
{
  "title": "What is the capital of France?",
  "description": "This flashcard tests knowledge of European capitals. The answer is Paris, which is located in the north-central part of France on the Seine River."
}
```

## Multiple Flashcards Example

You can upload multiple flashcards at once by wrapping them in a JSON array:

```json
[
  {
    "title": "What is the capital of France?",
    "description": "This flashcard tests knowledge of European capitals. The answer is Paris."
  },
  {
    "title": "What is the largest planet in our solar system?",
    "description": "This flashcard tests astronomy knowledge. The answer is Jupiter."
  },
  {
    "title": "Who wrote Romeo and Juliet?",
    "description": "This flashcard tests literature knowledge. The answer is William Shakespeare."
  },
  {
    "title": "What is the chemical symbol for gold?",
    "description": "This flashcard tests chemistry knowledge. The answer is Au (from the Latin word 'aurum')."
  }
]
```

## Examples by Subject

### Mathematics Flashcards

```json
[
  {
    "title": "What is the Pythagorean theorem?",
    "description": "In a right triangle, the square of the hypotenuse equals the sum of squares of the other two sides: a² + b² = c²"
  },
  {
    "title": "What is the derivative of x²?",
    "description": "The derivative of x² with respect to x is 2x."
  },
  {
    "title": "What is the value of π (pi) to 4 decimal places?",
    "description": "π ≈ 3.1416"
  }
]
```

### Science Flashcards

```json
[
  {
    "title": "What is photosynthesis?",
    "description": "The process by which plants use sunlight, carbon dioxide, and water to produce glucose and oxygen."
  },
  {
    "title": "What is the speed of light in a vacuum?",
    "description": "The speed of light in a vacuum is approximately 299,792,458 meters per second (or about 300,000 km/s)."
  },
  {
    "title": "What are the three states of matter?",
    "description": "The three common states of matter are solid, liquid, and gas."
  }
]
```

### History Flashcards

```json
[
  {
    "title": "When did World War II end?",
    "description": "World War II ended in 1945. The war in Europe ended on May 8, 1945 (VE Day), and the war in the Pacific ended on September 2, 1945 (VJ Day)."
  },
  {
    "title": "Who was the first person to walk on the moon?",
    "description": "Neil Armstrong was the first person to walk on the moon on July 20, 1969, during the Apollo 11 mission."
  },
  {
    "title": "What year did the Berlin Wall fall?",
    "description": "The Berlin Wall fell in 1989, specifically on November 9, 1989."
  }
]
```

### Language Learning Flashcards

```json
[
  {
    "title": "How do you say 'Hello' in Spanish?",
    "description": "Hello in Spanish is 'Hola' (pronounced: OH-lah)"
  },
  {
    "title": "What does 'Bonjour' mean in French?",
    "description": "'Bonjour' is French for 'Good morning' or 'Hello'"
  },
  {
    "title": "How do you say 'Thank you' in German?",
    "description": "Thank you in German is 'Danke' (pronounced: DAHN-keh)"
  }
]
```

## Minimal Examples

### Simple Flashcard with Title Only

```json
{
  "title": "What is 2 + 2?"
}
```

### Simple Array of Minimal Flashcards

```json
[
  {
    "title": "What is the capital of Spain?"
  },
  {
    "title": "Who invented the telephone?"
  },
  {
    "title": "What is the largest ocean?"
  }
]
```

## Error Handling

### Common Validation Errors

1. **Missing Title**: Every flashcard must have a title
   ```json
   {
     "description": "This will fail because title is missing"
   }
   ```

2. **Title Too Short**: Title must be at least 3 characters
   ```json
   {
     "title": "Hi"
   }
   ```

3. **Title Too Long**: Title must be maximum 255 characters
   ```json
   {
     "title": "This title is way too long and exceeds the maximum allowed length of 255 characters. Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur."
   }
   ```

4. **Description Too Long**: Description must be maximum 1000 characters
   ```json
   {
     "title": "Valid title",
     "description": "This description is too long... (imagine this continues for over 1000 characters)"
   }
   ```

## Best Practices

1. **Clear and Concise Titles**: Make titles clear and specific
2. **Useful Descriptions**: Use descriptions to provide context, explanations, or answers
3. **Consistent Format**: Maintain a consistent format across your flashcards
4. **Logical Grouping**: Group related flashcards together when uploading in batches
5. **Test Small Batches**: Start with a small batch to ensure your JSON format is correct

## Upload Process

1. Navigate to the Flashcard Group you want to add flashcards to
2. Click on the "Flashcards" tab/section
3. Click the "Upload JSON" button
4. Paste your JSON data into the editor
5. Click "Upload and Validate" to process the flashcards
6. Review any error messages and fix issues if necessary
7. Successfully uploaded flashcards will appear in the list

The system will validate each flashcard and provide detailed error messages if any issues are found, allowing you to correct them and try again.
