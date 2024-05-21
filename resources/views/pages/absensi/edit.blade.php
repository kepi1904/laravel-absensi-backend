<!-- resources/views/pages/absensi/edit.blade.php -->

@extends('layouts.app')

@section('title', 'View Attendance')

@push('style')
    <!-- CSS Libraries -->
    <link rel="stylesheet" href="{{ asset('library/selectric/public/selectric.css') }}">
@endpush

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>View Attendance</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="#">Dashboard</a></div>
                    <div class="breadcrumb-item"><a href="#">Attendances</a></div>
                    <div class="breadcrumb-item">View Attendance</div>
                </div>
            </div>

            <div class="section-body">
                <h2 class="section-title">View Attendance</h2>
                <p class="section-lead">Modify the attendance details below.</p>

                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>View Attendance</h4>
                            </div>
                            <div class="card-body">
                                @if(session('error'))
                                    <div class="alert alert-danger">
                                        {{ session('error') }}
                                    </div>
                                @endif

                                <form action="{{ route('attendances.update', $attendance->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')

                                    <div class="form-group">
                                        <label for="user_name">Name</label>
                                        <input type="text" id="user_name" class="form-control" value="{{ $attendance->user->name }}" disabled>
                                    </div>

                                    <div class="form-group">
                                        <label for="date">Date</label>
                                        <input type="date" name="date" id="date" class="form-control" value="{{ old('date', $attendance->date) }}" required>
                                    </div>

                                    <div class="form-group">
                                        <label for="time_in">Time In</label>
                                        <input type="time" name="time_in" id="time_in" class="form-control" value="{{ old('time_in', $attendance->time_in) }}" required>
                                    </div>

                                    <div class="form-group">
                                        <label for="time_out">Time Out</label>
                                        <input type="time" name="time_out" id="time_out" class="form-control" value="{{ old('time_out', $attendance->time_out) }}">
                                    </div>

                                    <div class="form-group">
                                        <label for="latlon_in">Latlong In</label>
                                        <input type="text" name="latlon_in" id="latlon_in" class="form-control" value="{{ old('latlon_in', $attendance->latlon_in) }}">
                                    </div>

                                    <div class="form-group">
                                        <label for="latlon_out">Latlong Out</label>
                                        <input type="text" name="latlon_out" id="latlon_out" class="form-control" value="{{ old('latlon_out', $attendance->latlon_out) }}">
                                    </div>

                                    {{-- <div class="form-group">
                                        <button type="submit" class="btn btn-primary">Update Attendance</button>
                                    </div> --}}
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    <!-- JS Libraies -->
    <script src="{{ asset('library/selectric/public/jquery.selectric.min.js') }}"></script>
@endpush
