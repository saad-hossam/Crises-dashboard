<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UnitSeeder extends Seeder
{
    public function run(): void
    {
        $units = [
            1  => 'الافريقية الطبية',
            2  => 'الشركة العربية الأمريكية للسيارات',
            3  => 'صقر',
            4  => 'الالكترونيات',
            5  => 'الطاقة',
            6  => 'اللوحات',
            7  => 'قادر',
            8  => 'أتيكو',
            9  => 'المحركات',
            10 => 'الطائرات',
            11 => 'سيماف',
            12 => 'حلوان',
            13 => 'العقارية',
            14 => 'ABD',
            15 => 'الجهاز',
            16 => 'بحوث الطيران',
            17=>'المصاعد'
        ];

        foreach ($units as $id => $name) {
            DB::table('units')->updateOrInsert(
                ['id' => $id],
                [
                    'name' => $name,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }
    }
}
