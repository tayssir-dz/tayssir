<?php

use App\Models\User;
use App\Models\Division;
use App\Services\GoogleAuthService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

it('registers a new user via google and returns tokens', function () {
    // Arrange
    $division = Division::create(['name' => 'Division A', 'description' => 'Test division']);
    $payload = [
        'email' => 'googleuser@example.com',
        'sub' => 'google-sub-123',
        'name' => 'Google User',
        'picture' => 'https://example.com/avatar.png',
    ];

    // Mock GoogleAuthService
    $this->mock(GoogleAuthService::class, function ($mock) use ($payload) {
        $mock->shouldReceive('verifyIdToken')->once()->andReturn($payload);
    });

    // Ensure student role exists to avoid hitting factory logic mid-request
    Role::firstOrCreate(['name' => 'student']);

    // Act
    $response = $this->postJson('/api/v1/auth/google/register', [
        'id_token' => 'dummy-id-token',
        'name' => 'Google User',
        'age' => 17,
        'division_id' => $division->id,
    ]);

    // Assert
    $response->assertStatus(200)
        ->assertJsonStructure(['data' => ['token', 'refresh_token']]);

    $user = User::where('email', 'googleuser@example.com')->first();
    expect($user)->not->toBeNull();
    expect($user->google_id)->toBe('google-sub-123');
    expect($user->password)->toBeNull();
});

it('is idempotent when registering google user again (updates fields)', function () {
    uses(RefreshDatabase::class);
    $payload = [
        'email' => 'existing@example.com',
        'sub' => 'google-sub-999',
        'name' => 'Existing User',
    ];

    $division = Division::create(['name' => 'Division B', 'description' => 'Second division']);

    $this->mock(GoogleAuthService::class, function ($mock) use ($payload) {
        $mock->shouldReceive('verifyIdToken')->andReturn($payload);
    });

    Role::firstOrCreate(['name' => 'student']);

    // First registration
    $this->postJson('/api/v1/auth/google/register', [
        'id_token' => 'token1',
        'name' => 'Existing User',
        'age' => 18,
        'division_id' => $division->id,
    ])->assertStatus(200);

    // Second registration with changed name
    $payload['name'] = 'Existing User Changed';
    $this->mock(GoogleAuthService::class, function ($mock) use ($payload) {
        $mock->shouldReceive('verifyIdToken')->andReturn($payload);
    });

    $this->postJson('/api/v1/auth/google/register', [
        'id_token' => 'token2',
        'name' => 'Existing User Changed',
        'age' => 18,
        'division_id' => $division->id,
    ])->assertStatus(200);

    $user = User::where('email', 'existing@example.com')->first();
    expect($user->name)->toBe('Existing User Changed');
});
