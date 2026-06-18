@extends('layouts.app', ['title' => 'Recommendation Logs'])

@section('content')
<h1 class="h3 mb-3">Recommendation Logs</h1>

@php
    if (! function_exists('mongo_normalize')) {
        function mongo_normalize($value) {
            if ($value === null || $value === '') {
                return null;
            }

            if ($value instanceof \MongoDB\Model\BSONDocument || $value instanceof \MongoDB\Model\BSONArray) {
                return mongo_normalize($value->getArrayCopy());
            }

            if ($value instanceof \MongoDB\BSON\ObjectId) {
                return (string) $value;
            }

            if ($value instanceof \MongoDB\BSON\UTCDateTime) {
                return $value
                    ->toDateTime()
                    ->setTimezone(new \DateTimeZone(config('app.timezone')))
                    ->format('Y-m-d H:i:s');
            }

            if ($value instanceof \Carbon\CarbonInterface) {
                return $value
                    ->copy()
                    ->setTimezone(config('app.timezone'))
                    ->format('Y-m-d H:i:s');
            }

            if (is_array($value)) {
                $result = [];

                foreach ($value as $key => $item) {
                    $result[$key] = mongo_normalize($item);
                }

                return $result;
            }

            if (is_object($value)) {
                return mongo_normalize(json_decode(json_encode($value), true));
            }

            return $value;
        }
    }

    if (! function_exists('mongo_json')) {
        function mongo_json($value) {
            $value = mongo_normalize($value);

            if ($value === null || $value === [] || $value === '') {
                return '-';
            }

            if (is_scalar($value)) {
                return (string) $value;
            }

            return json_encode(
                $value,
                JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
            ) ?: '-';
        }
    }
@endphp

<style>
    .json-box {
        max-height: 320px;
        overflow: auto;
        white-space: pre-wrap;
        word-break: break-word;
        background: #f8f9fa;
        color: #212529 !important;
        opacity: 1 !important;
        padding: 10px;
        border-radius: 6px;
        font-size: 12px;
        margin-bottom: 0;
    }

    .table td {
        vertical-align: top;
    }
</style>

<div class="card">
    <div class="card-body table-responsive">
        <table class="table table-bordered table-striped align-middle">
            <thead>
            <tr>
                <th style="width: 140px;">Created At</th>
                <th style="width: 120px;">Requested By Maba ID</th>
                <th style="width: 180px;">Input NRP</th>
                <th>Recommended Groups</th>
                <th>Scoring Detail</th>
            </tr>
            </thead>
            <tbody>
            @forelse($items as $item)
                <tr>
                    <td>{{ $item->getAttribute('created_at') ?? '-' }}</td>
                    <td>{{ $item->getAttribute('requested_by_maba_id') ?? '-' }}</td>

                    <td>
                        <pre class="json-box">{{ mongo_json($item->getAttribute('input_nrp_list')) }}</pre>
                    </td>

                    <td>
                        <pre class="json-box">{{ mongo_json($item->getAttribute('recommended_groups')) }}</pre>
                    </td>

                    <td>
                        <pre class="json-box">{{ mongo_json($item->getAttribute('scoring_detail')) }}</pre>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center text-muted">
                        Belum ada recommendation log.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>

        {{ $items->links() }}
    </div>
</div>
@endsection
