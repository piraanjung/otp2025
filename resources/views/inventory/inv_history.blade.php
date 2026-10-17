@extends('inventory.inv_master')

@section('title', 'ประวัติการเบิกจ่าย') 
@section('header_title', 'ประวัติการทำรายการ (Transaction Logs)') 

@section('content') 
<div class="card p-4"> 

    <!-- 📑 ส่วนของ Tab สถานะ -->
    <!-- 📑 ส่วนของ Tab สถานะ พร้อมตัวเลขจำนวน -->
    @php
        $currentTab = request('status', 'all');
    @endphp
    <ul class="nav nav-tabs mb-4">
        <li class="nav-item">
            <a class="nav-link {{ $currentTab == 'all' ? 'active fw-bold' : '' }}" 
               href="{{ route('inventory.history', array_merge(request()->except(['status', 'page']), ['status' => 'all'])) }}">
               ทั้งหมด <span class="badge bg-secondary ms-1">{{ $countAll ?? 0 }}</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $currentTab == 'pending' ? 'active fw-bold text-warning' : '' }}" 
               href="{{ route('inventory.history', array_merge(request()->except(['status', 'page']), ['status' => 'pending'])) }}">
               ⏳ รออนุมัติ <span class="badge bg-warning text-dark ms-1">{{ $countPending ?? 0 }}</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $currentTab == 'approved' ? 'active fw-bold text-primary' : '' }}" 
               href="{{ route('inventory.history', array_merge(request()->except(['status', 'page']), ['status' => 'approved'])) }}">
               📦 อนุมัติแล้ว (รอเบิกจ่าย) <span class="badge bg-primary ms-1">{{ $countApproved ?? 0 }}</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $currentTab == 'completed' ? 'active fw-bold text-success' : '' }}" 
               href="{{ route('inventory.history', array_merge(request()->except(['status', 'page']), ['status' => 'completed'])) }}">
               ✅ เบิกจ่ายแล้ว <span class="badge bg-success ms-1">{{ $countCompleted ?? 0 }}</span>
            </a>
        </li>
    </ul>

    <form action="{{ route('inventory.history') }}" method="GET" class="mb-4"> 
        <!-- เก็บค่า status ไว้ใน input hidden เพื่อเวลาค้นหาแล้วแท็บไม่หลุด -->
        <input type="hidden" name="status" value="{{ $currentTab }}">

        <div class="row g-2 align-items-end bg-light p-3 rounded border"> 
            <div class="col-md-4"> 
                <label class="form-label small text-muted">ค้นหาพัสดุ</label> 
                <div class="input-group"> 
                    <span class="input-group-text bg-white"><i class="material-icons-round fs-6">search</i></span> 
                    <input type="text" name="search" class="form-control" placeholder="ชื่อ หรือ รหัสพัสดุ" value="{{ request('search') }}"> 
                </div> 
            </div> 
            <div class="col-md-3"> 
                <label class="form-label small text-muted">ตั้งแต่วันที่</label> 
                <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}"> 
            </div> 
            <div class="col-md-3"> 
                <label class="form-label small text-muted">ถึงวันที่</label> 
                <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}"> 
            </div> 
            <div class="col-md-2"> 
                <button type="submit" class="btn btn-primary w-100 btn-material"> ค้นหา </button> 
            </div> 
        </div> 
    </form> 

    <!-- ตารางแสดงผลข้อมูล (โค้ดตารางเดิมของคุณ) -->
    <div class="table-responsive"> 
        <table class="table table-hover align-middle"> 
            <thead class="bg-light"> 
                <tr> 
                    <th width="12%">วันที่ / เลขที่</th> 
                    <th width="15%">ผู้เบิก</th> 
                    <th width="35%">รายการพัสดุ</th> 
                    <th class="text-center" width="15%">สถานะการอนุมัติ</th> 
                    <th class="text-end" width="10%">จำนวน</th> 
                    <th class="text-center" width="15%">จัดการ</th> 
                </tr> 
            </thead> 
            <tbody> 
                @forelse($transactions as $trans) 
                @php 
                    $groupItems = $trans->ref_no ? \App\Models\InvTransaction::where('ref_no', $trans->ref_no)->with(['item', 'detail'])->get() : collect([$trans]); 
                    $workflowGroups = $groupItems->groupBy('approve_workflow_id_fk'); 
                @endphp 
                <tr> 
                    <!-- คอลัมน์ วันที่/เลขที่ -->
                    <td class="align-middle" style="width: 12%;"> 
                        <div class="fw-bold text-dark">{{ \Carbon\Carbon::parse($trans->created_at)->format('d/m/Y') }}</div> 
                        <small class="text-muted">{{ \Carbon\Carbon::parse($trans->created_at)->format('H:i') }} น.</small> 
                        @if($trans->ref_no) 
                        <div class="mt-1"> 
                            <span class="badge bg-light text-secondary border"> 
                                <i class="material-icons-round fs-6 align-text-bottom" style="font-size: 10px;">receipt</i> {{ $trans->ref_no }} 
                            </span> 
                        </div> 
                        @endif 
                    </td> 

                    <!-- คอลัมน์ ผู้เบิก -->
                    <td class="align-middle" style="width: 15%;"> 
                        <div class="mb-1"> 
                            <span class="text-dark fw-bold small"> 
                                <i class="material-icons-round fs-6 align-middle text-muted">person</i> {{ optional($trans->requester)->prefix . "" . optional($trans->requester)->firstname . " " . optional($trans->requester)->lastname }} 
                            </span> 
                        </div> 
                        <small class="text-muted fst-italic d-block text-truncate" style="max-width: 140px;"> "{{ $trans->purpose }}" </small> 
                    </td> 

                    <!-- คอลัมน์ พัสดุ -->
                    <td colspan="4" class="p-0"> 
                        <table class="table table-borderless mb-0 align-middle"> 
                            @foreach($workflowGroups as $wfId => $itemsInWf) 
                            @php 
                                $pendingSteps = \App\Models\InvTransactionApprovals::where('approval_workflow_id', $wfId) ->where('ref_no', $trans->ref_no) ->where('status', 'PENDING') ->count(); 
                                $rejectedSteps = \App\Models\InvTransactionApprovals::where('approval_workflow_id', $wfId) ->where('ref_no', $trans->ref_no) ->where('status', 'REJECTED') ->count(); 
                            @endphp 
                            <tr class="{{ !$loop->first ? 'border-top' : '' }}"> 
                                <td style="width: 15%;"> 
                                    <span class="badge bg-primary bg-opacity-10 text-primary mb-1" style="font-size: 10px;"> Workflow #{{ $wfId }} </span> 
                                    @foreach($itemsInWf as $gItem) 
                                    <div class="text-dark small"> • {{ optional($gItem->item)->name ?? '-' }} @if(optional($gItem->item)->code) <span class="text-muted">({{ $gItem->item->code }})</span> @endif </div> 
                                    @endforeach 
                                </td> 
                                <td class="text-center" style="width: 15%;"> 
                                    @if($rejectedSteps > 0) 
                                        <span class="badge bg-danger text-white" style="font-size: 11px;">❌ ถูกตีกลับ</span> 
                                    @elseif($pendingSteps > 0) 
                                        <span class="badge bg-warning text-dark" style="font-size: 11px;">⏳ รออนุมัติ</span> 
                                    @else 
                                        <span class="badge bg-success text-white" style="font-size: 11px;">✅ อนุมัติแล้ว</span> 
                                    @endif 
                                    @if ($trans->status == 'COMPLETED') 
                                        <br><span class="badge bg-success text-white mt-1" style="font-size: 11px;">✅ เบิกจ่ายแล้ว</span> 
                                    @endif 
                                </td> 
                                <td class="text-end" style="width: 1%;"> 
                                    @foreach($itemsInWf as $gItem) 
                                    <div class="small fw-bold text-secondary"> {{ number_format($gItem->quantity) }} <span class="text-muted fw-normal">{{ optional($gItem->item)->unit ?? 'หน่วย' }}</span> </div> 
                                    @endforeach 
                                </td> 
                            </tr> 
                            @endforeach 
                        </table> 
                    </td> 

                    <!-- คอลัมน์ จัดการ -->
                    <td class="align-middle text-center" style="width: 15%;"> 
                        @if($trans->ref_no) 
                            <div class="mb-2"> 
                                <a href="{{ route('inventory.withdraw.show_ref', $trans->ref_no) }}" class="btn btn-sm btn-outline-info shadow-sm w-100 py-1" title="ดูเอกสารใบเบิกทั้งหมด (A4)"> 
                                    <i class="material-icons-round fs-6 align-middle">visibility</i> ดูใบเบิก 
                                </a> 
                            </div> 
                            @if ($trans->status != 'COMPLETED') 
                                @foreach($workflowGroups as $wfId => $itemsInWf) 
                                @php 
                                    $isCompleted = \App\Models\InvTransactionApprovals::isWorkflowCompleted($trans->ref_no, $wfId); 
                                @endphp 
                                <div class="{{ !$loop->first ? 'mt-2 pt-2 border-top' : '' }}"> 
                                    @if($isCompleted) 
                                        <a href="{{ route('inventory.withdraw.dispense_form', ['refNo' => $trans->ref_no, 'workflow_id' => $wfId]) }}" class="btn btn-sm btn-warning shadow-sm w-100 py-1" style="font-size: 12px;" title="เบิกจ่ายพัสดุเฉพาะสายงานนี้"> 
                                            <i class="material-icons-round fs-6 align-middle">local_shipping</i> จ่ายพัสดุ 
                                        </a> 
                                    @else 
                                        <span class="text-muted small d-block py-1 bg-light rounded border border-light" style="font-size: 11px;"> 🔒 รออนุมัติครบ </span> 
                                    @endif 
                                </div> 
                                @endforeach 
                            @endif 
                        @else 
                            <a href="{{ route('inventory.withdraw.show', $trans->id) }}" class="btn btn-sm btn-outline-primary shadow-sm" title="ดูรายละเอียด"> 
                                <i class="material-icons-round fs-6">visibility</i> 
                            </a> 
                        @endif 
                    </td> 
                </tr> 
                @empty 
                <tr> 
                    <td colspan="6" class="text-center py-5 text-muted"> 
                        <i class="material-icons-round display-4 opacity-25">history</i> 
                        <p>ไม่พบประวัติการทำรายการในสถานะนี้</p> 
                    </td> 
                </tr> 
                @endforelse 
            </tbody> 
        </table> 
    </div> 

    <div class="mt-3"> 
        {{ $transactions->links() }} 
    </div> 
</div> 
@endsection