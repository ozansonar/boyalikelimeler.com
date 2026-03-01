@extends('layouts.admin')

@section('title', 'Dashboard — Admin')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h4 fw-bold mb-1">Dashboard</h1>
        <p class="text-muted small mb-0">Hoş geldiniz, {{ auth()->user()->name }}</p>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="bg-primary bg-opacity-10 rounded p-3">
                    <i class="bi bi-people fs-4 text-primary"></i>
                </div>
                <div>
                    <div class="text-muted small">Toplam Kullanıcı</div>
                    <div class="fw-bold fs-5">{{ $userCount }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="bg-success bg-opacity-10 rounded p-3">
                    <i class="bi bi-shield-check fs-4 text-success"></i>
                </div>
                <div>
                    <div class="text-muted small">Toplam Rol</div>
                    <div class="fw-bold fs-5">{{ $roleCount }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-bottom">
        <h6 class="fw-semibold mb-0"><i class="bi bi-people me-2 text-primary"></i>Kullanıcılar</h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4 text-muted small fw-semibold">Ad</th>
                        <th class="text-muted small fw-semibold">E-posta</th>
                        <th class="pe-4 text-muted small fw-semibold">Rol</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                    <tr>
                        <td class="ps-4 small">{{ $user->name }}</td>
                        <td class="small">{{ $user->email }}</td>
                        <td class="pe-4 small">
                            <span class="badge bg-secondary">{{ $user->role->name ?? '-' }}</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
