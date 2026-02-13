<?php

test('default appearance is dark when no cookie is set', function () {
    $response = $this->get('/');

    $response->assertOk();

    $html = $response->getContent();
    expect($html)->toContain('class="dark"');
});

test('appearance is light when cookie is set to light', function () {
    $response = $this->withUnencryptedCookie('appearance', 'light')->get('/');

    $response->assertOk();

    $html = $response->getContent();
    expect($html)->not->toContain('class="dark"');
});

test('appearance is dark when cookie is set to dark', function () {
    $response = $this->withUnencryptedCookie('appearance', 'dark')->get('/');

    $response->assertOk();

    $html = $response->getContent();
    expect($html)->toContain('class="dark"');
});
