<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Models\Transaction;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable,HasRoles;

    public function canAccessPanel(Panel $panel): bool
    {
      return $this->hasRole('admin');

    }
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'photo',
        'occupation',
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

    //aturan ini ada tambahan method hasMany ke model pivot courseStudent

    public function transaction(){
        return $this->hasMany(Transaction::class);
    }

    // use for get data from transaction table for user active subscription and output object
    public function getActiveSubscription(){
        // bisa mengambil method ORM transaction() karena masih di ssatu class
        return $this->transaction()
            ->where('is_paid', true)
            ->where('ended_at','>=', now())
            ->first();
    }

    // use for check if user has active subscription and output boolean
    public function hasActiveSubscription(){
        return $this->transaction()
            ->where('is_paid', true)
            ->where('ended_at','>=', now())
            ->exists();
    }

}
