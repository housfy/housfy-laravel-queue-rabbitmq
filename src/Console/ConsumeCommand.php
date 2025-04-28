<?php

namespace Housfy\LaravelQueueRabbitMQ\Console;

use Illuminate\Contracts\Queue\Job;
use Illuminate\Queue\Console\WorkCommand;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Housfy\LaravelQueueRabbitMQ\Consumer;

class ConsumeCommand extends WorkCommand
{
    protected $signature = 'rabbitmq:consume
                            {connection? : The name of the queue connection to work}
                            {--name=default : The name of the consumer}
                            {--queue= : The names of the queues to work}
                            {--once : Only process the next job on the queue}
                            {--stop-when-empty : Stop when the queue is empty}
                            {--delay=0 : The number of seconds to delay failed jobs (Deprecated)}
                            {--backoff=0 : The number of seconds to wait before retrying a job that encountered an uncaught exception}
                            {--max-jobs=0 : The number of jobs to process before stopping}
                            {--max-time=0 : The maximum number of seconds the worker should run}
                            {--force : Force the worker to run even in maintenance mode}
                            {--memory=128 : The memory limit in megabytes}
                            {--sleep=3 : Number of seconds to sleep when no job is available}
                            {--timeout=60 : The number of seconds a child process can run}
                            {--tries=1 : Number of times to attempt a job before logging it failed}
                            {--rest=0 : Number of seconds to rest between jobs}

                            {--max-priority=}
                            {--consumer-tag}
                            {--prefetch-size=0}
                            {--prefetch-count=1000}
                           ';

    protected $description = 'Consume messages';

    public function handle(): void
    {
        /** @var Consumer $consumer */
        $consumer = $this->worker;

        $consumer->setContainer($this->laravel);
        $consumer->setName($this->option('name'));
        $consumer->setConsumerTag($this->consumerTag());
        $consumer->setMaxPriority((int) $this->option('max-priority'));
        $consumer->setPrefetchSize((int) $this->option('prefetch-size'));
        $consumer->setPrefetchCount((int) $this->option('prefetch-count'));

        parent::handle();
    }

    protected function consumerTag(): string
    {
        if ($consumerTag = $this->option('consumer-tag')) {
            return $consumerTag;
        }

        return Str::slug(config('app.name', 'laravel'), '_').'_'.getmypid();
    }

    protected function writeStatus(Job $job, $status, $type)
    {
        $log_line = '';

        try {
            $rawData = json_decode($job->getRawBody(), true);
            $unserialize_command = unserialize($rawData['data']['command']);
            $log_line = $unserialize_command->getClassName().' @ '.$unserialize_command->getMethodName();
        } catch (\Error $exception) {
            $log_line = $job->resolveName();
        }

        if (config('app.RABBIT_MQ_SHOW_MEM_USAGE')) {
            $real_memory = memory_get_usage(true) / 1024 / 1024;
            $log_line .= sprintf(' [Mem used: %d MB]', $real_memory);
        }

        $this->output->writeln(sprintf(
            "<{$type}>[%s][%s] %s</{$type}> %s",
            Carbon::now()->format('Y-m-d H:i:s'),
            $job->getJobId(),
            str_pad("{$status}:", 11), $log_line
        ));
    }
}
