@extends('layouts.admin1')

@section('nav-header')
    <a href="{{ url('/admin/invoice_period') }}"> รอบบิลการจัดเก็บ</a>
@endsection

@section('mainheader') รายการรอบบิลประจำปีงบประมาณ {{ $budgetyear ? $budgetyear->budgetyear_name : '-' }} @endsection

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">ตารางรอบบิล 12 เดือน</h5>
            @if($budgetyear)
                <span class="badge bg-gradient-success p-2" style="font-size: 1rem;">ปีงบประมาณปัจจุบัน:
                    {{ $budgetyear->budgetyear_name }}</span>
            @endif
        </div>

        <div class="card-content">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 50px;">#</th>
                        <th>รอบบิลประจำเดือน</th>
                        <th class="text-center">ช่วงวันที่</th>
                        <th class="text-center">สถานะการออกบิล</th>
                        <th class="text-center" style="width: 200px;">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($periods as $index => $period)
                        @php
                            // ตัวอย่างการคำนวณเงื่อนไขล่วงหน้า 10 วัน
                            // $period->startdate คือวันที่เริ่มต้นของรอบบิลนั้น (เช่น 2026-11-01)
                            $startDate = \Carbon\Carbon::parse($period->startdate);
                            $daysUntilStart = \Carbon\Carbon::now()->diffInDays($startDate, false);
                            // diffInDays แบบ false จะให้ค่าติดลบถ้าผ่านวันนั้นมาแล้ว และเป็นบวกถ้ายังไม่ถึง

                            // เงื่อนไขการแสดงปุ่ม:
                            // 1. ถ้ายังไม่สร้างบิล และ (เป็นเดือน active หรือ เหลือเวลาอีก <= 10 วันจะถึงรอบนี้)
                            $canShowButton = !$period->is_generated && ($period->status == 'active' || ($daysUntilStart <= 10 && $daysUntilStart >= 0));
                        @endphp

                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td class="font-weight-bold">{{ $period->inv_p_name }}</td>
                            <td class="text-center">{{ $startDate->format('d/m/Y') }} ถึง
                                {{ \Carbon\Carbon::parse($period->enddate)->format('d/m/Y') }}</td>
                            <td class="text-center">
                                @if ($period->is_generated)
                                    <span class="badge bg-gradient-success">สร้างใบแจ้งหนี้แล้ว ({{ $period->invoice_count }}
                                        รายการ)</span>
                                @elseif ($period->status == 'active')
                                    <span class="badge bg-gradient-primary">รอบบิลปัจจุบัน (Active)</span>
                                @else
                                    <span class="badge bg-gradient-secondary">รอรอบจัดเก็บ</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if ($period->is_generated)
                                    <a href="#" class="btn btn-info btn-sm">
                                        <i class="fa fa-search"></i> ดูรายละเอียด
                                    </a>
                                @elseif ($canShowButton)
                                    @if(!$hasConfig)
                                        <div class="alert alert-warning mb-3">
                                            <i class="fa fa-exclamation-triangle me-1"></i>
                                          <div>ยังไม่ได้ตั้งค่า</div>   
                                            <div>"ประเภทผู้ใช้น้ำ" / "อัตราค่าน้ำ"</div> 
                                            <div>กรุณาไปตั้งค่าก่อน</div>
                                            <div>เริ่มออกใบแจ้งหนี้</div>
                                            <a href="{{ route('meter_types.index') }}" class="alert-link">ไปหน้าตั้งค่า</a>
                                        </div>
                                    @else
                                        {{-- แสดงปุ่มเฉพาะเดือนปัจจุบัน หรือก่อนถึง 10 วัน --}}
                                        <a href="{{ route('admin.invoice_period.generate', $period->id) }}"
                                            class="btn btn-success btn-sm"
                                            onclick="return confirm('ยืนยันการสร้างใบแจ้งหนี้สำหรับรอบบิล {{ $period->inv_p_name }} ?')">
                                            <i class="fa fa-plus-circle"></i> สร้างใบแจ้งหนี้
                                        </a>
                                    @endif

                                @else
                                    {{-- ป้องกันการกด ล็อคไว้ก่อน --}}
                                    <button class="btn btn-secondary btn-sm" disabled title="ยังไม่ถึงกำหนดรอบจัดเก็บ">
                                        <i class="fa fa-lock"></i> ยังไม่เปิดรอบ
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection