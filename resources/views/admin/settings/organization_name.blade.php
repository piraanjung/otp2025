<!-- general form elements -->
<div class="card card-primary">
    <div class="card-body">
        <div class="row">
            <div class="form-group col-12 col-md-6">
                <label for="organization_name">ชื่อองค์กร</label>
                <input type="text" class="form-control" id="organization_name" name="organization_name"
                    value="{{ isset($organizations['organization_name']) ? $organizations['organization_name'] : 'เทศบาลตำบลขามป้อม' }}"
                    placeholder="เทศบาลตำบลขามป้อม">
            </div>
            <div class="form-group col-12 col-md-6">
                <label for="organization_short_name">ชื่อย่อองค์กร</label>
                <input type="text" class="form-control" id="organization_short_name" name="organization_short_name"
                    value="{{ isset($organizations['organization_short_name']) ? $organizations['organization_short_name'] : 'ทต.ขามป้อม' }}"
                    placeholder="ทต.ขามป้อม">
            </div>

            <div class="form-group col-12 col-md-6">
                <label for="department_name">ชื่อหน่วยงาน</label>
                <input type="text" class="form-control" id="department_name" name="department_name"
                    value="{{ isset($organizations['department_name']) ? $organizations['department_name'] : 'งานประปาเทศบาลตำบลขามป้อม' }}"
                    placeholder="งานประปาเทศบาลตำบลขามป้อม">
            </div>
            <div class="form-group col-12 col-md-6">
                <label for="department_short_name">ชื่อย่อหน่วยงาน</label>
                <input type="text" class="form-control" id="department_short_name" name="department_short_name"
                    value="ปป.ขามป้อม" placeholder="ปป.ขามป้อม">
            </div>
            <div class="form-group col-12 col-md-6">
                <label for="department_phone">เบอโทรร์ติดต่อหน่วยงาน</label>
                <input type="text" class="form-control" id="department_phone" name="department_phone"
                    value="0984567854">
            </div>
        </div>
    </div>
    <!-- /.card-body -->

</div>
<!-- /.card -->