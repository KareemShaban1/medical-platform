@extends('backend.dashboards.clinic.layouts.app')

@section('content')
    <style>
        .approval-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.85);
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(4px);
        }

        .approval-modal {
            background: white;
            padding: 0;
            border-radius: 12px;
            max-width: 700px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }

        .modal-header-custom {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            color: white;
            padding: 2rem;
            border-radius: 12px 12px 0 0;
            text-align: center;
        }

        .modal-header-custom h3 {
            margin: 0;
            font-size: 1.75rem;
            font-weight: 600;
        }

        .modal-body-custom {
            padding: 2rem;
        }

        /* Document Requirements Styles */
        .alert-header {
            background: linear-gradient(135deg, #ff6b35 0%, #f7931e 100%);
            color: white;
            padding: 1.5rem;
            border-radius: 8px 8px 0 0;
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 0;
        }

        .alert-header i {
            font-size: 2rem;
        }

        .alert-header-text h5 {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 0.25rem;
        }

        .alert-header-text small {
            opacity: 0.95;
        }

        .documents-grid {
            background: white;
            padding: 1.5rem;
            border: 1px solid #e5e7eb;
            border-top: none;
            border-radius: 0 0 8px 8px;
        }

        .doc-item {
            display: flex;
            align-items: flex-start;
            padding: 1rem;
            margin-bottom: 0.75rem;
            border-left: 4px solid #ff6b35;
            background: #fff8f6;
            border-radius: 6px;
            transition: all 0.2s ease;
        }

        .doc-item:last-child {
            margin-bottom: 0;
        }

        .doc-item:hover {
            background: #fff3ef;
            transform: translateX(4px);
        }

        .doc-item.optional {
            border-left-color: #94a3b8;
            background: #f8fafc;
        }

        .doc-item.optional:hover {
            background: #f1f5f9;
        }

        .doc-icon {
            font-size: 1.5rem;
            color: #ff6b35;
            margin-right: 1rem;
            margin-top: 0.25rem;
            min-width: 28px;
        }

        .doc-item.optional .doc-icon {
            color: #94a3b8;
        }

        .doc-content {
            flex: 1;
        }

        .doc-title {
            font-size: 1rem;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 0.25rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .doc-badge {
            background: #94a3b8;
            color: white;
            padding: 0.15rem 0.5rem;
            border-radius: 10px;
            font-size: 0.7rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .doc-description {
            color: #64748b;
            font-size: 0.875rem;
            line-height: 1.5;
        }

        /* Status Alerts */
        .status-alert {
            padding: 1.5rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .status-alert i {
            font-size: 2rem;
        }

        .status-alert.info {
            background: #eff6ff;
            color: #1e40af;
            border-left: 4px solid #3b82f6;
        }

        .status-alert.danger {
            background: #fef2f2;
            color: #991b1b;
            border-left: 4px solid #ef4444;
        }

        .status-alert.success {
            background: #f0fdf4;
            color: #166534;
            border-left: 4px solid #22c55e;
        }

        /* Form Styles */
        .upload-section {
            background: #f9fafb;
            padding: 1.5rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
        }

        .form-label {
            font-weight: 600;
            color: #374151;
            margin-bottom: 0.5rem;
        }

        .form-control {
            border: 2px solid #e5e7eb;
            border-radius: 6px;
            padding: 0.75rem;
            transition: border-color 0.2s;
        }

        .form-control:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border-radius: 6px;
            font-weight: 500;
            transition: all 0.2s;
        }

        .btn-primary {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            border: none;
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
        }

        .btn-secondary {
            background: #6b7280;
            border: none;
        }

        .btn-secondary:hover {
            background: #4b5563;
        }

        .btn-success {
            background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
            border: none;
        }

        .btn-success:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(34, 197, 94, 0.4);
        }

        /* Document Cards */
        .doc-card {
            background: white;
            border: 2px solid #e5e7eb;
            border-radius: 6px;
            padding: 1rem;
            text-align: center;
            transition: all 0.2s;
        }

        .doc-card:hover {
            border-color: #3b82f6;
            transform: translateY(-2px);
        }

        .doc-card i {
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }

        @media (max-width: 768px) {
            .approval-modal {
                width: 95%;
                max-height: 95vh;
            }

            .modal-header-custom,
            .modal-body-custom {
                padding: 1.5rem;
            }

            .alert-header {
                flex-direction: column;
                text-align: center;
            }
        }
    </style>

    <div class="approval-overlay">
        <div class="approval-modal">
            <div class="modal-header-custom">
                <h3>Clinic Approval Status</h3>
            </div>

            <div class="modal-body-custom">
                @if ($status === 'pending')
                    <div class="alert-header">
                        <i class="fas fa-exclamation-circle"></i>
                        <div class="alert-header-text">
                            <h5 class="mb-0">Action Required</h5>
                            <small>Please upload the required documents below</small>
                        </div>
                    </div>

                    <div class="documents-grid">
                        <div class="doc-item">
                            <i class="fas fa-file-contract doc-icon"></i>
                            <div class="doc-content">
                                <div class="doc-title">Clinic License</div>
                                <div class="doc-description">License number must be clearly visible</div>
                            </div>
                        </div>

                        <div class="doc-item">
                            <i class="fas fa-id-card doc-icon"></i>
                            <div class="doc-content">
                                <div class="doc-title">National ID</div>
                                <div class="doc-description">Front & back of clinic owner's ID</div>
                            </div>
                        </div>

                        <div class="doc-item">
                            <i class="fas fa-camera doc-icon"></i>
                            <div class="doc-content">
                                <div class="doc-title">Interior Photos</div>
                                <div class="doc-description">Clear photos of clinic interior spaces</div>
                            </div>
                        </div>

                        <div class="doc-item optional">
                            <i class="fas fa-store doc-icon"></i>
                            <div class="doc-content">
                                <div class="doc-title">
                                    Signboard Photo
                                    <span class="doc-badge">Optional</span>
                                </div>
                                <div class="doc-description">Exterior signboard with clinic name</div>
                            </div>
                        </div>
                    </div>

                @elseif($status === 'under_review')
                    <div class="status-alert info">
                        <i class="fas fa-search"></i>
                        <div>
                            <strong>Under Review</strong>
                            <p class="mb-0 mt-1">Your clinic documents are being reviewed by our team. We'll notify you once the review is complete.</p>
                        </div>
                    </div>

                @elseif($status === 'rejected')
                    <div class="status-alert danger">
                        <i class="fas fa-times-circle"></i>
                        <div>
                            <strong>Application Rejected</strong>
                            @if ($approval && $approval->notes)
                                <p class="mb-0 mt-1"><strong>Reason:</strong> {{ $approval->notes }}</p>
                            @endif
                        </div>
                    </div>

                @elseif($status === 'approved')
                    <div class="status-alert success">
                        <i class="fas fa-check-circle"></i>
                        <div>
                            <strong>Application Approved!</strong>
                            <p class="mb-0 mt-1">Your clinic application has been approved. You can now explore your dashboard.</p>
                        </div>
                    </div>
                @endif

                @if ($status === 'pending' || $status === 'rejected')
                    <div class="upload-section">
                        <form id="uploadForm" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <label for="documents" class="form-label">Upload Clinic Documents</label>
                                <input type="file" class="form-control" id="documents" name="documents[]" multiple
                                    accept=".pdf,.jpg,.jpeg,.png" required>
                                <small class="form-text text-muted">Accepted formats: PDF, JPG, JPEG, PNG (Max: 10MB each)</small>
                            </div>

                            @if ($status === 'rejected' && $previousDocuments->count() > 0)
                                <div class="mb-3">
                                    <h6 class="mb-3">Previously Rejected Documents:</h6>
                                    <div class="row g-2">
                                        @foreach ($previousDocuments as $doc)
                                            <div class="col-md-4">
                                                <div class="doc-card">
                                                    <i class="fas fa-file text-danger"></i>
                                                    <small class="d-block">{{ $doc->name }}</small>
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
                    </div>
                @endif

                @if ($status === 'under_review' && $currentDocuments->count() > 0)
                    <div class="mb-4">
                        <h6 class="mb-3">Uploaded Documents:</h6>
                        <div class="row g-2">
                            @foreach ($currentDocuments as $doc)
                                <div class="col-md-4">
                                    <div class="doc-card">
                                        <i class="fas fa-file text-success"></i>
                                        <small class="d-block">{{ $doc->name }}</small>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="text-center mt-4">
                    <form action="{{ route('clinic.logout') }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn btn-secondary">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </button>
                    </form>
                    @if ($status === 'approved')
                        <a href="{{ route('clinic.dashboard') }}" class="btn btn-success ms-2">
                            <i class="fas fa-arrow-right"></i> Go to Dashboard
                        </a>
                    @endif
                </div>
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

            fetch('{{ route('clinic.approval.upload') }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
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
                        messageDiv.innerHTML = '<i class="fas fa-exclamation-circle"></i> ' + (data.message || 'Upload failed');
                        messageDiv.style.display = 'block';
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = '<i class="fas fa-upload"></i> Upload Documents';
                    }
                })
                .catch(error => {
                    messageDiv.className = 'alert alert-danger';
                    messageDiv.innerHTML = '<i class="fas fa-exclamation-circle"></i> Upload failed. Please try again.';
                    messageDiv.style.display = 'block';
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fas fa-upload"></i> Upload Documents';
                });
        });
    </script>
@endsection
