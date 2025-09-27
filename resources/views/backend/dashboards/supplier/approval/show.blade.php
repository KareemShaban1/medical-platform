@extends('backend.dashboards.supplier.layouts.app')

@section('content')
    <div class="approval-overlay"
        style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); z-index: 9999; display: flex; align-items: center; justify-content: center;">
        <div class="approval-modal"
            style="background: white; padding: 30px; border-radius: 10px; max-width: 600px; width: 90%; max-height: 80vh; overflow-y: auto;">
            <div class="text-center mb-4">
                <h3 class="mb-3">Supplier Approval Status</h3>

                @if ($status === 'pending')
                    <div class="alert alert-warning">
                        <i class="fas fa-clock"></i>
                        <strong>Action Required:</strong> Please upload your supplier documents for approval.
                    </div>
                @elseif($status === 'under_review')
                    <div class="alert alert-info">
                        <i class="fas fa-search"></i>
                        <strong>Under Review:</strong> Your supplier documents are being reviewed by our team. We'll notify
                        you once the review is complete.
                    </div>
                @elseif($status === 'rejected')
                    <div class="alert alert-danger">
                        <i class="fas fa-times-circle"></i>
                        <strong>Rejected:</strong> Your supplier application has been rejected.
                        @if ($approval && $approval->notes)
                            <br><small><strong>Reason:</strong> {{ $approval->notes }}</small>
                        @endif
                    </div>
                @endif
            </div>

            @if ($status === 'pending' || $status === 'rejected')
                <form id="uploadForm" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="documents" class="form-label">Upload Supplier Documents</label>
                        <input type="file" class="form-control" id="documents" name="documents[]" multiple
                            accept=".pdf,.jpg,.jpeg,.png" required>
                        <small class="form-text text-muted">Accepted formats: PDF, JPG, JPEG, PNG (Max: 10MB each)</small>
                    </div>

                    @if ($status === 'rejected' && $previousDocuments->count() > 0)
                        <div class="mb-3">
                            <h6>Previously Rejected Documents:</h6>
                            <div class="row">
                                @foreach ($previousDocuments as $doc)
                                    <div class="col-md-4 mb-2">
                                        <div class="card">
                                            <div class="card-body text-center p-2">
                                                <i class="fas fa-file text-danger"></i>
                                                <small class="d-block">{{ $doc->name }}</small>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div id="uploadMessage" class="alert" style="display: none; margin-bottom: 15px;"></div>

                    <div class="text-center">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-upload"></i> Upload Documents
                        </button>
                    </div>
                </form>
            @endif

            @if ($status === 'under_review' && $currentDocuments->count() > 0)
                <div class="mb-3">
                    <h6>Uploaded Documents:</h6>
                    <div class="row">
                        @foreach ($currentDocuments as $doc)
                            <div class="col-md-4 mb-2">
                                <div class="card">
                                    <div class="card-body text-center p-2">
                                        <i class="fas fa-file text-success"></i>
                                        <small class="d-block">{{ $doc->name }}</small>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="text-center mt-3">
                <form action="{{ route('supplier.logout') }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-secondary">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('uploadForm')?.addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const submitBtn = this.querySelector('button[type="submit"]');
            const messageDiv = document.getElementById('uploadMessage');

            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Uploading...';
            messageDiv.style.display = 'none';

            fetch('{{ route('supplier.approval.upload') }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        messageDiv.className = 'alert alert-success';
                        messageDiv.innerHTML = '<i class="fas fa-check-circle"></i> ' + data.message;
                        messageDiv.style.display = 'block';
                        setTimeout(() => {
                            window.location.reload();
                        }, 2000);
                    } else {
                        messageDiv.className = 'alert alert-danger';
                        messageDiv.innerHTML = '<i class="fas fa-exclamation-circle"></i> ' + (data.message ||
                            'Upload failed');
                        messageDiv.style.display = 'block';
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = '<i class="fas fa-upload"></i> Upload Documents';
                    }
                })
                .catch(error => {
                    messageDiv.className = 'alert alert-danger';
                    messageDiv.innerHTML =
                        '<i class="fas fa-exclamation-circle"></i> Upload failed. Please try again.';
                    messageDiv.style.display = 'block';
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fas fa-upload"></i> Upload Documents';
                });
        });
    </script>
@endsection
