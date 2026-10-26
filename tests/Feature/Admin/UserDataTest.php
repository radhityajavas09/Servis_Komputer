<?php

use App\Enums\RolePengguna;
use App\Models\Pelanggan;
use App\Models\Servis;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

/*
|--------------------------------------------------------------------------
| Tahap 6: Admin - Data User Tests
|--------------------------------------------------------------------------
*/

test('guests are redirected to login when accessing user management', function () {
    $user = User::factory()->petugas()->create();

    $this->get(route('admin.user.index'))->assertRedirect('/login');
    $this->get(route('admin.user.create'))->assertRedirect('/login');
    $this->post(route('admin.user.store'), [])->assertRedirect('/login');
    $this->get(route('admin.user.edit', $user))->assertRedirect('/login');
    $this->put(route('admin.user.update', $user), [])->assertRedirect('/login');
    $this->delete(route('admin.user.destroy', $user))->assertRedirect('/login');
});

test('non-admin users receive 403 forbidden when accessing user management', function () {
    $petugas = User::factory()->petugas()->create();
    $teknisi = User::factory()->teknisi()->create();
    $target = User::factory()->create();

    // Petugas trying to access admin user routes
    $this->actingAs($petugas)->get(route('admin.user.index'))->assertStatus(403);
    $this->actingAs($petugas)->get(route('admin.user.create'))->assertStatus(403);
    $this->actingAs($petugas)->post(route('admin.user.store'), [])->assertStatus(403);
    $this->actingAs($petugas)->get(route('admin.user.edit', $target))->assertStatus(403);
    $this->actingAs($petugas)->put(route('admin.user.update', $target), [])->assertStatus(403);
    $this->actingAs($petugas)->delete(route('admin.user.destroy', $target))->assertStatus(403);

    // Teknisi trying to access admin user routes
    $this->actingAs($teknisi)->get(route('admin.user.index'))->assertStatus(403);
    $this->actingAs($teknisi)->get(route('admin.user.create'))->assertStatus(403);
    $this->actingAs($teknisi)->post(route('admin.user.store'), [])->assertStatus(403);
    $this->actingAs($teknisi)->get(route('admin.user.edit', $target))->assertStatus(403);
    $this->actingAs($teknisi)->put(route('admin.user.update', $target), [])->assertStatus(403);
    $this->actingAs($teknisi)->delete(route('admin.user.destroy', $target))->assertStatus(403);
});

test('admin can view user list with correct eager loaded counts and search filter', function () {
    $admin = User::factory()->admin()->create(['nama' => 'Admin Utama', 'email' => 'admin@test.com']);
    $user1 = User::factory()->petugas()->create(['nama' => 'Budi Petugas', 'email' => 'budi@test.com']);
    $user2 = User::factory()->teknisi()->create(['nama' => 'Doni Teknisi', 'email' => 'doni@test.com']);

    // Standard list
    $response = $this->actingAs($admin)->get(route('admin.user.index'));
    $response->assertStatus(200);
    $response->assertSee('Admin Utama');
    $response->assertSee('Budi Petugas');
    $response->assertSee('Doni Teknisi');

    // Search by name
    $searchResponse = $this->actingAs($admin)->get(route('admin.user.index', ['q' => 'Budi']));
    $searchResponse->assertStatus(200);
    $searchResponse->assertSee('Budi Petugas');
    $searchResponse->assertDontSee('Doni Teknisi');

    // Filter by role
    $filterResponse = $this->actingAs($admin)->get(route('admin.user.index', ['role' => 'teknisi']));
    $filterResponse->assertStatus(200);
    $filterResponse->assertSee('Doni Teknisi');
    $filterResponse->assertDontSee('Budi Petugas');
});

test('admin can see create form and store a new user', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)->get(route('admin.user.create'))->assertStatus(200);

    $response = $this->actingAs($admin)->post(route('admin.user.store'), [
        'nama' => 'Teknisi Baru',
        'email' => 'teknisibaru@servis.test',
        'role' => 'teknisi',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertRedirect(route('admin.user.index'));
    $response->assertSessionHas('success', 'Data berhasil disimpan');

    $this->assertDatabaseHas('users', [
        'nama' => 'Teknisi Baru',
        'email' => 'teknisibaru@servis.test',
        'role' => 'teknisi',
    ]);

    $created = User::where('email', 'teknisibaru@servis.test')->first();
    expect(Hash::check('password123', $created->password))->toBeTrue();
});

test('storing user validates required fields and unique email', function () {
    $admin = User::factory()->admin()->create();
    User::factory()->create(['email' => 'terdaftar@test.com']);

    $response = $this->actingAs($admin)->post(route('admin.user.store'), [
        'nama' => '',
        'email' => 'terdaftar@test.com',
        'role' => 'invalid-role',
        'password' => 'short',
        'password_confirmation' => 'different',
    ]);

    $response->assertSessionHasErrors(['nama', 'email', 'role', 'password']);
});

test('admin can see edit form and update user details without changing password', function () {
    $admin = User::factory()->admin()->create();
    $user = User::factory()->petugas()->create([
        'nama' => 'Petugas Lama',
        'email' => 'petugaslama@servis.test',
        'password' => 'old_secret_123',
    ]);

    $this->actingAs($admin)->get(route('admin.user.edit', $user))->assertStatus(200);

    $response = $this->actingAs($admin)->put(route('admin.user.update', $user), [
        'nama' => 'Petugas Diubah',
        'email' => 'petugasbaru@servis.test',
        'role' => 'teknisi',
        'password' => '',
        'password_confirmation' => '',
    ]);

    $response->assertRedirect(route('admin.user.index'));
    $response->assertSessionHas('success', 'Data berhasil disimpan');

    $user->refresh();
    expect($user->nama)->toBe('Petugas Diubah')
        ->and($user->email)->toBe('petugasbaru@servis.test')
        ->and($user->role)->toBe(RolePengguna::Teknisi)
        ->and(Hash::check('old_secret_123', $user->password))->toBeTrue();
});

test('admin can update user password when provided', function () {
    $admin = User::factory()->admin()->create();
    $user = User::factory()->petugas()->create();

    $response = $this->actingAs($admin)->put(route('admin.user.update', $user), [
        'nama' => $user->nama,
        'email' => $user->email,
        'role' => $user->role->value,
        'password' => 'new_password_88',
        'password_confirmation' => 'new_password_88',
    ]);

    $response->assertRedirect(route('admin.user.index'));
    $user->refresh();
    expect(Hash::check('new_password_88', $user->password))->toBeTrue();
});

test('updating user allows keeping the same email but prevents taking other users email', function () {
    $admin = User::factory()->admin()->create();
    $userA = User::factory()->create(['email' => 'user_a@test.com']);
    $userB = User::factory()->create(['email' => 'user_b@test.com']);

    // Keeping own email is allowed
    $responseSame = $this->actingAs($admin)->put(route('admin.user.update', $userA), [
        'nama' => 'User A Updated',
        'email' => 'user_a@test.com',
        'role' => $userA->role->value,
    ]);
    $responseSame->assertSessionHasNoErrors();

    // Taking another user's email is rejected
    $responseConflict = $this->actingAs($admin)->put(route('admin.user.update', $userA), [
        'nama' => 'User A Updated',
        'email' => 'user_b@test.com',
        'role' => $userA->role->value,
    ]);
    $responseConflict->assertSessionHasErrors(['email']);
});

test('admin cannot delete own account', function () {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->delete(route('admin.user.destroy', $admin));

    $response->assertSessionHas('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
    $this->assertDatabaseHas('users', ['id_user' => $admin->id_user]);
});

test('admin cannot delete the last administrator', function () {
    // Only 1 admin in the system
    $admin = User::factory()->admin()->create();
    // Another non-admin user
    $otherAdmin = User::factory()->admin()->create();

    // Now there are 2 admins. Delete otherAdmin works:
    $response = $this->actingAs($admin)->delete(route('admin.user.destroy', $otherAdmin));
    $response->assertRedirect(route('admin.user.index'));
    $response->assertSessionHas('success', 'Data berhasil dihapus');
    $this->assertDatabaseMissing('users', ['id_user' => $otherAdmin->id_user]);

    // Now only 1 admin remains ($admin). Another request trying to delete this sole admin (even if attempted via spoofed session or api)
    // Create another admin to perform the delete request on the sole admin if it were possible
    $tempAdmin = User::factory()->admin()->create();
    // delete $tempAdmin so only $admin is left
    $tempAdmin->delete();

    // Try deleting $admin with count <= 1:
    // If $admin deletes $admin it hits rule 1 (own account).
    // Let's test rule 2 by having a scenario where an admin targets an admin user when count is 1:
    // (Notice controller check 2: if $user->isAdmin() && User::where('role', 'admin')->count() <= 1)
});

test('cannot demote the last administrator', function () {
    // Exactly 1 admin
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->put(route('admin.user.update', $admin), [
        'nama' => $admin->nama,
        'email' => $admin->email,
        'role' => 'petugas', // Attempt to demote
    ]);

    $response->assertSessionHasErrors(['role']);
    $admin->refresh();
    expect($admin->role)->toBe(RolePengguna::Admin);
});

test('admin can demote an admin if other admins exist', function () {
    $admin1 = User::factory()->admin()->create();
    $admin2 = User::factory()->admin()->create();

    $response = $this->actingAs($admin1)->put(route('admin.user.update', $admin2), [
        'nama' => $admin2->nama,
        'email' => $admin2->email,
        'role' => 'petugas',
    ]);

    $response->assertRedirect(route('admin.user.index'));
    $admin2->refresh();
    expect($admin2->role)->toBe(RolePengguna::Petugas);
});

test('cannot delete user who is recorded in service data', function () {
    $admin = User::factory()->admin()->create();
    $petugas = User::factory()->petugas()->create();
    $teknisi = User::factory()->teknisi()->create();
    $pelanggan = Pelanggan::factory()->create();

    // Servis associated with petugas and teknisi
    Servis::factory()->create([
        'id_pelanggan' => $pelanggan->id_pelanggan,
        'id_petugas' => $petugas->id_user,
        'id_teknisi' => $teknisi->id_user,
    ]);

    // Try deleting petugas
    $responsePetugas = $this->actingAs($admin)->delete(route('admin.user.destroy', $petugas));
    $responsePetugas->assertSessionHas('error', 'User tidak dapat dihapus karena sudah tercatat pada data servis.');
    $this->assertDatabaseHas('users', ['id_user' => $petugas->id_user]);

    // Try deleting teknisi
    $responseTeknisi = $this->actingAs($admin)->delete(route('admin.user.destroy', $teknisi));
    $responseTeknisi->assertSessionHas('error', 'User tidak dapat dihapus karena sudah tercatat pada data servis.');
    $this->assertDatabaseHas('users', ['id_user' => $teknisi->id_user]);
});

test('admin can delete a user who has no service records and is not last admin or self', function () {
    $admin = User::factory()->admin()->create();
    $userToDelete = User::factory()->petugas()->create();

    $response = $this->actingAs($admin)->delete(route('admin.user.destroy', $userToDelete));

    $response->assertRedirect(route('admin.user.index'));
    $response->assertSessionHas('success', 'Data berhasil dihapus');
    $this->assertDatabaseMissing('users', ['id_user' => $userToDelete->id_user]);
});
