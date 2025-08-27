<?php

namespace App\Enum;

interface BackedEnumInterface extends \BackedEnum {}

enum EtatSortie: string implements BackedEnumInterface
{
    case CREEE = 'cree';
    case OUVERTE = 'ouverte';
    case EN_COURS = 'en cours';
    case CLOTUREE = 'cloturee';
    case ANNULEE = 'annulee';
    case TERMINEE = 'terminee';

}
