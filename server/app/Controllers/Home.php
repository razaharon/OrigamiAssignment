<?php

namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;

class Home extends BaseController
{
    use ResponseTrait;
    public function index()
    {
        return $this->respond(['status' => 'OK'], 200);
    }

    public function task()
    {
        $task = json_decode($this->request->getBody());
        $isValid = $this->validateData((array)$task, [
            'title'         => 'required|string',
            'description'   => 'required|string',
            'date'          => 'required|valid_date'
        ]);
        if (!$isValid) {
            return $this->respond($this->validator->getErrors(), 400);
        }
        $service = service('task');
        $response = $service->logTask($task);
        return $this->respond($response, 200);
    }
}