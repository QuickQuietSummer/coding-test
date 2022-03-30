<?php

namespace Tests\Feature;

use App\Models\Bid;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use PhpParser\Node\Expr\Array_;
use Tests\TestCase;

class BidGetTest extends TestCase
{
    use DatabaseMigrations;

    public function test_that_entry_point_available()
    {
        $this->get('/api/bids/')->assertStatus(200);
    }

    public function test_that_incorrect_sort_is_just_no_sort_and_200()
    {
        $this->get('/api/bids/?sort_status=' . 'incorrect active or resolved')->assertStatus(200);
        $this->get('/api/bids/?sort_date=' . 'incorrect new or last')->assertStatus(200);

    }

    /**
     * @depends test_that_entry_point_available
     */
    public function test_that_response_exact_structure()
    {
        $this->get('/api/bids/')->assertJsonStructure([
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
    public function test_that_bids_sorting_by_active()
    {
        $resolvedBid = Bid::factory()->createOne([
            'status' => Bid::STATUS_RESOLVED
        ])->toArray();
        $activeBid = Bid::factory()->createOne([
            'status' => Bid::STATUS_ACTIVE
        ])->toArray();

        $bids = $this->get('/api/bids/?sort_status=' . 'active')['data'];

        self::assertTrue($bids[0]['id'] == $activeBid['id']);
        self::assertTrue($bids[1]['id'] == $resolvedBid['id']);
    }


    /**
     * @depends test_that_response_exact_structure
     */
    public function test_that_bids_sorting_by_resolved()
    {
        $activeBid = Bid::factory()->createOne([
            'status' => Bid::STATUS_ACTIVE
        ])->toArray();
        $resolvedBid = Bid::factory()->createOne([
            'status' => Bid::STATUS_RESOLVED
        ])->toArray();

        $bids = $this->get('/api/bids/?sort_status=' . 'resolved')['data'];

        self::assertTrue($bids[0]['id'] == $resolvedBid['id']);
        self::assertTrue($bids[1]['id'] == $activeBid['id']);
    }


    /**
     * @depends test_that_response_exact_structure
     */
    public function test_that_bids_sorting_by_date()
    {
        $date = Carbon::now();
        $bid0 = Bid::factory()->createOne(['created_at' => $date])->toArray();
        $date = $date->addDay();
        $bid1 = Bid::factory()->createOne(['created_at' => $date])->toArray();

        $bidsResponse = $this->get('/api/bids/?sort_date=' . 'last')['data'];

        self::assertTrue($bidsResponse[0]['id'] == $bid1['id']);
        self::assertTrue($bidsResponse[1]['id'] == $bid0['id']);

        $date = $date->addDay();
        $bid2 = Bid::factory()->createOne(['created_at' => $date])->toArray();
        $date = $date->addDay();
        $bid3 = Bid::factory()->createOne(['created_at' => $date])->toArray();

        $bidsResponse = $this->get('/api/bids/?sort_date=' . 'new')['data'];

        self::assertTrue($bidsResponse[0]['id'] == $bid0['id']);
        self::assertTrue($bidsResponse[1]['id'] == $bid1['id']);
        self::assertTrue($bidsResponse[2]['id'] == $bid2['id']);
        self::assertTrue($bidsResponse[3]['id'] == $bid3['id']);
    }
}
