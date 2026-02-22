@extends('layouts.keptkaya') 

@section('content')
<div class="container-fluid py-4 bg-white">
   <div id="print-section"> 
    {{-- Header รายงาน --}}
    <div class="text-center mb-4">
        <h5>{{ $title }}</h5>
        <p class="text-sm text-secondary">พิมพ์เมื่อ: {{ \Carbon\Carbon::now()->addYears(543)->format('d/m/Y H:i') }}</p>
        
        <div class="no-print mt-3">
            <button onclick="window.print()" class="btn btn-outline-dark btn-sm"><i class="fas fa-print"></i> พิมพ์</button>
            <button onclick="exportTableToExcel('reportTable')" class="btn btn-outline-success btn-sm"><i class="fas fa-file-excel"></i> Excel</button>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered align-items-center mb-0" id="reportTable">
            
            {{-- CASE 1: รายงานรับเงินประจำวัน --}}
            @if($type == 'daily_collection')
                <thead>
                    <tr class="bg-gray-100">
                        <th class="text-center">ลำดับ</th>
                        <th>เวลา</th>
                        <th>ใบเสร็จเลขที่</th>
                        <th>ผู้ชำระเงิน</th>
                        <th>รหัสถัง</th>
                        <th class="text-end">จำนวนเงิน</th>
                    </tr>
                </thead>
                <tbody>
                    @php $total = 0; @endphp
                    @forelse($data as $key => $row)
                        <tr>
                            <td class="text-center">{{ $key+1 }}</td>
                            <td>{{ $row->created_at->format('H:i') }}</td>
                            <td>{{ str_pad($row->id, 6, '0', STR_PAD_LEFT) }}</td>
                            <td>{{ $row->subscription->wasteBin->user->firstname ?? '-' }}</td>
                            <td>{{ $row->subscription->wasteBin->bin_code }}</td>
                            <td class="text-end">{{ number_format($row->amount_paid, 2) }}</td>
                        </tr>
                        @php $total += $row->amount_paid; @endphp
                    @empty
                        <tr><td colspan="6" class="text-center">ไม่มีรายการรับเงินในวันนี้</td></tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="fw-bold bg-gray-100">
                        <td colspan="5" class="text-end">รวมทั้งสิ้น</td>
                        <td class="text-end">{{ number_format($total, 2) }}</td>
                    </tr>
                </tfoot>

            {{-- CASE 2: รายงานลูกหนี้ค้างชำระ --}}
            @elseif($type == 'arrears')
                <thead>
                    <tr class="bg-gray-100">
                        <th class="text-center">ลำดับ</th>
                        <th>ชื่อ-สกุล</th>
                        <th>ที่อยู่ / โซน</th>
                        <th>รหัสถัง</th>
                        <th class="text-end">ยอดประเมิน</th>
                        <th class="text-end">ชำระแล้ว</th>
                        <th class="text-end text-danger">ค้างชำระ</th>
                    </tr>
                </thead>
                <tbody>
                    @php $sumDebt = 0; @endphp
                    @forelse($data as $key => $row)
                        @php $debt = $row->annual_fee - $row->total_paid_amt; @endphp
                        <tr>
                            <td class="text-center">{{ $key+1 }}</td>
                            <td>{{ $row->wasteBin->user->firstname ?? '-' }} {{ $row->wasteBin->user->lastname ?? '' }}</td>
                            <td>{{ $row->wasteBin->user->address }} ({{ $row->wasteBin->user->user_zone->zone_name ?? '-' }})</td>
                            <td>{{ $row->wasteBin->bin_code }}</td>
                            <td class="text-end">{{ number_format($row->annual_fee, 2) }}</td>
                            <td class="text-end">{{ number_format($row->total_paid_amt, 2) }}</td>
                            <td class="text-end text-danger fw-bold">{{ number_format($debt, 2) }}</td>
                        </tr>
                        @php $sumDebt += $debt; @endphp
                    @empty
                        <tr><td colspan="7" class="text-center">ไม่พบรายการค้างชำระ</td></tr>
                    @endforelse
                </tbody>
                 <tfoot>
                    <tr class="fw-bold bg-gray-100">
                        <td colspan="6" class="text-end">รวมยอดค้างชำระทั้งสิ้น</td>
                        <td class="text-end text-danger">{{ number_format($sumDebt, 2) }}</td>
                    </tr>
                </tfoot>

            {{-- CASE 3: สรุปยอดตามโซน --}}
            @elseif($type == 'zone_summary')
                <thead>
                    <tr class="bg-gray-100">
                        <th class="text-center">โซนพื้นที่</th>
                        <th class="text-center">จำนวนถัง</th>
                        <th class="text-end">ยอดประเมินรวม (บาท)</th>
                        <th class="text-end text-success">เก็บได้จริง (บาท)</th>
                        <th class="text-end text-danger">ค้างชำระ (บาท)</th>
                        <th class="text-center">คิดเป็น %</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data as $row)
                        @php 
                            $percent = $row->total_revenue > 0 ? ($row->total_collected / $row->total_revenue) * 100 : 0;
                        @endphp
                        <tr>
                            <td class="text-center fw-bold">{{ $row->zone_name }}</td>
                            <td class="text-center">{{ number_format($row->total_bins) }}</td>
                            <td class="text-end">{{ number_format($row->total_revenue, 2) }}</td>
                            <td class="text-end text-success">{{ number_format($row->total_collected, 2) }}</td>
                            <td class="text-end text-danger">{{ number_format($row->total_outstanding, 2) }}</td>
                            <td class="text-center">
                                <div class="d-flex align-items-center justify-content-center">
                                    <span class="me-2 text-xs font-weight-bold">{{ number_format($percent, 1) }}%</span>
                                    <div>
                                        <div class="progress" style="width: 60px; height: 4px;">
                                            <div class="progress-bar bg-gradient-success" role="progressbar" style="width: {{ $percent }}%"></div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center">ไม่มีข้อมูล</td></tr>
                    @endforelse
                </tbody>

            {{-- CASE 4: ทะเบียนคุมใบเสร็จรับเงิน --}}
            {{-- แก้ไข: เปลี่ยนจาก @if เป็น @elseif เพื่อให้เชื่อมต่อกับด้านบน --}}
            @elseif($type == 'receipt_control')
                <thead>
                    <tr class="bg-gray-100">
                        <th class="text-center" style="width: 10%">ว/ด/ป</th>
                        <th class="text-center" style="width: 10%">เลขที่ใบเสร็จ</th>
                        <th>ได้รับเงินจาก (ชื่อผู้ชำระ)</th>
                        <th>รายการ (รหัสถัง)</th>
                        <th class="text-center">ประเภท</th>
                        <th class="text-end">จำนวนเงิน</th>
                        <th class="text-center">ผู้รับเงิน</th>
                        <th>หมายเหตุ</th>
                    </tr>
                </thead>
                <tbody>
                    @php $total = 0; @endphp
                    @forelse($data as $row)
                        <tr>
                            <td class="text-center">{{ \Carbon\Carbon::parse($row->created_at)->addYears(543)->format('d/m/y') }}</td>
                            <td class="text-center">{{ str_pad($row->id, 6, '0', STR_PAD_LEFT) }}</td>
                            <td>{{ $row->subscription->wasteBin->user->firstname ?? '-' }} {{ $row->subscription->wasteBin->user->lastname ?? '' }}</td>
                            <td>{{ $row->subscription->wasteBin->bin_code }}</td>
                            <td class="text-center">
                                {{ $row->payment_method == 'transfer' ? 'โอน' : 'เงินสด' }}
                            </td>
                            <td class="text-end">{{ number_format($row->amount_paid, 2) }}</td>
                            <td class="text-center text-xs">{{ $row->created_by_user_name ?? 'จนท.' }}</td>
                            <td></td>
                        </tr>
                        @php $total += $row->amount_paid; @endphp
                    @empty
                        <tr><td colspan="8" class="text-center">ไม่พบข้อมูล</td></tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="fw-bold bg-gray-100">
                        <td colspan="5" class="text-end">รวมยอดเงินทั้งสิ้น</td>
                        <td class="text-end">{{ number_format($total, 2) }}</td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>

            {{-- CASE 5: ใบนำส่งเงิน (Remittance) --}}
            @elseif($type == 'remittance')
                <thead>
                    <tr class="bg-gray-100">
                        <th class="text-center">ลำดับ</th>
                        <th>เลขที่ใบเสร็จ</th>
                        <th>ชื่อผู้ชำระเงิน</th>
                        <th class="text-center">ประเภทชำระ</th>
                        <th class="text-end">จำนวนเงิน</th>
                        <th>หมายเหตุ</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data as $index => $row)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>เล่มที่ ... เลขที่ {{ str_pad($row->id, 6, '0', STR_PAD_LEFT) }}</td>
                            <td>{{ $row->subscription->wasteBin->user->firstname ?? '-' }} {{ $row->subscription->wasteBin->user->lastname ?? '' }}</td>
                            <td class="text-center">
                                @if($row->payment_method == 'transfer')
                                    <span class="badge badge-sm bg-gradient-info">โอนเงิน</span>
                                @else
                                    <span class="badge badge-sm bg-gradient-success">เงินสด</span>
                                @endif
                            </td>
                            <td class="text-end">{{ number_format($row->amount_paid, 2) }}</td>
                            <td></td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center">วันนี้ยังไม่มีการรับเงิน</td></tr>
                    @endforelse
                </tbody>
                
                <tfoot>
                    <tr class="fw-bold" style="border-top: 2px solid black;">
                        <td colspan="4" class="text-end">รวมรับเงินสด</td>
                        <td class="text-end">{{ number_format($summary['cash'], 2) }}</td>
                        <td>บาท</td>
                    </tr>
                    <tr class="fw-bold">
                        <td colspan="4" class="text-end">รวมรับเงินโอน/เช็ค</td>
                        <td class="text-end">{{ number_format($summary['transfer'], 2) }}</td>
                        <td>บาท</td>
                    </tr>
                    <tr class="fw-bold bg-gray-200">
                        <td colspan="4" class="text-end">รวมนำส่งทั้งสิ้น ({{ $summary['count'] }} รายการ)</td>
                        <td class="text-end text-primary" style="font-size: 1.2em;">{{ number_format($summary['total'], 2) }}</td>
                        <td>บาท</td>
                    </tr>
                </tfoot>

            {{-- CASE 6: จุดเก็บขยะ (Service Points) --}}
            @elseif($type == 'service_points')
                <thead>
                    <tr class="bg-gray-100">
                        <th class="text-center">ลำดับ</th>
                        <th>รหัสถัง</th>
                        <th>บ้านเลขที่ / เจ้าของ</th>
                        <th>โซนพื้นที่</th>
                        <th>พิกัด (GPS)</th>
                        <th>ประเภทขยะ</th>
                        <th class="text-center">สถานะ</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data as $index => $row)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td class="fw-bold">{{ $row->bin_code }}</td>
                            <td>
                                {{ $row->user->address ?? '-' }} <br>
                                <span class="text-xs text-secondary">({{ $row->user->firstname }} {{ $row->user->lastname }})</span>
                            </td>
                            <td>{{ $row->user->user_zone->zone_name ?? '-' }}</td>
                            <td>
                                @if($row->latitude && $row->longitude)
                                    <a href="https://www.google.com/maps/search/?api=1&query={{ $row->latitude }},{{ $row->longitude }}" target="_blank" class="text-primary text-xs">
                                        <i class="fas fa-map-marker-alt me-1"></i> {{ number_format($row->latitude, 5) }}, {{ number_format($row->longitude, 5) }}
                                    </a>
                                @else
                                    <span class="text-secondary text-xs">-</span>
                                @endif
                            </td>
                            <td>{{ $row->kpUserGroup->usergroup_name ?? 'ทั่วไป' }}</td>
                            <td class="text-center">
                                <span class="badge badge-sm bg-gradient-success">Active</span>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center">ไม่พบข้อมูลจุดเก็บขยะ</td></tr>
                    @endforelse
                </tbody>

            {{-- CASE 7: ถังชำรุด (Damaged Bins) --}}
            @elseif($type == 'damaged_bins')
                <thead>
                    <tr class="bg-gray-100">
                        <th class="text-center">ลำดับ</th>
                        <th>วันที่แจ้ง/สถานะล่าสุด</th>
                        <th>รหัสถัง</th>
                        <th>เจ้าของ / ที่อยู่</th>
                        <th>รายละเอียดตำแหน่ง</th>
                        <th class="text-center">สถานะ</th>
                        <th>หมายเหตุ</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data as $index => $row)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ \Carbon\Carbon::parse($row->updated_at)->addYears(543)->format('d/m/Y') }}</td>
                            <td class="fw-bold text-danger">{{ $row->bin_code }}</td>
                            <td>
                                {{ $row->user->firstname }} {{ $row->user->lastname }}<br>
                                <span class="text-xs">{{ $row->user->address }} ({{ $row->user->user_zone->zone_name ?? '-' }})</span>
                            </td>
                            <td>{{ $row->location_description ?? '-' }}</td>
                            <td class="text-center">
                                <span class="badge badge-sm bg-gradient-danger">ชำรุด (Damaged)</span>
                            </td>
                            <td></td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center">ไม่พบรายการถังชำรุด</td></tr>
                    @endforelse
                </tbody>

            @endif {{-- ปิด if ใหญ่ทีเดียวตรงนี้ --}}
        </table>
        
        {{-- ลายเซ็น ย้ายออกมาอยู่นอก Table แต่อยู่ใน div table-responsive หรือ print-section --}}
        @if($type == 'remittance')
            <div class="row mt-5 p-3">
                <div class="col-6 text-center">
                    <p>ลงชื่อ ..................................................... ผู้นำส่ง</p>
                    <p>( ..................................................... )</p>
                    <p>ตำแหน่ง .....................................................</p>
                </div>
                <div class="col-6 text-center">
                    <p>ลงชื่อ ..................................................... ผู้รับเงิน</p>
                    <p>( ..................................................... )</p>
                    <p>วันที่ ........../........../..........</p>
                </div>
            </div>
        @endif

    </div> {{-- จบ table-responsive --}}
   </div> {{-- จบ print-section --}}
</div>

{{-- Script และ Style คงเดิม --}}
<script src="https://cdn.sheetjs.com/xlsx-latest/package/dist/xlsx.full.min.js"></script>
<script>
   function exportTableToExcel(tableID, filename = '') {
    var tableSelect = document.getElementById(tableID);
    var wb = XLSX.utils.table_to_book(tableSelect, { sheet: "Sheet1" });
    filename = filename ? filename + '.xlsx' : 'report_data.xlsx';
    XLSX.writeFile(wb, filename);
}
</script>

<style>
    @media print {
        body * { visibility: hidden; }
        #print-section, #print-section * { visibility: visible; }
        #print-section {
            position: absolute; left: 0; top: 0; width: 100%;
            margin: 0; padding: 0; background-color: white;
        }
        .no-print { display: none !important; }
        .container-fluid { width: 100%; margin: 0; padding: 0; }
    }
</style>
@endsection