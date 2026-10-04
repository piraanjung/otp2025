<?php

/*
|--------------------------------------------------------------------------
| ชื่อหน้า (แท็บ browser) ตามเมนูที่เปิดอยู่
|--------------------------------------------------------------------------
| รูปแบบ: 'pattern ของชื่อ route' => 'ชื่อเมนู'  (pattern ใช้ได้เหมือน request()->routeIs())
| ตรวจเรียงจากบนลงล่าง ตัวแรกที่ตรงจะถูกใช้ จึงให้ใส่ pattern เฉพาะเจาะจงไว้ก่อน pattern กว้าง
| ชื่อต้องตรงกับข้อความเมนูใน layouts/super-admin-navigation และ layouts/admin1_navigation
| หน้าที่ไม่อยู่ในรายการนี้จะใช้หัวข้อจาก section ของ view ตามเดิม (nav-current-title, mainheader ฯลฯ)
*/

return [
    // --- เมนูหลัก ---
    'admin.dashboard' => 'Dashboard ผู้บริหาร',
    'dashboard' => 'Dashboard',
    'accessmenu' => 'หน้าเมนูหลัก',

    // --- ประเภทมิเตอร์ / อัตราค่าน้ำ ---
    'admin.metertype.*' => 'ประเภทมิเตอร์',
    'admin.meter_rates.*' => 'อัตราชำระตามประเภทมิเตอร์',
    'admin.pricing_types.*' => 'ประเภทการชำระเงิน',

    // --- รายงาน / ข้อมูลหลัก ---
    'keptkayas.emission.*' => 'Emission Factor',
    'ef.*' => 'Emission Factor',
    'admin.financial.*' => 'รายงาน สรุป ทางบัญชี',
    'org-admins.*' => 'Org SuperAdmins',
    'admin.org-types.*' => 'ประเภทหน่วยงาน',
    'admin.users.staff' => 'เจ้าหน้าที่',
    'admin.users.*' => 'สมาชิก',
    'superadmin.staff.*' => 'Staffs',
    'keptkayas.staffs.*' => 'Staffs',
    'admin.zone.*' => 'ตั้งค่าหมู่บ้าน',
    'admin.undertaker_subzone' => 'ผู้รับผิดชอบพื้นที่จดมิเตอร์',

    // --- สิทธิ์การใช้งานระบบ ---
    'admin.roles.*' => 'Roles',
    'admin.permissions.*' => 'Permission',
    'admin.workflows.*' => 'สายการอนุมัติ',

    // --- ตั้งค่าองค์กร / ใบแจ้งหนี้ ---
    'admin.settings.invoice' => 'ใบแจ้งหนี้/vat',
    'admin.settings.*' => 'ตั้งค่าข้อมูลองค์กร',
    'admin.excel.*' => 'Import excel',
    'admin.budgetyear.*' => 'ปีงบประมาณ',
    'admin.invoice_period.*' => 'รอบบิล',

    // --- ใบแจ้งหนี้ / รับชำระ / รายงานน้ำประปา ---
    'invoice.*' => 'ออกใบแจ้งหนี้',
    'admin.owepaper.*' => 'ออกใบแจ้งเตือนค้างชำระหนี้',
    'payment.search' => 'ค้นหาใบเสร็จรับเงิน',
    'payment.*' => 'รับชำระค่าน้ำประปา',
    'reports.owe' => 'ผู้ค้างชำระค่าน้ำประปา',
    'reports.dailypayment' => 'การชำระค่าน้ำประปาประจำวัน',
    'reports.meter_record_history' => 'สมุดจดเลขอ่านมาตรวัดน้ำ(ป.31)',
    'reports.p17' => 'สมุด ledger(ป.17)',
    'reports.water_used' => 'ปริมาณการใช้น้ำประปา',

    // --- ระบบธนาคารขยะ ---
    'admin.bulk_sales.index' => 'ประวัติการขายใหญ่',
    'admin.bulk_sales.create' => 'บันทึกขายขยะใหม่',
    'admin.bulk_sales.*' => 'การขายรวม (Bulk)',
    'admin.welfare.dashboard' => 'กองทุนสวัสดิการ',
    'admin.welfare.config' => 'ตั้งค่าเกณฑ์สวัสดิการ',
    'admin.withdraws.index' => 'รายการรอจ่ายเงิน',
    'admin.withdraws.summary' => 'สรุปยอดเบิกรายรอบ',
    'admin.suppliers.*' => 'ผู้จำหน่าย (Suppliers)',
];
