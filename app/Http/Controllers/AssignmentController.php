<?php


namespace App\Http\Controllers;


use App\Http\Requests\Request\AssignmentStoreFormRequest;
use App\Models\Role;
use App\Services\Request\RequestService;
use Illuminate\Support\Facades\Response;

class AssignmentController extends Controller
{
    private RequestService $requestService;

    public function __construct(RequestService $requestService)
    {
        $this->requestService = $requestService;

        $this->middleware('auth:sanctum');
        $this->middleware('role:' . Role::EMPLOYEE, ['only' => ['store']]);
    }

    /**
     * Assign request for responsible employee
     *
     * Only for employees
     * @authenticated
     * @group Employee api
     */
    public function store(AssignmentStoreFormRequest $httpRequest)
    {
        $data = $httpRequest->validated();
        $assignmentId = $this->requestService->assignRequest($data['request_id'], $httpRequest->user());
        return Response::json([
            'message' => 'Created',
            'data' => $assignmentId
        ], 201);
    }
}
