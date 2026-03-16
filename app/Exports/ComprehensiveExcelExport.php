<?php

namespace App\Exports;

use Illuminate\Support\Collection;

class ComprehensiveExcelExport
{
    protected $data;
    
    public function __construct($data)
    {
        $this->data = $data;
    }
    
    public function download($filename)
    {
        // Create Excel XML format (SpreadsheetML)
        $xml = $this->generateExcelXML();
        
        return response($xml, 200, [
            'Content-Type' => 'application/vnd.ms-excel',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
    
    protected function generateExcelXML()
    {
        $d = $this->data;
        
        // Start Excel XML
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<?mso-application progid="Excel.Sheet"?>' . "\n";
        $xml .= '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"' . "\n";
        $xml .= ' xmlns:o="urn:schemas-microsoft-com:office:office"' . "\n";
        $xml .= ' xmlns:x="urn:schemas-microsoft-com:office:excel"' . "\n";
        $xml .= ' xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"' . "\n";
        $xml .= ' xmlns:html="http://www.w3.org/TR/REC-html40">' . "\n";
        
        // Styles
        $xml .= $this->getStyles();
        
        // Worksheet
        $xml .= '<Worksheet ss:Name="Report">' . "\n";
        $xml .= '<Table>' . "\n";
        
        // === HEADER SECTION ===
        $xml .= $this->addRow(['Republic of the Philippines'], 'header');
        $xml .= $this->addRow(['DEPARTMENT OF ENVIRONMENT AND NATURAL RESOURCES'], 'subheader');
        $xml .= $this->addRow(['Regional Office No. 4A (CALABARZON)'], 'subheader');
        $xml .= $this->addRow(['Queueing & Inquiry Management System Report'], 'title');
        $xml .= $this->addRow(['']); // Empty row
        
        // Report Period
        $startDate = \Carbon\Carbon::parse($d['date_range']['start'])->format('F d, Y');
        $endDate = \Carbon\Carbon::parse($d['date_range']['end'])->format('F d, Y');
        $xml .= $this->addRow(["Report Period: $startDate to $endDate"], 'bold');
        
        $generatedDate = \Carbon\Carbon::now()->format('F d, Y h:i A');
        $xml .= $this->addRow(["Date Generated: $generatedDate"], 'bold');
        $xml .= $this->addRow(['']); // Empty row
        
        // === OVERALL STATISTICS - HORIZONTAL LAYOUT ===
        $xml .= $this->addRow(['OVERALL STATISTICS'], 'section');
        $xml .= $this->addRow(['TOTAL INQUIRIES', 'COMPLETED', 'TOTAL ASSESSMENTS', 'TOTAL REVENUE'], 'tableHeader');
        $xml .= $this->addRow([
            $d['overall_stats']['total'] ?? 0,
            $d['overall_stats']['completed'] ?? 0,
            $d['assessments_count'] ?? 0,
            '₱' . number_format($d['total_fees'] ?? 0, 2)
        ], 'data');
        $xml .= $this->addRow(['']); // Empty row
        
        // === CATEGORY STATISTICS - WIDE HORIZONTAL TABLE ===
        $xml .= $this->addRow(['CATEGORY STATISTICS'], 'section');
        $xml .= $this->addRow(['#', 'Category Code', 'Category Name', 'Section', 'Total', 'Completed', 'Waiting', 'Skipped'], 'tableHeader');
        
        $counter = 1;
        foreach (($d['category_stats'] ?? []) as $code => $stats) {
            if (($stats['total'] ?? 0) > 0) {
                $xml .= $this->addRow([
                    $counter++,
                    $code ?? '',
                    $stats['name'] ?? '',
                    $stats['section'] ?? '',
                    $stats['total'] ?? 0,
                    $stats['completed'] ?? 0,
                    $stats['waiting'] ?? 0,
                    $stats['skipped'] ?? 0
                ], 'data');
            }
        }
        $xml .= $this->addRow(['']); // Empty row
        
        // === REVENUE STATISTICS - SIDE BY SIDE ===
        $xml .= $this->addRow(['REVENUE STATISTICS'], 'section');
        $xml .= $this->addRow(['Date', 'Revenue Amount'], 'tableHeader');
        foreach (($d['revenue_by_date'] ?? []) as $date => $revenue) {
            $xml .= $this->addRow([
                \Carbon\Carbon::parse($date)->format('M d, Y'),
                '₱' . number_format($revenue, 2)
            ], 'data');
        }
        $xml .= $this->addRow(['']); // Empty row
        
        // === REPORT STATISTICS OVERVIEW - TWO COLUMNS ===
        $xml .= $this->addRow(['REPORT STATISTICS OVERVIEW'], 'section');
        $xml .= $this->addRow(['Metric', 'Value'], 'tableHeader');
        $xml .= $this->addRow(['Total Inquiries', $d['overall_stats']['total'] ?? 0], 'data');
        $xml .= $this->addRow(['Completed', $d['overall_stats']['completed'] ?? 0], 'data');
        $xml .= $this->addRow(['Total Assessments', $d['assessments_count'] ?? 0], 'data');
        $xml .= $this->addRow(['Total Revenue', '₱' . number_format($d['total_fees'] ?? 0, 2)], 'data');
        $xml .= $this->addRow(['']); // Empty row
        
        // === SECTION STATISTICS ===
        $xml .= $this->addRow(['SECTION STATISTICS'], 'section');
        $xml .= $this->addRow(['Section Name', 'Total Inquiries'], 'tableHeader');
        foreach (($d['section_stats'] ?? []) as $section => $count) {
            $xml .= $this->addRow([$section, $count], 'data');
        }
        $xml .= $this->addRow(['']); // Empty row
        
        // === INQUIRY DETAILS - FULL HORIZONTAL TABLE ===
        $xml .= $this->addRow(['INQUIRY DETAILS'], 'section');
        $xml .= $this->addRow(['#', 'Queue Number', 'Client Name', 'Category', 'Request Type', 'Priority', 'Status', 'Date Served'], 'tableHeader');
        
        $counter = 1;
        foreach (($d['inquiries'] ?? []) as $inquiry) {
            $xml .= $this->addRow([
                $counter++,
                $inquiry->queue_number ?? 'N/A',
                $inquiry->name ?? 'Unknown',
                $inquiry->category && isset($inquiry->category->name) ? $inquiry->category->name : 'N/A',
                ucfirst($inquiry->request_type ?? 'walk-in'),
                ucfirst($inquiry->priority ?? 'normal'),
                ucfirst($inquiry->status ?? 'pending'),
                $inquiry->date ? \Carbon\Carbon::parse($inquiry->date)->format('M d, Y') : 'N/A'
            ], 'data');
        }
        
        $xml .= '</Table>' . "\n";
        $xml .= '</Worksheet>' . "\n";
        $xml .= '</Workbook>';
        
        return $xml;
    }
    
    protected function addRow($cells, $style = 'default')
    {
        $row = '        <Row>' . "\n";
        foreach ($cells as $cell) {
            $row .= '            <Cell>' . "\n";
            $row .= '                <Data ss:Type="String">' . htmlspecialchars($cell ?? '', ENT_XML1 | ENT_QUOTES) . '</Data>' . "\n";
            $row .= '            </Cell>' . "\n";
        }
        $row .= '        </Row>' . "\n";
        return $row;
    }
    
    protected function getStyles()
    {
        return '    <Styles>' . "\n" .
               '        <Style ss:ID="default">' . "\n" .
               '            <Alignment ss:Vertical="Center"/>' . "\n" .
               '            <Font ss:Size="11"/>' . "\n" .
               '            <Borders>' . "\n" .
               '                <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1"/>' . "\n" .
               '                <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1"/>' . "\n" .
               '                <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1"/>' . "\n" .
               '                <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1"/>' . "\n" .
               '            </Borders>' . "\n" .
               '        </Style>' . "\n" .
               '        <Style ss:ID="header">' . "\n" .
               '            <Font ss:Bold="1" ss:Size="18" ss:Color="#000000"/>' . "\n" .
               '            <Alignment ss:Horizontal="Center"/>' . "\n" .
               '            <Borders>' . "\n" .
               '                <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="2" ss:Color="#000000"/>' . "\n" .
               '            </Borders>' . "\n" .
               '        </Style>' . "\n" .
               '        <Style ss:ID="subheader">' . "\n" .
               '            <Font ss:Bold="1" ss:Size="14" ss:Color="#333333"/>' . "\n" .
               '            <Alignment ss:Horizontal="Center"/>' . "\n" .
               '        </Style>' . "\n" .
               '        <Style ss:ID="title">' . "\n" .
               '            <Font ss:Bold="1" ss:Size="16" ss:Color="#0066CC"/>' . "\n" .
               '            <Alignment ss:Horizontal="Center"/>' . "\n" .
               '            <Borders>' . "\n" .
               '                <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="2" ss:Color="#000000"/>' . "\n" .
               '            </Borders>' . "\n" .
               '        </Style>' . "\n" .
               '        <Style ss:ID="bold">' . "\n" .
               '            <Font ss:Bold="1" ss:Size="12"/>' . "\n" .
               '        </Style>' . "\n" .
               '        <Style ss:ID="section">' . "\n" .
               '            <Font ss:Bold="1" ss:Size="14" ss:Color="#FFFFFF"/>' . "\n" .
               '            <Interior ss:Color="#4472C4" ss:Pattern="Solid"/>' . "\n" .
               '            <Alignment ss:Horizontal="Left"/>' . "\n" .
               '            <Borders>' . "\n" .
               '                <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="2" ss:Color="#000000"/>' . "\n" .
               '                <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1"/>' . "\n" .
               '                <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1"/>' . "\n" .
               '                <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1"/>' . "\n" .
               '            </Borders>' . "\n" .
               '        </Style>' . "\n" .
               '        <Style ss:ID="tableHeader">' . "\n" .
               '            <Font ss:Bold="1" ss:Size="12" ss:Color="#FFFFFF"/>' . "\n" .
               '            <Interior ss:Color="#5B9BD5" ss:Pattern="Solid"/>' . "\n" .
               '            <Alignment ss:Horizontal="Center"/>' . "\n" .
               '            <Borders>' . "\n" .
               '                <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="2" ss:Color="#000000"/>' . "\n" .
               '                <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1"/>' . "\n" .
               '                <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1"/>' . "\n" .
               '                <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1"/>' . "\n" .
               '            </Borders>' . "\n" .
               '        </Style>' . "\n" .
               '        <Style ss:ID="data">' . "\n" .
               '            <Alignment ss:Vertical="Center"/>' . "\n" .
               '            <Font ss:Size="11"/>' . "\n" .
               '            <Borders>' . "\n" .
               '                <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1"/>' . "\n" .
               '                <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1"/>' . "\n" .
               '                <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1"/>' . "\n" .
               '                <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1"/>' . "\n" .
               '            </Borders>' . "\n" .
               '        </Style>' . "\n" .
               '        <Style ss:ID="number">' . "\n" .
               '            <Alignment ss:Horizontal="Right"/>' . "\n" .
               '            <Borders>' . "\n" .
               '                <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1"/>' . "\n" .
               '                <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1"/>' . "\n" .
               '                <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1"/>' . "\n" .
               '                <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1"/>' . "\n" .
               '            </Borders>' . "\n" .
               '        </Style>' . "\n" .
               '    </Styles>' . "\n";
    }
}
