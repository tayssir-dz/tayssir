<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

describe('Division API Endpoints', function () {

    it('returns a successful response for divisions list', function () {
        $response = $this->get('/api/divisions');

        $response->assertStatus(200);
    });

    it('returns a successful response for specific division', function () {
        $response = $this->get('/api/divisions/0');
        dd($response->getContent());
        $response->assertStatus(200);
    });

    it('returns 404 for non-existent division', function () {
        $response = $this->get('/api/divisions/99999');

        $response->assertStatus(404);
    });
});
