<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Schedule::command('otp:clean')->daily();
Schedule::command('sanctum:prune-expired')->daily();
Schedule::command('media-library:clean')->daily();
