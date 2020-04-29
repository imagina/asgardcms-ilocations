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
use Modules\Ilocations\Http\Requests\CreateGeozonesRequest;
use Modules\Ilocations\Http\Requests\UpdateGeozonesRequest;

// Transformers
use Modules\Ilocations\Transformers\GeozoneTransformer;

// Repositories
use Modules\Ilocations\Repositories\GeozonesRepository;

class GeozoneApiController extends BaseApiController
{

  private $geozone;

  public function __construct(GeozonesRepository $geozone){
    $this->geozone = $geozone;
  }

  /**
   * @param Request $request
   * @return mixed
   */
  public function index (Request $request) {
    try {
      $params = $this->getParamsRequest($request);
      $geozones = $this->geozone->getItemsBy($params);
      $response = ['data' => GeozoneTransformer::collection($geozones)];
      $params->page ? $response["meta"] = ["page" => $this->pageTransformer($geozones)] : false;
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
      $geozone = $this->geozone->getItem($criteria, $params);
      if(!$geozone) throw new Exception('Item not found',404);
      $response = ['data' => new GeozoneTransformer($geozone)];
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
      $this->validateRequestApi(new CreateGeozonesRequest($data));
      $geozone = $this->geozone->create($data);
      $response = ['data' => new GeozoneTransformer($geozone)];
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
      $this->validateRequestApi(new UpdateGeozonesRequest($data));
      $params = $this->getParamsRequest($request);
      $geozone = $this->geozone->getItem($criteria, $params);
      $this->geozone->update($geozone, $data);
      $response = ['data' => new GeozoneTransformer($geozone)];
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
      $geozone = $this->geozone->getItem($criteria, $params);
      $this->geozone->destroy($geozone);
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