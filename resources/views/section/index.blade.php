@extends('layouts.app')

@section('title', 'Section Dashboard')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <h1 class="mb-4">Section Dashboard</h1>
            
            @if($category)
                <div class="card mb-4">
                    <div class="card-header">
                        <h3>{{ $category->name }}</h3>
                        <p class="text-muted mb-0">Code: {{ $category->code }}</p>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="card bg-primary text-white">
                                    <div class="card-body text-center">
                                        <h4 id="waiting-count">0</h4>
                                        <p>Waiting</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-warning text-white">
                                    <div class="card-body text-center">
                                        <h4 id="serving-count">0</h4>
                                        <p>Serving</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-success text-white">
                                    <div class="card-body text-center">
                                        <h4 id="completed-count">0</h4>
                                        <p>Completed</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-secondary text-white">
                                    <div class="card-body text-center">
                                        <h4 id="skipped-count">0</h4>
                                        <p>Skipped</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h4>Currently Serving</h4>
                            </div>
                            <div class="card-body">
                                <div id="currently-serving">
                                    <p class="text-muted">No one is currently being served.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h4>Waiting List</h4>
                            </div>
                            <div class="card-body">
                                <div id="waiting-list">
                                    <p class="text-muted">No one is waiting.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <button id="call-next-btn" class="btn btn-primary btn-lg mr-2">
                        <i class="fas fa-bell"></i> Call Next
                    </button>
                    <button id="complete-btn" class="btn btn-success btn-lg mr-2" disabled>
                        <i class="fas fa-check"></i> Complete
                    </button>
                    <button id="skip-btn" class="btn btn-warning btn-lg mr-2" disabled>
                        <i class="fas fa-forward"></i> Skip
                    </button>
                    <button id="forward-btn" class="btn btn-danger btn-lg" disabled>
                        <i class="fas fa-share"></i> Forward to Admin
                    </button>
                </div>

                <!-- Category Selection Modal -->
                <div class="modal fade" id="categorySelectionModal" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Select Category</h5>
                                <button type="button" class="close" data-dismiss="modal">
                                    <span>&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="form-group">
                                    <label for="category-select">Choose a Category:</label>
                                    <select id="category-select" class="form-control">
                                        <option value="">Loading categories...</option>
                                    </select>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                <button type="button" class="btn btn-primary" id="confirm-category-selection">Confirm</button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Remarks Modal -->
                <div class="modal fade" id="remarksModal" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Add Remarks</h5>
                                <button type="button" class="close" data-dismiss="modal">
                                    <span>&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <textarea id="remarks-text" class="form-control" rows="3" placeholder="Enter remarks..."></textarea>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                <button type="button" class="btn btn-primary" id="save-remarks">Save</button>
                            </div>
                        </div>
                    </div>
                </div>
            @elseif(auth()->user()->isAdmin())
                <div class="card mb-4">
                    <div class="card-header">
                        <h3>Admin Section Dashboard</h3>
                        <p class="text-muted mb-0">Select a category to view details</p>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <h4>Welcome Admin!</h4>
                            <p>You're accessing the section dashboard as an administrator. You can:</p>
                            <ul>
                                <li>View statistics for all categories</li>
                                <li>Monitor section activities</li>
                                <li>Manage category assignments</li>
                            </ul>
                            <p><strong>Note:</strong> To use the full functionality, please assign yourself to a specific category or use the category management section.</p>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-3">
                                <div class="card bg-primary text-white">
                                    <div class="card-body text-center">
                                        <h4 id="waiting-count">N/A</h4>
                                        <p>Waiting</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-warning text-white">
                                    <div class="card-body text-center">
                                        <h4 id="serving-count">N/A</h4>
                                        <p>Serving</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-success text-white">
                                    <div class="card-body text-center">
                                        <h4 id="completed-count">N/A</h4>
                                        <p>Completed</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-secondary text-white">
                                    <div class="card-body text-center">
                                        <h4 id="skipped-count">N/A</h4>
                                        <p>Skipped</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mt-4">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h4>Currently Serving</h4>
                                    </div>
                                    <div class="card-body">
                                        <div id="currently-serving">
                                            <p class="text-muted">Please assign yourself to a category to use section functions.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h4>Waiting List</h4>
                                    </div>
                                    <div class="card-body">
                                        <div id="waiting-list">
                                            <p class="text-muted">Please assign yourself to a category to view waiting list.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <button id="call-next-btn" class="btn btn-primary btn-lg mr-2" disabled>
                                <i class="fas fa-bell"></i> Call Next
                            </button>
                            <button id="complete-btn" class="btn btn-success btn-lg mr-2" disabled>
                                <i class="fas fa-check"></i> Complete
                            </button>
                            <button id="skip-btn" class="btn btn-warning btn-lg mr-2" disabled>
                                <i class="fas fa-forward"></i> Skip
                            </button>
                            <button id="forward-btn" class="btn btn-danger btn-lg" disabled>
                                <i class="fas fa-share"></i> Forward to Admin
                            </button>
                        </div>
                    </div>
                </div>
            @else
                <div class="alert alert-warning">
                    <h4>No Category Assigned</h4>
                    <p>You don't have a category assigned to you. Please contact your administrator.</p>
                </div>
            @endif
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    let currentInquiry = null;
    const categoryId = {{ $category->id ?? 'null' }};
    const isAdmin = {{ auth()->user()->isAdmin() ? 'true' : 'false' }};

    const isSectionStaff = {{ auth()->user()->isSectionStaff() ? 'true' : 'false' }};
    
    if (categoryId || isAdmin || isSectionStaff) {
        // Show loading state
        if (categoryId) {
            document.getElementById('waiting-count').textContent = '...';
            document.getElementById('serving-count').textContent = '...';
            document.getElementById('completed-count').textContent = '...';
            document.getElementById('skipped-count').textContent = '...';
        }
        
        // Load initial data
        setTimeout(() => {
            // For section staff without assigned category, show a category selection
            if (!categoryId && isSectionStaff) {
                // Show message about selecting a category
                document.getElementById('currently-serving').innerHTML = '<p class="text-muted">Please select a category to work with.</p>';
                document.getElementById('waiting-list').innerHTML = '<p class="text-muted">Please select a category to view waiting list.</p>';
                
                // Enable action buttons but they'll prompt for category
                document.getElementById('call-next-btn').disabled = false;
                document.getElementById('complete-btn').disabled = false;
                document.getElementById('skip-btn').disabled = false;
                document.getElementById('forward-btn').disabled = false;
            } else if (categoryId) {
                loadStatistics();
                loadCurrentlyServing();
                loadWaitingList();
                
                // Set up auto-refresh
                setInterval(() => {
                    loadStatistics();
                    loadCurrentlyServing();
                    loadWaitingList();
                }, 5000);

                // Event listeners
                document.getElementById('call-next-btn').addEventListener('click', callNext);
                document.getElementById('complete-btn').addEventListener('click', () => showRemarksModal('complete'));
                document.getElementById('skip-btn').addEventListener('click', () => showRemarksModal('skip'));
                document.getElementById('forward-btn').addEventListener('click', () => showRemarksModal('forward'));
                document.getElementById('save-remarks').addEventListener('click', saveRemarks);
            }
        }, 100);
    } else {
        console.log('No category ID found and not admin or section staff');
    }

    function loadStatistics() {
        if (!categoryId && !isAdmin && isSectionStaff) {
            // For section staff without assigned category, show all statistics
            axios.get('/section/statistics')
                .then(response => {
                    const data = response.data;
                    document.getElementById('waiting-count').textContent = data.waiting;
                    document.getElementById('serving-count').textContent = data.serving;
                    document.getElementById('completed-count').textContent = data.completed;
                    document.getElementById('skipped-count').textContent = data.skipped;
                })
                .catch(error => console.error('Error loading statistics:', error));
        } else {
            axios.get('/section/statistics')
                .then(response => {
                    const data = response.data;
                    document.getElementById('waiting-count').textContent = data.waiting;
                    document.getElementById('serving-count').textContent = data.serving;
                    document.getElementById('completed-count').textContent = data.completed;
                    document.getElementById('skipped-count').textContent = data.skipped;
                })
                .catch(error => console.error('Error loading statistics:', error));
        }
    }

    function loadCurrentlyServing() {
        if (!categoryId && !isAdmin && isSectionStaff) {
            // For section staff without assigned category, load from all categories
            axios.get('/section/currently-serving')
                .then(response => {
                    const data = response.data;
                    const container = document.getElementById('currently-serving');
                    if (data) {
                        container.innerHTML = `
                            <div class="alert alert-info">
                                <h5>${data.queue_number}</h5>
                                <p>${data.name} (${data.category?.name || 'Unknown Category'})</p>
                                <small>Serving since: ${new Date(data.served_at).toLocaleTimeString()}</small>
                            </div>
                        `;
                        currentInquiry = data;
                        enableButtons();
                    } else {
                        container.innerHTML = '<p class="text-muted">No one is currently being served.</p>';
                        currentInquiry = null;
                        disableButtons();
                    }
                })
                .catch(error => console.error('Error loading currently serving:', error));
        } else {
            axios.get('/section/currently-serving')
                .then(response => {
                    const data = response.data;
                    const container = document.getElementById('currently-serving');
                    if (data) {
                        container.innerHTML = `
                            <div class="alert alert-info">
                                <h5>${data.queue_number}</h5>
                                <p>${data.name}</p>
                                <small>Serving since: ${new Date(data.served_at).toLocaleTimeString()}</small>
                            </div>
                        `;
                        currentInquiry = data;
                        enableButtons();
                    } else {
                        container.innerHTML = '<p class="text-muted">No one is currently being served.</p>';
                        currentInquiry = null;
                        disableButtons();
                    }
                })
                .catch(error => console.error('Error loading currently serving:', error));
        }
    }

    function loadWaitingList() {
        if (!categoryId && !isAdmin && isSectionStaff) {
            // For section staff without assigned category, load from all categories
            axios.get('/section/waiting-list')
                .then(response => {
                    const data = response.data;
                    const container = document.getElementById('waiting-list');
                    if (data.length > 0) {
                        container.innerHTML = data.map(inquiry => `
                            <div class="alert alert-secondary">
                                <strong>${inquiry.queue_number}</strong> - ${inquiry.name} (${inquiry.category?.name || 'Unknown Category'})
                                <br><small>Arrived: ${new Date(inquiry.created_at).toLocaleTimeString()}</small>
                            </div>
                        `).join('');
                    } else {
                        container.innerHTML = '<p class="text-muted">No one is waiting.</p>';
                    }
                })
                .catch(error => console.error('Error loading waiting list:', error));
        } else {
            axios.get('/section/waiting-list')
                .then(response => {
                    const data = response.data;
                    const container = document.getElementById('waiting-list');
                    if (data.length > 0) {
                        container.innerHTML = data.map(inquiry => `
                            <div class="alert alert-secondary">
                                <strong>${inquiry.queue_number}</strong> - ${inquiry.name}
                                <br><small>Arrived: ${new Date(inquiry.created_at).toLocaleTimeString()}</small>
                            </div>
                        `).join('');
                    } else {
                        container.innerHTML = '<p class="text-muted">No one is waiting.</p>';
                    }
                })
                .catch(error => console.error('Error loading waiting list:', error));
        }
    }

    function callNext() {
        if (!categoryId && !isAdmin && isSectionStaff) {
            // For section staff without assigned category, show category selection modal
            loadCategories().then(() => {
                $('#categorySelectionModal').modal('show');
                document.getElementById('confirm-category-selection').onclick = function() {
                    const selectedCategory = document.getElementById('category-select').value;
                    if (!selectedCategory) {
                        alert('Please select a category first');
                        return;
                    }
                    
                    axios.post('/section/call-next', { category_id: selectedCategory })
                        .then(response => {
                            const data = response.data;
                            if (data.success) {
                                alert(`Calling ${data.inquiry.queue_number} - ${data.inquiry.name}`);
                                loadCurrentlyServing();
                                loadWaitingList();
                                loadStatistics();
                            } else {
                                alert(data.error);
                            }
                        })
                        .catch(error => {
                            console.error('Error calling next:', error);
                            alert('Error calling next inquiry');
                        });
                    
                    $('#categorySelectionModal').modal('hide');
                };
            });
        } else {
            axios.post('/section/call-next')
                .then(response => {
                    const data = response.data;
                    if (data.success) {
                        alert(`Calling ${data.inquiry.queue_number} - ${data.inquiry.name}`);
                        loadCurrentlyServing();
                        loadWaitingList();
                        loadStatistics();
                    } else {
                        alert(data.error);
                    }
                })
                .catch(error => {
                    console.error('Error calling next:', error);
                    alert('Error calling next inquiry');
                });
        }
    }

    function showRemarksModal(action) {
        document.getElementById('remarks-text').value = '';
        document.getElementById('remarksModal').dataset.action = action;
        $('#remarksModal').modal('show');
    }

    function saveRemarks() {
        const action = document.getElementById('remarksModal').dataset.action;
        const remarks = document.getElementById('remarks-text').value;
        
        // If user is section staff without assigned category, prompt for category
        if (!categoryId && !isAdmin && isSectionStaff) {
            const selectedCategory = prompt('Enter category ID for this action:');
            if (!selectedCategory) return;
            
            axios.post(`/section/${action}`, { 
                remarks: remarks,
                category_id: selectedCategory
            })
            .then(response => {
                const data = response.data;
                if (data.success) {
                    alert(data.message);
                    $('#remarksModal').modal('hide');
                    loadCurrentlyServing();
                    loadWaitingList();
                    loadStatistics();
                } else {
                    alert(data.error);
                }
            })
            .catch(error => {
                console.error('Error saving remarks:', error);
                alert('Error saving remarks');
            });
        } else {
            axios.post(`/section/${action}`, { remarks: remarks })
                .then(response => {
                    const data = response.data;
                    if (data.success) {
                        alert(data.message);
                        $('#remarksModal').modal('hide');
                        loadCurrentlyServing();
                        loadWaitingList();
                        loadStatistics();
                    } else {
                        alert(data.error);
                    }
                })
                .catch(error => {
                    console.error('Error saving remarks:', error);
                    alert('Error saving remarks');
                });
        }
    }

    function enableButtons() {
        document.getElementById('complete-btn').disabled = false;
        document.getElementById('skip-btn').disabled = false;
        document.getElementById('forward-btn').disabled = false;
    }

    function disableButtons() {
        document.getElementById('complete-btn').disabled = true;
        document.getElementById('skip-btn').disabled = true;
        document.getElementById('forward-btn').disabled = true;
    }
    async function loadCategories() {
        try {
            const response = await axios.get('/api/categories'); // Assuming you have an API endpoint for categories
            const categories = response.data;
            const selectElement = document.getElementById('category-select');
            
            // Clear existing options
            selectElement.innerHTML = '<option value="">Select a Category</option>';
            
            // Add categories to the select element
            categories.forEach(category => {
                const option = document.createElement('option');
                option.value = category.id;
                option.textContent = `${category.name} (${category.code})`;
                selectElement.appendChild(option);
            });
        } catch (error) {
            console.error('Error loading categories:', error);
            const selectElement = document.getElementById('category-select');
            selectElement.innerHTML = '<option value="">Error loading categories</option>';
        }
    }
    
    // Also add a fallback if the API endpoint doesn't exist
    async function loadCategoriesFallback() {
        try {
            // This would need to be implemented as an API endpoint
            // For now, we'll just return a promise that resolves
            return Promise.resolve();
        } catch (error) {
            console.error('Error in fallback category loading:', error);
        }
    }
    
    // Update the saveRemarks function to handle section staff without assigned categories
    function saveRemarks() {
        const action = document.getElementById('remarksModal').dataset.action;
        const remarks = document.getElementById('remarks-text').value;
        
        // If user is section staff without assigned category, use category selection
        if (!categoryId && !isAdmin && isSectionStaff) {
            loadCategories().then(() => {
                $('#categorySelectionModal').modal('show');
                document.getElementById('confirm-category-selection').onclick = function() {
                    const selectedCategory = document.getElementById('category-select').value;
                    if (!selectedCategory) {
                        alert('Please select a category first');
                        return;
                    }
                    
                    axios.post(`/section/${action}`, { 
                        remarks: remarks,
                        category_id: selectedCategory
                    })
                    .then(response => {
                        const data = response.data;
                        if (data.success) {
                            alert(data.message);
                            $('#remarksModal').modal('hide');
                            $('#categorySelectionModal').modal('hide');
                            loadCurrentlyServing();
                            loadWaitingList();
                            loadStatistics();
                        } else {
                            alert(data.error);
                        }
                    })
                    .catch(error => {
                        console.error('Error saving remarks:', error);
                        alert('Error saving remarks');
                    });
                };
            });
        } else {
            axios.post(`/section/${action}`, { remarks: remarks })
                .then(response => {
                    const data = response.data;
                    if (data.success) {
                        alert(data.message);
                        $('#remarksModal').modal('hide');
                        loadCurrentlyServing();
                        loadWaitingList();
                        loadStatistics();
                    } else {
                        alert(data.error);
                    }
                })
                .catch(error => {
                    console.error('Error saving remarks:', error);
                    alert('Error saving remarks');
                });
        }
    }
</script>
@endpush