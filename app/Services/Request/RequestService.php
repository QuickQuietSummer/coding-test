<?php

namespace App\Services\Request;

use App\Events\RequestResolved;
use App\Models\Request;
use App\Models\User;

class RequestService
{
    /**
     * Returns id
     * @param User $user
     * @param string $message
     * @return string|int
     */
    public function createRequest(User $user, string $message): string|int
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
     * @param $comment
     * @return string|int
     */
    public function resolveRequest(int $id, $comment): string|int
    {
        $request = Request::whereId($id)->firstOr(function () {
            abort(404, 'Not found request');
        });
        if ($request->status == Request::STATUS_RESOLVED) abort(422, 'Already resolved');
        $request->update([
            'comment' => $comment,
            'status' => Request::STATUS_RESOLVED
        ]);
        RequestResolved::dispatch($request);
        return $request->id;
    }
}
