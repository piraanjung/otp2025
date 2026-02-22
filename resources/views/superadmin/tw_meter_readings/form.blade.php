<div class="mb-3">
    <label for="period_id" class="form-label">Period:</label>
    <select id="period_id" name="period_id" class="form-select @error('period_id') is-invalid @enderror" required>
        <option value="">Select Period</option>
        @foreach ($periods as $periodOption)
            <option value="{{ $periodOption->id }}" {{ old('period_id', $reading->period_id_fk ?? '') == $periodOption->id ? 'selected' : '' }}>
                {{ $periodOption->period_name }} ({{ $periodOption->budgetYear->year ?? 'N/A' }} - Status:
                {{ ucfirst($periodOption->status) }})
            </option>
        @endforeach
    </select>
    @error('period_id')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="meter_id" class="form-label">Meter:</label>
    <select id="meter_id" name="meter_id" class="form-select @error('meter_id') is-invalid @enderror" required>
        <option value="">Select Meter</option>
        @foreach ($meters as $meterOption)
            <option value="{{ $meterOption->id }}" {{ old('meter_id', $reading->meter_id_fk ?? '') == $meterOption->id ? 'selected' : '' }}>
                {{ "hz000" . $meterOption->id }} - {{ $meterOption->user->first_name . " " . $meterOption->user->lastname }}
            </option>
        @endforeach
    </select>
    @error('meter_id')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="reading_date" class="form-label">Reading Date:</label>
    <input type="date" id="reading_date" name="reading_date"
        class="form-control @error('reading_date') is-invalid @enderror"
        value="{{ old('reading_date', $reading->reading_date ? $reading->reading_date->format('Y-m-d') : date('Y-m-d')) }}"
        required>
    @error('reading_date')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="previous_reading_value" class="form-label">Previous Reading Value:</label>
    {{-- Display only for visual confirmation, actual value comes from hidden input or is fetched via JS --}}
    <input type="number" step="0.01" id="previous_reading_value_display" class="form-control form-control-static"
        value="{{ old('previous_reading_value', $reading->previous_reading_value ?? '') }}" readonly>
    {{-- Hidden input to actually send the value --}}
    <input type="hidden" id="previous_reading_value" name="previous_reading_value"
        value="{{ old('previous_reading_value', $reading->previous_reading_value ?? '') }}">
    @error('previous_reading_value')
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
    <div class="form-text">This value is automatically loaded from the meter's current active reading.</div>
</div>


<div class="mb-3">
    <label for="reading_value" class="form-label">Current Reading Value:</label>
    <input type="number" step="0.01" id="reading_value" name="reading_value"
        class="form-control @error('reading_value') is-invalid @enderror"
        value="{{ old('reading_value', $reading->reading_value ?? '') }}" required min="0">
    @error('reading_value')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="comment" class="form-label">Comment:</label>
    <textarea id="comment" name="comment"
        class="form-control @error('comment') is-invalid @enderror">{{ old('comment', $reading->comment ?? '') }}</textarea>
    @error('comment')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const meterSelect = document.getElementById('meter_id');
        const previousReadingValueHidden = document.getElementById('previous_reading_value');
        const previousReadingValueDisplay = document.getElementById('previous_reading_value_display');

        function fetchPreviousReading() {
            const selectedMeterId = meterSelect.value;

            if (selectedMeterId) {
                // Fetch previous reading value from the server
                fetch(`{{ route('superadmin.tw_meter_readings.get_previous_reading') }}?meter_id=${selectedMeterId}`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        // Update both hidden and display inputs
                        previousReadingValueHidden.value = data.previous_reading_value;
                        previousReadingValueDisplay.value = data.previous_reading_value;
                    })
                    .catch(error => {
                        console.error('Error fetching previous reading:', error);
                        previousReadingValueHidden.value = 0.00;
                        previousReadingValueDisplay.value = 0.00;
                    });
            } else {
                previousReadingValueHidden.value = '';
                previousReadingValueDisplay.value = '';
            }
        }

        // Fetch previous reading when meter selection changes (only in create mode or if meter is changed in edit)
        // In edit mode, if $reading->previous_reading_value is already set, don't auto-fetch unless meter_id changes
        if (!'{{ $reading->exists }}' || !previousReadingValueHidden.value) { // Only fetch if creating or previous is empty
            meterSelect.addEventListener('change', fetchPreviousReading);
            // Initial fetch if a meter is already selected (e.g., from old() or query param)
            if (meterSelect.value) {
                fetchPreviousReading();
            }
        }
    });
</script>