<?php

namespace App\Http\Controllers\Admin;

use App\Exports\UserTemplateExport;
use App\Http\Controllers\Api\FunctionsController;
use App\Http\Controllers\Controller;
use App\Imports\UserImport;
use App\Models\Admin\Organization;
use App\Models\Admin\Subzone;
use App\Models\AnnualTrash\AnnualTrashSubscription;
use App\Models\Tabwater\TwInvoice;
use App\Models\Tabwater\TwInvoiceHistory;
use App\Models\User;
use App\Models\Admin\Zone;
use App\Models\FoodWaste\FoodWasteAccount;
use App\Models\AnnualTrash\AnnualTrashPayratePerMonth;
use App\Models\FoodWaste\FoodWasteUserPreference;
use App\Models\KeptKaya\KpUserWastePreference;
use App\Models\KpBankAccount;
use App\Models\Tabwater\TwUsersInfos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 20);

        // ใช้ with() เพื่อป้องกัน N+1 Query (โหลดข้อมูลบัญชีมาพร้อมกันเลย)
        
        $query = User::with(['wastePreference.kpBankAccount', 'foodWasteAccount', 'annualTrashSubscription'])
            ->whereHas('wastePreference', function($q){
                $q->where('org_id_fk',Auth::user()->org_id_fk );
            })
            ->where('org_id_fk', Auth::user()->org_id_fk)
            ->whereDoesntHave('roles', function ($q) {
                $q->where('name', 'Super Admin');
            });

            if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('firstname', 'like', "%$search%")
                    ->orWhere('lastname', 'like', "%$search%")
                    ->orWhere('id', $search)
                    ->orWhere('address', 'like', "%$search%");
            });
        }


        // --- 🌟 Filter ตามโซน (Zone / Subzone) ---
        if ($request->filled('zone_id')) {
            $query->where('zone_id', $request->zone_id);
        }
        if ($request->filled('subzone_id')) {
            $query->where('subzone_id', $request->subzone_id);
        }

        // --- 🌟 Filter ตามสถานะบริการ (Service Status) ---
        if ($request->filled('service_filter')) {
            $filter = $request->service_filter;
            if ($filter == 'recycle') $query->has('kpBankAccount');
            if ($filter == 'food_waste') $query->has('foodWasteAccount');
            if ($filter == 'annual_trash') $query->has('annualTrashSubscription');
        }
        $users = ($perPage == 'all') ? $query->get() : $query->paginate($perPage);

        // ดึงข้อมูล Zone สำหรับตัวเลือก Filter
        $zones = Zone::all();

        return view('admin.users.index', compact('users', 'perPage', 'zones'));
    }

    protected function _index(){
        $arr = [
        ['Panthita51103@gmail.com','กลุ่มงานพยาบาล','งานการพยาบาลผู้ป่วยใน'],
        ['atcharawadeeritthikhan@gmail.com','กลุ่มงานพยาบาล','งานผู้ป่วยนอก'],
        ['padak_2526@hotmail.com','กลุ่มงานบริการด้านปฐมภูมิและองค์รวม','งานสุขภาพจิตและยาเสพติด'],
        ['Auraporn0611@gmail.com','กลุ่มงานบริหารทั่วไป','งานพัสดุ'],
        ['Jirakan2532n@gmail.com','กลุ่มงานพยาบาล','งานอุบัติเหตุฉุกเฉิน'],
        ['aemt65-23@scphub.ac.th','กลุ่มงานพยาบาล','งานอุบัติเหตุฉุกเฉิน'],
        ['bbb_@hotmail.com','กลุ่มงานบริหารทั่วไป','งานการเงิน'],
        ['kimmy.it4@gmail.com','กลุ่มงานประกันสุขภาพยุทธศาสตร์และสารสนเทศทางการแพทย์','งานเวชระเบียน'],
        ['ddd_@hotmail.com','กลุ่มงานการแพทย์แผนไทยและการแพทย์ทางเลือก','แพทย์แผนจีน'],
        ['janji.wit1988@gmail.com','กลุ่มงานบริหารทั่วไป','งานโภชนศาสตร์'],
        ['mery.ssbd@gmail.com','กลุ่มงานประกันสุขภาพยุทธศาสตร์และสารสนเทศทางการแพทย์','งานศูนย์ประกันสุขภาพ'],
        ['kkk_@hotmail.com','กลุ่มงานบริการด้านปฐมภูมิและองค์รวม','งานเวชปฏิบัติครอบครัว'],
        ['ad_@hotmali.com','กลุ่มงานพยาบาล','งานการพยาบาลผู้ป่วยใน'],
        ['po_@hotmail.com','กลุ่มงานพยาบาล','งานอุบัติเหตุฉุกเฉิน'],
        ['kittiyafc55@gmail.com','กลุ่มงานเภสัชกรรมและคุ้มครองผู้บริโภค','ฝ่ายเภสัชกรรมชุมชน'],
        ['poopheprimphan@gmail.com','กลุ่มงานบริหารทั่วไป','งานทำความสะอาด'],
        ['ph_ir@hotmail.com','กลุ่มงานบริหารทั่วไป','งานรักษาความปลอดภัย'],
        ['Chonthichafon123@gmail.com','กลุ่มงานทันตกรรม','ฝ่ายทันตสาธารณสุข'],
        ['chalisac245@gmail.com','กลุ่มงานทันตกรรม','ฝ่ายทันตสาธารณสุข'],
        ['tongyz1234@gmail.com','กลุ่มงานบริหารทั่วไป','งานซ่อมบำรุง'],
        ['si_@hotmail.com','กลุ่มงานบริหารทั่วไป','งานรักษาความปลอดภัย'],
        ['golfmike_seed@hotmail.com','กลุ่มงานเทคนิคการแพทย์','งานเทคนิคการแพทย์'],
        ['ngunlasomni@gmail.com','กลุ่มงานบริหารทั่วไป','งานภูมิทัศน์'],
        ['wanlayaneejantarangsee@gmail.com','กลุ่มงานพยาบาล','งานการพยาบาลผู้คลอดเเละทารกเเรกเกิด'],
        ['sup_@hotmail.com','กลุ่มงานพยาบาล','งานอุบัติเหตุฉุกเฉิน'],
        ['palmmy.4529@gmail.com','กลุ่มงานพยาบาล','งานอุบัติเหตุฉุกเฉิน'],
        ['ko_@hotmail.com','กลุ่มงานพยาบาล','งานอุบัติเหตุฉุกเฉิน'],
        ['ilada19.pan@gmail.com','กลุ่มงานประกันสุขภาพยุทธศาสตร์และสารสนเทศทางการแพทย์','งานศูนย์ประกันสุขภาพ'],
        ['niraporn.747@gmail.com','กลุ่มงานบริการด้านปฐมภูมิและองค์รวม','สุขาภิบาลและป้องกันโรค'],
        ['jittrayuy88888@gmail.com','กลุ่มงานพยาบาล','บริหารกลุ่มการพยาบาล'],
        ['itsaree76z@gmail.com','กลุ่มงานประกันสุขภาพยุทธศาสตร์และสารสนเทศทางการแพทย์','งานศูนย์ประกันสุขภาพ'],
        ['na_@hotmail.com','กลุ่มงานรังสีวิทยา','งานรังสี'],
        ['oa_@hotmail.com','กลุ่มงานบริหารทั่วไป','งานรักษาความปลอดภัย'],
        ['narumon_19_35@hotmail.com','กลุ่มงานบริหารทั่วไป','งานพัสดุ'],
        ['aewwiwan@gmail.com','กลุ่มงานบริหารทั่วไป','งานการเงิน'],
        ['tawann1502@gmail.com','กลุ่มงานพยาบาล','งานผู้ป่วยนอก'],
        ['t.audio@hotmail.com','กลุ่มงานบริหารทั่วไป','งานซ่อมบำรุง'],
        ['nadthapat_@hotmail.com','กลุ่มงานเภสัชกรรมและคุ้มครองผู้บริโภค','ฝ่ายเภสัชกรรมชุมชน'],
        ['armnatta2@gmail.com','กลุ่มงานบริการด้านปฐมภูมิและองค์รวม','งานสุขภาพจิตและยาเสพติด'],
        ['Penprasinghan@gmail.com','กลุ่มงานพยาบาล','งานอุบัติเหตุฉุกเฉิน'],
        ['nngkhnuchphrhmngxy@gmail.com','กลุ่มงานเวชศาสตร์ฟื้นฟู','ฝ่ายเวชกรรมฟื้นฟู'],
        ['so_@hotmail.com','กลุ่มงานบริหารทั่วไป','งานภูมิทัศน์'],
        ['Buakhai2521@gmail.com','กลุ่มงานการแพทย์แผนไทยและการแพทย์ทางเลือก','งานแพทย์แผนไทย'],
        ['kemmawat@hotmail.com','กลุ่มงานบริหารทั่วไป','งานยานพาหนะ'],
        ['pjpj40831@gmail.com','กลุ่มงานบริหารทั่วไป','งานซักฟอก'],
        ['soraya.muntee@gmail.com','กลุ่มงานบริหารทั่วไป','งานพัสดุ'],
        ['ji@hotmail.com','กลุ่มงานทันตกรรม','ฝ่ายทันตสาธารณสุข'],
        ['botaongoi@gmail.com','กลุ่มงานประกันสุขภาพยุทธศาสตร์และสารสนเทศทางการแพทย์','งานเวชระเบียน'],
        ['she_@hotmail.com','กลุ่มงานทันตกรรม','ฝ่ายทันตสาธารณสุข'],
        ['praneet_k@hotmail.com','กลุ่มงานบริหารทั่วไป','งานซ่อมบำรุง'],
        ['nu_@hotmail.com','กลุ่มงานโภชนศาสตร์','งานโภชนศาสตร์'],
        ['Tanayod2211@gmail.com','กลุ่มงานบริหารทั่วไป','งานยานพาหนะ'],
        ['Riaw_La@gmail.com','กลุ่มงานบริหารทั่วไป','งานยานพาหนะ'],
        ['ru_ng@hotmail.com','กลุ่มงานบริหารทั่วไป','งานยานพาหนะ'],
        ['wiirat1234@gmail.com','กลุ่มงานประกันสุขภาพยุทธศาสตร์และสารสนเทศทางการแพทย์','งานเวชระเบียน'],
        ['narubeth12@gmail.com','กลุ่มงานประกันสุขภาพยุทธศาสตร์และสารสนเทศทางการแพทย์','งานเวชระเบียน'],
        ['montira4248@gmail.com','กลุ่มงานประกันสุขภาพยุทธศาสตร์และสารสนเทศทางการแพทย์','งานศูนย์ประกันสุขภาพ'],
        ['arsa1970000@gmail.com','กลุ่มงานเทคนิคการแพทย์','งานเทคนิคการแพทย์'],
        ['chamaiporn_@hotmail.com','กลุ่มงานพยาบาล','งานอุบัติเหตุฉุกเฉิน'],
        ['ch_@hotmail.com','กลุ่มงานพยาบาล','งานการพยาบาลหน่วยควบคุมการติดเชื้อและงานจ่ายกลาง'],
        ['lawongkerdkan@gmail.com','กลุ่มงานพยาบาล','งานอุบัติเหตุฉุกเฉิน'],
        ['pata_@hotmail.com','กลุ่มงานพยาบาล','งานการพยาบาลหน่วยควบคุมการติดเชื้อและงานจ่ายกลาง'],
        ['su_wa@hotmail.com','กลุ่มงานพยาบาล','งานการพยาบาลหน่วยควบคุมการติดเชื้อและงานจ่ายกลาง'],
        ['sa1470500@gmail.com','กลุ่มงานบริหารทั่วไป','งานการเงิน'],
        ['sirikan.udorn@gmail.com','กลุ่มงานเภสัชกรรมและคุ้มครองผู้บริโภค','ฝ่ายเภสัชกรรมชุมชน'],
        ['june19jjune@gmail.com','กลุ่มงานบริหารทั่วไป','งานธุรการ'],
        ['sang_Jun@hotmail.com','กลุ่มงานพยาบาล','งานอุบัติเหตุฉุกเฉิน'],
        ['thipph_ha@hotmail.com','กลุ่มงานบริการด้านปฐมภูมิและองค์รวม','งานเวชปฏิบัติครอบครัว'],
        ['siyanun301@gmail.com','กลุ่มงานพยาบาล','งานการพยาบาลผู้ป่วยใน'],
        ['taksapornchantima@gmail.com','กลุ่มงานบริการด้านปฐมภูมิและองค์รวม','งานเวชปฏิบัติครอบครัว'],
        ['kraaew@gmail.com','กลุ่มงานการแพทย์แผนไทยและการแพทย์ทางเลือก','งานแพทย์แผนไทย'],
        ['jureeratjuryry@gmail.com','กลุ่มงานพยาบาล','งานอุบัติเหตุฉุกเฉิน'],
        ['pimwara29@gmail.com','กลุ่มงานบริหารทั่วไป','งานบริหารงานทั่วไป'],
        ['pirin.jija@gmail.com','กลุ่มงานเทคนิคการแพทย์','งานเทคนิคการแพทย์'],
        ['yonrada21@hotmail.com','กลุ่มงานเวชศาสตร์ฟื้นฟู','ฝ่ายเวชกรรมฟื้นฟู'],
        ['fonfun1928@gmail.com','กลุ่มงานทันตกรรม','ฝ่ายทันตสาธารณสุข'],
        ['wachiraphorn.phl@gmail.com','กลุ่มงานบริการด้านปฐมภูมิและองค์รวม','งานเวชปฏิบัติครอบครัว'],
        ['phonpk1999@gmail.com','กลุ่มงานพยาบาล','งานอุบัติเหตุฉุกเฉิน'],
        ['runchiya@hotmail.com','กลุ่มงานพยาบาล','งานการพยาบาลผู้คลอดเเละทารกเเรกเกิด'],
        ['jan@hotmail.com','กลุ่มงานพยาบาล','งานการพยาบาลผู้คลอดเเละทารกเเรกเกิด'],
        ['kwang_der@hotmail.com','กลุ่มงานพยาบาล','งานการพยาบาลผู้คลอดเเละทารกเเรกเกิด'],
        ['pongrakth15@gmail.com','กลุ่มงานพยาบาล','งานการพยาบาลผู้คลอดเเละทารกเเรกเกิด'],
        ['hongtong.chan@gmail.com','กลุ่มงานบริการด้านปฐมภูมิและองค์รวม','งานสุขภาพจิตและยาเสพติด'],
        ['tairajpo@hotmail.com','กลุ่มงานบริการด้านปฐมภูมิและองค์รวม','งานเวชปฏิบัติครอบครัว'],
        ['suwimon19951995@gmail.com','กลุ่มงานพยาบาล','งานอุบัติเหตุฉุกเฉิน'],
        ['Jutharmas@gmail.com','กลุ่มงานพยาบาล','งานผู้ป่วยนอก'],
        ['th_@hotmail.com','กลุ่มงานพยาบาล','งานอุบัติเหตุฉุกเฉิน'],
        ['Atchii.puy@gmail.com','กลุ่มงานพยาบาล','งานอุบัติเหตุฉุกเฉิน'],
        ['yyy_ao@hotmail.com','กลุ่มงานพยาบาล','งานอุบัติเหตุฉุกเฉิน'],
        ['throngkeawmaneekan@gmail.com','กลุ่มงานพยาบาล','งานผู้ป่วยนอก'],
        ['sumalinee@hotmail.com','กลุ่มงานพยาบาล','บริหารกลุ่มการพยาบาล'],
        ['smallnurse111@gmail.com','กลุ่มงานพยาบาล','งานผู้ป่วยนอก'],
        ['n@hotmail.com','กลุ่มงานเภสัชกรรมและคุ้มครองผู้บริโภค','ฝ่ายเภสัชกรรมชุมชน'],
        ['Preeyanutkham2532@gmail.com','กลุ่มงานเภสัชกรรมและคุ้มครองผู้บริโภค','ฝ่ายเภสัชกรรมชุมชน'],
        ['pawa_@hotmail.com','กลุ่มงานเภสัชกรรมและคุ้มครองผู้บริโภค','ฝ่ายเภสัชกรรมชุมชน'],
        ['dtida.rx@gmail.com','กลุ่มงานเภสัชกรรมและคุ้มครองผู้บริโภค','ฝ่ายเภสัชกรรมชุมชน'],
        ['spore.feelgood@gmail.com','กลุ่มงานเภสัชกรรมและคุ้มครองผู้บริโภค','ฝ่ายเภสัชกรรมชุมชน'],
        ['withit.n@ku.th','กลุ่มงานเภสัชกรรมและคุ้มครองผู้บริโภค','ฝ่ายเภสัชกรรมชุมชน'],
        ['siraboocha@gmail.com','กลุ่มงานเภสัชกรรมและคุ้มครองผู้บริโภค','ฝ่ายเภสัชกรรมชุมชน'],
        ['samart.bu@kkumail.com','กลุ่มงานเทคนิคการแพทย์','งานเทคนิคการแพทย์'],
        ['khaekhai08@gmail.com','กลุ่มงานเทคนิคการแพทย์','งานเทคนิคการแพทย์'],
        ['aaa_aaa@hotmail.com','กลุ่มงานรังสีวิทยา','งานรังสี'],
        ['num.nanbo@gmail.com','กลุ่มงานการแพทย์แผนไทยและการแพทย์ทางเลือก','งานแพทย์แผนไทย'],
        ['katsukipai16@gmail.com','กลุ่มงานเวชศาสตร์ฟื้นฟู','ฝ่ายเวชกรรมฟื้นฟู'],
        ['khuanchewa.pp@gmail.com','กลุ่มงานบริการด้านปฐมภูมิและองค์รวม','งานเวชปฏิบัติครอบครัว'],
        ['keattisk23@gmail.com','กลุ่มงานบริการด้านปฐมภูมิและองค์รวม','สุขาภิบาลและป้องกันโรค'],
        ['suangsuda.boocha@gmail.com','กลุ่มงานประกันสุขภาพยุทธศาสตร์และสารสนเทศทางการแพทย์','งานศูนย์ประกันสุขภาพ'],
        ['no@hotmail.com','กลุ่มงานบริหารทั่วไป','งานการเงิน'],
        ['win_aon@hotmail.com','กลุ่มงานบริหารทั่วไป','งานบริหารงานทั่วไป'],
        ['kittikawin0023@gmail.com','กลุ่มงานทันตกรรม','ฝ่ายทันตสาธารณสุข'],
        ['Suda.tipnaruk@gmail.com','กลุ่มงานทันตกรรม','ฝ่ายทันตสาธารณสุข'],
        ['panassaya@hotmail.com','กลุ่มงานทันตกรรม','ฝ่ายทันตสาธารณสุข'],
        ['้high_fire@windowslive.com','กลุ่มงานทันตกรรม','ฝ่ายทันตสาธารณสุข'],
        ['su_@hotmail.com','กลุ่มงานพยาบาล','งานการพยาบาลผู้ป่วยใน'],
        ['pjpj73370@gmail.com','กลุ่มงานบริหารทั่วไป','งานทำความสะอาด'],
        ['gggg_@hotmail.com','กลุ่มงานโภชนศาสตร์','งานโภชนศาสตร์'],
        ['chairat102523@gmail.com','กลุ่มงานพยาบาล','งานผู้ป่วยนอก'],
        ['Panapong861kp@gmail.com','กลุ่มงานพยาบาล','งานผู้ป่วยนอก'],
        ['tiraphon.4@gmail.com','กลุ่มงานพยาบาล','งานผู้ป่วยนอก'],
        ['diraklitthintongkhob@gmail.com','กลุ่มงานพยาบาล','งานผู้ป่วยนอก'],
        ['suris_@hotmail.com','กลุ่มงานพยาบาล','งานผู้ป่วยนอก'],
        ['kulthidaphichaykha2@gmail.com','กลุ่มงานพยาบาล','งานการพยาบาลผู้คลอดเเละทารกเเรกเกิด'],
        ['pa_@hotmail.com','กลุ่มงานพยาบาล','งานการพยาบาลผู้ป่วยใน'],
        ['tutsanee1993@gmail.com','กลุ่มงานพยาบาล','งานการพยาบาลผู้ป่วยใน'],
        ['waraphon@hotmail.com','กลุ่มงานพยาบาล','งานการพยาบาลผู้ป่วยใน'],
        ['maneegorn53@gmail.com','กลุ่มงานพยาบาล','งานการพยาบาลผู้ป่วยใน'],
        ['Panadda2647@gmail.com','กลุ่มงานพยาบาล','งานผู้ป่วยนอก'],
        ['mukda4696@gmail.com','กลุ่มงานพยาบาล','งานการพยาบาลผู้ป่วยใน'],
        ['cha_@hotmail.com','กลุ่มงานพยาบาล','งานการพยาบาลผู้ป่วยใน'],
        ['maneerat@hotmail.com','กลุ่มงานพยาบาล','งานการพยาบาลผู้ป่วยใน'],
        ['Ebeeve07052538@gmail.com','กลุ่มงานพยาบาล','งานการพยาบาลผู้ป่วยใน'],
        ['dewpawinee77@gmail.com','กลุ่มงานพยาบาล','งานการพยาบาลผู้ป่วยใน'],
        ['sanhawat@hotmail.com','กลุ่มงานการแพทย์','การแพทย์'],
        ['rabot.tha@gmail.com','กลุ่มงานการแพทย์','การแพทย์'],
        ['thi@hotmail.com','กลุ่มงานการแพทย์','การแพทย์'],
        ['rinradabam.p11@gmail.com','กลุ่มงานการแพทย์','การแพทย์'],
        ['dekdorn@gmail.com','กลุ่มงานประกันสุขภาพยุทธศาสตร์และสารสนเทศทางการแพทย์','สารสนเทศทางการแพทย์'],
        ['wichetpong159@hotmail.com','กลุ่มงานประกันสุขภาพยุทธศาสตร์และสารสนเทศทางการแพทย์','สารสนเทศทางการแพทย์'],


        ];

         DB::statement('SET FOREIGN_KEY_CHECKS=0;');
                KpUserWastePreference::truncate();
                KpBankAccount::truncate();
                DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            foreach($arr as $a){

                $user = User::where('email', $a[0])->get()->first();
               
                $user_pref = KpUserWastePreference::create([
                    'user_id' => $user->id,
                    'org_id_fk' => 2,
                    'zone_id' => $user->zone_id,
                    'subzone_id' => $user->subzone_id
                ]);

                $orgIdStr =  substr('000', strlen('2')) . '2';
                $userIdStr = substr('0000', strlen($user->id)) . $user->id;
                $user_prefStr = substr('0000', strlen($user_pref->id)) . $user_pref->id;

                // 1. จัดการธนาคารขยะรีไซเคิล (recycle_)

                KpBankAccount::firstOrCreate(
                        ['user_pref_id' => $user_pref->id],
                        [
                            'account_no' => 'RC-' . $orgIdStr . "-" . $userIdStr . $user_prefStr,
                            'status' => 'active',
                            'org_id_fk' => Auth::user()->org_id_fk
                        ],
                    );
                $user->assignRole('Recycle Bank User');
            
                // $zone = Zone::where('zone_name', $a[1])->get(['id']);
                // $subzone = Subzone::where('subzone_name', $a[2])->get(['id']);
                // if(collect($zone)->isEmpty()){
                //     return 'zone->'.$a[1];
                // }
                // $subzone_id = 0;
                // if(collect($subzone)->isEmpty()){
                //     $sz = Subzone::create([
                //         'zone_id' => $zone[0]->id,
                //         'subzone_name' => $a[2],
                //         'status' => 'active'
                //     ]);

                //     $subzone_id = $sz->id;
                //     //return 'zone->id'.$zone[0]->id .' ==> '.$a[2];
                // }else{
                //             $subzone_id = $subzone[0]->id;

                // }
                // $user->zone_id  = $zone[0]->id;
                // $user->subzone_id  = $subzone_id;
                // $user->save();
            }
            return 'xxx';
    }

    public function users_search(Request $request)
    {
        $users = User::role("user")->whereIn("zone_id", $request->input("zone"))->get();
        $usertype = "user";
        $zones = Zone::all();
        return view('admin.users.index', compact('users', 'usertype', 'zones'));
    }


    public function staff()
    {
        $users = User::with('roles')
            ->get()->filter(
                fn($user) => $user->roles->whereIn('name', ["admin", "tabwater man", "finance"])->toArray()
            );
        $usertype = "staff";
        return view('admin.users.index', compact('users', 'usertype'));
    }

    public function create()
    {

        // ดึง Org ของผู้ใช้งานที่ Logged in อยู่
        $org = Organization::find(Auth::user()->org_id_fk);

        // ดึง Zone ที่สังกัด Org เดียวกันเท่านั้น
        $zones = Zone::all();

        $defaultAddress = [
            'province' => $org->provinces->province_name ?? '-',
            'district'  => $org->districts->district_name ?? '-',
            'tambon' => $org->tambons->tambon_name ?? '-'
        ];

        return view('admin.users.create', compact('zones', 'defaultAddress'));
    }


    // 1. Validation ข้อมูลพื้นฐาน
    public function store(Request $request)
    {
        // กำหนดข้อความ Error เป็นภาษาไทย (Custom Messages)
        $messages = [
            'required' => 'กรุณากรอกข้อมูลในช่อง :attribute',
            'unique'   => ':attribute นี้มีอยู่ในระบบแล้ว',
            'min'      => ':attribute ต้องมีความยาวอย่างน้อย :min ตัวอักษร',
            'confirmed' => 'การยืนยันรหัสผ่านไม่ตรงกัน',
        ];

        // กำหนดชื่อเรียกฟิลด์เป็นภาษาไทย
        $attributes = [
            'username' => 'ชื่อผู้ใช้งาน',
            'password' => 'รหัสผ่าน',
            'firstname' => 'ชื่อจริง',
            'lastname' => 'นามสกุล',
            'phone' => 'เบอร์โทรศัพท์',
            'zone_id' => 'โซน',
            'subzone_id' => 'ซอย/ชุมชนย่อย',
        ];

        $request->validate([
            'username'   => 'required|string|max:255', //unique:users,username
            'password'   => 'required|string|min:6',
            'firstname'  => 'required|string|max:255',
            'lastname'   => 'required|string|max:255',
            'phone'      => 'required', //|unique:users,phone
            'zone_id'    => 'required|exists:zones,id', // ตรวจสอบชื่อ table ให้ตรง
            'subzone_id' => 'required|exists:subzones,id',
        ], $messages, $attributes);
        DB::beginTransaction();
        try {
            $org = Organization::find(Auth::user()->org_id_fk);
            // 2. สร้าง User หลัก
            $user = User::create([
                'username'      => $request->username,
                'password'      => Hash::make($request->password),
                'firstname'     => $request->firstname,
                'lastname'      => $request->lastname,
                'phone'         => $request->phone,
                'address'       => $request->address,
                'zone_id'       => $request->zone_id,
                'subzone_id'    => $request->subzone_id,
                'tambon_code'   => $org->org_tambon_id_fk,
                'district_code' => $org->org_district_id_fk,
                'province_code' => $org->org_province_id_fk,
                'org_id_fk'     => $org->id,
                'status'        => 'active',
            ]);

            $user->assignRole('User');

            // 3. เช็คและเปิดบริการตามที่ติ๊กมา
            // ธนาคารขยะรีไซเคิล
            if ($request->has('svc_recycle')) {
                KpBankAccount::create([
                    'user_id'    => $user->id,
                    'account_no' => 'RC-' . strtoupper(uniqid()),
                    'balance'    => 0,
                    'status'     => 'active'
                ]);
            }

            // ธนาคารขยะเปียก
            if ($request->has('svc_food_waste')) {
                FoodWasteAccount::create([
                    'user_id'               => $user->id,
                    'total_weight_kg'       => 0,
                    'last_contributed_at'   => now(),
                ]);
            }

            if ($request->has('svc_annual_trash')) {
                // 1. หาอัตราค่าบริการปัจจุบัน
                $payRate = AnnualTrashPayratePerMonth::where('status', 1)->latest()->first();
                $monthFee = $payRate ? $payRate->payrate_permonth : 20;

                // 2. คำนวณปีงบประมาณอัตโนมัติ (ใช้ฟังก์ชันใน Model ที่คุณมี)
                $fiscalYear = AnnualTrashSubscription::calculateFiscalYear();

                // 3. สร้าง Subscription
                // หมายเหตุ: ปกติ Subscription ต้องผูกกับถังขยะ (waste_bin_id)
                // หากตอนสมัครยังไม่มีถัง ให้สร้างถังขยะ "ใบแรก" ให้เขาก่อน หรืออนุญาตให้ waste_bin_id เป็น null ได้
                $subscription = AnnualTrashSubscription::create([
                    'waste_bin_id'           => $newAnnualTrash->id ?? null, // ผูกกับถังขยะ
                    'fiscal_year'            => $fiscalYear,
                    'payrate_permonth_id_fk' => $payRate->id ?? null,
                    'month_fee'              => $monthFee,
                    'annual_fee'             => $monthFee * 12,
                    'total_paid_amt'         => 0,
                    'status'                 => 'active',
                ]);
            }


            new KpUserWastePreference([
                'user_id' => $user->id,
                'is_annual_collection' => $request->has('svc_annual_trash') ? 1 : 0,
                'is_waste_bank' => $request->has('svc_recycle') ? 1 : 0,
            ]);

            DB::commit();
            return redirect()->route('admin.users.index')->with('success', 'เพิ่มผู้ใช้งานและเปิดบริการเรียบร้อยแล้ว');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage());
        }
    }


    public function edit($user_id, $addmeter = "")
    {
        // 1. ดึงข้อมูล User พร้อมความสัมพันธ์ที่จำเป็น
        // เปลี่ยนจาก .get() เป็น .firstOrFail() เพื่อให้ได้ Object ตัวเดียว
        $user = User::where('id', $user_id)
            ->with([
                'user_zone',
                'user_subzone',
                'recycleBankAccount',
                'foodWasteAccount',
                'annualTrashSubscription'
            ])
            ->firstOrFail();

        // 2. ดึงข้อมูล Organization ของแอดมินเพื่อใช้เป็นที่อยู่ฐาน (ตำบล/อำเภอ/จังหวัด)
        $org = Organization::find(Auth::user()->org_id_fk);
        $defaultAddress = [
            'province' => $org->province ?? '-',
            'amphure'  => $org->amphure ?? '-',
            'district' => $org->district ?? '-'
        ];

        // 3. ข้อมูลสำหรับ Dropdown
        $zones = Zone::where('org_id_fk', Auth::user()->org_id_fk)->get();

        // ดึง Subzone ของโซนที่ User คนนี้สังกัดอยู่มาโชว์รอไว้เลย
        $subzones = Subzone::where('zone_id', $user->zone_id)->get();

        return view('admin.users.edit', compact(
            'user',
            'zones',
            'subzones',
            'defaultAddress',
            'addmeter'
        ));
    }

    // public function edit($user_id, $addmeter = "")
    // {
    //     $meter_id = $user_id;
    //     $user = TwUsersInfos::where('meter_id', $meter_id)
    //         ->with('user', 'undertake_subzone')
    //         ->get();
    //     $zones = Zone::all();
    //     $meter_types = TwMeterType::all();
    //     return view('admin.users.edit', compact('user', 'zones', 'meter_types', 'addmeter'));
    // }

    // public function update(Request $request,  $meter_id)
    // {

    //     $checkDuplicateFactNo = TwUsersInfos::where('factory_no', $request->get('factory_no'))->count();
    //     if ($checkDuplicateFactNo > 1) {
    //         return redirect()->route('admin.users.index')->with(['message' => 'ไม่สามารถบันทึกข้อมูลได้ \nกรุณาตรวจสอบ รหัสมิเตอร์จากโรงงานเป็นค่าว่าง หรือ ถูกใช้งานแล้ว', 'color' => 'warning']);
    //     }
    //     $temp_password = User::where('id', $request->get('user_id'))->get('password')->first();
    //     $request->merge([
    //         'password' => collect($request->password)->isEmpty() ? $temp_password->password : Hash::make($request->password)
    //     ]);
    //     $request->validate(
    //         [
    //             "username"          => 'required',
    //             "password"          => 'required',
    //             "prefix_select"     => 'required',
    //             "firstname"         => 'required',
    //             "gender"            => 'required|in:w,m',
    //             "id_card"           => 'required',
    //             "phone"             => 'required',
    //             "address"           => 'required',
    //             "province_code"     => 'required|integer',
    //             "metertype_id"      => 'required|integer',
    //             "zone_id"           => 'required',
    //             "factory_no"        => 'required',
    //             "undertake_zone_id" => 'required|integer',
    //         ],
    //         [
    //             "required"  => "ใส่ข้อมูล",
    //             "in"        => "เลือกข้อมูล",
    //             "integer"   => "เลือกข้อมูล",
    //         ],

    //     );

    //     //user table
    //     User::where('id', $request->get('user_id'))->update([
    //         "username"      => $request->username,
    //         "password"      => $request->password,
    //         "email"         => $request->email,
    //         "prefix"        => $request->get('prefix_select') == "other" ? $request->get('prefix_text') : $request->get('prefix_select'),
    //         "firstname"     => $request->get('firstname'),
    //         "lastname"      => $request->get('lastname'),
    //         "id_card"       => $request->get('id_card'),
    //         "phone"         => $request->get('phone'),
    //         "gender"        => $request->get("gender"),
    //         "address"       => $request->get("address"),
    //         "zone_id"       => $request->get("zone_id"),
    //         "subzone_id"    => $request->get("zone_id"),
    //         "tambon_code"   => $request->get("tambon_code"),
    //         "district_code" => $request->get("district_code"),
    //         "province_code" => $request->get("province_code"),
    //         "status"        => 1,
    //         "updated_at"    => date("Y-m-d H:i:s"),
    //     ]);
    //     //usermeterinfo table
    //     if (collect($request->get('addmeter'))->isNotEmpty()) {
    //         $number_sequence = SequenceNumber::where('id', 1)->get();

    //         TwUsersInfos::create([
    //             "meter_id"              => $number_sequence[0]->tabmeter,
    //             "user_id"               => $request->get('user_id'),
    //             "meternumber"           => FunctionsController::createMeterNumberString($number_sequence[0]->tabmeter),
    //             "submeter_name"         => $request->get('submeter_name'),
    //             "undertake_zone_id"     => $request->get('undertake_zone_id'),
    //             "undertake_subzone_id"  => $request->get('undertake_subzone_id'),
    //             "factory_no"            => $request->get('factory_no'),
    //             "metertype_id"          => $request->get('metertype_id'),
    //             "meter_address"         => $request->get('address'),
    //             "acceptance_date"       => date('Y-m-d'),
    //             "payment_id"            => 1,
    //             "owe_count"             => 0,
    //             "status"                => "active",
    //             "recorder_id"           => Auth::id(),
    //             "created_at"            => date("Y-m-d H:i:s"),
    //             "updated_at"            => date("Y-m-d H:i:s"),
    //         ]);
    //         SequenceNumber::where('id', 1)->update([
    //             'tabmeter' => $number_sequence[0]->tabmeter + 1,
    //         ]);
    //     } else {
    //         TwUsersInfos::where('meter_id', $meter_id)->update([
    //             "metertype_id"          => $request->get('metertype_id'),
    //             "submeter_name"         => $request->get('submeter_name'),
    //             "undertake_zone_id"     => $request->get('undertake_zone_id'),
    //             "undertake_subzone_id"  => $request->get('undertake_subzone_id'),
    //             "factory_no"            => $request->get('factory_no'),
    //             "recorder_id"           => Auth::id(),
    //             "updated_at"            => date("Y-m-d H:i:s"),
    //         ]);
    //     }


    //     return redirect()->route('admin.users.index')->with(['messege', 'บันทึกแล้ว', 'color' => 'success']);
    // }

    public function update(Request $request, $id)
    {
        // 1. Validation (ยกเว้น unique ของตัวมันเอง)
        $messages = [
            'required' => 'กรุณากรอกข้อมูลในช่อง :attribute',
            'unique'   => ':attribute นี้ถูกใช้งานแล้ว',
            'confirmed' => 'การยืนยันรหัสผ่านไม่ตรงกัน',
        ];

        $request->validate([
            'username'   => 'required|string|max:255|unique:users,username,' . $id,
            'firstname'  => 'required|string|max:255',
            'lastname'   => 'required|string|max:255',
            'phone'      => 'required|unique:users,phone,' . $id,
            'password'   => 'nullable|min:6|confirmed', // เปลี่ยนรหัสผ่านเฉพาะเมื่อมีการกรอกเท่านั้น
            'zone_id'    => 'required|exists:kp_zones,id',
            'subzone_id' => 'required|exists:kp_subzones,id',
        ], $messages);

        DB::beginTransaction();
        try {
            $user = User::findOrFail($id);

            // 2. อัปเดตข้อมูลพื้นฐาน
            $user->username = $request->username;
            $user->firstname = $request->firstname;
            $user->lastname = $request->lastname;
            $user->phone = $request->phone;
            $user->address = $request->address;
            $user->zone_id = $request->zone_id;
            $user->subzone_id = $request->subzone_id;

            // ถ้ามีการกรอกรหัสผ่านใหม่เข้ามา
            if ($request->filled('password')) {
                $user->password = Hash::make($request->password);
            }

            $user->save();

            // 3. จัดการสิทธิ์บริการ (Service Logic)

            // --- ธนาคารขยะรีไซเคิล ---
            if ($request->has('svc_recycle')) {
                // ใช้ firstOrCreate เพื่อไม่ให้สร้างซ้ำถ้ามีอยู่แล้ว
                KpBankAccount::firstOrCreate(
                    ['user_id' => $user->id],
                    ['status' => 'active', 'balance' => 0]
                );
            }

            // 4. บริการธนาคารขยะเปียก (AiroBact)
            if ($request->has('svc_food_waste')) {
                // 🌟 สำคัญ: ต้องสร้าง Preference ก่อน เพื่อป้องกัน Error ใน Dashboard
                $preference = FoodWasteUserPreference::create([
                    'user_id' => $user->id,
                    'setup_status' => 'completed',
                    'compost_bin_type' => 'AiroBact_Bin',
                ]);

                // สร้าง Account โดยผูกกับ User หรือ Preference (ตามโครงสร้าง DB ล่าสุดของคุณ)
                FoodWasteAccount::create([
                    'user_id' => $user->id,
                    // 'fw_pref_id_fk' => $preference->id, // ถ้า DB ใช้ตัวนี้ให้เปิดบรรทัดนี้แทน
                    'points_balance' => 0,
                    'total_weight_kg' => 0,
                ]);
            }

            // 5. บริการขยะรายปี
            if ($request->has('svc_annual_trash')) {
                $payRate = AnnualTrashPayratePerMonth::where('status', 1)->latest()->first();
                $monthFee = $payRate ? $payRate->payrate_permonth : 20.00;

                AnnualTrashSubscription::create([
                    'user_id' => $user->id,
                    'fiscal_year' => AnnualTrashSubscription::calculateFiscalYear(),
                    'payrate_permonth_id_fk' => $payRate->id ?? null,
                    'month_fee' => $monthFee,
                    'annual_fee' => $monthFee * 12,
                    'status' => 'active',
                    'billing_status' => 'waived', // ให้สิทธิ์ฟรีเริ่มต้น
                ]);
            }


            DB::commit();
            return redirect()->route('admin.users.index')->with('success', 'อัปเดตข้อมูลและสิทธิ์บริการเรียบร้อยแล้ว');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage());
        }
    }

    // กำหนดค่าเริ่มต้นให้ $action เป็น null เพื่อป้องกัน Error เวลาส่งมาตัวเดียว
    public function show($function, $action = null)
    {
        if ($function == 'store') {
            return $action . "Error";
        }

        // Logic ปกติสำหรับแสดง User ถ้าไม่ใช่เคส store
        $user = User::find($function);
        return view('admin.users.show', compact('user'));
    }

    public function history(User $user)
    {
        $user = User::with('usermeterinfos', 'usermeterinfos.invoice')->where('id', $user->id)->get();
        return view('admin.users.history', compact('user'));
    }
    public function assignRole(Request $request, User $user)
    {
        if ($user->hasRole($request->role)) {
            return back()->with('message', 'Role exists.');
        }

        $user->assignRole($request->role);
        return back()->with('message', 'Role assigned.');
    }

    public function cancel($user_id)
    {
        //check ว่ามีค้างจ่ายไหม
        $user = User::where('id', $user_id)
            ->with([
                'usermeterinfos',
                'usermeterinfos.invoice' => function ($query) {
                    return $query->select('meter_id_fk', 'inv_id', 'status')
                        ->whereIn('status', ['init', 'tw_invoices', 'owe']);
                }
            ])
            ->get();
        return view('admin.users.cancel', compact('user'));
    }

    public function removeRole(User $user, Role $role)
    {
        if ($user->hasRole($role)) {
            $user->removeRole($role);
            return back()->with('message', 'Role removed.');
        }

        return back()->with('message', 'Role not exists.');
    }
    public function givePermission(Request $request, User $user)
    {
        return $user;
        if ($user->hasPermissionTo($request->permission)) {
            return back()->with('message', 'Permission exists.');
        }
        $user->givePermissionTo($request->permission);
        return back()->with('message', 'Permission added.');
    }

    public function revokePermission(User $user, Permission $permission)
    {
        if ($user->hasPermissionTo($permission)) {
            $user->revokePermissionTo($permission);
            return back()->with('message', 'Permission revoked.');
        }
        return back()->with('message', 'Permission does not exists.');
    }
    public function destroy($meter_id)
    {
        $usermeterinfos = TwUsersInfos::where('meter_id', $meter_id)->get(['user_id', 'meter_id'])->first();

        $user = User::find($usermeterinfos->user_id);
        if ($user->hasRole('admin')) {
            return back()->with('message', 'you are admin.');
        }

        $invoices = TwInvoice::where('meter_id_fk', $usermeterinfos->meter_id)->get();
        $invoicesHistory = TwInvoiceHistory::where('meter_id_fk', $usermeterinfos->meter_id)->get();

        foreach ($invoices as $invoice) {
            if ($invoice->status == 'init') {
                TwInvoice::where('inv_id', $invoice->inv_id)->delete();
            } else if ($invoice->status == 'tw_invoices') {
                TwInvoice::where('inv_id', $invoice->inv_id)->update([
                    'status'        => 'owe',
                    'updated_at'    => date('Y-m-d H:i:s')

                ]);
            }
        }
        TwInvoice::where('meter_id_fk', $usermeterinfos->meter_id)->update([
            'deleted' => '1',
        ]);
        TwInvoiceHistory::where('meter_id_fk', $usermeterinfos->meter_id)->update([
            'deleted' => '1',
        ]);
        $checkInvoiceHasHistoryInfos = collect($invoices)->filter(function ($v) {
            return $v->status == 'paid' || $v->status == 'owe';
        })->count();
        $checkInvoiceHistoryHasHistoryInfos = collect($invoicesHistory)->filter(function ($v) {
            return $v->status == 'paid';
        })->count();
        TwUsersInfos::where('meter_id', $meter_id)->update([
            'status'        => $checkInvoiceHasHistoryInfos > 0 && $checkInvoiceHistoryHasHistoryInfos > 0  ? 'deleted' : 'inactive',
            'deleted'       => '1',
            'comment'       => $checkInvoiceHasHistoryInfos > 0 && $checkInvoiceHistoryHasHistoryInfos > 0 ? 'ยกเลิกการใช้งาน' :  'ยกเลิกการใช้งานแต่มีข้อมูลเก่า',
            'updated_at'    => date('Y-m-d H:i:s')
        ]);

        $checkHaveMeternumber = $usermeterinfos::where([
            'status' => 'active',
            'user_id' => $usermeterinfos->user_id
        ])->count();

        if ($checkHaveMeternumber == 0) {
            $user->update([
                'status'        => 'deleted',
                'comment'       => 'ยกเลิกการใช้งาน',
                'updated_at'    => date('Y-m-d H:i:s')
            ]);
        }



        // $user->delete();
        // FunctionsController::reset_auto_increment_when_deleted('users');
        return redirect()->route('admin.users.index')->with(['message' => 'ทำการลบข้อมูลผู้ใช้งานระบบเรียบร้อยแล้ว', 'color' => 'success']);
    }

    public function showRegistrationForm()
    {
        $organizations = Organization::all();

        return view('auth.register', compact('organizations'));
    }

    /**
     * Handle user registration.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function register(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:255|unique:users,username',
            'prefix' => 'nullable|string|max:255',
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'id_card' => 'nullable|string|max:13|unique:users,id_card',
            'line_id' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'gender' => 'nullable|in:male,female,other',
            'address' => 'nullable|string',
            'organization_id' => 'nullable|exists:organizations,id',
            'zone_id' => 'nullable|exists:zones,id',
            'subzone_id' => 'nullable|exists:subzones,id', // Assuming subzones is the table name
            'tambon_code' => 'nullable|string|max:10',
            'district_code' => 'nullable|string|max:10',
            'province_code' => 'nullable|string|max:10',
            'status' => 'nullable|string|in:active,inactive,pending',
        ]);

        $user = User::create([
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'prefix' => $request->prefix,
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'name' => $request->firstname . ' ' . $request->lastname,
            'email' => $request->email,
            'id_card' => $request->id_card,
            'line_id' => $request->line_id,
            'phone' => $request->phone,
            'gender' => $request->gender,
            'address' => $request->address,
            'organization_id' => $request->organization_id,
            'zone_id' => $request->zone_id,
            'subzone_id' => $request->subzone_id,
            'tambon_code' => $request->tambon_code,
            'district_code' => $request->district_code,
            'province_code' => $request->province_code,
            'status' => $request->status ?? 'pending', // Default to 'pending'
        ]);

        // You might want to log the user in automatically after registration
        // Auth::login($user);

        return redirect()->route('login')->with('success', 'Registration successful! Please log in.');
    }

    public function users_by_subzone($subzone_id)
    {
        $users = User::where('subzone_id', $subzone_id)
            ->get(['id', 'firstname', 'lastname', 'subzone_id']);
        return response()->json($users);
    }

    public function updateMetrics(Request $request)
    {
        $request->validate([
            'age'    => 'required|integer|min:1|max:120',
            'weight' => 'required|numeric|min:10|max:300',
            'height' => 'required|integer|min:50|max:250',
            'gender' => 'required|in:male,female',
        ]);

        $user = User::find(Auth::id());
        $user->update([
            'age'    => $request->age,
            'weight' => $request->weight,
            'height' => $request->height,
            'gender' => $request->gender == 'male' ? 'm' : 'f',
        ]);

        return back()->with('success', 'บันทึกข้อมูลร่างกายเรียบร้อยแล้ว');
    }


    public function updateService(Request $request)
    {
        $services = $request->input('services', []);
        DB::beginTransaction();
        try {
            foreach ($services as $userId => $data) {
                $user = User::find($userId);
        
                if (!$user) continue;
                $org = Organization::find(Auth::user()->org_id_fk);
                $orgIdStr =  substr('000', strlen($org->id)) . $org->id;
                $userIdStr = substr('0000', strlen($user->id)) . $user->id;
                // 1. จัดการธนาคารขยะรีไซเคิล (recycle_)
                if (isset($data['recycle']) && $data['recycle'] == "1") {

                    KpBankAccount::firstOrCreate(
                        ['user_id' => $user->id],
                        [
                            'account_no' => 'RC-' . $org->org_code . "-" . $orgIdStr . $userIdStr,
                            'status' => 'active',
                            'org_id_fk' => Auth::user()->org_id_fk
                        ],
                    );
                $user->assignRole('Recycle Bank User');

                

                } else {
                    // หากยกเลิกติ๊ก อาจจะเลือกปิดสถานะ แทนการลบข้อมูล
                    $user->kpBankAccount()->update(['status' => 'inactive']);
                }

                // 2. จัดการธนาคารขยะเปียก (food_waste_)
                if (isset($data['food_waste']) && $data['food_waste'] == "1") {
                    FoodWasteAccount::firstOrCreate([
                        'user_id' => $user->id,
                        'org_id_fk' => Auth::user()->org_id_fk,
                        'last_contributed_at' => date('Y-m-d H:i:s')
                    ]);
                } else {
                    $user->foodWasteAccount()->delete();
                }

                // 3. จัดการขยะรายปี (annual_trash_)
                if (isset($data['annual_trash']) && $data['annual_trash'] == "1") {
                    AnnualTrashSubscription::firstOrCreate(
                        ['user_id' => $user->id],
                        ['billing_status' => 'waived', 'monthly_fee' => 20.00]
                    );
                } else {
                    $user->annualTrashSubscription()->delete();
                }
            }

            DB::commit();
            return back()->with('success', 'บันทึกการตั้งค่าสิทธิ์ผู้ใช้งานเรียบร้อยแล้ว');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage());
        }
    }

    public function importUsers(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);

        try {
            Excel::import(new UserImport, $request->file('file'));
            return back()->with('success', 'นำเข้าข้อมูลผู้ใช้งานเรียบร้อยแล้ว');
        } catch (\Exception $e) {
            return back()->with('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage());
        }
    }


    public function downloadUserTemplate()
    {
        // ตั้งชื่อไฟล์ให้ชัดเจน
        return Excel::download(new UserTemplateExport, 'template_import_users.xlsx');
    }
}
