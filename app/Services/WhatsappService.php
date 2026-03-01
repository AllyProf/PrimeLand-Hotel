<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsappService
{
    protected string $baseUrl;
    protected string $token;
    protected string $senderId;

    public function __construct()
    {
        $this->token    = config('services.whatsapp.token');
        $this->senderId = config('services.whatsapp.sender_id');
        $this->baseUrl  = config('services.whatsapp.base_url', 'https://messaging-service.co.tz');
    }

    /**
     * Send a WhatsApp message using a template.
     * 
     * @param string $to Recipient phone number
     * @param string $templateName Name of the template (letters only, 2-50 chars)
     * @param array $parameters Array of values for variables {{1}}, {{2}}, etc.
     * @return array
     */
    public function sendTemplate(string $to, string $templateName, array $parameters): array
    {
        $to = $this->formatPhone($to);

        try {
            // Note: The structure of the 'parameters' key may vary by API provider.
            // Some use 'body' as a list of strings, others use 'parameters' as a list of objects.
            $response = Http::withToken($this->token)
                ->acceptJson()
                ->asJson()
                ->post("{$this->baseUrl}/api/v1/whatsapp/template", [
                    'from'       => $this->senderId,
                    'to'         => $to,
                    'template'   => $templateName,
                    'parameters' => $parameters, 
                ]);

            return $this->handleResponse($response, "template={$templateName} to={$to}");

        } catch (\Throwable $e) {
            Log::error('[WhatsappService] HTTP error sending WhatsApp template', [
                'to'       => $to,
                'template' => $templateName,
                'error'    => $e->getMessage(),
            ]);
            return ['success' => false, 'message' => 'WhatsApp request failed: ' . $e->getMessage(), 'data' => null];
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Template Helpers
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * 1. bookingconfirmation
     * Variables: {{1}} = Guest Name, {{2}} = Booking Reference
     */
    public function sendBookingConfirmation(string $phone, string $guestName, string $ref): array
    {
        return $this->sendTemplate($phone, 'bookingconfirmation', [$guestName, $ref]);
    }

    /**
     * 2. corporatebooking
     * Variables: {{1}} = Company/Guest Name, {{2}} = Booking Reference
     */
    public function sendCorporateBooking(string $phone, string $name, string $ref): array
    {
        return $this->sendTemplate($phone, 'corporatebooking', [$name, $ref]);
    }

    /**
     * 3. welcomecheckin
     * Variables: {{1}} = Guest Name, {{2}} = Room Number
     */
    public function sendWelcomeCheckin(string $phone, string $guestName, string $roomNumber): array
    {
        return $this->sendTemplate($phone, 'welcomecheckin', [$guestName, $roomNumber]);
    }

    /**
     * 4. paymentreceipt
     * Variables: {{1}} = Guest Name, {{2}} = Amount, {{3}} = Reference
     */
    public function sendPaymentReceipt(string $phone, string $guestName, string $amount, string $ref): array
    {
        return $this->sendTemplate($phone, 'paymentreceipt', [$guestName, $amount, $ref]);
    }

    /**
     * 5. checkoutthanks
     * Variables: {{1}} = Guest Name
     */
    public function sendCheckoutThanks(string $phone, string $guestName): array
    {
        return $this->sendTemplate($phone, 'checkoutthanks', [$guestName]);
    }

    /**
     * 6. portalaccess
     * Variables: {{1}} = Guest Name, {{2}} = Portal URL/Ref
     */
    public function sendPortalAccess(string $phone, string $guestName, string $portalLink): array
    {
        return $this->sendTemplate($phone, 'portalaccess', [$guestName, $portalLink]);
    }

    /**
     * 7. prearrivalreg
     * Variables: {{1}} = Guest Name, {{2}} = Registration Link
     */
    public function sendPreArrivalReg(string $phone, string $guestName, string $regLink): array
    {
        return $this->sendTemplate($phone, 'prearrivalreg', [$guestName, $regLink]);
    }

    /**
     * 8. hotelmenu
     * Variables: {{1}} = Guest Name
     */
    public function sendHotelMenu(string $phone, string $guestName): array
    {
        return $this->sendTemplate($phone, 'hotelmenu', [$guestName]);
    }

    /**
     * 9. servicerequest
     * Variables: {{1}} = Guest Name, {{2}} = Service Details
     */
    public function sendServiceRequest(string $phone, string $guestName, string $details): array
    {
        return $this->sendTemplate($phone, 'servicerequest', [$guestName, $details]);
    }

    /**
     * 10. staffloginotp
     * Variables: {{1}} = OTP Code
     */
    public function sendStaffOtp(string $phone, string $otp): array
    {
        return $this->sendTemplate($phone, 'staffloginotp', [$otp]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Internal Helpers
    // ─────────────────────────────────────────────────────────────────────────

    private function formatPhone(string $phone): string
    {
        $phone = preg_replace('/\D/', '', $phone);
        if (strlen($phone) === 10 && str_starts_with($phone, '0')) {
            $phone = '255' . substr($phone, 1);
        }
        return $phone;
    }

    private function handleResponse($response, string $context): array
    {
        $status = $response->status();
        $data   = $response->json();

        if ($response->successful()) {
            Log::info("[WhatsappService] WhatsApp sent successfully ({$context})", ['status' => $status, 'response' => $data]);
            return ['success' => true, 'message' => 'WhatsApp sent successfully.', 'data' => $data];
        }

        Log::warning("[WhatsappService] WhatsApp failed ({$context})", ['status' => $status, 'response' => $data]);
        return ['success' => false, 'message' => "API returned HTTP {$status}", 'data' => $data];
    }
}
