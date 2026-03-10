<!DOCTYPE html>
<html>
<head>
    <title>Who Is Serving? - Diagnostic</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        h1 { color: #007bff; }
        table { border-collapse: collapse; width: 100%; margin: 20px 0; }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        th { background: #007bff; color: white; }
        .success { color: #28a745; font-weight: bold; }
        .error { color: #dc3545; font-weight: bold; }
        .warning { color: #ffc107; font-weight: bold; }
    </style>
</head>
<body>
    <h1>🔍 Who Is Serving? - Aggregate and Correction Section</h1>
    
    <h2>Currently Serving:</h2>
    @if($serving)
        <table>
            <tr><th>ID</th><th>Queue #</th><th>Name</th><th>Status</th><th>Priority</th><th>Section</th></tr>
            <tr>
                <td>{{ $serving->id }}</td>
                <td>{{ $serving->queue_number }}</td>
                <td>{{ $serving->guest_name }}</td>
                <td>{{ $serving->status }}</td>
                <td>{{ ucfirst($serving->priority) }}</td>
                <td>{{ $serving->section }}</td>
            </tr>
        </table>
    @else
        <p class="error">❌ NO ONE IS CURRENTLY SERVING</p>
    @endif
    
    <h2>Waiting Inquiries (Ordered by Created At):</h2>
    @if($waiting->isEmpty())
        <p>No waiting inquiries in this section</p>
    @else
        <table>
            <tr><th>ID</th><th>Queue #</th><th>Name</th><th>Priority</th><th>Created At</th></tr>
            @foreach($waiting as $w)
                <tr>
                    <td>{{ $w->id }}</td>
                    <td>{{ $w->queue_number }}</td>
                    <td>{{ $w->guest_name }}</td>
                    <td>{{ ucfirst($w->priority) }}</td>
                    <td>{{ $w->created_at->format('Y-m-d H:i:s') }}</td>
                </tr>
            @endforeach
        </table>
    @endif
    
    <h2>Joshua's Inquiry (ID: 65):</h2>
    @if($joshua)
        <ul>
            <li>ID: {{ $joshua->id }}</li>
            <li>Queue #: {{ $joshua->queue_number }}</li>
            <li>Name: {{ $joshua->guest_name }}</li>
            <li>Status: {{ $joshua->status }}</li>
            <li>Priority: {{ $joshua->priority }}</li>
            <li>Category ID: {{ $joshua->category_id }}</li>
            <li>Created At: {{ $joshua->created_at->format('Y-m-d H:i:s') }}</li>
        </ul>
        
        @if($joshua->category)
            <p><strong>Category Section: {{ $joshua->category->section }}</strong></p>
        @endif
        
        @php
            $isFirst = ($waiting->first() && $waiting->first()->id == 65);
        @endphp
        
        <p style="font-size: 1.5em;">
            Is Joshua first in queue? 
            @if($isFirst)
                <span class="success">✅ YES</span>
            @else
                <span class="error">❌ NO</span>
            @endif
        </p>
        
        <p style="font-size: 1.5em;">
            Should Joshua have enabled serve button? 
            @if(!$serving && $isFirst)
                <span class="success">✅ YES - Button should be ENABLED!</span>
            @elseif($serving)
                <span class="warning">⚠️ Someone else is being served ({{ $serving->queue_number }})</span>
            @else
                <span class="error">❌ NO</span>
            @endif
        </p>
    @else
        <p class="error">Joshua's inquiry not found!</p>
    @endif
    
    <hr>
    <p><a href="{{ route('admin.inquiries') }}">← Back to Admin Inquiries</a></p>
</body>
</html>
