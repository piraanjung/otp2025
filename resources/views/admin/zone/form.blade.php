<div class="row">
    <div class="col-md-3 text-end">ชื่อพื้นที่จดมิเตอร์น้ำประปา</div>
    <div class="col-md-4">
        <div class="mb-3">
            <input type="text" class="form-control" value="{{$zone->zone_name}}" id="zone_name" name="zone_name">
        </div>
    </div>
</div>
<div class="row">
        <div class="col-md-3 text-end">ที่ตั้งพื้นที่จดมิเตอร์น้ำประปา</div>
        <div class="col-md-4">
            <div class="mb-3">
                <textarea class="form-control" rows="2" id="location" name="location">{{$zone->location}}</textarea> 
            </div>
        </div>
    </div>
<div class="col-md-7">
    <div class="mb-3">
        <input type="submit" class="{{$formMode =='create' ? 'btn btn-info' : 'btn btn-warning'}} float-end" value="บันทึก"/>
    </div>
</div>

