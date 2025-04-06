<?php


namespace App\Models;

use App\Models\Domain;
use Illuminate\Database\Eloquent\Model;


class Tenant extends Model
{
    protected $fillable = ['id', 'data'];

    // Cast the 'data' field to an array so that it can be accessed as an array
    protected $casts = [
        'data' => 'array',
    ];
}
