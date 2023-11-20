<?php

use AcMarche\MaintenanceShop\Entity\User;
use AcMarche\MaintenanceShop\Security\CommandeAuthenticator;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;


return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('security', [
        'password_hashers' => [
            User::class => ['algorithm' => 'auto'],
        ],
    ]);

    $containerConfigurator->extension(
        'security',
        [
            'providers' => [
                'commande_user_provider' => [
                    'entity' => [
                        'class' => User::class,
                        'property' => 'username',
                    ],
                ],
            ],
        ]
    );

    $authenticators = [CommandeAuthenticator::class];

    $main = [
        'provider' => 'commande_user_provider',
        'logout' => ['path' => 'app_logout'],
        'form_login' => [],
        'entry_point' => CommandeAuthenticator::class,
        'login_throttling' => [
            'max_attempts' => 6, //per minute...
        ],
    ];

    $main['custom_authenticator'] = $authenticators;

    $containerConfigurator->extension(
        'security',
        [
            'firewalls' => [
                'main' => $main,
            ],
        ]
    );
};
