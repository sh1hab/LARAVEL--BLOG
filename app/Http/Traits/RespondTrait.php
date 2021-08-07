<?php

namespace App\Http\Traits;

use Illuminate\Http\JsonResponse;

trait RespondTrait
{
    /**
     * @var int
     */
    protected $statusCode = 200;

    /**
     * @param string $message
     * @param string $errors
     * @return \Illuminate\Http\JsonResponse
     */
    public function respondNotValidated($message = '', $errors = '')
    {
        $message = $message === '' ? __('app.status.not_validated') : $message;
        return $this->setStatusCode(400)->respondWithError($message, $errors);
    }

    /**
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    public function respondNotAuthorized($message = '', $errors = '')
    {
        $message = $message === '' ? __('app.status.not_authorized') : $message;
        return $this->setStatusCode(401)->respondWithError($message, $errors);
    }

    /**
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    public function respondNotFound($message = '', $errors = '')
    {
        $message = $message === '' ? __('app.status.not_found') : $message;
        return $this->setStatusCode(200)->respondWithError($message, $errors);
    }

    /**
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    public function respondInternalError($message = '', $errors = '')
    {
        $message = $message === '' ? __('app.status.internal_error') : $message;
        return $this->setStatusCode(500)->respondWithError($message, $errors);
    }

    /**
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    public function respondForbidden($message = '', $errors = '')
    {
        $message = $message === '' ? __('app.status.forbidden') : $message;
        return $this->setStatusCode(403)->respondWithError($message, $errors);
    }

    /**
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    public function respondBadGateway($message = '', $errors = '')
    {
        $message = $message === '' ? __('app.status.bad_gateway') : $message;
        return $this->setStatusCode(502)->respondWithError($message, $errors);
    }

    /**
     * @param $message
     * @param $errors
     * @return JsonResponse
     */
    private function respondWithError($message, $errors = '')
    {
        return $this->respond([
            'success' => false,
            'message' => $message,
            'errors' => $errors
        ]);
    }

    /**
     * @param $data
     * @param array $headers
     * @return JsonResponse
     */
    private function respond($data, $headers = [])
    {
        return response()->json($data, $this->getStatusCode(), $headers);
    }

    /**
     * @return mixed
     */
    private function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * @param mixed $statusCode
     * @return $this
     */
    private function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;

        return $this;
    }
}
