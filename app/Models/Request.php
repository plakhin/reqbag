<?php

namespace App\Models;

use App\Enums\HttpMethod;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Request extends Model
{
    use HasFactory;

    public const ?string UPDATED_AT = null;

    protected $fillable = [
        'method',
        'url',
        'headers',
        'post',
        'raw',
        'ips',
    ];

    /** @return array<mixed> */
    protected function casts(): array
    {
        return [
            'method' => HttpMethod::class,
            'headers' => 'array',
            'post' => 'array',
            'ips' => 'array',
        ];
    }

    /** @return BelongsTo<Bag, Request> */
    public function bag(): BelongsTo
    {
        return $this->belongsTo(Bag::class);
    }
}
