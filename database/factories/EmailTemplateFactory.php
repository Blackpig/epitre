<?php

namespace BlackpigCreatif\Epitre\Database\Factories;

use BlackpigCreatif\Epitre\Models\EmailTemplate;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<EmailTemplate>
 */
class EmailTemplateFactory extends Factory
{
    protected $model = EmailTemplate::class;

    public function definition(): array
    {
        return [
            'key' => fake()->unique()->slug(2),
            'subject' => ['en' => fake()->sentence()],
            'body' => ['en' => '<p>' . fake()->paragraph() . '</p>'],
        ];
    }
}
