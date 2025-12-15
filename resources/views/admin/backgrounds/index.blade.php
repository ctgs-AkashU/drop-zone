@extends('layouts.admin')

@section('title', 'Background Images')

@section('content')
<div class="container-fluid py-6">

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h2 class="h3 fw-bold text-gray-800 mb-1">
                Background Images
            </h2>
            <p class="text-muted mb-0">Manage images used as backgrounds across the site</p>
        </div>
        <div class="text-muted">
            Total: <strong class="text-primary">{{ $images->count() }}</strong> image{{ $images->count() !== 1 ? 's' : '' }}
        </div>
    </div>

    <!-- Success Alert -->
    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm d-flex align-items-center mb-5 rounded" role="alert">
            <div>{{ session('success') }}</div>
        </div>
    @endif

    <!-- Upload Form Card -->
    <div class="card border shadow-sm mb-5">
        <div class="card-header bg-white border-bottom py-3">
            <h5 class="mb-0 fw-bold text-gray-800">Upload New Background Image</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.backgrounds.store') }}" method="POST" enctype="multipart/form-data" id="uploadForm">
                @csrf
                <div class="row g-4 align-items-end">
                    <div class="col-lg-8">
                        <label for="image" class="form-label fw-medium">Select Image (JPG, PNG, WebP)</label>
                        <input type="file" 
                               class="form-control form-control-lg @error('image') is-invalid @enderror" 
                               id="image" 
                               name="image" 
                               accept="image/jpeg,image/png,image/webp" 
                               required>
                        @error('image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-lg-4">
                        <button type="submit" class="btn btn-primary btn-lg w-60 shadow-sm d-flex align-items-center justify-content-center gap-2">
                            Upload Image
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Images Grid -->
    @if($images->count() > 0)
        <div class="row g-4">
            @foreach($images as $image)
                <div class="col-md-6 col-lg-4 col-xl-3">
                    <div class="card border shadow-sm hover-shadow-lg transition h-100 overflow-hidden rounded-4">
                        <div class="position-relative">
                            <img src="{{ Storage::url($image->path) }}" 
                                 class="card-img-top" 
                                 alt="Background Image" 
                                 style="height: 220px; object-fit: cover; transition: transform 0.4s ease;">
                            
                            <!-- Overlay on hover -->
                            <div class="position-absolute top-0 start-0 w-100 h-100 bg-dark bg-opacity-0 hover-bg-opacity-60 transition d-flex align-items-center justify-content-center opacity-0 hover-opacity-100">
                                <div class="text-white text-center">
                                    <p class="mb-2 small opacity-90">Uploaded</p>
                                    <p class="fw-bold">{{ $image->created_at->format('M d, Y') }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <small class="text-muted">
                                    {{ \Illuminate\Support\Str::limit(basename($image->path), 20) }}
                                </small>
                                <span class="badge bg-light text-dark small">
                                    {{ round(\Storage::disk('public')->size($image->path) / 1024, 1) }} KB
                                </span>
                            </div>

                            <button onclick="confirmDelete({{ $image->id }})"
                                    class="btn btn-danger w-100 shadow-sm">
                                Delete Image
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <!-- Empty State -->
        <div class="text-center py-6">
            <i class="fas fa-image fa-5x text-muted opacity-25 mb-4"></i>
            <h4 class="text-muted">No background images yet</h4>
            <p class="text-muted">Upload your first image above to get started.</p>
        </div>
    @endif
</div>

<!-- SweetAlert2 + Delete Form -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function confirmDelete(id) {
    Swal.fire({
        title: 'Delete this background image?',
        text: "This action cannot be undone.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, delete it!',
        reverseButtons: true,
        customClass: {
            popup: 'shadow-lg border-0'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/admin/backgrounds/${id}`;
            form.innerHTML = `@csrf @method('DELETE')`;
            document.body.appendChild(form);
            form.submit();
        }
    });
}
</script>

<!-- Enhanced Styles -->
<style>
    .hover-shadow-lg {
        transition: all 0.3s ease;
    }
    .hover-shadow-lg:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.12) !important;
    }
    .hover-shadow-lg:hover img {
        transform: scale(1.05);
    }
    .hover-bg-opacity-60 {
        transition: background-color 0.4s ease;
    }
    .hover-opacity-100 {
        transition: opacity 0.4s ease;
    }
    .card {
        border: 1px solid rgba(0,0,0,.08) !important;
        border-radius: 1rem !important;
    }
    .card-img-top {
        border-top-left-radius: 1rem;
        border-top-right-radius: 1rem;
    }
</style>
@endsection