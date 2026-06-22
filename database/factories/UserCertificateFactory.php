<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\User;
use App\Models\UserCertificate;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<UserCertificate>
 *
 * A first-class, active certificate. Certificate numbers are unique per
 * year; tests that need the canonical sequential format should issue via
 * CertificateService instead — the factory only guarantees uniqueness.
 */
class UserCertificateFactory extends Factory
{
    protected $model = UserCertificate::class;

    public function definition(): array
    {
        $year = now()->year;

        return [
            'uuid'               => (string) Str::uuid(),
            'user_id'            => User::factory(),
            'course_id'          => Course::factory(),
            'source_type'        => UserCertificate::SOURCE_EXAM,
            'source_id'          => $this->faker->numberBetween(1, 100000),
            'certificate_number' => sprintf('CERT-%d-%06d', $year, $this->faker->unique()->numberBetween(1, 999999)),
            'status'             => UserCertificate::STATUS_ACTIVE,
            'issued_at'          => now(),
            'metadata'           => [],
        ];
    }

    public function revoked(): static
    {
        return $this->state(fn () => [
            'status'     => UserCertificate::STATUS_REVOKED,
            'revoked_at' => now(),
        ]);
    }

    public function fromEvaluation(): static
    {
        return $this->state(fn () => ['source_type' => UserCertificate::SOURCE_EVALUATION]);
    }
}
