<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selecione uma tabela</title>
    <!-- Link para o CSS do Tailwind -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.0.2/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <x-app-layout>
        <x-slot name="header">
            <h1 class="font-semibold text-xl text-gray-800 leading-tight">
                Selecione uma tabela
            </h1>
        </x-slot>
    
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                    @if(session('success'))
                <div class="bg-green-200 border-l-4 border-green-600 text-green-800 p-4 mb-4" role="alert">
                    <p class="font-bold">Sucesso!</p>
                    <p>{{ session('success') }}</p>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-200 border-l-4 border-red-600 text-red-800 p-4 mb-4" role="alert">
                    <p class="font-bold">Erro!</p>
                    <p>{{ session('error') }}</p>
                </div>
            @endif

                        <!-- Card para Upload CSV -->
                        <div class="mb-4 p-4 shadow rounded-lg border border-gray-200 bg-gray-50">
                            <h2 class="text-lg mb-2 text-gray-700 font-bold">Upload de CSV</h2>
                            <p>
                                <a href="{{ route('upload.csv') }}" class="text-gray-600 hover:text-gray-800 transition-colors duration-200">Fazer upload de um arquivo CSV</a>
                            </p>
                        </div>

                        <!-- Lista de tabelas -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                            @foreach ($tables as $table)
                            <div class="p-4 shadow rounded-lg border border-gray-200 bg-gray-50">
                                <h2 class="text-lg text-gray-700 font-bold">Tabela: {{ $table->table_name }}</h2>
                                <p>
                                    <a href="{{ route('show.table', $table->table_name) }}" class="text-gray-600 hover:text-gray-800 transition-colors duration-200">Acessar {{ $table->table_name }}</a>
                                </p>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </x-app-layout>
</body>
</html>
