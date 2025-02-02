<?php

namespace App\Livewire;

use App\Models\Article;
use Livewire\Component;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\RestoreAction;
use Filament\Tables\Actions\ForceDeleteAction;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Tables\Concerns\InteractsWithTable;

class ListArticle extends Component implements HasForms, HasTable
{
    use InteractsWithTable;
    use InteractsWithForms;

    public function table(Table $table): Table
    {
        return $table
            ->query(Article::query()->orderBy('created_at', 'desc'))
            // ->paginated(false)
            ->columns([
                TextColumn::make('nom')
                    ->label('Nom')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('description')
                    ->label('Description')
                    ->searchable(),

                TextColumn::make('prix')
                    ->label('Prix')
                    ->formatStateUsing(fn($state) => number_format($state, 0, ',', ' ') . ' FCFA')
                    ->searchable(),

                TextColumn::make('quantité')
                    ->label('Quantité')
                    ->searchable(),

                TextColumn::make('isDisponible')
                    ->label('Disponible')
                    ->formatStateUsing(fn($state) => $state ? 'Oui' : 'Non')
                    ->searchable(),

                TextColumn::make('isPromotion')
                    ->label('En promotion')
                    ->formatStateUsing(fn($state) => $state ? 'Oui' : 'Non')
                    ->searchable(),

                TextColumn::make('pourcentageReduction')
                    ->label('Réduction en %')
                    ->searchable(),

                TextColumn::make('prixPromotion')
                    ->label('Prix promo')
                    ->formatStateUsing(fn($state) => number_format($state, 0, ',', ' ') . ' FCFA')
                    ->searchable(),

                TextColumn::make('boutique.nom')
                    ->label('Boutique')
                    ->searchable(),

                TextColumn::make('sousCategorie.nom')
                    ->label('Sous catégorie')
                    ->searchable(),

                TextColumn::make('sousCategorie.categorie.nom')
                    ->label('Catégorie')
                    ->searchable(),

                ImageColumn::make('images.url_photo')
                    ->circular()
                    ->stacked()

            ])
            ->headerActions([

            ])
            ->filters([
            ])
            ->actions([

            ])
            ->bulkActions([]);
    }
    public function render()
    {
        return view('livewire.list-article');
    }
}
