# Typed Excel Modules — AOI Crises Dashboard

كل ملف Excel له جدول مخصص + Model + mapping في `config/excel_modules.php`.

## الموديولات المفعّلة (25)

| Code | الاسم | الجدول |
|------|------|--------|
| PRODUCTION_LINES | خطوط الإنتاج | production_lines |
| COMPANY | الشركات | companies |
| SITES_COMPANIES | الفروع والمواقع | sites |
| PRODUCTIVE_CAPACITIES | القدرات الإنتاجية | productive_capacities |
| WAREHOUSES | المخازن | warehouses |
| PRODUCTS_SERVICES | المنتجات والخدمات | products_services |
| FACILITIES_AND_PROPERTIES | المنشآت والعقارات | facilities_properties |
| MUTUAL_DEPENDENCE | الاعتماد المتبادل | mutual_dependencies |
| COMMUNICATIONS | الاتصالات | communications |
| LANDS | الأراضي | lands |
| UNUSED_ID | الأصول غير المستغلة | unused_assets |
| STRATEGIC_SITES | المواقع الاستراتيجية | strategic_sites |
| INFRASTRUCTURE | البنية التحتية | infrastructures |
| CONTINUED_COMPANY | الشركات التابعة | subsidiary_companies |
| CONTRACTS_AND_OPERATIONS | العقود والتشغيل | contracts_operations |
| HOTELS | الفنادق | hotels |
| VEHICLES | المركبات | vehicles |
| HOSPITALS | المستشفيات | hospitals |
| PROJECTS | المشروعات | projects |
| EQUIPMENT | المعدات | equipment |
| HUMAN_RESOURCES | الموارد البشرية | human_resources |
| FIRE_STATIONS | الحماية المدنية | fire_stations |
| PRODUCTION_SCOPES | نطاقات الإنتاج | production_scopes |
| OPERATIONS_ROOMS | غرف العمليات | operations_rooms |
| MACHINES | ماكينات | machines |
| POWER_STATIONS | محطات الكهرباء | power_stations |

## التشغيل

```bash
php artisan migrate
# ثم ارفع أي ملف Excel من الـ 26 عبر صفحة الاستيراد
```

`TypedExcelImporter` يقرأ `_System` sheet → ModuleCode → يختار الجدول والـ mapping تلقائياً.
