<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Commande;
use Filament\Tables\Table;
use App\Models\ArticleCommande;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\RestoreAction;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Actions\ForceDeleteAction;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Tables\Concerns\InteractsWithTable;

class ListCommande extends Component implements HasForms, HasTable
{
    use InteractsWithTable;
    use InteractsWithForms;

    public function table(Table $table): Table
    {
        return $table
            ->query(ArticleCommande::query()->orderBy('created_at', 'desc'))
            ->columns([
                TextColumn::make('commande.numero')
                    ->label('Commande')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('commande.user.name')
                    ->label('Client')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('article.nom')
                    ->label('Article')
                    ->searchable(),

                TextColumn::make('article.boutique.nom')
                    ->label('Vendeur')
                    ->searchable(),

                TextColumn::make('quantite')
                    ->label('Quantité')
                    ->sortable()
                    ->summarize(Sum::make()),

                TextColumn::make('prix_unitaire')
                    ->label('Prix unitaire')
                    ->formatStateUsing(fn($state) => number_format($state, 0, ',', ' ') . ' FCFA')
                    ->summarize(Sum::make()->suffix(' FCFA')),

                TextColumn::make('commande.prix')
                    ->label('Montant commande')
                    ->formatStateUsing(fn($state) => number_format($state, 0, ',', ' ') . ' FCFA'),


                TextColumn::make('commande.statut')
                    ->label('Statut')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'En attente' => 'warning',
                        'Prête pour livraison' => 'success',
                        'En cours de livraison' => 'success',
                        'Livrée' => 'success',
                        'Remboursée' => 'danger',
                        'Annulée' => 'danger',
                        default => 'gray',
                    }),


            ])
            ->defaultGroup('commande.numero') // 👈 groupement par numéro de commande
            ->filters([])
            ->actions([])
            ->bulkActions([]);
    }
    public function render()
    {
        return view('livewire.list-commande');
    }
}