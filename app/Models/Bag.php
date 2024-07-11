<?php

namespace App\Models;

use Database\Factories\BagFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Bag extends Model
{
    /** @use HasFactory<BagFactory> */
    use HasFactory;

    public const ?string UPDATED_AT = null;

    protected $fillable = ['slug'];

    /** @return HasMany<Request> */
    public function requests(): HasMany
    {
        return $this->hasMany(Request::class);
    }
}
