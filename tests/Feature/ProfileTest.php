<?php

use App\Enums\RolePengguna;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

test('unauthenticated guest cannot view or update profile', function () {
    $this->get('/profil')->assertRedirect('/login');
    $this->put('/profil', ['nama' => 'Test'])->assertRedirect('/login');
});

test('profile page is accessible and displays data for all three roles', function () {
    $admin = User::factory()->admin()->create(['nama' => 'Admin Utama']);
    $petugas = User::factory()->petugas()->create(['nama' => 'Petugas Satu']);
    $teknisi = User::factory()->teknisi()->create(['nama' => 'Teknisi Handal']);

    $this->actingAs($admin)->get('/profil')
        ->assertStatus(200)
        ->assertSee('Admin Utama')
        ->assertSee('Administrator');

    $this->actingAs($petugas)->get('/profil')
        ->assertStatus(200)
        ->assertSee('Petugas Satu')
        ->assertSee('Petugas Servis');

    $this->actingAs($teknisi)->get('/profil')
        ->assertStatus(200)
        ->assertSee('Teknisi Handal')
        ->assertSee('Teknisi');
});

test('user can update their name and email successfully', function () {
    $user = User::factory()->create([
        'nama' => 'Nama Lama',
        'email' => 'lama@servis.test',
    ]);

    $response = $this->actingAs($user)->put('/profil', [
        'nama' => 'Nama Baru Yang Keren',
        'email' => 'baru@servis.test',
    ]);

    $response->assertRedirect(route('profil.edit'));
    $response->assertSessionHas('success', 'Profil berhasil diperbarui.');

    $user->refresh();
    expect($user->nama)->toBe('Nama Baru Yang Keren')
        ->and($user->email)->toBe('baru@servis.test');
});

test('user cannot take another existing users email', function () {
    User::factory()->create(['email' => 'terpakai@servis.test']);
    $user = User::factory()->create(['email' => 'saya@servis.test']);

    $response = $this->actingAs($user)->from('/profil')->put('/profil', [
        'nama' => $user->nama,
        'email' => 'terpakai@servis.test',
    ]);

    $response->assertRedirect('/profil');
    $response->assertSessionHasErrors(['email']);
    expect($user->fresh()->email)->toBe('saya@servis.test');
});

test('user can keep their own email when updating profile', function () {
    $user = User::factory()->create(['email' => 'tetap@servis.test']);

    $response = $this->actingAs($user)->put('/profil', [
        'nama' => 'Ganti Nama Saja',
        'email' => 'tetap@servis.test',
    ]);

    $response->assertSessionHasNoErrors();
    expect($user->fresh()->nama)->toBe('Ganti Nama Saja');
});

test('updating password requires current password to be correct', function () {
    $user = User::factory()->create([
        'password' => Hash::make('password123'),
    ]);

    // Missing current_password
    $response1 = $this->actingAs($user)->from('/profil')->put('/profil', [
        'nama' => $user->nama,
        'email' => $user->email,
        'password' => 'newpassword123',
        'password_confirmation' => 'newpassword123',
    ]);
    $response1->assertSessionHasErrors(['current_password']);

    // Wrong current_password
    $response2 = $this->actingAs($user)->from('/profil')->put('/profil', [
        'nama' => $user->nama,
        'email' => $user->email,
        'current_password' => 'wrongpassword',
        'password' => 'newpassword123',
        'password_confirmation' => 'newpassword123',
    ]);
    $response2->assertSessionHasErrors(['current_password']);
    expect(Hash::check('password123', $user->fresh()->password))->toBeTrue();
});

test('password confirmation mismatch is rejected', function () {
    $user = User::factory()->create([
        'password' => Hash::make('password123'),
    ]);

    $response = $this->actingAs($user)->from('/profil')->put('/profil', [
        'nama' => $user->nama,
        'email' => $user->email,
        'current_password' => 'password123',
        'password' => 'newpassword123',
        'password_confirmation' => 'different123',
    ]);

    $response->assertSessionHasErrors(['password']);
});

test('user can change password with correct current password and valid confirmation', function () {
    $user = User::factory()->create([
        'password' => Hash::make('oldpassword123'),
    ]);

    $response = $this->actingAs($user)->put('/profil', [
        'nama' => $user->nama,
        'email' => $user->email,
        'current_password' => 'oldpassword123',
        'password' => 'newsecretpass',
        'password_confirmation' => 'newsecretpass',
    ]);

    $response->assertRedirect(route('profil.edit'));
    $response->assertSessionHas('success');

    $user->refresh();
    expect(Hash::check('newsecretpass', $user->password))->toBeTrue();
    $this->assertAuthenticatedAs($user);
});

test('role cannot be modified via profile update request payload', function () {
    $teknisi = User::factory()->teknisi()->create();

    $response = $this->actingAs($teknisi)->put('/profil', [
        'nama' => 'Teknisi Coba Jadi Admin',
        'email' => $teknisi->email,
        'role' => 'admin', // Malicious attempt to escalate privileges
    ]);

    $response->assertSessionHasNoErrors();
    expect($teknisi->fresh()->role)->toBe(RolePengguna::Teknisi);
});
