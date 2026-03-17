@extends('layouts.app')

@section('title', 'Access Restricted')

@section('content')
<div class="container h-100 d-flex align-items-center justify-content-center" style="min-height: 70vh;">
    <div class="text-center p-5 shadow-lg rounded-4 bg-white" style="max-width: 600px; border-top: 5px solid #dc3545;">
        <div class="mb-4 position-relative d-inline-block">
            <div class="lock-container">
                <i class="bi bi-shield-lock-fill text-danger display-1"></i>
                <div class="lock-animation-ring"></div>
            </div>
        </div>
        <h1 class="fw-bold text-dark mb-3">Access Locked</h1>
        <p class="lead text-muted mb-4">{{ $message ?? 'Unlock please contact the admin to enable this' }}</p>
        <div class="d-grid gap-2 d-md-flex justify-content-md-center">
            <a href="{{ url('/') }}" class="btn btn-outline-secondary px-4">
                <i class="bi bi-house-door me-2"></i> Back to Home
            </a>
            <button onclick="window.location.reload()" class="btn btn-danger px-4">
                <i class="bi bi-arrow-clockwise me-2"></i> Refresh Page
            </button>
        </div>
        <div class="mt-5 pt-4 border-top">
            <p class="small text-muted mb-0">
                <i class="bi bi-info-circle me-1"></i> This module has been temporarily disabled by the system administrator for maintenance or security purposes.
            </p>
        </div>
    </div>
</div>

<style>
    .lock-container {
        position: relative;
        z-index: 1;
        animation: lockShake 4s ease-in-out infinite;
    }

    .lock-animation-ring {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 120px;
        height: 120px;
        border: 2px solid #dc3545;
        border-radius: 50%;
        opacity: 0;
        z-index: -1;
        animation: ripple 2s linear infinite;
    }

    @keyframes lockShake {
        0%, 100% { transform: rotate(0deg); }
        5%, 15%, 25% { transform: rotate(5deg); }
        10%, 20%, 30% { transform: rotate(-5deg); }
        35% { transform: rotate(0deg); }
    }

    @keyframes ripple {
        0% { width: 120px; height: 120px; opacity: 0.5; }
        100% { width: 200px; height: 200px; opacity: 0; }
    }

    [data-theme="dark"] .bg-white {
        background-color: #1e1e1e !important;
        border-color: #333 !important;
    }
    
    [data-theme="dark"] .text-dark {
        color: #f8f9fa !important;
    }
</style>
@endsection
