<?php

declare(strict_types=1);

namespace App\Models;

use App\Services\Contracts\AiRequestAnalyzer;
use Database\Factories\AiAnalysisFactory;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Plakhin\RequestChronicle\Models\Request;

class AiAnalysis extends Model
{
    /** @use HasFactory<AiAnalysisFactory> */
    use HasFactory;

    public const ?string UPDATED_AT = null;

    protected $primaryKey = 'request_id';

    protected $fillable = [
        'request_id',
        'analysis_result',
    ];

    protected $attributes = [
        'analysis_result' => '{"is_successful":false,"response":""}',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'analysis_result' => AsArrayObject::class,
        ];
    }

    /** @return BelongsTo<Request, $this> */
    public function requests(): BelongsTo
    {
        return $this->belongsTo(Request::class);
    }

    /** @param Builder<static> $query */
    public function scopeForRequest(Builder $query, Request $request): void
    {
        $query->where('request_id', $request->id);
    }

    public static function makeForRequest(Request $request): self
    {
        try {
            $response = resolve(AiRequestAnalyzer::class)->analyze($request);
            $isSuccessful = true;
        } catch (Exception $e) {
            $response = 'There was an error trying to analyze the request: '.$e->getMessage();
            $isSuccessful = false;
        }

        return static::updateOrCreate(
            ['request_id' => $request->id],
            [
                'analysis_result' => [
                    'is_successful' => $isSuccessful,
                    'response' => $response,
                ],
            ]
        );
    }
}
