<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Guest;
use App\Models\ServiceRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class MobileCheckInController extends Controller
{
    /**
     * Show the mobile check-in portal
     */
    public function show($token)
    {
        $booking = Booking::where('checkin_token', $token)
            ->where('checkin_token_expires_at', '>', now())
            ->first();

        if (!$booking) {
            return view('errors.custom', [
                'message' => 'This check-in link has expired or is invalid. Please ask the receptionist for a new one.',
                'title' => 'Link Expired'
            ]);
        }

        if ($booking->check_in_status === 'checked_in') {
            return view('errors.custom', [
                'message' => 'You are already checked in. Enjoy your stay!',
                'title' => 'Already Checked In'
            ]);
        }

        return view('checkin.mobile', compact('booking'));
    }

    /**
     * Submit the mobile check-in data
     */
    public function submit(Request $request, $token)
    {
        $booking = Booking::where('checkin_token', $token)
            ->where('checkin_token_expires_at', '>', now())
            ->first();

        if (!$booking) {
            return response()->json(['success' => false, 'message' => 'Invalid or expired token.'], 403);
        }

        $request->validate([
            'id_document_type' => 'nullable|string',
            'id_document_number' => 'nullable|string',
            'id_scan_front' => 'nullable|string', // base64
            'id_scan_back' => 'nullable|string', // base64
            'guest_signature' => 'required|string', // base64
        ]);

        try {
            $updateData = [
                'id_document_type' => $request->id_document_type,
                'id_document_number' => $request->id_document_number,
                'mobile_checkin_submitted_at' => now(),
                'identity_captured_at' => now(),
                'checkin_token' => null, // Consume the token
            ];

            // Save Front ID Scan
            $idScanPath = $this->saveBase64Image($request->id_scan_front, 'scans', 'mob_id_front_' . $booking->id);
            if ($idScanPath) {
                $updateData['id_scan_path'] = $idScanPath;
            }

            // Save Back ID Scan
            if ($request->filled('id_scan_back')) {
                $idScanBackPath = $this->saveBase64Image($request->id_scan_back, 'scans', 'mob_id_back_' . $booking->id);
                if ($idScanBackPath) {
                    $updateData['id_scan_back_path'] = $idScanBackPath;
                }
            }

            // Save Signature
            $sigPath = $this->saveBase64Image($request->guest_signature, 'signatures', 'mob_sig_' . $booking->id);
            if ($sigPath) {
                $updateData['guest_signature_path'] = $sigPath;
            }

            // Update Guest profile if exists
            if ($booking->guest_id) {
                Guest::where('id', $booking->guest_id)->update([
                    'passport_number' => $request->id_document_number
                ]);
            }

            $booking->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'Internal records submitted to reception for review. Please proceed to the counter.'
            ]);

        } catch (\Exception $e) {
            Log::error('Mobile check-in error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'An error occurred during check-in.'], 500);
        }
    }

    /**
     * Show the mobile check-out portal
     */
    public function showCheckout($token)
    {
        $booking = Booking::where('checkout_token', $token)
            ->where('checkout_token_expires_at', '>', now())
            ->first();

        if (!$booking) {
            return view('errors.custom', [
                'message' => 'This check-out link has expired or is invalid.',
                'title' => 'Link Expired'
            ]);
        }

        // Calculate Bill Details
        $bill = $this->calculateBill($booking);

        return view('checkin.mobile_checkout', compact('booking', 'bill'));
    }

    /**
     * Submit the mobile check-out data
     */
    public function submitCheckout(Request $request, $token)
    {
        $booking = Booking::where('checkout_token', $token)
            ->where('checkout_token_expires_at', '>', now())
            ->first();

        if (!$booking) {
            return response()->json(['success' => false, 'message' => 'Invalid or expired token.'], 403);
        }

        $request->validate([
            'checkout_signature' => 'required|string', // base64
        ]);

        try {
            $updateData = [
                'mobile_checkin_submitted_at' => now(), // Generic "guest submitted" flag
                'checkout_token' => null, // Consume the token
            ];

            // Save Checkout Signature
            $sigPath = $this->saveBase64Image($request->checkout_signature, 'signatures', 'checkout_sig_' . $booking->id);
            if ($sigPath) {
                $updateData['checkout_signature_path'] = $sigPath;
            }

            $booking->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'Signature received. Please inform the receptionist to finalize your check-out.'
            ]);

        } catch (\Exception $e) {
            Log::error('Mobile check-out submission error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'An error occurred while saving your signature.'], 500);
        }
    }

    /**
     * Calculate comprehensive bill for mobile portal
     */
    private function calculateBill(Booking $booking)
    {
        $nights = $booking->check_in->diffInDays($booking->check_out);
        
        // Use locked exchange rate or fetch current
        $exchangeRate = $booking->locked_exchange_rate;
        if (!$exchangeRate || $exchangeRate <= 0) {
            try {
                $currencyService = new \App\Services\CurrencyExchangeService();
                $exchangeRate = $currencyService->getUsdToTshRate();
            } catch (\Exception $e) {
                $exchangeRate = 2500;
                Log::warning('Exchange rate service failed in mobile checkout, using default 2500');
            }
        }

        // 1. Calculate Room Charge
        $roomChargeUsd = $booking->total_price; // USD
        $roomChargeTsh = $roomChargeUsd * $exchangeRate;
        
        // 2. Fetch Services
        $serviceRequests = ServiceRequest::where('booking_id', $booking->id)
            ->whereIn('status', ['completed', 'delivered', 'approved'])
            ->get();
            
        $servicesTotalTsh = $serviceRequests->sum('total_price_tsh');
        $servicesTotalUsd = $servicesTotalTsh / $exchangeRate;
        
        // 3. Determine Responsibility
        $isCorporate = $booking->is_corporate_booking ?? false;
        $paymentResponsibility = $booking->payment_responsibility ?? 'self'; // 'self' or 'company'
        
        // For Corporate bookings, company pays room charge unless specified otherwise (rare)
        $companyPaysRoom = $isCorporate;
        $companyPaysServices = ($paymentResponsibility === 'company');
        
        // 4. Calculate Total Bill
        $totalBillTsh = $roomChargeTsh + $servicesTotalTsh;
        
        // 5. Calculate Paid Amount (for display to guest)
        // We start with what they actually paid
        $amountPaidTsh = ($booking->amount_paid ?? 0) * $exchangeRate;
        
        // Add individual service payments already marked as paid
        foreach ($serviceRequests as $sr) {
            if ($sr->payment_status === 'paid') {
                $amountPaidTsh += $sr->total_price_tsh;
            }
        }

        // 6. Treat Company Responsibility as "Paid" from the Guest's perspective
        if ($companyPaysRoom) {
            $amountPaidTsh += $roomChargeTsh;
        }
        if ($companyPaysServices) {
            // Add unpaid service charges to "paid" because they aren't the guest's debt
            $unpaidServicesTsh = $serviceRequests->where('payment_status', '!=', 'paid')->sum('total_price_tsh');
            $amountPaidTsh += $unpaidServicesTsh;
        }
        
        // 7. Final Balance Calculation
        $balanceTsh = max(0, $totalBillTsh - $amountPaidTsh);
        
        // Threshold (less than 50 TZS or $0.05 is treated as paid)
        if ($balanceTsh < 50 || $booking->payment_status === 'paid') {
            $balanceTsh = 0;
        }

        $totalAmountUsd = $totalBillTsh / $exchangeRate;
        // Adjust display "PaidAmount" to show what has been covered (by guest or company)
        $paidAmountUsd = $amountPaidTsh / $exchangeRate;
        $balanceUsd = $balanceTsh / $exchangeRate;

        return [
            'nights' => $nights,
            'roomCharge' => $roomChargeUsd,
            'roomChargeTsh' => $roomChargeTsh,
            'services' => $serviceRequests,
            'servicesTotal' => $servicesTotalUsd,
            'servicesTotalTsh' => $servicesTotalTsh,
            'totalAmount' => $totalAmountUsd,
            'totalAmountTsh' => $totalBillTsh,
            'paidAmount' => $paidAmountUsd,
            'paidAmountTsh' => $amountPaidTsh,
            'balance' => $balanceUsd,
            'balanceTsh' => $balanceTsh,
            'exchangeRate' => $exchangeRate
        ];
    }

    /**
     * Check if a booking has been checked in/out (for polling)
     */
    public function checkStatus($id)
    {
        $booking = Booking::find($id);
        if (!$booking) return response()->json(['success' => false], 404);

        return response()->json([
            'success' => true,
            'status' => $booking->check_in_status,
            'is_submitted' => !empty($booking->mobile_checkin_submitted_at),
            'is_checkout_submitted' => !empty($booking->checkout_signature_path),
            'signature_path' => $booking->checkout_signature_path ? asset($booking->checkout_signature_path) : ($booking->guest_signature_path ? asset($booking->guest_signature_path) : null),
            'checkout_signature_path' => $booking->checkout_signature_path ? asset($booking->checkout_signature_path) : null,
            'is_checked_in' => $booking->check_in_status === 'checked_in',
            'is_checked_out' => $booking->check_in_status === 'checked_out'
        ]);
    }

    /**
     * Clear the mobile signature (reject-and-retry)
     */
    public function clearSignature($id)
    {
        $booking = Booking::find($id);
        if (!$booking) return response()->json(['success' => false], 404);

        $booking->update([
            'mobile_checkin_submitted_at' => null,
            'checkout_signature_path' => null
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Signature cleared. Guest can now re-submit.'
        ]);
    }

    /**
     * Helper to save base64 image
     */
    private function saveBase64Image($base64String, $folder, $filenamePrefix)
    {
        if (!$base64String || !str_contains($base64String, ';base64,')) {
            return null;
        }

        try {
            $format = explode(';', explode(':', $base64String)[1])[0];
            $extension = str_contains($format, 'png') ? 'png' : (str_contains($format, 'jpeg') || str_contains($format, 'jpg') ? 'jpg' : 'png');
            $data = explode(',', $base64String)[1];
            $data = base64_decode($data);

            $filename = $filenamePrefix . '_' . time() . '.' . $extension;

            // Use the 'public' disk explicitly so files land in storage/app/public/
            // (accessible via the storage symlink at public/storage/)
            Storage::disk('public')->put($folder . '/' . $filename, $data);

            return 'storage/' . $folder . '/' . $filename;
        } catch (\Exception $e) {
            Log::error('Failed to save mobile base64 image: ' . $e->getMessage());
            return null;
        }
    }
}
