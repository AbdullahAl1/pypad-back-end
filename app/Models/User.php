<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username', 'first_name', 'last_name', 'email', 'email_verified_at', 'password', 'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        // 'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Get the JWT identifier for the user.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * User can have many codes.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function codes()
    {
        return $this->hasMany(Code::class);
    }

    /**
     * User can send many friend requests.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function sentFriendRequests()
    {
        return $this->hasMany(FriendRequest::class, 'sender_id');
    }

    /**
     * User can receive many friend requests.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function receivedFriendRequests()
    {
        return $this->hasMany(FriendRequest::class, 'receiver_id');
    }

    /**
     * User can have many friends.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function friends()
    {
        return $this->belongsToMany(User::class, 'friends', 'user_id', 'friend_id');
    }

    /**
     * User can send many chats.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function sentMessages()
    {
        return $this->hasMany(Chat::class, 'sender_id');
    }

    /**
     * User can receive many chats.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function receivedMessages()
    {
        return $this->hasMany(Chat::class, 'receiver_id');
    }
}
