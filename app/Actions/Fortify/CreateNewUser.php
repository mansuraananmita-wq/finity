<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * @param  array<string, string>  $input
     *
     * @throws ValidationException
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique(User::class)],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => $this->passwordRules(),
            'preferred_language' => ['nullable', Rule::in(['en', 'bn'])],
        ])->validate();

        $user = User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'phone' => $input['phone'] ?? null,
            'password' => $input['password'], // hashed via User cast
            'preferred_language' => $input['preferred_language'] ?? 'en',
            'role' => 'customer',
        ]);

        // Until real SMTP is configured, auto-verify so login/checkout aren't blocked locally.
        $mailer = config('mail.default');
        if (in_array($mailer, ['log', 'array'], true)) {
            $user->forceFill(['email_verified_at' => now()])->save();
        }

        return $user;
    }
}
