<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * Register a new user
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone_number' => 'required|string|max:20|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:tenant,landlord',
            'verification_method' => 'required|in:email,phone',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone_number' => $validated['phone_number'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'verification_method' => $validated['verification_method'],
        ]);

        // Generate OTP if phone verification chosen
        if ($validated['verification_method'] === 'phone') {
            $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            $user->update([
                'otp_code' => Hash::make($otp),
                'otp_expires_at' => now()->addMinutes(10),
            ]);

            // In production, send OTP via SMS (e.g., using Twilio)
            // For now, we'll store it in session for demo
            session(['pending_otp_user' => $user->id, 'demo_otp' => $otp]);

            return redirect('/verify-phone')->with([
                'success' => "OTP sent to {$user->phone_number}. Code for demo: {$otp}",
                'phone' => $user->phone_number,
            ]);
        }

        // Email verification
        return redirect('/verify-email')->with([
            'success' => 'A verification link has been sent to your email.',
            'email' => $user->email,
        ]);
    }

    /**
     * Verify email address
     */
    public function verifyEmail(Request $request)
    {
        if ($request->isMethod('post')) {
            $validated = $request->validate([
                'email' => 'required|email|exists:users',
                'code' => 'required|string|min:6',
            ]);

            // In production, verify the code against stored token
            // For demo, we'll accept any 6-character code
            if (strlen($validated['code']) >= 6) {
                $user = User::where('email', $validated['email'])->first();
                if ($user) {
                    $user->update([
                        'email_verified_at' => now(),
                    ]);

                    Auth::login($user);
                    return redirect($user->role === 'landlord' ? '/dashboard/landlord' : '/dashboard/tenant')
                        ->with('success', 'Email verified and logged in successfully!');
                }
            }

            return back()->withErrors(['code' => 'Invalid verification code.']);
        }

        return view('auth.verify-email');
    }

    /**
     * Verify phone with OTP
     */
    public function verifyPhone(Request $request)
    {
        if ($request->isMethod('post')) {
            $validated = $request->validate([
                'otp' => 'required|string|min:6|max:6',
            ]);

            $userId = session('pending_otp_user');
            if (!$userId) {
                return redirect('/register')->withErrors(['error' => 'Session expired. Please register again.']);
            }

            $user = User::find($userId);
            if (!$user) {
                return redirect('/register')->withErrors(['error' => 'User not found.']);
            }

            // Check if OTP is still valid
            if ($user->otp_expires_at < now()) {
                return back()->withErrors(['otp' => 'OTP has expired. Please register again.']);
            }

            // Verify OTP
            if (Hash::check($validated['otp'], $user->otp_code)) {
                $user->update([
                    'phone_verified_at' => now(),
                    'otp_code' => null,
                    'otp_expires_at' => null,
                ]);

                Auth::login($user);
                return redirect($user->role === 'landlord' ? '/dashboard/landlord' : '/dashboard/tenant')
                    ->with('success', 'Phone verified and logged in successfully!');
            }

            return back()->withErrors(['otp' => 'Invalid OTP. Please try again.']);
        }

        $phone = session('pending_phone', 'your phone');
        return view('auth.verify-phone', ['phone' => $phone]);
    }

    /**
     * Resend OTP
     */
    public function resendOtp()
    {
        $userId = session('pending_otp_user');
        if (!$userId) {
            return redirect('/register');
        }

        $user = User::find($userId);
        if (!$user) {
            return redirect('/register');
        }

        // Prevent spamming - check if OTP was sent less than 1 minute ago
        if ($user->otp_expires_at && $user->otp_expires_at->diffInSeconds(now()) > 540) {
            return back()->withErrors(['error' => 'Please wait before requesting a new OTP.']);
        }

        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $user->update([
            'otp_code' => Hash::make($otp),
            'otp_expires_at' => now()->addMinutes(10),
        ]);

        session(['demo_otp' => $otp]);

        return back()->with('success', "New OTP sent. Code for demo: {$otp}");
    }

    /**
     * Request password reset
     */
    public function forgotPassword(Request $request)
    {
        if ($request->isMethod('post')) {
            $validated = $request->validate([
                'email' => 'required|email|exists:users',
            ]);

            $user = User::where('email', $validated['email'])->first();
            $token = Str::random(64);

            // Store reset token
            DB::table('password_reset_tokens')->where('email', $user->email)->delete();
            DB::table('password_reset_tokens')->insert([
                'email' => $user->email,
                'token' => Hash::make($token),
                'created_at' => now(),
            ]);

            // In production, send email with reset link
            // For demo, we'll just return the token
            return redirect('/password-reset-confirm')->with([
                'success' => 'Password reset link sent to your email.',
                'email' => $user->email,
                'demo_token' => $token, // Only for demo purposes
            ]);
        }

        return view('auth.forgot-password');
    }

    /**
     * Reset password
     */
    public function resetPassword(Request $request)
    {
        if ($request->isMethod('post')) {
            $validated = $request->validate([
                'email' => 'required|email|exists:users',
                'token' => 'required|string',
                'password' => 'required|string|min:8|confirmed',
            ]);

            // Verify token
            $record = DB::table('password_reset_tokens')
                ->where('email', $validated['email'])
                ->first();

            if (!$record || !Hash::check($validated['token'], $record->token)) {
                return back()->withErrors(['token' => 'Invalid or expired password reset token.']);
            }

            // Check if token is older than 1 hour
            if ($record->created_at < now()->subHour()) {
                return back()->withErrors(['token' => 'Password reset link has expired.']);
            }

            // Update password
            $user = User::where('email', $validated['email'])->first();
            $user->update(['password' => Hash::make($validated['password'])]);

            // Delete token
            DB::table('password_reset_tokens')->where('email', $validated['email'])->delete();

            return redirect('/login')->with('success', 'Password reset successfully. Please login with your new password.');
        }

        $token = request('token');
        $email = request('email');

        return view('auth.reset-password', ['token' => $token, 'email' => $email]);
    }

    /**
     * Show password reset confirmation page
     */
    public function showResetConfirm()
    {
        return view('auth.password-reset-confirm');
    }

    /**
     * Login user
     */
    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
            'remember' => 'nullable|boolean',
        ]);

        if (Auth::attempt(['email' => $validated['email'], 'password' => $validated['password']], $validated['remember'] ?? false)) {
            $request->session()->regenerate();

            $user = auth()->user();

            // Check if verified
            if ($user->verification_method === 'email' && !$user->email_verified_at) {
                Auth::logout();
                return redirect('/verify-email')->with('warning', 'Please verify your email first.');
            }

            if ($user->verification_method === 'phone' && !$user->phone_verified_at) {
                Auth::logout();
                return redirect('/verify-phone')->with('warning', 'Please verify your phone first.');
            }

            $redirectPath = $user->role === 'landlord' ? '/dashboard/landlord' : '/dashboard/tenant';
            return redirect($redirectPath)->with('success', 'Logged in successfully!');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    /**
     * Logout user
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Logged out successfully!');
    }
}
