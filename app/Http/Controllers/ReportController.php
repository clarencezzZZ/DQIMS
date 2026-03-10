<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\Category;
use App\Models\Inquiry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use PDF;

class ReportController extends Controller
{
    /**
     * Get full section name from acronym
     */
    private function getSectionFullName($section)
    {
        $sectionNames = [
            'ACS' => 'AGGREGATE AND CORRECTION',
            'OOSS' => 'ORIGINAL AND OTHER SURVEYS',
            'LES' => 'LAND EVALUATION',
            'SCS' => 'SURVEYS AND CONTROL',
        ];
        return $sectionNames[$section] ?? $section;
    }
    
    /**
     * Display reports dashboard
     */
    public function index()
    {
        $categories = Category::where('is_active', true)->get();
        return view('reports.index', compact('categories'));
    }

    /**
     * Generate report data
     */
    public function generate(Request $request)
    {
        // Handle both GET and POST requests- only validate on POST
        if ($request->isMethod('post')) {
            $request->validate([
                'report_type' => 'required|in:daily,weekly,monthly,yearly,custom',
                'date_from' => 'nullable|date',
                'date_to' => 'nullable|date',
                'category' => 'nullable|exists:categories,id',
                'status' => 'nullable|in:all,waiting,serving,completed,skipped,forwarded',
                'section' => 'nullable|string',
            ]);
        }
        
        // Set default report type if not provided
        if (!$request->filled('report_type')) {
            $request->merge(['report_type' => 'daily']);
        }

        $dateRange = $this->getDateRange($request);
        $data = $this->getReportData($dateRange, $request);

        // Return the reports view with the data to display the generated report
     return view('reports.index', array_merge($data, ['categories' => Category::where('is_active', true)->get()]));
    }

    /**
     * Get date range based on report type
     */
    private function getDateRange(Request $request)
    {
        $type = $request->report_type;

        switch ($type) {
            case 'daily':
                return [
                    'start' => now()->startOfDay(),
                    'end' => now()->endOfDay(),
                ];
            case 'weekly':
                return [
                    'start' => now()->startOfWeek(),
                    'end' => now()->endOfWeek(),
                ];
            case 'monthly':
                return [
                    'start' => now()->startOfMonth(),
                    'end' => now()->endOfMonth(),
                ];
            case 'yearly':
                return [
                    'start' => now()->startOfYear(),
                    'end' => now()->endOfYear(),
                ];
            case 'custom':
                return [
                    'start' => $request->date_from ? \Carbon\Carbon::parse($request->date_from)->startOfDay() : now()->startOfDay(),
                    'end' => $request->date_to ? \Carbon\Carbon::parse($request->date_to)->endOfDay() : now()->endOfDay(),
                ];
            default:
                return [
                    'start' => now()->startOfDay(),
                    'end' => now()->endOfDay(),
                ];
        }
    }

    /**
     * Get report data
     */
    private function getReportData($dateRange, Request $request)
    {
        $query = Inquiry::whereBetween('date', [$dateRange['start']->toDateString(), $dateRange['end']->toDateString()]);

        if ($request->filled('category') && $request->category !== 'all') {
            $query->byCategory($request->category);
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Filter by section if provided
        if ($request->filled('section') && $request->section !== '') {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('section', $request->section);
            });
        }

        $inquiries = $query->with(['category', 'servedBy'])->get();

        // Statistics by category
        $categoryStats = [];
        $categories = Category::where('is_active', true)->get();

        foreach ($categories as $category) {
            // Only include categories that match the section filter (if applied)
            if ($request->filled('section') && $request->section !== '' && $category->section !== $request->section) {
                continue;
            }
            
            $catInquiries = $inquiries->where('category_id', $category->id);
            
            $categoryStats[$category->code] = [
                'name' => $category->name,
                'section' => $this->getSectionFullName($category->section),
                'section_acronym' => $category->section,
                'total' => $catInquiries->count(),
                'waiting' => $catInquiries->where('status', 'waiting')->count(),
                'serving' => $catInquiries->where('status', 'serving')->count(),
                'completed' => $catInquiries->where('status', 'completed')->count(),
                'skipped' => $catInquiries->where('status', 'skipped')->count(),
                'forwarded' => $catInquiries->where('status', 'forwarded')->count(),
            ];
        }

        // Overall statistics
        $totalStats = [
            'total' => $inquiries->count(),
            'waiting' => $inquiries->where('status', 'waiting')->count(),
            'serving' => $inquiries->where('status', 'serving')->count(),
            'completed' => $inquiries->where('status', 'completed')->count(),
            'skipped' => $inquiries->where('status', 'skipped')->count(),
            'forwarded' => $inquiries->where('status', 'forwarded')->count(),
        ];

        // Average processing time
        $completedInquiries = $inquiries->where('status', 'completed');
        $avgProcessingTime = $completedInquiries->avg(function ($inquiry) {
            return $inquiry->processing_time;
        });

        // Most common request types
        $requestTypes = $inquiries->groupBy('request_type')
            ->map(function ($items) {
                return $items->count();
            })
            ->sortDesc()
            ->take(5);

        // Assessments data
        $assessments = Assessment::whereBetween('assessment_date', [
            $dateRange['start']->toDateString(),
            $dateRange['end']->toDateString()
        ])->with(['category', 'processedBy'])->get();

        // Filter assessments by section if provided
        if ($request->filled('section') && $request->section !== '') {
            $assessments = $assessments->filter(function ($assessment) use ($request) {
                return $assessment->category && $assessment->category->section === $request->section;
            });
        }

        $totalFees = $assessments->sum('fees');

        return [
            'date_range' => [
                'start' => $dateRange['start']->toDateString(),
                'end' => $dateRange['end']->toDateString(),
            ],
            'overall_stats' => $totalStats,
            'category_stats' => $categoryStats,
            'average_processing_time' => round($avgProcessingTime, 2),
            'top_request_types' => $requestTypes,
            'total_fees' => $totalFees,
            'assessments_count' => $assessments->count(),
            'inquiries' => $inquiries,
            'assessments' => $assessments,
        ];
    }

    /**
     * Export report to PDF
     */
    public function exportPdf(Request $request)
    {
        $request->validate([
            'report_type' => 'sometimes|in:daily,weekly,monthly,yearly,custom',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date',
            'category' => 'nullable|exists:categories,id',
            'status' => 'nullable|in:all,waiting,serving,completed,skipped,forwarded',
            'section' => 'nullable|string',
        ]);

        $dateRange = $this->getDateRange($request);
        $data = $this->getReportData($dateRange, $request);

        $pdf = PDF::loadView('reports.pdf', $data);
        
        $filename = 'report_' . $dateRange['start']->format('Y-m-d') . '_' . $dateRange['end']->format('Y-m-d') . '.pdf';
        
        return $pdf->download($filename);
    }

    /**
     * Export report to Excel
     */
    public function exportExcel(Request $request)
    {
        $request->validate([
            'report_type' => 'sometimes|in:daily,weekly,monthly,yearly,custom',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date',
            'category' => 'nullable|exists:categories,id',
            'status' => 'nullable|in:all,waiting,serving,completed,skipped,forwarded',
            'section' => 'nullable|string',
        ]);

        $dateRange = $this->getDateRange($request);
        $data = $this->getReportData($dateRange, $request);

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="report_' . $dateRange['start']->format('Y-m-d') . '.csv"',
        ];

        $callback = function () use ($data) {
            $file = fopen('php://output', 'w');
            
            // Headers
            fputcsv($file, ['DENR Queueing & Inquiry Management System - Report']);
            fputcsv($file, ['Date Range:', $data['date_range']['start'] . ' to ' . $data['date_range']['end']]);
            fputcsv($file, []);
            
            // Overall Statistics
            fputcsv($file, ['Overall Statistics']);
            fputcsv($file, ['Total Inquiries', $data['overall_stats']['total']]);
            fputcsv($file, ['Waiting', $data['overall_stats']['waiting']]);
            fputcsv($file, ['Serving', $data['overall_stats']['serving']]);
            fputcsv($file, ['Completed', $data['overall_stats']['completed']]);
            fputcsv($file, ['Skipped', $data['overall_stats']['skipped']]);
            fputcsv($file, ['Forwarded', $data['overall_stats']['forwarded']]);
            fputcsv($file, ['Average Processing Time (minutes)', $data['average_processing_time']]);
            fputcsv($file, ['Total Fees', $data['total_fees']]);
            fputcsv($file, []);
            
            // Status Distribution Chart (Text-based Bar Chart)
            fputcsv($file, ['Status Distribution']);
            $maxVal = max($data['overall_stats']);
            foreach ($data['overall_stats'] as $status => $count) {
                $barLength = $maxVal > 0 ? intval(($count / $maxVal) * 20) : 0;
                $bar = str_repeat('█', $barLength);
                fputcsv($file, [$status, $bar, $count]);
            }
            fputcsv($file, []);

            // Category Statistics
            fputcsv($file, ['Category Statistics']);
            fputcsv($file, ['Category', 'Total', 'Waiting', 'Serving', 'Completed', 'Skipped', 'Forwarded']);
            foreach ($data['category_stats'] as $code => $stats) {
                fputcsv($file, [
                    $stats['name'],
                    $stats['total'],
                    $stats['waiting'],
                    $stats['serving'],
                    $stats['completed'],
                    $stats['skipped'],
                    $stats['forwarded'],
                ]);
            }
            fputcsv($file, []);

            // Inquiry Details
            fputcsv($file, ['Inquiry Details']);
            fputcsv($file, ['Queue Number', 'Name', 'Category', 'Request Type', 'Status', 'Date', 'Processing Time']);
            foreach ($data['inquiries'] as $inquiry) {
                fputcsv($file, [
                    $inquiry->queue_number,
                    $inquiry->name,
                    $inquiry->category->name,
                    $inquiry->request_type,
                    $inquiry->status,
                    $inquiry->date,
                    $inquiry->processing_time ?? 'N/A',
                ]);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    /**
     * Print report view
     */
    public function print(Request $request)
    {
        $request->validate([
            'report_type' => 'sometimes|in:daily,weekly,monthly,yearly,custom',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date',
            'category' => 'nullable|exists:categories,id',
            'status' => 'nullable|in:all,waiting,serving,completed,skipped,forwarded',
            'section' => 'nullable|string',
        ]);

        $dateRange = $this->getDateRange($request);
        $data = $this->getReportData($dateRange, $request);

        return view('reports.print', $data);
    }
}
