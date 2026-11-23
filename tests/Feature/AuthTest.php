<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;

beforeEach(function () {
    RateLimiter::clear('admin@servis.test|127.0.0.1');
    RateLimiter::clear('wrong@servis.test|127.0.0.1');
});

test('guest visiting root / is redirected to customer portal /lacak', function () {
    $response = $this->get('/');

    $response->assertRedirect('/lacak');
});

test('guest can view the login form', function () {
    $response = $this->get('/login');

    $response->assertStatus(200);
    $response->assertSee('Selamat Datang Kembali');
    $response->assertSee('Alamat Email');
    $response->assertSee('Password');
    $response->assertSee('Masuk ke Sistem');
});

test('login fails with invalid credentials and retains email input', function () {
    User::factory()->create([
        'email' => 'admin@servis.test',
        'password' => Hash::make('password'),
    ]);

    $response = $this->from('/login')->post('/login', [
        'email' => 'admin@servis.test',
        'password' => 'wrong-password',
    ]);

    $response->assertRedirect('/login');
    $response->assertSessionHasErrors(['email']);
    expect(session('errors')->first('email'))->toBe('Email atau password salah.');
    $this->assertGuest();
});

test('login rate limiting triggers after 5 failed attempts with throttle message', function () {
    User::factory()->create([
        'email' => 'wrong@servis.test',
        'password' => Hash::make('password'),
    ]);

    for ($i = 0; $i < 5; $i++) {
        $this->post('/login', [
            'email' => 'wrong@servis.test',
            'password' => 'wrong-password',
        ]);
    }

    // 6th attempt should trigger throttle
    $response = $this->post('/login', [
        'email' => 'wrong@servis.test',
        'password' => 'wrong-password',
    ]);

    $response->assertSessionHasErrors(['email']);
    expect(session('errors')->first('email'))->toContain('Terlalu banyak percobaan');
});

test('admin login succeeds and redirects to /admin/dashboard', function () {
    $admin = User::factory()->admin()->create([
        'email' => 'admin@servis.test',
        'password' => Hash::make('password'),
    ]);

    $response = $this->post('/login', [
        'email' => 'admin@servis.test',
        'password' => 'password',
    ]);

    $response->assertRedirect(route('admin.dashboard'));
    $this->assertAuthenticatedAs($admin);
});

test('petugas login succeeds and redirects to /petugas/dashboard', function () {
    $petugas = User::factory()->petugas()->create([
        'email' => 'petugas@servis.test',
        'password' => Hash::make('password'),
    ]);

    $response = $this->post('/login', [
        'email' => 'petugas@servis.test',
        'password' => 'password',
    ]);

    $response->assertRedirect(route('petugas.dashboard'));
    $this->assertAuthenticatedAs($petugas);
});

test('teknisi login succeeds and redirects to /teknisi/dashboard', function () {
    $teknisi = User::factory()->teknisi()->create([
        'email' => 'teknisi@servis.test',
        'password' => Hash::make('password'),
    ]);

    $response = $this->post('/login', [
        'email' => 'teknisi@servis.test',
        'password' => 'password',
    ]);

    $response->assertRedirect(route('teknisi.dashboard'));
    $this->assertAuthenticatedAs($teknisi);
});

test('authenticated user visiting /login or / is redirected to respective dashboard', function () {
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin)->get('/login')->assertRedirect(route('admin.dashboard'));
    $this->actingAs($admin)->get('/')->assertRedirect(route('admin.dashboard'));

    $petugas = User::factory()->petugas()->create();
    $this->actingAs($petugas)->get('/login')->assertRedirect(route('petugas.dashboard'));
    $this->actingAs($petugas)->get('/')->assertRedirect(route('petugas.dashboard'));

    $teknisi = User::factory()->teknisi()->create();
    $this->actingAs($teknisi)->get('/login')->assertRedirect(route('teknisi.dashboard'));
    $this->actingAs($teknisi)->get('/')->assertRedirect(route('teknisi.dashboard'));
});

test('unauthenticated guest is blocked from accessing protected dashboards', function () {
    $this->get('/admin/dashboard')->assertRedirect('/login');
    $this->get('/petugas/dashboard')->assertRedirect('/login');
    $this->get('/teknisi/dashboard')->assertRedirect('/login');
});

test('cross-role access is strictly blocked with 403 error across all combinations', function () {
    $admin = User::factory()->admin()->create();
    $petugas = User::factory()->petugas()->create();
    $teknisi = User::factory()->teknisi()->create();

    // Admin trying other roles
    $this->actingAs($admin)->get('/petugas/dashboard')->assertStatus(403);
    $this->actingAs($admin)->get('/teknisi/dashboard')->assertStatus(403);

    // Petugas trying other roles
    $this->actingAs($petugas)->get('/admin/dashboard')->assertStatus(403);
    $this->actingAs($petugas)->get('/teknisi/dashboard')->assertStatus(403);

    // Teknisi trying other roles
    $this->actingAs($teknisi)->get('/admin/dashboard')->assertStatus(403);
    $this->actingAs($teknisi)->get('/petugas/dashboard')->assertStatus(403);
});

test('logout clears session and redirects to /login', function () {
    $user = User::factory()->admin()->create();

    $response = $this->actingAs($user)->post('/logout');

    $response->assertRedirect('/login');
    $this->assertGuest();
});
