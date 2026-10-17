{{-- ข้อความแจ้งผลกลางของ layouts.super-admin: success / error / warning / message+color (รูปแบบเดิมของ admin1) (validation errors ให้แต่ละหน้าแสดงเอง) --}}
@php
  $flashes = collect([
      ['success', session('success')],
      ['danger', session('error')],
      ['warning', session('warning')],
      [session('color', 'info'), session('message')],
  ])->filter(fn ($f) => filled($f[1]));
@endphp

@foreach ($flashes as [$type, $text])
  <div class="alert alert-{{ $type }} alert-dismissible fade show text-white" role="alert">
    <span class="alert-text">{{ $text }}</span>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
@endforeach

