<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\ConsultantProfile;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ConsultantProfile>
 */
class ConsultantProfileFactory extends Factory
{
    protected $model = ConsultantProfile::class;

    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'user_id' => User::factory(),
            'specialty' => fake()->randomElement([
                'General Consultation',
                'Therapy',
                'Nutrition',
            ]),
            'is_active' => true,
        ];
    }
}