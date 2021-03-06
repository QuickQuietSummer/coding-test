<?php

namespace App\Http\Controllers;

use App\Http\Requests\Request\RequestContactFormRequest;
use App\Http\Requests\Request\RequestPutFormRequest;
use App\Http\Requests\Request\RequestStoreFormRequest;
use App\Models\Role;
use App\Repositories\Request\RequestRepository;
use App\Services\Request\RequestService;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Support\Facades\Response;

class RequestController extends Controller
{
    private RequestService $requestService;
    private RequestRepository $requestRepository;

    public function __construct(RequestRepository $requestRepository, RequestService $requestService)
    {
        $this->requestService = $requestService;
        $this->requestRepository = $requestRepository;

        $this->middleware('auth:sanctum');
        $this->middleware('role:' . Role::CLIENT, ['only' => ['store']]);
        $this->middleware('role:' . Role::EMPLOYEE, ['only' => ['index', 'update', 'contact']]);
    }

    /**
     * Get all requests
     *
     * Only for employees.
     * @authenticated
     * @group Employee api
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
     *
     * @queryParam assigned_to_me bool Filter only requests assigned to your employee account.
     */
    public function index(HttpRequest $httpRequest)
    {
        $sortDate = $httpRequest->get('sort_date');
        $sortStatus = $httpRequest->get('sort_status');
        $start = $httpRequest->get('start');
        $end = $httpRequest->get('end');
        $filterStatus = $httpRequest->get('filter_status');
        $assignedToMe = $httpRequest->get('assigned_to_me');

        $serviceRequests = $this->requestRepository->getAll($sortDate, $sortStatus, $start, $end, $filterStatus, $assignedToMe);


        return Response::json([
            'message' => 'OK',
            'data' => $serviceRequests
        ]);
    }

    /**
     * Resolve request with comment
     *
     * Only for employee
     * @authenticated
     * @group Employee api
     */
    public function update(RequestPutFormRequest $httpRequest, int $id)
    {
        $data = $httpRequest->validated();
        $requestId = $this->requestService->resolveRequest($id, $data['comment'], $httpRequest->user());
        return Response::json([
            'message' => 'Resolved',
            'data' => $requestId
        ], 200);
    }

    /**
     * Create new request
     *
     * Only for clients
     * @authenticated
     * @group Client api
     */
    public function store(RequestStoreFormRequest $httpRequest)
    {
        $user = $httpRequest->user();
        $data = $httpRequest->validated();
        $requestId = $this->requestService->createRequest($user, $data['message']);

        return Response::json([
            'message' => 'OK',
            'data' => $requestId,
        ], 201);
    }

    /**
     * Contact with client who initiate request
     *
     * Only for employees
     * @authenticated
     * @group Employee api
     */
    public function contact(RequestContactFormRequest $httpRequest)
    {
        $data = $httpRequest->validated();
        $this->requestService->contact($data['request_id'], $data['message'], $httpRequest->user());
        return Response::json([
            'message' => 'OK',
        ]);
    }
}
