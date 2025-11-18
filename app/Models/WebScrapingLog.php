<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model logu web scrapingu.
 *
 * Reprezentuje pojedynczą operację web scrapingu wykonaną podczas
 * wzbogacania danych produktu.
 *
 * @property int $id
 * @property int $product_description_id
 * @property string $search_query
 * @property string $url_scraped
 * @property array|null $data_extracted
 * @property bool $success
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read ProductDescription $productDescription
 */
class WebScrapingLog extends Model
{
    /**
     * Atrybuty które mogą być masowo przypisywane.
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'product_description_id',
        'search_query',
        'url_scraped',
        'data_extracted',
        'success',
    ];

    /**
     * Castowanie atrybutów.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'data_extracted' => 'array',
            'success' => 'boolean',
        ];
    }

    /**
     * Relacja do opisu produktu.
     * Każdy log należy do jednego opisu produktu.
     *
     * @return BelongsTo<ProductDescription, WebScrapingLog>
     */
    public function productDescription(): BelongsTo
    {
        return $this->belongsTo(ProductDescription::class);
    }
}
