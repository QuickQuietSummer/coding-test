<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bid extends Model
{
    public const STATUS_ACTIVE = 'Active';
    public const STATUS_RESOLVED = 'Resolved';

    use HasFactory;
}
