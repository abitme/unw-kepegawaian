<?php

/**
 * IonAuth routes file.
 */

$routes->add('login', 		'Auth::login', 			['namespace' => 'IonAuth\Controllers']);
// $routes->add('register',	'Auth::create_user', 	['namespace' => 'IonAuth\Controllers']);
$routes->get('logout', 		'Auth::logout', 		['namespace' => 'IonAuth\Controllers']);

$routes->group('menus', ['namespace' => 'IonAuth\Controllers', 'filter' => 'login'], function ($routes) {
	$routes->get('/',						'Menu::index');
	$routes->post('create',					'Menu::create');
	$routes->post('edit/(:num)',			'Menu::update/$1');
	$routes->post('delete/(:num)',			'Menu::delete/$1');
	$routes->post('sortMenu',				'Menu::sortMenu');
	$routes->get('groupSelected/(:num)',	'Menu::groupSelected/$1');
	$routes->get('crudSelected/(:num)',		'Menu::crudSelected/$1');
});

$routes->group('profile', ['namespace' => 'IonAuth\Controllers', 'filter' => 'login'], function ($routes) {
	$routes->get('', 'Profile::index');
	$routes->get('edit', 'Profile::edit');
	$routes->put('update', 'Profile::update');

	// $routes->get('(:any)', function() {
	// 	throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
	// });
});

$routes->group('users', ['namespace' => 'IonAuth\Controllers', 'filter' => 'login'], function ($routes) {
	$routes->get('/',				'Auth::users');
	$routes->add('create',			'Auth::create_user');
	$routes->add('edit/(:num)',		'Auth::edit_user/$1');
	$routes->add('import',			'Auth::import_user');
});

$routes->group('groups', ['namespace' => 'IonAuth\Controllers', 'filter' => 'login'], function ($routes) {
	$routes->get('/',               'Auth::groups');
	$routes->add('new',				'Auth::create_group');
	$routes->add('edit/(:num)',		'Auth::edit_group/$1');
	$routes->get('access/(:num)',	'Auth::access_group/$1');
	$routes->delete('(:segment)',	'Auth::delete_group/$1');
});

$routes->group('auth', ['namespace' => 'IonAuth\Controllers'], function ($routes) {
	$routes->get('/', 'Auth::index');

	$routes->get('activate/(:num)', 'Auth::activate/$1');
	$routes->get('activate/(:num)/(:hash)', 'Auth::activate/$1/$2');
	$routes->add('deactivate/(:num)', 'Auth::deactivate/$1');
	$routes->add('forgot_password', 'Auth::forgot_password');
	$routes->get('reset_password/(:hash)', 'Auth::reset_password/$1');
	$routes->post('reset_password/(:hash)', 'Auth::reset_password/$1');
	$routes->post('reset_password/(:hash)', 'Auth::reset_password/$1');
	$routes->get('change_password', 'Auth::change_password');
	$routes->post('change_password', 'Auth::change_password');

	$routes->post('menu_access/(:num)', 'Auth::menu_access/$1');
	$routes->post('groups_access/(:num)/(:num)', 'Auth::groups_access/$1/$2');
	$routes->post('change_access', 'Auth::change_access');
	$routes->post('change_crud_access', 'Auth::change_crud_access');
});
