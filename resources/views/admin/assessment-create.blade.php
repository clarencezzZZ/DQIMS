@extends('layouts.app')

@section('title', 'Create Assessment')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="mb-1"><i class="bi bi-file-earmark-plus text-success"></i> Create Assessment</h2>
                    <p class="text-muted mb-0">Create assessment for completed inquiry</p>
                </div>
                <a href="{{ route('admin.inquiries') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Back to Inquiries
                </a>
            </div>

            <!-- Inquiry Details Card -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="bi bi-info-circle"></i> Inquiry Details</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td class="text-muted">Queue Number:</td>
                                    <td class="fw-bold">{{ $inquiry->queue_number }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Client Name:</td>
                                    <td class="fw-bold">{{ $inquiry->guest_name }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Contact:</td>
                                    <td>{{ $inquiry->address ?? 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td class="text-muted">Category:</td>
                                    <td>
                                        @if($inquiry->category)
                                            <span class="badge" style="background-color: {{ $inquiry->category->color }}">
                                                {{ $inquiry->category->name }}
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Date:</td>
                                    <td>{{ $inquiry->created_at->format('M d, Y h:i A') }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Purpose:</td>
                                    <td>{{ $inquiry->purpose ?? 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Assessment Form -->
            <div class="card shadow">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="bi bi-calculator"></i> Assessment Details</h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('admin.assessments.store', $inquiry) }}" method="POST">
                        @csrf
                        
                        <!-- Fees -->
                        <div class="mb-4">
                            <label for="fees" class="form-label fw-bold">
                                <i class="bi bi-currency-dollar text-success"></i> Assessment Fees (₱) <span class="text-danger">*</span>
                            </label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text">₱</span>
                                <input type="number" step="0.01" min="0" 
                                       class="form-control @error('fees') is-invalid @enderror" 
                                       id="fees" name="fees" placeholder="0.00" 
                                       value="{{ old('fees') }}" required>
                            </div>
                            @error('fees')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Enter the total assessment fees in Philippine Peso</div>
                        </div>

                        <!-- Remarks -->
                        <div class="mb-4">
                            <label for="remarks" class="form-label fw-bold">
                                <i class="bi bi-chat-text text-success"></i> Remarks / Notes
                            </label>
                            <textarea class="form-control @error('remarks') is-invalid @enderror" 
                                      id="remarks" name="remarks" rows="4" 
                                      placeholder="Additional notes, requirements, or remarks...">{{ old('remarks') }}</textarea>
                            @error('remarks')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Summary Preview -->
                        <div class="alert alert-light border mb-4">
                            <h6 class="fw-bold mb-3"><i class="bi bi-receipt"></i> Assessment Summary</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="mb-1"><span class="text-muted">Client:</span> {{ $inquiry->guest_name }}</p>
                                    <p class="mb-1"><span class="text-muted">Queue #:</span> {{ $inquiry->queue_number }}</p>
                                </div>
                                <div class="col-md-6 text-md-end">
                                    <p class="mb-1"><span class="text-muted">Category:</span> {{ $inquiry->category->name ?? 'N/A' }}</p>
                                    <p class="mb-1"><span class="text-muted">Date:</span> {{ date('M d, Y') }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Officer incharge Selection -->
                        <div class="mb-4">
                            <label for="officer_in_charge" class="form-label fw-bold">
                                <i class="bi bi-person-badge text-success"></i> Officer incharge
                            </label>
                            <select class="form-select @error('officer_in_charge') is-invalid @enderror" 
                                    id="officer_in_charge" name="officer_in_charge" required>
                                <option value="">-- Select Officer incharge --</option>
                                <option value="lota">Mr. Stanley M. Lota</option>
                            </select>
                            @error('officer_in_charge')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Submit Buttons -->
                        <div class="d-flex gap-3">
                            <button type="submit" class="btn btn-success btn-lg flex-fill">
                                <i class="bi bi-check-circle"></i> Create Assessment
                            </button>
                            <a href="{{ route('admin.inquiries') }}" class="btn btn-outline-secondary btn-lg">
                                <i class="bi bi-x-circle"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Format fees input
    document.getElementById('fees').addEventListener('blur', function() {
        if (this.value) {
            this.value = parseFloat(this.value).toFixed(2);
        }
    });


</script>
@endsection
