<?php

namespace App\Http\Controllers;

use App\Repositories\BidRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class BidController extends Controller
{
    private BidRepository $bidRepository;

    public function __construct(BidRepository $bidRepository)
    {
        $this->bidRepository = $bidRepository;
    }

    /**
     * @queryParam sort_date string Sorting by created. Values: new, old.
     * @queryParam sort_status string Sorting by status. Values: active, resolved.
     */
    public function index(Request $request)
    {
        $sortDate = $request->get('sort_date', null);
        $sortStatus = $request->get('sort_status', null);

        $bids = $this->bidRepository->getAll($sortDate, $sortStatus);

        $result = [
            'status' => 200,
            'message' => 'OK',
            'data' => $bids
        ];

        return Response::json($result);
    }
}
