<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class StartServices extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'services:start
                            {--queue-only : Only start the queue worker}
                            {--schedule-only : Only start the scheduler}';

    /**
     * The console command description.
     */
    protected $description = 'Start both the scheduler and queue worker in parallel';

    /**
     * The running processes.
     */
    protected array $processes = [];

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $queueOnly = $this->option('queue-only');
        $scheduleOnly = $this->option('schedule-only');

        // Validate options
        if ($queueOnly && $scheduleOnly) {
            $this->error('Cannot use both --queue-only and --schedule-only options together.');
            return self::FAILURE;
        }

        $this->info('Starting services...');
        $this->newLine();

        // Determine which services to start
        $startQueue = !$scheduleOnly;
        $startSchedule = !$queueOnly;

        // Start scheduler
        if ($startSchedule) {
            $this->info('🕐 Starting scheduler...');
            $schedulerProcess = new Process(
                [PHP_BINARY, 'artisan', 'schedule:work'],
                base_path()
            );
            $schedulerProcess->setTimeout(null);
            $schedulerProcess->start();
            $this->processes['scheduler'] = $schedulerProcess;
            $this->line('   Scheduler started (PID: ' . $schedulerProcess->getPid() . ')');
        }

        // Start queue worker
        if ($startQueue) {
            $this->info('⚙️  Starting queue worker...');
            $queueProcess = new Process(
                [PHP_BINARY, 'artisan', 'queue:work', '--queue=document-processing', '--tries=3', '--timeout=300'],
                base_path()
            );
            $queueProcess->setTimeout(null);
            $queueProcess->start();
            $this->processes['queue'] = $queueProcess;
            $this->line('   Queue worker started (PID: ' . $queueProcess->getPid() . ')');
        }

        $this->newLine();
        $this->info('✓ All services started successfully!');
        $this->newLine();
        $this->comment('Press Ctrl+C to stop all services...');
        $this->newLine();

        // Register signal handlers for graceful shutdown
        if (function_exists('pcntl_signal')) {
            pcntl_signal(SIGTERM, [$this, 'handleShutdown']);
            pcntl_signal(SIGINT, [$this, 'handleShutdown']);
        }

        // Monitor processes and display output
        try {
            while (true) {
                foreach ($this->processes as $name => $process) {
                    // Check if process is still running
                    if (!$process->isRunning()) {
                        $this->error("⚠️  {$name} stopped unexpectedly!");
                        $this->error($process->getErrorOutput());
                        $this->stopAllProcesses();
                        return self::FAILURE;
                    }

                    // Display output
                    $output = $process->getIncrementalOutput();
                    if (!empty($output)) {
                        $this->line("[{$name}] " . trim($output));
                    }

                    $errorOutput = $process->getIncrementalErrorOutput();
                    if (!empty($errorOutput)) {
                        $this->error("[{$name}] " . trim($errorOutput));
                    }
                }

                // Allow signal handling
                if (function_exists('pcntl_signal_dispatch')) {
                    pcntl_signal_dispatch();
                }

                usleep(100000); // Sleep for 100ms
            }
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            $this->stopAllProcesses();
            return self::FAILURE;
        }

        return self::SUCCESS;
    }

    /**
     * Handle shutdown signal.
     */
    public function handleShutdown(): void
    {
        $this->newLine();
        $this->info('Shutting down services...');
        $this->stopAllProcesses();
        exit(0);
    }

    /**
     * Stop all running processes.
     */
    protected function stopAllProcesses(): void
    {
        foreach ($this->processes as $name => $process) {
            if ($process->isRunning()) {
                $this->line("Stopping {$name}...");
                $process->stop(3, SIGTERM);
            }
        }
        $this->info('All services stopped.');
    }
}

