<?php

use Illuminate\Routing\Router;


$router->group(['prefix' => '/v2/ilocations'], function (Router $router) {
  
  
  $router->group(['prefix' => '/countries'], function (Router $router) {
    
    $router->get('/', [
      'as' => 'ilocation.api.get.countries',
      'uses' => 'CountryApiController@index',
    ]);
    
  });
  
  $router->group(['prefix' => '/provinces'], function (Router $router) {
    
    $router->get('/', [
      'as' => 'ilocation.api.get.provinces',
      'uses' => 'ProvinceApiController@index',
    ]);
  });
  
  $router->group(['prefix' => '/cities'], function (Router $router) {
    
    $router->get('/', [
      'as' => 'ilocation.api.get.cities',
      'uses' => 'CityApiController@index',
    ]);
    
  });
});



$router->group(['prefix' => '/ilocations'], function (Router $router) {
  
  
  $router->get('allfullcountries', [
    'as' => 'ilocation.api.get.allfullcountries',
    'uses' => 'CountryController@allFullCountries',
  ]);
  
  $router->get('allmincountries', [
    'as' => 'ilocation.api.get.allmincountries',
    'uses' => 'CountryController@allMinCountries',
  ]);
  
  $router->get('allprovincesbycountry/iso2/{countryCode}', [
    'as' => 'ilocation.api.get.allprovincesbycountry.iso2',
    'uses' => 'CountryController@allProvincesByCountryIso2',
  ]);
  
  $router->get('allprovincesbycountry/iso3/{countryCode}', [
    'as' => 'ilocation.api.get.allprovincesbycountry.iso3',
    'uses' => 'CountryController@allProvincesByCountryIso3',
  ]);
  $router->get('allcitiesbyprovince/{provinceId}', [
    'as' => 'ilocation.api.get.allcitiesbyprovince',
    'uses' => 'CountryController@allCitiesByProvinceId',
  ]);
  
});