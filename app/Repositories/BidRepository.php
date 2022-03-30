<?php

namespace App\Repositories;

use App\Models\Bid;
use Illuminate\Support\Facades\DB;

class BidRepository
{


    public function getAll(array $sort = null): array
    {
        if (!isset($sort) || gettype($sort) != 'array') {
            return Bid::all()->toArray();
        }
        $bidsQuery = DB::table('bids');
        if (isset($sort['status'])) {
            $isStatusAsc = strtolower($sort['status']) == strtolower(Bid::STATUS_ACTIVE);
            $bidsQuery = $bidsQuery->orderBy('status', $isStatusAsc ? 'ASC' : 'DESC');
        }
        if (isset($sort['date'])) {
//            dump($sort['date']);
            $bidsQuery = $bidsQuery->orderBy('created_at', strtoupper($sort['date']));
        }
        $bids = $bidsQuery->get()->toArray();
        return $bids;
    }
}
