<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="charset=utf-8" />
        <title>TODO</title>
        <link rel="stylesheet" href="{{ asset('css/app.css') }}">
        <style>
            body { font-family: DejaVu Sans, sans-serif; }
        </style>
    </head>
    <body>
        <h1 class="text-2xl text-center">{{ $title }}</h1>
        <p>{{ $date }}</p>
        @if($description)
            <p>Описание: {{ $description }}</p>
        @endif

        <div
            class="min-w-full"
        >
            <h2 class="text-2xl tracking-wider text-center">
                Не выполненные
            </h2>
            <hr class="my-2" />
            @foreach($tasksNotCompleted as $task)
                <div class="py-2 px-3 flex">
                    <div class="flex-1 pr-1">
                        <h3 class="text-lg font-semibold">
                            {{ $task->title }}
                        </h3>
                        <p>
                            {{ $task->description }}
                        </p>
                    </div>
                </div>
                <p class="text-xs text-right pr-1">
                    @if($task->user)
                        @if($task->user->id === $userId)
                            Создал(-а):
                            <strong>Вы</strong>
                        @else
                            Создал(-а): {{ $task->user->name }}
                        @endif
                    @else
                        Создано:
                    @endif
                    {{ date('d.m.Y', strtotime($task->dateTime)) }}
                </p>
                <hr class="my-2" />
            @endforeach
            <h2 class="text-2xl tracking-wider text-center">
                Выполненные
            </h2>
            <hr class="my-2" />
            @foreach($tasksCompleted as $task)
                <div class="py-2 flex px-3">
                    <div class="flex-1 pr-1">
                        <h3 class="text-lg font-semibold">
                            {{ $task->title }}
                        </h3>
                        <p>
                            {{ $task->description }}
                        </p>
                    </div>
                </div>
                <p class="text-xs text-right pr-1">
                    @if($task->completedUser)
                        @if($task->completedUser->id === $userId)
                            Выполнил(-а):
                            <strong>Вы</strong>
                        @else
                            Выполнил(-а): {{ $task->completedUser->name }}
                        @endif
                    @else
                        Выполнено:
                    @endif
                    {{ date('d.m.Y', strtotime($task->dateTime)) }}
                </p>
                <hr class="my-2" />
            @endforeach
    </body>
</html>
