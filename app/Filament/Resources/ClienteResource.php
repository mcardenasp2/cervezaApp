<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClienteResource\Pages;
use App\Filament\Resources\ClienteResource\RelationManagers;
use App\Models\Cliente;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Validation\Rule;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;

class ClienteResource extends Resource
{
    protected static ?string $model = Cliente::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Mantenimiento';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('cedula')
                ->required()
                ->maxLength(255)
                ->label('Cédula')
                ->disabled(fn ($livewire) => $livewire instanceof \Filament\Resources\Pages\EditRecord) // Solo deshabilitar en edición
                ->rules([
                    function ($get, $record) {
                        return Rule::unique('clientes', 'cedula')
                            ->ignore($record?->id)
                            ->where('estado', 1); // Única entre los activos
                    }
                ]),
                Forms\Components\TextInput::make('nombres')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('correo')
                    ->email()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('cedula'),
                Tables\Columns\TextColumn::make('nombres'),
                Tables\Columns\TextColumn::make('estado')
                    ->formatStateUsing(fn ($state) => $state ? 'Activo' : 'Inactivo')
                    ->color(fn ($state) => $state ? 'success' : 'danger'),
                Tables\Columns\TextColumn::make('correo'),
            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make()
                    ->action(function ($record) {
                        $record->estado = 0;
                        $record->save();
                    })
                    ->label('Dar de baja')
                    ->requiresConfirmation()
                    ->modalHeading('Confirmar baja')
                    ->modalSubheading('¿Estás seguro de dar de baja este cliente?')
                    ->modalButton('Sí, dar de baja'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListClientes::route('/'),
            'create' => Pages\CreateCliente::route('/create'),
            'edit' => Pages\EditCliente::route('/{record}/edit'),
        ];
    }

}
