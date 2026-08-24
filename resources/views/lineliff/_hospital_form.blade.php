<div id="hospital_form" class="org_regis_form d-none">
    <div class="form-floating mb-4">
        <input type="text" id="org_display" class="form-control clickable-input" placeholder="เลือกหน่วยงาน..."
            readonly>
        <label id="org_label">ระบุชื่อโรงพยาบาล</label>

        <input type="hidden" name="hospital_org_id" id="hospital_org_id">
        <input type="hidden" name="hospital_province_id" id="hospital_province_id">
        <input type="hidden" name="hospital_district_id" id="hospital_district_id">
        <input type="hidden" name="hospital_tambon_id" id="hospital_tambon_id">
    </div>

    <div id="location_info_display" class=" bg-light p-3 rounded-3 mb-3 border border-light">
        <p class="small text-muted mb-2"><i class="bi bi-geo-alt-fill"></i> ที่ตั้งหน่วยงาน</p>
        <div class="row g-2">
            <div class="col-4">
                <input type="text" id="show_province" class="form-control form-control-sm bg-white border-0" disabled
                    placeholder="จ.">
            </div>
            <div class="col-4">
                <input type="text" id="show_district" class="form-control form-control-sm bg-white border-0" disabled
                    placeholder="อ.">
            </div>
            <div class="col-4">
                <input type="text" id="show_tambon" class="form-control form-control-sm bg-white border-0" disabled
                    placeholder="ต.">
            </div>
        </div>
    </div>
</div>
