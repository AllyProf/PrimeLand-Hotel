<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    protected string $baseUrl   = 'https://messaging-service.co.tz';
    protected string $token;
    protected string $senderId;

    public function __construct()
    {
        $this->token    = config('services.sms.token',    env('SMS_TOKEN',     '1a60690cac7c9db9f1ba5d68ee88c2d4'));
        $this->senderId = config('services.sms.sender_id', env('SMS_SENDER_ID', 'PrimeLand'));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Public API
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Send a single SMS to one recipient.
     *
     * @param  string  $to       Phone number starting with 255 (e.g. 255677155156)
     * @param  string  $message  Text message content
     * @return array{success: bool, message: string, data: mixed}
     */
    public function sendSingle(string $to, string $message): array
    {
        $to = $this->formatPhone($to);

        try {
            $response = Http::withToken($this->token)
                ->acceptJson()
                ->asJson()
                ->post("{$this->baseUrl}/api/sms/v2/text/single", [
                    'from'    => $this->senderId,
                    'to'      => $to,
                    'text'    => $message,
                ]);

            return $this->handleResponse($response, "to={$to}");

        } catch (\Throwable $e) {
            Log::error('[SmsService] HTTP error sending SMS', [
                'to'      => $to,
                'error'   => $e->getMessage(),
            ]);
            return ['success' => false, 'message' => 'SMS request failed: ' . $e->getMessage(), 'data' => null];
        }
    }

    /**
     * Send SMS to multiple recipients.
     *
     * @param  array<string>  $recipients  Array of phone numbers starting with 255
     * @param  string         $message     Text message content
     * @return array
     */
    public function sendMultiple(array $recipients, string $message): array
    {
        $messages = array_map(fn ($to) => [
            'from' => $this->senderId,
            'to'   => $this->formatPhone($to),
            'text' => $message,
        ], $recipients);

        try {
            $response = Http::withToken($this->token)
                ->acceptJson()
                ->asJson()
                ->post("{$this->baseUrl}/api/sms/v2/text/multi", [
                    'messages' => $messages,
                ]);

            return $this->handleResponse($response, 'bulk send (' . count($recipients) . ' recipients)');

        } catch (\Throwable $e) {
            Log::error('[SmsService] HTTP error sending bulk SMS', [
                'count' => count($recipients),
                'error' => $e->getMessage(),
            ]);
            return ['success' => false, 'message' => 'SMS request failed: ' . $e->getMessage(), 'data' => null];
        }
    }

    /**
     * Check remaining SMS balance.
     *
     * @return array{success: bool, balance: string|null, data: mixed}
     */
    public function getBalance(): array
    {
        try {
            $response = Http::withToken($this->token)
                ->acceptJson()
                ->get("{$this->baseUrl}/api/v2/balance");

            if ($response->successful()) {
                $data = $response->json();
                $balanceStr = $data['display'] ?? ($data['balance'] ?? '0');
                
                // Extract numeric value from "248.00 TSH"
                $numericBalance = (float) preg_replace('/[^0-9.]/', '', $balanceStr);
                $smsPrice = 16.0;
                $smsCount = floor($numericBalance / $smsPrice);

                return [
                    'success' => true,
                    'balance' => $balanceStr,
                    'sms_count' => $smsCount,
                    'data'    => $data,
                ];
            }

            return ['success' => false, 'balance' => null, 'sms_count' => 0, 'data' => $response->json()];

        } catch (\Throwable $e) {
            Log::error('[SmsService] Failed to get SMS balance: ' . $e->getMessage());
            return ['success' => false, 'balance' => null, 'sms_count' => 0, 'data' => null];
        }
    }

    /**
     * Get sent SMS logs from the API.
     *
     * @param array $params Optional filters (from, to, sentSince, sentUntil, limit, offset)
     * @return array
     */
    public function getLogs(array $params = []): array
    {
        try {
            $response = Http::withToken($this->token)
                ->acceptJson()
                ->get("{$this->baseUrl}/api/sms/v1/logs", $params);



            if ($response->successful()) {
                return [
                    'success' => true,
                    'logs'    => $response->json()['results'] ?? [],
                    'data'    => $response->json(),
                ];
            }

            return ['success' => false, 'logs' => [], 'data' => $response->json()];

        } catch (\Throwable $e) {
            Log::error('[SmsService] Failed to get SMS logs: ' . $e->getMessage());
            return ['success' => false, 'logs' => [], 'data' => null];
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Convenience helpers for common hotel notifications
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Send booking confirmation SMS to guest.
     */
    public function sendBookingConfirmation(string $phone, string $guestName, string $ref, string $checkIn, string $checkOut, string $roomNumber): array
    {
        $message = "Dear {$guestName}, your booking at PrimeLand Hotel is CONFIRMED!\n"
            . "Ref: {$ref}\n"
            . "Room: {$roomNumber}\n"
            . "Check-in: {$checkIn} | Check-out: {$checkOut}\n"
            . "For assistance call: 0677-155-156";

        return $this->sendSingle($phone, $message);
    }

    /**
     * Send check-in confirmation SMS to guest.
     */
    public function sendCheckInConfirmation(string $phone, string $guestName, string $roomNumber, string $checkOut): array
    {
        $message = "Welcome to PrimeLand Hotel, {$guestName}!\n"
            . "You have successfully checked in to Room {$roomNumber}.\n"
            . "Check-out: {$checkOut}\n"
            . "Enjoy your stay! Call 0677-155-156 for any assistance.";

        return $this->sendSingle($phone, $message);
    }

    /**
     * Send check-out confirmation SMS to guest.
     */
    public function sendCheckOutConfirmation(string $phone, string $guestName): array
    {
        $message = "Dear {$guestName}, thank you for staying at PrimeLand Hotel!\n"
            . "We hope you had a great experience. We look forward to hosting you again.\n"
            . "- PrimeLand Hotel Team";

        return $this->sendSingle($phone, $message);
    }

    /**
     * Send payment receipt SMS to guest.
     */
    public function sendPaymentConfirmation(string $phone, string $guestName, string $ref, string $amount): array
    {
        $message = "Dear {$guestName}, payment of {$amount} received for booking #{$ref} at PrimeLand Hotel. "
            . "Thank you! Call 0677-155-156 for support.";

        return $this->sendSingle($phone, $message);
    }

    /**
     * Send swimming service confirmation SMS to guest.
     */
    public function sendSwimmingConfirmation(string $phone, string $guestName, string $ref, string $amount): array
    {
        $message = "Dear {$guestName}, your swimming pool access at PrimeLand Hotel is CONFIRMED!\n"
            . "Ref: {$ref}\n"
            . "Amount Paid: {$amount} TZS\n"
            . "Thank you for choosing PrimeLand Hotel!";

        return $this->sendSingle($phone, $message);
    }

    /**
     * Send ceremony service confirmation SMS to guest.
     */
    public function sendCeremonyConfirmation(string $phone, string $guestName, string $ref, string $type): array
    {
        $message = "Dear {$guestName}, your {$type} booking at PrimeLand Hotel is CONFIRMED!\n"
            . "Ref: {$ref}\n"
            . "Thank you for choosing PrimeLand Hotel! Call 0677-155-156 for assistance.";

        return $this->sendSingle($phone, $message);
    }

    /**
     * Send purchase request notification SMS to manager.
     */
    public function sendPurchaseRequestNotification(string $phone, string $managerName, string $requesterName, string $department, int $itemCount, string $priority): array
    {
        $priorityLabel = strtoupper($priority);
        $itemWord = $itemCount === 1 ? 'item' : 'items';
        $message = "Dear {$managerName}, a new purchase request has been submitted.\n"
            . "From: {$requesterName} ({$department} Dept)\n"
            . "Items: {$itemCount} {$itemWord} | Priority: {$priorityLabel}\n"
            . "Please review at your management dashboard.";

        return $this->sendSingle($phone, $message);
    }

    /**
     * Notify a staff member that their purchase request was approved.
     */
    public function sendPurchaseApprovedNotification(string $phone, string $staffName, string $itemName, string $managerName): array
    {
        $message = "Hi {$staffName}, good news!\n"
            . "Your purchase request for \"{$itemName}\" has been APPROVED by {$managerName}.\n"
            . "The item will be added to the shopping list. - Management";

        return $this->sendSingle($phone, $message);
    }

    /**
     * Notify a staff member that their purchase request was rejected.
     */
    public function sendPurchaseRejectedNotification(string $phone, string $staffName, string $itemName, string $managerName, string $reason): array
    {
        $message = "Hi {$staffName}, your purchase request for \"{$itemName}\" has been DECLINED by {$managerName}.\n"
            . "Reason: {$reason}\n"
            . "Contact management if you have questions.";

        return $this->sendSingle($phone, $message);
    }

    /**
     * Notify a staff member that their purchase request was edited by the manager.
     */
    public function sendPurchaseEditedNotification(string $phone, string $staffName, string $itemName, string $managerName, array $changes): array
    {
        $changesText = '';
        foreach ($changes as $change) {
            $changesText .= "\n- {$change['field']}: {$change['old']} → {$change['new']}";
        }

        $message = "Hi {$staffName}, your purchase request for \"{$itemName}\" was updated by {$managerName}."
            . ($changesText ? "\nChanges:{$changesText}" : '')
            . "\nCheck your dashboard for details.";

        return $this->sendSingle($phone, $message);
    }

    /**
     * Send shopping list finalization notification to owner.
     */
    public function sendShoppingListFinalizedNotification(string $phone, string $ownerName, string $listName, float $totalCost, int $itemCount): array
    {
        $costFormatted = number_format($totalCost, 0);
        $message = "Dear {$ownerName}, the shopping list \"{$listName}\" has been FINALIZED.\n"
            . "Total Actual Cost: TSh {$costFormatted}\n"
            . "Items Purchased: {$itemCount}\n"
            . "Check your dashboard for the detailed report.\n"
            . "- PrimeLand Hotel System";

        return $this->sendSingle($phone, $message);
    }

    /**
     * Notify department staff that items have been transferred to their department.
     */
    public function sendDepartmentTransferNotification(string $phone, string $staffName, string $department, int $itemCount): array
    {
        $itemWord = $itemCount === 1 ? 'item' : 'items';
        $message = "Hi {$staffName}, {$itemCount} new {$itemWord} have been transferred to the {$department} department.\n"
            . "Please log in to your dashboard to receive them and update your inventory.\n"
            . "- PrimeLand Hotel Management";

        return $this->sendSingle($phone, $message);
    }

    /**
     * Send OTP SMS.
     */
    public function sendOtp(string $phone, string $otp): array
    {
        $message = "Your PrimeLand Hotel login OTP is: {$otp}. Valid for 10 minutes. Do not share this code.";
        return $this->sendSingle($phone, $message);
    }

    /**
     * Notify manager/owner that a reception staff has started a shift.
     */
    public function sendShiftOpenedNotification(string $phone, string $recipientName, string $staffName, string $openedAt, float $openingCash): array
    {
        $cashFormatted = number_format($openingCash, 0);
        $message = "SHIFT ALERT - PrimeLand Hotel\n"
            . "Hi {$recipientName}, {$staffName} (Reception) has just opened a new shift.\n"
            . "Time: {$openedAt}\n"
            . "Opening Cash: TZS {$cashFormatted}\n"
            . "- Hotel System";

        return $this->sendSingle($phone, $message);
    }

    /**
     * Notify about low stock level.
     */
    public function sendLowStockAlert(string $phone, string $staffName, string $itemName, float $currentStock, float $minStock, string $department): array
    {
        $message = "⚠️ LOW STOCK ALERT: {$itemName} in {$department} department is now at {$currentStock}. "
            . "Minimum level set is {$minStock}. Please restock soon!\n"
            . "- PrimeLand Hotel System";

        return $this->sendSingle($phone, $message);
    }

    /**
     * Notify housekeepers that a guest has checked in.
     */
    public function notifyHousekeeperCheckIn(string $phone, string $housekeeperName, string $roomNumber, string $guestName): array
    {
        $message = "Hi {$housekeeperName}, FYI: Guest {$guestName} has just checked in to Room {$roomNumber}.\n"
            . "- Reception";

        return $this->sendSingle($phone, $message);
    }

    /**
     * Notify housekeepers that a guest has checked out and the room needs cleaning.
     */
    public function notifyHousekeeperCheckout(string $phone, string $housekeeperName, string $roomNumber): array
    {
        $message = "Hi {$housekeeperName}, Room {$roomNumber} has been CHECKED OUT. Please proceed with cleaning as soon as possible.\n"
            . "- Reception";

        return $this->sendSingle($phone, $message);
    }


    /**
     * Notify reception of a stay extension/decrease request.
     */
    public function sendExtensionRequestSms(string $phone, string $guestName, string $roomNumber, string $newDate, string $type = 'extension'): array
    {
        $subject = $type === 'decrease' ? 'STAY DECREASE' : 'STAY EXTENSION';
        $message = "🏨 {$subject} REQUEST\n"
            . "Guest: {$guestName}\n"
            . "Room: {$roomNumber}\n"
            . "Requested Date: {$newDate}\n"
            . "Please review in your dashboard.";
        
        return $this->sendSingle($phone, $message);
    }

    /**
     * Notify reception of a new issue report.
     */
    public function sendIssueReportSms(string $phone, string $guestName, string $roomNumber, string $subject, string $priority): array
    {
        $priorityLabel = strtoupper($priority);
        $message = "⚠️ NEW ISSUE REPORTED\n"
            . "Room: {$roomNumber} ({$guestName})\n"
            . "Issue: {$subject}\n"
            . "Priority: {$priorityLabel}\n"
            . "Action required in dashboard.";

        return $this->sendSingle($phone, $message);
    }

    /**
     * Notify guest that their extension/decrease request was approved or rejected.
     */
    public function sendExtensionStatusSms(string $phone, string $guestName, string $newDate, string $status = 'approved'): array
    {
        if ($status === 'approved') {
            $message = "Dear {$guestName}, your request has been APPROVED. Your new checkout date is " . $newDate . ". Enjoy your stay at PrimeLand Hotel!";
        } else {
            $message = "Dear {$guestName}, your extension request could not be approved at this time. Please contact reception at 601 for more details.";
        }
        
        return $this->sendSingle($phone, $message);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Private helpers
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Normalize phone number to 255xxxxxxxxx format.
     */
    private function formatPhone(string $phone): string
    {
        // Strip all non-digits
        $phone = preg_replace('/\D/', '', $phone);

        // Handle 07xxxxxxxx → 2557xxxxxxxx
        if (strlen($phone) === 10 && str_starts_with($phone, '0')) {
            $phone = '255' . substr($phone, 1);
        }

        // Handle +255xxxxxxxxx → 255xxxxxxxxx (already done by stripping +)
        // Handle 255xxxxxxxxx → keep as-is
        return $phone;
    }

    /**
     * Parse and log API response.
     */
    private function handleResponse($response, string $context): array
    {
        $status = $response->status();
        $data   = $response->json();

        if ($response->successful()) {
            Log::info("[SmsService] SMS sent successfully ({$context})", ['status' => $status, 'response' => $data]);
            return ['success' => true, 'message' => 'SMS sent successfully.', 'data' => $data];
        }

        Log::warning("[SmsService] SMS failed ({$context})", ['status' => $status, 'response' => $data]);
        $errorMsg = $data['requestError']['serviceException']['text']
            ?? $data['message']
            ?? "API returned HTTP {$status}";

        return ['success' => false, 'message' => $errorMsg, 'data' => $data];
    }
}
