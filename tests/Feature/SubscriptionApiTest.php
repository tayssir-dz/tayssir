<?php

describe('Subscription API Endpoints', function () {

    it('returns a successful response for subscriptions list', function () {
        $response = $this->get('/api/subscriptions');

        $response->assertStatus(200);
    });

    it('returns a successful response for specific subscription', function () {
        $subscriptionsResponse = $this->get('/api/subscriptions/0');

        $subscriptionsResponse->assertStatus(200);
    });

    it('returns 404 for non-existent subscription', function () {
        $response = $this->get('/api/subscriptions/99999');

        $response->assertStatus(404);
    });
});
