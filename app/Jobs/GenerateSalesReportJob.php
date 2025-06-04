<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Order;
use App\Models\Report;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

final class GenerateSalesReportJob implements ShouldQueue
{
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(public Report $report)
    {
    }

    public function handle(): void
    {
        $this->report->update(['status' => 'processing']);

        try {
            $path = storage_path("app/reports/{$this->report->uuid}.jsonl");

            $ordersCount = Order::count();

            $line = json_encode([
                    'total_orders' => $ordersCount,
                    'generated_at' => now()->toDateTimeString(),
                ]) . PHP_EOL;

            file_put_contents($path, $line);

            $this->report->update([
                'status' => 'completed',
                'file_path' => $path,
            ]);
        } catch (\Exception $e) {
            $this->report->update(['status' => 'failed']);
            throw $e;
        }
    }

}
