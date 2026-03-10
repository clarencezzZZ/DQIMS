<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lobby Monitor - ACS & OOSS</title>
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
        .monitor-header {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-bottom: 3px solid rgba(255, 255, 255, 0.2);
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
        
        /* Smooth card visibility transitions */
        [data-section] {
            transition: all 0.4s ease-in-out;
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
        
        /* Fixed height content areas */
        .card-content-area {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: calc(100% - 120px); /* Account for header height */
            padding: 2rem;
            box-sizing: border-box;
            position: relative;
        }
        
        .queue-info-container {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            width: 100%;
            height: 100%;
            text-align: center;
            max-height: calc(100% - 60px); /* Prevent overflow */
            overflow: hidden;
        }
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
            min-width: 200px;
            text-align: center;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
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
            font-size: 2.8rem;
            font-weight: bold;
        }
        .date-display {
            font-size: 1.4rem;
            opacity: 0.9;
        }
        /* Dynamic grid container with 3x3 monitor-style design */
        .grid-container {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            grid-template-rows: repeat(3, 1fr);
            gap: 25px;
            padding: 20px;
            min-height: 80vh;
            justify-content: center;
            align-items: center;
        }
        
        /* 2 cards layout - maximized width filling screen */
        .grid-container.two-cards {
            grid-template-columns: repeat(2, 1fr);
            justify-content: center;
            justify-items: center;
            width: 100%;
            max-width: none;
            margin: 0;
            padding: 20px 30px;
            gap: 30px;
        }
        
        /* 3 cards layout - maximized width */
        .grid-container.three-cards {
            grid-template-columns: repeat(3, 1fr);
            width: 100%;
            max-width: none;
            margin: 0;
            justify-content: space-between;
            padding: 25px 35px;
            gap: 30px;
        }
        
        /* 3x3 grid layout for all cases */
        .grid-container.four-or-more {
            grid-template-columns: repeat(3, 1fr);
            grid-template-rows: repeat(3, 1fr);
        }
        
        /* 2 cards specific positioning - centered alignment */
        .grid-container.two-cards .card-position-1 { grid-column: 1; grid-row: 1; justify-self: center; align-self: center; }
        .grid-container.two-cards .card-position-2 { grid-column: 2; grid-row: 1; justify-self: center; align-self: center; }
        
        /* 3 cards specific positioning - left, center, right */
        .grid-container.three-cards .card-position-1 { grid-column: 1; grid-row: 1; justify-self: center; align-self: center; }
        .grid-container.three-cards .card-position-2 { grid-column: 2; grid-row: 1; justify-self: center; align-self: center; }
        .grid-container.three-cards .card-position-3 { grid-column: 3; grid-row: 1; justify-self: center; align-self: center; }
        
        /* 4+ cards grid positions */
        .grid-container.four-or-more .card-position-1 { grid-column: 1; grid-row: 1; justify-self: center; align-self: center; }
        .grid-container.four-or-more .card-position-2 { grid-column: 2; grid-row: 1; justify-self: center; align-self: center; }
        .grid-container.four-or-more .card-position-3 { grid-column: 3; grid-row: 1; justify-self: center; align-self: center; }
        .grid-container.four-or-more .card-position-4 { grid-column: 1; grid-row: 2; justify-self: center; align-self: center; }
        .grid-container.four-or-more .card-position-5 { grid-column: 2; grid-row: 2; justify-self: center; align-self: center; }
        .grid-container.four-or-more .card-position-6 { grid-column: 3; grid-row: 2; justify-self: center; align-self: center; }
        .grid-container.four-or-more .card-position-7 { grid-column: 1; grid-row: 3; justify-self: center; align-self: center; }
        .grid-container.four-or-more .card-position-8 { grid-column: 2; grid-row: 3; justify-self: center; align-self: center; }
        .grid-container.four-or-more .card-position-9 { grid-column: 3; grid-row: 3; justify-self: center; align-self: center; }
        @media (max-width: 768px) {
            .queue-number {
                font-size: 2.5rem;
            }
            .clock-display {
                font-size: 1.8rem;
            }
            .section-card {
                max-width: 95%;
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
                            <p class="mb-0 opacity-75">Queueing & Inquiry Management System - ACS & OOSS</p>
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

    <!-- Queue Status Cards - Dynamic Layout -->
    @php 
        $cardCount = count($sectionData);
        $containerClass = '';
        if ($cardCount == 2) {
            $containerClass = 'two-cards';
        } elseif ($cardCount == 3) {
            $containerClass = 'three-cards';
        } elseif ($cardCount >= 4) {
            $containerClass = 'four-or-more';
        }
    @endphp
    <div class="container-fluid py-4">
        <div class="grid-container {{ $containerClass }}" id="queueDisplay">
            @foreach($sectionData as $section => $data)
            @php 
                $positionClass = 'card-position-' . ($loop->index + 1);
                // Assign colors based on section name/keywords
                $headerColor = '#6c757d'; // Default gray
                $sectionNameUpper = strtoupper($section);
                
                if (strpos($sectionNameUpper, 'AGGREGATE') !== false || strpos($sectionNameUpper, 'CORRECTION') !== false || $sectionNameUpper === 'ACS') {
                    $headerColor = '#dc3545'; // Red
                } elseif (strpos($sectionNameUpper, 'ORIGINAL') !== false || strpos($sectionNameUpper, 'OTHER SURVEY') !== false || $sectionNameUpper === 'OOSS') {
                    $headerColor = '#17a2b8'; // Light Blue
                } elseif (strpos($sectionNameUpper, 'SURVEY') !== false && strpos($sectionNameUpper, 'CONTROL') !== false || $sectionNameUpper === 'SCS') {
                    $headerColor = '#28a745'; // Yellow Green
                } elseif (strpos($sectionNameUpper, 'LAND EVALUATION') !== false || $sectionNameUpper === 'LES') {
                    $headerColor = '#6f42c1'; // Purple
                } elseif (strpos($sectionNameUpper, 'ADMIN') !== false) {
                    $headerColor = '#fd7e14'; // Orange
                } elseif (strpos($sectionNameUpper, 'TECHNICAL') !== false) {
                    $headerColor = '#e83e8c'; // Pink
                } elseif (strpos($sectionNameUpper, 'FOREST') !== false) {
                    $headerColor = '#20c997'; // Teal
                } elseif (strpos($sectionNameUpper, 'WATER') !== false) {
                    $headerColor = '#007bff'; // Blue
                }
            @endphp
            <div class="{{ $positionClass }}" data-section="{{ $section }}">
                <div class="section-card h-100">
                    <div class="section-header d-flex justify-content-between align-items-center" style="background-color: {{ $headerColor }}">
                        <h2 class="mb-0 fw-bold">{{ $data['name'] }}</h2>
                        <span class="badge bg-dark waiting-badge" id="waiting_{{ $section }}">Waiting: 0</span>
                    </div>
                    <div class="card-content-area">
                        <div class="queue-info-container">
                            <div class="queue-number mb-3" id="serving_{{ $section }}">---</div>
                            <div class="text-muted status-text" id="status_{{ $section }}">No Queue</div>
                            <div class="mt-2 text-muted status-text" style="font-size: 2.0rem;" id="next_queue_{{ $section }}"></div>
                        </div>
                        <div class="text-muted category-text" id="category_{{ $section }}"></div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    
    <!-- Responsive adjustments for different screen sizes -->
    <style>
        /* Zoom-specific adjustments to maintain consistent card sizing */
        @media all and (min-resolution: .75dppx) and (max-resolution: .75dppx), (min-resolution: 1.25dppx) and (max-resolution: 1.25dppx), (min-resolution: 1.5dppx) and (max-resolution: 1.5dppx), (min-resolution: 2dppx) and (max-resolution: 2dppx) {
            .section-card {
                height: 800px !important;
                min-height: 800px !important;
                flex-basis: 800px;
            }
            
            .card-content-area {
                height: calc(100% - 120px) !important;
            }
            
            .waiting-badge {
                font-size: 2.3rem;
                padding: 22px 32px;
                min-width: 190px;
            }
        }
        
        /* Zoom-responsive waiting badge */
        @media (min-resolution: 1.25dppx), (-webkit-min-device-pixel-ratio: 1.25) {
            .waiting-badge {
                font-size: 2.3rem;
                padding: 22px 32px;
                min-width: 190px;
            }
        }
        
        @media (min-resolution: 1.5dppx), (-webkit-min-device-pixel-ratio: 1.5) {
            .waiting-badge {
                font-size: 2.1rem;
                padding: 20px 30px;
                min-width: 180px;
            }
        }
        
        @media (min-resolution: 2dppx), (-webkit-min-device-pixel-ratio: 2) {
            .waiting-badge {
                font-size: 1.9rem;
                padding: 18px 28px;
                min-width: 170px;
            }
        }
        
        /* Ensure badge visibility on zoom */
        .waiting-badge {
            position: relative;
            z-index: 5;
            will-change: transform;
            transform: translateZ(0);
            backface-visibility: hidden;
        }
        
        /* Prevent text selection issues */
        .waiting-badge {
            user-select: none;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
        }
        @media (max-width: 1200px) {
            .grid-container.two-cards {
                grid-template-columns: repeat(2, 1fr);
                grid-template-rows: repeat(1, 1fr);
                width: 100%;
                max-width: none;
                gap: 25px;
                margin: 0;
                padding: 20px;
            }
            
            .grid-container.three-cards {
                grid-template-columns: repeat(3, 1fr);
                grid-template-rows: repeat(1, 1fr);
                width: 100%;
                max-width: none;
                gap: 25px;
                margin: 0;
                padding: 20px;
            }
            
            .waiting-badge {
                font-size: 2.2rem;
                padding: 20px 30px;
                min-width: 180px;
            }
            
            .grid-container.four-or-more {
                grid-template-columns: repeat(3, 1fr);
                grid-template-rows: repeat(3, 1fr);
                gap: 20px;
            }
            
            .grid-container.four-or-more .card-position-1 { grid-column: 1; grid-row: 1; justify-self: center; align-self: center; }
            .grid-container.four-or-more .card-position-2 { grid-column: 2; grid-row: 1; justify-self: center; align-self: center; }
            .grid-container.four-or-more .card-position-3 { grid-column: 3; grid-row: 1; justify-self: center; align-self: center; }
            .grid-container.four-or-more .card-position-4 { grid-column: 1; grid-row: 2; justify-self: center; align-self: center; }
            .grid-container.four-or-more .card-position-5 { grid-column: 2; grid-row: 2; justify-self: center; align-self: center; }
            .grid-container.four-or-more .card-position-6 { grid-column: 3; grid-row: 2; justify-self: center; align-self: center; }
            .grid-container.four-or-more .card-position-7 { grid-column: 1; grid-row: 3; justify-self: center; align-self: center; }
            .grid-container.four-or-more .card-position-8 { grid-column: 2; grid-row: 3; justify-self: center; align-self: center; }
            .grid-container.four-or-more .card-position-9 { grid-column: 3; grid-row: 3; justify-self: center; align-self: center; }
        }
        
        @media (max-width: 768px) {
            .grid-container.two-cards,
            .grid-container.three-cards,
            .grid-container.four-or-more {
                grid-template-columns: 1fr;
                grid-template-rows: auto;
                gap: 20px;
                padding: 20px;
                margin: 0 auto;
                max-width: none;
                justify-content: center;
            }
            
            .section-card {
                height: auto;
                min-height: 400px;
                min-width: auto;
            }
            
            .waiting-badge {
                font-size: 1.8rem;
                padding: 15px 25px;
                min-width: 150px;
                position: relative;
                z-index: 10;
            }
            
            .grid-container.two-cards .card-position-1,
            .grid-container.two-cards .card-position-2,
            .grid-container.three-cards .card-position-1,
            .grid-container.three-cards .card-position-2,
            .grid-container.three-cards .card-position-3,
            .grid-container.four-or-more .card-position-1,
            .grid-container.four-or-more .card-position-2,
            .grid-container.four-or-more .card-position-3,
            .grid-container.four-or-more .card-position-4,
            .grid-container.four-or-more .card-position-5,
            .grid-container.four-or-more .card-position-6,
            .grid-container.four-or-more .card-position-7,
            .grid-container.four-or-more .card-position-8,
            .grid-container.four-or-more .card-position-9 {
                grid-column: 1;
                grid-row: auto;
                justify-self: center;
                align-self: center;
                width: 100%;
                max-width: 500px;
            }
            
            .section-card {
                min-width: 350px;
                height: 600px;
                min-height: 600px;
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
                min-width: 700px;
                height: 800px;
            }
            
            .section-header {
                font-size: 3.5rem;
            }
            
            .queue-number {
                font-size: 8.5rem;
            }
            
            .waiting-badge {
                font-size: 2.8rem;
                padding: 30px 40px;
                min-width: 220px;
            }
            
            .grid-container.two-cards {
                width: 100%;
                max-width: none;
                margin: 0;
                padding: 30px 50px;
                gap: 60px;
            }
        }
        
        .status-text {
            font-size: 2.8rem;
        }
        
        .category-text {
            font-size: 2.3rem;
            word-break: break-word;
            overflow-wrap: break-word;
            hyphens: auto;
            text-align: center;
            max-width: 100%;
            padding: 0 20px;
            box-sizing: border-box;
        }
    </style>

    <!-- Bootstrap JS -->
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    
    <script>
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
        fetch('{{ route('monitor.queue-data-lobby2') }}')
            .then(response => response.json())
            .then(data => {
                updateQueueDisplay(data);
            })
            .catch(error => console.error('Error loading queue data:', error));
    }

    // Update queue display
    function updateQueueDisplay(data) {
        if (!data.sections) return;
        
        // Track which sections should be visible
        const visibleSections = [];
        
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
            const cardContainer = document.querySelector(`[data-section="${section}"]`);
            
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
                    visibleSections.push(section);
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
                    visibleSections.push(section);
                } else {
                    servingEl.textContent = '---';
                    servingEl.style.color = '#6c757d';
                    statusEl.textContent = 'No Queue';
                    if (nextQueueEl) nextQueueEl.textContent = '';
                    if (categoryEl) categoryEl.textContent = '';
                    // Don't add to visible sections - hide card
                }
            }
        }
        
        // Hide/show cards based on visibility
        hideInactiveCards(visibleSections);
    }
    
    // Format queue number to show only sequential number
    function formatQueueNumber(fullQueueNumber) {
        if (!fullQueueNumber) return '---';
        
        // Try to extract the last number after the last hyphen
        // e.g., "SECSIME NO.R4A-L_SMD-01-009" -> "#9"
        // e.g., "dsds-001" -> "#1"
        const parts = fullQueueNumber.split('-');
        if (parts.length > 0) {
            const lastPart = parts[parts.length - 1];
            const num = parseInt(lastPart.replace(/^0+/, '')); // Remove leading zeros
            if (!isNaN(num)) {
                return '#' + num;
            }
        }
        // Fallback: if no number found, return the original
        return fullQueueNumber;
    }
    
    // Hide cards that don't have active queues
    function hideInactiveCards(visibleSections) {
        const allCards = document.querySelectorAll('[data-section]');
        
        allCards.forEach(card => {
            const section = card.getAttribute('data-section');
            if (visibleSections.includes(section)) {
                card.style.display = 'block';
                card.style.opacity = '1';
                card.style.transform = 'scale(1)';
            } else {
                card.style.display = 'none';
                card.style.opacity = '0';
                card.style.transform = 'scale(0.95)';
            }
        });
        
        // Update grid layout based on visible cards count
        updateGridLayout(visibleSections.length);
    }
    
    // Update grid layout based on number of visible cards
    function updateGridLayout(cardCount) {
        const gridContainer = document.getElementById('queueDisplay');
        if (!gridContainer) return;
        
        // Remove existing layout classes
        gridContainer.classList.remove('two-cards', 'three-cards', 'four-or-more');
        
        // Add appropriate layout class
        if (cardCount === 2) {
            gridContainer.classList.add('two-cards');
        } else if (cardCount === 3) {
            gridContainer.classList.add('three-cards');
        } else if (cardCount >= 4) {
            gridContainer.classList.add('four-or-more');
        }
        
        // If no cards are visible, show a message
        if (cardCount === 0) {
            showNoQueueMessage();
        } else {
            hideNoQueueMessage();
        }
    }
    
    // Show message when no queues are active
    function showNoQueueMessage() {
        const gridContainer = document.getElementById('queueDisplay');
        if (!gridContainer) return;
        
        // Check if message already exists
        if (document.getElementById('no-queue-message')) return;
        
        const message = document.createElement('div');
        message.id = 'no-queue-message';
        message.innerHTML = `
            <div class="text-center py-5">
                <div class="display-4 text-muted mb-3">
                    <i class="bi bi-inbox"></i>
                </div>
                <h2 class="text-muted">No Active Queues</h2>
                <p class="text-muted">Currently no sections have active queue numbers</p>
            </div>
        `;
        message.style.gridColumn = '1 / -1';
        message.style.gridRow = '1';
        
        gridContainer.appendChild(message);
    }
    
    // Hide no queue message
    function hideNoQueueMessage() {
        const message = document.getElementById('no-queue-message');
        if (message) {
            message.remove();
        }
    }

    // Auto-refresh every 5 seconds
    setInterval(loadQueueData, 5000);
    
    // Initial load
    loadQueueData();
    
    // Responsive badge adjustment for zoom levels
    function adjustBadgeForZoom() {
        const badges = document.querySelectorAll('.waiting-badge');
        const zoomLevel = window.devicePixelRatio || 1;
        
        badges.forEach(badge => {
            // Adjust based on zoom level
            if (zoomLevel >= 2) {
                badge.style.fontSize = '1.6rem';
                badge.style.padding = '12px 20px';
                badge.style.minWidth = '140px';
            } else if (zoomLevel >= 1.5) {
                badge.style.fontSize = '1.9rem';
                badge.style.padding = '15px 25px';
                badge.style.minWidth = '160px';
            } else if (zoomLevel >= 1.25) {
                badge.style.fontSize = '2.1rem';
                badge.style.padding = '18px 28px';
                badge.style.minWidth = '180px';
            } else {
                // Reset to default values
                badge.style.fontSize = '';
                badge.style.padding = '';
                badge.style.minWidth = '';
            }
        });
    }
    
    // Adjust badges on load and resize
    window.addEventListener('load', adjustBadgeForZoom);
    window.addEventListener('resize', adjustBadgeForZoom);
    window.addEventListener('zoom', adjustBadgeForZoom);
    </script>
</body>
</html>