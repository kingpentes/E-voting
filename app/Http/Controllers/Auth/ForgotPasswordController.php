<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Service\GmailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class ForgotPasswordController extends Controller
{
    /**
     * Show forgot password form
     */
    public function showForgotForm(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Send OTP to user's email
     */
    public function sendOTP(Request $request, GmailService $gmailService): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ], [
            'email.required' => 'Email harus diisi',
            'email.email' => 'Format email tidak valid',
            'email.exists' => 'Email tidak terdaftar dalam sistem',
        ]);

        $email = $request->email;

        // Generate 6 digit OTP
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Store OTP in database with 10 minutes expiry
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $email],
            [
                'token' => $otp,
                'created_at' => now(),
                'expires_at' => now()->addMinutes(10),
            ]
        );

        // Send OTP via Gmail
        $sent = $gmailService->sendOTP($email, $otp);

        if (!$sent) {
            return back()->with('error', 'Gagal mengirim OTP. Silakan coba lagi.');
        }

        // Redirect to verify OTP page
        return redirect()->route('password.verify.form')
            ->with('email', $email)
            ->with('success', 'Kode OTP telah dikirim ke email Anda. Silakan cek inbox Anda.');
    }

    /**
     * Show verify OTP form
     */
    public function showVerifyForm(): View|RedirectResponse
    {
        $email = session('email');
        
        if (!$email) {
            return redirect()->route('password.request')
                ->with('error', 'Sesi expired. Silakan request OTP kembali.');
        }

        return view('auth.verify-otp', compact('email'));
    }

    /**
     * Verify OTP code
     */
    public function verifyOTP(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
            'otp' => ['required', 'string', 'size:6'],
        ], [
            'otp.required' => 'Kode OTP harus diisi',
            'otp.size' => 'Kode OTP harus 6 digit',
        ]);

        $record = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$record) {
            return back()->with('error', 'Kode OTP tidak ditemukan. Silakan request OTP kembali.');
        }

        // Check if OTP expired
        if (now()->greaterThan($record->expires_at)) {
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            return back()->with('error', 'Kode OTP sudah kadaluarsa. Silakan request OTP kembali.');
        }

        // Verify OTP
        if ($record->token !== $request->otp) {
            return back()->with('error', 'Kode OTP salah. Silakan coba lagi.');
        }

        // OTP verified, redirect to reset password page
        return redirect()->route('password.reset.form')
            ->with('email', $request->email)
            ->with('otp_verified', true)
            ->with('success', 'Kode OTP berhasil diverifikasi. Silakan buat password baru.');
    }

    /**
     * Show reset password form
     */
    public function showResetForm(): View|RedirectResponse
    {
        $email = session('email');
        $otpVerified = session('otp_verified');

        if (!$email || !$otpVerified) {
            return redirect()->route('password.request')
                ->with('error', 'Silakan verifikasi OTP terlebih dahulu.');
        }

        return view('auth.reset-password', compact('email'));
    }

    /**
     * Reset password
     */
    public function resetPassword(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'password.required' => 'Password harus diisi',
            'password.min' => 'Password minimal 8 karakter',
            'password.confirmed' => 'Konfirmasi password tidak cocok',
        ]);

        // Check if there's a valid OTP record (recently verified)
        $record = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        // Verify that OTP exists and not expired (verified within last 15 minutes)
        if (!$record || now()->greaterThan($record->expires_at)) {
            return redirect()->route('password.request')
                ->with('error', 'Sesi verifikasi expired. Silakan request OTP kembali.');
        }

        // Update user password
        $user = User::where('email', $request->email)->first();
        $user->password = Hash::make($request->password);
        $user->save();

        // Delete OTP record
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        // Clear session
        $request->session()->forget(['email', 'otp_verified']);

        return redirect()->route('login')
            ->with('success', 'Password berhasil direset! Silakan login dengan password baru Anda.');
    }

    /**
     * Resend OTP
     */
    public function resendOTP(Request $request, GmailService $gmailService): RedirectResponse
    {
        $email = $request->input('email') ?? session('email');

        if (!$email) {
            return back()->with('error', 'Email tidak ditemukan.');
        }

        // Check if user exists
        $user = User::where('email', $email)->first();
        if (!$user) {
            return back()->with('error', 'Email tidak terdaftar dalam sistem.');
        }

        // Generate new OTP
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Update OTP in database
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $email],
            [
                'token' => $otp,
                'created_at' => now(),
                'expires_at' => now()->addMinutes(10),
            ]
        );

        // Send OTP via Gmail
        $sent = $gmailService->sendOTP($email, $otp);

        if (!$sent) {
            return back()->with('error', 'Gagal mengirim OTP. Silakan coba lagi.');
        }

        return back()->with('success', 'Kode OTP baru telah dikirim ke email Anda.');
    }
}
