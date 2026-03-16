<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Queue Ticket - {{ $inquiry->queue_number }}</title>
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/bootstrap-icons.css') }}">
    <style>
        @media print {
            body {
                width: 80mm;
                margin: 0 auto;
                padding: 0;
                background-color: white;
            }
            .ticket-wrapper {
                padding: 0;
                min-height: auto;
                display: block;
            }
            .ticket-container {
                box-shadow: none !important;
                border: none !important;
                margin: 0 auto !important;
                width: 100% !important;
                max-width: 100% !important;
            }
            .no-print {
                display: none !important;
            }
        }

        body {
            background-color: #f5f5f5;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
        }

        .ticket-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            width: 100%;
        }

        .ticket-container {
            width: 100%;
            max-width: 350px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            overflow: hidden;
            margin: 0 auto;
        }

        .ticket-header {
            background: linear-gradient(135deg, #1a5f2a 0%, #2e7d32 100%);
            color: white;
            padding: 25px 20px;
            text-align: center;
        }

        .ticket-header h1 {
            font-size: 1.2rem;
            margin: 0;
            font-weight: 600;
        }

        .ticket-header p {
            margin: 5px 0 0 0;
            opacity: 0.9;
            font-size: 0.85rem;
        }

        .ticket-body {
            padding: 30px 20px;
            text-align: center;
        }

        .queue-label {
            font-size: 0.9rem;
            color: #666;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .queue-number {
            font-size: 4rem;
            font-weight: 700;
            color: #1a5f2a;
            margin: 15px 0;
            line-height: 1;
        }

        .category-badge {
            display: inline-block;
            padding: 8px 20px;
            border-radius: 25px;
            font-weight: 600;
            font-size: 0.9rem;
            margin: 15px 0;
        }

        .guest-info {
            margin-top: 25px;
            padding-top: 20px;
            border-top: 2px dashed #dee2e6;
            text-align: center;
        }

        .info-row {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 12px;
            font-size: 0.95rem;
        }

        .info-label {
            color: #888;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 2px;
        }

        .info-value {
            font-weight: 700;
            color: #1a5f2a;
            font-size: 1.1rem;
        }

        .ticket-footer {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
        }

        .date-time {
            font-size: 0.85rem;
            color: #666;
            margin-bottom: 10px;
        }

        .barcode {
            margin: 15px 0;
            padding: 10px;
            background: white;
            border-radius: 5px;
        }

        .barcode-text {
            font-family: 'Courier New', monospace;
            font-size: 1.2rem;
            letter-spacing: 3px;
            color: #333;
        }

        .instructions {
            font-size: 0.8rem;
            color: #666;
            margin-top: 15px;
            line-height: 1.5;
        }

        .priority-indicator {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 5px 12px;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 600;
            margin-top: 10px;
        }

        .priority-normal { background: #e9ecef; color: #495057; }
        .priority-priority { background: #fff3cd; color: #856404; }
        .priority-urgent { background: #f8d7da; color: #721c24; }

        /* Print Button */
        .print-actions {
            position: fixed;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 10px;
            z-index: 1000;
        }

        .btn-print {
            padding: 15px 40px;
            font-size: 1.1rem;
            font-weight: 600;
            border-radius: 50px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.3);
        }

        .btn-close-ticket {
            padding: 15px 30px;
            font-size: 1.1rem;
            border-radius: 50px;
        }
    </style>
</head>
<body>
    <div class="ticket-wrapper">
        <div class="ticket-container">
            <!-- Header -->
            <div class="ticket-header">
                <i class="bi bi-tree" style="font-size: 2.5rem; margin-bottom: 10px; display: block;"></i>
                <h1>DENR</h1>
                <p>Department of Environment<br>and Natural Resources</p>
            </div>

            <!-- Body -->
            <div class="ticket-body">
                <div class="queue-label">Queue Number</div>
                <div class="queue-number">{{ $inquiry->short_queue_number }}</div>
                
                @if($inquiry->category)
                    <div class="category-badge" style="background-color: {{ $inquiry->category->color }}20; color: {{ $inquiry->category->color }}; border: 2px solid {{ $inquiry->category->color }}; border-radius: 8px;">
                        {{ $inquiry->category->name }}
                    </div>
                @endif

                @if($inquiry->priority != 'normal')
                    <div class="priority-indicator priority-{{ $inquiry->priority }}" style="margin: 10px auto; display: inline-flex;">
                        <i class="bi bi-exclamation-circle"></i>
                        {{ strtoupper($inquiry->priority) }}
                    </div>
                @endif

                <div class="guest-info">
                    <div class="info-row">
                        <span class="info-label">Client Name</span>
                        <span class="info-value">{{ $inquiry->guest_name }}</span>
                    </div>
                    @if($inquiry->contact_number)
                    <div class="info-row">
                        <span class="info-label">Contact Number</span>
                        <span class="info-value">{{ $inquiry->contact_number }}</span>
                    </div>
                    @endif
                    <div class="info-row" style="flex-direction: row; justify-content: space-around; width: 100%; border-top: 1px solid #eee; padding-top: 15px; margin-top: 5px;">
                        <div style="display: flex; flex-direction: column;">
                            <span class="info-label">Date</span>
                            <span class="info-value" style="font-size: 0.9rem;">{{ $inquiry->created_at->format('M d, Y') }}</span>
                        </div>
                        <div style="display: flex; flex-direction: column;">
                            <span class="info-label">Time</span>
                            <span class="info-value" style="font-size: 0.9rem;">{{ $inquiry->created_at->format('h:i A') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="ticket-footer">
                <div class="barcode">
                    <div class="barcode-text">{{ $inquiry->short_queue_number }}</div>
                    <small class="text-muted" style="font-size: 0.65rem; opacity: 0.7;">{{ $inquiry->queue_number }}</small>
                </div>
                
                <div class="instructions">
                    <i class="bi bi-info-circle"></i> Please wait for your number to be called.<br>
                    Proceed to the designated counter when announced.
                </div>
            </div>
        </div>
    </div>

    <!-- Print Actions -->
    <div class="print-actions no-print">
        <button onclick="window.print()" class="btn btn-success btn-print">
            <i class="bi bi-printer"></i> Print Ticket
        </button>
        <a href="{{ route('front-desk.index') }}" class="btn btn-outline-secondary btn-close-ticket">
            <i class="bi bi-x-lg"></i> Close
        </a>
    </div>

    <script>
        // Auto-print after 1 second
        setTimeout(() => {
            window.print();
        }, 1000);
    </script>
</body>
</html>
