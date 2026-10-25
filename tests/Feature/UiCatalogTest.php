<?php

test('ui catalog page is accessible in local environment and renders components', function () {
    $response = $this->get('/_ui');

    $response->assertStatus(200);
    $response->assertSee('Katalog Komponen & Kerangka UI');
    $response->assertSee('Kartu Statistik (Stat Cards)');
    $response->assertSee('Total Servis');
    $response->assertSee('Sedang Dikerjakan');
    $response->assertSee('Baru Diterima');
    $response->assertSee('Servis Selesai');
    $response->assertSee('Status Servis (x-status-badge)');
    $response->assertSee('Diterima');
    $response->assertSee('Dalam pemeriksaan');
    $response->assertSee('Dalam perbaikan');
    $response->assertSee('Selesai');
    $response->assertSee('Diambil');
    $response->assertSee('SRV-001');
});
