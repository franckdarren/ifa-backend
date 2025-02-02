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
            // ->paginated(false)
            ->columns([
                TextColumn::make('commande.numero')
                    ->label('Numero')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('prix')
                    ->label('Prix')
                    ->formatStateUsing(fn($state) => number_format($state, 0, ',', ' ') . ' FCFA')
                    ->summarize(Sum::make()->suffix(' FCFA'))
                    ->searchable(),

                TextColumn::make('commande.statut')
                    ->label('Statut')
                    ->searchable(),

                TextColumn::make('commande.commentaire')
                    ->label('Commentaire')
                    ->searchable(),

                TextColumn::make('commande.isLivrable')
                    ->label('Avec livraison')
                    ->formatStateUsing(fn($state) => $state ? 'Oui' : 'Non')
                    ->searchable(),

                TextColumn::make('commande.user.name')
                    ->label('Utilisateur')
                    ->searchable(),

                TextColumn::make('article.boutique.nom')
                    ->label('Vendeur')
                    ->searchable(),
            ])
            ->headerActions([

            ])
            ->filters([
            ])
            ->actions([

            ])
            ->bulkActions([])
            ->defaultGroup('commande.numero');
        ;
    }
    public function render()
    {
        return view('livewire.list-commande');
    }
}
