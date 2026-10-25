<?php

use App\Services\NomorServisGenerator;

test('nomor servis generator starts from SRV-001 when empty', function () {
    $generator = new NomorServisGenerator;

    expect($generator->nextFrom(null))->toBe('SRV-001')
        ->and($generator->nextFrom(''))->toBe('SRV-001');
});

test('nomor servis generator increments correctly with 3 digits padding', function () {
    $generator = new NomorServisGenerator;

    expect($generator->nextFrom('SRV-001'))->toBe('SRV-002')
        ->and($generator->nextFrom('SRV-009'))->toBe('SRV-010')
        ->and($generator->nextFrom('SRV-099'))->toBe('SRV-100');
});

test('nomor servis generator transitions from SRV-999 to SRV-1000 without extra zeros', function () {
    $generator = new NomorServisGenerator;

    expect($generator->nextFrom('SRV-999'))->toBe('SRV-1000')
        ->and($generator->nextFrom('SRV-1000'))->toBe('SRV-1001');
});
