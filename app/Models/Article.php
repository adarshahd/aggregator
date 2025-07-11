<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Article extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'description',
        'url',
        'lang',
        'thumbnail',
        'time',
        'article_source_id',
        'article_author_id',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'time' => 'timestamp',
            'article_source_id' => 'integer',
            'article_author_id' => 'integer',
        ];
    }

    public function articleSource(): BelongsTo
    {
        return $this->belongsTo(ArticleSource::class);
    }

    public function articleAuthor(): BelongsTo
    {
        return $this->belongsTo(ArticleAuthor::class);
    }
}
