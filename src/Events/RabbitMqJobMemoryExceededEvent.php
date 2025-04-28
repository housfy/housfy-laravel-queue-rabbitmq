<?php

namespace Housfy\LaravelQueueRabbitMQ\Events;

use Illuminate\Queue\Jobs\Job;

class RabbitMqJobMemoryExceededEvent
{
    /**
     * Create a new event instance.
     *
     * @param  int  $status
     * @return void
     */
    public function __construct(
        public int $status = 0,
        public Job $job
    ) {
        $this->status = $status;
        $this->job = $job;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function getJob(): Job
    {
        return $this->job;
    }
}