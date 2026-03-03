<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Debug Section Dashboard</title>
    <style>
        body { font-family: monospace; padding: 20px; background: #f5f5f5; }
        .test-item { background: white; padding: 15px; margin: 10px 0; border-radius: 5px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .success { color: green; }
        .error { color: red; }
        .info { color: blue; }
        pre { background: #f0f0f0; padding: 10px; overflow: auto; }
    </style>
</head>
<body>
    <h1>Section Dashboard Debug Test</h1>
    
    <div class="test-item">
        <h3>Test 1: Check Authentication</h3>
        <div id="auth-test">Testing...</div>
    </div>
    
    <div class="test-item">
        <h3>Test 2: Load Categories API</h3>
        <div id="categories-test">Testing...</div>
    </div>
    
    <div class="test-item">
        <h3>Test 3: Load Statistics API (All)</h3>
        <div id="stats-all-test">Testing...</div>
    </div>
    
    <div class="test-item">
        <h3>Test 4: Load Statistics for Category ID=1</h3>
        <div id="stats-cat1-test">Testing...</div>
    </div>
    
    <div class="test-item">
        <h3>Test 5: Simulate Admin Overview Loading</h3>
        <div id="admin-overview-test">
            <p>Loading...</p>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        // Test 1: Check if user is authenticated
        axios.get('/section/test')
            .then(response => {
                document.getElementById('auth-test').innerHTML = `
                    <div class="success">✓ User authenticated</div>
                    <pre>${JSON.stringify(response.data, null, 2)}</pre>
                `;
                
                const userData = response.data;
                console.log('User Data:', userData);
                
                // Test 2: Load categories
                return axios.get('/api/categories');
            })
            .then(response => {
                document.getElementById('categories-test').innerHTML = `
                    <div class="success">✓ Categories loaded: ${response.data.length} found</div>
                    <pre>${JSON.stringify(response.data.map(c => ({id: c.id, name: c.name, code: c.code})), null, 2)}</pre>
                `;
                
                // Test 3: Load statistics for all
                return axios.get('/section/statistics');
            })
            .then(response => {
                document.getElementById('stats-all-test').innerHTML = `
                    <div class="success">✓ Total statistics loaded</div>
                    <pre>${JSON.stringify(response.data, null, 2)}</pre>
                `;
                
                // Test 4: Load statistics for category 1
                return axios.get('/section/statistics', { params: { category_id: 1 } });
            })
            .then(response => {
                document.getElementById('stats-cat1-test').innerHTML = `
                    <div class="success">✓ Category 1 statistics loaded</div>
                    <pre>${JSON.stringify(response.data, null, 2)}</pre>
                `;
                
                // Test 5: Simulate admin overview
                return axios.get('/api/categories');
            })
            .then(response => {
                const categories = response.data;
                const container = document.getElementById('admin-overview-test');
                
                if (categories.length === 0) {
                    container.innerHTML = '<div class="error">✗ No categories found</div>';
                    return;
                }
                
                container.innerHTML = `<p>Loading statistics for ${categories.length} categories...</p>`;
                
                let html = '<table border="1" cellpadding="5" style="border-collapse: collapse; width: 100%; margin-top: 10px;">';
                html += '<tr><th>ID</th><th>Name</th><th>Code</th><th>Waiting</th><th>Serving</th><th>Completed</th></tr>';
                
                let loadedCount = 0;
                
                // Load sequentially
                const loadNext = (index) => {
                    if (index >= categories.length) {
                        html += '</table>';
                        container.innerHTML = html;
                        console.log('✓ Admin overview simulation complete');
                        return;
                    }
                    
                    const cat = categories[index];
                    axios.get('/section/statistics', { params: { category_id: cat.id } })
                        .then(statsRes => {
                            const stats = statsRes.data;
                            html += `<tr>
                                <td>${cat.id}</td>
                                <td>${cat.name}</td>
                                <td>${cat.code}</td>
                                <td>${stats.waiting}</td>
                                <td>${stats.serving}</td>
                                <td>${stats.completed}</td>
                            </tr>`;
                            
                            loadedCount++;
                            container.innerHTML = `<p>Loaded ${loadedCount}/${categories.length}...</p>` + html;
                            
                            loadNext(index + 1);
                        })
                        .catch(error => {
                            console.error(`Error loading cat ${cat.id}:`, error);
                            html += `<tr>
                                <td>${cat.id}</td>
                                <td>${cat.name}</td>
                                <td>${cat.code}</td>
                                <td colspan="3" class="error">Error loading</td>
                            </tr>`;
                            
                            loadedCount++;
                            loadNext(index + 1);
                        });
                };
                
                loadNext(0);
            })
            .catch(error => {
                console.error('Test failed:', error);
                
                if (error.response && error.response.status === 401) {
                    document.getElementById('auth-test').innerHTML = `
                        <div class="error">✗ Not authenticated - Please login first</div>
                        <p><a href="/login" class="button">Go to Login</a></p>
                    `;
                } else {
                    document.getElementById('categories-test').innerHTML = `
                        <div class="error">✗ Error: ${error.message}</div>
                        <pre>${JSON.stringify(error.response?.data || error.message, null, 2)}</pre>
                    `;
                }
            });
    </script>
</body>
</html>
