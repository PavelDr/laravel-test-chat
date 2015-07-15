<?php
namespace App\Services;

use App\User;

/**
 * Class RegistrationService
 * @package App\Services
 */
class RegistrationService {

    /**
     * Simple registration
     * @param $data
     * @return static
     */
    public function registration($data)
    {
        $user = User::create([
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => bcrypt($data['password'])
        ]);

        return $user;
    }
}