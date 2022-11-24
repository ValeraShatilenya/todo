<?php

namespace App\Http\Controllers;

use PDF;

class PDFController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public static function generateTasksPDF($tasksNotCompleted, $tasksCompleted, int $userId, $description = null)
    {
        $date = date('d.m.Y');

        $pdf = PDF::loadView('pdf/tasks', [
            'title' => 'Добро пожаловать в PDF программы TODO!',
            'date' => "Дата формирования: $date",
            'tasksNotCompleted' => $tasksNotCompleted,
            'tasksCompleted' => $tasksCompleted,
            'userId' => $userId,
            'description' => $description
        ]);

        return $pdf;
    }
}
