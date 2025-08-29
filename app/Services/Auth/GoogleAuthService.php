<?php

namespace App\Services\Auth;

use Google\Client as GoogleClient;
use Illuminate\Support\Facades\Log;

class GoogleAuthService
{
    protected GoogleClient $client;

    public function __construct()
    {
        $this->client = new GoogleClient;
        $this->client->setClientId(config('google.client_id'));
        $this->client->setClientSecret(config('google.client_secret'));
    }

    /**
     * Verify the integrity of the ID token and return the payload
     */
    public function verifyIdToken(string $idToken): ?array
    {
        try {
            $payload = $this->client->verifyIdToken($idToken);

            return $payload ?: null;
        } catch (\Throwable $e) {
            Log::warning('Google ID token verification failed', [
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }
}
