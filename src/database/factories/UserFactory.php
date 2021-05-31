<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $cpf_cnpj  = ['63372967020','69850570000177'];
        $user_type = ['customer', 'shopkeeper'];
        $index = rand(0, 1);

        return [
            'name'       => $this->faker->name,
            'email'      => $this->faker->unique()->safeEmail,
            'password'   => '$2y$10$ivDMjJDuJ9y4.fbehXP34.CTb9MK0HASwhR/pyrohtS.zej.qZGSy', // 123123
            'identifier' => $cpf_cnpj[$index],
            'user_type'  => $user_type[$index],
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];
    }
}
