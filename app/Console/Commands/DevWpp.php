<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class DevWpp extends Command
{
    protected $signature = 'dev:wpp';

    protected $description = 'Levanta php artisan serve + queue:work en paralelo para testear WhatsApp. ngrok va aparte.';

    public function handle(): int
    {
        $this->info('Dev WhatsApp — serve + queue:work en paralelo.');
        $this->line('  - HTTP:  http://127.0.0.1:8000');
        $this->line('  - Queue: database');
        $this->warn('ngrok se levanta aparte:  ngrok http 8000');
        $this->line('Ctrl+C para cortar todo.');
        $this->newLine();

        $serve = Process::fromShellCommandline('php artisan serve');
        $queue = Process::fromShellCommandline('php artisan queue:work --tries=3 --timeout=60');

        $serve->setTimeout(null);
        $queue->setTimeout(null);

        $serve->start();
        $queue->start();

        while ($serve->isRunning() || $queue->isRunning()) {
            if ($out = $serve->getIncrementalOutput()) {
                $this->output->write("<fg=green>[serve]</> {$out}");
            }
            if ($err = $serve->getIncrementalErrorOutput()) {
                $this->output->write("<fg=green>[serve]</> {$err}");
            }
            if ($out = $queue->getIncrementalOutput()) {
                $this->output->write("<fg=cyan>[queue]</> {$out}");
            }
            if ($err = $queue->getIncrementalErrorOutput()) {
                $this->output->write("<fg=cyan>[queue]</> {$err}");
            }
            usleep(200_000);
        }

        return self::SUCCESS;
    }
}
