<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');


Schedule::command('queue:prune-failed --hours=48')->daily();

Schedule::call(function() {
    $failedCount = DB::table('failed_jobs')->count();

    if($failedCount > 0){
        Log::warning("Monitoramento Diário: Contagem de: {$failedCount} jobs falhos");
    } else{
        Log::info("Monitoramento Diário: Nenhum job falho encontrado");
    }
})->dailyAt('08:00')->description('Monitora a saúde das filas e gera logs');