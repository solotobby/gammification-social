@extends('layouts.admin')

@section('content')
    <div class="content content-full content-boxed">
        @if (session('success'))
            <div class="alert alert-success" role="alert">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger" role="alert">
                {{ session('error') }}
            </div>
        @endif

        <div class="alert alert-info d-flex align-items-center justify-content-between" role="alert">
            <div class="flex-fill me-3">
                <p class="mb-0">Manage the trends that users can engage with on the platform. You can add new trends,
                    activate or deactivate existing ones to keep the content fresh and relevant.</p>
            </div>
            <div class="ms-3">
                <i class="fa fa-info-circle fa-2x"></i>
            </div>
        </div>


        <!-- Dynamic Table with Export Buttons -->
        <div class="block block-rounded">
           
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title">Add New Trend</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('trend.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="name" class="form-label">Trend Title</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Trend Description (optional)</label>
                            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Add Trend</button>
                    </form>
                </div>
            </div>



            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title">Existing Trends</h5>
                </div>
                <div class="card-body">
                    <table id="trends-table" class="table table-bordered table-striped table-vcenter js-dataTable-buttons">
                        <thead>
                            <tr>
                                {{-- <th class="text-center" style="width: 80px;">ID</th> --}}
                                <th>Title</th>
                                <th>Description</th>
                                <th>Status</th>
                                <th>Created At</th>
                                <th style="width: 15%;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($trends as $trend)
                                <tr>
                                    {{-- <td class="text-center">{{ $trend->id }}</td> --}}
                                    <td>{{ $trend->name }}</td>
                                    <td>{{ Str::limit($trend->description, 50) }}</td>
                                    <td>{{ $trend->status }}</td>
                                    <td>{{ $trend->created_at->format('Y-m-d') }}</td>
                                    
                                    <td>
                                        <!-- Action buttons (change status) -->
                                        <a href="{{ route('admin.trends.toggleStatus', $trend->id) }}"
                                            class="btn btn-sm {{ $trend->status === 'active' ? 'btn-success' : 'btn-warning' }}">
                                            {{ $trend->status === 'active' ? 'Deactivate' : 'Activate' }}
                                        </a>
                                        {{-- <a href="{{ route('admin.trends.edit', $trend->id) }}" class="btn btn-sm btn-primary">Edit</a> --}}
                                        {{-- <form action="{{ route('admin.trends.destroy', $trend->id) }}" method="POST" style="display:inline-block;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this trend?')">Delete</button>
                                        </form> --}}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>


                </div>
            </div>
        </div>
    @endsection
