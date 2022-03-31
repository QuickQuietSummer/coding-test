<?php

namespace Tests\Feature\Requests;

use App\Models\Request;
use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class RequestGetTest extends TestCase
{
    use DatabaseMigrations;

    public function test_that_entry_point_available()
    {
        $this->json('GET', '/api/requests/')->assertStatus(200);
    }

    /**
     * @depends test_that_entry_point_available
     */
    public function test_that_incorrect_sort_and_filter_is_just_no_sort_and_is_200()
    {

        $this->json('GET', '/api/requests/', ['sort_status' => 'incorrect active or resolved'])
            ->assertStatus(200);

        $this->json('GET', '/api/requests/', ['sort_date' => 'incorrect new or last'])
            ->assertStatus(200);

        $this->json('GET', '/api/requests/', ['start' => 'incorrect start time', 'end' => 'incorrect end time'])
            ->assertStatus(200);
    }

    /**
     * @depends test_that_entry_point_available
     */
    public function test_that_response_exact_structure()
    {
        Request::factory()->createOne(['status' => Request::STATUS_RESOLVED])->toArray();
        $this->json('GET', '/api/requests/')->assertJsonStructure([
            'message',
            'data' => [
                '*' => [
                    'id',
                    'user_id',
                    'status',
                    'message',
                    'comment',
                    'created_at',
                    'updated_at',
                ]
            ]
        ]);
    }

    /**
     * @depends test_that_response_exact_structure
     */
    public function test_that_requests_sorting_by_active()
    {
        $resolvedRequest = Request::factory()->createOne(['status' => Request::STATUS_RESOLVED])->toArray();
        $activeRequest = Request::factory()->createOne(['status' => Request::STATUS_ACTIVE])->toArray();

        $requests = $this->json('GET', '/api/requests/', ['sort_status' => 'active'])['data'];

        self::assertTrue($requests[0]['id'] == $activeRequest['id']);
        self::assertTrue($requests[1]['id'] == $resolvedRequest['id']);
    }

    /**
     * @depends test_that_response_exact_structure
     */
    public function test_that_requests_sorting_by_resolved()
    {
        $activeRequest = Request::factory()->createOne([
            'status' => Request::STATUS_ACTIVE
        ])->toArray();
        $resolvedRequest = Request::factory()->createOne([
            'status' => Request::STATUS_RESOLVED
        ])->toArray();

        $requests = $this->json('GET', '/api/requests/', ['sort_status' => 'resolved'])['data'];

        self::assertTrue($requests[0]['id'] == $resolvedRequest['id']);
        self::assertTrue($requests[1]['id'] == $activeRequest['id']);
    }

    /**
     * @depends test_that_response_exact_structure
     */
    public function test_that_requests_sorting_by_date()
    {
        $request0 = Request::factory()->createOne(['created_at' => Carbon::create(2015)])->toArray();
        $request1 = Request::factory()->createOne(['created_at' => Carbon::create(2016)])->toArray();

        $actualRequests = $this->json('GET', '/api/requests/', ['sort_date' => 'new'])['data'];

        self::assertTrue($actualRequests[0]['id'] == $request1['id']);
        self::assertTrue($actualRequests[1]['id'] == $request0['id']);

        $request2 = Request::factory()->createOne(['created_at' => Carbon::create(2017)])->toArray();
        $request3 = Request::factory()->createOne(['created_at' => Carbon::create(2018)])->toArray();

        $actualRequests = $this->json('GET', '/api/requests/', ['sort_date' => 'old'])['data'];

        self::assertTrue($actualRequests[0]['id'] == $request0['id']);
        self::assertTrue($actualRequests[1]['id'] == $request1['id']);
        self::assertTrue($actualRequests[2]['id'] == $request2['id']);
        self::assertTrue($actualRequests[3]['id'] == $request3['id']);
    }

    /**
     * @depends test_that_response_exact_structure
     */
    public function test_that_requests_filtering_by_date()
    {
        $request15 = Request::factory()->createOne(['created_at' => Carbon::create(2015)])->toArray();
        $request16 = Request::factory()->createOne(['created_at' => Carbon::create(2016)])->toArray();
        $request18 = Request::factory()->createOne(['created_at' => Carbon::create(2018)])->toArray();
        $request19 = Request::factory()->createOne(['created_at' => Carbon::create(2019)])->toArray();
        $request20 = Request::factory()->createOne(['created_at' => Carbon::create(2020)])->toArray();

        $actualRequests = $this->json('GET', '/api/requests/', ['start' => '2018-1-1', 'end' => '2020-1-1'])['data'];
        self::assertCount(3, $actualRequests);

        $isNotContainsFilteredData = true;
        foreach ($actualRequests as $eachRequest) {
            if ($eachRequest['id'] == $request15['id'] || $eachRequest['id'] == $request16['id']) {
                $isNotContainsFilteredData = false;
                break;
            }
        }
        self::assertTrue($isNotContainsFilteredData);
    }

    /**
     * test_that_response_exact_structure
     */
    public function test_that_requests_filtering_by_status()
    {
        $requestResolved = Request::factory()->createOne(['status' => Request::STATUS_RESOLVED])->toArray();
        $requestActive = Request::factory()->createOne(['status' => Request::STATUS_ACTIVE])->toArray();
        $actualRequests = $this->json('GET', '/api/requests/', ['filter_status' => 'resolved'])['data'];
        self::assertCount(1, $actualRequests);
        self::assertTrue($actualRequests[0]['id'] == $requestResolved['id']);
    }

    public function test_that_only_employee_can_get_all_requests()
    {
        $this->actingAsRole(Role::CLIENT);
        $this->json('GET', '/api/requests/')->assertStatus(401);

        $this->actingAsRole(Role::EMPLOYEE);
        $this->json('GET', '/api/requests/')->assertStatus(200);
    }

    private function actingAsRole($role)
    {
        $user = User::factory()->has(Role::factory()
            ->state(function (array $attributes, User $user) use ($role) {
                return ['user_id' => $user->id, 'type' => $role];
            }))
            ->createOne();
        $this->actingAs($user);
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsRole(Role::EMPLOYEE);
    }
}
