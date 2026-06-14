@if(session('success'))
    <div class="alert alert-success alert-dismissible" role="alert">
        <div class="d-flex">
            <div>
                <i class="ti ti-circle-check me-2"></i>
            </div>
            <div>{{ session('success') }}</div>
        </div>

        <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible" role="alert">
        <div class="d-flex">
            <div>
                <i class="ti ti-alert-circle me-2"></i>
            </div>
            <div>{{ session('error') }}</div>
        </div>

        <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger" role="alert">
        <div class="d-flex">
            <div>
                <i class="ti ti-alert-triangle me-2"></i>
            </div>
            <div>
                <strong>Validasi gagal:</strong>
                <ul class="mb-0 mt-2">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
@endif
