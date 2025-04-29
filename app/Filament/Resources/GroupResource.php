<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GroupResource\Pages;
use App\Filament\Resources\GroupResource\RelationManagers;
use App\Models\Group;
use App\Models\Permission;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class GroupResource extends Resource
{
    protected static ?string $model = Group::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getNavigationItems(): array
    {
        $navigationItems = parent::getNavigationItems();

        if (auth()->user()->can('grupo-listar')) {
            return $navigationItems;  // Si tiene el permiso, muestra el recurso
        }

        return [];  // Si no tiene el permiso, no muestra el recurso
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')->required(),
                Select::make('permissions')
                    ->label('Permisos')
                    ->relationship('permissions', 'name', fn ($query) => $query->orderBy('name'))
                    ->multiple()
                    ->searchable()
                    ->required(),
            ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('permissions.name')
                    ->label('Permisos')
                    ->badge() // Opcional, lo pone bonito como "chips" separados
                    ->separator(', ') // Opcional, separa por coma
                    ->toggleable(), // Opcional, permite ocultarlo desde el menú de columnas
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                ->visible(function () {
                    return auth()->user()->can('grupo-editar');
                }),
            ])
            ->bulkActions([

            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGroups::route('/'),
            'create' => Pages\CreateGroup::route('/create'),
            'edit' => Pages\EditGroup::route('/{record}/edit'),
        ];
    }
}
