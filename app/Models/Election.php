<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Election extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'start_date',
        'end_date',
        'start_time',
        'end_time',
        'status',
        'is_published',
        'access_code',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_published' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($election) {
            if (empty($election->access_code)) {
                $election->access_code = strtoupper(Str::random(8));
            }
        });
    }

    // Relationship: Election belongs to one Organizer (User)
    public function organizer()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relationship: Election has many Candidates
    public function candidates()
    {
        return $this->hasMany(Candidate::class);
    }

    // Relationship: Election has many Rules
    public function rules()
    {
        return $this->hasMany(ElectionRule::class)->orderBy('order');
    }

    // Relationship: Election has one Settings
    public function settings()
    {
        return $this->hasOne(ElectionSetting::class);
    }

    // Relationship: Election has many Votes
    public function votes()
    {
        return $this->hasMany(Vote::class);
    }

    // Scope: Only get elections for current organizer
    public function scopeForOrganizer($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    // Scope: Only get published elections
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    // Scope: Only get active elections
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    // Helper: Check if election is active
    public function isActive()
    {
        return $this->status === 'active' && $this->is_published;
    }

    // Helper: Get total votes count
    public function getTotalVotesAttribute()
    {
        return $this->votes()->count();
    }

    // Helper: Get total voters count (including abstain)
    public function getTotalVotersAttribute()
    {
        return $this->votes()->distinct('voter_id')->count();
    }
}
