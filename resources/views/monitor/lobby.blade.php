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
            background: url('{{ asset('images/greenery_forest_nature_path_with_sunbeam_4k_hd_nature.jpg') }}') no-repeat center center fixed;
            background-size: cover;
            min-height: 100vh;
            color: white;
            overflow-x: hidden;
        }
        
        /* Dark overlay for better card visibility */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(15, 76, 26, 0.85) 0%, rgba(10, 47, 18, 0.9) 50%, rgba(5, 25, 9, 0.95) 100%);
            pointer-events: none;
            z-index: 0;
        }
        
        /* Animated background pattern */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: 
                radial-gradient(circle at 20% 50%, rgba(26, 95, 42, 0.15) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(26, 95, 42, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 40% 20%, rgba(40, 167, 69, 0.1) 0%, transparent 50%);
            pointer-events: none;
            z-index: 0;
            animation: pulse 15s ease-in-out infinite;
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.8; transform: scale(1.05); }
        }
        
        /* Subtle grid pattern overlay */
        body::after {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: 
                linear-gradient(rgba(255, 255, 255, 0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 255, 255, 0.03) 1px, transparent 1px);
            background-size: 50px 50px;
            pointer-events: none;
            z-index: 0;
        }
        
        .monitor-header {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(15px) saturate(180%);
            border-bottom: 2px solid rgba(255, 255, 255, 0.15);
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.3);
            position: relative;
            z-index: 10;
        }
        
        .section-card {
            background: linear-gradient(145deg, rgba(255, 255, 255, 0.95) 0%, rgba(250, 252, 250, 0.92) 100%);
            border-radius: 35px;
            overflow: hidden;
            box-shadow: 
                0 20px 60px rgba(0, 0, 0, 0.4),
                0 0 0 1px rgba(255, 255, 255, 0.2) inset,
                0 0 80px rgba(26, 95, 42, 0.15);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            height: 100%;
            min-height: 600px;
            width: 100%;
            display: flex;
            flex-direction: column;
            position: relative;
            z-index: 1;
        }
        
        .section-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(26, 95, 42, 0.03) 0%, transparent 50%, rgba(26, 95, 42, 0.02) 100%);
            pointer-events: none;
            border-radius: 35px;
        }
        
        .section-card:hover {
            transform: translateY(-8px) scale(1.015);
            box-shadow: 
                0 30px 80px rgba(0, 0, 0, 0.5),
                0 0 0 1px rgba(255, 255, 255, 0.3) inset,
                0 0 120px rgba(26, 95, 42, 0.25);
        }
        .section-header {
            padding: 25px 20px;
            color: white;
            font-weight: bold;
            font-size: 1.8rem;
            min-height: 100px;
            display: flex;
            align-items: center;
            text-align: center;
            justify-content: center;
            background: linear-gradient(135deg, rgba(26, 95, 42, 0.85) 0%, rgba(20, 80, 35, 0.9) 100%);
        }
        
        /* Grid container for 1x4 layout (1 row, 4 columns) */
        .grid-container {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            grid-template-rows: auto;
            gap: 25px;
            padding: 30px;
            min-height: 85vh;
            justify-content: center;
            align-items: stretch;
            max-width: 100%;
            margin: 0 auto;
            transition: all 0.5s ease;
        }
        
        /* Dynamic column adjustments based on active cards */
        .grid-container[data-active-cards="1"] {
            grid-template-columns: 1fr;
            max-width: 600px;
            margin: 0 auto;
        }
        
        .grid-container[data-active-cards="2"] {
            grid-template-columns: repeat(2, 1fr);
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .grid-container[data-active-cards="3"] {
            grid-template-columns: repeat(3, 1fr);
            max-width: 1600px;
            margin: 0 auto;
        }
        
        .grid-container[data-active-cards="4"] {
            grid-template-columns: repeat(4, 1fr);
            max-width: calc(100% - 100px);
            margin: 0 auto;
        }
        
        /* Card positions in the grid - all in first row */
        .card-position-acs { grid-column: 1; grid-row: 1; }
        .card-position-ooss { grid-column: 2; grid-row: 1; }
        .card-position-scs { grid-column: 3; grid-row: 1; }
        .card-position-les { grid-column: 4; grid-row: 1; }
        .card-position-additional-1 { grid-column: 5; grid-row: 1; }
        .card-position-additional-2 { grid-column: 6; grid-row: 1; }
        .card-position-additional-3 { grid-column: 7; grid-row: 1; }
        .card-position-additional-4 { grid-column: 8; grid-row: 1; }
        .card-position-additional-5 { grid-column: 9; grid-row: 1; }
        .queue-number {
            font-size: 4.5rem;
            font-weight: bold;
            color: #1a5f2a;
            text-shadow: 0 2px 8px rgba(26, 95, 42, 0.2);
            line-height: 1.1;
        }
        .waiting-badge {
            font-size: 1.4rem;
            padding: 15px 25px;
            border-radius: 50px;
        }
        .status-text {
            font-size: 1.6rem;
        }
        .category-text {
            font-size: 1.3rem;
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
            
            <!-- Grid Position 1: ACS (Red - Aggregate and Correction Section) -->
            @if(isset($sectionData['ACS']))
            <div class="card-position-acs" id="card_ACS">
                <div class="section-card h-100">
                    <div class="section-header d-flex justify-content-between align-items-center" style="background-color: {{ $headerColor }}">
                        <h2 class="mb-0 fw-bold">{{ $sectionData['ACS']['name'] }}</h2>
                        <span class="badge bg-dark waiting-badge" id="waiting_ACS">Waiting: 0</span>
                    </div>
                    <div class="p-4 text-center" style="flex: 1; display: flex; flex-direction: column; justify-content: center;">
                        <div class="queue-number mb-3" id="serving_ACS">---</div>
                        <div class="text-muted status-text" id="status_ACS">No Queue</div>
                        <div class="mt-2 text-muted status-text" style="font-size: 2.0rem;" id="next_queue_ACS"></div>
                        <div class="mt-3 text-muted category-text" id="category_ACS"></div>
                    </div>
                </div>
            </div>
            @endif
            
            <!-- Grid Position 2: OOSS (Light Blue - Original and Other Surveys Section) -->
            @if(isset($sectionData['OOSS']))
            <div class="card-position-ooss" id="card_OOSS">
                <div class="section-card h-100">
                    <div class="section-header d-flex justify-content-between align-items-center" style="background-color: {{ $headerColor }}">
                        <h2 class="mb-0 fw-bold">{{ $sectionData['OOSS']['name'] }}</h2>
                        <span class="badge bg-dark waiting-badge" id="waiting_OOSS">Waiting: 0</span>
                    </div>
                    <div class="p-4 text-center" style="flex: 1; display: flex; flex-direction: column; justify-content: center;">
                        <div class="queue-number mb-3" id="serving_OOSS">---</div>
                        <div class="text-muted status-text" id="status_OOSS">No Queue</div>
                        <div class="mt-2 text-muted status-text" style="font-size: 2.0rem;" id="next_queue_OOSS"></div>
                        <div class="mt-3 text-muted category-text" id="category_OOSS"></div>
                    </div>
                </div>
            </div>
            @endif
            
            <!-- Grid Position 3: SCS (Yellow Green - Surveys and Control Section) -->
            @if(isset($sectionData['SCS']))
            <div class="card-position-scs" id="card_SCS">
                <div class="section-card h-100">
                    <div class="section-header d-flex justify-content-between align-items-center" style="background-color: {{ $headerColor }}">
                        <h2 class="mb-0 fw-bold">{{ $sectionData['SCS']['name'] }}</h2>
                        <span class="badge bg-dark waiting-badge" id="waiting_SCS">Waiting: 0</span>
                    </div>
                    <div class="p-4 text-center" style="flex: 1; display: flex; flex-direction: column; justify-content: center;">
                        <div class="queue-number mb-3" id="serving_SCS">---</div>
                        <div class="text-muted status-text" id="status_SCS">No Queue</div>
                        <div class="mt-2 text-muted status-text" style="font-size: 2.0rem;" id="next_queue_SCS"></div>
                        <div class="mt-3 text-muted category-text" id="category_SCS"></div>
                    </div>
                </div>
            </div>
            @endif
            
            <!-- Grid Position 4: LES (Purple - Land Evaluation Section) -->
            @if(isset($sectionData['LES']))
            <div class="card-position-les" id="card_LES">
                <div class="section-card h-100">
                    <div class="section-header d-flex justify-content-between align-items-center" style="background-color: {{ $headerColor }}">
                        <h2 class="mb-0 fw-bold">{{ $sectionData['LES']['name'] }}</h2>
                        <span class="badge bg-dark waiting-badge" id="waiting_LES">Waiting: 0</span>
                    </div>
                    <div class="p-4 text-center" style="flex: 1; display: flex; flex-direction: column; justify-content: center;">
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
                // Use uniform DENR green color for all sections
                $headerColor = 'rgba(26, 95, 42, 0.85)';
            @endphp
            <div class="{{ $positionClass }}" id="card_{{ $section }}">
                <div class="section-card h-100">
                    <div class="section-header d-flex justify-content-between align-items-center" style="background-color: {{ $headerColor }}">
                        <h2 class="mb-0 fw-bold">{{ $data['name'] }}</h2>
                        <span class="badge bg-dark waiting-badge" id="waiting_{{ $section }}">Waiting: 0</span>
                    </div>
                    <div class="p-4 text-center" style="flex: 1; display: flex; flex-direction: column; justify-content: center;">
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
        @media (max-width: 1400px) {
            .grid-container {
                grid-template-columns: repeat(2, 1fr);
                grid-template-rows: auto;
                gap: 20px;
                padding: 25px;
                margin: 0 auto;
            }
            .section-card {
                min-height: 500px;
            }
            .section-header {
                font-size: 1.5rem;
                padding: 20px 15px;
                min-height: 90px;
            }
            .queue-number {
                font-size: 3.5rem;
            }
            .status-text {
                font-size: 1.4rem;
            }
            .category-text {
                font-size: 1.1rem;
            }
            .waiting-badge {
                font-size: 1.2rem;
                padding: 12px 20px;
            }
        }
        
        @media (max-width: 768px) {
            .grid-container {
                grid-template-columns: 1fr;
                grid-template-rows: auto;
                gap: 15px;
                padding: 15px;
                margin: 0 auto;
            }
            .section-card {
                min-height: 350px;
            }
            .section-header {
                font-size: 1.8rem;
                padding: 20px;
                min-height: 80px;
            }
            .queue-number {
                font-size: 4.0rem;
            }
            .status-text {
                font-size: 1.5rem;
            }
            .category-text {
                font-size: 1.2rem;
            }
            .waiting-badge {
                font-size: 1.3rem;
                padding: 12px 20px;
            }
        }
        
        @media (min-width: 1920px) {
            .section-card {
                min-height: 650px;
            }
            
            .section-header {
                font-size: 2.2rem;
                min-height: 110px;
            }
            
            .queue-number {
                font-size: 5.5rem;
            }
            
            .grid-container {
                max-width: 98%;
                margin: 0 auto;
                padding: 40px;
                gap: 35px;
            }
        }
        
        @media (min-width: 2560px) {
            .grid-container {
                max-width: 95%;
                margin: 0 auto;
                padding: 50px;
                gap: 45px;
            }
            
            .section-card {
                min-height: 750px;
            }
            
            .section-header {
                font-size: 2.8rem;
                min-height: 130px;
            }
            
            .queue-number {
                font-size: 6.5rem;
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
            
        let activeCardCount= 0;
        
        for (const [section, info] of Object.entries(data.sections)) {
            // Get card element
            const cardEl = document.getElementById('card_' + section);
           if (!cardEl) continue;
                
            // Check if section has any activity (someone being served OR people waiting)
            const hasActivity = info.now_serving || info.waiting_count > 0;
                
            // Show/hide card based on activity
           if (hasActivity) {
               cardEl.style.display = '';
                activeCardCount++;
            } else {
               cardEl.style.display = 'none';
                continue; // Skip updating this card
            }
                
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
                            nextQueueEl.textContent= '';
                        }
                    }
                        
                    // Hide category text - we don't want to display it
                    if (categoryEl) categoryEl.textContent= '';
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
                    if (categoryEl) categoryEl.textContent= '';
                }
            }
        }
        
        // Update grid layout based on number of active cards
        const gridContainer= document.getElementById('queueDisplay');
       if (gridContainer) {
            gridContainer.setAttribute('data-active-cards', activeCardCount);
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
