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
use Modules\Ilocations\Http\Requests\CreateCityRequest;
use Modules\Ilocations\Http\Requests\UpdateCityRequest;

// Transformers
use Modules\Ilocations\Transformers\CityTransformer;

// Repositories
use Modules\Ilocations\Repositories\CityRepository;


class CityApiController extends BaseApiController
{

  private $city;
  
  public function __construct(CityRepository $city){
    $this->city = $city;
  }

  /**
   * @param Request $request
   * @return mixed
   */
  public function index (Request $request) {
    try {
      $params = $this->getParamsRequest($request);
      $cities = $this->city->getItemsBy($params);
      $response = ['data' => CityTransformer::collection($cities)];
      $params->page ? $response["meta"] = ["page" => $this->pageTransformer($cities)] : false;
      $status = 200;
    } catch (Exception $exception) {
      Log::Error($exception);
      $status = $this->getStatusError($exception->getCode());
      $response = ['errors' => $exception->getMessage()];
    }
    return response()->json($response, $status);
  }

  /**
   * @param $criteria
   * @param Request $request
   * @return mixed
   */
  public function show ($criteria, Request $request) {
    try {
      $params = $this->getParamsRequest($request);
      $city = $this->city->getItem($criteria, $params);
      if(!$city) throw new Exception('Item not found',404);
      $response = ['data' => new CityTransformer($city)];
      $status = 200;
    } catch (Exception $exception) {
      Log::Error($exception);
      $status = $this->getStatusError($exception->getCode());
      $response = ['errors' => $exception->getMessage()];
    }
    return response()->json($response, $status);
  }

  /**
   * @param Request $request
   * @return mixed
   */
  public function create (Request $request) {
    DB::beginTransaction();
    try {
      $data = $request->input('attributes') ?? [];
      $this->validateRequestApi(new CreateCityRequest($data));
      $city = $this->city->create($data);
      $response = ['data' => new CityTransformer($city)];
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

  /**
   * @param $criteria
   * @param Request $request
   * @return mixed
   */
  public function update ($criteria, Request $request) {
    DB::beginTransaction();
    try {
      $data = $request->input('attributes') ?? [];
      $this->validateRequestApi(new UpdateCityRequest($data));
      $params = $this->getParamsRequest($request);
      $city = $this->city->getItem($criteria, $params);
      $this->city->update($city, $data);
      $response = ['data' => new CityTransformer($city)];
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

  /**
   * @param $criteria
   * @param Request $request
   * @return mixed
   */
  public function delete ($criteria, Request $request) {
    DB::beginTransaction();
    try {
      $params = $this->getParamsRequest($request);
      $city = $this->city->getItem($criteria, $params);
      $this->city->destroy($city);
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
