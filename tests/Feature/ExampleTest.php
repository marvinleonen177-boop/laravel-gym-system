<?php

test('guests are redirected to the administrator login', function () {
    $response = $this->get('/');

    $response->assertRedirect(route('login'));
});
