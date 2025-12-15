@extends('layouts.admin')

@section('title', 'Upload Details • ' . $upload->custom_id)

@section('content')
<div class="container-fluid py-6">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-4 mb-5">
        <div>
            <h2 class="h3 fw-bold text-gray-800 mb-2">
                Upload Session
            </h2>
            <div class="d-flex align-items-center gap-3 flex-wrap">
                <span class="badge bg-primary fs-6 px-3 py-2">{{ $upload->custom_id }}</span>
                <span class="text-muted small">
                    Uploaded {{ $upload->created_at->diffForHumans() }}
                </span>
            </div>
        </div>

        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('admin.uploads.download', $upload) }}"
               class="btn btn-success shadow-sm d-flex align-items-center gap-2">
                Download All as ZIP
            </a>
            <a href="{{ route('admin.uploads.index') }}"
               class="btn btn-outline-secondary shadow-sm">
                Back to List
            </a>
        </div>
    </div>

    <div class="row g-4 mb-5">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 fw-bold text-gray-800">
                        Session Details
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="d-flex">
                                <span class="text-muted fw-medium me-3">Message:</span>
                                <div class="flex-grow-1">
                                    @if($upload->message)
                                        <p class="mb-0 text-dark">{{ $upload->message }}</p>
                                    @else
                                        <em class="text-muted">No message provided</em>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex">
                                <span class="text-muted fw-medium me-3">IP Address:</span>
                                <code class="bg-light px-2 py-1 rounded small">{{ $upload->ip_address }}</code>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <span class="text-muted fw-medium me-3">Date & Time:</span>
                                <div>
                                    <div class="fw-medium">{{ $upload->created_at->format('M d, Y') }}</div>
                                    <small class="text-muted">{{ $upload->created_at->format('H:i:s') }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <span class="text-muted fw-medium me-3">Total Files:</span>
                                <span class="badge bg-primary fs-6 px-3 py-2">
                                    {{ $upload->files->count() }} file{{ $upload->files->count() !== 1 ? 's' : '' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm bg-gradient-primary text-white">
                <div class="card-body text-center py-5">
                    <h1 class="display-4 fw-bold mb-2">{{ $upload->files->count() }}</h1>
                    <p class="fs-5 mb-0 opacity-90">Files Uploaded</p>
                    <div class="mt-4 pt-3 border-top border-white border-opacity-25">
                        <small class="opacity-75">
                            Session ID: #{{ $upload->id }}
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold text-gray-800">
                Uploaded Files ({{ $upload->files->count() }})
            </h5>
            @if($upload->files_count > 0)
                <small class="text-success fw-medium">
                    All files are stored securely on S3
                </small>
            @endif
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">File Name</th>
                            <th>Size</th>
                            <th>Type</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($upload->files as $file)
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="flex-shrink-0">
                                            @if(str($file->mime_type)->contains('image/'))
                                                <i class="fas fa-image text-primary fa-lg"></i>
                                            @elseif(str($file->mime_type)->contains('pdf'))
                                                <i class="fas fa-file-pdf text-danger fa-lg"></i>
                                            @elseif(str($file->mime_type)->contains('video'))
                                                <i class="fas fa-video text-info fa-lg"></i>
                                            @else
                                                <i class="fas fa-file text-secondary fa-lg"></i>
                                            @endif
                                        </div>
                                        <div>
                                            <div class="fw-medium text-dark">
                                                {{ Str::limit($file->original_name, 50) }}
                                            </div>
                                            <small class="text-muted">
                                                {{ $file->original_name }}
                                            </small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="fw-medium">
                                        {{ \Illuminate\Support\Facades\Storage::disk('s3')->size($file->path) 
                                            ? round(\Illuminate\Support\Facades\Storage::disk('s3')->size($file->path) / 1024, 2) . ' KB'
                                            : number_format($file->size / 1024, 2) . ' KB' }}
                                    </span>
                                </td>
                                <td>
                                    <code class="small bg-light px-2 py-1 rounded">{{ Str::upper(Str::afterLast($file->mime_type, '/')) }}</code>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('admin.files.download', $file) }}"
                                       class="btn btn-sm btn-outline-success border-2"
                                       title="Download {{ $file->original_name }}">
                                        Download
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-6">
                                    <i class="fas fa-folder-open fa-4x text-muted opacity-25 mb-4 d-block"></i>
                                    <h6 class="text-muted">No files in this session</h6>
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
    .table th,
    .table td {
        border-color: #dee2e6 !important;
    }
    .bg-gradient-primary {
        background: linear-gradient(135deg, #4361ee 0%, #3f37c9 100%);
    }
    .btn-outline-success {
        border-width: 2px !important;
    }
    .btn-outline-success:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(25, 135, 84, 0.3);
    }
    .table tr:hover {
        background-color: #f8f9ff !important;
    }
</style>
@endsection