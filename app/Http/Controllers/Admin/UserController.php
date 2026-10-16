<?php

namespace App\Http\Controllers\Admin;

use App\Exports\UserTemplateExport;
use App\Http\Controllers\Controller;
use App\Imports\UserImport;
use App\Models\Admin\BudgetYear;
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
use App\Models\Tabwater\InvoicePeriod;
use App\Models\Tabwater\TwMeterInfos;
use App\Models\Tabwater\TwUsersInfos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Services\UserService;
class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 20);

        // ใช้ with() เพื่อป้องกัน N+1 Query (โหลดข้อมูลบัญชีมาพร้อมกันเลย)

        $query = User::with([
            'kpUserPreference',
            'recycleBankAccount',
            'foodWasteBankAccount',
            'foodWasteAccount',
            'annualTrashSubscription'
        ])
            ->whereHas('kpUserPreference', function ($q) {
                $q->where('org_id_fk', Auth::user()->org_id_fk);
            })
            // ->where('org_id_fk', Auth::user()->org_id_fk)
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

    protected function _index()
    {
        $arr = [
            ['Panthita51103@gmail.com', 'กลุ่มงานพยาบาล', 'งานการพยาบาลผู้ป่วยใน'],
            ['atcharawadeeritthikhan@gmail.com', 'กลุ่มงานพยาบาล', 'งานผู้ป่วยนอก'],
            ['padak_2526@hotmail.com', 'กลุ่มงานบริการด้านปฐมภูมิและองค์รวม', 'งานสุขภาพจิตและยาเสพติด'],
            ['Auraporn0611@gmail.com', 'กลุ่มงานบริหารทั่วไป', 'งานพัสดุ'],
            ['Jirakan2532n@gmail.com', 'กลุ่มงานพยาบาล', 'งานอุบัติเหตุฉุกเฉิน'],
            ['aemt65-23@scphub.ac.th', 'กลุ่มงานพยาบาล', 'งานอุบัติเหตุฉุกเฉิน'],
            ['bbb_@hotmail.com', 'กลุ่มงานบริหารทั่วไป', 'งานการเงิน'],
            ['kimmy.it4@gmail.com', 'กลุ่มงานประกันสุขภาพยุทธศาสตร์และสารสนเทศทางการแพทย์', 'งานเวชระเบียน'],
            ['ddd_@hotmail.com', 'กลุ่มงานการแพทย์แผนไทยและการแพทย์ทางเลือก', 'แพทย์แผนจีน'],
            ['janji.wit1988@gmail.com', 'กลุ่มงานบริหารทั่วไป', 'งานโภชนศาสตร์'],
            ['mery.ssbd@gmail.com', 'กลุ่มงานประกันสุขภาพยุทธศาสตร์และสารสนเทศทางการแพทย์', 'งานศูนย์ประกันสุขภาพ'],
            ['kkk_@hotmail.com', 'กลุ่มงานบริการด้านปฐมภูมิและองค์รวม', 'งานเวชปฏิบัติครอบครัว'],
            ['ad_@hotmali.com', 'กลุ่มงานพยาบาล', 'งานการพยาบาลผู้ป่วยใน'],
            ['po_@hotmail.com', 'กลุ่มงานพยาบาล', 'งานอุบัติเหตุฉุกเฉิน'],
            ['kittiyafc55@gmail.com', 'กลุ่มงานเภสัชกรรมและคุ้มครองผู้บริโภค', 'ฝ่ายเภสัชกรรมชุมชน'],
            ['poopheprimphan@gmail.com', 'กลุ่มงานบริหารทั่วไป', 'งานทำความสะอาด'],
            ['ph_ir@hotmail.com', 'กลุ่มงานบริหารทั่วไป', 'งานรักษาความปลอดภัย'],
            ['Chonthichafon123@gmail.com', 'กลุ่มงานทันตกรรม', 'ฝ่ายทันตสาธารณสุข'],
            ['chalisac245@gmail.com', 'กลุ่มงานทันตกรรม', 'ฝ่ายทันตสาธารณสุข'],
            ['tongyz1234@gmail.com', 'กลุ่มงานบริหารทั่วไป', 'งานซ่อมบำรุง'],
            ['si_@hotmail.com', 'กลุ่มงานบริหารทั่วไป', 'งานรักษาความปลอดภัย'],
            ['golfmike_seed@hotmail.com', 'กลุ่มงานเทคนิคการแพทย์', 'งานเทคนิคการแพทย์'],
            ['ngunlasomni@gmail.com', 'กลุ่มงานบริหารทั่วไป', 'งานภูมิทัศน์'],
            ['wanlayaneejantarangsee@gmail.com', 'กลุ่มงานพยาบาล', 'งานการพยาบาลผู้คลอดเเละทารกเเรกเกิด'],
            ['sup_@hotmail.com', 'กลุ่มงานพยาบาล', 'งานอุบัติเหตุฉุกเฉิน'],
            ['palmmy.4529@gmail.com', 'กลุ่มงานพยาบาล', 'งานอุบัติเหตุฉุกเฉิน'],
            ['ko_@hotmail.com', 'กลุ่มงานพยาบาล', 'งานอุบัติเหตุฉุกเฉิน'],
            ['ilada19.pan@gmail.com', 'กลุ่มงานประกันสุขภาพยุทธศาสตร์และสารสนเทศทางการแพทย์', 'งานศูนย์ประกันสุขภาพ'],
            ['niraporn.747@gmail.com', 'กลุ่มงานบริการด้านปฐมภูมิและองค์รวม', 'สุขาภิบาลและป้องกันโรค'],
            ['jittrayuy88888@gmail.com', 'กลุ่มงานพยาบาล', 'บริหารกลุ่มการพยาบาล'],
            ['itsaree76z@gmail.com', 'กลุ่มงานประกันสุขภาพยุทธศาสตร์และสารสนเทศทางการแพทย์', 'งานศูนย์ประกันสุขภาพ'],
            ['na_@hotmail.com', 'กลุ่มงานรังสีวิทยา', 'งานรังสี'],
            ['oa_@hotmail.com', 'กลุ่มงานบริหารทั่วไป', 'งานรักษาความปลอดภัย'],
            ['narumon_19_35@hotmail.com', 'กลุ่มงานบริหารทั่วไป', 'งานพัสดุ'],
            ['aewwiwan@gmail.com', 'กลุ่มงานบริหารทั่วไป', 'งานการเงิน'],
            ['tawann1502@gmail.com', 'กลุ่มงานพยาบาล', 'งานผู้ป่วยนอก'],
            ['t.audio@hotmail.com', 'กลุ่มงานบริหารทั่วไป', 'งานซ่อมบำรุง'],
            ['nadthapat_@hotmail.com', 'กลุ่มงานเภสัชกรรมและคุ้มครองผู้บริโภค', 'ฝ่ายเภสัชกรรมชุมชน'],
            ['armnatta2@gmail.com', 'กลุ่มงานบริการด้านปฐมภูมิและองค์รวม', 'งานสุขภาพจิตและยาเสพติด'],
            ['Penprasinghan@gmail.com', 'กลุ่มงานพยาบาล', 'งานอุบัติเหตุฉุกเฉิน'],
            ['nngkhnuchphrhmngxy@gmail.com', 'กลุ่มงานเวชศาสตร์ฟื้นฟู', 'ฝ่ายเวชกรรมฟื้นฟู'],
            ['so_@hotmail.com', 'กลุ่มงานบริหารทั่วไป', 'งานภูมิทัศน์'],
            ['Buakhai2521@gmail.com', 'กลุ่มงานการแพทย์แผนไทยและการแพทย์ทางเลือก', 'งานแพทย์แผนไทย'],
            ['kemmawat@hotmail.com', 'กลุ่มงานบริหารทั่วไป', 'งานยานพาหนะ'],
            ['pjpj40831@gmail.com', 'กลุ่มงานบริหารทั่วไป', 'งานซักฟอก'],
            ['soraya.muntee@gmail.com', 'กลุ่มงานบริหารทั่วไป', 'งานพัสดุ'],
            ['ji@hotmail.com', 'กลุ่มงานทันตกรรม', 'ฝ่ายทันตสาธารณสุข'],
            ['botaongoi@gmail.com', 'กลุ่มงานประกันสุขภาพยุทธศาสตร์และสารสนเทศทางการแพทย์', 'งานเวชระเบียน'],
            ['she_@hotmail.com', 'กลุ่มงานทันตกรรม', 'ฝ่ายทันตสาธารณสุข'],
            ['praneet_k@hotmail.com', 'กลุ่มงานบริหารทั่วไป', 'งานซ่อมบำรุง'],
            ['nu_@hotmail.com', 'กลุ่มงานโภชนศาสตร์', 'งานโภชนศาสตร์'],
            ['Tanayod2211@gmail.com', 'กลุ่มงานบริหารทั่วไป', 'งานยานพาหนะ'],
            ['Riaw_La@gmail.com', 'กลุ่มงานบริหารทั่วไป', 'งานยานพาหนะ'],
            ['ru_ng@hotmail.com', 'กลุ่มงานบริหารทั่วไป', 'งานยานพาหนะ'],
            ['wiirat1234@gmail.com', 'กลุ่มงานประกันสุขภาพยุทธศาสตร์และสารสนเทศทางการแพทย์', 'งานเวชระเบียน'],
            ['narubeth12@gmail.com', 'กลุ่มงานประกันสุขภาพยุทธศาสตร์และสารสนเทศทางการแพทย์', 'งานเวชระเบียน'],
            ['montira4248@gmail.com', 'กลุ่มงานประกันสุขภาพยุทธศาสตร์และสารสนเทศทางการแพทย์', 'งานศูนย์ประกันสุขภาพ'],
            ['arsa1970000@gmail.com', 'กลุ่มงานเทคนิคการแพทย์', 'งานเทคนิคการแพทย์'],
            ['chamaiporn_@hotmail.com', 'กลุ่มงานพยาบาล', 'งานอุบัติเหตุฉุกเฉิน'],
            ['ch_@hotmail.com', 'กลุ่มงานพยาบาล', 'งานการพยาบาลหน่วยควบคุมการติดเชื้อและงานจ่ายกลาง'],
            ['lawongkerdkan@gmail.com', 'กลุ่มงานพยาบาล', 'งานอุบัติเหตุฉุกเฉิน'],
            ['pata_@hotmail.com', 'กลุ่มงานพยาบาล', 'งานการพยาบาลหน่วยควบคุมการติดเชื้อและงานจ่ายกลาง'],
            ['su_wa@hotmail.com', 'กลุ่มงานพยาบาล', 'งานการพยาบาลหน่วยควบคุมการติดเชื้อและงานจ่ายกลาง'],
            ['sa1470500@gmail.com', 'กลุ่มงานบริหารทั่วไป', 'งานการเงิน'],
            ['sirikan.udorn@gmail.com', 'กลุ่มงานเภสัชกรรมและคุ้มครองผู้บริโภค', 'ฝ่ายเภสัชกรรมชุมชน'],
            ['june19jjune@gmail.com', 'กลุ่มงานบริหารทั่วไป', 'งานธุรการ'],
            ['sang_Jun@hotmail.com', 'กลุ่มงานพยาบาล', 'งานอุบัติเหตุฉุกเฉิน'],
            ['thipph_ha@hotmail.com', 'กลุ่มงานบริการด้านปฐมภูมิและองค์รวม', 'งานเวชปฏิบัติครอบครัว'],
            ['siyanun301@gmail.com', 'กลุ่มงานพยาบาล', 'งานการพยาบาลผู้ป่วยใน'],
            ['taksapornchantima@gmail.com', 'กลุ่มงานบริการด้านปฐมภูมิและองค์รวม', 'งานเวชปฏิบัติครอบครัว'],
            ['kraaew@gmail.com', 'กลุ่มงานการแพทย์แผนไทยและการแพทย์ทางเลือก', 'งานแพทย์แผนไทย'],
            ['jureeratjuryry@gmail.com', 'กลุ่มงานพยาบาล', 'งานอุบัติเหตุฉุกเฉิน'],
            ['pimwara29@gmail.com', 'กลุ่มงานบริหารทั่วไป', 'งานบริหารงานทั่วไป'],
            ['pirin.jija@gmail.com', 'กลุ่มงานเทคนิคการแพทย์', 'งานเทคนิคการแพทย์'],
            ['yonrada21@hotmail.com', 'กลุ่มงานเวชศาสตร์ฟื้นฟู', 'ฝ่ายเวชกรรมฟื้นฟู'],
            ['fonfun1928@gmail.com', 'กลุ่มงานทันตกรรม', 'ฝ่ายทันตสาธารณสุข'],
            ['wachiraphorn.phl@gmail.com', 'กลุ่มงานบริการด้านปฐมภูมิและองค์รวม', 'งานเวชปฏิบัติครอบครัว'],
            ['phonpk1999@gmail.com', 'กลุ่มงานพยาบาล', 'งานอุบัติเหตุฉุกเฉิน'],
            ['runchiya@hotmail.com', 'กลุ่มงานพยาบาล', 'งานการพยาบาลผู้คลอดเเละทารกเเรกเกิด'],
            ['jan@hotmail.com', 'กลุ่มงานพยาบาล', 'งานการพยาบาลผู้คลอดเเละทารกเเรกเกิด'],
            ['kwang_der@hotmail.com', 'กลุ่มงานพยาบาล', 'งานการพยาบาลผู้คลอดเเละทารกเเรกเกิด'],
            ['pongrakth15@gmail.com', 'กลุ่มงานพยาบาล', 'งานการพยาบาลผู้คลอดเเละทารกเเรกเกิด'],
            ['hongtong.chan@gmail.com', 'กลุ่มงานบริการด้านปฐมภูมิและองค์รวม', 'งานสุขภาพจิตและยาเสพติด'],
            ['tairajpo@hotmail.com', 'กลุ่มงานบริการด้านปฐมภูมิและองค์รวม', 'งานเวชปฏิบัติครอบครัว'],
            ['suwimon19951995@gmail.com', 'กลุ่มงานพยาบาล', 'งานอุบัติเหตุฉุกเฉิน'],
            ['Jutharmas@gmail.com', 'กลุ่มงานพยาบาล', 'งานผู้ป่วยนอก'],
            ['th_@hotmail.com', 'กลุ่มงานพยาบาล', 'งานอุบัติเหตุฉุกเฉิน'],
            ['Atchii.puy@gmail.com', 'กลุ่มงานพยาบาล', 'งานอุบัติเหตุฉุกเฉิน'],
            ['yyy_ao@hotmail.com', 'กลุ่มงานพยาบาล', 'งานอุบัติเหตุฉุกเฉิน'],
            ['throngkeawmaneekan@gmail.com', 'กลุ่มงานพยาบาล', 'งานผู้ป่วยนอก'],
            ['sumalinee@hotmail.com', 'กลุ่มงานพยาบาล', 'บริหารกลุ่มการพยาบาล'],
            ['smallnurse111@gmail.com', 'กลุ่มงานพยาบาล', 'งานผู้ป่วยนอก'],
            ['n@hotmail.com', 'กลุ่มงานเภสัชกรรมและคุ้มครองผู้บริโภค', 'ฝ่ายเภสัชกรรมชุมชน'],
            ['Preeyanutkham2532@gmail.com', 'กลุ่มงานเภสัชกรรมและคุ้มครองผู้บริโภค', 'ฝ่ายเภสัชกรรมชุมชน'],
            ['pawa_@hotmail.com', 'กลุ่มงานเภสัชกรรมและคุ้มครองผู้บริโภค', 'ฝ่ายเภสัชกรรมชุมชน'],
            ['dtida.rx@gmail.com', 'กลุ่มงานเภสัชกรรมและคุ้มครองผู้บริโภค', 'ฝ่ายเภสัชกรรมชุมชน'],
            ['spore.feelgood@gmail.com', 'กลุ่มงานเภสัชกรรมและคุ้มครองผู้บริโภค', 'ฝ่ายเภสัชกรรมชุมชน'],
            ['withit.n@ku.th', 'กลุ่มงานเภสัชกรรมและคุ้มครองผู้บริโภค', 'ฝ่ายเภสัชกรรมชุมชน'],
            ['siraboocha@gmail.com', 'กลุ่มงานเภสัชกรรมและคุ้มครองผู้บริโภค', 'ฝ่ายเภสัชกรรมชุมชน'],
            ['samart.bu@kkumail.com', 'กลุ่มงานเทคนิคการแพทย์', 'งานเทคนิคการแพทย์'],
            ['khaekhai08@gmail.com', 'กลุ่มงานเทคนิคการแพทย์', 'งานเทคนิคการแพทย์'],
            ['aaa_aaa@hotmail.com', 'กลุ่มงานรังสีวิทยา', 'งานรังสี'],
            ['num.nanbo@gmail.com', 'กลุ่มงานการแพทย์แผนไทยและการแพทย์ทางเลือก', 'งานแพทย์แผนไทย'],
            ['katsukipai16@gmail.com', 'กลุ่มงานเวชศาสตร์ฟื้นฟู', 'ฝ่ายเวชกรรมฟื้นฟู'],
            ['khuanchewa.pp@gmail.com', 'กลุ่มงานบริการด้านปฐมภูมิและองค์รวม', 'งานเวชปฏิบัติครอบครัว'],
            ['keattisk23@gmail.com', 'กลุ่มงานบริการด้านปฐมภูมิและองค์รวม', 'สุขาภิบาลและป้องกันโรค'],
            ['suangsuda.boocha@gmail.com', 'กลุ่มงานประกันสุขภาพยุทธศาสตร์และสารสนเทศทางการแพทย์', 'งานศูนย์ประกันสุขภาพ'],
            ['no@hotmail.com', 'กลุ่มงานบริหารทั่วไป', 'งานการเงิน'],
            ['win_aon@hotmail.com', 'กลุ่มงานบริหารทั่วไป', 'งานบริหารงานทั่วไป'],
            ['kittikawin0023@gmail.com', 'กลุ่มงานทันตกรรม', 'ฝ่ายทันตสาธารณสุข'],
            ['Suda.tipnaruk@gmail.com', 'กลุ่มงานทันตกรรม', 'ฝ่ายทันตสาธารณสุข'],
            ['panassaya@hotmail.com', 'กลุ่มงานทันตกรรม', 'ฝ่ายทันตสาธารณสุข'],
            ['้high_fire@windowslive.com', 'กลุ่มงานทันตกรรม', 'ฝ่ายทันตสาธารณสุข'],
            ['su_@hotmail.com', 'กลุ่มงานพยาบาล', 'งานการพยาบาลผู้ป่วยใน'],
            ['pjpj73370@gmail.com', 'กลุ่มงานบริหารทั่วไป', 'งานทำความสะอาด'],
            ['gggg_@hotmail.com', 'กลุ่มงานโภชนศาสตร์', 'งานโภชนศาสตร์'],
            ['chairat102523@gmail.com', 'กลุ่มงานพยาบาล', 'งานผู้ป่วยนอก'],
            ['Panapong861kp@gmail.com', 'กลุ่มงานพยาบาล', 'งานผู้ป่วยนอก'],
            ['tiraphon.4@gmail.com', 'กลุ่มงานพยาบาล', 'งานผู้ป่วยนอก'],
            ['diraklitthintongkhob@gmail.com', 'กลุ่มงานพยาบาล', 'งานผู้ป่วยนอก'],
            ['suris_@hotmail.com', 'กลุ่มงานพยาบาล', 'งานผู้ป่วยนอก'],
            ['kulthidaphichaykha2@gmail.com', 'กลุ่มงานพยาบาล', 'งานการพยาบาลผู้คลอดเเละทารกเเรกเกิด'],
            ['pa_@hotmail.com', 'กลุ่มงานพยาบาล', 'งานการพยาบาลผู้ป่วยใน'],
            ['tutsanee1993@gmail.com', 'กลุ่มงานพยาบาล', 'งานการพยาบาลผู้ป่วยใน'],
            ['waraphon@hotmail.com', 'กลุ่มงานพยาบาล', 'งานการพยาบาลผู้ป่วยใน'],
            ['maneegorn53@gmail.com', 'กลุ่มงานพยาบาล', 'งานการพยาบาลผู้ป่วยใน'],
            ['Panadda2647@gmail.com', 'กลุ่มงานพยาบาล', 'งานผู้ป่วยนอก'],
            ['mukda4696@gmail.com', 'กลุ่มงานพยาบาล', 'งานการพยาบาลผู้ป่วยใน'],
            ['cha_@hotmail.com', 'กลุ่มงานพยาบาล', 'งานการพยาบาลผู้ป่วยใน'],
            ['maneerat@hotmail.com', 'กลุ่มงานพยาบาล', 'งานการพยาบาลผู้ป่วยใน'],
            ['Ebeeve07052538@gmail.com', 'กลุ่มงานพยาบาล', 'งานการพยาบาลผู้ป่วยใน'],
            ['dewpawinee77@gmail.com', 'กลุ่มงานพยาบาล', 'งานการพยาบาลผู้ป่วยใน'],
            ['sanhawat@hotmail.com', 'กลุ่มงานการแพทย์', 'การแพทย์'],
            ['rabot.tha@gmail.com', 'กลุ่มงานการแพทย์', 'การแพทย์'],
            ['thi@hotmail.com', 'กลุ่มงานการแพทย์', 'การแพทย์'],
            ['rinradabam.p11@gmail.com', 'กลุ่มงานการแพทย์', 'การแพทย์'],
            ['dekdorn@gmail.com', 'กลุ่มงานประกันสุขภาพยุทธศาสตร์และสารสนเทศทางการแพทย์', 'สารสนเทศทางการแพทย์'],
            ['wichetpong159@hotmail.com', 'กลุ่มงานประกันสุขภาพยุทธศาสตร์และสารสนเทศทางการแพทย์', 'สารสนเทศทางการแพทย์'],


        ];

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        KpUserWastePreference::truncate();
        KpBankAccount::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        foreach ($arr as $a) {

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
        return redirect()->route('keptkayas.staffs.index'); 
    } 

    public function create() 
    { 
        $org = Organization::find(Auth::user()->org_id_fk); 
        $zones = Zone::all(); 
        $defaultAddress = [ 
            'province' => $org->provinces->province_name ?? '-', 
            'district' => $org->districts->district_name ?? '-', 
            'tambon' => $org->tambons->tambon_name ?? '-' 
        ]; 
        return view('admin.users.create', compact('zones', 'defaultAddress')); 
    }


    /**
     * บันทึกข้อมูลผู้ใช้ใหม่และเปิดสิทธิ์บริการ (Store)
     */
    public function store(Request $request) 
    { 
        $messages = [ 
            'required' => 'กรุณากรอกข้อมูลในช่อง :attribute', 
            'unique'   => ':attribute นี้มีอยู่ในระบบแล้ว', 
            'min'      => ':attribute ต้องมีความยาวอย่างน้อย :min ตัวอักษร', 
        ]; 

        $attributes = [ 
            'username'   => 'ชื่อผู้ใช้งาน', 
            'password'   => 'รหัสผ่าน', 
            'firstname'  => 'ชื่อจริง', 
            'lastname'   => 'นามสกุล', 
            'phone'      => 'เบอร์โทรศัพท์', 
            'id_card'    => 'เลขบัตรประชาชน', 
            'zone_id'    => 'โซน', 
            'subzone_id' => 'ซอย/ชุมชนย่อย', 
        ]; 

        $request->validate([ 
            'username'   => 'required|string|max:255|unique:users,username', 
            'password'   => 'required|string|min:6', 
            'firstname'  => 'required|string|max:255', 
            'lastname'   => 'required|string|max:255', 
            'phone'      => 'nullable|string|max:20', 
            'id_card'    => 'nullable|string|max:13|unique:users,id_card', 
            'zone_id'    => 'required', 
            'subzone_id' => 'required', 
        ], $messages,$attributes); 

        DB::beginTransaction(); 
        try { 
            $org = Organization::find(Auth::user()->org_id_fk); 

            // 1. สร้าง Master User Record
            $user = User::create([ 
                'username'      => $request->username, 
                'password'      => Hash::make($request->password), 
                'prefix'        => $request->prefix ?? 'นาย', 
                'firstname'     => $request->firstname, 
                'lastname'      => $request->lastname, 
                'phone'         => $request->phone, 
                'id_card'       => $request->id_card, 
                'address'       => $request->address, 
                'zone_id'       => $request->zone_id, 
                'subzone_id'    => $request->subzone_id, 
                'tambon_code'   => $org->org_tambon_id_fk ?? null, 
                'district_code' => $org->org_district_id_fk ?? null, 
                'province_code' => $org->org_province_id_fk ?? null, 
                'org_id_fk'     => $org->id ?? Auth::user()->org_id_fk, 
                'status'        => 'active', 
            ]); 

            $user->assignRole('User'); 

            // 2. แมปค่าบริการจาก Blade Switch (`svc_*`) ไปยัง UserService
            $serviceData = [
                'recycle'      => $request->has('svc_recycle') ? '1' : '0',
                'food_waste'   => $request->has('svc_food_waste') ? '1' : '0',                 'annual_trash' =>$request->has('svc_annual_trash') ? '1' : '0',
            ];

            // 3. เรียกใช้งาน UserService เพื่อเปิดบัญชีบริการและอัปเดตสิทธิ์
            $this->userService->syncUserServices($user,$serviceData);

            // 4. บันทึก/ซิงค์ KpUserWastePreference
            KpUserWastePreference::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'org_id_fk'             => $org->id ?? Auth::user()->org_id_fk,
                    'zone_id'               => $user->zone_id,
                    'subzone_id'            => $user->subzone_id,
                    'is_annual_collection'  => $request->has('svc_annual_trash') ? 1 : 0,                     'is_recycle_bank'       =>$request->has('svc_recycle') ? 1 : 0,
                ]
            );

            DB::commit(); 
            return redirect()->route('admin.users.index')->with('success', 'เพิ่มผู้ใช้งานและเปิดสิทธิ์บริการเรียบร้อยแล้ว'); 

        } catch (\Exception $e) { 
            DB::rollBack(); 
            return back()->withInput()->with('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage()); 
        } 
    } 
    

    /**
     * อัปเดตข้อมูลผู้ใช้งานและสิทธิ์บริการ (Update)
     */
    public function update(Request $request,$id) 
    { 
        $messages = [ 
            'required' => 'กรุณากรอกข้อมูลในช่อง :attribute', 
            'unique'   => ':attribute นี้ถูกใช้งานแล้ว', 
        ]; 

        $request->validate([ 
            'username'   => 'required|string|max:255|unique:users,username,' . $id, 
            'firstname'  => 'required|string|max:255', 
            'lastname'   => 'required|string|max:255', 
            'phone'      => 'nullable|string|max:20|unique:users,phone,' . $id, 
            'id_card'    => 'nullable|string|max:13|unique:users,id_card,' . $id, 
            'password'   => 'nullable|min:6', 
            'zone_id'    => 'required', 
            'subzone_id' => 'required', 
        ], $messages); 

        DB::beginTransaction(); 
        try { 
            $user = User::findOrFail($id); 
            
            // 1. อัปเดตข้อมูลบุคคล
            $user->username   =$request->username; 
            $user->prefix     =$request->prefix; 
            $user->firstname  =$request->firstname; 
            $user->lastname   =$request->lastname; 
            $user->phone      =$request->phone; 
            $user->id_card    =$request->id_card; 
            $user->address    =$request->address; 
            $user->zone_id    =$request->zone_id; 
            $user->subzone_id =$request->subzone_id; 

            if ($request->filled('password')) { 
                $user->password = Hash::make($request->password); 
            } 
            $user->save(); 

            // 2. แมปค่าสวิตช์บริการจาก Blade (`svc_*`) ส่งเข้า UserService
            $serviceData = [
                'recycle'      => $request->has('svc_recycle') ? '1' : '0',
                'food_waste'   => $request->has('svc_food_waste') ? '1' : '0',                 'annual_trash' =>$request->has('svc_annual_trash') ? '1' : '0',
            ];

            // 3. ซิงค์สิทธิ์เปิด/ปิดบัญชีบริการ
            $this->userService->syncUserServices($user,$serviceData);

            // 4. อัปเดต KpUserWastePreference
            KpUserWastePreference::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'zone_id'              => $user->zone_id,
                    'subzone_id'           => $user->subzone_id,
                    'is_annual_collection' => $request->has('svc_annual_trash') ? 1 : 0,                     'is_recycle_bank'      =>$request->has('svc_recycle') ? 1 : 0,
                ]
            );

            DB::commit(); 
            return redirect()->route('admin.users.index')->with('success', 'อัปเดตข้อมูลและสิทธิ์บริการเรียบร้อยแล้ว'); 

        } catch (\Exception $e) { 
            DB::rollBack(); 
            return back()->withInput()->with('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage()); 
        } 
    }

    public function edit($user_id, $addmeter = "") 
{ 
    $user = User::where('id', $user_id) 
        ->with([ 
            'user_zone', 
            'user_subzone', 
            'recycleBankAccount', 
            'foodWasteBankAccount', 
            'annualTrashSubscription',
            // ดึงความสัมพันธ์มิเตอร์น้ำประปาจาก User Model
            'usermeterinfos.undertake_zone',
            'usermeterinfos.undertake_subzone',
            'usermeterinfos.meter_type'
        ]) 
        ->firstOrFail(); 

    $org = Organization::find(Auth::user()->org_id_fk); 
    $defaultAddress = [ 
        'province' => $org->provinces->province_name ?? '-', 
        'amphure'  => $org->districts->district_name ?? '-', 
        'district' => $org->tambons->tambon_name ?? '-' 
    ]; 
    
    $zones = Zone::where('org_id_fk', Auth::user()->org_id_fk)->get(); 
    $subzones   = Subzone::where('zone_id', $user->zone_id)->get(); 
    $meterTypes = \App\Models\Tabwater\TwMeterType::all();
    
    $nextMeterCount   = collect($user->usermeterinfos)->count() + 1;
    $generatedMeterId = 'MTR-' . substr('000', strlen($org->id)).$org->id . '-' . substr('0000',strlen($user->id))$user->id.'-'.w$nextMeterCount;

    return view('admin.users.edit', compact( 
        'user', 'zones', 'subzones', 'defaultAddress', 'addmeter', 'meterTypes', 'generatedMeterId'
    )); 
}

 public function edit_invoices($meter_id)
    {
        $inv_period = InvoicePeriod::where('status', 'active')->get('id')->first();
        $usermeter_infos = TwMeterInfos::with([
            'invoice' => function ($q) use ($inv_period) {
                $budget_year = BudgetYear::with('invoicePeriod:id,budgetyear_id')->where('status', 'active')
                    ->get()->first();
                $inv_period_lists = collect($budget_year->invoicePeriod)->pluck('id');
                return $q->select('*')->whereIn('inv_period_id_fk', $inv_period_lists);
            }
        ])->where('meter_id', $meter_id)->get()->first();
        return view('tabwater.usermeter_infos.index', compact('usermeter_infos'));
    }

/**
 * ผูกมิเตอร์น้ำประปาใหม่เข้ากับผู้ใช้งาน (Master User)
 */
public function attachMeter(Request $request, $id)
{
    $request->validate([
        'meternumber'          => 'required|string|max:255',
        'meter_address'        => 'required|string',
        'undertake_zone_id'    => 'required',
        'undertake_subzone_id' => 'required',
        'metertype_id'         => 'required',
    ], [
        'required' => 'กรุณากรอกข้อมูลในช่อง :attribute ให้ครบถ้วน'
    ]);

    try {
        $user = User::findOrFail($id);
        $orgId = Auth::user()->org_id_fk ?? $user->org_id_fk;

        TwMeterInfos::create([
            'meter_id'             => $$user->id,
            'user_id'              => $user->id,
            'org_id_fk'            => $orgId,
            'meternumber'          => $request->meternumber,
            'factory_no'           => $request->factory_no,
            'submeter_name'        => $request->submeter_name,
            'meter_address'        => $request->meter_address,
            'undertake_zone_id'   => $request->undertake_zone_id,
            'undertake_subzone_id' => $request->undertake_subzone_id,
            'metertype_id'         => $request->metertype_id,
            'status'               => 'active',
            'acceptance_date'      => date('Y-m-d'),
            'recorder_id'          => Auth::id(),
            'last_meter_recording' => 0,
        ]);

        return back()->with('success', 'ผูกมิเตอร์น้ำประปาใหม่เรียบร้อยแล้ว');
    } catch (\Exception $e) {
        return back()->with('error', 'เกิดข้อผิดพลาดในการผูกมิเตอร์: ' . $e->getMessage());
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
        // ยังไม่มีหน้ารายละเอียดแยก ใช้หน้าแก้ไขซึ่งแสดงข้อมูลครบแล้ว
        return redirect()->route('admin.users.edit', $user->id ?? $function);
    }

    public function history(User $user)
    {
        $user = User::with('usermeterinfos', 'usermeterinfos.invoice')->where('id', $user->id)->get();
        return view('admin.users_tabwater.history', compact('user'));
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
        return view('admin.users_tabwater.cancel', compact('user'));
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

        try { 
            $this->userService->updateServices($services);
            return back()->with('success', 'บันทึกการตั้งค่าสิทธิ์ผู้ใช้งานเรียบร้อยแล้ว'); 
        } catch (\Exception $e) { 
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
