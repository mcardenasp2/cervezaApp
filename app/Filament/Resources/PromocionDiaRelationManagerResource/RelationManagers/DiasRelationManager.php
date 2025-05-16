<?php

namespace App\Filament\Resources\PromocionDiaRelationManagerResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DiasRelationManager extends RelationManager
{
    protected static string $relationship = 'dias';

    public function form(Form $form): Form
    {
        return $form->schema([
            Select::make('dia')
                ->label('Día')
                ->options([
                    'lunes' => 'Lunes',
                    'martes' => 'Martes',
                    'miércoles' => 'Miércoles',
                    'jueves' => 'Jueves',
                    'viernes' => 'Viernes',
                    'sábado' => 'Sábado',
                    'domingo' => 'Domingo',
                ])
                ->required(),

            TimePicker::make('hora_inicio')
                ->label('Desde')
                ->required(),

            TimePicker::make('hora_fin')
                ->label('Hasta')
                ->required(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('dia')->label('Día'),
            TextColumn::make('hora_inicio')->label('Desde'),
            TextColumn::make('hora_fin')->label('Hasta'),
        ]);
    }
}
