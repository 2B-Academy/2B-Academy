<?php
namespace App\Services;
use App\Http\Traits\HelperTrait;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class NotificationsApiService
{
    use HelperTrait;
    public function __construct()
    {
        $this->client = new Client();
        $this->hr_base_url = env('HR_BASE_URL');
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
                ]
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
        }
    }

    /************************Handle Exception*************************/
    public function handleException(RequestException $e)
    {
        if ($e->hasResponse()) {
            return $e->getMessage();
        }
        return 'An error occurred while connecting to Third Party API.';
    }

    /************************ Employees Login Hr System *************************/
    public function getAccessToken($email, $password, $getUserDetails = false)
    {
        $data = [
            'email'    => $email,
            'password' => $password,
        ];
        $login = $this->thirdPartyIntegration('POST', $this->hr_base_url . 'Auth/login', $data);
        return ($getUserDetails) ? $login->data : $login->data->token ?? null;
    }


    /************************ Send Notifications to selected users *************************/
    public function sendNotificationsToSelectedUsers($title , $body , $titleEng, $bodyEng , $users)
    {
        $email = env('HR_ADMIN_EMAIL');
        $password = env('HR_ADMIN_PASSWORD');
        $token = $this->getAccessToken($email, $password);
        if (!$token) {
            return $this->errorResponse('التوكن غير صحيح');
        }
        $body = [
            'title' => $title,
            'body' => $body,
            'titleEng' => $titleEng,
            'bodyEng' => $bodyEng,
            'employeesCode' => $users
        ];
        $data = $this->thirdPartyIntegration('POST', $this->hr_base_url . 'Notification/NotifyEmployees', $body, $token);
        return $data->statusCode == 200;
    }


    /************************ Send Notifications to all users *************************/
    public function sendNotificationsToAllUsers($title , $body , $titleEng, $bodyEng)
    {
        $email = env('HR_ADMIN_EMAIL');
        $password = env('HR_ADMIN_PASSWORD');
        $token = $this->getAccessToken($email, $password);
        if (!$token) {
            return $this->errorResponse('التوكن غير صحيح');
        }
        $body = [
            'title' => $title,
            'body' => $body,
            'titleEng' => $titleEng,
            'bodyEng' => $bodyEng
        ];
        $data = $this->thirdPartyIntegration('POST', $this->hr_base_url . 'Notification/SendNotificationToAll', $body, $token);
        return $data->statusCode == 200;
    }




}
