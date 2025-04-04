<?php

namespace App\Enums;

enum ChapterVisibility: string
{
    case DONE = 'done';
    case CURRENT = 'current';
    case LOCKED = 'locked';
}
