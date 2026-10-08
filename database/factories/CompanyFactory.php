<?php

namespace Database\Factories;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Company>
 */
class CompanyFactory extends Factory
{
    protected $model = Company::class;

    public function definition(): array
    {
        $companyName = fake()->unique()->company();

        return [
            'name' => $companyName,
            'slug' => Str::slug($companyName) . '-' . fake()->unique()->numberBetween(100, 9999),
            'email' => fake()->unique()->companyEmail(),
            'status' => 'active',
        ];
    }
}