<?php

namespace App\Filament\Resources\PromocionResource\Pages;

use App\Filament\Resources\PromocionResource;
use App\Models\DetallePromocionAplicada;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPromocion extends EditRecord
{
    protected static string $resource = PromocionResource::class;

    protected function getHeaderActions(): array
    {
        return [
        ];
    }

    public function mount($record): void
    {
        if (!auth()->user()->can('promocion-editar')) {
            abort(403); // Acceso denegado si no tiene el permiso
        }

        parent::mount($record);
    }
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getFormActions(): array
    {

        if($this->record->estado != 1){
            return [];
        }

        $appliedPromotionDetail = DetallePromocionAplicada::whereHas('venta', function($query){
                $query->where('estado', 1);
            })
            ->where('estado', 1)
            ->where('promocion_id', $this->record->id)
            ->count();

        if ($appliedPromotionDetail > 0) {
            return [];
        }

        // Si tiene permiso, mostrar acciones normales (puedes personalizar más si quieres)
        return parent::getFormActions();
    }
}
