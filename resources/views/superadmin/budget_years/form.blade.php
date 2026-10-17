<div>
    <label for="year">Year:</label><br>
    <input type="text" class="form-control" id="year" name="year" value="{{ old('year', $budgetYear->year ?? '') }}" required pattern="[0-9]{4}" title="Please enter a 4-digit year">
</div>
<br>

<div>
    <label for="start_date">Start Date:</label><br>
    <input type="date" class="form-control" id="start_date" name="start_date" value="{{ old('start_date', $budgetYear->start_date ? $budgetYear->start_date->format('Y-m-d') : '') }}" required>
</div>
<br>

<div>
    <label for="end_date">End Date:</label><br>
    <input type="date" class="form-control" id="end_date" name="end_date" value="{{ old('end_date', $budgetYear->end_date ? $budgetYear->end_date->format('Y-m-d') : '') }}" required>
</div>
<br>

<div>
    <label for="is_active" class=""> Is Active</label>
    <select id="is_active" name="is_active" class="form-control">

        <option value="1" {{ $budgetYear->is_active ==1 || collect($budgetYear)->isEmpty() ? 'checked' : '' }}>Active</option>
        <option value="0" {{ $budgetYear->is_active ==0 ? 'checked' : '' }}>Inactive</option>
    </select>
</div>
<br>