@extends('layouts.keptkaya')

@section('content')
  <div class="row mb-3">
    <div class="col-md-6">
      <a href="{{ route('keptkayas.tbank.items.export') }}" class="btn btn-success">Export Items to Excel</a>
    </div>
    <div class="col-md-6">
      <form action="{{ route('keptkayas.tbank.items.import') }}" method="POST" enctype="multipart/form-data"
        class="d-flex">
        @csrf
        <input type="file" name="file" class="form-control me-2" required>
        <button type="submit" class="btn btn-primary">Import Items from Excel</button>
      </form>
    </div>
  </div>
  <div class="card card-info">
    <div class="card-header">
      <h3 class="card-title">Horizontal Form</h3>
    </div>
    <!-- /.card-header -->
    <!-- form start -->
    <form action="{{ route('keptkayas.tbank.items.store') }}" class="form-horizontal" method="post"
      enctype="multipart/form-data">
      @csrf
      <div class="card-body">

        <div class="form-group row">
          <label for="inputEmail3" class="col-sm-2 col-form-label">ชื่อ</label>
          <div class="col-sm-10">
            <input type="text" class="form-control" name="kp_itemsname">
          </div>
        </div>

        <div class="form-group row">
          <label for="inputEmail3" class="col-sm-2 col-form-label">ประเภทขยะ</label>
          <div class="col-sm-10">
            <select name="kp_items_group_idfk" id="" class="form-control">
              <option>เลือก..</option>
              @foreach ($kp_items_groups as $kp_items_group)
                <option value="{{ $kp_items_group->id }}">{{ $kp_items_group->kp_items_groupname }}</option>
              @endforeach
            </select>
          </div>
        </div>
        {{-- <div class="form-group row">
          <label for="inputEmail3" class="col-sm-2 col-form-label">หน่วยนับ</label>
          <div class="col-sm-10">
            <select name="tbank_item_unit_idfk" id="" class="form-control">
              <option>เลือก..</option>
              @foreach ($tbank_item_units as $tbank_item_unit)
              <option value="{{ $tbank_item_unit->id }}">{{ $tbank_item_unit->unitname }}</option>
              @endforeach
            </select>
          </div>
        </div>

        <div class="form-group row">
          <label for="inputEmail3" class="col-sm-2 col-form-label">รหัส</label>
          <div class="col-sm-10">
            <input type="text" class="form-control" name="kp_itemscode">
          </div>
        </div> --}}

        <div class="form-group row">
          <label for="inputEmail3" class="col-sm-2 col-form-label">รูปภาพ</label>
          <div class="col-sm-10">
            <input type="file" class="form-control" name="image">
          </div>
        </div>


      </div>
      <!-- /.card-body -->
      <div class="card-footer">
        <button type="submit" class="btn btn-info float-right">บันทึก</button>
      </div>
      <!-- /.card-footer -->
    </form>
  </div>
@endsection