<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransaccionResource\Pages;
use App\Filament\Resources\TransaccionResource\RelationManagers;
use App\Models\Transaccion;
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

class TransaccionResource extends Resource
{
    protected static ?string $model = Transaccion::class;
    protected static ?string $navigationIcon = 'heroicon-o-archive-box';
    protected static ?string $navigationLabel = 'Transacciones';
    protected static ?string $navigationGroup = 'Procesos';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Select::make('pulsera_id')
                    ->relationship('pulsera', 'codigo_serial', function ($query) {
                        return $query->where('estado', 1);
                    })
                    ->searchable()
                    // ->preload()
                    ->label('Pulsera'),

                TextInput::make('codigo_uid')
                    ->required()
                    ->maxLength(255),

                Select::make('cerveza_id')
                    ->relationship('cerveza', 'nombre', function ($query) {
                        return $query->where('estado', 1);
                    })
                    ->searchable()
                    ->label('Cerveza'),

                TextInput::make('mililitros_consumidos')
                    ->numeric()
                    ->required()
                    ->suffix('ml'),

                TextInput::make('valor')
                    ->numeric()
                    ->required()
                    ->prefix('$'),

            ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                TextColumn::make('pulsera.codigo_serial')->label('Pulsera'),
                TextColumn::make('codigo_uid')->label('Código UID'),
                TextColumn::make('cerveza.nombre')->label('Cerveza'),
                TextColumn::make('mililitros_consumidos')->label('Mililitros')->sortable(),
                TextColumn::make('valor')->label('Valor')->money('USD')->sortable(),
                BadgeColumn::make('estado')
                    ->label('Estado')
                    ->colors([
                        'success' => 1,
                        'danger' => 0,
                    ])
                    ->formatStateUsing(fn ($state) => $state == 1 ? 'Activo' : 'Inactivo'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('estado')
                    ->options([
                        1 => 'Activo',
                        0 => 'Inactivo',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Action::make('desactivar')
                    ->label('Desactivar')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(fn (Transaccion $record) => $record->update(['estado' => 0])),
                        ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTransaccions::route('/'),
            'create' => Pages\CreateTransaccion::route('/create'),
            'edit' => Pages\EditTransaccion::route('/{record}/edit'),
        ];
    }
}
