<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ElectionRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'election_id',
        'rule',
        'order',
    ];

    // Relationship: Rule belongs to one Election
    public function election()
    {
        return $this->belongsTo(Election::class);
    }
}
