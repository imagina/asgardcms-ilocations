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
use Modules\Ilocations\Http\Requests\CreateNeighborhoodRequest;
use Modules\Ilocations\Http\Requests\UpdateNeighborhoodRequest;

// Transformers
use Modules\Ilocations\Transformers\NeighborhoodTransformer;

// Repositories
use Modules\Ilocations\Repositories\NeighborhoodRepository;

class NeighborhoodApiController extends BaseApiController
{

  private $neighborhood;

  public function __construct(NeighborhoodRepository $neighborhood){
    $this->neighborhood = $neighborhood;
  }

  public function index (Request $request) {
    try {
      $params = $this->getParamsRequest($request);
      $neighborhoods = $this->neighborhood->getItemsBy($params);
      $response = ['data' => NeighborhoodTransformer::collection($neighborhoods)];
      $params->page ? $response["meta"] = ["page" => $this->pageTransformer($neighborhoods)] : false;
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
      $neighborhood = $this->neighborhood->getItem($criteria, $params);
      if(!$neighborhood) throw new Exception('Item not found',404);
      $response = ['data' => new NeighborhoodTransformer($neighborhood)];
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
      $this->validateRequestApi(new CreateNeighborhoodRequest($data));
      $neighborhood = $this->neighborhood->create($data);
      $response = ['data' => new NeighborhoodTransformer($neighborhood)];
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
      $this->validateRequestApi(new UpdateNeighborhoodRequest($data));
      $params = $this->getParamsRequest($request);
      $neighborhood = $this->neighborhood->getItem($criteria, $params);
      $this->neighborhood->update($neighborhood, $data);
      $response = ['data' => new NeighborhoodTransformer($neighborhood)];
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
      $neighborhood = $this->neighborhood->getItem($criteria, $params);
      $this->neighborhood->destroy($neighborhood);
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