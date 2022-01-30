<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Sala;

class SalaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */

    protected $model=Sala::class;



    public function definition()
    {
        return [
        'nume'=>'',
        ];
    }
}
