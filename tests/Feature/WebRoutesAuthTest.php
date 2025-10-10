<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class WebRoutesAuthTest extends TestCase
{
    use DatabaseTransactions;

    public function test_guest_is_redirected_from_protected_routes(): void
    {
        $protected = [
            '/dashboard',
            '/profile',
            '/products',
            '/categories',
            '/import',
        ];

        foreach ($protected as $url) {
            $this->get($url)->assertRedirect('/login');
        }
    }

    public function test_authenticated_user_can_access_protected_routes(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->get('/dashboard')->assertOk();
        $this->get('/profile')->assertOk();
        $this->get('/products')->assertOk();
        $this->get('/categories')->assertOk();
        $this->get('/import')->assertOk();
    }
}
