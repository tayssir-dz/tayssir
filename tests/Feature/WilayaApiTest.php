<?php

describe('Wilaya API Endpoints', function () {

    it('returns a successful response for wilayas list', function () {
        $response = $this->get('/api/wilayas');

        $response->assertStatus(200);
    });

    it('returns a successful response for specific wilaya', function () {
        // Test with wilaya ID 1 (should exist)
        $response = $this->get('/api/wilayas/1');

        $response->assertStatus(200);
    });

    it('returns 404 for non-existent wilaya', function () {
        $response = $this->get('/api/wilayas/999');

        $response->assertStatus(404);
    });

    it('returns communes for specific wilaya', function () {
        // Test with wilaya ID 1 (should exist)
        $response = $this->get('/api/wilayas/1/communes');

        $response->assertStatus(200);
    });

    it('returns 404 for communes of non-existent wilaya', function () {
        $response = $this->get('/api/wilayas/999/communes');

        $response->assertStatus(404);
    });

    it('returns search results for wilaya', function () {
        $response = $this->get('/api/search/wilaya/alger');

        $response->assertStatus(200);
    });

    it('returns empty array for wilaya search with no results', function () {
        $response = $this->get('/api/search/wilaya/nonexistentcity');

        $response->assertStatus(200);
    });

    it('handles special characters in wilaya search', function () {
        $response = $this->get('/api/search/wilaya/البليدة');

        $response->assertStatus(200);
    });
});
