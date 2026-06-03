<?php

namespace BastModule\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use BastModule\Services\BastReportService;
use Illuminate\Http\Request;

class BastReportController extends Controller
{
    protected BastReportService $service;

    public function __construct(BastReportService $service)
    {
        $this->service = $service;
    }

    public function getBastReport(User $user)
    {
        $data = $this->service->generateBastPreview($user);
        // Render module view namespace
        return view('bast::reports.bast', array_merge($data, ['isPreview' => true]));
    }

    public function storeAndPrintBastReport(User $user)
    {
        $report = $this->service->createAndStoreBastReport($user);
        $data = $this->service->formatReportForRender($report, false);
        // Normalize key expected by view
        if (isset($data['reportRecord'])) {
            $data['newReportRecord'] = $data['reportRecord'];
        }
        // showSavedMessage and autoPrint based on config
        $data['showSavedMessage'] = config('bast.report_settings.show_save_message', true);
        $data['autoPrint'] = config('bast.report_settings.auto_print', false);

        return view('bast::reports.bast', array_merge($data, ['isPreview' => false]));
    }

    public function findBastReport(Request $request)
    {
        $number = $request->input('number');
        if (!$number) {
            return redirect()->back()->with('warning', 'Silakan masukkan Nomor BAST yang ingin dicari.');
        }

        $report = $this->service->findByReportNumber($number);
        if (!$report) {
            return redirect()->back()->with('error', 'Laporan BAST tidak ditemukan. Mohon periksa kembali nomor yang Anda masukkan.');
        }

        $data = $this->service->formatReportForRender($report, true);
        if (isset($data['reportRecord'])) {
            $data['newReportRecord'] = $data['reportRecord'];
        }
        return view('bast::reports.bast', array_merge($data, ['isPreview' => false]));
    }

    public function showBastSearchPage()
    {
        $bastReports = $this->service->getAllReports();
        return view('bast::reports.find-bast', compact('bastReports'));
    }

    public function checkBastExists(Request $request)
    {
        $number = $request->input('number');
        $found = $this->service->checkReportNumberExists($number);
        return response()->json(['found' => (bool)$found]);
    }

    public function viewBastReportById($id)
    {
        $report = $this->service->getReportById($id);
        if (!$report) {
            return redirect()->back()->with('error', 'Laporan BAST tidak ditemukan.');
        }
        $data = $this->service->formatReportForRender($report, false);
        if (isset($data['reportRecord'])) {
            $data['newReportRecord'] = $data['reportRecord'];
        }
        return view('bast::reports.bast', array_merge($data, ['isPreview' => false]));
    }

    public function getBastDataApi()
    {
        return response()->json($this->service->formatReportsForApi());
    }
}
