<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Filament\Resources\CategoryResource\RelationManagers;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $navigationIcon = 'heroicon-o-squares-plus';
    protected static ?string $navigationLabel = null;
    protected static ?string $modelLabel = null;
    protected static ?string $navigationGroup = null;
    protected static ?int $navigationSort = null;

    /**
     * @param Form $form
     * @return Form
     */
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Categorias')
                    ->description('Informações da categoria')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->placeholder('Digite o nome da categoria')
                            ->required()
                            ->maxLength(191),
                        Forms\Components\TextInput::make('description')
                            ->placeholder('Digite a descrição da categoria')
                            ->required()
                            ->maxLength(191),
                        Forms\Components\FileUpload::make('image')
                            ->image()
                            ->directory('uploads')
                            ->visibility('public')
                            ->columnSpanFull(),
                        Forms\Components\Toggle::make('show_on_home')
                            ->label('Exibir na Home?')
                            ->helperText('Se marcado, os jogos desta categoria aparecerão na página inicial.')
                            ->columnSpanFull(),
                        Forms\Components\MultiSelect::make('games')
                            ->label('Jogos desta categoria')
                            ->options(\App\Models\Game::where('type', 'slots')->pluck('name', 'id'))
                            ->helperText('Selecione os jogos que deseja associar a esta categoria. Somente jogos do tipo "slots" aparecem aqui.')
                            ->columnSpanFull(),
                    ])
                ]);
    }

    /**
     * @param Table $table
     * @return Table
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->size(20)
                    ->label('Imagem'),
                Tables\Columns\TextColumn::make('name')
                    ->sortable()
                    ->label('Nome')
                    ->searchable(),
                Tables\Columns\TextColumn::make('description')
                    ->label('Descrição')
                    ->limit(80)
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])->defaultSort('name', 'desc')
            ->filters([
                //
            ])
            ->actions(env('APP_DEMO') ? [
                Tables\Actions\ViewAction::make(),
            ] : [
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions(env('APP_DEMO') ? [] : [
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ]);
    }

    /**
     * @return array|\Filament\Resources\RelationManagers\RelationGroup[]|\Filament\Resources\RelationManagers\RelationManagerConfiguration[]|string[]
     */
    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    /**
     * @return array|\Filament\Resources\Pages\PageRegistration[]
     */
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }
}
