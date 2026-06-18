@extends('layouts.app', ['title' => 'Log MongoDB'])

@section('content')
<h1 class="h3 mb-3">Log MongoDB</h1>

<div class="row g-3">
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <div class="text-muted">Activity Logs</div>
                <div class="fs-3 fw-bold">{{ $activityCount }}</div>
                <a href="{{ route('admin.logs.activity') }}" class="btn btn-sm btn-dark mt-2">
                    Lihat
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <div class="text-muted">Recommendation Logs</div>
                <div class="fs-3 fw-bold">{{ $recommendationCount }}</div>
                <a href="{{ route('admin.logs.recommendation') }}" class="btn btn-sm btn-dark mt-2">
                    Lihat
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <div class="text-muted">Revision Histories</div>
                <div class="fs-3 fw-bold">{{ $revisionCount }}</div>
                <a href="{{ route('admin.logs.revision') }}" class="btn btn-sm btn-dark mt-2">
                    Lihat
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
