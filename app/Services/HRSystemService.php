<?php
namespace App\Services;
use App\Http\Traits\HelperTrait;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;

class HRSystemService
{
    use HelperTrait;
    public function __construct()
    {
        $this->client = new Client();
        $this->hr_base_url = env('HR_BASE_URL');
        $this->verify_ssl  = filter_var(env('HR_VERIFY_SSL', true), FILTER_VALIDATE_BOOLEAN);
    }

    /************************ Main Integration *************************/
    public function thirdPartyIntegration($method, $url, $data = null, $token = null)
    {
        try {
            $options = [
                'json' => $data ?? (object)[],
                'headers' => [
                    'Accept'        => 'application/json',
                    'Content-Type'  => 'application/json',
                ],
                'verify'  => $this->verify_ssl,
                'timeout' => 20,
            ];
            if ($token) {
                $options['headers']['Authorization'] = 'Bearer ' . $token;
            }
            if ($data) {
                $options['json'] = $data;
            }
            $response = $this->client->request($method, $url, $options);
            return json_decode($response->getBody()->getContents(), false);
        } catch (RequestException $e) {
            return $this->handleException($e);
        } catch (\Throwable $e) {
            Log::error('HRSystemService request failed', [
                'url'   => $url,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /************************Handle Exception*************************/
    public function handleException(RequestException $e)
    {
        Log::error('HRSystemService RequestException', [
            'message' => $e->getMessage(),
            'body'    => $e->hasResponse() ? (string) $e->getResponse()->getBody() : null,
        ]);
        return null;
    }

    /************************ Employees Login Hr System *************************/
    /**
     * Track the last reason getAccessToken returned null. One of:
     *  - 'unreachable' : Could not contact the HR API (network/SSL/timeout).
     *  - 'invalid'     : HR API responded but rejected the credentials.
     *  - null          : No error (success).
     */
    public ?string $lastError = null;

    public function getAccessToken($email, $password, $getUserDetails = false)
    {
        $this->lastError = null;

        $data = [
            'email'    => $email,
            'password' => $password,
        ];
        $login = $this->thirdPartyIntegration('POST', $this->hr_base_url . 'Auth/login', $data);

        if ($login === null) {
            $this->lastError = 'unreachable';
            return null;
        }

        if (!is_object($login) || !isset($login->data)) {
            $this->lastError = 'invalid';
            return null;
        }

        return $getUserDetails ? $login->data : ($login->data->token ?? null);
    }


    /************************ Get All Employees From HR System *************************/
    public function getAllEmployees()
    {
        $email = env('HR_ADMIN_EMAIL');
        $password = env('HR_ADMIN_PASSWORD');
        $token = $this->getAccessToken($email, $password);
        if (!$token) {
            return $this->errorResponse('التوكن غير صحيح');
        }
        $data = $this->thirdPartyIntegration('POST', $this->hr_base_url . 'Employee/GetCurrentEmployees', null, $token);

        if (!is_object($data) || !isset($data->statusCode)) {
            return collect();
        }

        return ($data->statusCode == 200) ? collect($data->data ?? []) : collect();
    }

    /************************ Get All Jobs From HR System *************************/
    /**
     * Pulls the authoritative job catalogue from the HR system.
     *
     * The endpoint returns a flat list of job records shaped as
     * `{ id, name, notes, creationTime, lastModificationTime, … }`.
     * It does **not** carry an employee count; pair with
     * {@see self::getAllEmployees()} when filtering by "has employees".
     *
     * Hits the same `HR_BASE_URL` host as {@see self::getAllEmployees()}
     * so the job-name space is guaranteed to overlap with the
     * `jobName` field on every employee record — different HR hosts
     * carry different rosters, and mixing them silently drops cards
     * whose names don't match across systems.
     *
     * @return \Illuminate\Support\Collection<int, object>
     */
    public function getAllJobs()
    {
        $token = $this->getAccessToken(env('HR_ADMIN_EMAIL'), env('HR_ADMIN_PASSWORD'));
        if (! $token) {
            return collect();
        }

        $data = $this->thirdPartyIntegration('POST', $this->hr_base_url . 'Job', null, $token);

        if (! is_object($data) || ! isset($data->data) || ! is_array($data->data)) {
            return collect();
        }

        return collect($data->data);
    }
}
