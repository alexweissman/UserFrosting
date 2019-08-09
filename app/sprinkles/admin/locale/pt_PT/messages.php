<?php

/*
 * UserFrosting (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/UserFrosting
 * @copyright Copyright (c) 2019 Alexander Weissman
 * @license   https://github.com/userfrosting/UserFrosting/blob/master/LICENSE.md (MIT License)
 */

/**
 * Portuguese message token translations for the 'admin' sprinkle.
 *
 * @author Bruno Silva (brunomnsilva@gmail.com)
 */
return [
    'ACTIVITY' => [
        1      => 'Atividade',
        2      => 'Atividades',
        'LAST' => 'Última atividade',
        'PAGE' => 'Lista de atividade dos utilizadores',
        'TIME' => 'Tempo da Atividade',
    ],
    'CACHE' => [
        'CLEAR'             => 'Limpar cache',
        'CLEAR_CONFIRM'     => 'Tem a certeza que pretende limpar a cache do site?',
        'CLEAR_CONFIRM_YES' => 'Sim, limpar cache',
        'CLEARED'           => 'Cache limpa com sucesso!',
    ],
    'DASHBOARD'           => 'Painel de Controlo',
    'NO_FEATURES_YET'     => 'It doesn\'t look like any features have been set up for this account...yet.  Maybe they haven\'t been implemented yet, or maybe someone forgot to give you access.  Either way, we\'re glad to have you aboard!',
    'DELETE_MASTER'       => 'Não pode apagar a conta principal!',
    'DELETION_SUCCESSFUL' => 'Utilizador <strong>{{user_name}}</strong> foi removido com sucesso.',
    'DETAILS_UPDATED'     => 'Detalhes de conta atualizados para o utilizador <strong>{{user_name}}</strong>',
    'DISABLE_MASTER'      => 'Não pode desativar a conta principal!',
    'DISABLE_SELF'        => 'You cannot disable your own account!',
    'DISABLE_SUCCESSFUL'  => 'Conta do utilizador <strong>{{user_name}}</strong> foi desativada com sucesso.',
    'ENABLE_SUCCESSFUL'   => 'Conta do utilizador <strong>{{user_name}}</strong> foi ativada com sucesso.',
    'GROUP'               => [
        1                     => 'Grupo',
        2                     => 'Grupos',
        'CREATE'              => 'Criar grupo',
        'CREATION_SUCCESSFUL' => 'Grupo criado com sucesso',
        'DELETE'              => 'Remover grupo',
        'DELETE_CONFIRM'      => 'Tem a certeza que pretende remover o grupo <strong>{{name}}</strong>?',
        'DELETE_DEFAULT'      => 'You can\'t delete the group <strong>{{name}}</strong> because it is the default group for newly registered users.',
        'DELETE_YES'          => 'Sim, remover grupo',
        'DELETION_SUCCESSFUL' => 'Grupo removido com sucesso',
        'EDIT'                => 'Editar grupo',
        'ICON'                => 'Icon do grupo',
        'ICON_EXPLAIN'        => 'Icon para membros do grupo',
        'INFO_PAGE'           => 'Página informativa do grupo {{name}}',
        'MANAGE'              => 'Manage group',
        'NAME'                => 'Nome do grupo',
        'NAME_EXPLAIN'        => 'Por favor introduza um nome para o grupo',
        'NOT_EMPTY'           => 'You can\'t do that because there are still users associated with the group <strong>{{name}}</strong>.',
        'PAGE_DESCRIPTION'    => 'Lista de grupos do site. Contém opções para editar e remover grupos.',
        'SUMMARY'             => 'Group Summary',
        'UPDATE'              => 'Details updated for group <strong>{{name}}</strong>',
    ],
    'MANUALLY_ACTIVATED'    => 'A conta de {{user_name}} foi ativada manualmente.',
    'MASTER_ACCOUNT_EXISTS' => 'A contra principal já existe!',
    'MIGRATION'             => [
        'REQUIRED' => 'É necessário uma atualização da base de dados.',
    ],
    'PERMISSION' => [
        1                  => 'Permissão',
        2                  => 'Permissões',
        'ASSIGN_NEW'       => 'Atribuir nova permissão',
        'HOOK_CONDITION'   => 'Hook/Condições',
        'ID'               => 'Permission ID',
        'INFO_PAGE'        => 'Permission information page for \'{{name}}\'',
        'MANAGE'           => 'Gerir permissões',
        'NOTE_READ_ONLY'   => '<strong>Please note:</strong> permissions are considered "part of the code" and cannot be modified through the interface.  To add, remove, or modify permissions, the site maintainers will need to use a <a href="https://learn.userfrosting.com/database/extending-the-database" target="about:_blank">database migration.</a>',
        'PAGE_DESCRIPTION' => 'Lista de permissões do site.  Contém opções para editar e remover permissões.',
        'SUMMARY'          => 'Permission Summary',
        'UPDATE'           => 'Atualizar permissões',
        'VIA_ROLES'        => 'Has permission via roles',
    ],
    'ROLE' => [
        1                     => 'Cargo',
        2                     => 'Cargos',
        'ASSIGN_NEW'          => 'Atribuir novo cargo',
        'CREATE'              => 'Criar cargo',
        'CREATION_SUCCESSFUL' => 'Cargo criado com sucesso',
        'DELETE'              => 'Remover cargo',
        'DELETE_CONFIRM'      => 'Tem a certeza que pretende remover o cargo <strong>{{name}}</strong>?',
        'DELETE_DEFAULT'      => 'You can\'t delete the role <strong>{{name}}</strong> because it is a default role for newly registered users.',
        'DELETE_YES'          => 'Sim, remover cargo',
        'DELETION_SUCCESSFUL' => 'Cargo removido com sucesso',
        'EDIT'                => 'Editar cargo',
        'HAS_USERS'           => 'You can\'t do that because there are still users who have the role <strong>{{name}}</strong>.',
        'INFO_PAGE'           => 'Página informativa do cargo {{name}}',
        'MANAGE'              => 'Gerir cargos',
        'NAME'                => 'Nome',
        'NAME_EXPLAIN'        => 'Por favor introduza um nome para o cargo',
        'NAME_IN_USE'         => 'A role named <strong>{{name}}</strong> already exist',
        'PAGE_DESCRIPTION'    => 'Lista de cargos do site.  Contém opções para editar e remover cargos.',
        'PERMISSIONS_UPDATED' => 'Permissions updated for role <strong>{{name}}</strong>',
        'SUMMARY'             => 'Role Summary',
        'UPDATED'             => 'Cargo <strong>{{name}}</strong> atualizado',
    ],
    'SYSTEM_INFO' => [
        '@TRANSLATION' => 'Informação do sistema',
        'DB_NAME'      => 'Nome da base de dados',
        'DB_VERSION'   => 'Versão da base de dados',
        'DIRECTORY'    => 'Diretório do projeto',
        'PHP_VERSION'  => 'Versão PHP',
        'SERVER'       => 'Software do servidor web',
        'SPRINKLES'    => 'Sprinkles carregados',
        'UF_VERSION'   => 'Versão do UserFrosting',
        'URL'          => 'Raiz (url) do site',
    ],
    'TOGGLE_COLUMNS' => 'Toggle columns',
    'USER'           => [
        1       => 'Utilizador',
        2       => 'Utilizadores',
        'ADMIN' => [
            'CHANGE_PASSWORD'    => 'Alterar password',
            'SEND_PASSWORD_LINK' => 'Enviar um link ao utilizador que lhe permita escolher a sua password',
            'SET_PASSWORD'       => 'Definir a password do utilizador como',
        ],
        'ACTIVATE'         => 'Ativar utilizador',
        'CREATE'           => 'Criar utilizador',
        'CREATED'          => 'User <strong>{{user_name}}</strong> has been successfully created',
        'DELETE'           => 'Remover utilizador',
        'DELETE_CONFIRM'   => 'Tem a certeza que pretende remover o utilizador <strong>{{name}}</strong>?',
        'DELETE_YES'       => 'Sim, remover utilizador',
        'DELETED'          => 'User deleted',
        'DISABLE'          => 'Desativar utilizador',
        'EDIT'             => 'Editar utilizador',
        'ENABLE'           => 'Ativar utilizador',
        'INFO_PAGE'        => 'Página informativa do utilizador {{name}}',
        'LATEST'           => 'Últimos Utilizadores',
        'PAGE_DESCRIPTION' => 'Lista de utilizadores do site.  Contém opções para editar detalhes, ativar/desativar utilizadores e outras.',
        'SUMMARY'          => 'Account Summary',
        'VIEW_ALL'         => 'Ver todos os utilizadores',
        'WITH_PERMISSION'  => 'Users with this permission',
    ],
    'X_USER' => [
        0 => 'Nenhum utilizador',
        1 => '{{plural}} utilizador',
        2 => '{{plural}} utilizadores',
    ],
];
