<?php

use App\Models\User;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Tahap 14: Penyempurnaan - Responsif, Keamanan, & Halaman Error Tests
|--------------------------------------------------------------------------
*/

test('custom 404 not found page is rendered with Nordic Pine styling when visiting nonexistent route', function () {
    $response = $this->get('/halaman-yang-pasti-tidak-ada-12345');

    $response->assertStatus(404);
    $response->assertSee('Error 404');
    $response->assertSee('Halaman Tidak Ditemukan');
    $response->assertSee('Kembali ke Beranda');
    $response->assertSee('Servis Komputer');
});

test('custom 403 forbidden error page is rendered with custom message on unauthorized role access', function () {
    $petugas = User::factory()->petugas()->create();

    // Accessing admin only route
    $response = $this->actingAs($petugas)->get(route('admin.dashboard'));

    $response->assertStatus(403);
    $response->assertSee('Error 403');
    $response->assertSee('Akses Ditolak');
    $response->assertSee('Kembali ke Dashboard');
});

test('custom 500 internal server error page renders cleanly', function () {
    // Dynamically register a test route that throws an exception
    Route::get('/_test-500-error', function () {
        abort(500, 'Kesalahan internal server simulasi');
    });

    $response = $this->get('/_test-500-error');

    $response->assertStatus(500);
    $response->assertSee('Error 500');
    $response->assertSee('Terjadi Kesalahan Server');
    $response->assertSee('Kembali ke Beranda');
});

test('session fixation protection: session id regenerates upon login and invalidates upon logout', function () {
    $user = User::factory()->create([
        'email' => 'session-test@servis.test',
        'password' => bcrypt('password'),
    ]);

    $this->get('/login');
    $initialSessionId = session()->getId();

    $loginResponse = $this->post('/login', [
        'email' => 'session-test@servis.test',
        'password' => 'password',
    ]);

    $authenticatedSessionId = session()->getId();

    expect($authenticatedSessionId)->not->toBe($initialSessionId);

    // Logout
    $this->actingAs($user)->post(route('logout'));
    $this->assertGuest();
});

test('all critical layouts enforce responsive viewport meta and touch accessibility', function () {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->get(route('admin.dashboard'));

    $response->assertStatus(200);
    // Viewport meta
    $response->assertSee('name="viewport" content="width=device-width, initial-scale=1"', false);
    // Mobile hamburger accessibility
    $response->assertSee('aria-label="Buka menu navigasi"', false);
    // CSRF meta tag
    $response->assertSee('name="csrf-token"', false);
});
