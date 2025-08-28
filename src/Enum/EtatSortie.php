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

}
