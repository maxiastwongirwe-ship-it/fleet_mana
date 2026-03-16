<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentRequest;
use Illuminate\Http\Request;

class PaymentRequestController extends Controller
{
    public function index()
    {
        $payments = PaymentRequest::with(['fuelRequest.vehicle', 'requester'])
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('admin.payment-requests.index', compact('payments'));
    }

    public function show(PaymentRequest $paymentRequest)
    {
        $paymentRequest->load([
            'fuelRequest.vehicle',
            'fuelRequest.requester',
            'requester',
            'fuelRequest.approvedBy'
        ]);

        return view('admin.payment-requests.show', compact('paymentRequest'));
    }

    /**
     * Approve the payment request
     */
    public function approve(PaymentRequest $paymentRequest)
    {
        if (!$paymentRequest->isPending()) {
            return back()->with('error', 'This payment request is no longer pending.');
        }

        $paymentRequest->update([
            'status' => 'approved',
        ]);

        $paymentRequest->fuelRequest->update([
            'status' => 'payment_approved',
        ]);

        return redirect()->route('admin.payment-requests.index')
            ->with('success', 'Payment approved successfully.');
    }

    /**
     * Reject the payment request with reason
     */
    public function reject(Request $request, PaymentRequest $paymentRequest)
    {
        if (!$paymentRequest->isPending()) {
            return back()->with('error', 'This payment request is no longer pending.');
        }

        $request->validate([
            'notes' => ['required', 'string', 'max:1000'],
        ]);

        $paymentRequest->update([
            'status' => 'rejected',
            'notes'  => $request->notes,
        ]);

        $paymentRequest->fuelRequest->update([
            'status' => 'payment_rejected',
        ]);

        return redirect()->route('admin.payment-requests.index')
            ->with('success', 'Payment rejected successfully.');
    }
}
