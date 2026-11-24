<?php

test('the application redirects guest from root to customer portal lacak', function () {
    $response = $this->get('/');

    $response->assertRedirect('/lacak');
});
