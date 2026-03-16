<?php

namespace App\Exports;

use Illuminate\Support\Collection;

class MinimalExcelExport
{
    protected $data;
    protected $filename;

    public function __construct($inquiries, $dateRange, $reportType = 'Report')
    {
        $this->data = [
            'inquiries' => $inquiries instanceof Collection ? $inquiries : collect($inquiries),
            'dateRange' => $dateRange,
            'reportType' => $reportType
        ];
    }

    public function download($filename)
    {
        $this->filename = $filename;
        
        // Generate CSV content (simpler than XLSX, works everywhere)
        $csvContent = $this->generateCSV();
        
        return response($csvContent, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . str_replace('.xlsx', '.csv', $filename) . '"',
        ]);
    }

    protected function generateCSV()
    {
        $output = fopen('php://temp', 'r+');
        
        // Add report header
        fputcsv($output, ['Republic of the Philippines']);
        fputcsv($output, ['DEPARTMENT OF ENVIRONMENT AND NATURAL RESOURCES']);
        fputcsv($output, ['Regional Office No. 4A (CALABARZON)']);
        fputcsv($output, ['Queueing & Inquiry Management System Report']);
        fputcsv($output, ['']);
        fputcsv($output, ["Report Type: {$this->data['reportType']}"]);
        
        if ($this->data['dateRange'] && isset($this->data['dateRange']['start']) && isset($this->data['dateRange']['end'])) {
            $start = $this->data['dateRange']['start'];
            $end = $this->data['dateRange']['end'];
            
            if ($start instanceof \Carbon\Carbon) {
                $start = $start->format('F d, Y');
            }
            if ($end instanceof \Carbon\Carbon) {
                $end = $end->format('F d, Y');
            }
            
            fputcsv($output, ["Period: $start to $end"]);
        }
        
        fputcsv($output, ['Date Generated: ' . \Carbon\Carbon::now()->format('F d, Y h:i A')]);
        fputcsv($output, ['']);
        
        // Summary statistics
        $total = $this->data['inquiries']->count();
        $completed = $this->data['inquiries']->where('status', 'completed')->count();
        $waiting = $this->data['inquiries']->where('status', 'waiting')->count();
        $skipped = $this->data['inquiries']->where('status', 'skipped')->count();
        
        fputcsv($output, ['SUMMARY STATISTICS']);
        fputcsv($output, ['Total Inquiries', 'Completed', 'Waiting', 'Skipped']);
        fputcsv($output, [$total, $completed, $waiting, $skipped]);
        fputcsv($output, ['']);
        
        // Inquiry details
        fputcsv($output, ['INQUIRY DETAILS']);
        fputcsv($output, ['#', 'Queue Number', 'Name', 'Contact/Address', 'Category', 'Request Type', 'Priority', 'Status', 'Date']);
        
        $counter = 1;
        foreach ($this->data['inquiries'] as $inquiry) {
            try {
                fputcsv($output, [
                    $counter++,
                    $inquiry->queue_number ?? 'N/A',
                    $inquiry->name ?? 'Unknown',
                    $inquiry->address ?? ($inquiry->contact_numbers ?? 'N/A'),
                    ($inquiry->category && isset($inquiry->category->name)) ? $inquiry->category->name : 'N/A',
                    ucfirst($inquiry->request_type ?? 'walk-in'),
                    ucfirst($inquiry->priority ?? 'normal'),
                    ucfirst($inquiry->status ?? 'pending'),
                    $inquiry->date ? \Carbon\Carbon::parse($inquiry->date)->format('M d, Y') : 'N/A'
                ]);
            } catch (\Exception $e) {
                // Skip problematic records silently
                continue;
            }
        }
        
        if ($total === 0) {
            fputcsv($output, ['']);
            fputcsv($output, ['No inquiries found for the selected period.']);
        }
        
        $content = stream_get_contents($output);
        fclose($output);
        
        return $content;
    }
}
