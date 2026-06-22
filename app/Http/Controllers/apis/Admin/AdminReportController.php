<?php

namespace App\Http\Controllers\apis\Admin;

use App\Exports\GenericReportExport;
use App\Http\Controllers\apis\ApiController;
use App\Services\Admin\AdminReportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Excel as ExcelType;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;
use ZipArchive;

/**
 * Admin endpoints powering the 2026 Reports redesign.
 *
 * Intentionally NEW and additive — the legacy controller
 * App\Http\Controllers\apis\ReportController and its routes
 * (routes/apis/reports.php) are left untouched.
 */
class AdminReportController extends ApiController
{
    public function __construct(private readonly AdminReportService $service) {}

    /**
     * GET /api/v1/admin/reports/summary
     *
     * Returns the 6 report cards with their "last generated" timestamps.
     */
    public function summary(): JsonResponse
    {
        return $this->success(__('messages.retrieved'), $this->service->summary());
    }

    /**
     * GET /api/v1/admin/reports/compliance-preview
     *
     * Live grouped compliance preview shown beneath the cards.
     */
    public function compliancePreview(): JsonResponse
    {
        return $this->success(__('messages.retrieved'), [
            'rows' => $this->service->compliancePreview(),
        ]);
    }

    /**
     * GET /api/v1/admin/reports/{type}/export?format=csv|xlsx
     */
    public function export(Request $request, string $type): StreamedResponse|BinaryFileResponse|JsonResponse
    {
        if (!in_array($type, AdminReportService::TYPES, true)) {
            return $this->error(__('messages.not_found'), 404);
        }

        $format = strtolower($request->get('format', 'csv'));
        if (!in_array($format, ['csv', 'xlsx'], true)) {
            return $this->error('Invalid format. Allowed: csv, xlsx', 422);
        }

        [$headings, $rows] = $this->service->dataset($type);

        $this->service->logExport($type, $format, optional($request->user())->id);

        $filename = $this->buildFilename($type, $format);

        if ($format === 'csv') {
            return $this->streamCsv($filename, $headings, $rows);
        }

        $catalog = AdminReportService::catalog();
        $label   = $catalog[$type]['label'] ?? 'Report';

        return Excel::download(
            new GenericReportExport($headings, $rows, $label),
            $filename,
            ExcelType::XLSX,
        );
    }

    /**
     * GET /api/v1/admin/reports/export-all?format=csv|xlsx
     *
     * Returns a single ZIP archive containing all 6 reports in the
     * requested format.
     */
    public function exportAll(Request $request): BinaryFileResponse|JsonResponse
    {
        $format = strtolower($request->get('format', 'csv'));
        if (!in_array($format, ['csv', 'xlsx'], true)) {
            return $this->error('Invalid format. Allowed: csv, xlsx', 422);
        }

        $tmpDir = storage_path('app/reports-tmp-' . uniqid());
        if (!is_dir($tmpDir) && !mkdir($tmpDir, 0775, true) && !is_dir($tmpDir)) {
            return $this->error('Could not prepare export directory.', 500);
        }

        try {
            $zipPath = $tmpDir . DIRECTORY_SEPARATOR . 'reports.zip';
            $zip = new ZipArchive();
            if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
                return $this->error('Could not create archive.', 500);
            }

            $catalog = AdminReportService::catalog();
            $adminId = optional($request->user())->id;

            foreach (AdminReportService::TYPES as $type) {
                [$headings, $rows] = $this->service->dataset($type);
                $entryName = $this->buildFilename($type, $format);
                $filePath  = $tmpDir . DIRECTORY_SEPARATOR . $entryName;

                if ($format === 'csv') {
                    $this->writeCsvFile($filePath, $headings, $rows);
                } else {
                    $label = $catalog[$type]['label'] ?? 'Report';
                    Excel::store(
                        new GenericReportExport($headings, $rows, $label),
                        basename($filePath),
                        'local',
                        ExcelType::XLSX,
                    );
                    // Maatwebsite stores to storage/app — move into tmpDir.
                    $stored = storage_path('app/' . basename($filePath));
                    if (is_file($stored)) {
                        @rename($stored, $filePath);
                    }
                }

                $zip->addFile($filePath, $entryName);
                $this->service->logExport($type, $format, $adminId);
            }

            $zip->close();

            return response()->download(
                $zipPath,
                'reports-' . now()->format('Ymd-His') . '.zip',
                ['Content-Type' => 'application/zip'],
            )->deleteFileAfterSend(true);
        } finally {
            // Best-effort cleanup; the downloaded zip is deleted by the
            // response above, and any leftover per-report files will be
            // removed here.
            register_shutdown_function(function () use ($tmpDir) {
                if (is_dir($tmpDir)) {
                    foreach (glob($tmpDir . DIRECTORY_SEPARATOR . '*') ?: [] as $f) {
                        @unlink($f);
                    }
                    @rmdir($tmpDir);
                }
            });
        }
    }

    /* ------------------------------------------------------------------ *
     |  HELPERS                                                           |
     * ------------------------------------------------------------------ */

    private function buildFilename(string $type, string $format): string
    {
        $slug = str_replace(['_', ' '], '-', $type);
        $date = now()->format('Ymd');
        return "{$slug}-{$date}.{$format}";
    }

    /**
     * @param  list<string>                       $headings
     * @param  list<list<string|int|float|null>>  $rows
     */
    private function streamCsv(string $filename, array $headings, array $rows): StreamedResponse
    {
        return response()->streamDownload(function () use ($headings, $rows) {
            $out = fopen('php://output', 'w');
            // BOM so Excel opens UTF-8 cleanly.
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, $headings);
            foreach ($rows as $row) {
                fputcsv($out, $row);
            }
            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * @param  list<string>                       $headings
     * @param  list<list<string|int|float|null>>  $rows
     */
    private function writeCsvFile(string $path, array $headings, array $rows): void
    {
        $fh = fopen($path, 'w');
        if ($fh === false) {
            return;
        }
        fwrite($fh, "\xEF\xBB\xBF");
        fputcsv($fh, $headings);
        foreach ($rows as $row) {
            fputcsv($fh, $row);
        }
        fclose($fh);
    }
}
