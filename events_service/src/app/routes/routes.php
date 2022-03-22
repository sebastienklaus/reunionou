<?php

use DavidePastore\Slim\Validation\Validation;
use reu\events\app\controller\Commande_Controller;
use reu\events\app\controller\Commande_Item_Controller;
use reu\events\app\middleware\CommandeValidator;
use reu\events\app\middleware\Token;

//* TD1 & TD2

// Route pour une commande
$app->get('/td/commandes/{id}[/]', Commande_Controller::class . ':getCommande')
    ->setName('getCommande')
    ->add(Token::class . ':check');
