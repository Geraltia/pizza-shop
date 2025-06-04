<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\GenerateSalesReportJob;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class AdminReportController extends Controller
{
    public function generateReport()
    {
        $uuid = Str::uuid();

        $report = Report::create([
            'uuid' => $uuid,
            'status' => 'pending',
        ]);

        GenerateSalesReportJob::dispatch($report);

        return response()->json([
            'uuid' => $uuid,
            'message' => 'Отчёт поставлен в очередь',
            'status' => 'pending',
        ], Response::HTTP_ACCEPTED);
    }
}
