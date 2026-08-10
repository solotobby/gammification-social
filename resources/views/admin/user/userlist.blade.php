@extends('layouts.admin')

@section('styles')
    @include('admin.partials.dash-styles')
@endsection

@section('content')
    <div class="content p-0">
        <div class="dash">
            <header class="dash-header">
                <div>
                    <h1>Users</h1>
                    <p>Manage accounts, levels, and verification status</p>
                </div>
                <a href="{{ route('admin.home') }}" class="dash-btn dash-btn--ghost">
                    <i class="fa fa-arrow-left"></i> Dashboard
                </a>
            </header>

            @if (session('success'))
                <div class="dash-alert dash-alert--success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="dash-alert dash-alert--error">{{ session('error') }}</div>
            @endif

            @include('admin.partials.users-table', [
                'users' => $users,
                'level' => $level,
                'levelTabs' => $levelTabs,
            ])
        </div>
    </div>
@endsection
