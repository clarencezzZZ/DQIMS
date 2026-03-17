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
        $xml .= '<Table ss:DefaultColumnWidth="100">' . "\n";
        
        // Column Widths
        $xml .= '    <Column ss:Width="30"/>' . "\n"; // #
        $xml .= '    <Column ss:Width="120"/>' . "\n"; // Queue Number / Category Code
        $xml .= '    <Column ss:Width="180"/>' . "\n"; // Client Name / Category Name
        $xml .= '    <Column ss:Width="150"/>' . "\n"; // Section
        $xml .= '    <Column ss:Width="80"/>' . "\n";  // Total / Waiting
        $xml .= '    <Column ss:Width="80"/>' . "\n";  // Completed
        $xml .= '    <Column ss:Width="80"/>' . "\n";  // Serving
        $xml .= '    <Column ss:Width="80"/>' . "\n";  // Skipped
        $xml .= '    <Column ss:Width="100"/>' . "\n"; // Date
        
        // === HEADER SECTION ===
        $xml .= $this->addRow(['Republic of the Philippines'], 'header', 7);
        $xml .= $this->addRow(['DEPARTMENT OF ENVIRONMENT AND NATURAL RESOURCES'], 'subheader', 7);
        $xml .= $this->addRow(['Regional Office No. 4A (CALABARZON)'], 'subheader', 7);
        $xml .= $this->addRow(['Queueing & Inquiry Management System Report'], 'title', 7);
        $xml .= $this->addRow([''], 'default'); // Empty row
        
        // Report Period
        $startDate = \Carbon\Carbon::parse($d['date_range']['start'])->format('F d, Y');
        $endDate = \Carbon\Carbon::parse($d['date_range']['end'])->format('F d, Y');
        $xml .= $this->addRow(["Report Period: $startDate to $endDate"], 'bold', 7);
        
        $generatedDate = \Carbon\Carbon::now()->format('F d, Y h:i A');
        $xml .= $this->addRow(["Date Generated: $generatedDate"], 'bold', 7);
        $xml .= $this->addRow([''], 'default'); // Empty row
        
        // === OVERALL STATISTICS ===
        $xml .= $this->addRow(['OVERALL STATISTICS'], 'section', 7);
        $xml .= $this->addRow(['TOTAL INQUIRIES', 'COMPLETED', 'TOTAL ASSESSMENTS', 'TOTAL REVENUE'], 'tableHeader');
        $xml .= $this->addRow([
            $d['overall_stats']['total'] ?? 0,
            $d['overall_stats']['completed'] ?? 0,
            $d['assessments_count'] ?? 0,
            '₱' . number_format($d['total_fees'] ?? 0, 2)
        ], 'data');
        $xml .= $this->addRow([''], 'default'); // Empty row
        
        // === CATEGORY STATISTICS ===
        $xml .= $this->addRow(['CATEGORY STATISTICS'], 'section', 7);
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
        $xml .= $this->addRow([''], 'default'); // Empty row
        
        // === REVENUE STATISTICS ===
        $xml .= $this->addRow(['REVENUE STATISTICS'], 'section', 1);
        $xml .= $this->addRow(['Date', 'Revenue Amount'], 'tableHeader');
        foreach (($d['revenue_by_date'] ?? []) as $date => $revenue) {
            $xml .= $this->addRow([
                \Carbon\Carbon::parse($date)->format('M d, Y'),
                '₱' . number_format($revenue, 2)
            ], 'data');
        }
        $xml .= $this->addRow([''], 'default'); // Empty row
        
        // === SECTION STATISTICS ===
        $xml .= $this->addRow(['SECTION STATISTICS'], 'section', 1);
        $xml .= $this->addRow(['Section Name', 'Total Inquiries'], 'tableHeader');
        foreach (($d['section_stats'] ?? []) as $section => $count) {
            $xml .= $this->addRow([$section, $count], 'data');
        }
        $xml .= $this->addRow([''], 'default'); // Empty row
        
        // === INQUIRY DETAILS ===
        $xml .= $this->addRow(['INQUIRY DETAILS'], 'section', 7);
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
    
    protected function addRow($cells, $style = 'default', $mergeAcross = 0)
    {
        $row = '        <Row>' . "\n";
        foreach ($cells as $index => $cell) {
            $mergeAttr = ($index === 0 && $mergeAcross > 0) ? ' ss:MergeAcross="' . $mergeAcross . '"' : '';
            $row .= '            <Cell ss:StyleID="' . $style . '"' . $mergeAttr . '>' . "\n";
            $dataType = is_numeric($cell) && $style === 'data' ? 'Number' : 'String';
            $row .= '                <Data ss:Type="' . $dataType . '">' . htmlspecialchars($cell ?? '', ENT_XML1 | ENT_QUOTES) . '</Data>' . "\n";
            $row .= '            </Cell>' . "\n";
            
            // If we merged across, we only provide the first cell
            if ($index === 0 && $mergeAcross > 0) break;
        }
        $row .= '        </Row>' . "\n";
        return $row;
    }
    
    protected function getStyles()
    {
        return '    <Styles>' . "\n" .
               '        <Style ss:ID="default">' . "\n" .
               '            <Alignment ss:Vertical="Center"/>' . "\n" .
               '            <Font ss:FontName="Calibri" ss:Size="11"/>' . "\n" .
               '        </Style>' . "\n" .
               '        <Style ss:ID="header">' . "\n" .
               '            <Font ss:FontName="Calibri" ss:Bold="1" ss:Size="14" ss:Color="#000000"/>' . "\n" .
               '            <Alignment ss:Horizontal="Center" ss:Vertical="Center"/>' . "\n" .
               '        </Style>' . "\n" .
               '        <Style ss:ID="subheader">' . "\n" .
               '            <Font ss:FontName="Calibri" ss:Bold="1" ss:Size="12" ss:Color="#333333"/>' . "\n" .
               '            <Alignment ss:Horizontal="Center" ss:Vertical="Center"/>' . "\n" .
               '        </Style>' . "\n" .
               '        <Style ss:ID="title">' . "\n" .
               '            <Font ss:FontName="Calibri" ss:Bold="1" ss:Size="16" ss:Color="#1F4E78"/>' . "\n" .
               '            <Alignment ss:Horizontal="Center" ss:Vertical="Center"/>' . "\n" .
               '        </Style>' . "\n" .
               '        <Style ss:ID="bold">' . "\n" .
               '            <Font ss:FontName="Calibri" ss:Bold="1" ss:Size="11"/>' . "\n" .
               '            <Alignment ss:Vertical="Center"/>' . "\n" .
               '        </Style>' . "\n" .
               '        <Style ss:ID="section">' . "\n" .
               '            <Font ss:FontName="Calibri" ss:Bold="1" ss:Size="12" ss:Color="#FFFFFF"/>' . "\n" .
               '            <Interior ss:Color="#2E75B6" ss:Pattern="Solid"/>' . "\n" .
               '            <Alignment ss:Horizontal="Left" ss:Vertical="Center"/>' . "\n" .
               '            <Borders>' . "\n" .
               '                <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#000000"/>' . "\n" .
               '                <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#000000"/>' . "\n" .
               '                <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#000000"/>' . "\n" .
               '                <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#000000"/>' . "\n" .
               '            </Borders>' . "\n" .
               '        </Style>' . "\n" .
               '        <Style ss:ID="tableHeader">' . "\n" .
               '            <Font ss:FontName="Calibri" ss:Bold="1" ss:Size="11" ss:Color="#FFFFFF"/>' . "\n" .
               '            <Interior ss:Color="#5B9BD5" ss:Pattern="Solid"/>' . "\n" .
               '            <Alignment ss:Horizontal="Center" ss:Vertical="Center" ss:WrapText="1"/>' . "\n" .
               '            <Borders>' . "\n" .
               '                <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#000000"/>' . "\n" .
               '                <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#000000"/>' . "\n" .
               '                <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#000000"/>' . "\n" .
               '                <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#000000"/>' . "\n" .
               '            </Borders>' . "\n" .
               '        </Style>' . "\n" .
               '        <Style ss:ID="data">' . "\n" .
               '            <Alignment ss:Vertical="Center" ss:Horizontal="Left" ss:WrapText="1"/>' . "\n" .
               '            <Font ss:FontName="Calibri" ss:Size="11"/>' . "\n" .
               '            <Borders>' . "\n" .
               '                <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#D9D9D9"/>' . "\n" .
               '                <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#D9D9D9"/>' . "\n" .
               '                <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#D9D9D9"/>' . "\n" .
               '                <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#D9D9D9"/>' . "\n" .
               '            </Borders>' . "\n" .
               '        </Style>' . "\n" .
               '    </Styles>' . "\n";
    }
}
