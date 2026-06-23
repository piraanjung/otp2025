@extends('layouts.admin1')

@section('nav-header', 'รายงาน')
@section('nav-main')
    <a href="{{ route('reports.water_used') }}">ปริมาณการใช้น้ำประปา</a>
@endsection
@section('nav-topic', 'ข้อมูลปริมาณการใช้น้ำประปา')
@section('report-water_used', 'active')

@section('style')
    <style>
        .table-responsive { overflow-x: auto; }
        /* ปรับสี Footer ให้เด่นชัด */
        tfoot th { background-color: #f4f6f9; border-top: 2px solid #dee2e6 !important; }
    </style>
    <script src="{{ asset('js/chartjs/chart.js_2.7.1.js') }}"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">
@endsection

@section('content')
    {{-- 1. Search Form --}}
    <div class="card mb-3">
        <div class="card-body">
            <form action="{{ route('reports.water_used') }}" method="GET">
                {{-- @csrf ไม่จำเป็นสำหรับ GET request แต่ใส่ไว้ก็ไม่เสียหาย --}}
                <div class="row align-items-end">
                    <div class="col-md-2">
                        <label class="form-label">ปีงบประมาณ</label>
                        <select class="form-control" name="budgetyear_id" id="budgetyear_id">
                            <option value="all">ทั้งหมด</option>
                            {{-- ใช้ Loop หรือ Logic selected ที่นี่ --}}
                            <option value="1" selected>2567</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">หมู่ที่</label>
                        <select class="form-control" name="zone_id" id="zone_id">
                            <option value="all" selected>ทั้งหมด</option>
                            @for($i=1; $i<=12; $i++)
                                <option value="{{ $i }}">หมู่ {{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">เส้นทาง</label>
                        <select class="form-control" name="subzone_id" id="subzone_id">
                            <option value="all" selected>ทั้งหมด</option>
                            {{-- Options จะถูกเติมด้วย AJAX --}}
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search"></i> ค้นหา
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- 2. Chart Section --}}
    <div class="card mb-3">
        <div class="card-body">
            <div style="position: relative; height:40vh; width:100%">
                <canvas id="barChart"></canvas>
            </div>
            <script>
                var ctx = document.getElementById('barChart').getContext('2d');
                var myChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: @json($data['labels']),
                        datasets: [{
                            label: 'ปริมาณการใช้น้ำแยกตามหมู่บ้าน',
                            data: @json($data['data']),
                            borderColor: '#4dc9f6',
                            backgroundColor: '#4dc9f6',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            yAxes: [{ ticks: { beginAtZero: true } }] // Syntax Chart.js 2.x
                        }
                    }
                });
            </script>
        </div>
    </div>

    {{-- 3. Data Table Section --}}
    <div class="card">
        <div class="card-header bg-light">
            <h5 class="mb-0">
                ตารางสรุปปริมาณการใช้น้ำ
                {{ $zone_and_subzone_selected_text == 'ทั้งหมด' ? 'หมู่ที่ 1 - 12' : $zone_and_subzone_selected_text }}
                ปีงบประมาณ {{ $selected_budgetYear->budgetyear_name }}
            </h5>
        </div>
        <div class="card-body">
            @php
                // เตรียมข้อมูลสำหรับ colspan
                $periodColumns = $waterUsedDataTables[0]['classify_by_inv_period'];
                $countPeriod = count($periodColumns);
            @endphp

            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover text-nowrap" id="waterTable" style="width:100%">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th class="text-center" style="vertical-align: middle;">หมู่ที่</th>
                            @foreach ($periodColumns as $item)
                                <th class="text-center">{{ $item['inv_p_name'] }}</th>
                            @endforeach
                            <th class="text-center bg-info" style="vertical-align: middle;">รวม (หน่วย)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($waterUsedDataTables as $data)
                            <tr>
                                <td>{{ $data['zone_name'] }}</td>
                                
                                {{-- Loop แสดงข้อมูลรายเดือน --}}
                                @foreach ($data['classify_by_inv_period'] as $item)
                                    <td class="text-end sum-col">
                                        {{ number_format($item['water_used']) }}
                                    </td>
                                @endforeach

                                {{-- คอลัมน์รวมสุดท้าย --}}
                                <td class="text-end font-weight-bold sum-col bg-light">
                                    {{ number_format($data['water_used']) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th class="text-end font-weight-bold">รวมทั้งสิ้น</th>
                            {{-- สร้าง Footer เปล่าๆ รอให้ JS มาเติมตัวเลข --}}
                            @foreach ($periodColumns as $item)
                                <th class="text-end font-weight-bold text-primary"></th>
                            @endforeach
                            <th class="text-end font-weight-bold text-success bg-light"></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('script')
    {{-- Libraries --}}
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>

    <script>
        $(document).ready(() => {
            
            // 1. Setup DataTable
            let table = $('#waterTable').DataTable({
                ordering: false, // ปิด sort เพื่อรักษาลำดับตาม Query
                paging: false,   // แสดงหน้าเดียว
                searching: false,
                info: false,
                dom: 'Bfrtip',   // Layout ของปุ่ม
                buttons: [
                    {
                        extend: 'excelHtml5',
                        text: '<i class="fas fa-file-excel"></i> Excel',
                        className: 'btn btn-success btn-sm',
                        title: 'รายงานปริมาณการใช้น้ำ',
                        footer: true // เอา Footer (ผลรวม) ไปด้วย
                    }, 
                    {
                        extend: 'print',
                        text: '<i class="fas fa-print"></i> Print',
                        className: 'btn btn-info btn-sm',
                        footer: true,
                        customize: function (win) {
                            $(win.document.body).css('font-size', '10pt');
                            $(win.document.body).find('table').addClass('compact').css('font-size', 'inherit');
                        }
                    }
                ],
                // 2. Footer Callback (คำนวณผลรวมอัตโนมัติ)
                footerCallback: function (row, data, start, end, display) {
                    let api = this.api();

                    // ฟังก์ชันแปลงค่า string เป็น int (ตัด comma ออก)
                    let intVal = function (i) {
                        return typeof i === 'string' ?
                            i.replace(/[\$,]/g, '') * 1 :
                            typeof i === 'number' ? i : 0;
                    };

                    // วนลูปทุกคอลัมน์ตั้งแต่อันที่ 1 (ข้ามชื่อหมู่) จนถึงคอลัมน์สุดท้าย
                    api.columns('.sum-col').every(function () {
                        let sum = this
                            .data()
                            .reduce((a, b) => intVal(a) + intVal(b), 0);
                        
                        // ใส่ค่าลงใน Footer พร้อม format ตัวเลข
                        $(this.footer()).html(new Intl.NumberFormat('en-US').format(sum));
                    });
                }
            });

            // ปรับ Style ปุ่ม DataTables นิดหน่อย
            $('.dt-buttons button').removeClass('dt-button');

            // 3. Setup AJAX Dropdown (Zone -> Subzone)
            $('#zone_id').change(function () {
                let zoneId = $(this).val();
                if(zoneId === 'all') {
                    $('#subzone_id').html('<option value="all" selected>ทั้งหมด</option>');
                    return;
                }

                // ใช้ url() ของ blade เพื่อความชัวร์ของ path
                let url = "{{ url('api/subzone/getSubzone') }}/" + zoneId;

                $.get(url)
                    .done(function (data) {
                        let text = '<option value="all" selected>ทั้งหมด</option>';
                        data.forEach(element => {
                            text += `<option value="${element.id}">${element.subzone_name}</option>`;
                        });
                        $('#subzone_id').html(text);
                    })
                    .fail(function() {
                        console.error("Error loading subzones");
                    });
            }); 
        });
    </script>
@endsection