@extends('layouts.app')

@section('title', 'Admin Panel')

@section('content')
<div style="margin-top: 2rem;">
    <h1 style="font-size: 1.75rem; font-weight: 700; margin-bottom: 1.5rem;">Admin Panel</h1>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem;">
        <div style="background: white; padding: 1.5rem; border-radius: 0.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <h3 style="font-size: 0.875rem; color: #6b7280; margin-bottom: 0.5rem;">Hoş Geldiniz</h3>
            <p style="font-size: 1.25rem; font-weight: 600;">{{ auth()->user()->name }}</p>
            <p style="color: #6b7280; font-size: 0.875rem; margin-top: 0.25rem;">{{ auth()->user()->role->name ?? '-' }}</p>
        </div>

        <div style="background: white; padding: 1.5rem; border-radius: 0.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <h3 style="font-size: 0.875rem; color: #6b7280; margin-bottom: 0.5rem;">Toplam Kullanıcı</h3>
            <p style="font-size: 1.25rem; font-weight: 600;">{{ \App\Models\User::count() }}</p>
        </div>

        <div style="background: white; padding: 1.5rem; border-radius: 0.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <h3 style="font-size: 0.875rem; color: #6b7280; margin-bottom: 0.5rem;">Toplam Rol</h3>
            <p style="font-size: 1.25rem; font-weight: 600;">{{ \App\Models\Role::count() }}</p>
        </div>
    </div>

    <div style="background: white; padding: 1.5rem; border-radius: 0.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-top: 2rem;">
        <h2 style="font-size: 1.25rem; font-weight: 600; margin-bottom: 1rem;">Kullanıcılar</h2>
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="border-bottom: 2px solid #e5e7eb;">
                    <th style="text-align: left; padding: 0.75rem; font-size: 0.875rem; color: #6b7280;">Ad</th>
                    <th style="text-align: left; padding: 0.75rem; font-size: 0.875rem; color: #6b7280;">E-posta</th>
                    <th style="text-align: left; padding: 0.75rem; font-size: 0.875rem; color: #6b7280;">Rol</th>
                </tr>
            </thead>
            <tbody>
                @foreach (\App\Models\User::with('role')->get() as $user)
                <tr style="border-bottom: 1px solid #e5e7eb;">
                    <td style="padding: 0.75rem; font-size: 0.875rem;">{{ $user->name }}</td>
                    <td style="padding: 0.75rem; font-size: 0.875rem;">{{ $user->email }}</td>
                    <td style="padding: 0.75rem; font-size: 0.875rem;">{{ $user->role->name ?? '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
