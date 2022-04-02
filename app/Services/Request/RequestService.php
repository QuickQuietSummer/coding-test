<?php

namespace App\Services\Request;

use App\Events\RequestResolved;
use App\Mail\RequestContact;
use App\Models\Request;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class RequestService
{
    /**
     * Returns id
     * @param User $user
     * @param string $message
     * @return int
     */
    public function createRequest(User $user, string $message): int
    {
        if (!isset($message) || empty($message)) {
            abort(422, 'Need message');
        }
        $request = $user->requests()->create([
            'status' => Request::STATUS_ACTIVE,
            'message' => $message,
        ]);
        return $request->id;
    }

    /**
     * Returns id
     * @param int $id
     * @param string $comment
     * @param User $employee
     * @return int
     */
    public function resolveRequest(int $id, string $comment, User $employee): int
    {
        $request = Request::whereId($id)->firstOr(function () {
            abort(404, 'Not found request');
        });
        if ($request->assignment === null) {
            abort(422, 'Is not assigned request');
        }

        if ($request->assignment->user_id !== $employee->id) {
            abort(401, 'Cannot resolve request which not assigned to you');
        }
        if ($request->status === Request::STATUS_RESOLVED) {
            abort(422, 'Already resolved');
        }
        $request->update([
            'comment' => $comment,
            'status' => Request::STATUS_RESOLVED
        ]);
        RequestResolved::dispatch($request);
        return $request->id;
    }

    /**
     * Returns id
     * @param int $id
     * @param User $employee
     * @return int
     */
    public function assignRequest(int $id, User $employee): int
    {
        $request = Request::whereId($id)->firstOr(function () {
            abort(404, 'Not found request');
        });
        if ($request->assignment !== null) {
            abort(422, 'Already assigned');
        }
        if ($request->status === Request::STATUS_RESOLVED) {
            abort(422, 'Already resolved');
        }
        if ($employee->role->type !== Role::EMPLOYEE) {
            abort(401, 'Only for employees');
        }
        return $employee->assignments()->create(['request_id' => $id])->id;
    }

    public function contact(int $requestId, string $employeeMessage, User $employeeFrom): void
    {
        $request = Request::whereId($requestId)->first();
        if ($request === null) {
            abort(404, 'Request not found');
        }
        $assignedRequest = $employeeFrom->assignments()->where('request_id', '=', $requestId)->first();
        if ($assignedRequest === null || $request->assignment === null || $employeeFrom->assignments === null) {
            abort(422, 'Not assigned');
        }
        Mail::to($request->user->email)->queue(new RequestContact($employeeFrom->email, $employeeFrom->name, $requestId, $employeeMessage));

    }
}
