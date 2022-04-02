<?php

namespace App\Repositories\Request;

use App\Models\Request;
use Carbon\Carbon;
use Carbon\Exceptions\InvalidFormatException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class RequestRepository
{
    private array $statusDictionary = [
        'ACTIVE' => 'ASC',
        'RESOLVED' => 'DESC',
    ];
    private array $dateDictionary = [
        'OLD' => 'ASC',
        'NEW' => 'DESC',
    ];

    public function getAll(
        string|null $sortDateValue,
        string|null $sortStatusValue,
        string|null $start,
        string|null $end,
        string|null $filterStatus,
        bool|null $assignedToMe,
    ): array
    {
        $builder = Request::query();
        $builder = $this->filterAssignedToMe($builder, $assignedToMe, Auth::id());
        $builder = $this->filterStatus($builder, $filterStatus);
        $builder = $this->sort($builder, 'status', $sortStatusValue, $this->statusDictionary);
        $builder = $this->filterDate($builder, $start, $end);
        $builder = $this->sort($builder, 'created_at', $sortDateValue, $this->dateDictionary);
        return $builder->get()->toArray();
    }

    private function filterAssignedToMe(Builder $builder, bool|null $assignedToMe, int $employeeId)
    {
        if (!$assignedToMe) {
            return $builder;
        }
        return $builder->whereHas('assignment', function ($q) use ($employeeId) {
            $q->where('user_id', '=', $employeeId);
        });
    }

    private function filterStatus(Builder $builder, string|null $status)
    {
        if (!isset($status)) return $builder;
        if (!array_key_exists(strtoupper($status), $this->statusDictionary)) return $builder;

        return $builder->where('status', '=', ucfirst(strtolower($status)));
    }

    private function filterDate(Builder $builder, string|null $start, string|null $end)
    {
        if (!isset($start) || !isset($end)) return $builder;
        try {
            $parsedStart = Carbon::parse($start);
            $parsedEnd = Carbon::parse($end);
        } catch (InvalidFormatException) {
            return $builder;
        }
        return $builder->whereBetween('created_at', [$parsedStart, $parsedEnd]);
    }

    private function sort(Builder $builder, string $sortColumn, string|null $sortValue, array $dictionary): Builder
    {
        if (!isset($sortValue)) return $builder;
        $sortKey = strtoupper($sortValue);
        if (!array_key_exists($sortKey, $dictionary)) return $builder;
        return $builder->orderBy($sortColumn, $dictionary[$sortKey]);
    }
}
