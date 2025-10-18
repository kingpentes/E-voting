<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'id_number',
        'face_photo',
        'organization',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Relationship: User (Organizer) has many Elections
    public function elections()
    {
        return $this->hasMany(Election::class);
    }

    // Relationship: User (Voter) has many Votes
    public function votes()
    {
        return $this->hasMany(Vote::class, 'voter_id');
    }

    // Helper: Check if user is organizer
    public function isOrganizer()
    {
        return $this->role === 'organizer';
    }

    // Helper: Check if user is voter
    public function isVoter()
    {
        return $this->role === 'voter';
    }

    // Helper: Check if user is admin
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    // Helper: Check if user has voted in specific election
    public function hasVotedIn($electionId)
    {
        return $this->votes()->where('election_id', $electionId)->exists();
    }
}
