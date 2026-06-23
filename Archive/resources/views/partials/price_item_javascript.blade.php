{{-- partials/price_item_javascript.blade.php --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const itemsContainer = document.getElementById('items-container');
        const addItemBtn = document.getElementById('add-item-btn');
        const itemTemplate = document.getElementById('item-block-template');
        let itemIndex = itemsContainer.querySelectorAll('.item-block').length;
        const tempIndexPlaceholder = '999'; // Index ที่ใช้ใน Template

        // --- Template สำหรับ Unit Tier ใหม่ ---
        function getNewUnitTemplate(itemIdx, unitIdx) {
            return `
                <div class="row g-3 align-items-end unit-item mb-3" data-unit-index="${unitIdx}">
                    <div class="col-md-3">
                        <label class="form-label">หน่วยนับ:</label>
                        <select name="items_data[${itemIdx}][units_data][${unitIdx}][kp_units_idfk]" class="form-select" required>
                            <option value="">เลือกหน่วยนับ</option>
                            ${unitOptionsHtml}
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">ราคาจ่ายให้สมาชิก:</label>
                        <input type="number" step="0.01" name="items_data[${itemIdx}][units_data][${unitIdx}][price_for_member]" class="form-control" value="0" required min="0">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">ราคาจากร้านรับซื้อ:</label>
                        <input type="number" step="0.01" name="items_data[${itemIdx}][units_data][${unitIdx}][price_from_dealer]" class="form-control" value="0" required min="0">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">คะแนน:</label>
                        <input type="number" name="items_data[${itemIdx}][units_data][${unitIdx}][point]" class="form-control" value="0" min="0">
                    </div>
                    <div class="col-md-1 d-flex align-items-end">
                        <button type="button" class="btn btn-danger btn-sm remove-unit-btn"><i class="fa fa-trash"></i></button>
                    </div>
                </div>
            `;
        }

        // --- Logic สำหรับ Checkbox "ตลอดไป" ---
        function toggleEndDate(checkbox) {
            const itemBlock = checkbox.closest('.item-block');
            const endDateInput = itemBlock.querySelector('.item-end-date');
            
            if (checkbox.checked) {
                endDateInput.disabled = true;
                endDateInput.removeAttribute('required');
                // ไม่ต้องล้าง value ที่นี่ แต่จะไปล้างตอน submit เพื่อให้ PHP รับค่า null
            } else {
                endDateInput.disabled = false;
                endDateInput.setAttribute('required', 'required');
            }
        }
        
        // --- Logic สำหรับ Re-indexing (สำคัญหลังการลบ) ---
        function updateItemIndexes() {
            itemsContainer.querySelectorAll('.item-block').forEach((block, index) => {
                const oldIndex = block.getAttribute('data-index');

                // 1. อัปเดต Label และ Index หลัก
                block.querySelector('.item-index-label').textContent = index + 1;
                block.setAttribute('data-index', index);

                // 2. อัปเดต Name และ ID Attribute
                block.querySelectorAll('[name*="items_data"], [id*="items_"]').forEach(field => {
                    const oldName = field.name || field.id;
                    if (oldName) {
                        // แทนที่ Index เก่าในชื่อด้วย Index ใหม่
                        const newName = oldName.replace(`[${oldIndex}]`, `[${index}]`).replace(`items_${oldIndex}_`, `items_${index}_`);
                        if (field.name) field.name = newName;
                        if (field.id) field.id = newName;
                    }
                });

                // 3. Re-index Unit Tiers ภายใน Block
                let unitIdxCounter = 0;
                block.querySelectorAll('.unit-item').forEach(unitRow => {
                    const unitRowIndex = unitRow.getAttribute('data-unit-index');
                    unitRow.setAttribute('data-unit-index', unitIdxCounter);
                    
                    unitRow.querySelectorAll('[name*="units_data"]').forEach(field => {
                        const oldName = field.name;
                        if (oldName) {
                            // แทนที่ Unit Index เก่าด้วย Unit Index ใหม่
                            const tempName = oldName.replace(`[${index}]`, `[${index}]`); // Index Item หลัก
                            field.name = tempName.replace(`[units_data][${unitRowIndex}]`, `[units_data][${unitIdxCounter}]`);
                        }
                    });
                    unitIdxCounter++;
                });
            });
            itemIndex = itemsContainer.querySelectorAll('.item-block').length;
        }

        // --- Event Listener หลัก ---

        // A. เพิ่ม Item Block ใหม่
        addItemBtn.addEventListener('click', function() {
            const newBlock = itemTemplate.content.cloneNode(true);
            const blockDiv = newBlock.querySelector('.item-block');
            
            // แทนที่ Placeholder Index (999) ด้วย Index ใหม่
            let htmlContent = blockDiv.outerHTML;
            htmlContent = htmlContent.replace(new RegExp(tempIndexPlaceholder, 'g'), itemIndex);

            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = htmlContent;
            
            itemsContainer.appendChild(tempDiv.firstChild);

            itemIndex++;
            updateItemIndexes();
            toggleEndDate(itemsContainer.lastElementChild.querySelector('.is-forever-active-checkbox')); // ตั้งค่าเริ่มต้น
        });

        // B. จัดการ Event Delegation (ลบ Item Block, เพิ่ม/ลบ Unit Tier, Checkbox)
        itemsContainer.addEventListener('click', function(e) {
            const target = e.target.closest('button');

            if (!target) return; // ไม่ใช่ปุ่ม

            // 1. ลบ Item Block
            if (target.classList.contains('remove-item-btn')) {
                const itemBlock = target.closest('.item-block');
                if (itemsContainer.querySelectorAll('.item-block').length > 1) {
                    itemBlock.remove();
                    updateItemIndexes();
                } else {
                    alert('ต้องมีรายการขยะอย่างน้อย 1 รายการ');
                }
            } 
            
            // 2. เพิ่ม Unit Tier
            else if (target.classList.contains('add-unit-btn-per-item')) {
                const itemBlock = target.closest('.item-block');
                const itemBlockIndex = itemBlock.getAttribute('data-index');
                const unitsContainer = itemBlock.querySelector('.units-container');
                let unitIdx = unitsContainer.querySelectorAll('.unit-item').length;

                unitsContainer.insertAdjacentHTML('beforeend', getNewUnitTemplate(itemBlockIndex, unitIdx));
            } 
            
            // 3. ลบ Unit Tier
            else if (target.classList.contains('remove-unit-btn')) {
                const unitRow = target.closest('.unit-item');
                const unitsContainer = unitRow.closest('.units-container');
                if (unitsContainer.querySelectorAll('.unit-item').length > 1) {
                    unitRow.remove();
                    // ไม่ต้อง Re-index Unit Tier ภายในนี้ เพราะเราใช้ Index ใหม่เสมอเมื่อเพิ่ม
                } else {
                    alert('ต้องมีหน่วยนับอย่างน้อย 1 รายการ');
                }
            }
        });

        itemsContainer.addEventListener('change', function(e) {
            // 4. Checkbox Toggler
            if (e.target.classList.contains('is-forever-active-checkbox')) {
                toggleEndDate(e.target);
            }
        });
        
        // C. Pre-submission Cleanup (เพื่อให้ PHP รับค่า null ได้)
        const form = document.querySelector('form');
        if (form) {
            form.addEventListener('submit', function() {
                itemsContainer.querySelectorAll('.item-block').forEach(block => {
                    const checkbox = block.querySelector('.is-forever-active-checkbox');
                    const endDateInput = block.querySelector('.item-end-date');
                    
                    if (checkbox && checkbox.checked) {
                         // ถ้า 'ตลอดไป' ถูกเลือก: Re-enable และล้างค่าวันที่
                        endDateInput.disabled = false;
                        endDateInput.value = '';
                    } else if (endDateInput.disabled) {
                        // กรณีที่ Checkbox ถูกยกเลิก แต่ field ยัง disabled (ควรไม่เกิด แต่เพื่อความชัวร์)
                        endDateInput.disabled = false; 
                    }
                });
            });
        }
        
        // Initial setup
        updateItemIndexes();
        itemsContainer.querySelectorAll('.is-forever-active-checkbox').forEach(toggleEndDate);
    });
</script>