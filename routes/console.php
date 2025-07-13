<?php

use App\Jobs\NewsSyncJob;
use App\Services\GuardianApiService;
use App\Services\NewsApiAiService;
use App\Services\NewsApiOrgService;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schedule;

Artisan::command("inspire", function () {
    $this->comment(Inspiring::quote());
})->purpose("Display an inspiring quote");

/**
    Dispatch job to sync articles from https://newsapi.org source
*/
Schedule::call(function () {
    $newsApiOrgApiKey = env("NEWSAPI_ORG_KEY");
    if ($newsApiOrgApiKey == null || $newsApiOrgApiKey == "") {
        Log::error(
            "No API key found for https://newsapi.org. Please provide a key"
        );
        return;
    }
    $newsApiOrgService = new NewsApiOrgService($newsApiOrgApiKey);
    NewsSyncJob::dispatch($newsApiOrgService);
})->everyFifteenMinutes();

/**
    Dispatch job to sync articles from https://newsapi.ai source
*/
Schedule::call(function () {
    $newsApiAiApiKey = env("NEWSAPI_AI_KEY");
    if ($newsApiAiApiKey == null || $newsApiAiApiKey == "") {
        Log::error(
            "No API key found for https://newsapi.ai. Please provide a key"
        );
        return;
    }
    $newsApiAiService = new NewsApiAiService($newsApiAiApiKey);
    NewsSyncJob::dispatch($newsApiAiService);
})->everyFifteenMinutes();

/**
    Dispatch job to sync articles from https://open-platform.theguardian.com source
*/
Schedule::call(function () {
    $theGuardianApiKey = env("GUARDIAN_KEY");
    if ($theGuardianApiKey == null || $theGuardianApiKey == "") {
        Log::error(
            "No API key found for https://open-platform.theguardian.com. Please provide a key"
        );
        return;
    }
    $theGuardianApiService = new GuardianApiService($theGuardianApiKey);
    NewsSyncJob::dispatch($theGuardianApiService);
})->everyMinute();
