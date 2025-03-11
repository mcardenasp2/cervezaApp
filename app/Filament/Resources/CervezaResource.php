<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CervezaResource\Pages;
use App\Filament\Resources\CervezaResource\RelationManagers;
use App\Models\Cerveza;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CervezaResource extends Resource
{
    protected static ?string $model = Cerveza::class;

    protected static ?string $navigationIcon = 'heroicon-o-archive-box';

    protected static ?string $navigationLabel = 'Cervezas';
    protected static ?string $navigationGroup = 'Mantenimiento';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                TextInput::make('nombre')
                    ->label('Nombre')
                    ->required()
                    ->maxLength(255),

                TextInput::make('precio_por_mililitro')
                    ->label('Precio por ml')
                    ->numeric()
                    ->required(),
            ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                TextColumn::make('nombre')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('precio_por_mililitro')
                    ->label('Precio por ml')
                    ->sortable(),

                BadgeColumn::make('estado')
                    ->label('Estado')
                    ->colors([
                        'success' => 1,
                        'danger' => 0,
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
                    ->action(fn (Cerveza $record) => $record->update(['estado' => 0])),
                        ]);

    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCervezas::route('/'),
            'create' => Pages\CreateCerveza::route('/create'),
            'edit' => Pages\EditCerveza::route('/{record}/edit'),
        ];
    }
}
