<?php

namespace Tests\Feature;

use App\Models\ReadingLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReadingLogTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_create_a_reading_log(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('reading-logs.store'), [
                'volume_id' => 'abc123',
                'title'     => 'El Señor de los Anillos',
                'authors'   => 'J.R.R. Tolkien',
                'status'    => 'wishlist',
            ])
            ->assertRedirect(route('books.show', 'abc123'));

        $this->assertDatabaseHas('reading_logs', [
            'user_id'   => $user->id,
            'volume_id' => 'abc123',
            'title'     => 'El Señor de los Anillos',
        ]);
    }

    public function test_unauthenticated_user_is_redirected_when_creating_a_log(): void
    {
        $this->post(route('reading-logs.store'), [
            'volume_id' => 'abc123',
            'title'     => 'El Señor de los Anillos',
        ])->assertRedirect(route('login'));
    }

    public function test_user_can_change_status_of_their_log(): void
    {
        $user = User::factory()->create();
        $log  = ReadingLog::factory()->create(['user_id' => $user->id, 'status' => 'wishlist']);

        $this->actingAs($user)
            ->patch(route('reading-logs.update', $log), ['status' => 'reading'])
            ->assertRedirect();

        $this->assertDatabaseHas('reading_logs', ['id' => $log->id, 'status' => 'reading']);
    }

    public function test_user_can_save_a_valid_rating(): void
    {
        $user = User::factory()->create();
        $log  = ReadingLog::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user)
            ->patch(route('reading-logs.rating', $log), ['rating' => 3.5])
            ->assertRedirect();

        $this->assertDatabaseHas('reading_logs', ['id' => $log->id, 'rating' => 3.5]);
    }

    public function test_user_can_delete_their_own_log(): void
    {
        $user = User::factory()->create();
        $log  = ReadingLog::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user)
            ->delete(route('reading-logs.destroy', $log))
            ->assertRedirect(route('reading-logs.index'));

        $this->assertDatabaseMissing('reading_logs', ['id' => $log->id]);
    }

    public function test_user_cannot_delete_another_users_log(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $log   = ReadingLog::factory()->create(['user_id' => $owner->id]);

        $this->actingAs($other)
            ->delete(route('reading-logs.destroy', $log))
            ->assertForbidden();

        $this->assertDatabaseHas('reading_logs', ['id' => $log->id]);
    }
}
