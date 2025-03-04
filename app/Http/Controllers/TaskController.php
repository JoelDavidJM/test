<?php

namespace App\Http\Controllers;

use App\Mail\TareaEstadoCambiado;
use App\Models\Task;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class TaskController extends Controller
{
    // all tasks are displayed
    public function index(Request $request)
    {
        try {
            // The customer's response is first evaluated
            $validator = Validator::make($request->all(), [
                // The in is a validation rule in Laravel that verifies that the field value matches one of the specific values ​​listed
                'estado' => 'nullable|in:pendiente,completada,en_progreso',
                'sort_by' => 'nullable|in:titulo,fecha_vencimiento',
                'order' => 'nullable|in:asc,desc'
            ]);
            if($validator->fails()) {
                return response()->json(
                    [
                        'errors' => $validator->errors()
                    ],
                    Response::HTTP_BAD_REQUEST
                );
            };
            //A state was created for each of the validators
            $estado = $request->query('estado');
            $sortBy = $request->query('sort_by', 'fecha_vencimiento');
            $order = $request->query('order', 'asc');
            //It is used to obtain the values ​​in this case of task
            // with is to bring you users with the related task
            // Task::query() create a new query
            $query = Task::query()->with('user');;
            // A condition is made to see if the state has information
            if($estado){
                $query->where('estado', $estado);
            }
            //order of values
            $query->orderBy($sortBy, $order);
            // paginate is used to have 10 tasks per page
            $tasks = $query->paginate(10);
            if ($tasks->isEmpty()) {
                return response()->json(
                    ['message' => 'there is no record'],
                    Response::HTTP_NOT_FOUND
                );
            }
            return response()->json($tasks, Response::HTTP_OK);// HTTP 200
        } catch (Exception $e) {
            // will return the response transformed into json
            return response()->json(
                [   // $e->getMessage() provides the detail of the error that occurred
                    'message' => 'error: ' . $e->getMessage()
                ],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    // a new task is created
    // Request is used to represent the HTTP request that the client sends to the server
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // required It means that it is mandatory
            'titulo' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'estado' => 'nullable|string',
            'fecha_vencimiento' => 'nullable|date',
            // exists:users,id here it is said that the user table must exist and must match the id
            'user_id' => 'required|exists:users,id'
        ]);
        if($validator->fails()) {
            return response()->json(
                [
                    'errors' => $validator->errors()
                ], 400
            );
        }
        try {
            $task = Task::create($request->only('titulo', 'descripcion', 'estado', 'fecha_vencimiento', 'user_id'));
            return response()->json($task, Response::HTTP_CREATED);//HTTP 201
        } catch (Exception $e) {
            return response()->json(
                [
                    'message' => 'error ' . $e->getMessage()
                ],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    // here the task is brought by its id
    public function show(Task $task)
    {
        try {
            $task->load('user');
            return response()->json($task, Response::HTTP_OK);//HTTP 200
        } catch (Exception $e) {
            return response()->json(
                [
                    'message' => 'error ' . $e->getMessage()
                ],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    // the values ​​are modified by the id
    public function update(Request $request, Task $task)
    {
        $validator = Validator::make($request->all(), [
            'titulo' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'estado' => 'nullable|string',
            'fecha_vencimiento' => 'nullable|date',
            'user_id' => 'required|exists:users,id'
        ]);
        if($validator->fails()) {
            return response()->json(
                [
                    'An error occurred while updating the task ' => $validator->errors()
                ], 400
            );
        }
        try {
            // the task is obtained before being modified
            $estadoSinActualizar = $task->estado;
            // update update the task with the data provided
            $task->update($request->only('titulo', 'descripcion', 'estado', 'fecha_vencimiento', 'user_id'));
            if($estadoSinActualizar !== $task->estado) {
                Mail::to('jodajativa17@gmail.com')->send(new TareaEstadoCambiado($task));
            }
            return response()->json($task, Response::HTTP_OK);//HTTP 200
        } catch (Exception $e) {
            return response()->json(
                [
                    'message' => 'error ' . $e->getMessage()
                ],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    // This function is designed to delete a resource
    public function destroy(Task $task)
    {
        try {
            // delete delete the selected task from the database
            $task->delete();
            return response()->json(
                [
                    'message' => 'Task deleted successfully'
                ],
                Response::HTTP_OK
            );
        } catch (Exception $e) {
            return response()->json(
                [
                    'message' => 'error ' . $e->getMessage()
                ],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
