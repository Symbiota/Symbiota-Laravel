<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers {
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User {
        Validator::make($input, [
            'username' => ['required', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique(User::class),
            ],
            'password' => $this->passwordRules(),
        ])->validate();

        $firstName = null;
        $lastName = $input['name'];
        $name_parts = explode(' ', $input['name']);
        if(count($name_parts) > 1) {
            $firstName = trim($name_parts[0]);
            $lastName = trim($name_parts[1]);
        }

        return User::create([
            'username' => $input['username'],
            'name' => $input['name'],
            'firstName' => $firstName,
            'lastName' => $lastName,
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
        ]);
    }
}
