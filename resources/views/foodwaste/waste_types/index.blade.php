@extends('layouts.keptkaya')

@section('title_page', 'จัดการประเภทขยะ')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                    <h6>รายการประเภทขยะรีไซเคิล</h6>
                    <button type="button" class="btn bg-gradient-primary btn-sm mb-0" data-bs-toggle="modal" data-bs-target="#addWasteTypeModal">
                        <i class="fas fa-plus me-1"></i> เพิ่มประเภทขยะใหม่
                    </button>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show mx-3 mt-3" role="alert">
                            <span class="alert-text text-white"><strong>สำเร็จ!</strong> {{ session('success') }}</span>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif
                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show mx-3 mt-3" role="alert">
                            <span class="alert-text text-white"><strong>เกิดข้อผิดพลาด!</strong> โปรดตรวจสอบข้อมูลที่กรอก</span>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">ชื่อประเภทขยะ</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">หน่วยเริ่มต้น</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">ราคาซื้อสมาชิก (กก.)</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">ราคาขายโรงงาน (กก.)</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">ราคาซื้อสมาชิก (ชิ้น)</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">ราคาขายโรงงาน (ชิ้น)</th>
                                    <th class="text-secondary opacity-7">การจัดการ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($wasteTypes as $wasteType)
                                <tr>
                                    <td>
                                        <div class="d-flex px-2 py-1">
                                            <div class="d-flex flex-column justify-content-center">
                                                <h6 class="mb-0 text-sm">{{ $wasteType->name }}</h6>
                                                <p class="text-xs text-muted mb-0">{{ $wasteType->description }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">{{ $wasteType->default_unit == 'kg' ? 'กิโลกรัม' : 'ชิ้น/ขวด' }}</p>
                                    </td>
                                    <td class="align-middle text-center text-sm">
                                        <span class="text-xs font-weight-bold">{{ number_format($wasteType->member_buy_price_per_kg, 2) }} ฿</span>
                                    </td>
                                    <td class="align-middle text-center text-sm">
                                        <span class="text-xs font-weight-bold">{{ number_format($wasteType->factory_buy_price_per_kg, 2) }} ฿</span>
                                    </td>
                                    <td class="align-middle text-center text-sm">
                                        <span class="text-xs font-weight-bold">{{ number_format($wasteType->member_buy_price_per_piece, 2) }} ฿</span>
                                    </td>
                                    <td class="align-middle text-center text-sm">
                                        <span class="text-xs font-weight-bold">{{ number_format($wasteType->factory_buy_price_per_piece, 2) }} ฿</span>
                                    </td>
                                    <td class="align-middle">
                                        <button type="button" class="btn btn-link text-secondary font-weight-bold text-xs px-0 mb-0 me-2" data-bs-toggle="modal" data-bs-target="#editWasteTypeModal" data-id="{{ $wasteType->id }}">
                                            <i class="fas fa-edit me-1"></i> แก้ไข
                                        </button>
                                        <form action="{{ route('waste_types.destroy', $wasteType->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-link text-danger text-gradient px-0 mb-0" onclick="return confirm('คุณแน่ใจหรือไม่ที่จะลบประเภทขยะนี้?')">
                                                <i class="fas fa-trash-alt me-1"></i> ลบ
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center">ไม่มีประเภทขยะในระบบ</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <div class="d-flex justify-content-center mt-3">
                            {{ $wasteTypes->links('pagination::bootstrap-5') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<!-- Modal for Add Waste Type -->
<div class="modal fade" id="addWasteTypeModal" tabindex="-1" role="dialog" aria-labelledby="addWasteTypeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addWasteTypeModalLabel">เพิ่มประเภทขยะใหม่</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="wasteTypeForm" action="{{ route('waste_types.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="wasteTypeName" class="form-label">ชื่อประเภทขยะ</label>
                        <input type="text" class="form-control" id="wasteTypeName" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="defaultUnit" class="form-label">หน่วยเริ่มต้น</label>
                        <select class="form-select" id="defaultUnit" name="default_unit" required>
                            <option value="kg">กิโลกรัม</option>
                            <option value="piece">ชิ้น/ขวด</option>
                        </select>
                    </div>
                    {{-- Dropdown สำหรับ Waste Group (ถ้ามี) --}}
                    {{--
                    <div class="mb-3">
                        <label for="wasteGroupId" class="form-label">กลุ่มประเภทขยะ</label>
                        <select class="form-select" id="wasteGroupId" name="waste_group_id">
                            <option value="">-- เลือกกลุ่ม --</option>
                            @foreach(\App\Models\WasteGroup::all() as $group)
                                <option value="{{ $group->id }}">{{ $group->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    --}}
                    <hr class="my-3">
                    <h6 class="mb-3">ราคาต่อกิโลกรัม (ถ้ามี)</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="memberBuyPriceKg" class="form-label">ราคาซื้อสมาชิก (฿/กก.)</label>
                                <input type="number" step="0.01" class="form-control" id="memberBuyPriceKg" name="member_buy_price_per_kg" value="0.00">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="factoryBuyPriceKg" class="form-label">ราคาขายโรงงาน (฿/กก.)</label>
                                <input type="number" step="0.01" class="form-control" id="factoryBuyPriceKg" name="factory_buy_price_per_kg" value="0.00">
                            </div>
                        </div>
                    </div>
                    <hr class="my-3">
                    <h6 class="mb-3">ราคาต่อชิ้น/ขวด (ถ้ามี)</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="memberBuyPricePiece" class="form-label">ราคาซื้อสมาชิก (฿/ชิ้น)</label>
                                <input type="number" step="0.01" class="form-control" id="memberBuyPricePiece" name="member_buy_price_per_piece" value="0.00">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="factoryBuyPricePiece" class="form-label">ราคาขายโรงงาน (฿/ชิ้น)</label>
                                <input type="number" step="0.01" class="form-control" id="factoryBuyPricePiece" name="factory_buy_price_per_piece" value="0.00">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">รายละเอียด</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn bg-gradient-primary">บันทึก</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal for Edit Waste Type -->
<div class="modal fade" id="editWasteTypeModal" tabindex="-1" role="dialog" aria-labelledby="editWasteTypeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editWasteTypeModalLabel">แก้ไขประเภทขยะ</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editWasteTypeForm" action="#" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <input type="hidden" id="editWasteTypeId" name="id">
                    <div class="mb-3">
                        <label for="editWasteTypeName" class="form-label">ชื่อประเภทขยะ</label>
                        <input type="text" class="form-control" id="editWasteTypeName" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="editDefaultUnit" class="form-label">หน่วยเริ่มต้น</label>
                        <select class="form-select" id="editDefaultUnit" name="default_unit" required>
                            <option value="kg">กิโลกรัม</option>
                            <option value="piece">ชิ้น/ขวด</option>
                        </select>
                    </div>
                    {{-- Dropdown สำหรับ Waste Group (ถ้ามี) --}}
                    {{--
                    <div class="mb-3">
                        <label for="editWasteGroupId" class="form-label">กลุ่มประเภทขยะ</label>
                        <select class="form-select" id="editWasteGroupId" name="waste_group_id">
                            <option value="">-- เลือกกลุ่ม --</option>
                            @foreach(\App\Models\WasteGroup::all() as $group)
                                <option value="{{ $group->id }}">{{ $group->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    --}}
                    <hr class="my-3">
                    <h6 class="mb-3">ราคาต่อกิโลกรัม (ถ้ามี)</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="editMemberBuyPriceKg" class="form-label">ราคาซื้อสมาชิก (฿/กก.)</label>
                                <input type="number" step="0.01" class="form-control" id="editMemberBuyPriceKg" name="member_buy_price_per_kg" value="0.00">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="editFactoryBuyPriceKg" class="form-label">ราคาขายโรงงาน (฿/กก.)</label>
                                <input type="number" step="0.01" class="form-control" id="editFactoryBuyPriceKg" name="factory_buy_price_per_kg" value="0.00">
                            </div>
                        </div>
                    </div>
                    <hr class="my-3">
                    <h6 class="mb-3">ราคาต่อชิ้น/ขวด (ถ้ามี)</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="editMemberBuyPricePiece" class="form-label">ราคาซื้อสมาชิก (฿/ชิ้น)</label>
                                <input type="number" step="0.01" class="form-control" id="editMemberBuyPricePiece" name="member_buy_price_per_piece" value="0.00">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="editFactoryBuyPricePiece" class="form-label">ราคาขายโรงงาน (฿/ชิ้น)</label>
                                <input type="number" step="0.01" class="form-control" id="editFactoryBuyPricePiece" name="factory_buy_price_per_piece" value="0.00">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="editDescription" class="form-label">รายละเอียด</label>
                        <textarea class="form-control" id="editDescription" name="description" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn bg-gradient-primary">บันทึกการเปลี่ยนแปลง</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle opening of the Edit Waste Type Modal
        const editWasteTypeModal = document.getElementById('editWasteTypeModal');
        editWasteTypeModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget; // Button that triggered the modal
            const wasteTypeId = button.getAttribute('data-id'); // Extract info from data-* attributes
            const form = editWasteTypeModal.querySelector('#editWasteTypeForm');

            // Fetch waste type data via AJAX (replace with your actual route)
            fetch(`/waste-types/${wasteTypeId}/data`)
                .then(response => response.json())
                .then(data => {
                    form.action = `/waste-types/${data.id}`; // Set form action for update
                    form.querySelector('#editWasteTypeId').value = data.id;
                    form.querySelector('#editWasteTypeName').value = data.name;
                    form.querySelector('#editDefaultUnit').value = data.default_unit;
                    form.querySelector('#editMemberBuyPriceKg').value = data.member_buy_price_per_kg ? data.member_buy_price_per_kg.toFixed(2) : '0.00';
                    form.querySelector('#editFactoryBuyPriceKg').value = data.factory_buy_price_per_kg ? data.factory_buy_price_per_kg.toFixed(2) : '0.00';
                    form.querySelector('#editMemberBuyPricePiece').value = data.member_buy_price_per_piece ? data.member_buy_price_per_piece.toFixed(2) : '0.00';
                    form.querySelector('#editFactoryBuyPricePiece').value = data.factory_buy_price_per_piece ? data.factory_buy_price_per_piece.toFixed(2) : '0.00';
                    form.querySelector('#editDescription').value = data.description;

                    // Update modal title
                    editWasteTypeModal.querySelector('.modal-title').textContent = 'แก้ไขประเภทขยะ: ' + data.name;
                })
                .catch(error => console.error('Error fetching waste type data:', error));
        });

        // Handle opening of the Add Waste Type Modal (reset form)
        const addWasteTypeModal = document.getElementById('addWasteTypeModal');
        addWasteTypeModal.addEventListener('show.bs.modal', function (event) {
            const form = addWasteTypeModal.querySelector('#wasteTypeForm');
            form.reset(); // Clear form fields
            form.action = '{{ route('waste_types.store') }}'; // Set form action for store
            addWasteTypeModal.querySelector('.modal-title').textContent = 'เพิ่มประเภทขยะใหม่';
        });
    });
</script>
@endsection
