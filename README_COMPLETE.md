# AOI Crises Dashboard — المشروع الكامل

## إصلاح خطأ "Please provide a valid cache path"

هذا الخطأ يظهر لو مجلد `storage/framework/views` مش موجود.
في هذه النسخة المجلدات موجودة، ولو ظهرت المشكلة مرة تانية نفّذ:

**Windows (CMD):**
```bat
mkdir storage\framework\views
mkdir storage\framework\cache\data
mkdir storage\framework\sessions
mkdir storage\logs
mkdir bootstrap\cache
```

أو شغّل `setup.bat`

**Linux/Mac:**
```bash
bash setup.sh
```

---

## التشغيل

```bash
composer install
cp .env.example .env          # أو copy على ويندوز
php artisan key:generate
# عدّل بيانات الـ DB في .env
php artisan migrate
php artisan serve
```

ثم افتح: http://127.0.0.1:8000

- تسجيل الدخول حسب بيانات الـ seeder إن وُجدت
- النظام الديناميكي: `/admin/excel-modules/dashboard`

## المحتويات

- النظام الأصلي (Users, Roles, Permissions, Units, Production Lines)
- نظام Excel الديناميكي (كل ملفات الـ 26)
- 8 ملفات Excel تجريبية في `sample_excel_files/`
- تفاصيل النظام الديناميكي: `DYNAMIC_EXCEL_SYSTEM.md`
