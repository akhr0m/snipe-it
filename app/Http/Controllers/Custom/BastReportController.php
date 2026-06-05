<?php

namespace App\Http\Controllers\Custom;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Custom\BastReport;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class BastReportController extends Controller
{
    /**
     * Display a listing of historical BAST reports with search functionality.
     */
    public function search(Request $request)
    {
        $query = BastReport::query();

        if ($search = $request->input('search')) {
            $query->where(function($q) use ($search) {
                $q->where('bast_number', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%")
                  ->orWhere('user_email', 'like', "%{$search}%")
                  ->orWhere('user_nik', 'like', "%{$search}%");
            });
        }

        $reports = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('custom.bast.search', compact('reports'));
    }

    /**
     * Preview BAST for a user before printing.
     */
    public function preview($userId)
    {
        $user = User::findOrFail($userId);
        $admin = auth()->user();

        // Get assets currently assigned to user
        $assets = $user->assets()->with('model.category')->get();

        if ($assets->isEmpty()) {
            return redirect()->back()->with('error', 'User tidak memiliki aset yang sedang dideploy.');
        }

        // Generate default checkout note as "Keterangan"
        foreach ($assets as $asset) {
            $log = \App\Models\Actionlog::where('target_id', $user->id)
                ->where('target_type', User::class)
                ->where('item_id', $asset->id)
                ->where('item_type', \App\Models\Asset::class)
                ->where('action_type', 'checkout')
                ->orderBy('id', 'desc')
                ->first();
            $asset->keterangan = $log ? $log->note : '';
        }

        $bastNumber = $this->generateBastNumber();
        $dateFormatted = Carbon::now()->format('l, j F Y'); // e.g. Friday, 5 June 2026

        return view('custom.bast.print', compact('user', 'admin', 'assets', 'bastNumber', 'dateFormatted'));
    }

    /**
     * AJAX endpoint to save BAST snapshot.
     */
    public function save(Request $request)
    {
        $request->validate([
            'bast_number' => 'required|string',
            'user_id' => 'required|integer',
            'assets' => 'required|array',
            'user_nik' => 'nullable|string',
            'admin_nik' => 'nullable|string',
            'notes' => 'nullable|string',
            'return_notes' => 'nullable|string',
        ]);

        // Check if BAST number already exists to avoid duplication
        $existing = BastReport::where('bast_number', $request->bast_number)->first();
        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'Nomor BAST sudah digunakan.'
            ], 422);
        }

        $user = User::findOrFail($request->user_id);
        $admin = auth()->user();

        $report = BastReport::create([
            'bast_number' => $request->bast_number,
            'user_id' => $user->id,
            'username' => $user->present()->fullName(),
            'user_email' => $user->email,
            'user_nik' => $request->input('user_nik'),
            'user_jobtitle' => $user->jobtitle,
            'user_department' => $user->department ? $user->department->name : '',
            'user_location' => $user->location ? $user->location->name : '',
            'admin_name' => $admin->present()->fullName(),
            'admin_nik' => $request->input('admin_nik'),
            'admin_title' => $admin->jobtitle ?: 'IT OFFICER',
            'date_printed' => Carbon::now(),
            'perihal' => $request->input('perihal', 'Penyerahan Aset (Inventaris Kantor)'),
            'notes' => $request->input('notes'),
            'return_notes' => $request->input('return_notes'),
            'assets_data' => $request->assets,
        ]);

        return response()->json([
            'success' => true,
            'id' => $report->id,
            'message' => 'BAST berhasil disimpan.'
        ]);
    }

    /**
     * Show a saved BAST report from snapshot (Reprint mode).
     */
    public function reprint($id)
    {
        $report = BastReport::findOrFail($id);
        $dateFormatted = Carbon::parse($report->date_printed)->format('l, j F Y');

        return view('custom.bast.print', compact('report', 'dateFormatted'));
    }

    /**
     * Helper to generate unique auto-increment BAST number.
     */
    private function generateBastNumber()
    {
        $year = date('Y');
        $monthRoman = $this->getRomanMonth((int)date('n'));

        // Find the last BAST report for this year
        $lastReport = BastReport::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();

        $nextSeq = 1;
        if ($lastReport) {
            $parts = explode('/', $lastReport->bast_number);
            if (count($parts) > 0) {
                $lastSeq = (int)$parts[0];
                $nextSeq = $lastSeq + 1;
            }
        }

        $seqStr = str_pad($nextSeq, 5, '0', STR_PAD_LEFT);
        
        return "{$seqStr}/BAST/IT/HO/{$monthRoman}/{$year}";
    }

    /**
     * Helper to get Roman numerals for months.
     */
    private function getRomanMonth($month)
    {
        $romans = [
            1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI',
            7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'
        ];
        return $romans[$month] ?? '';
    }
}
