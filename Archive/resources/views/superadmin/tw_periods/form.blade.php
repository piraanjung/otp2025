<div class="w-25">
    <label for="budgetyear_id">Budget Year:</label>

    <input type="text" class="form-control readonly" readonly id="budgetyear_name" name="budgetyear_name"
        value="{{ old('budgetyear_name', $currentBudgetYear->budgetyear_name ?? '') }}" required>
    <input type="hidden" id="budgetyear_id" name="budgetyear_id"
        value="{{ old('budgetyear_id', $currentBudgetYear->id ?? '') }}" required>
</div>
<div class="row">

    <div class="col-12 col-md-6">
        <label for="month_select">Select Month:</label>
        @php
            $current_month_index = date("n") - 1;
            if(collect($currentBudgetYear->twPeriods)->isEmpty()){
                $current_month_index = 0;
            }
            $months = App\Http\Controllers\FunctionsController::getTwPeriodLists();
        @endphp
        <select class="form-control" id="month_select" required>
            <option value="">Select Month</option>
            @foreach ($months as $index => $month) {{-- <-- ใช้ $months ที่ส่งมาจาก Controller --}} 
                @php
                    $current_year = date('Y') + 543;
                    if (collect($currentBudgetYear->twPeriods)->isEmpty() && in_array($index, ['ตุลาคม', 'พฤศจิกายน', 'ธันวาคม'])) {
                        $current_year--;
                    }
                @endphp 
                @if($index >= $current_month_index) 
                <option
                        value="{{ $month }}">
                        {{ $index }} {{ $current_year }}
                </option>
                @endif
            @endforeach
        </select>
    </div>
    <div class="col-12 col-md-6">
        <label for="month_select">&nbsp;</label>

        <input type="text" class="form-control readonly" readonly id="period_name" name="period_name"
            value="{{ old('period_name', $period->period_name ?? '') }}" required>
    </div>
</div>


<div class="row">
    <div class="col-12 col-md-6">
        <label for="start_date">Start Date:</label><br>
        <input type="date" class="form-control date" id="start_date" name="start_date"
            value="{{ old('start_date', $period->start_date ? $period->start_date->format('Y-m-d') : '') }}" required>
    </div>
    <div class="col-12 col-md-6">
        <label for="end_date">End Date:</label><br>
        <input type="date" class="form-control date" id="end_date" name="end_date"
            value="{{ old('end_date', $period->end_date ? $period->end_date->format('Y-m-d') : '') }}" required>
    </div>
</div>


<div class="w-25">
    <label for="status">Status:</label><br>
    <select class="form-control" id="status" name="status" required>
        <option value="draft" {{ old('status', $period->status ?? '') == 'draft' ? 'selected' : '' }}>Draft</option>
        @if ($from == 'edit')

            <option value="published" {{ old('status', $period->status ?? '') == 'published' ? 'selected' : '' }}>Published
            </option>
            <option value="closed" {{ old('status', $period->status ?? '') == 'closed' ? 'selected' : '' }}>Closed</option>
        @endif
    </select>
</div>
<br>
@section('script')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <script src="{{asset('soft-ui/assets/js/plugins/flatpickr-th.js')}}"></script>
    <script>

        $(document).ready(function () {

            let calendar = flatpickr('.date', {
                "dateFormat": 'd-m-Y',
                "locale": "th",
            });
        })
        $(document).on('change', '#month_select', function () {
            let month_select = parseInt($(this).val()) 
            month_select = month_select < 10 ? `0${month_select}` : month_select
            const d = new Date();
            let year = d.getFullYear() + 543;
            let budgetyear_name = $('#budgetyear_name').val()

            if (month_select >= 10 && month_select <= 12) {
                year = year - 1;
            }
            $('#period_name').val(month_select + '-' + year+'/'+ budgetyear_name)
        });

    </script>
@endsection