<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class SimpleReportExport implements FromArray, ShouldAutoSize, WithStyles, WithTitle
{
    protected $inquiries;
    protected $dateRange;
    protected $reportType;

    public function __construct($inquiries, $dateRange, $reportType = 'Custom')
    {
        $this->inquiries = $inquiries ?? [];
        $this->dateRange = $dateRange;
        $this->reportType = $reportType;
    }

    public function array(): array
    {
        $rows = [];

        // Header
        $rows[] = ['Republic of the Philippines'];
        $rows[] = ['DEPARTMENT OF ENVIRONMENT AND NATURAL RESOURCES'];
        $rows[] = ['Regional Office No. 4A (CALABARZON)'];
        $rows[] = ['Queueing & Inquiry Management System Report'];
        $rows[] = [''];
        $rows[] = ["Report Type: {$this->reportType}"];
        
        if ($this->dateRange && isset($this->dateRange['start']) && isset($this->dateRange['end'])) {
            $startDate = $this->dateRange['start'] instanceof \Carbon\Carbon 
                ? $this->dateRange['start']->format('F d, Y') 
                : $this->dateRange['start'];
            $endDate = $this->dateRange['end'] instanceof \Carbon\Carbon 
                ? $this->dateRange['end']->format('F d, Y') 
                : $this->dateRange['end'];
            $rows[] = ["Period: $startDate to $endDate"];
        }
        
        $rows[] = ['Date Generated: ' . \Carbon\Carbon::now()->format('F d, Y h:i A')];
        $rows[] = [''];

        // Summary Statistics
        $totalInquiries = count($this->inquiries);
        $completedCount = collect($this->inquiries)->where('status', 'completed')->count();
        $waitingCount = collect($this->inquiries)->where('status', 'waiting')->count();
        $skippedCount = collect($this->inquiries)->where('status', 'skipped')->count();

        $rows[] = ['SUMMARY STATISTICS'];
        $rows[] = ['Total Inquiries', 'Completed', 'Waiting', 'Skipped'];
        $rows[] = [$totalInquiries, $completedCount, $waitingCount, $skippedCount];
        $rows[] = [''];

        // Inquiry Details
        $rows[] = ['INQUIRY DETAILS'];
        $rows[] = ['#', 'Queue Number', 'Name', 'Contact', 'Category', 'Request Type', 'Priority', 'Status', 'Date'];

        $counter = 1;
        foreach ($this->inquiries as $inquiry) {
            try {
                $rows[] = [
                    $counter++,
                    $inquiry->queue_number ?? 'N/A',
                    $inquiry->name ?? 'Unknown',
                    $inquiry->address ?? ($inquiry->contact_numbers ?? 'N/A'),
                    $inquiry->category->name ?? 'N/A',
                    ucfirst($inquiry->request_type ?? 'walk-in'),
                    ucfirst($inquiry->priority ?? 'normal'),
                    ucfirst($inquiry->status ?? 'pending'),
                    $inquiry->date ? \Carbon\Carbon::parse($inquiry->date)->format('M d, Y') : 'N/A'
                ];
            } catch (\Exception $e) {
                // Skip problematic records
                continue;
            }
        }

        if ($totalInquiries === 0) {
            $rows[] = [''];
            $rows[] = ['No inquiries found for the selected period.'];
        }

        return $rows;
    }

    public function styles(Worksheet $sheet)
    {
        // Style header rows
        $sheet->getStyle('A1:D1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A2:D2')->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('A3:D3')->getFont()->setSize(12);
        $sheet->getStyle('A4:D4')->getFont()->setBold(true)->setSize(14);
        
        // Style section headers
        $highestRow = $sheet->getHighestRow();
        for ($row = 1; $row <= $highestRow; $row++) {
            $cellValue = $sheet->getCell("A$row")->getValue();
            if (in_array($cellValue, ['SUMMARY STATISTICS', 'INQUIRY DETAILS'])) {
                $sheet->getStyle("A$row:D$row")
                    ->getFont()
                    ->setBold(true)
                    ->setSize(12);
                $sheet->getStyle("A$row:D$row")
                    ->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('FFE0E0E0');
            }
        }

        // Style table headers
        $headerRow = $sheet->toArray();
        foreach ($headerRow as $index => $row) {
            if (isset($row[0]) && $row[0] === '#') {
                $rowNum = $index + 1;
                $sheet->getStyle("A{$rowNum}:I{$rowNum}")
                    ->getFont()
                    ->setBold(true);
                $sheet->getStyle("A{$rowNum}:I{$rowNum}")
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                break;
            }
        }

        return [];
    }

    public function title(): string
    {
        return 'Report';
    }
}
