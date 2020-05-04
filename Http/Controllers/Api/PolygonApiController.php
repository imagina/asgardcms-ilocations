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
use Modules\Ilocations\Http\Requests\CreatePolygonRequest;
use Modules\Ilocations\Http\Requests\UpdatePolygonRequest;

// Transformers
use Modules\Ilocations\Transformers\PolygonTransformer;

// Repositories
use Modules\Ilocations\Repositories\PolygonRepository;

class PolygonApiController extends BaseApiController
{

  private $polygon;

  public function __construct(PolygonRepository $polygon){
    $this->polygon = $polygon;
  }

  public function index (Request $request) {
    try {
      $params = $this->getParamsRequest($request);
      $polygons = $this->polygon->getItemsBy($params);
      $response = ['data' => PolygonTransformer::collection($polygons)];
      $params->page ? $response["meta"] = ["page" => $this->pageTransformer($polygons)] : false;
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
      $polygon = $this->polygon->getItem($criteria, $params);
      if(!$polygon) throw new Exception('Item not found',404);
      $response = ['data' => new PolygonTransformer($polygon)];
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
      $this->validateRequestApi(new CreatePolygonRequest($data));
      $polygon = $this->polygon->create($data);
      $response = ['data' => new PolygonTransformer($polygon)];
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
      $this->validateRequestApi(new UpdatePolygonRequest($data));
      $params = $this->getParamsRequest($request);
      $polygon = $this->polygon->getItem($criteria, $params);
      $this->polygon->update($polygon, $data);
      $response = ['data' => new PolygonTransformer($polygon)];
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
      $polygon = $this->polygon->getItem($criteria, $params);
      $this->polygon->destroy($polygon);
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