<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class SuperAdminController extends Controller
{

    // ===================================================
    // Show Super Admin Login Form
    // ===================================================

    // Show login form
    public function showLoginForm()
    {
        // Calculate remaining lockout time
        $lockoutRemaining = null;
        if (session()->has('super_admin_lockout_until')) {
            $diff = now()->diff(session('super_admin_lockout_until'));
            if (now()->lessThan(session('super_admin_lockout_until'))) {
                $lockoutRemaining = $diff;
            } else {
                session()->forget('super_admin_lockout_until');
                session()->forget('super_admin_attempts');
            }
        }

        return view('superadmin.login', compact('lockoutRemaining'));
    }
   

    // ===================================================
    // Process Super Admin Login
    // ===================================================
    public function login(Request $request)
    {
        // Validate input fields
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'secret_key' => 'required'
        ]);

        $ip = $request->ip();

        // Check if locked out
        if (session()->has('super_admin_lockout_until') &&
            now()->lessThan(session('super_admin_lockout_until'))) {

            return back()->withErrors([
                'Too many failed attempts. Try again later.'
            ]);
        }

        // Check credentials against .env
        $valid =
            $request->email === env('SUPER_ADMIN_EMAIL') &&
            $request->password === env('SUPER_ADMIN_PASSWORD') &&
            $request->secret_key === env('SUPER_ADMIN_SECRET');

        if (!$valid) {

            // Increase failed attempts counter
            $attempts = session('super_admin_attempts', 0) + 1;
            session(['super_admin_attempts' => $attempts]);

            // Log failed attempt to file
            Log::warning('Super Admin failed login attempt', [
                'ip' => $ip,
                'email_attempted' => $request->email,
                'time' => now()
            ]);

            // Lock if max attempts reached
            if ($attempts >= env('SUPER_ADMIN_MAX_ATTEMPTS')) {

                session([
                    'super_admin_lockout_until' =>
                        now()->addMinutes((int) env('SUPER_ADMIN_LOCKOUT_MINUTES'))
                ]);

                session()->forget('super_admin_attempts');

                return back()->withErrors([
                    'Too many failed attempts. Locked temporarily.'
                ]);
            }

            return back()->withErrors(['Invalid credentials.']);
        }

        // Successful login → clear attempts
        session()->forget([
            'super_admin_attempts',
            'super_admin_lockout_until'
        ]);

        // Start secure session
        session([
            'super_admin' => true,
            'super_admin_expires_at' =>
                now()->addMinutes((int) env('SUPER_ADMIN_SESSION_MINUTES'))
        ]);

        // Log successful login
        Log::info('Super Admin logged in successfully', [
            'ip' => $ip,
            'time' => now()
        ]);

        return redirect()->route('superadmin.recovery');
    }

    // ===================================================
    // Show Recovery Form
    // ===================================================
    public function showRecoveryForm()
    {
        return view('superadmin.recovery');
    }

    // ===================================================
    // Process Admin Recreation
    // ===================================================
    public function processRecovery(Request $request)
    {
        // Validate input
        $request->validate([
            'new_email' => 'required|email',
            'new_password' => 'required|min:6'
        ]);

        // Find existing admin (role = 1)
        $admin = User::where('role', 1)->first();

        if ($admin) {

            // Update existing admin
            $admin->update([
                'name' => 'Admin',
                'user_name' => 'Admin',
                'email' => $request->new_email,
                'password' => Hash::make($request->new_password),
            ]);

        } else {

            // Create admin if missing
            User::create([
                'name' => 'Admin',
                'user_name' => 'Admin',
                'role' => 1,
                'email' => $request->new_email,
                'password' => Hash::make($request->new_password),
            ]);
        }

        // Log recovery action
        Log::info('Super Admin recreated Admin account', [
            'ip' => $request->ip(),
            'new_admin_email' => $request->new_email,
            'time' => now()
        ]);

        return back()->with('success',
            'Admin account recreated successfully.');
    }
}



