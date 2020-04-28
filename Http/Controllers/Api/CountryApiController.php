<?php

namespace Modules\Ilocations\Http\Controllers\Api;

// Libs
use Modules\Ihelpers\Http\Controllers\Api\BaseApiController;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Exception;
use Log;
use DB;

// Custom Requests
use Modules\Ilocations\Http\Requests\CreateCountryRequest;
use Modules\Ilocations\Http\Requests\UpdateCountryRequest;

// Transformers
use Modules\Ilocations\Transformers\CountryTransformer;

// Repositories
use Modules\Ilocations\Repositories\CountryRepository;


class CountryApiController extends BaseApiController
{

  private $country;

  public function __construct(CountryRepository $country){
    $this->country = $country;
  }

  /**
   * @param Request $request
   * @return mixed
   */
  public function index (Request $request) {
    try {
      $params = $this->getParamsRequest($request);
      $countries = $this->country->getItemsBy($params);
      $response = ['data' => CountryTransformer::collection($countries)];
      $params->page ? $response["meta"] = ["page" => $this->pageTransformer($countries)] : false;
      $status = 200;
    } catch (Exception $exception) {
      Log::Error($exception);
      $status = $this->getStatusError($exception->getCode());
      $response = ['errors' => $exception->getMessage()];
    }
    return response()->json($response, $status);
  }

  public function show ($criteria, Request $request) {
    try {
      $params = $this->getParamsRequest($request);
      $country = $this->country->getItem($criteria, $params);
      if(!$country) throw new Exception('Item not found',404);
      $response = ['data' => new CountryTransformer($country)];
      $status = 200;
    } catch (Exception $exception) {
      Log::Error($exception);
      $status = $this->getStatusError($exception->getCode());
      $response = ['errors' => $exception->getMessage()];
    }
    return response()->json($response, $status);
  }

  public function create (Request $request) {
    DB::beginTransaction();
    try {
      $data = $request->input('attributes') ?? [];
      $this->validateRequestApi(new CreateCountryRequest($data));
      $country = $this->country->create($data);
      $response = ['data' => new CountryTransformer($country)];
      $status = 200;
      DB::commit();
    } catch (Exception $exception) {
      Log::Error($exception);
      DB::rollback();
      $status = $this->getStatusError($exception->getCode());
      $response = ['errors' => $exception->getMessage()];
    }
    return response()->json($response, $status);
  }

  public function update ($criteria, Request $request) {
    DB::beginTransaction();
    try {
      $data = $request->input('attributes') ?? [];
      $this->validateRequestApi(new UpdateCountryRequest($data));
      $params = $this->getParamsRequest($request);
      $country = $this->country->getItem($criteria, $params);
      $this->country->update($country, $data);
      $response = ['data' => new CountryTransformer($country)];
      $status = 200;
      DB::commit();
    } catch (Exception $exception) {
      Log::Error($exception);
      DB::rollback();
      $status = $this->getStatusError($exception->getCode());
      $response = ['errors' => $exception->getMessage()];
    }
    return response()->json($response, $status);
  }

  public function delete ($criteria, Request $request) {
    DB::beginTransaction();
    try {
      $params = $this->getParamsRequest($request);
      $country = $this->country->getItem($criteria, $params);
      $this->country->destroy($country);
      $response = ['data' => true];
      $status = 200;
      DB::commit();
    } catch (Exception $exception) {
      Log::Error($exception);
      DB::rollback();
      $status = $this->getStatusError($exception->getCode());
      $response = ['errors' => $exception->getMessage()];
    }
    return response()->json($response, $status);
  }


}
