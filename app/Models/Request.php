<?php

namespace App\Models;

use App\Enums\HttpMethod;
use App\Services\Contracts\AiRequestAnalyzer;
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
        'is_analysis_requested',
        'analysis',
        'ips',
    ];

    /** @return array<mixed> */
    protected function casts(): array
    {
        return [
            'method' => HttpMethod::class,
            'headers' => 'array',
            'post' => 'array',
            'is_analysis_requested' => 'boolean',
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

    /** @return Attribute<mixed, ?string> */
    protected function analysis(): Attribute
    {
        return Attribute::make(
            get: function ($value): mixed {
                if ($value) {
                    return $value;
                }

                $analyzer = resolve(AiRequestAnalyzer::class);

                if (! $this->is_analysis_requested) {
                    return $analyzer->isConfigured()
                        ? 'Analysis were not performed yet.'
                        : 'Analyzer is not configured.';
                }

                if (! $analyzer->isConfigured()) {
                    return 'AI Analyzer is not properly configured';
                }

                try {
                    $analysis = $analyzer->analyze($this);
                } catch (\Exception $e) {
                    return 'There was an error trying to analyze the request: '.$e->getMessage();
                }

                $this->analysis = $analysis;
                $this->save();

                return $analysis;
            },
        );
    }
}
