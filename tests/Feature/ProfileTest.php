<?php

namespace Tests\Feature;

use App\Models\ReadingLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_access_their_profile(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('me'))
            ->assertOk()
            ->assertViewIs('profile.index');
    }

    public function test_profile_stats_reflect_reading_logs(): void
    {
        $user = User::factory()->create();

        ReadingLog::factory()->create(['user_id' => $user->id, 'status' => 'read']);
        ReadingLog::factory()->create(['user_id' => $user->id, 'status' => 'read']);
        ReadingLog::factory()->create(['user_id' => $user->id, 'status' => 'reading']);
        ReadingLog::factory()->create(['user_id' => $user->id, 'status' => 'wishlist']);
        ReadingLog::factory()->create(['user_id' => $user->id, 'status' => 'dropped']);

        $this->actingAs($user)
            ->get(route('me'))
            ->assertOk()
            ->assertViewHas('total', 5)
            ->assertViewHas('read', 2)
            ->assertViewHas('reading', 1)
            ->assertViewHas('wishlist', 1)
            ->assertViewHas('dropped', 1);
    }

    public function test_unauthenticated_user_is_redirected_from_profile(): void
    {
        $this->get(route('me'))
            ->assertRedirect(route('login'));
    }
}
