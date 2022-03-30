<?php

namespace App\Http\Controllers;

use App\Repositories\RequestRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class RequestController extends Controller
{
    private RequestRepository $requestRepository;

    public function __construct(RequestRepository $requestRepository)
    {
        $this->requestRepository = $requestRepository;
    }

    /**
     * Get all requests
     *
     * @queryParam sort_date string Sorting by created. Values: new, old.
     * @queryParam sort_status string Sorting by status. Values: active, resolved.
     */
    public function index(Request $httpRequest)
    {
        $sortDate = $httpRequest->get('sort_date', null);
        $sortStatus = $httpRequest->get('sort_status', null);

        $serviceRequests = $this->requestRepository->getAll($sortDate, $sortStatus);

        $result = [
            'status' => 200,
            'message' => 'OK',
            'data' => $serviceRequests
        ];

        return Response::json($result);
    }
}
