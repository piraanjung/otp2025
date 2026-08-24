<?php
namespace App\Exports;

use App\Models\KeptKaya\KpTbankItems;
use App\Models\KeptKaya\KpTbankUnits;
use App\Models\KeptKaya\KpTbankItemsPriceAndPoint;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;

class PriceTemplateExport implements FromCollection, WithHeadings, WithMapping, WithEvents
{
    protected $orgId;
    protected $rowCount = 0;

    public function __construct($orgId)
    {
        $this->orgId = $orgId;
    }

    /**
     * ดึงรายการ Item x Unit ทั้งหมด (Bank และ Kiosk)
     */
    public function collection()
    {
        $items = KpTbankItems::where('org_id_fk', $this->orgId)
            ->where('status', 'active')
            ->get();

        $exportData = collect();

        foreach ($items as $item) {
            // ดึงหน่วยที่ผูกไว้กับ Item (Bank และ Kiosk)
            $unitIds = array_filter([$item->unit_bank_idfk, $item->unit_kiosk_idfk]);
            $unitIds = array_unique($unitIds); // ป้องกันกรณีใช้หน่วยเดียวกันทั้งสองระบบ

            if (empty($unitIds)) {
                // ถ้ายังไม่ได้ตั้งหน่วยเลย ให้สร้างแถวว่างไว้ 1 แถว (เพื่อให้ User เลือกเอง)
                $exportData->push($this->getLatestPrice($item->id, null));
            } else {
                foreach ($unitIds as $unitId) {
                    $exportData->push($this->getLatestPrice($item->id, $unitId));
                }
            }
        }

        $this->rowCount = $exportData->count();
        return $exportData;
    }

    /**
     * Helper สำหรับดึงราคาสุดท้าย (ถ้ามี) หรือสร้าง Row เปล่า
     */
    private function getLatestPrice($itemId, $unitId)
    {
        $price = KpTbankItemsPriceAndPoint::with(['item', 'kp_units_info'])
            ->where('kp_items_idfk', $itemId)
            ->where('kp_units_idfk', $unitId)
            ->where('status', 'active')
            ->first();

        if (!$price) {
            // ถ้ายังไม่เคยมีราคา ให้จำลอง Model ขึ้นมาเพื่อเอาชื่อ Item/Unit
            $price = new KpTbankItemsPriceAndPoint([
                'kp_items_idfk' => $itemId,
                'kp_units_idfk' => $unitId,
                'price_from_dealer' => 0,
                'price_for_member' => 0,
                'point' => 0,
                'type' => 'รีไซเคิล'
            ]);
            // โหลดความสัมพันธ์แบบ Manual
            $price->setRelation('item', KpTbankItems::find($itemId));
            $price->setRelation('kp_units_info', KpTbankUnits::find($unitId));
        }
        return $price;
    }

    public function headings(): array
    {
        return [
            'item_id(ห้ามแก้)',         // A: Item ID
            'รายการขยะ',                // B
            'หน่วยนับ',      // C: หน่วยนับ (ต้องชื่อเดียวกับใน Import)
            'ราคาซื้อจากร้านค้า',      // D: ราคาซื้อจากร้านค้า
            'ราคาให้สมาชิก',       // E: ราคาให้สมาชิก
            'แต้ม',           // F: แต้ม
            'ประเภท',        // G: ประเภท
            'วันที่เปิดใช้งาน'  // H: วันที่
        ];
    }

    public function map($price): array
    {
        return [
            $price->kp_items_idfk,
            $price->item->kp_itemsname ?? 'N/A',
            $price->kp_units_info->unitname ?? 'กก.',
            $price->price_from_dealer,
            $price->price_for_member,
            $price->point,
            $price->type ?? 'รีไซเคิล',
            date('Y-m-d'),
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // สร้าง Hidden Sheet สำหรับ Dropdown
                $dataSheet = $event->sheet->getDelegate()->getParent()->createSheet();
                $dataSheet->setTitle('Lists');
                $dataSheet->setSheetState(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::SHEETSTATE_HIDDEN);

                $units = KpTbankUnits::where('org_id_fk', $this->orgId)->pluck('unitname')->toArray();
                foreach($units as $i => $name) $dataSheet->setCellValue('A'.($i+1), $name);

                $types = ['ทั่วไป', 'รีไซเคิล', 'ขยะอันตราย', 'ขยะเปียก'];
                foreach($types as $i => $name) $dataSheet->setCellValue('B'.($i+1), $name);

                $maxRow = $this->rowCount + 50;

                // ใส่ Dropdown
                $this->setDropdown($sheet, 'C2:C'.$maxRow, 'Lists!$A$1:$A$'.count($units));
                $this->setDropdown($sheet, 'G2:G'.$maxRow, 'Lists!$B$1:$B$'.count($types));

                // ล็อกคอลัมน์ A, B (Item ID และชื่อรายการ)
                $sheet->getStyle('A2:B'.$maxRow)->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('F0F0F0');
            },
        ];
    }

    private function setDropdown($sheet, $range, $formula) {
        $validation = $sheet->getCell(explode(':', $range)[0])->getDataValidation();
        $validation->setType(DataValidation::TYPE_LIST);
        $validation->setShowDropDown(true);
        $validation->setFormula1($formula);
        $sheet->setDataValidation($range, $validation);
    }
}
