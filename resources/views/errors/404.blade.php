@extends('frontend.layouts.app')

@section('title', 'Page Not Found')

@section('content')

    <div class="d-flex align-items-center justify-content-center text-center" style="
                                    min-height: calc(100vh - 300px);
                                    padding-top: 90px;
                                    box-sizing: border-box;
                                 ">

        <div class="col-md-6 px-4">

            <div class="mb-4">
                <i class="fas fa-exclamation-triangle fa-5x text-warning"></i>
            </div>

            <h1 class="display-4 fw-bold mb-3">Oops!</h1>

            <h2 class="h5 text-muted mb-4">
                We can't seem to find the page you're looking for.
            </h2>

            <p class="text-secondary mb-5">
                The page you are looking for might have been removed,
                had its name changed, or is temporarily unavailable.
            </p>
            <a href="{{ url('/') }}" class="btn btn-primary px-5 py-2">
                <i class="fas fa-home me-2"></i>
                Back to Homepage
            </a>


        </div>
    </div>

@endsection