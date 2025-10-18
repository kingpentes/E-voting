<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ElectionSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'election_id',
        'allow_abstain',
        'show_results_after_vote',
        'require_confirmation',
        'allow_vote_change',
        'max_votes_per_voter',
    ];

    protected $casts = [
        'allow_abstain' => 'boolean',
        'show_results_after_vote' => 'boolean',
        'require_confirmation' => 'boolean',
        'allow_vote_change' => 'boolean',
    ];

    // Relationship: Settings belongs to one Election
    public function election()
    {
        return $this->belongsTo(Election::class);
    }
}
