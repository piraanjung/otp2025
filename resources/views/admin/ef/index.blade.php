@extends('layouts.super-admin')
@section('nav-header', 'จัดการก๊าซเรือนกระจก')
@section('nav-current', 'Emission Factors')
@section('page-topic', 'ฐานข้อมูล Emission Factor (ค่าสัมประสิทธิ์คาร์บอน)')

@section('content')
    <div class="container-fluid">

        <div class="row">
            <div class="col-lg-3 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>{{ $totalCount }}</h3>
                        <p>รายการวัสดุในระบบ</p>
                    </div>
                    <div class="icon"><i class="fas fa-leaf"></i></div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{ number_format($avgEF, 2) }}</h3>
                        <p>ค่า EF เฉลี่ย (kgCO2e/kg)</p>
                    </div>
                    <div class="icon"><i class="fas fa-chart-line"></i></div>
                </div>
            </div>
        </div>

        <div class="card card-default">
            <div class="card-header">
                <h3 class="card-title text-bold"><i class="fas fa-file-excel"></i> เครื่องมือนำเข้า/ส่งออกข้อมูล</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 border-right">
                        <label>ขั้นตอนที่ 1: เตรียมไฟล์</label><br>
                        <a href="{{ route('keptkayas.emission.export') }}" class="btn btn-outline-primary">
                            <i class="fas fa-download"></i> ดาวน์โหลด Template Excel
                        </a>
                        <p class="text-muted mt-2 small">* กรุณากรอกข้อมูลตามรูปแบบตัวอย่างในไฟล์เพื่อป้องกันข้อผิดพลาด</p>
                    </div>
                    <div class="col-md-6">
                        <label>ขั้นตอนที่ 2: อัปโหลดข้อมูล</label>
                        <form action="{{ route('keptkayas.emission.import') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="input-group">
                                <div class="custom-file">
                                    <input type="file" name="file" class="custom-file-input" id="efFile"
                                        accept=".xlsx, .xls" required>
                                    <label class="custom-file-label" for="efFile">เลือกไฟล์ Excel...</label>
                                </div>
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-success">นำเข้าข้อมูล</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="card card-outline card-success">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title text-bold">รายการ Emission Factor ทั้งหมด</h3>
                <div class="card-tools">
                    <a href="{{ route('keptkayas.emission.create') }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-plus"></i> เพิ่มรายการใหม่
                    </a>
                    <button class="btn btn-sm btn-warning" onclick="location.reload()">
                        <i class="fas fa-sync"></i> รีเฟรชข้อมูล
                    </button>
                </div>
            </div>
            <div class="card-body p-0">
                <table class="table table-striped table-hover m-0">
                    <thead>
                        <tr class="bg-light">
                            <th style="width: 50px">#</th>
                            <th>ชื่อวัสดุ (Material Name)</th>
                            <th class="text-center">หน่วย (Unit)</th>
                            <th class="text-center">ค่า EF (kgCO2e)</th>
                            <th>ตัวอย่าง/หมายเหตุ</th>
                            <th>แหล่งที่มา</th>
                            <th style="width: 100px">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($emissionFactors as $ef)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td class="text-bold">{{ $ef->material_name }}</td>
                                <td class="text-center"><span class="badge badge-info">{{ $ef->unit }}</span></td>
                                <td class="text-center text-success text-bold">{{ number_format($ef->ef_value, 4) }}</td>
                                <td><small>{{ $ef->example ?? '-' }}</small></td>
                                <td><small class="text-muted">{{ $ef->source }}</small></td>
                                <td class="text-center">
                                    <div class="btn-group">
                                        <!-- ปุ่มแก้ไข (Edit) -->
                                        <a href="{{ route('keptkayas.emission.edit', $ef->id) }}" class="btn btn-xs btn-default"
                                            title="แก้ไขข้อมูล">
                                            <i class="fas fa-edit text-primary"></i>
                                        </a>

                                        <!-- ปุ่มลบ (Delete) -->
                                        <form action="{{ route('keptkayas.emission.destroy', $ef->id) }}" method="POST"
                                            onsubmit="return confirm('ยืนยันการลบรายการนี้? ข้อมูลที่ถูกลบจะไม่สามารถกู้คืนได้');"
                                            style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-xs btn-default" title="ลบข้อมูล">
                                                <i class="fas fa-trash text-danger"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center p-4">
                                    <img src="https://cdn-icons-png.flaticon.com/512/7486/7486744.png" width="80"
                                        class="mb-2 opacity-50"><br>
                                    <span class="text-muted">ยังไม่มีข้อมูลในระบบ กรุณานำเข้าข้อมูลด้วยไฟล์ Excel</span>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        // แสดงชื่อไฟล์ที่เลือกใน Input
        $('.custom-file-input').on('change', function () {
            let fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').addClass("selected").html(fileName);
        });
    </script>
@endsection
