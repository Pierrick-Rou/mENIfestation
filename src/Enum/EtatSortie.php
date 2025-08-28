<?php

namespace App\Enum;

interface BackedEnumInterface extends \BackedEnum {}

enum EtatSortie: string implements BackedEnumInterface
{
    case CREEE = 'créée';
    case OUVERTE = 'ouverte';
    case EN_COURS = 'en cours';
    case CLOTUREE = 'cloturée';
    case ANNULEE = 'annulée';
    case TERMINEE = 'terminée';



    // Méthode statique pour générer le tableau 'label => value' pour le formulaire
    public static function choices(): array
    {
        $choices = [];
        foreach (self::cases() as $case) {
            $choices[$case->value] = $case->value;
        }
        return $choices;
    }

}
