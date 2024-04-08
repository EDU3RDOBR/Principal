<!DOCTYPE html>
<html>
<head>
    <title>Selecione uma tabela</title>
</head>
<body>
    <x-app-layout>
        <x-slot name="header">
            <h1 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Selecione uma tabela') }}
            </h1>
        </x-slot>
    
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <ul>
                            <li><a href="{{ route('upload.csv') }}" class="underline text-blue-500 hover:text-blue-700">Fazer upload de um arquivo CSV</a></li>
                            @foreach ($tables as $table)
                                <li><a href="{{ route('show.table', $table->table_name) }}">{{ $table->table_name }}</a></li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </x-app-layout>
</body>
</html>
