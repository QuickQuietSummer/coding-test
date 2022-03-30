<?php

namespace Tests\Feature;

use App\Models\Request;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use PhpParser\Node\Expr\Array_;
use Tests\TestCase;

class RequestGetTest extends TestCase
{
    use DatabaseMigrations;

    public function test_that_entry_point_available()
    {
        $this->get('/api/requests/')->assertStatus(200);
    }

    public function test_that_incorrect_sort_is_just_no_sort_and_200()
    {
        $this->get('/api/requests/?sort_status=' . 'incorrect active or resolved')->assertStatus(200);
        $this->get('/api/requests/?sort_date=' . 'incorrect new or last')->assertStatus(200);
    }

    /**
     * @depends test_that_entry_point_available
     */
    public function test_that_response_exact_structure()
    {
        $this->get('/api/requests/')->assertJsonStructure([
            'status',
            'message',
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'email',
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
        $resolvedBid = Request::factory()->createOne([
            'status' => Request::STATUS_RESOLVED
        ])->toArray();
        $activeBid = Request::factory()->createOne([
            'status' => Request::STATUS_ACTIVE
        ])->toArray();

        $requests = $this->get('/api/requests/?sort_status=' . 'active')['data'];

        self::assertTrue($requests[0]['id'] == $activeBid['id']);
        self::assertTrue($requests[1]['id'] == $resolvedBid['id']);
    }


    /**
     * @depends test_that_response_exact_structure
     */
    public function test_that_requests_sorting_by_resolved()
    {
        $activeBid = Request::factory()->createOne([
            'status' => Request::STATUS_ACTIVE
        ])->toArray();
        $resolvedBid = Request::factory()->createOne([
            'status' => Request::STATUS_RESOLVED
        ])->toArray();

        $requests = $this->get('/api/requests/?sort_status=' . 'resolved')['data'];

        self::assertTrue($requests[0]['id'] == $resolvedBid['id']);
        self::assertTrue($requests[1]['id'] == $activeBid['id']);
    }


    /**
     * @depends test_that_response_exact_structure
     */
    public function test_that_requests_sorting_by_date()
    {
        $date = Carbon::now();
        $request0 = Request::factory()->createOne(['created_at' => $date])->toArray();
        $date = $date->addDay();
        $request1 = Request::factory()->createOne(['created_at' => $date])->toArray();

        $actualRequests = $this->get('/api/requests/?sort_date=' . 'last')['data'];

        self::assertTrue($actualRequests[0]['id'] == $request1['id']);
        self::assertTrue($actualRequests[1]['id'] == $request0['id']);

        $date = $date->addDay();
        $request2 = Request::factory()->createOne(['created_at' => $date])->toArray();
        $date = $date->addDay();
        $request3 = Request::factory()->createOne(['created_at' => $date])->toArray();

        $actualRequests = $this->get('/api/requests/?sort_date=' . 'new')['data'];

        self::assertTrue($actualRequests[0]['id'] == $request0['id']);
        self::assertTrue($actualRequests[1]['id'] == $request1['id']);
        self::assertTrue($actualRequests[2]['id'] == $request2['id']);
        self::assertTrue($actualRequests[3]['id'] == $request3['id']);
    }
    public function test_that_requests_filtering_by_date(){
        $request15 = Request::factory()->createOne(['created_at' => Carbon::create(2015)])->toArray();
        $request16 = Request::factory()->createOne(['created_at' => Carbon::create(2016)])->toArray();
        $request18 = Request::factory()->createOne(['created_at' => Carbon::create(2018)])->toArray();
        $request19 = Request::factory()->createOne(['created_at' => Carbon::create(2019)])->toArray();
        $request20 = Request::factory()->createOne(['created_at' => Carbon::create(2020)])->toArray();

        $actualRequests = $this->get('/api/requests/?filterDate=' . 'new')['data'];
    }
}
