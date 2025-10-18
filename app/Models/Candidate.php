<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Candidate extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'election_id',
        'number',
        'name',
        'photo',
        'visi',
        'vote_count',
    ];

    // Relationship: Candidate belongs to one Election
    public function election()
    {
        return $this->belongsTo(Election::class);
    }

    // Relationship: Candidate has many Missions
    public function missions()
    {
        return $this->hasMany(CandidateMission::class)->orderBy('order');
    }

    // Relationship: Candidate has many Votes
    public function votes()
    {
        return $this->hasMany(Vote::class);
    }

    // Helper: Update vote count cache
    public function updateVoteCount()
    {
        $this->vote_count = $this->votes()->count();
        $this->save();
    }

    // Helper: Get vote percentage
    public function getVotePercentageAttribute()
    {
        $totalVotes = $this->election->total_votes;
        
        if ($totalVotes == 0) {
            return 0;
        }
        
        return round(($this->vote_count / $totalVotes) * 100, 2);
    }
}
