<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FoodwasteRewardSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('foodwaste_reward_settings')->insert([
            [
                'key' => 'daily_base_pts',
                'title' => 'แต้มพื้นฐานรายวัน',
                'value' => 10,
                'unit' => 'pts',
                'description' => 'ได้เมื่อบันทึกการเทครั้งแรกของวัน'
            ],
            [
                'key' => 'evening_bonus_pts',
                'title' => 'โบนัสรวบรวมเทตอนเย็น',
                'value' => 20,
                'unit' => 'pts',
                'description' => 'ได้รับเพิ่มหากมาเทครั้งแรกในช่วงเย็น (17:00-21:00)'
            ],
            [
                'key' => 'pts_per_dry_kg',
                'title' => 'แต้มต่อน้ำหนักแห้ง 1 กก.',
                'value' => 100,
                'unit' => 'pts',
                'description' => 'แต้มคุณภาพจากเนื้ออินทรีย์วัตถุจริง'
            ],
            [
                'key' => 'money_per_dry_kg',
                'title' => 'เงินคืนต่อน้ำหนักแห้ง 1 กก.',
                'value' => 5.00,
                'unit' => 'thb',
                'description' => 'เงินรางวัลสนับสนุนการแยกขยะ'
            ],
        ]);
    }
}
