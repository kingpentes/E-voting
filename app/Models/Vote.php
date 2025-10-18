<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Vote extends Model
{
    use HasFactory;

    protected $fillable = [
        'election_id',
        'voter_id',
        'candidate_id',
        'vote_hash',
        'ip_address',
        'user_agent',
        'voted_at',
    ];

    protected $casts = [
        'voted_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($vote) {
            // Generate unique hash for vote verification
            $vote->vote_hash = hash('sha256', 
                $vote->election_id . 
                $vote->voter_id . 
                now()->timestamp . 
                Str::random(32)
            );
            
            $vote->voted_at = now();
            
            // Update candidate vote count
            if ($vote->candidate_id) {
                $candidate = Candidate::find($vote->candidate_id);
                if ($candidate) {
                    $candidate->increment('vote_count');
                }
            }
        });
    }

    // Relationship: Vote belongs to one Election
    public function election()
    {
        return $this->belongsTo(Election::class);
    }

    // Relationship: Vote belongs to one Voter (User)
    public function voter()
    {
        return $this->belongsTo(User::class, 'voter_id');
    }

    // Relationship: Vote belongs to one Candidate (nullable for abstain)
    public function candidate()
    {
        return $this->belongsTo(Candidate::class);
    }

    // Helper: Check if vote is abstain
    public function isAbstain()
    {
        return is_null($this->candidate_id);
    }
}
