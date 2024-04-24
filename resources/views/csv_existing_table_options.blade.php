{{-- Em resources/views/csv_existing_table_options.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Opções para Tabela Existente') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    @if(session('error'))
                        <div class="bg-red-200 border-l-4 border-red-600 text-red-800 p-4 mb-4" role="alert">
                            <p class="font-bold">Erro!</p>
                            <p>{{ session('error') }}</p>
                        </div>
                    @endif

                    <h1 class="text-lg font-bold mb-4">{{ __('A tabela \'' . $tableName . '\' já existe no sistema.') }}</h1>
                    <p>Selecione uma das seguintes opções para proceder:</p>

                    <div class="mt-4">
                        <form action="{{ route('import') }}" method="POST" class="inline-block">
                            @csrf
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Adicionar Dados à Tabela Existente
                            </button>
                        </form>
                        <a href="{{ route('cancel-import2') }}" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded inline-block ml-2">
                            Cancelar Importação
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
