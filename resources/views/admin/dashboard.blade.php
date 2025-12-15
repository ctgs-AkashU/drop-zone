@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
<div class="container-fluid py-6">
    <div class="d-flex justify-content-between align-items-center mb-5">
        <h2 class="h3 fw-bold text-gray-800">Dashboard Overview</h2>
        <div class="text-muted">Welcome back,<span class="text-primary fw-700" style="font-size:20px;">    {{ auth()->check() ? auth()->user()->name : 'Admin' }}</span></div>
    </div>

    <!-- Quick Stats Cards -->
    <div class="row g-4 mb-5">
        <div class="col-md-6 col-xl-4">
            <div class="card border-0 shadow-sm hover-shadow-lg transition h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="flex-shrink-0 bg-primary bg-opacity-10 text-primary rounded-3 p-3 me-4">
                        <i class="fas fa-cloud-upload-alt fa-2x"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1">Total Upload Sessions</h6>
                        <h3 class="fw-bold text-dark mb-0">{{ number_format($stats['total_sessions'] ?? 0) }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-4">
            <div class="card border-0 shadow-sm hover-shadow-lg transition h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="flex-shrink-0 bg-success bg-opacity-10 text-success rounded-3 p-3 me-4">
                        <i class="fas fa-file-alt fa-2x"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1">Total Files Uploaded</h6>
                        <h3 class="fw-bold text-dark mb-0">{{ number_format($stats['total_files'] ?? 0) }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-12 col-xl-4">
            <div class="card border-0 shadow-sm hover-shadow-lg transition h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="flex-shrink-0 bg-info bg-opacity-10 text-info rounded-3 p-3 me-4">
                        <i class="fas fa-clock fa-2x"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1">Recent Activity</h6>
                        <h3 class="fw-bold text-dark mb-0">{{ $stats['recent_uploads']->count() }} in last 24h</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @php
        $awsStats = $storage ?? app(\App\Services\S3StorageService::class)->getStorageStats();
        $percentage = $awsStats['percentage'] ?? 0;
        $isNearLimit = !empty($awsStats['is_near_limit']) && $awsStats['is_near_limit'];
        $usedState = $percentage >= 90 ? 'danger' : ($percentage >= 60 ? 'warning' : 'primary');
        $usedBg = $percentage >= 90 ? 'danger' : ($percentage >= 60 ? 'orange' : 'primary');
    @endphp

    <!-- Storage Warning Alert -->
    @if($isNearLimit)
        <div class="alert alert-danger border-0 shadow-sm d-flex align-items-center mb-5" role="alert">
            <i class="fas fa-exclamation-triangle fa-2x me-3"></i>
            <div>
                <strong>Storage Critical:</strong> {{ $percentage }}% of storage used.<br>
                <span class="text-danger">Non-admin uploads are blocked.</span> Please delete unused files immediately.
            </div>
        </div>
    @endif

    <!-- Storage Overview -->
    <div class="card border-0 shadow-sm mb-5">
        <div class="card-header bg-white border-0 py-4">
            <h5 class="mb-0 fw-bold text-gray-800"><i class="fas fa-hdd me-2"></i> S3 Storage Usage</h5>
        </div>
        <div class="card-body">
            <div class="row g-4 mb-4">
                <div class="col-md-4">
                    <div class="text-center p-4 bg-danger bg-opacity-10 rounded-3">
                        <h1 class="fw-bold text-danger mb-1">{{ $awsStats['used'] ?? '0 GB' }}</h1>
                        <p class="text-muted mb-0">Used</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="text-center p-4 bg-success bg-opacity-10 rounded-3">
                        <h1 class="fw-bold text-success mb-1">{{ $awsStats['free'] ?? '5 GB' }}</h1>
                        <p class="text-muted mb-0">Free</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="text-center p-4 bg-primary bg-opacity-10 rounded-3">
                        <h1 class="fw-bold text-primary mb-1">5 GB</h1>
                        <p class="text-muted mb-0">Total Limit</p>
                    </div>
                </div>
            </div>

            <!-- Progress Bar -->
            <div class="mt-4">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted">Storage Usage</span>
                    <span class="fw-bold d-flex align-items-center gap-2
                        @if($percentage >= 90) text-danger 
                        @elseif($percentage >= 70) text-warning 
                        @else text-primary 
                        @endif">
                        @if($percentage >= 90)
                            Critical Level
                        @elseif($percentage >= 70)
                            Warning Level
                        @endif
                        {{ $percentage }}% Used
                    </span>
                </div>
                <div class="progress" style="height: 28px; border-radius: 14px; overflow: hidden;">
                    <div class="progress-bar 
                        {{ $percentage >= 90 ? 'bg-danger' : ($percentage >= 70 ? 'bg-warning' : 'bg-success') }}
                        progress-bar-striped"
                         role="progressbar"
                         style="width: {{ $percentage }}%; font-weight: bold;"
                         aria-valuenow="{{ $percentage }}"
                         aria-valuemin="0"
                         aria-valuemax="100">
                        {{ $percentage }}%
                    </div>
                </div>
                @if($percentage >= 90)
                    <small class="text-danger mt-2 d-block">
                        <i class="fas fa-exclamation-circle"></i> Extremely low space remaining!
                    </small>
                @endif
            </div>
        </div>
    </div>

    <!-- Recent Uploads Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center py-4">
            <h5 class="mb-0 fw-bold text-gray-800"><i class="fas fa-history me-2"></i> Recent Upload Sessions</h5>
            <a href="{{ route('admin.uploads.index') }}" class="btn btn-sm btn-outline-primary">
                View All
            </a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">ID</th>
                            <th>Custom ID</th>
                            <th>Files</th>
                            <th>Uploaded</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($stats['recent_uploads'] ?? [] as $upload)
                            <tr>
                                <td class="ps-4 fw-medium">#{{ $upload->id }}</td>
                                <td>
                                    <code class="bg-light px-2 py-1 rounded">{{ $upload->custom_id }}</code>
                                </td>
                                <td>
                                    <span class="badge bg-primary rounded-pill">
                                        {{ $upload->files_count }} files
                                    </span>
                                </td>
                                <td>
                                    <small class="text-muted">
                                        <i class="far fa-clock"></i> {{ $upload->created_at->diffForHumans() }}
                                    </small>
                                </td>
                                <td>
                                    <a href="{{ route('admin.uploads.show', $upload) }}"
                                       class="btn btn-sm btn-outline-secondary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <i class="fas fa-inbox fa-3x mb-3 opacity-25"></i><br>
                                    No recent uploads found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    .hover-shadow-lg {
        transition: all 0.3s ease;
    }
    .hover-shadow-lg:hover {
        transform: translateY(-5px);
        box-shadow: 0 1rem 3rem rgba(0,0,0,.175)!important;
    }
    .transition {
        transition: all 0.3s ease;
    }
</style>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endsection