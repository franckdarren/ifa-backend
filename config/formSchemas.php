<?php

return [
    'chaussures' => [
        [
            'name' => 'size',
            'label' => 'Pointure',
            'type' => 'number',
            'validation' => 'required|integer|min:20|max:50',
        ],
        [
            'name' => 'color',
            'label' => 'Couleur',
            'type' => 'string',
            'validation' => 'required|string',
        ],
        [
            'name' => 'quantite',
            'label' => 'Quantité',
            'type' => 'number',
            'validation' => 'required|integer',
        ],
    ],

    'vetements' => [
        [
            'name' => 'size',
            'label' => 'Taille',
            'type' => 'enum',
            'options' => ['S', 'M', 'L', 'XL', 'XXL'],
            'validation' => 'required|string',
        ],
        [
            'name' => 'color',
            'label' => 'Couleur',
            'type' => 'string',
            'validation' => 'required|string',
        ],
        [
            'name' => 'quantite',
            'label' => 'Quantité',
            'type' => 'number',
            'validation' => 'required|integer',
        ],
    ],
];
