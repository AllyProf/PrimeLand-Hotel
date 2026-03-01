<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Staff;
use App\Models\Guest;
use App\Mail\PasswordResetMail;
use App\Models\ActivityLog;
use App\Models\SystemLog;

class PasswordResetController extends Controller
{
    /**
     * Handle forgot password request - Step 1: Send OTP
     */
    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $email = $request->email;
        $user = null;
        $userType = null;

        // Check if email exists in Staff table
        $staff = Staff::where('email', $email)->first();
        if ($staff) {
            // Check if staff is active
            if (!$staff->is_active) {
                return back()->withErrors([
                    'email' => 'Your account has been deactivated. Please contact administrator.'
                ])->withInput($request->only('email'));
            }
            $user = $staff;
            $userType = 'staff';
        } else {
            // Check if email exists in Guest table
            $guest = Guest::where('email', $email)->first();
            if ($guest) {
                // Check if guest is active
                if (!$guest->is_active) {
                    return back()->withErrors([
                        'email' => 'Your account has been deactivated. Please contact administrator.'
                    ])->withInput($request->only('email'));
                }
                $user = $guest;
                $userType = 'guest';
            }
        }

        // Check if email exists
        if (!$user) {
            // Log the attempt for security monitoring
            Log::warning('Password reset requested for non-existent email', [
                'email' => $email,
                'ip_address' => $request->ip(),
            ]);
            
            return back()->withErrors([
                'email' => 'This email address does not exist in our system.'
            ])->withInput($request->only('email'))->with('show_forgot_password', true);
        }

        // Generate 6-digit OTP
        $otp = sprintf("%06d", mt_rand(1, 999999));
        $expiresAt = now()->addMinutes(10);

        // Store OTP in login_otps table
        DB::table('login_otps')->insert([
            'email' => $email,
            'otp' => $otp,
            'user_type' => $userType,
            'user_id' => $user->id,
            'ip_address' => $request->ip(),
            'expires_at' => $expiresAt,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Send OTP via Email
        try {
            Mail::raw("Your PrimeLand Hotel password reset OTP is: {$otp}. It will expire in 10 minutes.", function ($message) use ($email) {
                $message->to($email)->subject('Password Reset OTP - PrimeLand Hotel');
            });
            
            \Log::info('Password reset OTP email sent', [
                'user_id' => $user->id,
                'user_type' => $userType,
                'email' => $email,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send OTP email', [
                'error' => $e->getMessage(),
                'email' => $email,
            ]);
        }

        // Send OTP via SMS
        if ($user->phone) {
            try {
                $smsService = new \App\Services\SmsService();
                $smsMessage = "Your PrimeLand Hotel password reset OTP is: {$otp}. Valid for 10 minutes.";
                $smsService->sendSingle($user->phone, $smsMessage);
                \Log::info('Password reset OTP SMS sent', ['phone' => $user->phone]);
            } catch (\Exception $e) {
                \Log::error('Failed to send OTP SMS: ' . $e->getMessage());
            }
        }

        // Log activity
        ActivityLog::create([
            'user_id' => $user->id,
            'user_type' => $userType === 'staff' ? Staff::class : Guest::class,
            'action' => 'password_reset_otp_requested',
            'description' => "Password reset OTP requested: {$user->name} ({$email})",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return back()->with('success', 'A 6-digit OTP has been sent to your email and phone number.')
                     ->with('email_for_otp', $email)
                     ->with('show_otp_form', true);
    }

    /**
     * Resend OTP
     */
    public function resendOtp(Request $request)
    {
        $email = $request->email;
        if (!$email) {
            return back()->withErrors(['email' => 'Session expired. Please start again.'])->with('show_forgot_password', true);
        }

        // We can just call forgotPassword logic but with a different message
        // For simplicity, let's just trigger a redirect to the existing logic
        return $this->forgotPassword($request);
    }

    /**
     * Verify OTP - Step 2
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|string|size:6',
        ]);

        $otpRecord = DB::table('login_otps')
            ->where('email', $request->email)
            ->where('otp', $request->otp)
            ->where('used', false)
            ->where('expires_at', '>', now())
            ->first();

        if (!$otpRecord) {
            return back()->withErrors(['otp' => 'Invalid or expired OTP.'])
                         ->with('email_for_otp', $request->email)
                         ->with('show_otp_form', true);
        }

        // Mark OTP as used
        DB::table('login_otps')->where('id', $otpRecord->id)->update(['used' => true]);

        // Put a temporary token in session to allow password reset
        $resetToken = \Illuminate\Support\Str::random(60);
        session(['password_reset_token' => $resetToken, 'password_reset_email' => $request->email]);

        return back()->with('success', 'OTP verified successfully. Please set your new password.')
                     ->with('reset_token', $resetToken)
                     ->with('password_reset_email', $request->email)
                     ->with('show_reset_form', true);
    }

    /**
     * Reset Password Content - Step 3
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed',
            'token' => 'required|string',
        ]);

        if (session('password_reset_token') !== $request->token || session('password_reset_email') !== $request->email) {
            return redirect()->route('login')->withErrors(['email' => 'Session expired or invalid. Please try again.'])->with('show_forgot_password', true);
        }

        $email = $request->email;
        $user = Staff::where('email', $email)->first() ?? Guest::where('email', $email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'User not found.'])->with('show_forgot_password', true);
        }

        // Update password
        $user->password = Hash::make($request->password);
        $user->save();

        // Clear session
        session()->forget(['password_reset_token', 'password_reset_email']);

        // Log activity
        ActivityLog::log('updated', $user, "Password updated via OTP reset for user: {$user->name} ({$email})");
        SystemLog::log('info', "Password reset successfully via OTP for user: {$user->name} ({$email})", 'security', [
            'user_id' => $user->id,
            'email' => $email,
        ]);

        return redirect()->route('login')->with('success', 'Password reset successfully. You can now login with your new password.');
    }

}

