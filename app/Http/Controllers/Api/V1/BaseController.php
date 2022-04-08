<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Symfony\Component\HttpFoundation\Response;

class BaseController extends Controller
{
    /**
     * Constructor for base controller.
     */
    public function __construct()
    {
        $this->success  = true;     // Default success value response

        // Status code response
        $this->code['http_ok']              = Response::HTTP_OK;
        $this->code['http_created']         = Response::HTTP_CREATED;
        $this->code['http_unauthorized']    = Response::HTTP_UNAUTHORIZED;
        $this->code['http_not_found']       = Response::HTTP_NOT_FOUND;

        $this->data     = NULL;     // Default response data
        $this->message  = '';       // Default response message
    }

    /**
     * Sent firebase notification.
     *
     * @param  string  $title
     * @param  string  $body
     * @param  array   $deviceToken
     * @return \Illuminate\Http\Response
     */
    public function sendNotification($title, $body, $deviceToken)
    {
        $SERVER_API_KEY = env('FCM_SERVER_KEY');

        $data = [
            "registration_ids" => $deviceToken,
            "notification" => [
                "title" => $title,
                "body" => $body
            ]
        ];

        $dataString = json_encode($data);

        $headers = [
            'Authorization: key=' . $SERVER_API_KEY,
            'Content-Type: application/json',
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
        $response = curl_exec($ch);

        return $response;
    }
}
