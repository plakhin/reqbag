<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\BagFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\DB;
use Plakhin\RequestChronicle\Models\Request;

class Bag extends Model
{
    /** @use HasFactory<BagFactory> */
    use HasFactory;

    public const ?string UPDATED_AT = null;

    protected $fillable = ['slug'];

    /** @return MorphMany<Request, $this> */
    public function requests(): MorphMany
    {
        return $this->morphMany(Request::class, 'model');
    }

    /** @param array<int> $ids */
    public static function destroyWithRequests(array $ids): int
    {
        return DB::transaction(function () use ($ids) {
            Request::where('model_type', static::class)->whereIn('model_id', $ids)->delete();

            return static::destroy($ids);
        });
    }
}
