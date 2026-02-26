<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lobby Monitor - SCS & LES</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
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
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            transition: transform 0.3s ease;
            margin: 0 auto;
            max-width: 800px;
        }
        .section-card:hover {
            transform: scale(1.02);
        }
        .section-header {
            padding: 25px;
            color: white;
            font-weight: bold;
        }
        .queue-number {
            font-size: 4.5rem;
            font-weight: bold;
            color: #1a5f2a;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
        }
        .waiting-badge {
            font-size: 1.8rem;
            padding: 12px 25px;
            border-radius: 50px;
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
        .section-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 70vh;
        }
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
                            <p class="mb-0 opacity-75">Queueing & Inquiry Management System - SCS & LES</p>
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

    <!-- Queue Status Cards - Side by Side Layout -->
    <div class="container-fluid py-5">
        <div class="row g-4 justify-content-center">
            @php
                $sortedSections = ['SCS', 'LES'];
                $sectionDataOrdered = [];
                foreach($sortedSections as $sectionName) {
                    if(isset($sectionData[$sectionName])) {
                        $sectionDataOrdered[$sectionName] = $sectionData[$sectionName];
                    }
                }
            @endphp
            
            @foreach($sectionDataOrdered as $section => $data)
            <div class="col-12 col-md-6">
                <div class="section-container">
                    <div class="section-card w-100">
                        <div class="section-header d-flex justify-content-between align-items-center" style="background-color: {{ $data['color'] }}">
                            <h2 class="mb-0 fw-bold">{{ $section }}</h2>
                            <span class="badge bg-dark waiting-badge" id="waiting_{{ $section }}">Waiting: 0</span>
                        </div>
                        <div class="p-5 text-center">
                            <div class="queue-number mb-4" id="serving_{{ $section }}">---</div>
                            <div class="text-muted fs-4" id="status_{{ $section }}">No Queue</div>
                            <div class="mt-4 text-muted fs-5" id="category_{{ $section }}"></div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
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
        fetch('{{ route('monitor.queue-data-lobby1') }}')
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
            
            if (servingEl && statusEl) {
                if (info.now_serving) {
                    servingEl.textContent = info.now_serving;
                    servingEl.style.color = '#1a5f2a';
                    statusEl.textContent = 'Now Serving';
                    if (categoryEl) categoryEl.textContent = info.now_serving_category || '';
                } else if (info.latest_waiting) {
                    servingEl.textContent = info.latest_waiting;
                    servingEl.style.color = '#f39c12';
                    statusEl.textContent = 'Next in Queue';
                    if (categoryEl) categoryEl.textContent = info.latest_waiting_category || '';
                } else {
                    servingEl.textContent = '---';
                    servingEl.style.color = '#6c757d';
                    statusEl.textContent = 'No Queue';
                    if (categoryEl) categoryEl.textContent = '';
                }
            }
        }
    }

    // Auto-refresh every 5 seconds
    setInterval(loadQueueData, 5000);
    
    // Initial load
    loadQueueData();
    </script>
</body>
</html>