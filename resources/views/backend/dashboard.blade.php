<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use App\Models\Vehicle;
use App\Models\ImportReceipt;
use App\Models\VehicleSale;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $today        = Carbon::today();
        $startOfMonth = $today->copy()->startOfMonth();
        $endOfMonth   = $today->copy()->endOfMonth();

        /*
        |--------------------------------------------------------------------------
        | 1. TỔNG TỒN KHO (XE ĐANG TRONG KHO)
        |--------------------------------------------------------------------------
        */
        $totalStock = Vehicle::where(function ($q) {
                $q->where('status', 0)
                  ->orWhere('status', 'in_stock');
            })
            ->count();

        /*
        |--------------------------------------------------------------------------
        | 2. SỐ LƯỢNG XE BÁN TRONG THÁNG (HÓA ĐƠN BÁN LẺ)
        |--------------------------------------------------------------------------
        */
        $soldThisMonth = VehicleSale::whereBetween('sale_date', [
                $startOfMonth->toDateString(),
                $endOfMonth->toDateString(),
            ])
            ->count();

        /*
        |--------------------------------------------------------------------------
        | 3. CÔNG NỢ NHÀ CUNG CẤP (NCC)
        |    - Dựa vào phiếu nhập: total_amount, paid_amount
        |    - Phiếu nào total_amount > paid_amount -> còn nợ
        |--------------------------------------------------------------------------
        */
        $unpaidImportQuery = ImportReceipt::query()
            ->whereColumn('total_amount', '>', 'paid_amount');

        // Số phiếu nhập còn nợ NCC
        $unpaidSupplierReceipts = (clone $unpaidImportQuery)->count();

        // Tổng nợ NCC (tổng total_amount - paid_amount)
        $totalSupplierDebt = (clone $unpaidImportQuery)
            ->select(DB::raw('SUM(total_amount - paid_amount) AS debt'))
            ->value('debt') ?? 0;

        /*
        |--------------------------------------------------------------------------
        | 4. CÔNG NỢ KHÁCH HÀNG
        |    - Dựa vào vehicle_sales: amount, paid_amount
        |    - HĐ nào amount > paid_amount -> còn nợ
        |--------------------------------------------------------------------------
        */
        $customerDebtQuery = VehicleSale::query()
            ->whereColumn('amount', '>', 'paid_amount');

        // Số khách đang nợ (distinct theo customer_id)
        $debtorCount = (clone $customerDebtQuery)
            ->distinct('customer_id')
            ->count('customer_id');

        // Tổng tiền khách nợ
        $totalCustomerDebt = (clone $customerDebtQuery)
            ->select(DB::raw('SUM(amount - paid_amount) AS debt'))
            ->value('debt') ?? 0;

        /*
        |--------------------------------------------------------------------------
        | 5. RANK DÒNG XE BÁN CHẠY (TOP MODEL)
        |--------------------------------------------------------------------------
        */
        $topModels = VehicleSale::selectRaw('vehicle_models.name AS name, COUNT(*) AS total')
            ->join('vehicles', 'vehicle_sales.vehicle_id', '=', 'vehicles.id')
            ->join('vehicle_models', 'vehicles.model_id', '=', 'vehicle_models.id')
            ->groupBy('vehicles.model_id', 'vehicle_models.name')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | 6. CẢNH BÁO DÒNG XE TỒN ÍT
        |    - Đếm xe trong kho theo model, model nào <= 3 xe thì cảnh báo
        |--------------------------------------------------------------------------
        */
        $lowStockModels = Vehicle::selectRaw('vehicle_models.name AS name, COUNT(*) AS total')
            ->join('vehicle_models', 'vehicles.model_id', '=', 'vehicle_models.id')
            ->where(function ($q) {
                $q->where('vehicles.status', 0)
                  ->orWhere('vehicles.status', 'in_stock');
            })
            ->groupBy('vehicles.model_id', 'vehicle_models.name')
            ->having('total', '<=', 3)
            ->orderBy('total', 'asc')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | 7. BIỂU ĐỒ DOANH THU THEO THÁNG (6 THÁNG GẦN NHẤT)
        |--------------------------------------------------------------------------
        */
        $months = collect(range(5, 0, -1))->map(function ($i) {
            return Carbon::now()->subMonths($i)->startOfMonth();
        });

        $revenueLabels = [];
        $revenueValues = [];

        foreach ($months as $m) {
            $monthStart = $m->copy();
            $monthEnd   = $m->copy()->endOfMonth();

            $label = $m->format('m/Y');

            $total = VehicleSale::whereBetween('sale_date', [
                    $monthStart->toDateString(),
                    $monthEnd->toDateString(),
                ])
                ->sum('amount');

            $revenueLabels[] = $label;
            $revenueValues[] = (int) $total;
        }

        /*
        |--------------------------------------------------------------------------
        | 8. BIỂU ĐỒ NHẬP – XUẤT THEO THÁNG (6 THÁNG GẦN NHẤT)
        |--------------------------------------------------------------------------
        |    - Nhập: đếm phiếu nhập
        |    - Xuất: đếm HĐ bán lẻ (coi như xuất kho)
        |--------------------------------------------------------------------------
        */
        $flowImport = [];
        $flowExport = [];

        foreach ($months as $m) {
            $monthStart = $m->copy();
            $monthEnd   = $m->copy()->endOfMonth();

            $importCnt = ImportReceipt::whereBetween('import_date', [
                    $monthStart->toDateString(),
                    $monthEnd->toDateString(),
                ])
                ->count();

            $exportCnt = VehicleSale::whereBetween('sale_date', [
                    $monthStart->toDateString(),
                    $monthEnd->toDateString(),
                ])
                ->count();

            $flowImport[] = $importCnt;
            $flowExport[] = $exportCnt;
        }

        /*
        |--------------------------------------------------------------------------
        | 9. KHỐI CHI TIẾT THEO CARD ĐƯỢC CLICK (detail = ...)
        |--------------------------------------------------------------------------
        |    detail = stock           -> danh sách xe đang trong kho
        |    detail = sales_month     -> HĐ bán lẻ trong tháng
        |    detail = supplier_debt   -> phiếu nhập còn nợ NCC
        |    detail = customer_debt   -> HĐ bán lẻ còn nợ KH
        |--------------------------------------------------------------------------
        */
        $detailType  = $request->get('detail');
        $detailTitle = null;
        $detailItems = null;

        switch ($detailType) {
            case 'stock':
                $detailTitle = 'Danh sách xe đang trong kho';
                $detailItems = Vehicle::with(['model.brand', 'color', 'warehouse'])
                    ->where(function ($q) {
                        $q->where('status', 0)
                          ->orWhere('status', 'in_stock');
                    })
                    ->orderBy('warehouse_id')
                    ->orderBy('frame_no')
                    ->paginate(20);
                break;

            case 'sales_month':
                $detailTitle = 'Hóa đơn bán lẻ trong tháng hiện tại';
                $detailItems = VehicleSale::with(['customer', 'vehicle.model.brand'])
                    ->whereBetween('sale_date', [
                        $startOfMonth->toDateString(),
                        $endOfMonth->toDateString(),
                    ])
                    ->orderByDesc('sale_date')
                    ->paginate(20);
                break;

            case 'supplier_debt':
                $detailTitle = 'Phiếu nhập còn nợ nhà cung cấp';
                $detailItems = ImportReceipt::with(['supplier', 'warehouse'])
                    ->whereColumn('total_amount', '>', 'paid_amount')
                    ->orderByDesc('import_date')
                    ->paginate(20);
                break;

            case 'customer_debt':
                $detailTitle = 'Hóa đơn bán lẻ còn nợ khách hàng';
                $detailItems = VehicleSale::with(['customer', 'vehicle.model.brand'])
                    ->whereColumn('amount', '>', 'paid_amount')
                    ->orderByDesc('sale_date')
                    ->paginate(20);
                break;
        }

        if ($detailItems) {
            $detailItems->appends(['detail' => $detailType]);
        }

        return view('backend.dashboard.index', compact(
            'startOfMonth',
            'totalStock',
            'soldThisMonth',
            'unpaidSupplierReceipts',
            'totalSupplierDebt',
            'debtorCount',
            'totalCustomerDebt',
            'topModels',
            'lowStockModels',
            'revenueLabels',
            'revenueValues',
            'flowImport',
            'flowExport',
            'detailType',
            'detailTitle',
            'detailItems'
        ));
    }
}
