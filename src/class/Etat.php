<?php

interface TestEnumInterface extends \BackedEnum {}

enum Etat: string implements TestEnumInterface
{
    case CREATE = 'Créée';
    case OPEN = 'Ouverte';
    case CLOSED = 'Clôturée';
    case NOW = 'Activité en cours';
    case PASSED = 'Passée';
    case CANCELED = 'Annulée';

}
