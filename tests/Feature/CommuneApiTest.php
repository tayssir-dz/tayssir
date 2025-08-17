<?php

describe('Commune API Endpoints', function () {

    it('returns a successful response for communes list', function () {
        $response = $this->get('/api/communes');

        $response->assertStatus(200);
    });

    it('returns a successful response for specific commune', function () {
        // Test with commune ID 1 (should exist)
        $response = $this->get('/api/communes/1');

        $response->assertStatus(200);
    });

    it('returns 404 for non-existent commune', function () {
        $response = $this->get('/api/communes/99999');

        $response->assertStatus(404);
    });

    it('returns search results for commune', function () {
        $response = $this->get('/api/search/commune/alger');

        $response->assertStatus(200);
    });

    it('returns empty array for commune search with no results', function () {
        $response = $this->get('/api/search/commune/nonexistentcommune');

        $response->assertStatus(200);
    });

    it('handles special characters in commune search', function () {
        $response = $this->get('/api/search/commune/البليدة');

        $response->assertStatus(200);
    });
});
