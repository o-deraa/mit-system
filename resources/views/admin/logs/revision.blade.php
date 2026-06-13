@extends('layouts.app', ['title' => 'Revision Histories'])

@section('content')
<h1 class="h3 mb-3">Revision Histories</h1>

<div class="card">
    <div class="card-body table-responsive">
        <table class="table table-bordered table-striped align-middle">
            <thead>
            <tr>
                <th>Realisasi ID</th>
                <th>Admin</th>
                <th>Old Status</th>
                <th>New Status</th>
                <th>Notes</th>
                <th>Changed Fields</th>
                <th>Created At</th>
            </tr>
            </thead>
            <tbody>
            @forelse($items as $item)
                @php
                    $changedFields = $item->getAttribute('changed_fields');

                    if ($changedFields instanceof \MongoDB\Model\BSONDocument || $changedFields instanceof \MongoDB\Model\BSONArray) {
                        $changedFields = $changedFields->getArrayCopy();
                    }

                    $changedFieldsJson = $changedFields
                        ? json_encode($changedFields, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
                        : '-';
                @endphp

                <tr>
                    <td>{{ $item->getAttribute('realisasi_id') ?? '-' }}</td>
                    <td>{{ $item->getAttribute('admin_identifier') ?? '-' }}</td>
                    <td>{{ $item->getAttribute('old_status') ?? '-' }}</td>
                    <td>{{ $item->getAttribute('new_status') ?? '-' }}</td>
                    <td>{{ $item->getAttribute('notes') ?? '-' }}</td>
                    <td>
                        <pre class="mb-0 small">{{ $changedFieldsJson }}</pre>
                    </td>
                    <td>{{ $item->getAttribute('created_at') ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center text-muted">
                        Belum ada revision history.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>

        {{ $items->links() }}
    </div>
</div>
@endsection
