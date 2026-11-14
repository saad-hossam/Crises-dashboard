<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ManufacturingReportController extends Controller
{
    /**
     * عرض الصفحة الرئيسية للتقرير
     */
    public function index()
    {
        return view('admin.manufacturing-report.index');
    }

    /**
     * بيانات JSON كاملة للتقرير
     */
    public function data(Request $request)
    {
        $inventorySummary = $this->inventorySummary();
        $fuelData         = $this->fuelData();

        return response()->json([
            'overview'  => $this->overviewData(),
            'machines'  => $this->machinesData(),
            'lines'     => $this->linesData(),

            // ✅ inventory كـ array مباشرة (الشكل القديم)
            'inventory' => $inventorySummary,

            // ✅ fuel كـ array مباشرة (الشكل القديم)
            'fuel'      => $fuelData,

            // ✅ كمان nested object (للتوافق مع الكود اللي بيقرأ من .summary)
            'inventory_full' => [
                'summary' => $inventorySummary,
                'fuel'    => $fuelData,
            ],

            'hr'        => $this->hrData(),
            'vehicles'  => $this->vehiclesData(),
            'capacity'  => $this->capacityData(),
        ]);
    }

    public function overview()      { return response()->json($this->overviewData()); }
    public function machines()      { return response()->json($this->machinesData()); }
    public function productionLines(){ return response()->json($this->linesData()); }

    public function inventory()
    {
        return response()->json([
            'summary' => $this->inventorySummary(),
            'fuel'    => $this->fuelData(),
        ]);
    }

    public function hr()            { return response()->json($this->hrData()); }
    public function vehicles()      { return response()->json($this->vehiclesData()); }
    public function capacity()      { return response()->json($this->capacityData()); }
    public function warehouses()    { return response()->json($this->warehousesData()); }
    public function facilities()    { return response()->json($this->facilitiesData()); }

    public function exportExcel()   { /* TODO: Maatwebsite Excel */ }
    public function exportPdf()     { /* TODO: DomPDF */ }

    /* ==================== Data Sources ==================== */

    protected function overviewData(): array
    {
        return [
            ['unit'=>'ABD','machines'=>0,'validity'=>0,'lines'=>0,'workingLines'=>0,'avgLineUtil'=>0,'workers'=>0,'vehicles'=>0,'validVehicles'=>0,'warehouses'=>0,'facilities'=>0,'products'=>0],
            ['unit'=>'اتيكو','machines'=>49,'validity'=>100,'lines'=>4,'workingLines'=>100,'avgLineUtil'=>0,'workers'=>184,'vehicles'=>20,'validVehicles'=>17,'warehouses'=>10,'facilities'=>37,'products'=>0],
            ['unit'=>'الافريقية الطبية','machines'=>0,'validity'=>0,'lines'=>5,'workingLines'=>0,'avgLineUtil'=>0.06,'workers'=>94,'vehicles'=>0,'validVehicles'=>0,'warehouses'=>0,'facilities'=>1,'products'=>5],
            ['unit'=>'الالكترونيات','machines'=>25,'validity'=>0,'lines'=>22,'workingLines'=>100,'avgLineUtil'=>40.0012,'workers'=>420,'vehicles'=>0,'validVehicles'=>0,'warehouses'=>8,'facilities'=>7,'products'=>22],
            ['unit'=>'الجهاز','machines'=>0,'validity'=>0,'lines'=>0,'workingLines'=>0,'avgLineUtil'=>0,'workers'=>1191,'vehicles'=>90,'validVehicles'=>90,'warehouses'=>3,'facilities'=>0,'products'=>0],
            ['unit'=>'السيارات','machines'=>0,'validity'=>0,'lines'=>1,'workingLines'=>100,'avgLineUtil'=>45,'workers'=>523,'vehicles'=>62,'validVehicles'=>57,'warehouses'=>5,'facilities'=>12,'products'=>1],
            ['unit'=>'الطائرات','machines'=>422,'validity'=>90.5,'lines'=>7,'workingLines'=>100,'avgLineUtil'=>64.4771,'workers'=>1224,'vehicles'=>80,'validVehicles'=>72,'warehouses'=>8,'facilities'=>58,'products'=>14],
            ['unit'=>'الطاقة','machines'=>0,'validity'=>0,'lines'=>1,'workingLines'=>100,'avgLineUtil'=>0,'workers'=>109,'vehicles'=>0,'validVehicles'=>0,'warehouses'=>4,'facilities'=>0,'products'=>1],
            ['unit'=>'الطبية','machines'=>0,'validity'=>0,'lines'=>0,'workingLines'=>0,'avgLineUtil'=>0,'workers'=>0,'vehicles'=>16,'validVehicles'=>15,'warehouses'=>0,'facilities'=>0,'products'=>0],
            ['unit'=>'العقارية','machines'=>0,'validity'=>0,'lines'=>0,'workingLines'=>0,'avgLineUtil'=>0,'workers'=>0,'vehicles'=>6,'validVehicles'=>6,'warehouses'=>0,'facilities'=>0,'products'=>0],
            ['unit'=>'اللوحات','machines'=>0,'validity'=>0,'lines'=>1,'workingLines'=>100,'avgLineUtil'=>87,'workers'=>46,'vehicles'=>3,'validVehicles'=>3,'warehouses'=>3,'facilities'=>1,'products'=>1],
            ['unit'=>'المحركات','machines'=>0,'validity'=>0,'lines'=>15,'workingLines'=>93.3,'avgLineUtil'=>0.6713,'workers'=>1566,'vehicles'=>132,'validVehicles'=>116,'warehouses'=>12,'facilities'=>2,'products'=>16],
            ['unit'=>'المستشفي','machines'=>0,'validity'=>0,'lines'=>0,'workingLines'=>0,'avgLineUtil'=>0,'workers'=>0,'vehicles'=>0,'validVehicles'=>0,'warehouses'=>9,'facilities'=>0,'products'=>0],
            ['unit'=>'المصاعد','machines'=>0,'validity'=>0,'lines'=>0,'workingLines'=>0,'avgLineUtil'=>0,'workers'=>3,'vehicles'=>3,'validVehicles'=>3,'warehouses'=>1,'facilities'=>2,'products'=>0],
            ['unit'=>'بحوث الطيران','machines'=>0,'validity'=>0,'lines'=>0,'workingLines'=>0,'avgLineUtil'=>0,'workers'=>25,'vehicles'=>3,'validVehicles'=>3,'warehouses'=>0,'facilities'=>0,'products'=>0],
            ['unit'=>'حلوان','machines'=>208,'validity'=>63.5,'lines'=>17,'workingLines'=>100,'avgLineUtil'=>47.7647,'workers'=>570,'vehicles'=>43,'validVehicles'=>43,'warehouses'=>5,'facilities'=>28,'products'=>6],
        ];
    }

    protected function machinesData(): array
    {
        return [
            ['unit'=>'قادر','total'=>471,'valid'=>0,'invalid'=>0,'unknown'=>471,'availHours'=>0,'usedHours'=>0,'loadRate'=>null,'validityPct'=>0,'utilPct'=>null],
            ['unit'=>'الطائرات','total'=>422,'valid'=>382,'invalid'=>40,'unknown'=>0,'availHours'=>314640,'usedHours'=>182692,'loadRate'=>0.5165,'validityPct'=>90.5,'utilPct'=>58.1],
            ['unit'=>'حلوان','total'=>208,'valid'=>132,'invalid'=>7,'unknown'=>69,'availHours'=>104800,'usedHours'=>91400,'loadRate'=>0.5004,'validityPct'=>63.5,'utilPct'=>87.2],
            ['unit'=>'اتيكو','total'=>49,'valid'=>49,'invalid'=>0,'unknown'=>0,'availHours'=>25040,'usedHours'=>11675,'loadRate'=>0.4072,'validityPct'=>100,'utilPct'=>46.6],
            ['unit'=>'الالكترونيات','total'=>25,'valid'=>0,'invalid'=>0,'unknown'=>25,'availHours'=>0,'usedHours'=>0,'loadRate'=>null,'validityPct'=>0,'utilPct'=>null],
        ];
    }

    protected function linesData(): array
    {
        return [
            ['unit'=>'الالكترونيات','total'=>22,'working'=>22,'notWorking'=>0,'underPrep'=>0,'avgUtil'=>40.0012,'workingPct'=>100],
            ['unit'=>'سيماف','total'=>18,'working'=>18,'notWorking'=>0,'underPrep'=>0,'avgUtil'=>0.3796,'workingPct'=>100],
            ['unit'=>'حلوان','total'=>17,'working'=>17,'notWorking'=>0,'underPrep'=>0,'avgUtil'=>47.7647,'workingPct'=>100],
            ['unit'=>'المحركات','total'=>15,'working'=>14,'notWorking'=>1,'underPrep'=>0,'avgUtil'=>0.6713,'workingPct'=>93.3],
            ['unit'=>'قادر','total'=>9,'working'=>5,'notWorking'=>4,'underPrep'=>0,'avgUtil'=>33.1667,'workingPct'=>55.6],
            ['unit'=>'الطائرات','total'=>7,'working'=>7,'notWorking'=>0,'underPrep'=>0,'avgUtil'=>64.4771,'workingPct'=>100],
            ['unit'=>'الافريقية الطبية','total'=>5,'working'=>0,'notWorking'=>0,'underPrep'=>5,'avgUtil'=>0.06,'workingPct'=>0],
            ['unit'=>'صقر','total'=>5,'working'=>5,'notWorking'=>0,'underPrep'=>0,'avgUtil'=>82.4,'workingPct'=>100],
            ['unit'=>'اتيكو','total'=>4,'working'=>4,'notWorking'=>0,'underPrep'=>0,'avgUtil'=>null,'workingPct'=>100],
            ['unit'=>'السيارات','total'=>1,'working'=>1,'notWorking'=>0,'underPrep'=>0,'avgUtil'=>45,'workingPct'=>100],
            ['unit'=>'اللوحات','total'=>1,'working'=>1,'notWorking'=>0,'underPrep'=>0,'avgUtil'=>87,'workingPct'=>100],
            ['unit'=>'الطاقة','total'=>1,'working'=>1,'notWorking'=>0,'underPrep'=>0,'avgUtil'=>null,'workingPct'=>100],
        ];
    }

    protected function inventorySummary(): array
    {
        return [
            ['unit'=>'السيارات','items'=>35044,'positive'=>35022,'zero'=>22,'avgDays'=>90,'minDays'=>90,'maxDays'=>90],
            ['unit'=>'قادر','items'=>110,'positive'=>45,'zero'=>65,'avgDays'=>49.4,'minDays'=>16,'maxDays'=>925],
            ['unit'=>'اللوحات','items'=>7,'positive'=>4,'zero'=>1,'avgDays'=>169.5,'minDays'=>56,'maxDays'=>252],
            ['unit'=>'المحركات','items'=>3,'positive'=>3,'zero'=>0,'avgDays'=>17,'minDays'=>5,'maxDays'=>40],
            ['unit'=>'الطائرات','items'=>2,'positive'=>2,'zero'=>0,'avgDays'=>7.5,'minDays'=>7,'maxDays'=>8],
            ['unit'=>'حلوان','items'=>1,'positive'=>1,'zero'=>0,'avgDays'=>38,'minDays'=>38,'maxDays'=>38],
        ];
    }

    protected function fuelData(): array
    {
        return [
            ['item'=>'بنزين','qty'=>10000,'unit'=>'لتر','days'=>7,'org'=>'الطائرات','readiness'=>'جاهز'],
            ['item'=>'سولار','qty'=>16000,'unit'=>'لتر','days'=>8,'org'=>'الطائرات','readiness'=>'جاهز'],
            ['item'=>'سولار','qty'=>18000,'unit'=>'لتر','days'=>6,'org'=>'المحركات','readiness'=>'مرتفع'],
            ['item'=>'بنزين','qty'=>6300,'unit'=>'لتر','days'=>5,'org'=>'المحركات','readiness'=>'مرتفع'],
            ['item'=>'jet-A1','qty'=>129000,'unit'=>'لتر','days'=>40,'org'=>'المحركات','readiness'=>'مرتفع'],
        ];
    }

    protected function hrData(): array
    {
        return [
            ['unit'=>'المحركات','specialties'=>598,'totalWorkers'=>1566,'emergency'=>1470],
            ['unit'=>'الطائرات','specialties'=>558,'totalWorkers'=>1224,'emergency'=>899],
            ['unit'=>'الجهاز','specialties'=>5,'totalWorkers'=>1191,'emergency'=>0],
            ['unit'=>'قادر','specialties'=>262,'totalWorkers'=>1164,'emergency'=>839],
            ['unit'=>'سيماف','specialties'=>219,'totalWorkers'=>974,'emergency'=>435],
            ['unit'=>'حلوان','specialties'=>225,'totalWorkers'=>570,'emergency'=>0],
            ['unit'=>'السيارات','specialties'=>15,'totalWorkers'=>523,'emergency'=>0],
            ['unit'=>'الالكترونيات','specialties'=>9,'totalWorkers'=>420,'emergency'=>306],
            ['unit'=>'اتيكو','specialties'=>50,'totalWorkers'=>184,'emergency'=>0],
            ['unit'=>'الطاقة','specialties'=>109,'totalWorkers'=>109,'emergency'=>0],
            ['unit'=>'الافريقية الطبية','specialties'=>32,'totalWorkers'=>94,'emergency'=>6],
            ['unit'=>'اللوحات','specialties'=>14,'totalWorkers'=>46,'emergency'=>28],
            ['unit'=>'بحوث الطيران','specialties'=>25,'totalWorkers'=>25,'emergency'=>25],
            ['unit'=>'المصاعد','specialties'=>3,'totalWorkers'=>3,'emergency'=>3],
            ['unit'=>'صقر','specialties'=>109,'totalWorkers'=>0,'emergency'=>1126],
        ];
    }

    protected function vehiclesData(): array
    {
        return [
            ['unit'=>'المحركات','total'=>132,'valid'=>116,'broken'=>16,'scrapped'=>0],
            ['unit'=>'قادر','total'=>97,'valid'=>91,'broken'=>6,'scrapped'=>0],
            ['unit'=>'الجهاز','total'=>90,'valid'=>90,'broken'=>0,'scrapped'=>0],
            ['unit'=>'الطائرات','total'=>80,'valid'=>72,'broken'=>8,'scrapped'=>0],
            ['unit'=>'سيماف','total'=>76,'valid'=>76,'broken'=>3,'scrapped'=>0],
            ['unit'=>'السيارات','total'=>62,'valid'=>57,'broken'=>5,'scrapped'=>0],
            ['unit'=>'حلوان','total'=>43,'valid'=>43,'broken'=>0,'scrapped'=>0],
            ['unit'=>'اتيكو','total'=>20,'valid'=>17,'broken'=>1,'scrapped'=>2],
            ['unit'=>'الطبية','total'=>16,'valid'=>15,'broken'=>1,'scrapped'=>0],
            ['unit'=>'العقارية','total'=>6,'valid'=>6,'broken'=>0,'scrapped'=>0],
            ['unit'=>'اللوحات','total'=>3,'valid'=>3,'broken'=>0,'scrapped'=>0],
            ['unit'=>'المصاعد','total'=>3,'valid'=>3,'broken'=>0,'scrapped'=>0],
            ['unit'=>'بحوث الطيران','total'=>3,'valid'=>3,'broken'=>0,'scrapped'=>0],
        ];
    }

    protected function capacityData(): array
    {
        return [
            ['unit'=>'صقر','products'=>25,'designCapacity'=>335550,'availableCapacity'=>252257,'actualProduction'=>9115,'utilization'=>3.6],
            ['unit'=>'الالكترونيات','products'=>22,'designCapacity'=>3556124,'availableCapacity'=>2748902,'actualProduction'=>423513,'utilization'=>15.4],
            ['unit'=>'المحركات','products'=>16,'designCapacity'=>15,'availableCapacity'=>15,'actualProduction'=>6,'utilization'=>40],
            ['unit'=>'قادر','products'=>16,'designCapacity'=>0,'availableCapacity'=>27040,'actualProduction'=>250,'utilization'=>0.9],
            ['unit'=>'الطائرات','products'=>14,'designCapacity'=>6212,'availableCapacity'=>6212,'actualProduction'=>6022,'utilization'=>96.9],
            ['unit'=>'سيماف','products'=>8,'designCapacity'=>1166,'availableCapacity'=>763,'actualProduction'=>259,'utilization'=>33.9],
            ['unit'=>'حلوان','products'=>6,'designCapacity'=>220000,'availableCapacity'=>120000,'actualProduction'=>30912,'utilization'=>25.8],
            ['unit'=>'الافريقية الطبية','products'=>5,'designCapacity'=>160177000,'availableCapacity'=>17700,'actualProduction'=>0,'utilization'=>0],
            ['unit'=>'السيارات','products'=>1,'designCapacity'=>0,'availableCapacity'=>0,'actualProduction'=>2265,'utilization'=>null],
        ];
    }

    protected function warehousesData(): array
    {
        return [
            ['unit'=>'صقر','count'=>17,'area'=>14100,'designCap'=>8460,'usedCap'=>8460],
            ['unit'=>'قادر','count'=>15,'area'=>6336,'designCap'=>6396,'usedCap'=>4585],
            ['unit'=>'المحركات','count'=>12,'area'=>382080,'designCap'=>400935,'usedCap'=>215015],
            ['unit'=>'سيماف','count'=>12,'area'=>8014,'designCap'=>855,'usedCap'=>0],
            ['unit'=>'اتيكو','count'=>10,'area'=>0,'designCap'=>0,'usedCap'=>0],
            ['unit'=>'المستشفي','count'=>9,'area'=>652,'designCap'=>652,'usedCap'=>456.4],
            ['unit'=>'الالكترونيات','count'=>8,'area'=>7850,'designCap'=>23550,'usedCap'=>23550],
            ['unit'=>'الطائرات','count'=>8,'area'=>6019,'designCap'=>6019,'usedCap'=>3590],
            ['unit'=>'حلوان','count'=>5,'area'=>103220,'designCap'=>327100,'usedCap'=>190015],
            ['unit'=>'السيارات','count'=>5,'area'=>8577,'designCap'=>8577,'usedCap'=>8577],
        ];
    }

    protected function facilitiesData(): array
    {
        return [
            ['unit'=>'صقر','count'=>189,'totalArea'=>193011.5,'builtArea'=>192962.5],
            ['unit'=>'قادر','count'=>81,'totalArea'=>66026,'builtArea'=>66028],
            ['unit'=>'الطائرات','count'=>58,'totalArea'=>95831,'builtArea'=>95831],
            ['unit'=>'سيماف','count'=>39,'totalArea'=>222003,'builtArea'=>138525],
            ['unit'=>'اتيكو','count'=>37,'totalArea'=>0,'builtArea'=>0],
            ['unit'=>'حلوان','count'=>28,'totalArea'=>142596.3,'builtArea'=>142596.3],
            ['unit'=>'السيارات','count'=>12,'totalArea'=>0,'builtArea'=>0],
            ['unit'=>'الالكترونيات','count'=>7,'totalArea'=>0,'builtArea'=>0],
            ['unit'=>'المحركات','count'=>2,'totalArea'=>0,'builtArea'=>206666],
        ];
    }
}