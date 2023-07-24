<?php

namespace App\Services;
use App\Models\Origami_Model;

class TaskService
{

    private Origami_Model $origami_model;

    public function __construct() {
        $this->origami_model = new Origami_Model();
    }
    /**
     * @param object{ title: string, description: string, date: string } $task
     */
    public function logTask($task)
    {
        $form_data = [
            'group_data_name' => 'log_task_details',
            'log_task_details' => [
                'log_task_details_title'        => $task->title,
                'log_task_details_description'  => $task->description,
                'log_task_details_date'         => $task->date,
                'log_task_details_created_by'   => 'Raz Aharon'
            ]
        ];
        return $this->origami_model->create('log', $form_data);
    }
}