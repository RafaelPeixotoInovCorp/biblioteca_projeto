<?php

namespace App\Livewire;

use Livewire\Component;
use Laravel\Fortify\Actions\DisableTwoFactorAuthentication;
use Laravel\Fortify\Actions\EnableTwoFactorAuthentication;
use Laravel\Fortify\Actions\ConfirmTwoFactorAuthentication;

class TwoFactorAuthenticationForm extends Component
{
    public $enabled = false;
    public $showingQrCode = false;
    public $showingConfirmation = false;
    public $showingRecoveryCodes = false;
    public $code = '';

    public function mount()
    {
        $user = auth()->user();
        $this->enabled = ! is_null($user->two_factor_secret);
        $this->showingQrCode = $this->enabled && is_null($user->two_factor_confirmed_at);
        $this->showingConfirmation = $this->showingQrCode;
    }

    public function enableTwoFactorAuthentication(EnableTwoFactorAuthentication $enable)
    {
        $enable(auth()->user());

        $this->enabled = true;
        $this->showingQrCode = true;
        $this->showingConfirmation = true;
    }

    public function confirmTwoFactorAuthentication(ConfirmTwoFactorAuthentication $confirm)
    {
        $confirm(auth()->user(), $this->code);

        $this->showingQrCode = false;
        $this->showingConfirmation = false;
        $this->showingRecoveryCodes = true;
    }

    public function disableTwoFactorAuthentication(DisableTwoFactorAuthentication $disable)
    {
        $disable(auth()->user());

        $this->enabled = false;
        $this->showingQrCode = false;
        $this->showingConfirmation = false;
        $this->showingRecoveryCodes = false;
    }

    public function showRecoveryCodes()
    {
        $this->showingRecoveryCodes = true;
    }

    public function render()
    {
        return view('livewire.two-factor-authentication-form');
    }
}
