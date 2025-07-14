<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class LoginForm extends Component
{
    public $email = '';

    public $password = '';

    public $remember = false;

    public function login()
    {
        $this->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            session()->regenerate();

            return redirect()->intended('/dashboard');
        }

        $this->addError('email', 'Email atau password salah.');
    }

    public function render()
    {
        return view('livewire.auth.login-form');
    }

    public function handleUserRedirect($user)
    {
        if ($user->hasRole('fakultas')) {
            return redirect()->route('fakultas.prodi');
        }

        if ($user->hasRole('prodi')) {
            // Pastikan Anda sudah membuat rute bernama 'prodi.dashboard'
            return redirect()->route('prodi.dashboard');
        }
        if ($user->hasRole('mahasiswa')) {
            return redirect()->route('mahasiswa.dashboard');
        }

        // Pengalihan default untuk user lain
        return redirect('/dashboard');
    }
}
