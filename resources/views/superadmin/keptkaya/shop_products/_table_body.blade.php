@forelse($products as $product)
    <tr>
        <td>
            <div class="d-flex px-2 py-1">
                <div>
                    <img src="{{ asset('storage/' . $product->image_path) }}" class="avatar avatar-sm me-3" alt="{{ $product->name }}">
                </div>
                <div class="d-flex flex-column justify-content-center">
                    <h6 class="mb-0 text-sm">{{ $product->name }}</h6>
                    <p class="text-xs text-secondary mb-0">{{ $product->sku }}</p>
                </div>
            </div>
        </td>
        <td>
            <p class="text-xs font-weight-bold mb-0">{{ $product->category->name ?? 'N/A' }}</p>
        </td>
        <td class="align-middle text-center text-sm">
            <p class="text-xs font-weight-bold mb-0">{{ number_format($product->point_price) }} แต้ม</p>
        </td>
        <td class="align-middle text-center text-sm">
            <p class="text-xs font-weight-bold mb-0">{{ number_format($product->cash_price, 2) }} บาท</p>
        </td>
        <td class="align-middle text-center text-sm">
            <p class="text-xs font-weight-bold mb-0">{{ $product->stock }}</p>
        </td>
        <td class="align-middle text-center text-sm">
            <span class="badge badge-sm bg-gradient-{{ $product->status == 'active' ? 'success' : 'secondary' }}">{{ ucfirst($product->status) }}</span>
        </td>
        <td class="align-middle">
            <a href="{{ route('superadmin.keptkaya.shop-products.edit', $product->id) }}" class="text-secondary font-weight-bold text-xs me-2" data-toggle="tooltip" data-original-title="Edit user">
                แก้ไข
            </a>
            <form action="{{ route('superadmin.keptkaya.shop-products.destroy', $product->id) }}" method="POST" class="d-inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-link text-danger text-gradient px-0 mb-0" onclick="return confirm('คุณแน่ใจหรือไม่ที่จะลบสินค้านี้?')">ลบ</button>
            </form>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="7" class="text-center">ไม่มีข้อมูลสินค้าในระบบ</td>
    </tr>
@endforelse