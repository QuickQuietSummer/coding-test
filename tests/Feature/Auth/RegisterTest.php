<?php

namespace Tests\Feature\Auth;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use DatabaseMigrations;

    private TestResponse $response;

    public function test_that_entry_point_available()
    {
        $this->response->assertStatus(201);
    }

    /**
     * @depends test_that_entry_point_available
     */
    public function test_that_registration_add_token_and_role()
    {
        self::assertCount(1, User::all());
        self::assertCount(1, Role::all());
        self::assertCount(1, User::first()->tokens);
    }

    /**
     * @depends test_that_entry_point_available
     * @depends test_that_registration_add_token_and_role
     */
    public function test_that_public_api_create_only_client_but_not_employee()
    {
        self::assertTrue(User::first()->role->type == Role::CLIENT);
    }

    public function test_that_response_exact_structure()
    {
        $this->response->assertJsonStructure([
            'message',
            'data' => [
                'access_token',
                'token_type',
            ]
        ]);
    }

    protected function setUp(): void
    {
        parent::setUp(); // TODO: Change the autogenerated stub

        $this->response = $this->json('POST', 'api/register', [
                'name' => 'John',
                'email' => 'john@mail.ru',
                'password' => 'pass1234',
                'password_confirmation' => 'pass1234',
            ]
        );
    }
}