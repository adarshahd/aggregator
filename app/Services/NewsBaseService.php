<?php
declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Collection;

interface NewsBaseService
{
    function getArticles(string $query): Collection;
    function getTopArticles(): Collection;
}
