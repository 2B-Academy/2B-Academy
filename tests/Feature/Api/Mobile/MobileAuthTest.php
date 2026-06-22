<?php

namespace Tests\Feature\Api\Mobile;

/**
 * Cross-cutting auth gate for every mobile endpoint:
 *   mobile.token   → 401 when the shared token is missing/wrong
 *   mobile.employee→ 422 when Employee-Code missing, 404 when unknown
 *
 * Uses GET /mobile/me as the representative protected route.
 */
class MobileAuthTest extends MobileTestCase
{
    private function url(): string
    {
        return self::BASE . '/mobile/me';
    }

    public function test_missing_shared_token_returns_401(): void
    {
        $user = $this->employee();

        $response = $this->withHeaders([
            'Employee-Code' => $user->machine_code,
            'Accept'        => 'application/json',
        ])->getJson($this->url());

        $this->assertError($response, 401);
    }

    public function test_wrong_shared_token_returns_401(): void
    {
        $user = $this->employee();

        $response = $this->withHeaders([
            'X-Api-Token'   => 'not-the-real-token',
            'Employee-Code' => $user->machine_code,
            'Accept'        => 'application/json',
        ])->getJson($this->url());

        $this->assertError($response, 401);
    }

    public function test_bearer_header_is_accepted_as_shared_token(): void
    {
        $user = $this->employee();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . self::TOKEN,
            'Employee-Code' => $user->machine_code,
            'Accept'        => 'application/json',
        ])->getJson($this->url());

        $this->assertSuccess($response);
    }

    public function test_missing_employee_code_returns_422(): void
    {
        $response = $this->withHeaders($this->tokenOnlyHeaders())->getJson($this->url());

        $this->assertError($response, 422);
    }

    public function test_unknown_employee_code_returns_404(): void
    {
        $response = $this->withHeaders([
            'X-Api-Token'   => self::TOKEN,
            'Employee-Code' => 'DOES-NOT-EXIST',
            'Accept'        => 'application/json',
        ])->getJson($this->url());

        $this->assertError($response, 404);
    }

    public function test_me_returns_learner_identity(): void
    {
        $user = $this->employee(['name' => 'Mona Learner']);

        $response = $this->withHeaders($this->headersFor($user))->getJson($this->url());

        $this->assertSuccess($response);
        $response->assertJsonPath('result.machine_code', $user->machine_code)
                 ->assertJsonPath('result.name', 'Mona Learner')
                 ->assertJsonStructure(['result' => ['id', 'machine_code', 'name', 'department_name', 'learner_type']]);
    }
}
