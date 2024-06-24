<?php

namespace App\Models;

use App\Enums\HttpMethod;
use Illuminate\Database\Eloquent\Casts\Attribute;
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

    /** @return Attribute<array<string>, never> */
    protected function flatHeaders(): Attribute
    {
        return Attribute::make(
            get: fn (): array => array_map(fn ($header) => $header[0], (array) $this->headers),
        );
    }

    /** @return Attribute<array<string>, never> */
    protected function getVariables(): Attribute
    {
        return Attribute::make(
            get: function (): array {
                $vars = [];
                parse_str(urldecode(strval(parse_url($this->url, PHP_URL_QUERY))), $vars);

                return $vars;
            },
        );
    }
}
