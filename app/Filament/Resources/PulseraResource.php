<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PulseraResource\Pages;
use App\Filament\Resources\PulseraResource\RelationManagers;
use App\Models\Pulsera;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PulseraResource extends Resource
{
    protected static ?string $model = Pulsera::class;
    protected static ?string $navigationIcon = 'heroicon-o-archive-box';
    protected static ?string $navigationGroup = 'Mantenimiento';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                TextInput::make('codigo_serial')
                    ->label('Código Serial')
                    ->required()
                    ->unique(),

                TextInput::make('codigo_uid')
                    ->label('Código UID')
                    ->required()
                    ->unique(),

            ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
        ->columns([
            TextColumn::make('codigo_serial')->sortable()->searchable(),
            TextColumn::make('codigo_uid')->sortable()->searchable(),
            BadgeColumn::make('estado')
                ->label('Estado')
                ->colors([
                    'success' => 1,   // Verde para Activo
                    'danger' => 0,    // Rojo para Inactivo
                ])
                ->formatStateUsing(fn ($state) => $state == 1 ? 'Activo' : 'Inactivo'),
        ])
        ->actions([
            EditAction::make(),
            Action::make('desactivar')
                    ->label('Desactivar')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(fn (Pulsera $record) => $record->update(['estado' => 0])),
                        ]);

    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPulseras::route('/'),
            'create' => Pages\CreatePulsera::route('/create'),
            'edit' => Pages\EditPulsera::route('/{record}/edit'),
        ];
    }


    // public static function getEloquentQuery(): Builder
    // {
    //     return parent::getEloquentQuery()->orderBy('codigo_serial');
    // }
}
