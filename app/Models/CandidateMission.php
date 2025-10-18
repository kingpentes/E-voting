<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CandidateMission extends Model
{
    use HasFactory;

    protected $fillable = [
        'candidate_id',
        'mission',
        'order',
    ];

    // Relationship: Mission belongs to one Candidate
    public function candidate()
    {
        return $this->belongsTo(Candidate::class);
    }
}
