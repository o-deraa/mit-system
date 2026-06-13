@extends('layouts.app', ['title' => 'Recommendation Logs'])

@section('content')
<h1 class="h3 mb-3">Recommendation Logs</h1>

<div class="card">
    <div class="card-body table-responsive">
        <table class="table table-bordered table-striped">
            <thead>
            <tr>
                <th>Requested By Maba ID</th>
                <th>Input NRP</th>
                <th>Recommended Groups</th>
                <th>Created At</th>
            </tr>
            </thead>
            <tbody>
            @forelse($items as $item)
                <tr>
                    <td>{{ $item->requested_by_maba_id }}</td>
                    <td><pre class="mb-0">{{ json_encode($item->input_nrp_list, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre></td>
                    <td><pre class="mb-0">{{ json_encode($item->recommended_groups, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre></td>
                    <td>{{ $item->created_at }}</td>
                </tr>
            @empty
                <tr><td colspan="4" class="text-center text-muted">Belum ada recommendation log.</td></tr>
            @endforelse
            </tbody>
        </table>
        {{ $items->links() }}
    </div>
</div>
@endsection
```

## `resources/views/admin/logs/revision.blade.php`

```blade
@extends('layouts.app', ['title' => 'Revision Histories'])

@section('content')
<h1 class="h3 mb-3">Revision Histories</h1>

<div class="card">
    <div class="card-body table-responsive">
        <table class="table table-bordered table-striped">
            <thead>
            <tr>
                <th>Realisasi ID</th>
                <th>Admin</th>
                <th>Old Status</th>
                <th>New Status</th>
                <th>Notes</th>
                <th>Created At</th>
            </tr>
            </thead>
            <tbody>
            @forelse($items as $item)
                <tr>
                    <td>{{ $item->realisasi_id }}</td>
                    <td>{{ $item->admin_identifier }}</td>
                    <td>{{ $item->old_status }}</td>
                    <td>{{ $item->new_status }}</td>
                    <td>{{ $item->notes }}</td>
                    <td>{{ $item->created_at }}</td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-muted">Belum ada revision history.</td></tr>
            @endforelse
            </tbody>
        </table>
        {{ $items->links() }}
    </div>
</div>
@endsection
