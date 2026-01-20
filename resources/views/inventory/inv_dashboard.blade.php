@extends('inventory.inv_master')

@section('title', 'ภาพรวมระบบ')
@section('header_title', 'Dashboard ภาพรวมคลังพัสดุ')

@section('content')
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card p-3 border-0 shadow-sm h-100 bg-primary text-white">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="opacity-75 mb-0">รายการพัสดุ (SKUs)</h6>
                    <h2 class="fw-bold m-0">{{ number_format($totalItems) }}</h2>
                </div>
                <div class="bg-white text-primary rounded-circle p-2 opacity-75">
                    <i class="material-icons-round fs-2">inventory_2</i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card p-3 border-0 shadow-sm h-100 bg-success text-white">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="opacity-75 mb-0">ขวด/กล่อง ที่มีของ</h6>
                    <h2 class="fw-bold m-0">{{ number_format($totalBottles) }}</h2>
                </div>
                <div class="bg-white text-success rounded-circle p-2 opacity-75">
                    <i class="material-icons-round fs-2">science</i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card p-3 border-0 shadow-sm h-100 bg-warning text-dark">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="opacity-75 mb-0">ใกล้หมดอายุ (30 วัน)</h6>
                    <h2 class="fw-bold m-0">{{ number_format($expiringSoon) }}</h2>
                </div>
                <div class="bg-white text-warning rounded-circle p-2 opacity-75">
                    <i class="material-icons-round fs-2">warning</i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-5 mb-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white py-3">
                <h6 class="m-0 fw-bold">สัดส่วนพัสดุตามหมวดหมู่</h6>
            </div>
            <div class="card-body">
                <canvas id="categoryChart"></canvas>
            </div>
        </div>
    </div>

    <div class="col-md-7 mb-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 fw-bold">การเคลื่อนไหวล่าสุด</h6>
                <a href="#" class="small text-decoration-none">ดูทั้งหมด</a>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">พัสดุ</th>
                            <th>ผู้ทำรายการ</th>
                            <th>จำนวน</th>
                            <th>เวลา</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentTransactions as $trans)
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold text-dark">{{ $trans->item->name }}</div>
                                <small class="text-muted">{{ $trans->purpose }}</small>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="bg-secondary text-white rounded-circle me-2 d-flex justify-content-center align-items-center" style="width: 25px; height: 25px; font-size: 10px;">
                                        {{ substr($trans->user->name, 0, 1) }}
                                    </div>
                                    <small>{{ $trans->user->name }}</small>
                                </div>
                            </td>
                            <td>
                                <span class="badge {{ $trans->quantity < 0 ? 'bg-danger' : 'bg-success' }}">
                                    {{ $trans->quantity }} {{ $trans->item->unit }}
                                </span>
                            </td>
                            <td><small class="text-muted">{{ $trans->created_at->diffForHumans() }}</small></td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-4 text-muted">ยังไม่มีรายการเคลื่อนไหว</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // รับค่า Array จาก PHP -> JavaScript
    const labels = @json($labels);
    const data = @json($data);

    const ctx = document.getElementById('categoryChart').getContext('2d');
    const myChart = new Chart(ctx, {
        type: 'doughnut', // กราฟวงกลมโดนัท
        data: {
            labels: labels,
            datasets: [{
                label: 'จำนวนรายการ',
                data: data,
                backgroundColor: [
                    '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#858796'
                ],
                hoverOffset: 4
            }]
        },
        options: {
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                }
            }
        }
    });
</script>
@endsection