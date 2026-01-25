@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-body text-center p-4">
                    <h5 class="card-title mb-3">Pengguna Saat Ini</h5>
                    <p class="display-4 fw-bold text-primary">{{ $totalUsers }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
