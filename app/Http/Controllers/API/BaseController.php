<?php

namespace App\Http\Controllers\API;

class BaseController
{
    /**
     * success response method.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendResponse($result = [], $message = 'Success')
    {
        $response = [
            'success' => true,
            'message' => $message,
            'data' => $result
        ];
        if (empty($result)) {
            unset($response['data']);
        }

        return response()->json($response, 200);
    }

    /**
     * return error response.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendError($error, $errorMessages = [], $code = 404)
    {
        $response = [
            'success' => false,
            'message' => $error,
            'data' => $errorMessages
        ];

        if (empty($errorMessages)) {
            unset($response['data']);
        }

        return response()->json($response, $code);
    }

    /**
     * return error response.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendValidationError($errorMessages = [])
    {
        $response = [
            'success' => false,
            'message' => 'Validation Error.',
            'data' => $errorMessages
        ];

        if (empty($errorMessages)) {
            unset($response['data']);
        }

        return response()->json($response, 422);
    }
}
