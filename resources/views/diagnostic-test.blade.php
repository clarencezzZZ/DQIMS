<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Section Dashboard Diagnostic Test</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .test-result { margin: 20px 0; padding: 15px; border-radius: 5px; }
        .test-pass { background-color: #d4edda; border: 1px solid #c3e6cb; }
        .test-fail { background-color: #f8d7da; border: 1px solid #f5c6cb; }
        .test-running { background-color: #fff3cd; border: 1px solid #ffeeba; }
        .log-entry { font-family: monospace; font-size: 12px; margin: 5px 0; padding: 5px; background: #f8f9fa; }
        .error-log { color: #dc3545; }
        .success-log { color: #28a745; }
        .info-log { color: #007bff; }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h1><i class="bi bi-bug"></i> Section Dashboard Diagnostic Tool</h1>
        <p class="lead">This tool will test each component to find why the dashboard is stuck on "Loading..."</p>
        
        <button class="btn btn-primary btn-lg" onclick="runAllTests()">
            <i class="bi bi-play-circle"></i> Run All Tests
        </button>
        
        <div id="test-results"></div>
        
        <div class="mt-4">
            <h3>Detailed Logs:</h3>
            <div id="logs" style="max-height: 500px; overflow-y: auto; background: #000; color: #0f0; padding: 15px; border-radius: 5px;"></div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        const logs = [];
        
        function log(message, type = 'info') {
            const timestamp = new Date().toLocaleTimeString();
            const logEntry = `[${timestamp}] ${message}`;
            logs.push(logEntry);
            
            const logDiv = document.getElementById('logs');
            const entry = document.createElement('div');
            entry.className = `log-entry ${type}-log`;
            entry.textContent = logEntry;
            logDiv.appendChild(entry);
            logDiv.scrollTop = logDiv.scrollHeight;
            
            console.log(logEntry);
        }
        
        function addTestResult(title, status, details, data = null) {
            const resultsDiv = document.getElementById('test-results');
            const testDiv = document.createElement('div');
            testDiv.className = `test-result test-${status}`;
            
            let content = `<h5>${title}</h5>`;
            content += `<p>${details}</p>`;
            
            if (data) {
                content += `<pre>${JSON.stringify(data, null, 2)}</pre>`;
            }
            
            testDiv.innerHTML = content;
            resultsDiv.appendChild(testDiv);
        }
        
        async function runAllTests() {
            document.getElementById('test-results').innerHTML = '';
            document.getElementById('logs').innerHTML = '';
            logs.length = 0;
            
            log('Starting diagnostic tests...', 'info');
            
            // Test 1: Check if axios is loaded
            try {
                if (typeof axios === 'undefined') {
                    throw new Error('Axios library not loaded');
                }
                addTestResult('Test 1: Axios Library', 'pass', 'Axios is properly loaded');
                log('Axios library loaded successfully', 'success');
            } catch (error) {
                addTestResult('Test 1: Axios Library', 'fail', error.message);
                log('Axios not loaded: ' + error.message, 'error');
                return;
            }
            
            // Test 2: Test basic connectivity
            try {
                log('Testing basic connectivity...', 'info');
                const response = await axios.get('/section/test');
                
                if (response.status === 200) {
                    addTestResult('Test 2: Basic Connectivity', 'pass', 'Can reach Laravel backend', response.data);
                    log('Basic connectivity test passed', 'success');
                } else {
                    throw new Error(`HTTP Status: ${response.status}`);
                }
            } catch (error) {
                addTestResult('Test 2: Basic Connectivity', 'fail', 
                    `Failed to connect: ${error.message}`, 
                    { response: error.response?.data, status: error.response?.status });
                log('Basic connectivity failed: ' + error.message, 'error');
                return;
            }
            
            // Test 3: Load categories API
            try {
                log('Testing /api/categories endpoint...', 'info');
                const startTime = Date.now();
                const response = await axios.get('/api/categories');
                const duration = Date.now() - startTime;
                
                if (!Array.isArray(response.data)) {
                    throw new Error('Response is not an array');
                }
                
                addTestResult(
                    'Test 3: Categories API', 
                    'pass', 
                    `Loaded ${response.data.length} categories in ${duration}ms`,
                    { count: response.data.length, categories: response.data.map(c => ({ id: c.id, name: c.name, code: c.code })) }
                );
                log(`Categories API loaded ${response.data.length} categories in ${duration}ms`, 'success');
                
                // Test 4: Load statistics for FIRST category only
                if (response.data.length > 0) {
                    const firstCategory = response.data[0];
                    try {
                        log(`Testing /section/statistics for category ID ${firstCategory.id}...`, 'info');
                        const statsStart = Date.now();
                        const statsResponse = await axios.get('/section/statistics', { 
                            params: { category_id: firstCategory.id },
                            timeout: 5000
                        });
                        const statsDuration = Date.now() - statsStart;
                        
                        addTestResult(
                            'Test 4: Statistics API (First Category)', 
                            'pass', 
                            `Loaded stats for ${firstCategory.name} in ${statsDuration}ms`,
                            statsResponse.data
                        );
                        log(`Statistics API working for ${firstCategory.name}`, 'success');
                        
                        // Test 5: Try loading stats for ALL categories sequentially
                        log('Testing sequential stats loading for ALL categories...', 'info');
                        const allStatsStart = Date.now();
                        let successCount = 0;
                        let failCount = 0;
                        const allResults = [];
                        
                        for (let i = 0; i < response.data.length; i++) {
                            const cat = response.data[i];
                            try {
                                const catStart = Date.now();
                                const catStats = await axios.get('/section/statistics', { 
                                    params: { category_id: cat.id },
                                    timeout: 5000
                                });
                                const catDuration = Date.now() - catStart;
                                
                                successCount++;
                                allResults.push({ name: cat.name, success: true, time: catDuration });
                                log(`✓ [${i+1}/${response.data.length}] ${cat.name} (${catDuration}ms)`, 'success');
                            } catch (error) {
                                failCount++;
                                allResults.push({ name: cat.name, success: false, error: error.message });
                                log(`✗ [${i+1}/${response.data.length}] ${cat.name}: ${error.message}`, 'error');
                            }
                            
                            // Add small delay between requests
                            await new Promise(resolve => setTimeout(resolve, 100));
                        }
                        
                        const allStatsDuration = Date.now() - allStatsStart;
                        
                        if (failCount === 0) {
                            addTestResult(
                                'Test 5: All Categories Stats (Sequential)', 
                                'pass', 
                                `Successfully loaded all ${successCount} categories in ${allStatsDuration}ms`,
                                { results: allResults, total: response.data.length }
                            );
                            log(`All categories loaded successfully!`, 'success');
                            
                            // FINAL TEST: Simulate the actual loadAdminOverview function
                            log('Simulating actual dashboard load...', 'info');
                            simulateDashboardLoad(response.data);
                        } else {
                            addTestResult(
                                'Test 5: All Categories Stats (Sequential)', 
                                'fail', 
                                `Loaded ${successCount}/${response.data.length} categories. ${failCount} failed.`,
                                { results: allResults }
                            );
                            log(`${failCount} categories failed to load`, 'error');
                        }
                    } catch (error) {
                        addTestResult(
                            'Test 4: Statistics API (First Category)', 
                            'fail', 
                            `Failed to load stats for ${firstCategory.name}: ${error.message}`,
                            { response: error.response?.data, status: error.response?.status }
                        );
                        log('Statistics API test failed: ' + error.message, 'error');
                    }
                }
            } catch (error) {
                addTestResult('Test 3: Categories API', 'fail', error.message, 
                    { response: error.response?.data, status: error.response?.status });
                log('Categories API failed: ' + error.message, 'error');
            }
        }
        
        function simulateDashboardLoad(categories) {
            log('=== SIMULATING DASHBOARD LOAD ===', 'info');
            
            const tbody = document.createElement('tbody');
            let html = '';
            let loadedCount = 0;
            
            log(`Starting to load ${categories.length} categories...`, 'info');
            
            const loadNext = (index) => {
                if (index >= categories.length) {
                    log(`✅ SUCCESS: All ${categories.length} categories loaded!`, 'success');
                    log('Final HTML length: ' + html.length, 'info');
                    
                    addTestResult(
                        '🎉 FINAL TEST: Dashboard Simulation', 
                        'pass', 
                        `Successfully simulated loading all ${categories.length} categories!`,
                        { htmlLength: html.length, loadedCount }
                    );
                    
                    document.getElementById('test-results').innerHTML += `
                        <div class="alert alert-success mt-3">
                            <strong>✅ DIAGNOSIS COMPLETE!</strong><br>
                            The loading mechanism works correctly in isolation.<br>
                            The issue might be with the actual page's JavaScript execution context or timing.
                        </div>
                    `;
                    
                    return;
                }
                
                const cat = categories[index];
                log(`Loading [${index + 1}/${categories.length}]: ${cat.name} (ID: ${cat.id})`, 'info');
                
                axios.get('/section/statistics', { 
                    params: { category_id: cat.id },
                    timeout: 8000
                })
                .then(statsRes => {
                    const stats = statsRes.data;
                    log(`✅ Loaded: ${cat.name} - W:${stats.waiting}, S:${stats.serving}, C:${stats.completed}`, 'success');
                    
                    html += `<tr><td>${cat.name}</td><td>${stats.waiting}</td></tr>`;
                    loadedCount++;
                    
                    setTimeout(() => loadNext(index + 1), 100);
                })
                .catch(error => {
                    log(`❌ Failed: ${cat.name} - ${error.message}`, 'error');
                    
                    html += `<tr><td>${cat.name}</td><td>Error</td></tr>`;
                    loadedCount++;
                    
                    setTimeout(() => loadNext(index + 1), 100);
                });
            };
            
            loadNext(0);
        }
        
        // Auto-run tests on page load
        window.addEventListener('DOMContentLoaded', () => {
            log('Page loaded. Click "Run All Tests" to start diagnostics.', 'info');
            addTestResult('Ready', 'info', 'Click the "Run All Tests" button above to start diagnosis');
        });
    </script>
</body>
</html>
