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

    public function index(Request $request)
    {
        $sort = $request->get('sort', null);

        $bids = $this->bidRepository->getAll($sort);

        $result = [
            'status' => 200,
            'message' => 'OK',
            'data' => $bids
        ];

        return Response::json($result);
    }
}
