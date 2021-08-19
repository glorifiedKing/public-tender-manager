<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ContractResource;
use App\Http\Resources\ContractsCollection;
use App\Imports\ContractsImport;
use App\Jobs\ImportJob;
use App\Models\Contract;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class ContractsController extends Controller
{

    
    /**
     * @OA\Post(
     *      path="/contracts/uploadContracts",     
     *      tags={"Contracts"},
     *      security={ {"bearerAuth": {} }},
     *      summary="Upload excel document of contracts",
     *      description="Returns batchId to monitor upload progress",
     *      @OA\RequestBody(
     *	    required=true,	
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="file",
     *                     type="file"
     *                 ),
     *                 
     *                 example={"file": "file to upload"}
     *             )
     *         )
     *     ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="File Upload in progress. use batchId to see progress"),
     *              @OA\Property(property="status", type="string", example="success"),
     *              @OA\Property(
     *              property="data",
     *              type="object",
     *                  @OA\Property(
     *                  property="batchId",
     *                    type="string",
     *                    example="65-876557886",             
     *                  )
     *              )
     *          )
     *       ),
     *      
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Unauthenticated"),
     *          )
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Server Error",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Server Error"),
     *              @OA\Property(property="status", type="string", example="error"),
     *              @OA\Property(
     *              property="errors",
     *              type="object",
     *                  @OA\Property(
     *                  property="server",
     *                    type="string",
     *                    example="server is unable to process your request at this time. Try again later",             
     *                  )
     *              )
     *          )
     *       ),
     * )
     */
    public function upload_contract(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'file' => 'required|mimes:xls,xlsx',
        ]);
        if ($validator->fails()) {
			
			return response()->json([
				"message" => "the given data was invalid",
				"errors" => $validator->messages()->toArray(),
				"status" => "error"
			],422);
        }

        if ($file = $request->file('file')) {

            $uploadedFilePath = $file->store('public/files');            
            try {
              
                $userBatchIdentifier = auth()->user()->id."-".now()->unix();   
                $batch = Bus::batch([
                    new ImportJob($uploadedFilePath,$userBatchIdentifier,$request->user()->id),
                ])->dispatch();                 
                Cache::put("batch_id_{$userBatchIdentifier}", $batch->id,now()->addWeek());                
                
                return response()->json([               
                    "message" => "File Upload in progress. use batchId to see progress",
                    "data" => ["batchId" => $userBatchIdentifier],
                    "status" => "success"
                ],200);

            }catch(\Exception $e)
            {
                Log::error("file upload error: {$e->getMessage()}"); 
                return response()->json([
                    "message" => "Server Error",
                    "errors" => ['server'=> 'server is unable to process your request at this time. Try again later'],
                    "status" => "error"
                ],500);
            }
            
  
        }
    }

    /**
     * @OA\Post(
     * path="/contracts/getUploadStatus",
     * summary="Get Upload Progress",
     * description="Get The status of upload and import of excel sheet",
     * security={ {"bearerAuth": {} }},
     * tags={"Contracts"},
     * @OA\RequestBody(
     *    required=true,
     *    description="BatchId of the upload",
     *    @OA\JsonContent(
     *       required={"batchId"},
     *       @OA\Property(property="batchId", type="string", example="36-847464585847"),
     *       
     *       
     *    ),
     * ),
     * @OA\Response(
     *    response=422,
     *    description="Invalid Data response",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="the given data was invalid"),
     *       @OA\Property(property="status", type="string", example="error"),
     *       @OA\Property(
     *           property="errors",
     *           type="object",
     *           @OA\Property(
     *              property="batchId",
     *              type="array",
     *              collectionFormat="multi",
     *              @OA\Items(
     *                 type="string",
     *                 example={"The batchId field is required.","The batchId must be a valid."},
     *              )
     *           )
     *        )
     *      )
     *   ),
     * @OA\Response(
     *          response=404,
     *          description="Resource Not Found",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Resource not found"),
     *              @OA\Property(property="status", type="string", example="error"),
     *              @OA\Property(
     *              property="errors",
     *              type="object",
     *                  @OA\Property(
     *                  property="batchId",
     *                  type="string",
     *                  example="batchId does not exist or is already complete",
     *                  
     *                  )
     *             )
     *          )
     *      ),
     *  @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="upload in progress"),
     *              @OA\Property(property="status", type="string", example="success"),
     *              @OA\Property(
     *              property="data",
     *              type="object",
     *                  @OA\Property(
     *                  property="batchId",
     *                    type="string",
     *                    example="76-986687766",             
     *                  ),
     *                  @OA\Property(
     *                  property="currentRow",
     *                    type="string",
     *                    example="1"              
     *                  ),
     *                  @OA\Property(
     *                  property="totalRows",
     *                    type="string",
     *                    example="154676"              
     *                  ),
     *                  @OA\Property(
     *                  property="uploadedRows",
     *                    type="string",
     *                    example="1"              
     *                  ),
     *                  @OA\Property(
     *                  property="status",
     *                    type="string",
     *                    example="in progress"              
     *                  ),
     *              )
     *          )
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Unauthenticated"),
     *          )
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Upload Failed",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Upload Failed"),
     *              @OA\Property(property="status", type="string", example="error"),
     *              @OA\Property(
     *              property="errors",
     *              type="object",
     *                  @OA\Property(
     *                  property="batchId",
     *                    type="string",
     *                    example="44-88474747",             
     *                  ),
     *                  @OA\Property(
     *                  property="status",
     *                    type="string",
     *                    example="failed",             
     *                  ),
     *              )
     *          )
     *       ),
     * )
     */
    public function get_upload_progress(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'batchId' => 'required'
        ]);

        if ($validator->fails()) {
			
			return response()->json([
				"message" => "the given data was invalid",
				"errors" => $validator->messages()->toArray(),
				"status" => "error"
			],422);
        }

       
       if (!Cache::has("batch_id_{$request->batchId}")) {
            return response()->json([
                "message" => "Resource not found",
                "errors" => ['batchId' => 'batchId does not exist or is already complete'],
                "status" => "error"
            ],404);
        }

        if (Cache::has("end_date_{$request->batchId}")) {            
            $totalRows = Cache::has("total_rows_{$request->batchId}") ? Cache::get("total_rows_{$request->batchId}") : 0;
            $uploadedRows = Cache::has("uploaded_rows_{$request->batchId}") ? Cache::get("uploaded_rows_{$request->batchId}") : 0;
            return response()->json([
                "message" => "Upload Complete",
                "data" => ['batchId' => $request->batchId,'uploadedRows' => $uploadedRows,'totalRows' => $totalRows,'status' => 'completed'],
                "status" => "success"
            ],200);
        }

        if (Cache::has("current_row_{$request->batchId}")) {
            $currentRow = Cache::has("current_row_{$request->batchId}") ? Cache::get("current_row_{$request->batchId}") : 0;
            $totalRows = Cache::has("total_rows_{$request->batchId}") ? Cache::get("total_rows_{$request->batchId}") : 0;
            
            return response()->json([
                "message" => "upload in progress",
                "data" => ['batchId' => $request->batchId,'currentRow' => $currentRow,'totalRows' => $totalRows,'status' => 'in progress'],
                "status" => "success"
            ],200);
        }

        //if above doesnt apply then upload didnt start(queue is not started) or it has failed
        return response()->json([
            "message" => "Upload Failed",
            "errors" => ['batchId' => $request->batchId,'status' => 'failed'],
            "status" => "error"
        ],500);
    }


    /**
     * @OA\Get(
     *      path="/contracts/getContract/{id}",
     *      
     *      tags={"Contracts"},
     *      security={ {"bearerAuth": {} }},
     *      summary="Get contract information",
     *      description="Returns contract data",
     *      @OA\Parameter(
     *          name="id",
     *          description="Contract id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="contract record found"),
     *              @OA\Property(property="status", type="string", example="success"),
     *              @OA\Property(property="data",ref="#/components/schemas/Contract")
     * )
     *       ),   
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Unauthenticated"),
     *          )
     *      ),
     *       @OA\Response(
     *          response=404,
     *          description="Resource Not Found",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Resource not found"),
     *              @OA\Property(property="status", type="string", example="error"),
     *              @OA\Property(
     *              property="errors",
     *              type="object",
     *                  @OA\Property(
     *                  property="contractId",
     *                  type="string",
     *                  example="contract does not exist",
     *                  
     *                  )
     *             )
     *          )
     *      )
     * )
     */
    public function get_contract(Request $request,$contractId)
    {
        $contract = Contract::find($contractId);
        if(!$contract)
        {
            return response()->json([
                "message" => "Resource not found",
                "errors" => ['contractId' => 'contract does not exist'],
                "status" => "error"
            ],404);
        }
        $contract->read = true;
        $contract->save();

        return response()->json([
            "message" => "Contract record found",
            "data" => new ContractResource($contract),
            "status" => "success"
        ],200);
    }

    /**
     * @OA\Get(
     *      path="/contracts/getContractReadStatus/{id}",
     *      
     *      tags={"Contracts"},
     *      security={ {"bearerAuth": {} }},
     *      summary="Know if the contract has been read or not",
     *      description="Returns read status of the contract",
     *      @OA\Parameter(
     *          name="id",
     *          description="Contract id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="yes"),
     *              @OA\Property(property="status", type="string", example="success"),
     *              @OA\Property(
     *              property="data",
     *              type="object",
     *                  @OA\Property(
     *                  property="read",
     *                    type="string",
     *                    example="yes",             
     *                  ),
     *                  @OA\Property(
     *                  property="contractId",
     *                    type="integer",
     *                    example="1"              
     *                  ),
     *              )
     *          )
     *       ),    
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Unauthenticated"),
     *          )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Resource Not Found",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Resource not found"),
     *              @OA\Property(property="status", type="string", example="error"),
     *              @OA\Property(
     *              property="errors",
     *              type="object",
     *                  @OA\Property(
     *                  property="contractId",
     *                  type="string",
     *                  example="contract does not exist",
     *                  
     *                  )
     *             )
     *          )
     *      )
     * )
     */
    public function get_contract_read_status(Request $request,$contractId)
    {
        $contract = Contract::find($contractId);
        if(!$contract)
        {
            return response()->json([
                "message" => "Resource not found",
                "errors" => ['contractId' => 'contract does not exist'],
                "status" => "error"
            ],404);
        }
        $contractReadStatus = ($contract->read) ? 'yes' : 'no';

        return response()->json([
            "message" => "$contractReadStatus",
            "data" => ['contractId' => $contractId,'read' => $contractReadStatus],
            "status" => "success"
        ],200);
    }

    /**
     * @OA\Post(
     * path="/contracts/searchContracts",
     * summary="Search through the existing contracts",
     * description="Search the existing contracts based on dateOfAward, Contract amount range, and winning Company",
     * security={ {"bearerAuth": {} }},
     * tags={"Contracts"},
     * @OA\Parameter(
     *          name="page",
     *          description="navigate to a specific page in results",
     *          required=false,
     *          in="query",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     * @OA\RequestBody(
     *    required=true,
     *    description="Search Parameters, you can use 1 or more search parameters",
     *    @OA\JsonContent(
     *       required={},
     *       @OA\Property(property="dateOfAward", type="string", example="2016-05-31",description="the date when the contract was awarded"),
     *       @OA\Property(property="fromContractAmount", type="float", example="4619.78"),
     *       @OA\Property(property="toContractAmount", type="float", example="5000.84"),
     *       @OA\Property(property="winningCompany", type="string", example="EXPM Lda"),
     *       @OA\Property(property="combineQuery", type="boolean", example="false"),
     *       
     *       
     *    ),
     * ),
     * @OA\Response(
     *    response=422,
     *    description="Invalid Data Response",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="the given data was invalid"),
     *       @OA\Property(property="status", type="string", example="error"),
     *       @OA\Property(
     *           property="errors",
     *           type="object",
     *           @OA\Property(
     *              property="dateOfAward",
     *              type="array",
     *              collectionFormat="multi",
     *              @OA\Items(
     *                 type="string",
     *                 example={"The date of award field is required when none of from contract amount / winning company are present.","The date of award is not a valid date."},
     *              )
     *           ),
     *           @OA\Property(
     *              property="winningCompany",
     *              type="array",
     *              collectionFormat="multi",
     *              @OA\Items(
     *                 type="string",
     *                 example={"The winning company field is required when none of from contract amount / date of award are present."},
     *              )
     *           ),
     *           @OA\Property(
     *              property="fromContractAmount",
     *              type="array",
     *              collectionFormat="multi",
     *              @OA\Items(
     *                 type="string",
     *                 example={"The from contract amount must be a number","The from contract amount field is required when to contract amount is present"},
     *              )
     *           ), 
     *           @OA\Property(
     *              property="toContractAmount",
     *              type="array",
     *              collectionFormat="multi",
     *              @OA\Items(
     *                 type="string",
     *                 example={"The to contract amount must be a number","The to contract amount field is required when from contract amount is present"},
     *              )
     *           ),  
     *        )
     *      )
     *   ), 
     *  @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(     *             
     *              @OA\Property(
     *              property="data",
     *              type="array",
     *              collectionFormat="multi",
     *              @OA\Items(
     *                 type="object",
     *                 ref="#/components/schemas/Contract"
     *                 )                               
     *              ),
     *              @OA\Property(
     *                  property="links",
     *                  type="object",
     *                      @OA\Property(
     *                        property="self",
     *                        type="string",
     *                        example="link-value",                  
     *                      ),
     *                      @OA\Property(
     *                        property="first",
     *                        type="string",
     *                        example="http://localhost:8000/api/contracts/searchContracts?page=1",                  
     *                      ),
     *                      @OA\Property(
     *                        property="last",
     *                        type="string",
     *                        example="http://localhost:8000/api/contracts/searchContracts?page=92",                  
     *                      ),
     *                      @OA\Property(
     *                        property="prev",
     *                        type="string",
     *                        example="http://localhost:8000/api/contracts/searchContracts?page=90",                  
     *                      ),
     *                      @OA\Property(
     *                        property="next",
     *                        type="string",
     *                        example="http://localhost:8000/api/contracts/searchContracts?page=92",                  
     *                      ),
     *                  
     *              )
     *          )
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Unauthenticated"),
     *          )
     *      ),
     * )
     */
    public function search_contracts(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'dateOfAward' => 'nullable|date|required_without_all:fromContractAmount,winningCompany',
            'fromContractAmount' => 'nullable|numeric|required_with:toContractAmount',
            'toContractAmount' => 'nullable|numeric|required_with:fromContractAmount',
            'winningCompany' => 'nullable|required_without_all:fromContractAmount,dateOfAward',
            'combineQuery' => 'nullable|boolean',
            'page'         => 'nullable|integer' 
        ]);

        if ($validator->fails()) {
			
			return response()->json([
				"message" => "the given data was invalid",
				"errors" => $validator->messages()->toArray(),
				"status" => "error"
			],422);
        }
        $combineQuery = ($request->combineQuery) ? true : false;

        
        //let us first handle when the query doesnt need to be combined
        if(!$combineQuery)
        {
            if($request->dateOfAward)
            {
                $dateOfAward = Carbon::parse($request->dateOfAward);               
                $query = Contract::whereDate('dataCelebracaoContrato',$dateOfAward->format('Y-m-d'));
                
            }
            else if($request->fromContractAmount)
            {
                $query = Contract::whereBetween('precoContratual',[$request->fromContractAmount,$request->toContractAmount]);
            }
            else if($request->winningCompany)
            {
                $query = Contract::where('adjudicatarios','like',"%$request->winningCompany%");
            }
            return new ContractsCollection($query->paginate(20));
        }

        else if($combineQuery)
        {
            $query = Contract::query();
            $result = (clone $query);
            if($request->dateOfAward)
            {
                $dateOfAward = Carbon::parse($request->dateOfAward);               
                $result = $query->whereDate('dataCelebracaoContrato',$dateOfAward->format('Y-m-d'));                
                
            }
            if($request->fromContractAmount)
            {
                $result = $query->whereBetween('precoContratual',[$request->fromContractAmount,$request->toContractAmount]);
            }
            if($request->winningCompany)
            {
                
                $result = $query->where('adjudicatarios','like',"%$request->winningCompany%");
                
            }

            return new ContractsCollection($result->paginate(20));
        }
        
        
        
    }
}
