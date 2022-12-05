<?php

namespace Anfragen\TwoFactor\Tests\Factories;

use Anfragen\TwoFactor\Tests\Models\User;
use Illuminate\Support\{Carbon, Str};
use Orchestra\Testbench\Factories\UserFactory as Orchestra;

class UserFactory extends Orchestra
{
    /**
     * Specifies the model that this factory represents.
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name'              => $this->faker->name(),
            'email'             => $this->faker->unique()->safeEmail(),
            'email_verified_at' => Carbon::now(),
            'password'          => bcrypt('password'),
            'remember_token'    => Str::random(10),
        ];
    }
}
