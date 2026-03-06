<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lobby Monitor - Ground Floor</title>
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/bootstrap-icons.css') }}">
    <style>
        body {
            background: linear-gradient(135deg, #1a5f2a 0%, #0d3d16 100%);
            min-height: 100vh;
            color: white;
            overflow-x: hidden;
        }
        .monitor-header {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-bottom: 3px solid rgba(255, 255, 255, 0.2);
        }
        .section-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 40px;
            overflow: hidden;
            box-shadow: 0 35px 90px rgba(0, 0, 0, 0.6);
            transition: transform 0.3s ease;
            height: 600px;
            min-width: 380px;
            width: 100%;
        }
        .section-card:hover {
            transform: scale(1.02);
        }
        .section-header {
            padding: 40px;
            color: white;
            font-weight: bold;
            font-size: 3.2rem;
        }
        
        /* Grid container for 3x3 layout */
        .grid-container {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            grid-template-rows: repeat(3, 1fr);
            gap: 25px;
            padding: 20px;
            min-height: 80vh;
        }
        
        /* Card positions in the grid */
        .card-position-acs { grid-column: 1; grid-row: 1; }
        .card-position-ooss { grid-column: 2; grid-row: 1; }
        .card-position-scs { grid-column: 3; grid-row: 1; }
        .card-position-les { grid-column: 1; grid-row: 2; }
        .card-position-additional-1 { grid-column: 2; grid-row: 2; }
        .card-position-additional-2 { grid-column: 3; grid-row: 2; }
        .card-position-additional-3 { grid-column: 1; grid-row: 3; }
        .card-position-additional-4 { grid-column: 2; grid-row: 3; }
        .card-position-additional-5 { grid-column: 3; grid-row: 3; }
        .queue-number {
            font-size: 7.5rem;
            font-weight: bold;
            color: #1a5f2a;
            text-shadow: 5px 5px 10px rgba(0,0,0,0.4);
        }
        .waiting-badge {
            font-size: 2.5rem;
            padding: 25px 35px;
            border-radius: 50px;
        }
        .status-text {
            font-size: 2.8rem;
        }
        .category-text {
            font-size: 2.3rem;
        }
        .fullscreen-btn {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            background: rgba(255, 255, 255, 0.2);
            border: 2px solid white;
            color: white;
            padding: 10px 20px;
            border-radius: 50px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .fullscreen-btn:hover {
            background: white;
            color: #1a5f2a;
        }
        .clock-display {
            font-size: 2.5rem;
            font-weight: bold;
        }
        .date-display {
            font-size: 1.2rem;
            opacity: 0.9;
        }
        @media (max-width: 768px) {
            .queue-number {
                font-size: 2rem;
            }
            .clock-display {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <!-- Fullscreen Button -->
    <button class="fullscreen-btn" onclick="toggleFullscreen()">
        <i class="bi bi-fullscreen"></i> Fullscreen
    </button>

    <!-- Header -->
    <div class="monitor-header py-4">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <div class="d-flex align-items-center">
                        <img src="{{ asset('images/denrlogo.webp') }}" alt="DENR Logo" style="width: 80px; height: 80px; border-radius: 50%; margin-right: 20px; border: 3px solid white;">
                        <div>
                            <h1 class="mb-0 fw-bold">DENR REGION 4A CALABARZON</h1>
                            <p class="mb-0 opacity-75">Queueing & Inquiry Management System</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 text-end">
                    <div class="clock-display" id="currentTime">--:--:--</div>
                    <div class="date-display" id="currentDate">--</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Queue Status Cards - 3x3 Grid Layout -->
    <div class="container-fluid py-4">
        <div class="grid-container" id="queueDisplay">
            <!-- Fixed positions: ACS (col 1), OOSS (col 2), SCS (col 3) in first row, LES (col 1 of second row), others follow -->
            
            <!-- Grid Position 1: ACS (Top Left) -->
            @if(isset($sectionData['ACS']))
            <div class="card-position-acs">
                <div class="section-card h-100">
                    <div class="section-header d-flex justify-content-between align-items-center" style="background-color: {{ $sectionData['ACS']['color'] }}">
                        <h2 class="mb-0 fw-bold">{{ $sectionData['ACS']['name'] }}</h2>
                        <span class="badge bg-dark waiting-badge" id="waiting_ACS">Waiting: 0</span>
                    </div>
                    <div class="p-5 text-center">
                        <div class="queue-number mb-3" id="serving_ACS">---</div>
                        <div class="text-muted status-text" id="status_ACS">No Queue</div>
                        <div class="mt-2 text-muted status-text" style="font-size: 2.0rem;" id="next_queue_ACS"></div>
                        <div class="mt-3 text-muted category-text" id="category_ACS"></div>
                    </div>
                </div>
            </div>
            @endif
            
            <!-- Grid Position 2: OOSS (Top Center) -->
            @if(isset($sectionData['OOSS']))
            <div class="card-position-ooss">
                <div class="section-card h-100">
                    <div class="section-header d-flex justify-content-between align-items-center" style="background-color: {{ $sectionData['OOSS']['color'] }}">
                        <h2 class="mb-0 fw-bold">{{ $sectionData['OOSS']['name'] }}</h2>
                        <span class="badge bg-dark waiting-badge" id="waiting_OOSS">Waiting: 0</span>
                    </div>
                    <div class="p-5 text-center">
                        <div class="queue-number mb-3" id="serving_OOSS">---</div>
                        <div class="text-muted status-text" id="status_OOSS">No Queue</div>
                        <div class="mt-2 text-muted status-text" style="font-size: 2.0rem;" id="next_queue_OOSS"></div>
                        <div class="mt-3 text-muted category-text" id="category_OOSS"></div>
                    </div>
                </div>
            </div>
            @endif
            
            <!-- Grid Position 3: SCS (Top Right) -->
            @if(isset($sectionData['SCS']))
            <div class="card-position-scs">
                <div class="section-card h-100">
                    <div class="section-header d-flex justify-content-between align-items-center" style="background-color: {{ $sectionData['SCS']['color'] }}">
                        <h2 class="mb-0 fw-bold">{{ $sectionData['SCS']['name'] }}</h2>
                        <span class="badge bg-dark waiting-badge" id="waiting_SCS">Waiting: 0</span>
                    </div>
                    <div class="p-5 text-center">
                        <div class="queue-number mb-3" id="serving_SCS">---</div>
                        <div class="text-muted status-text" id="status_SCS">No Queue</div>
                        <div class="mt-2 text-muted status-text" style="font-size: 2.0rem;" id="next_queue_SCS"></div>
                        <div class="mt-3 text-muted category-text" id="category_SCS"></div>
                    </div>
                </div>
            </div>
            @endif
            
            <!-- Grid Position 4: LES (Middle Left) -->
            @if(isset($sectionData['LES']))
            <div class="card-position-les">
                <div class="section-card h-100">
                    <div class="section-header d-flex justify-content-between align-items-center" style="background-color: {{ $sectionData['LES']['color'] }}">
                        <h2 class="mb-0 fw-bold">{{ $sectionData['LES']['name'] }}</h2>
                        <span class="badge bg-dark waiting-badge" id="waiting_LES">Waiting: 0</span>
                    </div>
                    <div class="p-5 text-center">
                        <div class="queue-number mb-3" id="serving_LES">---</div>
                        <div class="text-muted status-text" id="status_LES">No Queue</div>
                        <div class="mt-2 text-muted status-text" style="font-size: 2.0rem;" id="next_queue_LES"></div>
                        <div class="mt-3 text-muted category-text" id="category_LES"></div>
                    </div>
                </div>
            </div>
            @endif
            
            <!-- Additional sections fill remaining spots in order (Row 2 - Card 5, Row 2 - Card 6, etc.) -->
            @php
            $fixedSections = ['ACS', 'OOSS', 'SCS', 'LES'];
            $dynamicSections = [];
            foreach($sectionData as $section => $data) {
                if(!in_array($section, $fixedSections)) {
                    $dynamicSections[] = $section;
                }
            }
            @endphp
            
            @foreach($dynamicSections as $index => $section)
            @php 
                $data = $sectionData[$section]; 
                $positionClass = 'card-position-additional-' . ($index + 1);
            @endphp
            <div class="{{ $positionClass }}">
                <div class="section-card h-100">
                    <div class="section-header d-flex justify-content-between align-items-center" style="background-color: {{ $data['color'] }}">
                        <h2 class="mb-0 fw-bold">{{ $data['name'] }}</h2>
                        <span class="badge bg-dark waiting-badge" id="waiting_{{ $section }}">Waiting: 0</span>
                    </div>
                    <div class="p-5 text-center">
                        <div class="queue-number mb-3" id="serving_{{ $section }}">---</div>
                        <div class="text-muted status-text" id="status_{{ $section }}">No Queue</div>
                        <div class="mt-2 text-muted status-text" style="font-size: 2.0rem;" id="next_queue_{{ $section }}"></div>
                        <div class="mt-3 text-muted category-text" id="category_{{ $section }}"></div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    
    <!-- Responsive adjustments for different screen sizes -->
    <style>
        @media (max-width: 1200px) {
            .grid-container {
                grid-template-columns: repeat(2, 1fr);
                grid-template-rows: repeat(4, 1fr);
                gap: 20px;
                margin: 0 auto;
                padding: 20px;
            }
            .card-position-acs { grid-column: 1; grid-row: 1; }
            .card-position-ooss { grid-column: 2; grid-row: 1; }
            .card-position-scs { grid-column: 1; grid-row: 2; }
            .card-position-les { grid-column: 2; grid-row: 2; }
            .card-position-additional-1 { grid-column: 1; grid-row: 3; }
            .card-position-additional-2 { grid-column: 2; grid-row: 3; }
            .card-position-additional-3 { grid-column: 1; grid-row: 4; }
            .card-position-additional-4 { grid-column: 2; grid-row: 4; }
            .card-position-additional-5 { grid-column: 1; grid-row: 5; }
        }
        
        @media (max-width: 768px) {
            .grid-container {
                grid-template-columns: 1fr;
                grid-template-rows: repeat(6, 1fr);
                gap: 15px;
                padding: 15px;
                margin: 0 auto;
            }
            .card-position-acs { grid-column: 1; grid-row: 1; }
            .card-position-ooss { grid-column: 1; grid-row: 2; }
            .card-position-scs { grid-column: 1; grid-row: 3; }
            .card-position-les { grid-column: 1; grid-row: 4; }
            .card-position-additional-1 { grid-column: 1; grid-row: 5; }
            .card-position-additional-2 { grid-column: 1; grid-row: 6; }
            .card-position-additional-3 { grid-column: 1; grid-row: 7; }
            .card-position-additional-4 { grid-column: 1; grid-row: 8; }
            .card-position-additional-5 { grid-column: 1; grid-row: 9; }
            
            .section-card {
                min-width: auto;
                height: auto;
                min-height: 300px;
            }
            
            .section-header {
                font-size: 2.2rem;
                padding: 25px;
            }
            
            .queue-number {
                font-size: 5rem;
            }
            
            .status-text {
                font-size: 2rem;
            }
            
            .category-text {
                font-size: 1.6rem;
            }
        }
        
        @media (min-width: 1920px) {
            .section-card {
                min-width: 450px;
            }
            
            .section-header {
                font-size: 3.5rem;
            }
            
            .queue-number {
                font-size: 8.5rem;
            }
            
            .grid-container {
                margin: 0 auto;
                padding: 30px;
            }
        }
    </style>

    <!-- Bootstrap JS -->
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    
    <script>
    // Cache buster - forces browser to reload this script
    console.log('Lobby Monitor Script Loaded - ' + new Date().toISOString());
    
    // Fullscreen toggle function
    function toggleFullscreen() {
        if (!document.fullscreenElement) {
            document.documentElement.requestFullscreen().catch(err => {
                console.log(`Error attempting to enable fullscreen: ${err.message}`);
            });
        } else {
            document.exitFullscreen();
        }
    }

    // Auto-enter fullscreen on load (optional - for TV display)
    // Uncomment the line below if you want it to auto-fullscreen
    // window.addEventListener('load', toggleFullscreen);

    // Update clock
    function updateClock() {
        const now = new Date();
        document.getElementById('currentTime').textContent = now.toLocaleTimeString('en-US', { 
            hour: '2-digit', 
            minute: '2-digit',
            second: '2-digit'
        });
        document.getElementById('currentDate').textContent = now.toLocaleDateString('en-US', { 
            weekday: 'long', 
            year: 'numeric', 
            month: 'long', 
            day: 'numeric' 
        });
    }
    
    setInterval(updateClock, 1000);
    updateClock();

    // Load queue data
    function loadQueueData() {
        fetch('{{ route('monitor.queue-data') }}')
            .then(response => response.json())
            .then(data => {
                updateQueueDisplay(data);
            })
            .catch(error => console.error('Error loading queue data:', error));
    }

    // Update queue display
    function updateQueueDisplay(data) {
        if (!data.sections) return;
        
        for (const [section, info] of Object.entries(data.sections)) {
            // Update waiting count
            const waitingEl = document.getElementById('waiting_' + section);
            if (waitingEl) {
                waitingEl.textContent = 'Waiting: ' + info.waiting_count;
            }
            
            // Update queue number and status
            const servingEl = document.getElementById('serving_' + section);
            const statusEl = document.getElementById('status_' + section);
            const categoryEl = document.getElementById('category_' + section);
            const nextQueueEl = document.getElementById('next_queue_' + section);
            
            if (servingEl && statusEl) {
                if (info.now_serving) {
                    // Someone is being served
                    const queueNum = formatQueueNumber(info.now_serving);
                    servingEl.textContent = queueNum;
                    servingEl.style.color = '#1a5f2a';
                    statusEl.textContent = 'Now Serving';
                    
                    // Show next in queue if available
                    if (nextQueueEl) {
                        if (info.second_in_queue) {
                            const nextNum = formatQueueNumber(info.second_in_queue);
                            nextQueueEl.innerHTML = '<div style="font-size: 1.8rem; margin-top: 10px;">Next in Queue</div><div style="font-weight: bold; color: #f39c12;">' + nextNum + '</div>';
                        } else if (info.first_in_queue) {
                            const nextNum = formatQueueNumber(info.first_in_queue);
                            nextQueueEl.innerHTML = '<div style="font-size: 1.8rem; margin-top: 10px;">Next in Queue</div><div style="font-weight: bold; color: #f39c12;">' + nextNum + '</div>';
                        } else {
                            nextQueueEl.textContent = '';
                        }
                    }
                    
                    // Hide category text - we don't want to display it
                    if (categoryEl) categoryEl.textContent = '';
                } else if (info.first_in_queue) {
                    // No one being served, show first in queue
                    const queueNum = formatQueueNumber(info.first_in_queue);
                    servingEl.textContent = queueNum;
                    servingEl.style.color = '#f39c12';
                    statusEl.textContent = 'Now Serving';
                    
                    // Show next in queue if available
                    if (nextQueueEl) {
                        if (info.second_in_queue) {
                            const nextNum = formatQueueNumber(info.second_in_queue);
                            nextQueueEl.innerHTML = '<div style="font-size: 1.8rem; margin-top: 10px;">Next in Queue</div><div style="font-weight: bold; color: #f39c12;">' + nextNum + '</div>';
                        } else {
                            nextQueueEl.textContent = '';
                        }
                    }
                    
                    // Hide category text - we don't want to display it
                    if (categoryEl) categoryEl.textContent = '';
                } else {
                    servingEl.textContent = '---';
                    servingEl.style.color = '#6c757d';
                    statusEl.textContent = 'No Queue';
                    if (nextQueueEl) nextQueueEl.textContent = '';
                    if (categoryEl) categoryEl.textContent = '';
                }
            }
        }
    }

    // Format queue number to show only sequential number
    function formatQueueNumber(fullQueueNumber) {
        console.log('Formatting queue number:', fullQueueNumber);
        
        // Try to extract the last number after the last hyphen
        // e.g., "SECSIME NO.R4A-L_SMD-01-009" -> "#9"
        // e.g., "dsds-001" -> "#1"
        const parts = fullQueueNumber.split('-');
        if (parts.length > 0) {
            const lastPart = parts[parts.length - 1];
            const num = parseInt(lastPart.replace(/^0+/, '')); // Remove leading zeros
            console.log('Extracted number:', num);
            if (!isNaN(num)) {
                const formatted = '#' + num;
                console.log('Formatted as:', formatted);
                return formatted;
            }
        }
        // Fallback: if no number found, return the original
        console.log('Returning original:', fullQueueNumber);
        return fullQueueNumber;
    }

    // Auto-refresh every 5 seconds
    setInterval(loadQueueData, 5000);
    
    // Initial load
    loadQueueData();
    </script>
</body>
</html>
