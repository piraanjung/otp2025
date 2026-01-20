<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tabwater\TwInvoice;
use App\Models\Tabwater\TwInvoiceHistory;
use Illuminate\Support\Facades\DB;

class ArchivePaidInvoices extends Command
{
    protected $signature = 'invoice:archive';
    protected $description = 'Move paid invoices to history table';

    public function handle()
    {
        $this->info('Starting Archive Process...');

        // 1. เลือกบิลที่จ่ายแล้ว (Paid) และเก่ากว่า X วัน (เพื่อความปลอดภัย เผื่อมีการแก้ไข)
        // หรือจะเอา paid ทั้งหมดเลยก็ได้ตาม business logic
        $query = TwInvoice::where('status', 'paid');
        // ->where('updated_at', '<', now()->subDays(7)); // ตัวอย่าง: ย้ายเฉพาะที่จ่ายเกิน 7 วันแล้ว

        // 2. ใช้ Chunk เพื่อป้องกัน Memory เต็ม (ทีละ 500-1000 แถว)
        $query->chunk(500, function ($invoices) {
            DB::transaction(function () use ($invoices) {
                // เตรียมข้อมูลแปลงเป็น Array
                $dataToInsert = [];
                $idsToDelete = [];

                foreach ($invoices as $invoice) {
                    // แปลง Model เป็น Array
                    $data = $invoice->toArray();
                    
                    // ลบ id ออก ถ้าต้องการให้ History รัน ID ใหม่ (แต่แนะนำให้เก็บ ID เดิมไว้เพื่ออ้างอิง)
                    // unset($data['id']); 
                    
                    $dataToInsert[] = $data;
                    $idsToDelete[] = $invoice->id;
                }

                // A. Insert ลง History (ใช้ insert เพื่อความเร็ว - แต่อย่าลืมเรื่อง timestamp)
                // TwInvoiceHistory::insert($dataToInsert); <-- ระวัง created_at/updated_at อาจเพี้ยนถ้าใช้ insert raw
                // แนะนำให้ loop create หรือ insert แบบระวัง field วันที่
                
                // วิธีที่ปลอดภัยกว่า (แต่นานกว่านิดหน่อย)
                foreach($dataToInsert as $data) {
                     TwInvoiceHistory::create($data);
                }

                // B. ลบจากตารางหลัก
                TwInvoice::whereIn('id', $idsToDelete)->delete();
                
                $this->info('Archived ' . count($idsToDelete) . ' records.');
            });
        });

        $this->info('Archive Complete!');
    }
}