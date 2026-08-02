<?php

namespace App\Console\Commands;

use App\Models\Facility;
use App\Services\DelinquencyService;
use Illuminate\Console\Command;

class RunDelinquency extends Command
{
    protected $signature = 'storagesoftai:delinquency {--facility=}';

    protected $description = 'Run late/lockout/lien/auction delinquency ladder for facilities';

    public function handle(DelinquencyService $service): int
    {
        $query = Facility::query()->where('is_active', true);
        if ($this->option('facility')) {
            $query->where('id', $this->option('facility'));
        }

        foreach ($query->with('settings')->get() as $facility) {
            $summary = $service->run($facility);
            $this->info($facility->name.': '.json_encode($summary));
        }

        return self::SUCCESS;
    }
}
