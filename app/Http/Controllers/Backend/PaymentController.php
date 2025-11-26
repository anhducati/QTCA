<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\ExportReceipt;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = Payment::with('receipt.customer');

        if ($code = $request->get('code')) {
            $query->whereHas('receipt', function ($q) use ($code) {
                $q->where('code', 'like', "%{$code}%");
            });
        }

        $payments = $query->orderByDesc('payment_date')->orderByDesc('id')->paginate(20);

        return view('backend.payments.index', compact('payments'));
    }

    public function create(ExportReceipt $exportReceipt)
    {
        return view('backend.payments.create', compact('exportReceipt'));
    }

    public function store(Request $request, ExportReceipt $exportReceipt)
    {
        $data = $request->validate([
            'payment_date' => 'required|date',
            'amount'       => 'required|numeric|min:0.01',
            'method'       => 'nullable|string|max:50',
            'note'         => 'nullable|string',
        ]);

        DB::transaction(function () use ($data, $exportReceipt) {
            Payment::create([
                'export_receipt_id' => $exportReceipt->id,
                'payment_date'      => $data['payment_date'],
                'amount'            => $data['amount'],
                'method'            => $data['method'] ?? null,
                'note'              => $data['note'] ?? null,
            ]);

            $totalPaid = Payment::where('export_receipt_id', $exportReceipt->id)->sum('amount');
            $debt      = $exportReceipt->total_amount - $totalPaid;

            $status = 'unpaid';
            if ($debt <= 0) {
                $status = 'paid';
                $debt   = 0;
            } elseif ($totalPaid > 0 && $debt > 0) {
                $status = 'partial';
            }

            $exportReceipt->update([
                'paid_amount'    => $totalPaid,
                'debt_amount'    => $debt,
                'payment_status' => $status,
            ]);
        });

        return redirect()->route('admin.export_receipts.show', $exportReceipt->id)
            ->with('success', 'Thu tiền khách hàng thành công');
    }

    public function destroy(Payment $payment)
    {
        $receipt = $payment->receipt;
        DB::transaction(function () use ($payment, $receipt) {
            $payment->delete();

            $totalPaid = Payment::where('export_receipt_id', $receipt->id)->sum('amount');
            $debt      = $receipt->total_amount - $totalPaid;

            $status = 'unpaid';
            if ($debt <= 0) {
                $status = 'paid';
                $debt   = 0;
            } elseif ($totalPaid > 0 && $debt > 0) {
                $status = 'partial';
            }

            $receipt->update([
                'paid_amount'    => $totalPaid,
                'debt_amount'    => $debt,
                'payment_status' => $status,
            ]);
        });

        return back()->with('success', 'Xóa phiếu thu thành công');
    }
}
