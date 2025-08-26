<?php

namespace App\class;

interface BackedEnumInterface extends \BackedEnum {}

enum Etat: string implements BackedEnumInterface
{
    case CREATED = 'Créée';
    case OPENED = 'Ouverte';
    case CLOSED = 'Clôturée';
    case NOW = 'Activité en cours';
    case PASSED = 'Passée';
    case CANCELLED = 'Annulée';

}
