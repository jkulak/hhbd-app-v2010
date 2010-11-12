<?php

/**
 * Wyjatek
 * 
 * @author Playah
 */
class Dupa_Exception extends Exception
{
    const ERROR_DB        = 1;    // blad z baza danych
    const ERROR_VALIDATE  = 2;    // niepoprawne parametry
}
?>