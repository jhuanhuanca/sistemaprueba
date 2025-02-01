<x-filament::page>
    <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700 w-full">
        <table class="w-full table-fixed divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-100 dark:bg-gray-800">
                <tr>
                    <th scope="col" class="w-1/6 px-4 py-2 text-left text-sm font-semibold text-gray-900 dark:text-white border">
                        codigo
                    </th>
                    <th scope="col" class="w-1/6 px-4 py-2 text-left text-sm font-semibold text-gray-900 dark:text-white border">
                        produccion
                    </th>
                    <th scope="col" class="w-1/6 px-4 py-2 text-left text-sm font-semibold text-gray-900 dark:text-white border">
                        serigrafiado
                    </th>
                    <th scope="col" class="w-1/6 px-4 py-2 text-left text-sm font-semibold text-gray-900 dark:text-white border">
                        empaquetado
                    </th>
                    <th scope="col" class="w-1/6 px-4 py-2 text-left text-sm font-semibold text-gray-900 dark:text-white border">
                        sellado
                    </th>
                    <th scope="col" class="w-1/6 px-4 py-2 text-left text-sm font-semibold text-gray-900 dark:text-white border">
                        enbolsado
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-900">
                @php
                    $codigos = DB::table('fabricacions')
                        ->where('estado', true)
                        ->whereNotNull('codigo')
                        ->orderBy('codigo')
                        ->pluck('codigo');
                @endphp

                @foreach ($codigos as $codigo)
                <tr>
                    <td class="px-4 py-2 whitespace-nowrap text-sm border">
                        {{ $codigo }}
                    </td>
                    @foreach(['produccion', 'serigrafiado', 'empaquetado', 'sellado', 'enbolsado'] as $tipoProceso)
                    <td class="p-0 whitespace-nowrap text-sm border">
                        @php
                            $tieneRegistro = DB::table('procesos')
                                ->join('asientos', 'procesos.asiento_id', '=', 'asientos.id')
                                ->join('fabricacions', 'procesos.fabricacion_id', '=', 'fabricacions.id')
                                ->where('fabricacions.codigo', $codigo)
                                ->where('fabricacions.estado', true)
                                ->where('asientos.descripcion', $tipoProceso)
                                ->exists();
                        @endphp
                        <div class="w-full h-full {{ $tieneRegistro ? 'bg-green-500' : 'bg-white dark:bg-gray-900' }} min-h-[30px]">
                            &nbsp;
                        </div>
                    </td>
                    @endforeach
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <style>
        table {
            border-collapse: collapse;
            width: 100%;
            table-layout: fixed;
        }
        
        th, td {
            border: 1px solid #e5e7eb;
            width: 16.666%;
        }
        
        .dark th, .dark td {
            border-color: rgb(75, 85, 99);
        }

        td {
            padding: 0 !important;
        }

        .bg-green-500 {
            background-color: #22c55e !important;
        }

        .w-full {
            width: 100% !important;
        }
    </style>
</x-filament::page>