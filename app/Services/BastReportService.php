<?php

namespace App\Services;

use App\Models\Setting;
use App\Models\User;
use App\Models\UserReport;
use Carbon\Carbon;

/**
 * BAST (Berita Acara Serah Terima) Report Service
 * 
 * Handles all business logic for BAST report generation, numbering, and storage
 * This service is isolated for module extraction purposes
 */
class BastReportService
{
    /**
     * Generate preview BAST report without saving
     */
    public function generateBastPreview(User $user): array
    {
        $adminUser = auth()->user()->loadMissing(['department', 'location']);
        $user->loadMissing(['department', 'location']);
        $assets = $user->assets()->get();
        $settings = Setting::getSettings();
        $today = Carbon::now();
        $reportNumber = $this->generateNextBastReportNumber($today);
        $todayFormatted = $today->isoFormat('dddd, D MMMM YYYY');

        return compact('user', 'adminUser', 'assets', 'settings', 'reportNumber', 'todayFormatted');
    }

    /**
     * Create and store BAST report record
     */
    public function createAndStoreBastReport(User $user): UserReport
    {
        $adminUser = auth()->user()->loadMissing(['department', 'location']);
        $user->loadMissing(['department', 'location']);
        $today = Carbon::now();
        $settings = Setting::getSettings();

        return $this->createBastReportRecord($user, $adminUser, $today, $settings);
    }

    /**
     * Find BAST report by report number
     */
    public function findByReportNumber(string $reportNumber): ?UserReport
    {
        return UserReport::with(['recipient', 'giver'])
            ->where('report_number', $reportNumber)
            ->first();
    }

    /**
     * Get all BAST reports
     */
    public function getAllReports()
    {
        return UserReport::with(['recipient', 'giver'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get BAST report by ID
     */
    public function getReportById($id): ?UserReport
    {
        return UserReport::with(['recipient', 'giver'])->find($id);
    }

    /**
     * Check if BAST report number exists
     */
    public function checkReportNumberExists(string $reportNumber): bool
    {
        return UserReport::where('report_number', $reportNumber)->exists();
    }

    /**
     * Get formatted BAST report data for rendering
     */
    public function formatReportForRender(UserReport $reportRecord, bool $isPreview = false): array
    {
        $user = $reportRecord->recipient_snapshot ?: $reportRecord->recipient;
        $adminUser = $reportRecord->giver_snapshot ?: $reportRecord->giver;

        if (!$user || !$adminUser) {
            return ['error' => 'Data BAST ditemukan, tetapi pengguna terkait telah dihapus dari sistem.'];
        }

        $assets = collect($reportRecord->assets_snapshot);
        $settings = Setting::getSettings();
        $reportHeader = $reportRecord->header_snapshot ?? [];
        $todayFormatted = $reportRecord->handover_date->isoFormat('dddd, D MMMM YYYY');

        return compact(
            'user',
            'adminUser',
            'assets',
            'settings',
            'reportRecord',
            'todayFormatted',
            'reportHeader',
            'isPreview'
        );
    }

    /**
     * Format BAST data for API response
     */
    public function formatReportsForApi(): array
    {
        $bastReports = $this->getAllReports();
        $rows = [];

        foreach ($bastReports as $index => $report) {
            $userName = $report->recipient_display_name;
            $actionBtn = '<a href="' . url('/bast-report/view/' . $report->id) . '" class="btn btn-sm btn-info" title="Lihat Dokumen" target="_blank" rel="noopener noreferrer"><i class="fas fa-eye"></i></a>';

            $rows[] = [
                'no' => $index + 1,
                'report_number' => $report->report_number,
                'name' => $userName,
                'actions' => $actionBtn
            ];
        }

        return [
            'total' => count($rows),
            'rows' => $rows
        ];
    }

    /**
     * PRIVATE METHODS
     */

    /**
     * Generate next BAST report number (format: 00001/BAST/IT/HO/I/2026)
     */
    private function generateNextBastReportNumber(Carbon $date): string
    {
        $lastRecordThisYear = UserReport::whereYear('handover_date', $date->year)
            ->latest('id')
            ->first();

        $nextSequence = 1;

        if ($lastRecordThisYear) {
            $lastSequence = (int)explode('/', $lastRecordThisYear->report_number)[0];
            $nextSequence = $lastSequence + 1;
        }

        $sequence = str_pad($nextSequence, 5, '0', STR_PAD_LEFT);

        return sprintf(
            '%s/BAST/IT/HO/%s/%s',
            $sequence,
            $this->toRomanNumeral($date->month),
            $date->year
        );
    }

    /**
     * Convert number to Roman numeral
     */
    private function toRomanNumeral(int $number): string
    {
        $map = [
            'M' => 1000,
            'CM' => 900,
            'D' => 500,
            'CD' => 400,
            'C' => 100,
            'XC' => 90,
            'L' => 50,
            'XL' => 40,
            'X' => 10,
            'IX' => 9,
            'V' => 5,
            'IV' => 4,
            'I' => 1,
        ];

        $roman = '';

        foreach ($map as $symbol => $value) {
            while ($number >= $value) {
                $number -= $value;
                $roman .= $symbol;
            }
        }

        return $roman;
    }

    /**
     * Create BAST report record in database
     */
    private function createBastReportRecord(User $user, User $adminUser, Carbon $date, ?Setting $settings): UserReport
    {
        $assetsSnapshot = $user->assets()->get()->map(function ($asset) {
            return [
                'asset_tag' => $asset->asset_tag,
                'name' => $asset->name,
                'serial' => $asset->serial,
            ];
        })->values()->all();

        return UserReport::create([
            'recipient_id' => $user->id,
            'giver_id' => $adminUser->id,
            'recipient_snapshot' => $this->makeBastUserSnapshot($user),
            'giver_snapshot' => $this->makeBastUserSnapshot($adminUser),
            'header_snapshot' => $this->makeBastHeaderSnapshot($settings),
            'report_number' => $this->generateNextBastReportNumber($date),
            'assets_snapshot' => $assetsSnapshot,
            'handover_date' => $date,
        ])->load(['recipient', 'giver']);
    }

    /**
     * Create user snapshot for BAST record
     */
    private function makeBastUserSnapshot(User $user): array
    {
        $user->loadMissing(['department', 'location']);

        return [
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'employee_num' => $user->employee_num,
            'jobtitle' => $user->jobtitle,
            'department' => [
                'name' => $user->department?->name,
            ],
            'location' => [
                'name' => $user->location?->name,
                'address' => $user->location?->address,
                'address2' => $user->location?->address2,
                'city' => $user->location?->city,
                'state' => $user->location?->state,
                'zip' => $user->location?->zip,
                'country' => $user->location?->country,
            ],
        ];
    }

    /**
     * Create header snapshot for BAST record
     */
    private function makeBastHeaderSnapshot(?Setting $settings): array
    {
        return [
            'site_name' => $settings?->site_name ?: config('app.name', 'Snipe-IT'),
            'logo' => $settings?->logo,
        ];
    }
}
