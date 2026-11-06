# الجداول المخصصة (Typed Modules)

## الفكرة
كل ملف Excel → `ModuleCode` من شيت `_System` → **جدول خاص** في قاعدة البيانات.

| ModuleCode | الجدول | Model |
|---|---|---|
| PRODUCTION_LINES | production_lines | ProductionLine |
| COMPANY | companies | Modules\Company |
| SITES_COMPANIES | sites | Modules\Site |
| PRODUCTIVE_CAPACITIES | productive_capacities | Modules\ProductiveCapacity |
| WAREHOUSES | warehouses | Modules\Warehouse |
| PRODUCTS_SERVICES | products_services | Modules\ProductService |
| FACILITIES_AND_PROPERTIES | facilities_properties | Modules\FacilityProperty |
| MUTUAL_DEPENDENCE | mutual_dependencies | Modules\MutualDependency |

## إضافة موديول جديد (من الـ 26)
1. أضف entry في `config/excel_modules.php` (mapping عربي → إنجليزي)
2. أنشئ migration + Model
3. ارفع الملف — النظام يكتشف ModuleCode تلقائياً

## الاستيراد
```
/admin/excel-modules/upload
```
بعد التأكيد: البيانات تروح للجدول المخصص (مش excel_records).

## العرض
```
/admin/excel-modules/table/PRODUCTION_LINES
/admin/excel-modules/table/COMPANY
```

## التحليل والـ Charts لاحقاً
الأعمدة منظمة (design_capacity, utilization_rate, governorate...)  
تقدر تعمل:
```php
ProductionLine::selectRaw('governorate, avg(utilization_rate)')->groupBy('governorate')->get();
```
