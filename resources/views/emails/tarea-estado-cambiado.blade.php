@component('mail::message')
{{-- this is the h1 of markdown --}}
{{-- {{ $task->titulo }} With this I enter the task properties--}}
# La tarea {{ $task->titulo }} ha cambiado de estado

{{-- ** in Markdown makes it bold --}}
El estado actual es **{{ $task->estado }}**

{{-- route('tasks.show', $task->id) with this I am going to look at the specific task that was modified and generate a URL for a specific route --}}
{{-- route allows you to enter a specific route --}}
@component('mail::button', ['url' => route('tasks.show', $task->id)])
Saber más sobre la tarea modificada
@endcomponent

Gracias<br>
{{ config('app.name') }}
@endcomponent
