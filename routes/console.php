<?php

use App\Jobs\NewsSyncJob;
use App\Services\NewsApiOrgService;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command("inspire", function () {
    $this->comment(Inspiring::quote());
})->purpose("Display an inspiring quote");

/**
    This will dispatch news sync jobs for all news providers and
    starts syncing news articles
*/
Schedule::call(function () {
    $newsApiOrgApiKey = env("NEWSAPI_ORG_KEY");
    $newsApiOrgService = new NewsApiOrgService($newsApiOrgApiKey);
    NewsSyncJob::dispatch($newsApiOrgService);
})->everyMinute();
