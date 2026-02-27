<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\Contracts\TwoFactorAuthenticationProvider;

class TwoFactorController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        return view('profile.two-factor', [
            'enabled' => !is_null($user->two_factor_secret) && !is_null($user->two_factor_confirmed_at),
            'qrCode' => $user->twoFactorQrCodeSvg(),
            'recoveryCodes' => $user->two_factor_recovery_codes ?
                json_decode(decrypt($user->two_factor_recovery_codes)) : []
        ]);
    }

    public function enable(Request $request, TwoFactorAuthenticationProvider $provider)
    {
        $user = $request->user();

        $user->forceFill([
            'two_factor_secret' => encrypt($provider->generateSecretKey()),
            'two_factor_confirmed_at' => null,
        ])->save();

        return redirect()->route('profile.two-factor')->with('show_qr', true);
    }

    public function confirm(Request $request, TwoFactorAuthenticationProvider $provider)
    {
        $request->validate([
            'code' => 'required|string|size:6',
        ]);

        $user = $request->user();

        if ($provider->verify(decrypt($user->two_factor_secret), $request->code)) {
            // Gerar códigos de recuperação
            $codes = [];
            for ($i = 0; $i < 8; $i++) {
                $codes[] = strtoupper(substr(md5(uniqid()), 0, 10));
            }

            $user->forceFill([
                'two_factor_confirmed_at' => now(),
                'two_factor_recovery_codes' => encrypt(json_encode($codes)),
            ])->save();

            return redirect()->route('profile.two-factor')->with('success', '2FA ativado com sucesso!');
        }

        return redirect()->route('profile.two-factor')->with('error', 'Código inválido.')->with('show_qr', true);
    }

    public function disable(Request $request)
    {
        $user = $request->user();

        $user->forceFill([
            'two_factor_secret' => null,
            'two_factor_confirmed_at' => null,
            'two_factor_recovery_codes' => null,
        ])->save();

        return redirect()->route('profile.two-factor')->with('success', '2FA desativado com sucesso!');
    }
}
