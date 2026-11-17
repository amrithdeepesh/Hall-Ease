@extends('layouts/contentNavbarLayout')

@section('title', 'General Settings')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-md-3">
            <div class="list-group">
                <a href="{{ route('admin.settings.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('admin.settings.index') || request()->routeIs('admin.settings.general') ? 'active' : '' }}">
                    <i class="bx bx-cog"></i> General
                </a>
                <a href="{{ route('admin.settings.email') }}" class="list-group-item list-group-item-action {{ request()->routeIs('admin.settings.email') ? 'active' : '' }}">
                    <i class="bx bx-envelope"></i> Email
                </a>
                <a href="{{ route('admin.settings.security') }}" class="list-group-item list-group-item-action {{ request()->routeIs('admin.settings.security') ? 'active' : '' }}">
                    <i class="bx bx-lock"></i> Security
                </a>
            </div>
        </div>
        <div class="col-md-9">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">General Settings</h5>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    <form action="{{ route('admin.settings.update') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label" for="app_name">Application Name</label>
                            <input type="text" class="form-control" id="app_name" name="app_name" value="{{ config('app.name', 'Hall-Ease') }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="app_email">System Contact Email</label>
                            <input type="email" class="form-control" id="app_email" name="app_email" value="{{ config('mail.from.address', 'admin@staloysius.edu.in') }}" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Save Settings</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
