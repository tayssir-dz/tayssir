<?php

use App\Models\User;
use App\Services\GoogleAuthService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

it('logs in an existing google user and refreshes profile info', function () {
    Role::firstOrCreate(['name' => 'student']);

    $user = User::factory()->create([
        'email' => 'loginuser@example.com',
        'google_id' => 'sub-abc',
        'name' => 'Old Name',
        'password' => null,
    ]);

    $payload = [
        'email' => 'loginuser@example.com',
        'sub' => 'sub-abc',
        'name' => 'New Name',
        'picture' => 'https://example.com/new.png',
    ];

    $this->mock(GoogleAuthService::class, function ($mock) use ($payload) {
        $mock->shouldReceive('verifyIdToken')->once()->andReturn($payload);
    });

    $response = $this->postJson('/api/v1/auth/google/login', [
        'id_token' => 'token-login',
    ]);

    $response->assertStatus(200)
        ->assertJsonStructure(['data' => ['token', 'refresh_token']]);

    $user->refresh();
    expect($user->name)->toBe('New Name');
    expect($user->avatar_url)->toBe('https://example.com/new.png');
});

it('fails login if account does not exist', function () {
    $payload = [
        'email' => 'notfound@example.com',
        'sub' => 'missing-sub',
        'name' => 'Ghost User',
    ];

    $this->mock(GoogleAuthService::class, function ($mock) use ($payload) {
        $mock->shouldReceive('verifyIdToken')->once()->andReturn($payload);
    });

    $response = $this->postJson('/api/v1/auth/google/login', [
        'id_token' => 'token-missing',
    ]);

    $response->assertStatus(401);
});
