<?php
namespace App\Livewire\Auth;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

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
}
