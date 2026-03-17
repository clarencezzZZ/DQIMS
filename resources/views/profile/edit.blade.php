@extends('layouts.app')

@section('title', 'Edit Profile')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1"><i class="bi bi-person-gear text-primary"></i> Edit Profile</h2>
                    <p class="text-muted mb-0">Update your account information</p>
                </div>
                <a href="{{ route('admin.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Back to Dashboard
                </a>
            </div>
        </div>
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Error Messages -->
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle"></i> 
            <strong>Oops! Something went wrong.</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <!-- Profile Picture & Quick Actions -->
        <div class="col-lg-4 mb-4">
            <!-- Profile Picture Card -->
            <div class="card shadow-sm text-center">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="bi bi-person-badge"></i> Profile Picture</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" id="profilePictureForm">
                        @csrf
                        @method('PUT')
                        
                        <!-- Hidden required fields -->
                        <input type="hidden" name="name" value="{{ $user->name }}">
                        <input type="hidden" name="username" value="{{ $user->username }}">
                        <input type="hidden" name="email" value="{{ $user->email }}">
                        
                        <div class="mb-3">
                            <div class="position-relative d-inline-block">
                                @if($user->profile_picture && file_exists(public_path('uploads/profiles/' . $user->profile_picture)))
                                    <img src="{{ asset('uploads/profiles/' . $user->profile_picture) }}" 
                                         alt="Profile Picture" 
                                         class="rounded-circle img-thumbnail mb-3"
                                         style="width: 200px; height: 200px; object-fit: cover;">
                                @else
                                    <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center mb-3 mx-auto" 
                                         style="width: 200px; height: 200px; font-size: 5rem;">
                                        <i class="bi bi-person-circle"></i>
                                    </div>
                                @endif
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="profile_picture" class="form-label fw-bold">
                                <i class="bi bi-camera"></i> Upload New Picture
                            </label>
                            <input type="file" 
                                   class="form-control @error('profile_picture') is-invalid @enderror" 
                                   id="profile_picture" 
                                   name="profile_picture"
                                   accept="image/*"
                                   onchange="previewImage(this)">
                            @error('profile_picture')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text mt-1">Max size: 10MB. Supports all image formats (JPG, PNG, GIF, JFIF, WEBP, BMP, SVG, HEIC).</div>
                        </div>
                        
                        <div id="imagePreview" class="mb-3 d-none">
                            <p class="text-muted mb-2"><small>Preview:</small></p>
                            <img id="previewImg" src="" alt="Preview" class="img-thumbnail" style="max-width: 100%;">
                        </div>
                        
                        <button type="submit" class="btn btn-success w-100">
                            <i class="bi bi-upload"></i> Update Picture
                        </button>
                    </form>
                </div>
            </div>
            
            <!-- Profile Summary Card -->
            <div class="card shadow-sm mt-3">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="bi bi-info-circle"></i> Profile Summary</h5>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span><i class="bi bi-person-badge text-primary"></i> Username</span>
                            <strong>{{ $user->username }}</strong>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span><i class="bi bi-envelope text-primary"></i> Email</span>
                            <small>{{ $user->email }}</small>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span><i class="bi bi-shield-check text-primary"></i> Role</span>
                            <span class="badge bg-primary">{{ ucfirst(str_replace('_', ' ', $user->role)) }}</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span><i class="bi bi-check-circle text-primary"></i> Status</span>
                            <span class="badge {{ $user->is_active ? 'bg-success' : 'bg-secondary' }}">
                                {{ $user->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Quick Stats -->
            @if($user->processedAssessments()->count() > 0 || $user->servedInquiries()->count() > 0)
            <div class="card shadow-sm mt-3">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="bi bi-graph-up"></i> Your Statistics</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        @if($user->processedAssessments()->count() > 0)
                        <div class="col-6">
                            <div class="text-center p-2 bg-light rounded">
                                <h3 class="mb-0 text-success">{{ $user->processedAssessments()->count() }}</h3>
                                <small class="text-muted">Assessments</small>
                            </div>
                        </div>
                        @endif
                        @if($user->servedInquiries()->count() > 0)
                        <div class="col-6">
                            <div class="text-center p-2 bg-light rounded">
                                <h3 class="mb-0 text-primary">{{ $user->servedInquiries()->count() }}</h3>
                                <small class="text-muted">Served</small>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif
        </div>
        <!-- Profile Information Card -->
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-person-badge"></i> Account Information</h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('profile.update') }}" method="POST" class="needs-validation" novalidate>
                        @csrf
                        @method('PUT')
                        
                        <!-- Name Field -->
                        <div class="mb-3">
                            <label for="name" class="form-label fw-bold">
                                <i class="bi bi-person"></i> Full Name
                            </label>
                            <input type="text" 
                                   class="form-control form-control-lg @error('name') is-invalid @enderror" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name', $user->name) }}" 
                                   required
                                   placeholder="Enter your full name">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Your full name as it appears in the system</div>
                        </div>

                        <!-- Username Field -->
                        <div class="mb-3">
                            <label for="username" class="form-label fw-bold">
                                <i class="bi bi-person-circle"></i> Username
                            </label>
                            <input type="text" 
                                   class="form-control form-control-lg @error('username') is-invalid @enderror" 
                                   id="username" 
                                   name="username" 
                                   value="{{ old('username', $user->username) }}" 
                                   required
                                   placeholder="Enter your username">
                            @error('username')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Your unique username for login</div>
                        </div>

                        <!-- Email Field -->
                        <div class="mb-3">
                            <label for="email" class="form-label fw-bold">
                                <i class="bi bi-envelope"></i> Email Address
                            </label>
                            <input type="email" 
                                   class="form-control form-control-lg @error('email') is-invalid @enderror" 
                                   id="email" 
                                   name="email" 
                                   value="{{ old('email', $user->email) }}" 
                                   required
                                   placeholder="Enter your email address">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">We'll never share your email with anyone else</div>
                        </div>

                        <!-- Role Display (Read-only) -->
                        <div class="mb-3">
                            <label for="role" class="form-label fw-bold">
                                <i class="bi bi-shield-lock"></i> Role
                            </label>
                            <input type="text" 
                                   class="form-control form-control-lg" 
                                   id="role" 
                                   value="{{ ucfirst(str_replace('_', ' ', $user->role)) }}" 
                                   readonly
                                   disabled>
                            <div class="form-text">Your role cannot be changed from here</div>
                        </div>

                        <hr class="my-4">

                        <!-- Password Section -->
                        <h6 class="mb-3 text-primary">
                            <i class="bi bi-key"></i> Change Password (Optional)
                        </h6>
                        
                        <!-- Current Password Field -->
                        <div class="mb-3">
                            <label for="password" class="form-label fw-bold">
                                <i class="bi bi-lock"></i> New Password
                            </label>
                            <input type="password" 
                                   class="form-control form-control-lg @error('password') is-invalid @enderror" 
                                   id="password" 
                                   name="password" 
                                   placeholder="Leave blank to keep current password">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Minimum 6 characters. Leave blank to keep your current password</div>
                        </div>

                        <!-- Confirm Password Field -->
                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label fw-bold">
                                <i class="bi bi-lock-fill"></i> Confirm New Password
                            </label>
                            <input type="password" 
                                   class="form-control form-control-lg" 
                                   id="password_confirmation" 
                                   name="password_confirmation" 
                                   placeholder="Confirm your new password">
                            <div class="form-text">Re-enter your new password to confirm</div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" class="btn btn-primary btn-lg px-4">
                                <i class="bi bi-check-lg"></i> Update Profile
                            </button>
                            <a href="{{ route('admin.index') }}" class="btn btn-outline-secondary btn-lg px-4">
                                <i class="bi bi-x-lg"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function previewImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            document.getElementById('previewImg').src = e.target.result;
            document.getElementById('imagePreview').classList.remove('d-none');
        }
        
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

@endsection

@section('styles')
<style>
    .form-control:focus, .form-select:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }
    
    .card {
        transition: all 0.3s ease;
    }
    
    .alert {
        animation: slideIn 0.3s ease-out;
    }
    
    @keyframes slideIn {
        from {
            transform: translateY(-20px);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }
</style>
@endsection
