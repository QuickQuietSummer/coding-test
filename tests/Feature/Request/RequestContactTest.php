<?php


namespace Tests\Feature\Request;


use App\Mail\RequestContact;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Mail;
use Tests\Feature\Request\Traits\ActingAsRole;
use Tests\TestCase;

class RequestContactTest extends TestCase
{
    use DatabaseMigrations;
    use ActingAsRole;

    public function test_that_entry_point_available()
    {

        /** @var User $client */
        $client = $this->actingAsRole(Role::CLIENT);
        $request = $client->requests()->create(['message' => 'hello']);

        $employee = $this->actingAsRole(Role::EMPLOYEE);
        $employee->assignments()->create(['request_id' => $request->id]);

        $this->json('POST', 'api/request-contact', ['request_id' => $request->id, 'message' => 'blablabla'])->assertStatus(200);
    }

    public function test_that_mail_queued()
    {
        Mail::fake();
        /** @var User $client */
        $client = $this->actingAsRole(Role::CLIENT);
        $request = $client->requests()->create(['message' => 'hello']);

        $employee = $this->actingAsRole(Role::EMPLOYEE);
        $employee->assignments()->create(['request_id' => $request->id]);

        $this->json('POST', 'api/request-contact', ['request_id' => $request->id, 'message' => 'blablabla']);
        Mail::assertQueued(RequestContact::class);
    }

    public function test_that_only_employee_can_contact()
    {
        /** @var User $client */
        $client = $this->actingAsRole(Role::CLIENT);
        $request = $client->requests()->create(['message' => 'hello']);

        $employee = $this->actingAsRole(Role::CLIENT);
        $employee->assignments()->create(['request_id' => $request->id]);

        $response = $this->json('POST', 'api/request-contact', ['request_id' => $request->id, 'message' => 'blablabla']);
        $response->assertStatus(401);
    }

    public function test_that_only_employee_which_assigned_can_contact()
    {
        /** @var User $client */
        $client = $this->actingAsRole(Role::CLIENT);
        $request = $client->requests()->create(['message' => 'hello']);

        $anotherEmployee = $this->actingAsRole(Role::EMPLOYEE);
        $anotherEmployee->assignments()->create(['request_id' => $request->id]);

        $employee = $this->actingAsRole(Role::EMPLOYEE);

        $response = $this->json('POST', 'api/request-contact', ['request_id' => $request->id, 'message' => 'blablabla']);
        $response->assertStatus(422);
    }
    public function test_that_only_assigned_requests_can_contact()
    {
        /** @var User $client */
        $client = $this->actingAsRole(Role::CLIENT);
        $request = $client->requests()->create(['message' => 'hello']);

        $employee = $this->actingAsRole(Role::EMPLOYEE);

        $response = $this->json('POST', 'api/request-contact', ['request_id' => $request->id, 'message' => 'blablabla']);
        $response->assertStatus(422);
    }
}
