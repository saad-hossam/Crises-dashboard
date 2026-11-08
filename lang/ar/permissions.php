<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Permissions — Arabic
    |--------------------------------------------------------------------------
    */

    // Titles
    'title'                     => 'الصلاحيات',
    'permission'                => 'الصلاحية',
    'create'                    => 'إضافة صلاحية',
    'edit'                      => 'تعديل الصلاحية',
    'show'                      => 'تفاصيل الصلاحية',
    'details'                   => 'التفاصيل',

    // Fields
    'name'                      => 'الاسم',
    'name_hint'                 => 'استخدم صيغة مثل: reports.export (حروف صغيرة ونقطة فاصلة).',
    'group'                     => 'المجموعة',
    'group_hint'                => 'مثال: reports، users، settings.',
    'description'               => 'الوصف',
    'description_placeholder'   => 'وصف مختصر للصلاحية (اختياري)',
    'created_at'                => 'تاريخ الإنشاء',
    'roles_count'               => 'عدد الأدوار',
    'search_placeholder'        => 'ابحث باسم الصلاحية...',
    'all_groups'                => 'كل المجموعات',
    'ungrouped'                 => 'بدون مجموعة',
    'roles_with_permission'     => 'الأدوار التي تمتلك هذه الصلاحية',

    // Messages
    'created_successfully'      => 'تم إنشاء الصلاحية بنجاح.',
    'updated_successfully'      => 'تم تحديث الصلاحية بنجاح.',
    'deleted_successfully'      => 'تم حذف الصلاحية بنجاح.',
    'delete_confirm'            => 'هل أنت متأكد من حذف هذه الصلاحية؟',
    'has_roles_hint'            => 'لا يمكن حذف الصلاحية لارتباطها بأدوار.',
    'no_roles'                  => 'لا توجد أدوار مرتبطة بهذه الصلاحية.',

    // Group labels (used in roles permission picker)
    'groups' => [
        'dashboard'     => 'لوحة التحكم',
        'users'         => 'المستخدمون',
        'roles'         => 'الأدوار',
        'permissions'   => 'الصلاحيات',
        'imports'       => 'الاستيراد',
        'data'          => 'البيانات',
        'reports'       => 'التقارير',
        'settings'      => 'الإعدادات',
        'activity_logs' => 'سجل النشاط',
        'units'         => 'الوحدات',
    ],
];