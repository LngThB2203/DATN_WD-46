<?php
namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class VNPayController extends Controller
{
    public function vnpayReturn(Request $request)
    {
        $vnp_HashSecret = config('vnpay.vnp_HashSecret');
        $inputData      = $request->all();

        $vnp_SecureHash = $inputData['vnp_SecureHash'] ?? null;
        unset($inputData['vnp_SecureHash']);
        unset($inputData['vnp_SecureHashType']);

        ksort($inputData);
        $hashData = '';
        foreach ($inputData as $key => $value) {
            if ($hashData !== '') {
                $hashData .= '&';
            }

            $hashData .= $key . '=' . $value;
        }

        $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);

        if ($secureHash !== $vnp_SecureHash) {
            Log::warning('VNPay return: invalid signature', ['data' => $inputData]);
            return redirect()->route('orders.index')->with('error', 'Sai chữ ký — giao dịch không hợp lệ!');
        }

        $txnRef        = $request->get('vnp_TxnRef');
        $responseCode  = $request->get('vnp_ResponseCode');
        $transactionNo = $request->get('vnp_TransactionNo');
        $amount        = $request->get('vnp_Amount');

        DB::beginTransaction();
        try {
            $order = Order::find($txnRef);
            if (! $order) {
                DB::rollBack();
                return redirect()->route('orders.index')->with('error', 'Đơn hàng không tìm thấy.');
            }

            // kiểm tra số tiền
            $expected = intval(round($order->grand_total * 100));
            if ((int) $amount !== $expected) {
                Log::error('VNPay return: amount mismatch', ['order' => $order->id, 'expected' => $expected, 'got' => $amount]);
                DB::rollBack();
                return redirect()->route('orders.index')->with('error', 'Số tiền không khớp.');
            }

            if ($responseCode === '00') {
                Payment::where('order_id', $order->id)->update([
                    'status'           => 'paid',
                    'transaction_code' => $transactionNo,
                    'paid_at'          => now(),
                ]);
                $order->update(['order_status' => 'paid']);
                DB::commit();
                return redirect()->route('orders.index')->with('success', 'Thanh toán thành công!');
            } else {
                DB::rollBack();
                return redirect()->route('orders.index')->with('error', 'Thanh toán thất bại hoặc bị hủy!');
            }
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('VNPay return exception: ' . $e->getMessage());
            return redirect()->route('orders.index')->with('error', 'Lỗi xử lý sau thanh toán.');
        }
    }

    public function vnpayIpn(Request $request)
    {
        $vnp_HashSecret = config('vnpay.vnp_HashSecret');
        $data           = $request->all();

        $vnp_SecureHash = $data['vnp_SecureHash'] ?? null;
        unset($data['vnp_SecureHash']);
        unset($data['vnp_SecureHashType']);

        ksort($data);
        $hashData = '';
        foreach ($data as $k => $v) {
            if ($hashData !== '') {
                $hashData .= '&';
            }

            $hashData .= $k . '=' . $v;
        }

        $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);

        if ($secureHash !== $vnp_SecureHash) {
            Log::warning('VNPay IPN invalid signature', $data);
            return response('Invalid signature', 400);
        }

        $txnRef        = $data['vnp_TxnRef'] ?? null;
        $responseCode  = $data['vnp_ResponseCode'] ?? null;
        $transactionNo = $data['vnp_TransactionNo'] ?? null;
        $amount        = $data['vnp_Amount'] ?? null;

        $order = Order::find($txnRef);
        if (! $order) {
            Log::error('VNPay IPN order not found', ['txnRef' => $txnRef]);
            return response('Order not found', 404);
        }

        $expected = intval(round($order->grand_total * 100));
        if ((int) $amount !== $expected) {
            Log::error('VNPay IPN amount mismatch', ['order' => $order->id, 'expected' => $expected, 'amount' => $amount]);
            return response('Amount mismatch', 400);
        }

        if ($responseCode === '00') {
            Payment::where('order_id', $order->id)->update([
                'status'           => 'paid',
                'transaction_code' => $transactionNo,
                'paid_at'          => now(),
            ]);
            $order->update(['order_status' => 'paid']);
            Log::info('VNPay IPN processed', ['order_id' => $order->id]);
            return response('OK', 200);
        }

        Log::info('VNPay IPN not success', ['order_id' => $order->id, 'response' => $responseCode]);
        return response('Not success', 200);
    }
}
