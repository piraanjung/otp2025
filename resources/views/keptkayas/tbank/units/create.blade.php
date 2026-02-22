@extends('layouts.keptkaya')
@section('page-topic', 'หน่วยนับสินค้า')
@section('nav-header', ' หน่วยนับสินค้า')
@section('nav-current', 'สร้างข้อมูลหน่วยนับสินค้า')
@section('content')

    <div class="card">
        <div class="card-body">
            <div class="container">
                <h3>สร้างหน่วยนับสินค้า</h3>


                <form action="{{ route('keptkayas.tbank.units.store') }}" method="POST">
                    @csrf

                    {{-- Container for dynamic input fields --}}
                    <div id="unit-fields">
                        {{-- Initial field --}}
                        <div class="mb-3 input-group-row border p-3 rounded mb-3"> {{-- เพิ่ม border, padding, rounded --}}
                            <h5 class="mb-3">หน่วยนับสินค้า 1</h5>
                            <div class="row">
                                <div class="col-4">
                                    <label for="unitname_0" class="form-label">ชื่อหน่วยนับ</label>
                                    <input type="text" class="form-control" id="unitname_0" name="unitname[0][unitname]"
                                        value="{{ old('unitname_0') }}" required>
                                </div>
                                <div class="col-4">
                                    <label for="unit_short_name_0" class="form-label">ชื่อย่อหน่วยนับ</label>
                                    <input type="text" class="form-control" id="unit_short_name_0"
                                        name="unitname[0][unit_short_name]" value="{{ old('unit_short_name.0') }}">
                                </div>
                                <div class="col-2">
                                    <label for="unit_short_name_0" class="form-label">&nbsp;</label>
                                    <button type="button" class="btn btn-outline-danger remove-field form-control"
                                        style="display:none; border: 1px solid red;">ลบ</button>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="mb-3">
                        <button type="button" class="btn btn-info" id="add-field-button">เพื่อหน่วยอื่นๆ</button>
                    </div>

                    {{-- Global status and deleted checkboxes --}}
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="status" name="status" value="1" {{ old('status', 1) ? 'checked' : '' }}>
                        <label class="form-check-label" for="status">สถานะ (Active)</label>
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="deleted" name="deleted" value="1" {{ old('deleted', 0) ? 'checked' : '' }}>
                        <label class="form-check-label" for="deleted">ลบ</label>
                    </div>

                    <button type="submit" class="btn btn-success">บันทึกข้อมูล</button>
                </form>
            </div>
        </div>
    </div>



@endsection

@section('script')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const addFieldButton = document.getElementById('add-field-button');
            const unitFields = document.getElementById('unit-fields');
            let fieldCount = 1; // Start counting from 1 for new fields

            function updateRemoveButtons() {
                const removeButtons = document.querySelectorAll('.remove-field');
                if (fieldCount === 1) { // If only one field, hide its remove button
                    document.querySelector('.input-group-row .row .remove-field').style.display = 'none';
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
                        <div class="mb-3 input-group-row border p-3 rounded mb-3">
                            <h5 class="mb-3">หน่วยนับสินค้า ${fieldCount + 1}</h5>
                            <div class="row">
                                <div class="col-4">
                                    <label for="unitname_${fieldCount}" class="form-label">ชื่อหน่วยนับ</label>
                                    <input type="text" class="form-control" id="unitname_${fieldCount}" name="unitname[${fieldCount}][unitname]" required>
                                </div>
                                <div class="col-4">
                                    <label for="unit_short_name_${fieldCount}" class="form-label">ชื่อย่อหน่วยนับ</label>
                                    <input type="text" class="form-control" id="unit_short_name_${fieldCount}" name="unitname[${fieldCount}][unit_short_name]">
                                </div>
                                <div class="col-2">
                                    <label for="unit_short_name_${fieldCount}" class="form-label">&nbsp;</label>
                                    <button type="button" class="btn btn-outline-danger remove-field form-control">ลบ</button>
                                </div>
                            </div>
                        </div>
                    `;
                unitFields.insertAdjacentHTML('beforeend', newFieldHtml);
                fieldCount++;
                updateRemoveButtons(); // Update visibility of remove buttons
            });

            // Event delegation for remove buttons
            unitFields.addEventListener('click', function (event) {
                if (event.target.classList.contains('remove-field')) {
                    if (fieldCount > 1) { // Prevent removing the last field
                        event.target.closest('.input-group-row').remove();
                        fieldCount--;
                        // Re-index labels and titles (optional, but good for clarity)
                        document.querySelectorAll('#unit-fields .input-group-row').forEach((row, index) => {
                            row.querySelector('h5').textContent = `Unit Entry ${index + 1}`;
                            row.querySelector('[name="unitname[]"]').id = `unitname_${index}`;
                            row.querySelector('[for^="unitname_"]').setAttribute('for', `unitname_${index}`);
                            row.querySelector('[name="unit_short_name[]"]').id = `unit_short_name_${index}`;
                            row.querySelector('[for^="unit_short_name_"]').setAttribute('for', `unit_short_name_${index}`);
                        });
                        updateRemoveButtons(); // Update visibility after removal
                    }
                }
            });
        });
    </script>
@endsection