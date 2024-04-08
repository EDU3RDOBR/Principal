<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Import CSV') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form action="{{ route('upload.csv.process') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div>
                            <label for="csv_file">Selecione o arquivo CSV:</label>
                            <input type="file" id="csv_file" name="csv_file" accept=".csv" required>
                        </div>
                        <br>
                        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Importar CSV
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
