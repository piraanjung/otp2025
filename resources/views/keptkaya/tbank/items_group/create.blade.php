@extends('layouts.keptkaya')

@section('content')

    <div class="card card-info">
        <div class="card-header">
            <h3 class="card-title">Horizontal Form</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('keptkayas.tbank.items_group.store') }}" class="form-horizontal" method="post">
                @csrf

                {{-- Container for dynamic input fields --}}
                <div id="item-group-fields">
                    {{-- Initial field --}}
                    <div class="mb-3 input-group-row">
                        <label for="kp_items_groupname_0" class="form-label">Group Name 1</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="kp_items_groupname_0" name="kp_items_groupname[]"
                                value="{{ old('kp_items_groupname.0') }}" required>
                            <button type="button" class="btn btn-outline-danger remove-field"
                                style="display:none;">Remove</button>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <button type="button" class="btn btn-info" id="add-field-button">Add Another Group Name</button>
                </div>

                {{-- Global status and deleted checkboxes (or you can add them per field) --}}
                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="status" name="status" value="1" {{ old('status', 1) ? 'checked' : '' }}>
                    <label class="form-check-label" for="status">Status (Active)</label>
                </div>
                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="deleted" name="deleted" value="1" {{ old('deleted', 0) ? 'checked' : '' }}>
                    <label class="form-check-label" for="deleted">Deleted</label>
                </div>

                <button type="submit" class="btn btn-success">Create Item Group</button>
                <a href="{{ route('keptkayas.tbank.items_group.index') }}" class="btn btn-secondary">Back to List</a>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const addFieldButton = document.getElementById('add-field-button');
            const itemGroupFields = document.getElementById('item-group-fields');
            let fieldCount = 1; // Start counting from 1 for new fields

            function updateRemoveButtons() {
                const removeButtons = document.querySelectorAll('.remove-field');
                if (fieldCount === 1) { // If only one field, hide its remove button
                    document.querySelector('.input-group-row .remove-field').style.display = 'none';
                } else { // Show all remove buttons if more than one field
                    removeButtons.forEach(button => {
                        button.style.display = 'inline-block';
                    });
                }
            }

            // Initial call to set button visibility
            updateRemoveButtons();

            addFieldButton.addEventListener('click', function () {
                const newFieldHtml = `
                            <div class="mb-3 input-group-row">
                                <label for="kp_items_groupname_${fieldCount}" class="form-label">Group Name ${fieldCount + 1}</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="kp_items_groupname_${fieldCount}" name="kp_items_groupname[]" required>
                                    <button type="button" class="btn btn-outline-danger remove-field">Remove</button>
                                </div>
                            </div>
                        `;
                itemGroupFields.insertAdjacentHTML('beforeend', newFieldHtml);
                fieldCount++;
                updateRemoveButtons(); // Update visibility of remove buttons
            });

            // Event delegation for remove buttons
            itemGroupFields.addEventListener('click', function (event) {
                if (event.target.classList.contains('remove-field')) {
                    if (fieldCount > 1) { // Prevent removing the last field
                        event.target.closest('.input-group-row').remove();
                        fieldCount--;
                        // Re-index labels (optional, but good for clarity)
                        document.querySelectorAll('#item-group-fields .input-group-row').forEach((row, index) => {
                            row.querySelector('label').textContent = `Group Name ${index + 1}`;
                            row.querySelector('input').id = `kp_items_groupname_${index}`;
                        });
                        updateRemoveButtons(); // Update visibility after removal
                    }
                }
            });
        });
    </script>
@endsection