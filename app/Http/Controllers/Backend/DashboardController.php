<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $today           = Carbon::today();
        $startOfMonth    = $today->copy()->startOfMonth();
        $twelveMonthsAgo = $today->copy()->subMonths(11)->startOfMonth();

        // ====== KHỞI TẠO BIẾN CHI TIẾT (TRÁNH LỖI UNDEFINED) ======
        $detail      = $request->get('detail');   // stock | sales_month | supplier_debt | customer_debt
        $detailItems = null;
        $detailTitle = '';

        /*
         * 1. TỔNG TỒN KHO
         */
        $totalStock = DB::table('vehicles')
            ->where('status', 'in_stock')
            ->count();

        /*
         * 2. SỐ LƯỢNG BÁN TRONG THÁNG
         */
        $soldThisMonth = DB::table('vehicle_sales')
            ->whereBetween('sale_date', [
                $startOfMonth->toDateString(),
                $today->toDateString()
            ])
            ->count();

        /*
         * 3. CÔNG NỢ NCC
         */
        $unpaidSupplierVehicles = DB::table('vehicles')
            ->where('supplier_paid', 0)
            ->count();

        $unpaidSupplierReceipts = DB::table('vehicles')
            ->where('supplier_paid', 0)
            ->whereNotNull('import_receipt_id')
            ->distinct('import_receipt_id')
            ->count('import_receipt_id');

        $totalSupplierDebt = DB::table('vehicles')
            ->where('supplier_paid', 0)
            ->sum(DB::raw('COALESCE(purchase_price,0)'));

        /*
         * 4. CÔNG NỢ KHÁCH HÀNG
         */
        $debtorCount = DB::table('vehicle_sales')
            ->where('debt_amount', '>', 0)
            ->count();

        $totalCustomerDebt = DB::table('vehicle_sales')
            ->where('debt_amount', '>', 0)
            ->sum('debt_amount');

        /*
         * 5. TOP DÒNG XE BÁN CHẠY (TỔNG)
         */
        $topModels = DB::table('vehicle_sales as vs')
            ->join('vehicles as v', 'vs.vehicle_id', '=', 'v.id')
            ->join('vehicle_models as m', 'v.model_id', '=', 'm.id')
            ->select('m.name', DB::raw('COUNT(*) as total'))
            ->groupBy('m.id', 'm.name')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        /*
         * 6. BIỂU ĐỒ SỐ LƯỢNG BÁN THEO DÒNG (THÁNG HIỆN TẠI)
         */
        $modelSalesThisMonth = DB::table('vehicle_sales as vs')
            ->join('vehicles as v', 'vs.vehicle_id', '=', 'v.id')
            ->join('vehicle_models as m', 'v.model_id', '=', 'm.id')
            ->whereBetween('vs.sale_date', [
                $startOfMonth->toDateString(),
                $today->toDateString()
            ])
            ->select('m.name', DB::raw('COUNT(*) as total'))
            ->groupBy('m.id', 'm.name')
            ->orderByDesc('total')
            ->get();

        $chartModelLabels = $modelSalesThisMonth->pluck('name');
        $chartModelValues = $modelSalesThisMonth->pluck('total');

        /*
         * 7. BIỂU ĐỒ DOANH THU 12 THÁNG GẦN NHẤT
         */
        $revenueRows = DB::table('vehicle_sales')
            ->whereBetween('sale_date', [
                $twelveMonthsAgo->toDateString(),
                $today->toDateString()
            ])
            ->select(
                DB::raw("DATE_FORMAT(sale_date, '%Y-%m-01') as ym"),
                DB::raw('SUM(amount) as total')
            )
            ->groupBy('ym')
            ->orderBy('ym')
            ->get()
            ->keyBy('ym');

        $periodLabels   = [];
        $revenueValues  = [];
        $cursor         = $twelveMonthsAgo->copy();

        while ($cursor <= $today) {
            $ymKey = $cursor->format('Y-m-01');
            $periodLabels[]  = $cursor->format('m/Y');
            $revenueValues[] = isset($revenueRows[$ymKey])
                ? (float) $revenueRows[$ymKey]->total
                : 0;
            $cursor->addMonth();
        }

        $chartRevenueLabels = collect($periodLabels);
        $chartRevenueValues = collect($revenueValues);

        /*
         * 8. BIỂU ĐỒ NHẬP - XUẤT 12 THÁNG GẦN NHẤT
         */
        $importRows = DB::table('import_receipts')
            ->whereBetween('import_date', [
                $twelveMonthsAgo->toDateString(),
                $today->toDateString()
            ])
            ->select(
                DB::raw("DATE_FORMAT(import_date, '%Y-%m-01') as ym"),
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('ym')
            ->orderBy('ym')
            ->get()
            ->keyBy('ym');

        $exportRows = DB::table('export_receipts')
            ->whereBetween('export_date', [
                $twelveMonthsAgo->toDateString(),
                $today->toDateString()
            ])
            ->select(
                DB::raw("DATE_FORMAT(export_date, '%Y-%m-01') as ym"),
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('ym')
            ->orderBy('ym')
            ->get()
            ->keyBy('ym');

        $inOutLabels    = [];
        $importValues   = [];
        $exportValues   = [];
        $cursor         = $twelveMonthsAgo->copy();

        while ($cursor <= $today) {
            $ymKey = $cursor->format('Y-m-01');
            $inOutLabels[]  = $cursor->format('m/Y');
            $importValues[] = isset($importRows[$ymKey]) ? (int)$importRows[$ymKey]->total : 0;
            $exportValues[] = isset($exportRows[$ymKey]) ? (int)$exportRows[$ymKey]->total : 0;
            $cursor->addMonth();
        }

        $chartInOutLabels  = collect($inOutLabels);
        $chartImportValues = collect($importValues);
        $chartExportValues = collect($exportValues);

        /*
         * 9. CẢNH BÁO DÒNG XE TỒN ÍT
         */
        $lowStockThreshold = 3;

        $lowStockModels = DB::table('vehicles as v')
            ->join('vehicle_models as m', 'v.model_id', '=', 'm.id')
            ->where('v.status', 'in_stock')
            ->select('m.id', 'm.name', DB::raw('COUNT(*) as stock_count'))
            ->groupBy('m.id', 'm.name')
            ->having('stock_count', '<=', $lowStockThreshold)
            ->orderBy('stock_count')
            ->get();

        /*
         * 10. XỬ LÝ CHI TIẾT THEO TỪNG KPI
         */
        if ($detail === 'stock') {
            // Chi tiết tồn kho (đúng với tổng tồn kho)
            $detailTitle = 'Chi tiết tồn kho (xe đang in_stock)';

            $detailItems = DB::table('vehicles as v')
                ->leftJoin('vehicle_models as m', 'v.model_id', '=', 'm.id')
                ->leftJoin('brands as b', 'v.brand_id', '=', 'b.id')
                ->leftJoin('colors as c', 'v.color_id', '=', 'c.id')
                ->leftJoin('warehouses as w', 'v.warehouse_id', '=', 'w.id')
                ->leftJoin('suppliers as s', 'v.supplier_id', '=', 's.id')
                ->where('v.status', 'in_stock')
                ->select(
                    'v.id',                             // ID xe
                    'b.name as brand',
                    'm.name as model',
                    'c.name as color',
                    'w.name as warehouse',
                    's.name as supplier',
                    'v.frame_no',
                    'v.engine_no',
                    'v.year',
                    'v.purchase_price',
                    'v.created_at'
                )
                ->orderByDesc('v.id')
                ->paginate(50);

        } elseif ($detail === 'sales_month') {
            // Chi tiết hóa đơn bán lẻ trong tháng
            $detailTitle = 'Chi tiết bán lẻ trong tháng ' . $startOfMonth->format('m/Y');

            $detailItems = DB::table('vehicle_sales as vs')
                ->join('vehicles as v', 'vs.vehicle_id', '=', 'v.id')
                ->join('vehicle_models as m', 'v.model_id', '=', 'm.id')
                ->join('customers as c', 'vs.customer_id', '=', 'c.id')
                ->whereBetween('vs.sale_date', [
                    $startOfMonth->toDateString(),
                    $today->toDateString()
                ])
                ->select(
                    'vs.id',                    // ID hóa đơn
                    'vs.code',                 // Mã HĐ
                    'vs.sale_date',
                    'c.name as customer_name',
                    'c.phone as customer_phone',
                    'm.name as model',
                    'v.frame_no',
                    'v.engine_no',
                    'vs.amount',
                    'vs.paid_amount',
                    'vs.debt_amount',
                    'vs.payment_status'
                )
                ->orderByDesc('vs.sale_date')
                ->paginate(50);

        } elseif ($detail === 'supplier_debt') {
            // Chi tiết phiếu nhập chưa thanh toán NCC
            $detailTitle = 'Chi tiết phiếu nhập chưa thanh toán NCC';

            $detailItems = DB::table('vehicles as v')
                ->join('import_receipts as ir', 'v.import_receipt_id', '=', 'ir.id')
                ->join('suppliers as s', 'ir.supplier_id', '=', 's.id')
                ->where('v.supplier_paid', 0)
                ->whereNotNull('v.import_receipt_id')
                ->groupBy('ir.id', 'ir.code', 'ir.import_date', 's.name', 'ir.total_amount')
                ->select(
                    'ir.id',                    // ID phiếu nhập
                    'ir.code',
                    'ir.import_date',
                    's.name as supplier_name',
                    DB::raw('COUNT(v.id) as veh_count'),
                    DB::raw('SUM(COALESCE(v.purchase_price,0)) as unpaid_amount'),
                    'ir.total_amount'
                )
                ->orderByDesc('ir.import_date')
                ->paginate(50);

        } elseif ($detail === 'customer_debt') {
            // Chi tiết công nợ khách hàng
            $detailTitle = 'Chi tiết công nợ khách hàng';

            $detailItems = DB::table('vehicle_sales as vs')
                ->join('customers as c', 'vs.customer_id', '=', 'c.id')
                ->join('vehicles as v', 'vs.vehicle_id', '=', 'v.id')
                ->join('vehicle_models as m', 'v.model_id', '=', 'm.id')
                ->where('vs.debt_amount', '>', 0)
                ->select(
                    'vs.id',                     // ID HĐ
                    'vs.code',
                    'vs.sale_date',
                    'c.name as customer_name',
                    'c.phone as customer_phone',
                    'm.name as model',
                    'v.frame_no',
                    'v.engine_no',
                    'vs.amount',
                    'vs.paid_amount',
                    'vs.debt_amount',
                    'vs.payment_status'
                )
                ->orderByDesc('vs.sale_date')
                ->paginate(50);
        }

        return view('backend.dashboard.index', [
            'totalStock'             => $totalStock,
            'soldThisMonth'          => $soldThisMonth,
            'startOfMonth'           => $startOfMonth,

            'unpaidSupplierVehicles' => $unpaidSupplierVehicles,
            'unpaidSupplierReceipts' => $unpaidSupplierReceipts,
            'totalSupplierDebt'      => $totalSupplierDebt,

            'debtorCount'            => $debtorCount,
            'totalCustomerDebt'      => $totalCustomerDebt,

            'topModels'              => $topModels,

            'chartModelLabels'       => $chartModelLabels,
            'chartModelValues'       => $chartModelValues,

            'chartRevenueLabels'     => $chartRevenueLabels,
            'chartRevenueValues'     => $chartRevenueValues,

            'chartInOutLabels'       => $chartInOutLabels,
            'chartImportValues'      => $chartImportValues,
            'chartExportValues'      => $chartExportValues,

            'lowStockThreshold'      => $lowStockThreshold,
            'lowStockModels'         => $lowStockModels,

            // Biến cho block chi tiết
            'detail'                 => $detail,
            'detailItems'            => $detailItems,
            'detailTitle'            => $detailTitle,
        ]);
    }
}
