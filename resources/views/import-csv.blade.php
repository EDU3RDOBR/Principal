{{-- Em resources/views/import-csv.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Resultado da Importação CSV') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3>Importação Concluída!</h3>
                    <p>Aqui você pode exibir os detalhes da importação, como o número de registros importados, erros encontrados, etc.</p>
                    <a href="{{ route('home') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Voltar</a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
