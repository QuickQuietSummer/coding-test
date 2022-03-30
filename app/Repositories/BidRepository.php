<?php

namespace App\Repositories;

use App\Models\Bid;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class BidRepository
{
    private array $statusDictionary = [
        'ACTIVE' => 'ASC',
        'RESOLVED' => 'DESC',
    ];
    private array $dateDictionary = [
        'NEW' => 'DESC',
        'OLD' => 'ASC',
    ];

    public function getAll(string|null $sortDate = null, string|null $sortStatus = null): array
    {
        $bidsQuery = Bid::query();

        $bidsQuery = $this->sort($bidsQuery, 'status', $sortStatus, $this->statusDictionary);
        $bidsQuery = $this->sort($bidsQuery, 'created_at', $sortDate, $this->dateDictionary);

        return $bidsQuery->get()->toArray();
    }

    private function sort(Builder $builder, string $sortColumn, string|null $sortValue = null, array $dictionary): Builder
    {
        if (!isset($sortValue)) return $builder;
        $sortKey = strtoupper($sortValue);
        if (!array_key_exists($sortKey, $dictionary)) return $builder;
        return $builder->orderBy($sortColumn, $dictionary[$sortKey]);
    }
}
