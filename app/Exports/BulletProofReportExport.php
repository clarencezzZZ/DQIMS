<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Illuminate\Support\Collection;

class BulletProofReportExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithTitle, WithEvents
{
    protected $inquiries;
    protected $dateRange;
    protected $reportType;

    public function __construct($inquiries, $dateRange, $reportType = 'Report')
    {
        $this->inquiries = $inquiries instanceof Collection ? $inquiries : collect($inquiries);
        $this->dateRange = $dateRange;
        $this->reportType = $reportType;
    }

    public function collection()
    {
        return $this->inquiries;
    }

    public function headings(): array
    {
        return [
            '#',
            'Queue Number',
            'Name',
            'Contact/Address',
            'Category',
            'Request Type',
            'Priority',
            'Status',
            'Date'
        ];
    }

    public function map($inquiry): array
    {
        static $counter = 0;
        $counter++;

        return [
            $counter,
            $inquiry->queue_number ?? 'N/A',
            $inquiry->name ?? 'Unknown',
            $inquiry->address ?? ($inquiry->contact_numbers ?? 'N/A'),
            $inquiry->category && isset($inquiry->category->name) ? $inquiry->category->name : 'N/A',
            ucfirst($inquiry->request_type ?? 'walk-in'),
            ucfirst($inquiry->priority ?? 'normal'),
            ucfirst($inquiry->status ?? 'pending'),
            $inquiry->date ? \Carbon\Carbon::parse($inquiry->date)->format('M d, Y') : 'N/A'
        ];
    }

    public function title(): string
    {
        return 'Report';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                // Set header style
                $event->sheet->getStyle('A1:I1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 12
                    ],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => [
                            'argb' => 'FFCCCCCC'
                        ]
                    ]
                ]);

                // Auto-size columns
                foreach (range('A', 'I') as $col) {
                    $event->sheet->getColumnDimension($col)->setAutoSize(true);
                }
            },
        ];
    }
}
