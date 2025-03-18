<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class IseedPermissionsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('permissions')->delete();
        
        \DB::table('permissions')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'view_card',
                'guard_name' => 'web',
                'created_at' => '2025-03-13 01:16:37',
                'updated_at' => '2025-03-13 01:16:37',
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'view_any_card',
                'guard_name' => 'web',
                'created_at' => '2025-03-13 01:16:37',
                'updated_at' => '2025-03-13 01:16:37',
            ),
            2 => 
            array (
                'id' => 3,
                'name' => 'create_card',
                'guard_name' => 'web',
                'created_at' => '2025-03-13 01:16:37',
                'updated_at' => '2025-03-13 01:16:37',
            ),
            3 => 
            array (
                'id' => 4,
                'name' => 'update_card',
                'guard_name' => 'web',
                'created_at' => '2025-03-13 01:16:37',
                'updated_at' => '2025-03-13 01:16:37',
            ),
            4 => 
            array (
                'id' => 5,
                'name' => 'delete_card',
                'guard_name' => 'web',
                'created_at' => '2025-03-13 01:16:37',
                'updated_at' => '2025-03-13 01:16:37',
            ),
            5 => 
            array (
                'id' => 6,
                'name' => 'delete_any_card',
                'guard_name' => 'web',
                'created_at' => '2025-03-13 01:16:37',
                'updated_at' => '2025-03-13 01:16:37',
            ),
            6 => 
            array (
                'id' => 7,
                'name' => 'view_chapter',
                'guard_name' => 'web',
                'created_at' => '2025-03-13 01:16:37',
                'updated_at' => '2025-03-13 01:16:37',
            ),
            7 => 
            array (
                'id' => 8,
                'name' => 'view_any_chapter',
                'guard_name' => 'web',
                'created_at' => '2025-03-13 01:16:37',
                'updated_at' => '2025-03-13 01:16:37',
            ),
            8 => 
            array (
                'id' => 9,
                'name' => 'create_chapter',
                'guard_name' => 'web',
                'created_at' => '2025-03-13 01:16:37',
                'updated_at' => '2025-03-13 01:16:37',
            ),
            9 => 
            array (
                'id' => 10,
                'name' => 'update_chapter',
                'guard_name' => 'web',
                'created_at' => '2025-03-13 01:16:37',
                'updated_at' => '2025-03-13 01:16:37',
            ),
            10 => 
            array (
                'id' => 11,
                'name' => 'delete_chapter',
                'guard_name' => 'web',
                'created_at' => '2025-03-13 01:16:37',
                'updated_at' => '2025-03-13 01:16:37',
            ),
            11 => 
            array (
                'id' => 12,
                'name' => 'delete_any_chapter',
                'guard_name' => 'web',
                'created_at' => '2025-03-13 01:16:37',
                'updated_at' => '2025-03-13 01:16:37',
            ),
            12 => 
            array (
                'id' => 13,
                'name' => 'view_division',
                'guard_name' => 'web',
                'created_at' => '2025-03-13 01:16:37',
                'updated_at' => '2025-03-13 01:16:37',
            ),
            13 => 
            array (
                'id' => 14,
                'name' => 'view_any_division',
                'guard_name' => 'web',
                'created_at' => '2025-03-13 01:16:37',
                'updated_at' => '2025-03-13 01:16:37',
            ),
            14 => 
            array (
                'id' => 15,
                'name' => 'create_division',
                'guard_name' => 'web',
                'created_at' => '2025-03-13 01:16:37',
                'updated_at' => '2025-03-13 01:16:37',
            ),
            15 => 
            array (
                'id' => 16,
                'name' => 'update_division',
                'guard_name' => 'web',
                'created_at' => '2025-03-13 01:16:37',
                'updated_at' => '2025-03-13 01:16:37',
            ),
            16 => 
            array (
                'id' => 17,
                'name' => 'delete_division',
                'guard_name' => 'web',
                'created_at' => '2025-03-13 01:16:37',
                'updated_at' => '2025-03-13 01:16:37',
            ),
            17 => 
            array (
                'id' => 18,
                'name' => 'delete_any_division',
                'guard_name' => 'web',
                'created_at' => '2025-03-13 01:16:37',
                'updated_at' => '2025-03-13 01:16:37',
            ),
            18 => 
            array (
                'id' => 19,
                'name' => 'view_material',
                'guard_name' => 'web',
                'created_at' => '2025-03-13 01:16:37',
                'updated_at' => '2025-03-13 01:16:37',
            ),
            19 => 
            array (
                'id' => 20,
                'name' => 'view_any_material',
                'guard_name' => 'web',
                'created_at' => '2025-03-13 01:16:37',
                'updated_at' => '2025-03-13 01:16:37',
            ),
            20 => 
            array (
                'id' => 21,
                'name' => 'create_material',
                'guard_name' => 'web',
                'created_at' => '2025-03-13 01:16:37',
                'updated_at' => '2025-03-13 01:16:37',
            ),
            21 => 
            array (
                'id' => 22,
                'name' => 'update_material',
                'guard_name' => 'web',
                'created_at' => '2025-03-13 01:16:37',
                'updated_at' => '2025-03-13 01:16:37',
            ),
            22 => 
            array (
                'id' => 23,
                'name' => 'delete_material',
                'guard_name' => 'web',
                'created_at' => '2025-03-13 01:16:37',
                'updated_at' => '2025-03-13 01:16:37',
            ),
            23 => 
            array (
                'id' => 24,
                'name' => 'delete_any_material',
                'guard_name' => 'web',
                'created_at' => '2025-03-13 01:16:37',
                'updated_at' => '2025-03-13 01:16:37',
            ),
            24 => 
            array (
                'id' => 25,
                'name' => 'view_role',
                'guard_name' => 'web',
                'created_at' => '2025-03-13 01:16:37',
                'updated_at' => '2025-03-13 01:16:37',
            ),
            25 => 
            array (
                'id' => 26,
                'name' => 'view_any_role',
                'guard_name' => 'web',
                'created_at' => '2025-03-13 01:16:37',
                'updated_at' => '2025-03-13 01:16:37',
            ),
            26 => 
            array (
                'id' => 27,
                'name' => 'create_role',
                'guard_name' => 'web',
                'created_at' => '2025-03-13 01:16:37',
                'updated_at' => '2025-03-13 01:16:37',
            ),
            27 => 
            array (
                'id' => 28,
                'name' => 'update_role',
                'guard_name' => 'web',
                'created_at' => '2025-03-13 01:16:37',
                'updated_at' => '2025-03-13 01:16:37',
            ),
            28 => 
            array (
                'id' => 29,
                'name' => 'delete_role',
                'guard_name' => 'web',
                'created_at' => '2025-03-13 01:16:37',
                'updated_at' => '2025-03-13 01:16:37',
            ),
            29 => 
            array (
                'id' => 30,
                'name' => 'delete_any_role',
                'guard_name' => 'web',
                'created_at' => '2025-03-13 01:16:37',
                'updated_at' => '2025-03-13 01:16:37',
            ),
            30 => 
            array (
                'id' => 31,
                'name' => 'view_unit',
                'guard_name' => 'web',
                'created_at' => '2025-03-13 01:16:37',
                'updated_at' => '2025-03-13 01:16:37',
            ),
            31 => 
            array (
                'id' => 32,
                'name' => 'view_any_unit',
                'guard_name' => 'web',
                'created_at' => '2025-03-13 01:16:37',
                'updated_at' => '2025-03-13 01:16:37',
            ),
            32 => 
            array (
                'id' => 33,
                'name' => 'create_unit',
                'guard_name' => 'web',
                'created_at' => '2025-03-13 01:16:37',
                'updated_at' => '2025-03-13 01:16:37',
            ),
            33 => 
            array (
                'id' => 34,
                'name' => 'update_unit',
                'guard_name' => 'web',
                'created_at' => '2025-03-13 01:16:37',
                'updated_at' => '2025-03-13 01:16:37',
            ),
            34 => 
            array (
                'id' => 35,
                'name' => 'delete_unit',
                'guard_name' => 'web',
                'created_at' => '2025-03-13 01:16:37',
                'updated_at' => '2025-03-13 01:16:37',
            ),
            35 => 
            array (
                'id' => 36,
                'name' => 'delete_any_unit',
                'guard_name' => 'web',
                'created_at' => '2025-03-13 01:16:37',
                'updated_at' => '2025-03-13 01:16:37',
            ),
            36 => 
            array (
                'id' => 37,
                'name' => 'view_user',
                'guard_name' => 'web',
                'created_at' => '2025-03-13 01:16:37',
                'updated_at' => '2025-03-13 01:16:37',
            ),
            37 => 
            array (
                'id' => 38,
                'name' => 'view_any_user',
                'guard_name' => 'web',
                'created_at' => '2025-03-13 01:16:37',
                'updated_at' => '2025-03-13 01:16:37',
            ),
            38 => 
            array (
                'id' => 39,
                'name' => 'create_user',
                'guard_name' => 'web',
                'created_at' => '2025-03-13 01:16:37',
                'updated_at' => '2025-03-13 01:16:37',
            ),
            39 => 
            array (
                'id' => 40,
                'name' => 'update_user',
                'guard_name' => 'web',
                'created_at' => '2025-03-13 01:16:37',
                'updated_at' => '2025-03-13 01:16:37',
            ),
            40 => 
            array (
                'id' => 41,
                'name' => 'delete_user',
                'guard_name' => 'web',
                'created_at' => '2025-03-13 01:16:37',
                'updated_at' => '2025-03-13 01:16:37',
            ),
            41 => 
            array (
                'id' => 42,
                'name' => 'delete_any_user',
                'guard_name' => 'web',
                'created_at' => '2025-03-13 01:16:37',
                'updated_at' => '2025-03-13 01:16:37',
            ),
            42 => 
            array (
                'id' => 43,
                'name' => 'page_EditProfilePage',
                'guard_name' => 'web',
                'created_at' => '2025-03-13 01:16:37',
                'updated_at' => '2025-03-13 01:16:37',
            ),
            43 => 
            array (
                'id' => 44,
                'name' => 'create_many_card',
                'guard_name' => 'web',
                'created_at' => '2025-03-13 01:17:04',
                'updated_at' => '2025-03-13 01:17:04',
            ),
            44 => 
            array (
                'id' => 45,
                'name' => 'view_discount',
                'guard_name' => 'web',
                'created_at' => '2025-03-13 01:17:04',
                'updated_at' => '2025-03-13 01:17:04',
            ),
            45 => 
            array (
                'id' => 46,
                'name' => 'view_any_discount',
                'guard_name' => 'web',
                'created_at' => '2025-03-13 01:17:04',
                'updated_at' => '2025-03-13 01:17:04',
            ),
            46 => 
            array (
                'id' => 47,
                'name' => 'create_discount',
                'guard_name' => 'web',
                'created_at' => '2025-03-13 01:17:04',
                'updated_at' => '2025-03-13 01:17:04',
            ),
            47 => 
            array (
                'id' => 48,
                'name' => 'update_discount',
                'guard_name' => 'web',
                'created_at' => '2025-03-13 01:17:04',
                'updated_at' => '2025-03-13 01:17:04',
            ),
            48 => 
            array (
                'id' => 49,
                'name' => 'delete_discount',
                'guard_name' => 'web',
                'created_at' => '2025-03-13 01:17:04',
                'updated_at' => '2025-03-13 01:17:04',
            ),
            49 => 
            array (
                'id' => 50,
                'name' => 'delete_any_discount',
                'guard_name' => 'web',
                'created_at' => '2025-03-13 01:17:04',
                'updated_at' => '2025-03-13 01:17:04',
            ),
            50 => 
            array (
                'id' => 51,
                'name' => 'view_subscription',
                'guard_name' => 'web',
                'created_at' => '2025-03-13 01:17:04',
                'updated_at' => '2025-03-13 01:17:04',
            ),
            51 => 
            array (
                'id' => 52,
                'name' => 'view_any_subscription',
                'guard_name' => 'web',
                'created_at' => '2025-03-13 01:17:04',
                'updated_at' => '2025-03-13 01:17:04',
            ),
            52 => 
            array (
                'id' => 53,
                'name' => 'create_subscription',
                'guard_name' => 'web',
                'created_at' => '2025-03-13 01:17:04',
                'updated_at' => '2025-03-13 01:17:04',
            ),
            53 => 
            array (
                'id' => 54,
                'name' => 'update_subscription',
                'guard_name' => 'web',
                'created_at' => '2025-03-13 01:17:04',
                'updated_at' => '2025-03-13 01:17:04',
            ),
            54 => 
            array (
                'id' => 55,
                'name' => 'delete_subscription',
                'guard_name' => 'web',
                'created_at' => '2025-03-13 01:17:04',
                'updated_at' => '2025-03-13 01:17:04',
            ),
            55 => 
            array (
                'id' => 56,
                'name' => 'delete_any_subscription',
                'guard_name' => 'web',
                'created_at' => '2025-03-13 01:17:04',
                'updated_at' => '2025-03-13 01:17:04',
            ),
            56 => 
            array (
                'id' => 57,
                'name' => 'create_subsciption_cards_subscription',
                'guard_name' => 'web',
                'created_at' => '2025-03-13 01:17:04',
                'updated_at' => '2025-03-13 01:17:04',
            ),
            57 => 
            array (
                'id' => 58,
                'name' => 'copy_card_code_subscription',
                'guard_name' => 'web',
                'created_at' => '2025-03-13 01:17:04',
                'updated_at' => '2025-03-13 01:17:04',
            ),
            58 => 
            array (
                'id' => 59,
                'name' => 'attach_user_subscription',
                'guard_name' => 'web',
                'created_at' => '2025-03-13 01:17:04',
                'updated_at' => '2025-03-13 01:17:04',
            ),
            59 => 
            array (
                'id' => 60,
                'name' => 'delete_card_subscription',
                'guard_name' => 'web',
                'created_at' => '2025-03-13 01:17:04',
                'updated_at' => '2025-03-13 01:17:04',
            ),
            60 => 
            array (
                'id' => 61,
                'name' => 'assign_role_user',
                'guard_name' => 'web',
                'created_at' => '2025-03-13 01:17:04',
                'updated_at' => '2025-03-13 01:17:04',
            ),
            61 => 
            array (
                'id' => 62,
                'name' => 'assign_division_user',
                'guard_name' => 'web',
                'created_at' => '2025-03-13 01:17:04',
                'updated_at' => '2025-03-13 01:17:04',
            ),
            62 => 
            array (
                'id' => 63,
                'name' => 'verify_email_user',
                'guard_name' => 'web',
                'created_at' => '2025-03-13 01:17:04',
                'updated_at' => '2025-03-13 01:17:04',
            ),
            63 => 
            array (
                'id' => 64,
                'name' => 'view_all_user',
                'guard_name' => 'web',
                'created_at' => '2025-03-13 01:17:04',
                'updated_at' => '2025-03-13 01:17:04',
            ),
            64 => 
            array (
                'id' => 65,
                'name' => 'view_students_user',
                'guard_name' => 'web',
                'created_at' => '2025-03-13 01:17:04',
                'updated_at' => '2025-03-13 01:17:04',
            ),
            65 => 
            array (
                'id' => 66,
                'name' => 'view_with_roles_user',
                'guard_name' => 'web',
                'created_at' => '2025-03-13 01:17:04',
                'updated_at' => '2025-03-13 01:17:04',
            ),
            66 => 
            array (
                'id' => 67,
                'name' => 'widget_UsersCard',
                'guard_name' => 'web',
                'created_at' => '2025-03-13 01:17:04',
                'updated_at' => '2025-03-13 01:17:04',
            ),
        ));
        
        
    }
}