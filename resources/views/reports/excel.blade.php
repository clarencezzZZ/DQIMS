<table>
    <tbody>
        <tr>
            <td colspan="7" style="font-weight: bold; text-align: center;">Republic of the Philippines</td>
        </tr>
        <tr>
            <td colspan="7" style="text-align: center;">DEPARTMENT OF ENVIRONMENT AND NATURAL RESOURCES</td>
        </tr>
        <tr>
            <td colspan="7" style="text-align: center;">Regional Office No. 4A (CALABARZON)</td>
        </tr>
        <tr>
            <td colspan="7" style="font-weight: bold; text-align: center;">Queueing & Inquiry Management System Report</td>
        </tr>
        <tr><td colspan="7"></td></tr>
        <tr>
            <td colspan="2" style="font-weight: bold;">Report Period:</td>
            <td colspan="5">
                {{ optional($data['date_range']['start'] ?? null)->format('F d, Y') }} 
                to 
                {{ optional($data['date_range']['end'] ?? null)->format('F d, Y') }}
            </td>
        </tr>
        <tr>
            <td colspan="2" style="font-weight: bold;">Date Generated:</td>
            <td colspan="5">{{ \Carbon\Carbon::now()->format('F d, Y h:i A') }}</td>
        </tr>
        <tr><td colspan="7"></td></tr>
        <tr>
            <td colspan="4" style="font-weight: bold; background-color: #0d6efd; color: #ffffff; text-align: center;">OVERALL STATISTICS</td>
        </tr>
        <tr>
            <td style="font-weight: bold; border: 1px solid #000000; text-align: center;">TOTAL INQUIRIES</td>
            <td style="font-weight: bold; border: 1px solid #000000; text-align: center;">COMPLETED</td>
            <td style="font-weight: bold; border: 1px solid #000000; text-align: center;">TOTAL ASSESSMENTS</td>
            <td style="font-weight: bold; border: 1px solid #000000; text-align: center;">TOTAL REVENUE</td>
        </tr>
        <tr>
            <td style="border: 1px solid #000000; text-align: center;">{{ $data['overall_stats']['total'] ?? 0 }}</td>
            <td style="border: 1px solid #000000; text-align: center;">{{ $data['overall_stats']['completed'] ?? 0 }}</td>
            <td style="border: 1px solid #000000; text-align: center;">{{ $data['assessments_count'] ?? 0 }}</td>
            <td style="border: 1px solid #000000; text-align: center;">{{ number_format($data['total_fees'] ?? 0, 2) }}</td>
        </tr>
        <tr><td colspan="7"></td></tr>
        <tr>
            <td colspan="5" style="font-weight: bold; background-color: #0d6efd; color: #ffffff; text-align: center;">CATEGORY STATISTICS</td>
        </tr>
        <tr>
            <td style="font-weight: bold; border: 1px solid #000000;">#</td>
            <td style="font-weight: bold; border: 1px solid #000000;">Category</td>
            <td style="font-weight: bold; border: 1px solid #000000;">Section</td>
            <td style="font-weight: bold; border: 1px solid #000000;">Total</td>
            <td style="font-weight: bold; border: 1px solid #000000;">Status Breakdown</td>
        </tr>
        @php $counter = 1; @endphp
        @foreach(($data['category_stats'] ?? []) as $code => $stats)
            @if(($stats['total'] ?? 0) > 0)
                <tr>
                    <td style="border: 1px solid #000000;">{{ $counter++ }}</td>
                    <td style="font-weight: bold; border: 1px solid #000000;">{{ $stats['name'] ?? '' }}</td>
                    <td style="border: 1px solid #000000;">{{ $stats['section'] ?? '' }}</td>
                    <td style="font-weight: bold; border: 1px solid #000000;">{{ $stats['total'] ?? 0 }}</td>
                    <td style="border: 1px solid #000000;">
                        {{ $stats['completed'] ?? 0 }} Completed
                        @if(($stats['waiting'] ?? 0) > 0), {{ $stats['waiting'] }} Waiting @endif
                        @if(($stats['skipped'] ?? 0) > 0), {{ $stats['skipped'] }} Skipped @endif
                    </td>
                </tr>
            @endif
        @endforeach
        <tr><td colspan="7"></td></tr>
        <tr>
            <td colspan="7" style="font-weight: bold; background-color: #0d6efd; color: #ffffff; text-align: center;">INQUIRY DETAILS</td>
        </tr>
        <tr>
            <td style="font-weight: bold; border: 1px solid #000000;">#</td>
            <td style="font-weight: bold; border: 1px solid #000000;">Queue Number</td>
            <td style="font-weight: bold; border: 1px solid #000000;">Name</td>
            <td style="font-weight: bold; border: 1px solid #000000;">Category</td>
            <td style="font-weight: bold; border: 1px solid #000000;">Request Type</td>
            <td style="font-weight: bold; border: 1px solid #000000;">Status</td>
            <td style="font-weight: bold; border: 1px solid #000000;">Date</td>
        </tr>
        @foreach(($data['inquiries'] ?? []) as $index => $inquiry)
            <tr>
                <td style="border: 1px solid #000000;">{{ $index + 1 }}</td>
                <td style="font-weight: bold; border: 1px solid #000000;">{{ $inquiry->short_queue_number ?? '' }}</td>
                <td style="border: 1px solid #000000;">{{ $inquiry->name ?? '' }}</td>
                <td style="border: 1px solid #000000;">{{ $inquiry->category->name ?? 'N/A' }}</td>
                <td style="border: 1px solid #000000;">{{ $inquiry->request_type ?? '' }}</td>
                <td style="border: 1px solid #000000;">{{ ucfirst($inquiry->status ?? '') }}</td>
                <td style="border: 1px solid #000000;">{{ $inquiry->date ? \Carbon\Carbon::parse($inquiry->date)->format('M d, Y') : 'N/A' }}</td>
            </tr>
        @endforeach
        <tr><td colspan="7"></td></tr>
        <tr>
            <td colspan="2" style="font-weight: bold; background-color: #0d6efd; color: #ffffff; text-align: center;">REVENUE STATISTICS</td>
            <td colspan="5"></td>
        </tr>
        <tr>
            <td style="font-weight: bold; border: 1px solid #000000;">Date</td>
            <td style="font-weight: bold; border: 1px solid #000000;">Revenue</td>
            <td colspan="5"></td>
        </tr>
        @foreach(($charts['revenueChart'] ?? []) as $date => $revenue)
            <tr>
                <td style="border: 1px solid #000000;">{{ $date }}</td>
                <td style="border: 1px solid #000000;">{{ number_format($revenue ?? 0, 2) }}</td>
                <td colspan="5"></td>
            </tr>
        @endforeach
        <tr><td colspan="7"></td></tr>
        <tr>
            <td colspan="2" style="font-weight: bold; background-color: #0d6efd; color: #ffffff; text-align: center;">REPORT STATISTICS OVERVIEW</td>
            <td colspan="5"></td>
        </tr>
        <tr>
            <td style="font-weight: bold; border: 1px solid #000000;">Metric</td>
            <td style="font-weight: bold; border: 1px solid #000000;">Value</td>
            <td colspan="5"></td>
        </tr>
        @foreach(($charts['dailyChart'] ?? []) as $metric => $value)
            <tr>
                <td style="border: 1px solid #000000;">{{ $metric }}</td>
                <td style="border: 1px solid #000000;">{{ is_numeric($value) ? number_format($value, 2) : $value }}</td>
                <td colspan="5"></td>
            </tr>
        @endforeach
        <tr><td colspan="7"></td></tr>
        <tr>
            <td colspan="2" style="font-weight: bold; background-color: #0d6efd; color: #ffffff; text-align: center;">SECTION STATISTICS</td>
            <td colspan="5"></td>
        </tr>
        <tr>
            <td style="font-weight: bold; border: 1px solid #000000;">Section</td>
            <td style="font-weight: bold; border: 1px solid #000000;">Inquiries</td>
            <td colspan="5"></td>
        </tr>
        @foreach(($charts['sectionChart'] ?? []) as $section => $inquiries)
            <tr>
                <td style="border: 1px solid #000000;">{{ $section }}</td>
                <td style="border: 1px solid #000000;">{{ $inquiries ?? 0 }}</td>
                <td colspan="5"></td>
            </tr>
        @endforeach
    </tbody>
</table>