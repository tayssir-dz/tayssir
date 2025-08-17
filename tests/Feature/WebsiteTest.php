<?php

describe('Website Tests', function () {});
it('home page accessible', function () {
    $response = $this->get('/');
    $response->assertStatus(200);
});

it('dashboard redirects to login', function () {
    $response = $this->get('/dashboard');
    $response->assertStatus(302);
    $response->assertRedirect('/dashboard/login');
});

it('login page accessible', function () {
    $response = $this->get('/dashboard/login');
    $response->assertStatus(200);
});
