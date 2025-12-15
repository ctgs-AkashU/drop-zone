@extends('layouts.admin')

@section('title', 'Upload Activities')

@section('content')
<div class="container-fluid py-6">

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h2 class="h3 fw-bold text-gray-800 mb-1">
                Upload Activities
            </h2>
            <p class="text-muted mb-0">Manage and review all file upload sessions</p>
        </div>
        <div class="text-muted">
            Total: <strong class="text-primary">{{ $uploads->total() }}</strong> sessions
        </div>
    </div>

    <!-- Success Alert -->
    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm d-flex align-items-center mb-4 rounded" role="alert">
            <div>{{ session('success') }}</div>
        </div>
    @endif

    <!-- Uploads Table Card -->
    <div class="card border shadow-sm rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light border-bottom">
                        <tr>
                            <th class="ps-4 py-3 text-uppercase text-secondary small fw-bold">ID</th>
                            <th class="py-3 text-uppercase text-secondary small fw-bold">Custom ID</th>
                            <th class="py-3 text-uppercase text-secondary small fw-bold">Message</th>
                            <th class="text-center py-3 text-uppercase text-secondary small fw-bold">Files</th>
                            <th class="py-3 text-uppercase text-secondary small fw-bold">Uploaded At</th>
                            <th class="text-center py-3 text-uppercase text-secondary small fw-bold">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        @forelse($uploads as $upload)
                            <tr class="border-start-0 border-end-0 hover-bg-light transition">
                                <td class="ps-4 fw-medium">#{{ $upload->id }}</td>
                                <td>
                                    <code class="bg-light px-3 py-1 rounded small border">{{ $upload->custom_id }}</code>
                                </td>
                                <td>
                                    @if($upload->message)
                                        <span title="{{ $upload->message }}" class="text-muted small">
                                            {{ Str::limit($upload->message, 60) }}
                                        </span>
                                    @else
                                        <em class="text-muted small">No message</em>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="badge rounded-pill fw-medium
                                        {{ $upload->files_count > 10 ? 'bg-danger' : ($upload->files_count > 5 ? 'bg-warning text-dark' : 'bg-success') }}
                                        px-3 py-2">
                                        {{ $upload->files_count }} files
                                    </span>
                                </td>
                                <td>
                                    <div class="text-muted small">
                                        {{ $upload->created_at->format('M d, Y') }}
                                    </div>
                                    <div class="text-muted smaller">
                                        {{ $upload->created_at->format('H:i') }}
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group gap-2" role="group">
                                        <a href="{{ route('admin.uploads.show', $upload) }}"
                                           class="btn btn-sm btn-outline-primary border rounded"
                                           title="View Details">
                                            View
                                        </a>

                                        <button type="button"
                                                class="btn btn-sm btn-outline-danger border rounded"
                                                onclick="confirmDelete({{ $upload->id }})"
                                                title="Delete Session">
                                            Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-6">
                                    <div class="text-muted">
                                        <div class="mb-3">No upload sessions found</div>
                                        <p class="small text-secondary">When users upload files, they will appear here.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($uploads->hasPages())
                <div class="card-footer bg-transparent border-top py-4">
                    <div class="d-flex justify-content-center">
                        {{ $uploads->onEachSide(1)->links('vendor.pagination.bootstrap-5') }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function confirmDelete(id) {
    Swal.fire({
        title: 'Delete this upload session?',
        text: "All associated files will be permanently deleted.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, delete it!',
        reverseButtons: true,
        customClass: {
            popup: 'border-0 shadow-lg',
            confirmButton: 'rounded'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/admin/uploads/${id}`;
            form.innerHTML = `@csrf @method('DELETE')`;
            document.body.appendChild(form);
            form.submit();
        }
    });
}
</script>

<!-- Enhanced hover & visibility styles -->
<style>
    .hover-bg-light:hover {
        background-color: #f8f9fa !important;
    }
    .table th,
    .table td {
        border-color: #dee2e6 !important;
    }
    .btn-outline-primary,
    .btn-outline-danger {
        border-width: 1.5px !important;
    }
    .btn-outline-primary:hover,
    .btn-outline-danger:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    .card {
        border: 1px solid #e0e0e0 !important;
    }
</style>
@endsection