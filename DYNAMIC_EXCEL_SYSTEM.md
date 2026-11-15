# نظام استيراد Excel الديناميكي – الهيئة العربية للتصنيع

## ما تم إنجازه بالكامل

| الميزة | الحالة |
|--------|--------|
| Views كاملة (index / upload / preview / show) | ✅ |
| فلترة متقدمة (بحث + وحدة + عمود محدد) | ✅ |
| تصدير XLSX حقيقي (Maatwebsite) + fallback CSV | ✅ |
| Dashboard إحصائي (KPIs + Chart + آخر استيرادات) | ✅ |
| Artisan Command لنقل production_lines القديمة | ✅ |
| نظام Generic لأي ملف من الـ 26 | ✅ |
| حماية من تكرار الملف (SHA-256) | ✅ |

---

## هيكل الملفات النهائي

```
dynamic_excel_system/
├── migrations/          (3 ملفات)
├── models/              (ExcelModule, ExcelImportBatch, ExcelRecord)
├── services/            (GenericExcelImporter.php)
├── controllers/         (ExcelModuleController.php)  ← فيه dashboard + export
├── commands/            (MigrateProductionLinesToExcelRecords.php)
├── exports/             (ExcelModuleExport.php)
├── views/admin/excel-modules/
│   ├── dashboard.blade.php
│   ├── index.blade.php
│   ├── create.blade.php
│   ├── preview.blade.php
│   └── show.blade.php
├── config/excel_modules.php
├── routes_snippet.php
└── README.md
```

---

## خطوات التركيب

### 1. انسخ الملفات

```bash
# من داخل مجلد مشروع Laravel
cp migrations/*   database/migrations/
cp models/*       app/Models/
cp services/*     app/Services/
cp controllers/*  app/Http/Controllers/Admin/
cp commands/*     app/Console/Commands/
mkdir -p app/Exports
cp exports/*      app/Exports/
cp config/*       config/
mkdir -p resources/views/admin/excel-modules
cp views/admin/excel-modules/* resources/views/admin/excel-modules/
```

### 2. Migration

```bash
php artisan migrate
```

### 3. Routes

الصق محتوى `routes_snippet.php` داخل مجموعة الـ admin في `routes/web.php`.

### 4. Sidebar (اختياري)

```blade
<li class="nav-item">
    <a href="{{ route('admin.excel-modules.dashboard') }}"
       class="nav-link {{ request()->routeIs('admin.excel-modules.*') ? 'active' : '' }}">
        <i class="bi bi-file-earmark-spreadsheet"></i>
        <span>بيانات Excel</span>
    </a>
</li>
```

### 5. جرب

```
/admin/excel-modules/dashboard   ← اللوحة الإحصائية
/admin/excel-modules             ← كروت الموديولات
/admin/excel-modules/upload      ← رفع ملف
```

---

## نقل بيانات خطوط الإنتاج القديمة

لو عندك بيانات في جدول `production_lines` وعايز تنقلها للنظام الجديد:

```bash
# نقل مع الإبقاء على القديم
php artisan excel:migrate-production-lines

# أو مسح سجلات PRODUCTION_LINES من النظام الجديد أولاً ثم النقل
php artisan excel:migrate-production-lines --fresh
```

---

## التصدير

- الزر في صفحة الـ Show بينزل **XLSX حقيقي** باستخدام Maatwebsite/Excel (موجود أصلاً في المشروع).
- لو المكتبة مش متاحة لأي سبب، بيحصل fallback تلقائي لـ CSV مع BOM.

الفلاتر الحالية (بحث / وحدة / عمود) بتتحفظ في التصدير.

---

## الموديولات المدعومة من الملفات الحالية

| ModuleCode                  | الاسم العربي              |
|-----------------------------|---------------------------|
| MUTUAL_DEPENDENCE           | الاعتماد المتبادل         |
| COMPANY                     | الشركات                   |
| SITES_COMPANIES             | الفروع والمواقع           |
| PRODUCTIVE_CAPACITIES       | القدرات الإنتاجية         |
| WAREHOUSES                  | المخازن                   |
| PRODUCTS_SERVICES           | المنتجات والخدمات         |
| FACILITIES_AND_PROPERTIES   | المنشآت والعقارات         |
| PRODUCTION_LINES            | خطوط الإنتاج              |

أي ملف جديد بنفس الـ Template (`البيانات` + `_System`) هيشتغل فوراً بدون تعديل كود.
