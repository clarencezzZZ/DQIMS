<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ReportExport implements FromArray, ShouldAutoSize, WithStyles, WithTitle
{
    protected $data;
    protected $charts;

    public function __construct(array $data, array $charts)
    {
        $this->data = $data;
        $this->charts = $charts;
    }

    public function array(): array
    {
        $rows = [
            ['Republic of the Philippines'],
            ['DEPARTMENT OF ENVIRONMENT AND NATURAL RESOURCES'],
            ['Regional Office No. 4A (CALABARZON)'],
            ['Queueing & Inquiry Management System Report'],
            [''],
            ['Report Period:', optional($this->data['date_range']['start'] ?? null)->format('F d, Y') . ' to ' . optional($this->data['date_range']['end'] ?? null)->format('F d, Y')],
            ['Date Generated:', \Carbon\Carbon::now()->format('F d, Y h:i A')],
            [''],
            ['OVERALL STATISTICS'],
            ['TOTAL INQUIRIES', 'COMPLETED', 'TOTAL ASSESSMENTS', 'TOTAL REVENUE'],
            [
                $this->data['overall_stats']['total'] ?? 0,
                $this->data['overall_stats']['completed'] ?? 0,
                $this->data['assessments_count'] ?? 0,
                number_format($this->data['total_fees'] ?? 0, 2)
            ],
            [''],
            ['CATEGORY STATISTICS'],
            ['#', 'Category', 'Section', 'Total', 'Status Breakdown'],
        ];

        $counter = 1;
        foreach (($this->data['category_stats'] ?? []) as $code => $stats) {
            if (($stats['total'] ?? 0) > 0) {
                $breakdown = ($stats['completed'] ?? 0) . ' Completed';
                if (($stats['waiting'] ?? 0) > 0) $breakdown .= ', ' . $stats['waiting'] . ' Waiting';
                if (($stats['skipped'] ?? 0) > 0) $breakdown .= ', ' . $stats['skipped'] . ' Skipped';

                $rows[] = [
                    $counter++,
                    $stats['name'] ?? '',
                    $stats['section'] ?? '',
                    $stats['total'] ?? 0,
                    $breakdown
                ];
            }
        }

        $rows[] = [''];
        $rows[] = ['INQUIRY DETAILS'];
        $rows[] = ['#', 'Queue Number', 'Name', 'Category', 'Request Type', 'Status', 'Date'];

        foreach (($this->data['inquiries'] ?? []) as $index => $inquiry) {
            $rows[] = [
                $index + 1,
                $inquiry->short_queue_number ?? '',
                $inquiry->name ?? '',
                $inquiry->category->name ?? 'N/A',
                $inquiry->request_type ?? '',
                ucfirst($inquiry->status ?? ''),
                $inquiry->date ? \Carbon\Carbon::parse($inquiry->date)->format('M d, Y') : 'N/A'
            ];
        }

        $rows[] = [''];
        $rows[] = ['REVENUE STATISTICS'];
        $rows[] = ['Date', 'Revenue'];
        foreach (($this->charts['revenueChart'] ?? []) as $date => $revenue) {
            $rows[] = [$date, number_format($revenue ?? 0, 2)];
        }

        $rows[] = [''];
        $rows[] = ['REPORT STATISTICS OVERVIEW'];
        $rows[] = ['Metric', 'Value'];
        foreach (($this->charts['dailyChart'] ?? []) as $metric => $value) {
            $rows[] = [$metric, is_numeric($value) ? number_format($value, 2) : $value];
        }

        $rows[] = [''];
        $rows[] = ['SECTION STATISTICS'];
        $rows[] = ['Section', 'Inquiries'];
        foreach (($this->charts['sectionChart'] ?? []) as $section => $inquiries) {
            $rows[] = [$section, $inquiries ?? 0];
        }

        return $rows;
    }

    public function styles(Worksheet $sheet)
    {
        // Styling headers and sections
        return [
            1 => ['font' => ['bold' => true, 'size' => 16]],
            2 => ['font' => ['size' => 12]],
            3 => ['font' => ['size' => 12]],
            4 => ['font' => ['bold' => true, 'size' => 14]],
        ];
    }

    public function title(): string
    {
        return 'Report ' . optional($this->data['date_range']['start'] ?? null)->format('Y-m-d') . ' to ' . optional($this->data['date_range']['end'] ?? null)->format('Y-m-d');
    }
}
