<?php

namespace App\Imports;

use App\Models\Contract;
use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Events\AfterImport;
use Maatwebsite\Excel\Events\BeforeImport;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\SkipsOnError;

class ContractsImport implements ToModel,SkipsEmptyRows,WithHeadingRow,WithEvents,ShouldQueue, WithChunkReading,SkipsOnError
{
    use Importable;
    public $batchId; 
    private $UserId;

    public function __construct(string $batch,$userId)
    {
        $this->batchId = $batch;
        $this->UserId = $userId;
    }
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        Cache::increment("current_row_{$this->batchId}");
        Cache::increment("uploaded_rows_{$this->batchId}");

        return new Contract([
            'idcontrato' => $row['idcontrato'] ,
            'nAnuncio' => $row['nAnuncio'],
            'tipoContrato' => $row['tipoContrato'],
            'tipoprocedimento' => $row['tipoprocedimento'],
            'objectoContrato' => $row['objectoContrato'],
            'adjudicantes' => $row['adjudicantes'],
            'adjudicatarios' => $row['adjudicatarios'],
            'dataPublicacao' => Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['dataPublicacao']))->format('Y-m-d'),//$row['dataPublicacao'],
            'dataCelebracaoContrato' => Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['dataCelebracaoContrato']))->format('Y-m-d'),//$row['dataCelebracaoContrato'],
            'precoContratual' => $row['precoContratual'],
            'cpv' => $row['cpv'],
            'prazoExecucao' => $row['prazoExecucao'],
            'localExecucao' => $row['localExecucao'],
            'fundamentacao' => $row['fundamentacao'],
            'user_id' => $this->UserId,
            
        
        ]);
    }

    public function chunkSize(): int
    {
        return 5000;
    }

    public function registerEvents(): array
    {
        return [
            BeforeImport::class => function (BeforeImport $event) {
                $totalRows = $event->getReader()->getTotalRows();

                if (filled($totalRows)) {
                    Cache::put("total_rows_{$this->batchId}", array_values($totalRows)[0],now()->addWeek());
                    Cache::put("start_date_{$this->batchId}", now()->unix(),now()->addWeek());
                }
            },
            AfterImport::class => function (AfterImport $event) {
                cache(["end_date_{$this->batchId}" => now()], now()->addWeek());
                
            },
        ];
    }

        /**
     * @param \Throwable $e
     */
    public function onError(\Throwable $e)
    {
        Log::error("import error {$e->getMessage()}");
        Cache::decrement("uploaded_rows_{$this->batchId}");
    }

}
