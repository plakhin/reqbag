<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Bag extends Model
{
    use HasFactory;

    public const ?string UPDATED_AT = null;

    protected $fillable = ['slug'];

    /** @return HasMany<Request> */
    public function requests(): HasMany
    {
        return $this->hasMany(Request::class);
    }
}
