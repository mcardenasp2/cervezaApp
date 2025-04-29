<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RoleResource\Pages;
use App\Filament\Resources\RoleResource\RelationManagers;
use App\Models\Group;
use App\Models\Role;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\LinkAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;


class RoleResource extends Resource
{
    protected static ?string $model = Role::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';


    public static function getNavigationItems(): array
    {
        $navigationItems = parent::getNavigationItems();

        if (auth()->user()->can('rol-listar')) {
            return $navigationItems;  // Si tiene el permiso, muestra el recurso
        }

        return [];  // Si no tiene el permiso, no muestra el recurso
    }

    public static function form(Form $form): Form
    {

        return $form
        ->schema([

        ]);

    }




    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->sortable()
                    ->searchable()
                    ->label('ID'),
                Tables\Columns\TextColumn::make('name')
                    ->sortable()
                    ->searchable()
                    ->label('Nombre'),

            ])
            ->filters([
                //
            ])
            ->actions([
                LinkAction::make('Editar Permisos')
                ->visible(function () {
                    return auth()->user()->can('rol-editar');
                })
                ->label('Editar')
                ->icon('heroicon-o-pencil')
                ->color('primary')
                // ->url(fn ($record) => Pages\CreateRolePermissions::getUrl(['roleId' => $record->getKey()])),
                ->url(fn ($record) => url('/admin/roles/create-permissions/' . $record->getKey() . '/edit')), // <-- URL manual
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
            'index' => Pages\ListRoles::route('/'),
            'create-permission' => Pages\CreateRolePermissions::route('/create-permissions'),  // Agregar la ruta personalizada
            'edit-permissions' => Pages\CreateRolePermissions::route('/create-permissions/{roleId}/edit'),
        ];
    }

}
