@extends('layouts.app', ['title' => 'Activity Logs'])

@section('content')
<h1 class="h3 mb-3">Activity Logs</h1>

<div class="card">
    <div class="card-body table-responsive">
        <table class="table table-bordered table-striped">
            <thead>
            <tr>
                <th>User ID</th>
                <th>Role</th>
                <th>Action</th>
                <th>Description</th>
                <th>Created At</th>
            </tr>
            </thead>
            <tbody>
            @forelse($items as $item)
                <tr>
                    <td>{{ $item->user_id }}</td>
                    <td>{{ $item->role }}</td>
                    <td>{{ $item->action }}</td>
                    <td>{{ $item->description }}</td>
                    <td>{{ $item->created_at }}</td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center text-muted">Belum ada activity log.</td></tr>
            @endforelse
            </tbody>
        </table>
        {{ $items->links() }}
    </div>
</div>
@endsection
