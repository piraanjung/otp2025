@extends('layouts.admin1')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">Record Unrecorded Meters</h1>
        <a href="{{ route('tw_meter_readings.index', ['period_id' => $period->id]) }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back to Readings Summary
        </a>
    </div>



    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">
                Unrecorded Meters for Subzone: {{ $subzone->zone_block_name ?? 'N/A' }} ({{ $subzone->zone->zone_name ?? 'N/A' }})
                <br>
                Period: {{ $period->period_name ?? 'N/A' }} ({{ $period->budgetYear->year ?? 'N/A' }})
            </h5>
        </div>
        <div class="card-body">
            @if ($unrecordedMeters->isEmpty())
                <div class="alert alert-info text-center" role="alert">
                    All meters in this subzone have been recorded for this period.
                </div>
            @else
                <form action="{{ route('tw_meter_readings.store_unrecorded') }}" method="POST">
                    @csrf
                    <input type="hidden" name="period_id" value="{{ $period->id }}">

                    <div class="mb-3">
                        <label for="reading_date" class="form-label">Reading Date for all meters:</label>
                        <input type="date" id="reading_date" name="reading_date" class="form-control @error('reading_date') is-invalid @enderror" value="{{ old('reading_date', date('Y-m-d')) }}" required>
                        @error('reading_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="table-light">
                                <tr>
                                    <th>Meter Number</th>
                                    <th>Customer Name</th>
                                    <th>Meter Type</th>
                                    <th>Previous Reading</th>
                                    <th>Current Reading <span class="text-danger">*</span></th>
                                    <th>Comment</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($unrecordedMeters as $index => $meter)
                                    <tr>
                                        <td>
                                            {{ $meter->meternumber }}
                                            <input type="hidden" name="meters[{{ $index }}][id]" value="{{ $meter->id }}">
                                        </td>
                                        <td>{{ $meter->user->firstname." ".$meter->user->lastname }}</td>
                                        <td>{{ $meter->meterType->meter_type_name ?? 'N/A' }}</td>
                                        <td>
                                            {{ number_format($meter->current_active_reading, 2) }}
                                            <input type="hidden" name="meters[{{ $index }}][previous_reading_value]" value="{{ $meter->current_active_reading }}">
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" name="meters[{{ $index }}][reading_value]"
                                                   class="form-control @error('meters.'.$index.'.reading_value') is-invalid @enderror"
                                                   value="{{ old('meters.'.$index.'.reading_value') }}"  min="{{ $meter->current_active_reading }}">
                                            @error('meters.'.$index.'.reading_value')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </td>
                                        <td>
                                            <input type="text" name="meters[{{ $index }}][comment]"
                                                   class="form-control @error('meters.'.$index.'.comment') is-invalid @enderror"
                                                   value="{{ old('meters.'.$index.'.comment') }}">
                                            @error('meters.'.$index.'.comment')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-end mt-3">
                        <button type="submit" class="btn btn-success me-2">Save All Readings</button>
                        <a href="{{ route('tw_meter_readings.index', ['period_id' => $period->id]) }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            @endif
        </div>
    </div>
</div>
@endsection
