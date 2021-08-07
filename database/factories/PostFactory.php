<?php

namespace Database\Factories;

use App\Models\{Role, Post, User};
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Enums\UserTypes;

class PostFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Post::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title' => $this->faker->sentence,
            'content' => $this->faker->paragraph,
            'slug' => $this->faker->slug,
            'created_by' => User::factory([
                'role_id' => Role::factory([
                    'name' => UserTypes::getUserTypesArray()[mt_rand(0, count(UserTypes::getUserTypesArray())-1)]
                ])->create()->id,
            ])->create()->id,
        ];
    }
}
