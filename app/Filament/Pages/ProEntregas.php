<?php
namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use App\Models\ProduccionFinal;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use App\Models\ProduccionTotal;
use Filament\Notifications\Notification;

class ProEntregas extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationLabel = 'Producciones Por Entregar';
    protected static ?string $title = 'Lista de Producciones';
    protected static ?string $navigationGroup = 'LOGISTICA/VENTAS/ENTREGAS';
    protected static ?string $slug = 'pro-entregas';

    // Establecer la vista para la página
    protected static string $view = 'filament.pages.pro-entregas'; // Ruta a la vista Blade

    // Configurar tabla
    protected function getTableQuery(): Builder
    {
        return ProduccionTotal::query()
            ->where('cantidad_disponible', '>', 0)
            ->orderBy('id', 'desc');
    }

    protected function getDefaultTableSortColumn(): ?string
    {
        return 'id';
    }

    protected function getDefaultTableSortDirection(): ?string
    {
        return 'desc';
    }

    protected function getTableActions(): array
    {
        return [
            Action::make('crear_entrega')
                ->label('Crear Entrega')
                ->icon('heroicon-o-paper-airplane')
                ->url(fn (ProduccionTotal $record): string => 
                    route('filament.Dashboard.resources.entregas.create', [
                        'orden_produccion_id' => $record->fabricacion->orden_produccion_id,
                        'producto' => $record->producto,
                        'cantidad' => $record->cantidad_disponible,
                    ])
                )
                ->color('success')
                ->openUrlInNewTab(false),
            
            Action::make('enviar_a_inventario')
                ->label('Enviar a Inventario')
                ->icon('heroicon-o-archive-box')
                ->action(function (ProduccionTotal $record) {
                    // Aquí deberás implementar la lógica para:
                    // 1. Crear un nuevo registro en la tabla de inventario
                    // 2. Actualizar cantidad_disponible a 0 en ProduccionTotal
                    $record->update(['cantidad_disponible' => 0]);
                    
                    // Asumiendo que tienes un modelo Inventario, deberías crear el registro así:
                    // Inventario::create([
                    //     'producto' => $record->producto,
                    //     'cantidad' => $record->cantidad_disponible,
                    //     'produccion_total_id' => $record->id,
                    // ]);
                    
                    Notification::make()
                        ->title('Producto enviado al inventario')
                        ->success()
                        ->send();
                })
                ->requiresConfirmation()
                ->color('warning')
                ->visible(fn (ProduccionTotal $record) => $record->cantidad_disponible > 0),
        ];
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('fabricacion.codigo')
                ->label('Código de Fabricación')
                ->sortable()
                ->searchable(),

            TextColumn::make('fabricacion.OrdenProduccion.codigo')
                ->label('Orden de Producción')
                ->sortable()
                ->searchable(),

            TextColumn::make('producto')
                ->label('Producto')
                ->sortable()
                ->searchable(),

            TextColumn::make('cantidad_disponible')
                ->label('Disponible')
                ->formatStateUsing(fn ($state) => number_format($state, 2)),
        ];
    }
    public static function getNavigationBadge(): ?string
{
    return ProduccionTotal::where('cantidad_disponible', '>', 0)->count();
}
}
