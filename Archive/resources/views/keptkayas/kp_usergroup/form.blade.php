            <div class="col-sm-12 col-md-10">
                <div class="form-row">
                    <div class="form-group col-md-4">
                        {{-- {{dd($tabwatermeter)}} --}}
                        <strong class="text-muted d-block mb-2">ชื่อประเภทมิเตอร์</strong>
                        <input type="text" class="form-control" id="typemetername"
                            value="{{$mode=='edit' ? $tabwatermeter->typemetername : ''}}"
                            placeholder="ตัวอย่าง : ประปาหมู่บ้าน" name="typemetername" required>
                    </div>
                    <div class="form-group col-md-3">
                        <strong class="text-muted d-block mb-2">ขนาดมิเตอร์(หน่วย:นิ้ว)</strong>
                        <input type="text" class="form-control" id="metersize" 
                            value="{{$mode=='edit' ? $tabwatermeter->metersize : ''}}"
                            placeholder="ตัวอย่าง : 12"  name="metersize" required>
                    </div>
                    <div class="form-group col-md-3">
                        <strong class="text-muted d-block mb-2">ราคาต่อหน่วย</strong>
                        <input type="text" class="form-control" id="price_per_unit" 
                            value="{{$mode=='edit' ? $tabwatermeter->price_per_unit : ''}}"
                            placeholder="ตัวอย่าง : 8.25"   name="price_per_unit" required>
                    </div>
                    <div class="form-group col-md-2">
                        <strong class="text-muted d-block mb-2">&nbsp;</strong>
                        <button type="submit" class="btn {{$mode=='edit' ?  'btn-warning' : 'btn-primary'}}">บันทึก</button>
                    </div>
                </div>
            </div>