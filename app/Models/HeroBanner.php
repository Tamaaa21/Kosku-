<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HeroBanner extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'judul',
        'subjudul',
        'badge_text',
        'gambar_url',
        'urutan',
        'is_active',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'urutan' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Scope query to active banners only.
     *
     * @param  Builder<HeroBanner>  $query
     * @return Builder<HeroBanner>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope query to ordered banners.
     *
     * @param  Builder<HeroBanner>  $query
     * @return Builder<HeroBanner>
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('urutan', 'asc')->orderBy('id', 'asc');
    }
}
