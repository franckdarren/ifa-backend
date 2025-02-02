<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Categorie;
use Filament\Tables\Table;
use App\Models\SousCategorie;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\RestoreAction;
use Filament\Tables\Actions\ForceDeleteAction;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Tables\Concerns\InteractsWithTable;

class ListSousCategorie extends Component implements HasForms, HasTable
{
    use InteractsWithTable;
    use InteractsWithForms;

    public function table(Table $table): Table
    {
        return $table
            ->query(SousCategorie::query()->orderBy('nom', 'asc'))
            // ->paginated(false)
            ->columns([
                TextColumn::make('nom')
                    ->label('Nom')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('categorie.nom')
                    ->label('Catégorie')
                    ->searchable(),

                ImageColumn::make('url_image')
                    ->label('Image')
                    ->url(fn($record) => asset('/storage/' . $record->url_image)) // Prends en compte le chemin relatif
                    ->size(50),


            ])
            ->headerActions([
                Action::make('create')
                    ->label('Ajouter une sous catégorie')
                    ->button()
                    ->form([
                        TextInput::make('nom')
                            ->label('Nom')
                            ->required(),
                        Select::make('categorie_id')
                            ->label('Catégorie')
                            ->options(Categorie::all()->pluck('nom', 'id'))
                            ->searchable()
                            ->required()
                            ->placeholder('Sélectionnez une catégorie'),
                        FileUpload::make('url_image')   // Remplace le TextInput par FileUpload
                            ->label('Image')
                            ->image()                   // Opdescriptiontionnel : pour restreindre aux images
                            ->directory('categories')   // Répertoire de stockage (sera stocké dans storage/app/public/categories si le disque est 'public')
                            ->visibility('public')      // Pour que le fichier soit accessible publiquement
                            ->required(),
                    ])
                    ->action(function (array $data) {
                        SousCategorie::create($data);
                        Notification::make()
                            ->title('Succès')
                            ->body('Categorie créé avec succès!')
                            ->success()
                            ->send();
                    })
            ])
            ->filters([
            ])
            ->actions([
                Action::make('edit')
                    ->label('Modifier')
                    ->modalHeading('Modifier la catégorie')
                    ->form([
                        TextInput::make('nom')
                            ->label('Nom')
                            ->required()
                            ->default(fn($record) => $record->nom), // Lier explicitement au champ 'nom'
                        Select::make('categorie_id')
                            ->label('Catégorie')
                            ->options(Categorie::all()->pluck('nom', 'id'))
                            ->searchable()
                            ->required()
                            ->placeholder('Sélectionnez une catégorie'),
                        FileUpload::make('url_image')
                            ->label('Image')
                            ->image()
                            ->directory('categories')
                            ->visibility('public'),
                    ])
                    ->action(function (SousCategorie $record, array $data) {
                        $record->update($data);
                        Notification::make()
                            ->title('Succès')
                            ->body('Sous catégorie modifiée avec succès!')
                            ->success()
                            ->send();
                    })
                    ->modalButton('Enregistrer'),
                // Tu peux ajouter d'autres actions comme la suppression
                DeleteAction::make(),
            ])
            ->bulkActions([]);
    }

    public function render()
    {
        return view('livewire.list-sous-categorie');
    }
}
