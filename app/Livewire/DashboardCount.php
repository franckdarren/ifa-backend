<?php

namespace App\Livewire;

use App\Models\Article;
use App\Models\Boutique;
use App\Models\Commande;
use App\Models\Livraison;
use App\Models\User;
use Livewire\Component;

class DashboardCount extends Component
{
    public $nbreClient;
    public $nbreBoutique;
    public $nbreLivraison;

    public $nbreArticle;
    public $nbreCommande;

    public function render()
    {
        // Rafraîchir les données à chaque requête
        $this->nbreClient = User::where('role', 'Client')->count();
        $this->nbreBoutique = Boutique::count();
        $this->nbreLivraison = Livraison::count();
        $this->nbreArticle = Article::count();;
        $this->nbreCommande = Commande::count();

        return view('livewire.dashboard-count', [
            'nbreClient' => $this->nbreClient,
            'nbreBoutique' => $this->nbreBoutique,
            'nbreLivraison' => $this->nbreLivraison,
            'nbreArticle' => $this->nbreArticle,
            'nbreCommande' => $this->nbreCommande,

        ]);
    }
}
