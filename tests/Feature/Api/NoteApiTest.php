<?php

namespace Tests\Feature\Api;

use App\Models\Note;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class NoteApiTest extends TestCase
{
    use RefreshDatabase;

    private function login(): void
    {
        Sanctum::actingAs(User::factory()->create());
    }

    public function test_tanpa_token_semua_endpoint_notes_ditolak(): void
    {
        $note = Note::factory()->create();

        $this->getJson('/api/notes')->assertStatus(401);
        $this->postJson('/api/notes', ['title' => 'x'])->assertStatus(401);
        $this->getJson("/api/notes/{$note->id}")->assertStatus(401);
        $this->putJson("/api/notes/{$note->id}", ['title' => 'x'])->assertStatus(401);
        $this->deleteJson("/api/notes/{$note->id}")->assertStatus(401);
    }

    public function test_tanpa_token_dan_tanpa_header_accept_tetap_401_bukan_500(): void
    {
        // Meniru Postman default: tidak mengirim "Accept: application/json"
        $this->get('/api/notes')->assertStatus(401)->assertJson(['message' => 'Unauthenticated.']);
    }

    public function test_index_mengembalikan_daftar_catatan_dalam_json(): void
    {
        $this->login();
        Note::factory()->count(3)->create();

        $this->getJson('/api/notes')
            ->assertOk()
            ->assertJsonCount(3)
            ->assertJsonStructure([['id', 'title', 'content', 'created_at', 'updated_at']]);
    }

    public function test_store_membuat_catatan_baru(): void
    {
        $this->login();

        $this->postJson('/api/notes', ['title' => 'Catatan UTS', 'content' => 'Isi'])
            ->assertStatus(201)
            ->assertJsonFragment(['title' => 'Catatan UTS']);

        $this->assertDatabaseHas('notes', ['title' => 'Catatan UTS']);
    }

    public function test_store_gagal_jika_judul_kosong(): void
    {
        $this->login();

        $this->postJson('/api/notes', ['title' => '', 'content' => 'Tanpa judul'])
            ->assertStatus(422)
            ->assertJsonValidationErrors('title');

        $this->assertDatabaseCount('notes', 0);
    }

    public function test_show_mengembalikan_satu_catatan(): void
    {
        $this->login();
        $note = Note::factory()->create(['title' => 'Detail']);

        $this->getJson("/api/notes/{$note->id}")
            ->assertOk()
            ->assertJsonFragment(['id' => $note->id, 'title' => 'Detail']);
    }

    public function test_show_catatan_tidak_ada_mengembalikan_404_json(): void
    {
        $this->login();

        $this->getJson('/api/notes/9999')->assertStatus(404);
    }

    public function test_update_mengubah_catatan(): void
    {
        $this->login();
        $note = Note::factory()->create(['title' => 'Lama']);

        $this->putJson("/api/notes/{$note->id}", ['title' => 'Baru', 'content' => 'Isi baru'])
            ->assertOk()
            ->assertJsonFragment(['title' => 'Baru']);

        $this->assertDatabaseHas('notes', ['id' => $note->id, 'title' => 'Baru']);
    }

    public function test_destroy_menghapus_catatan(): void
    {
        $this->login();
        $note = Note::factory()->create();

        $this->deleteJson("/api/notes/{$note->id}")->assertOk();

        $this->assertDatabaseMissing('notes', ['id' => $note->id]);
    }
}
