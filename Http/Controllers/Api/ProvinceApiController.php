<?php

namespace Modules\Ilocations\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Ihelpers\Http\Controllers\Api\BaseApiController;
use Modules\Ilocations\Repositories\ProvinceRepository;
use Modules\Ilocations\Transformers\ProvinceTransformer;
use Exception;
use Log;
use DB;
use Modules\Ilocations\Http\Requests\CreateProvinceRequest;
use Modules\Ilocations\Http\Requests\UpdateProvinceRequest;

class ProvinceApiController extends BaseApiController
{

  private $province;

  public function __construct(ProvinceRepository $province){
    $this->province = $province;
  }

  public function index (Request $request) {
    try {
      $params = $this->getParamsRequest($request);
      $provinces = $this->province->getItemsBy($params);
      $response = ['data' => ProvinceTransformer::collection($provinces)];
      $params->page ? $response["meta"] = ["page" => $this->pageTransformer($provinces)] : false;
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
      $province = $this->province->getItem($criteria, $params);
      if(!$province) throw new Exception('Item not found',404);
      $response = ['data' => new ProvinceTransformer($province)];
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
      $this->validateRequestApi(new CreateProvinceRequest($data));
      $province = $this->province->create($data);
      $response = ['data' => new ProvinceTransformer($province)];
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
      $this->validateRequestApi(new UpdateProvinceRequest($data));
      $params = $this->getParamsRequest($request);
      $province = $this->province->getItem($criteria, $params);
      $this->province->update($province, $data);
      $response = ['data' => new ProvinceTransformer($province)];
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
      $province = $this->province->getItem($criteria, $params);
      $this->province->destroy($province);
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
