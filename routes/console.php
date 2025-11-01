<?php

use App\Console\Commands\DeleteTempFiles;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use Spatie\Health\Commands\RunHealthChecksCommand;

Schedule::command('otp:clean')->daily();
Schedule::command('sanctum:prune-expired')->daily();
Schedule::command('media-library:clean')->daily();
Schedule::command('models:prune')->daily();
Schedule::command('auth:clear-resets')->daily();
Schedule::command(RunHealthChecksCommand::class)->daily();
Schedule::command(DeleteTempFiles::class)->daily();
