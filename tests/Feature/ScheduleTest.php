<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Console\Scheduling\Event as SchedulingEvent;
use Tests\TestCase;

class ScheduleTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_jobs_command_is_scheduled(): void
    {
        $schedule = app()->make(Schedule::class);
        
        $events = collect($schedule->events())->filter(function (SchedulingEvent $event) {
            return stripos($event->command, 'queue:prune-failed') !== false;
        });

        $this->assertCount(1, $events, 'Falha: Queue:prune-failedão foi agendado.');
        
        $this->assertEquals('0 0 * * *', $events->first()->expression);
    }
}
