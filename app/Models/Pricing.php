<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pricing extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'duration',
        'price',
    ];

    public function transaction(){
        return $this->hasMany(Transaction::class);
    }

    public function isSubscribedByUser($userId)
    {
        return $this->transaction() //mengakses relasi transaction method yang diatas
            ->where('user_id', $userId)
            ->where('is_paid',  true)
            ->where('ended_at', '>=', now())
            ->exists();
    }
}
