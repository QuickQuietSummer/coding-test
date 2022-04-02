<?php


namespace Tests\Feature\Request;


use App\Models\Request;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\Feature\Request\Traits\ActingAsRole;
use Tests\TestCase;

class RequestAssigningTest extends TestCase
{
    use DatabaseMigrations;
    use ActingAsRole;

    public function test_that_entry_point_available()
    {
        /** @var User $client */
        $client = User::factory()->has(Role::factory()->state(['type' => Role::CLIENT]))->createOne();
        $request = $client->requests()->create(['message' => 'hello']);


        $this->actingAsRole(Role::EMPLOYEE);
        $this->json('POST', 'api/assignments', ['request_id' => $request->id])->assertStatus(201);
    }

    /**
     * @depends test_that_entry_point_available
     */
    public function test_that_only_employee_can_assigned()
    {
        /** @var User $client */
        $client = User::factory()->has(Role::factory()->state(['type' => Role::CLIENT]))->createOne();
        $request = $client->requests()->create(['message' => 'hello']);

        $this->actingAsRole(Role::CLIENT);
        $this->json('POST', 'api/assignments', ['request_id' => $request->id])->assertStatus(401);

    }

    /**
     * @depends test_that_entry_point_available
     */
    public function test_that_assigning_only_active_requests()
    {
        /** @var User $employee */
        $employee = User::factory()->has(Role::factory()->state(['type' => Role::EMPLOYEE]))->createOne();
        $request = $employee->requests()->create(['message' => 'hello','status'=>Request::STATUS_RESOLVED]);

        $this->actingAsRole(Role::EMPLOYEE);
        $this->json('POST', 'api/assignments', ['request_id' => $request->id])->assertStatus(422);
    }
}
