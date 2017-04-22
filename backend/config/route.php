<?php
return [
    'OPTIONS v1/<action:.+>' => 'v1/base/options',

    //**********************************************
    // 避免使用如下action:
    // DELETE api/auth/token/<token> => 'api/auth/delete-token'
    // 这种action在OPTIONS请求时,DELETE只能识别<id>结尾的action
    //**********************************************

    // rbac start
    'GET v1/rbac/init'                => 'v1/rbac/init',
    'GET v1/rbac/role'                => 'v1/rbac/role',
    'POST v1/rbac/role'                => 'v1/rbac/create-role',
    'DELETE v1/rbac/role'              => 'v1/rbac/delete-role',
    'GET v1/rbac/permission'           => 'v1/rbac/permission',
    'POST v1/rbac/permission'           => 'v1/rbac/create-permission',
    'GET v1/rbac/assignment'           => 'v1/rbac/assignment',
    'POST v1/rbac/user-role'          => 'v1/rbac/create-user-role',
    'DELETE v1/rbac/user-role'        => 'v1/rbac/delete-user-role',
    'GET v1/rbac/role-permission'      => 'v1/rbac/list-role-permission',
    'POST v1/rbac/role-permission'     => 'v1/rbac/create-role-permission',
    'DELETE v1/rbac/role-permission'   => 'v1/rbac/remove-role-permission',
    // rbac end

    'GET v1/rbac'     => 'v1/rbac',
];