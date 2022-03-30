<?php

namespace App\Http\Controllers;

use App\Http\Requests\RequestFormRequest;
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
     * @queryParam start string Start of date filter. Values: Carbon compatible string.
     *
     * For example: 2010-1-1 00:00:00
     *
     * @queryParam end string End of date filter. Values: Carbon compatible string.
     *
     * For example: 2020-12-25 12:45:16
     *
     * @queryParam filter_status string Filter by status. Values: active, resolved.
     */
    public function index(Request $httpRequest)
    {
        $sortDate = $httpRequest->get('sort_date', null);
        $sortStatus = $httpRequest->get('sort_status', null);
        $start = $httpRequest->get('start', null);
        $end = $httpRequest->get('end', null);
        $filterStatus = $httpRequest->get('filter_status', null);

        $serviceRequests = $this->requestRepository->getAll($sortDate, $sortStatus, $start, $end, $filterStatus);

        $result = [
            'message' => 'OK',
            'data' => $serviceRequests
        ];

        return Response::json($result);
    }
}
