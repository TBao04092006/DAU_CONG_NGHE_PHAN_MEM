<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BorrowTicket;
use App\Models\Transaction;
use App\Models\SystemRule;

class PaymentController extends Controller
{
    public function generateVietQR(Request $request)
    {
        $rules = SystemRule::first();
        $bank = $rules->bank_name ?? 'MBBank';
        $acc = $rules->bank_account ?? '0987654321';
        $holder = $rules->account_holder ?? 'THU VIEN LIBRANOVA QUOC GIA';
        
        $amount = (int)$request->amount;
        $desc = $request->description ?: 'NOP PHAT THU VIEN LIBRANOVA';

        // Napas 247 VietQR QuickLink format
        // https://img.vietqr.io/image/<BANK_ID>-<ACCOUNT_NO>-compact.png?amount=<AMOUNT>&addInfo=<DESCRIPTION>&accountName=<ACCOUNT_NAME>
        $bankBin = 'MB'; // Standard MBBank alias
        $qrUrl = "https://img.vietqr.io/image/{$bankBin}-{$acc}-compact2.png?amount={$amount}&addInfo=" . urlencode($desc) . "&accountName=" . urlencode($holder);

        return response()->json([
            'qr_url' => $qrUrl,
            'bank' => $bank,
            'account' => $acc,
            'holder' => $holder,
            'amount' => $amount,
            'description' => $desc
        ]);
    }

    public function confirmPayment(Request $request)
    {
        $ticketId = $request->ticket_id;
        $amount = $request->amount;
        $method = $request->payment_method ?? 'vietqr';

        if ($ticketId) {
            $ticket = BorrowTicket::find($ticketId);
            if ($ticket) {
                $ticket->payment_status = 'paid';
                $ticket->save();

                Transaction::create([
                    'transaction_code' => 'TXN-' . strtoupper(substr(uniqid(), -6)),
                    'ticket_id' => $ticket->id,
                    'reader_id' => $ticket->reader_id,
                    'reader_name' => $ticket->reader->name ?? 'Độc giả',
                    'amount' => $amount ?: $ticket->fine_amount,
                    'type' => 'fine',
                    'payment_method' => $method,
                    'description' => "Thanh toán tiền phạt trễ hạn trực tuyến ({$method})",
                    'status' => 'completed'
                ]);
            }
        }

        return response()->json(['status' => 'success', 'message' => 'Giao dịch thanh toán thành công!']);
    }
}
