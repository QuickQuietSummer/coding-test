<?php


namespace Tests\Feature\Request;


use App\Events\RequestResolved;
use App\Models\Request;
use App\Models\Role;
use App\Models\User;
use App\Services\Auth\TokenIssuer;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Tests\Feature\Request\Traits\ActingAsRole;
use Tests\TestCase;

class RequestPutTest extends TestCase
{
    use DatabaseMigrations;
    use ActingAsRole;

    private $employees;
    private $clients;
    private $comment = 'Извините, но программисты это не про утюги';

    public function test_that_entry_point_available()
    {
        self::assertCount(30, Request::all());
        $employee = $this->actingAsRole(Role::EMPLOYEE);
        $request = Request::firstWhere('status', '=', Request::STATUS_ACTIVE);
        $employee->assignments()->create(['request_id' => $request->id]);
        self::assertNotNull($request);
        $this->json('PUT', 'api/requests/' . $request->id, ['comment' => $this->comment])
            ->assertStatus(200);
    }

    /**
     * @depends test_that_entry_point_available
     */
    public function test_that_response_structure_exact()
    {
        $employee = $this->actingAsRole(Role::EMPLOYEE);
        $request = Request::firstWhere('status', '=', Request::STATUS_ACTIVE);
        $employee->assignments()->create(['request_id' => $request->id]);
        $response = $this->json('PUT', 'api/requests/' . $request->id, ['comment' => $this->comment]);
        $response->assertJsonStructure([
            'message',
            'data',
        ]);
    }

    /**
     * @depends test_that_entry_point_available
     */
    public function test_that_resolved_request_cannot_resolve_again()
    {
        $employee = $this->actingAsRole(Role::EMPLOYEE);
        $request = Request::firstWhere('status', '=', Request::STATUS_RESOLVED);
        $employee->assignments()->create(['request_id' => $request->id]);
        $this->json('PUT', 'api/requests/' . $request->id, ['comment' => $this->comment])
            ->assertStatus(422);
    }

    /**
     * @depends test_that_entry_point_available
     */
    public function test_that_request_resolved()
    {
        $employee = $this->actingAsRole(Role::EMPLOYEE);
        $request = Request::firstWhere('status', '=', Request::STATUS_ACTIVE);
        $employee->assignments()->create(['request_id' => $request->id]);

        $this->json('PUT', 'api/requests/' . $request->id, ['comment' => $this->comment]);

        $request->refresh();

        self::assertTrue($request->status == Request::STATUS_RESOLVED);
        self::assertTrue($request->comment == $this->comment);
    }

    /**
     * @depends test_that_request_resolved
     */
    public function test_that_event_dispatched_when_resolve()
    {
        Event::fake();

        $employee = $this->actingAsRole(Role::EMPLOYEE);
        $request = Request::firstWhere('status', '=', Request::STATUS_ACTIVE);
        $employee->assignments()->create(['request_id' => $request->id]);
        $this->json('PUT', 'api/requests/' . $request->id, ['comment' => $this->comment]);

        Event::assertDispatched(RequestResolved::class);
    }

    /**
     * @depends test_that_request_resolved
     */
    public function test_that_mail_sended_when_resolve()
    {
        Mail::fake();

        $employee = $this->actingAsRole(Role::EMPLOYEE);
        $request = Request::firstWhere('status', '=', Request::STATUS_ACTIVE);
        $employee->assignments()->create(['request_id' => $request->id]);

        $this->json('PUT', 'api/requests/' . $request->id, ['comment' => $this->comment]);

        Mail::assertQueued(\App\Mail\RequestResolved::class);
    }

    /**
     * @depends test_that_request_resolved
     */
    public function test_that_only_employee_can_resolve()
    {
        $client = $this->actingAsRole(Role::CLIENT);
        $request = Request::firstWhere('status', '=', Request::STATUS_ACTIVE);
        $client->assignments()->create(['request_id' => $request->id]);
        $comment = $this->comment;
        $this->json('PUT', 'api/requests/' . $request->id, ['comment' => $comment])
            ->assertStatus(401);

    }

    /**
     * @depends test_that_entry_point_available
     */
    public function test_that_cant_resolve_when_not_assigned_to_you()
    {
        /** @var User $client */
        $client = User::factory()->has(Role::factory()->state(['type' => Role::CLIENT]))->createOne();
        $wrongRequestId = $client->requests()->create(['message' => 'hello', 'status' => Request::STATUS_ACTIVE])->id;


        /** @var User $anotherEmployee */
        $anotherEmployee = User::factory()->has(Role::factory()->state(['type' => Role::EMPLOYEE]))->createOne();
        $anotherEmployee->assignments()->create(['request_id' => $wrongRequestId]);

        $currentEmployee = $this->actingAsRole(Role::EMPLOYEE);


        $this->json('PUT', 'api/requests/' . $wrongRequestId, ['comment' => $this->comment])->assertStatus(401);
    }

    protected function setUp(): void
    {
        parent::setUp(); // TODO: Change the autogenerated stub
        $this->employees = $this->generateUsers(Role::EMPLOYEE);
        $this->clients = $this->generateUsers(Role::CLIENT);
    }

    private function generateUsers(string $role)
    {
        $tokenIssuer = App::make(TokenIssuer::class);
        User::factory(15)
            ->has(
                Role::factory()
                    ->state(function (array $attributes, User $user) use ($role) {
                        return ['user_id' => $user->id, 'type' => $role];
                    }))
            ->has(
                Request::factory()
                    ->state(function (array $attributes, User $user) {
                        return ['user_id' => $user->id];
                    }))
            ->create()
            ->each(function (User $user) use ($tokenIssuer) {
                $tokenIssuer->createToken($user);
            });
    }
}
