<?php

namespace App\Services;

use App\Repositories\MetricDataRepository;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MetricDataService extends BaseService
{
    protected $metricDataRepository;
    
    public function __construct(MetricDataRepository $metricDataRepository)
    {
        $this->metricDataRepository = $metricDataRepository;
        parent::__construct($metricDataRepository);
    }
    
    /**
     * Get all metricDatas
     */
    public function getAllMetricDatas(array $relations = [])
    {
        return $this->repository->all($relations);
    }
    
    /**
     * Get active metricDatas
     */
    public function getActiveMetricDatas(array $relations = [])
    {
        return $this->metricDataRepository->getActiveMetricDatas($relations);
    }
    
    /**
     * Get metricData by ID
     */
    public function getMetricDataById($id, array $relations = [])
    {
        return $this->repository->find($id, $relations);
    }
    
    /**
     * Create a new metricData
     */
    public function createMetricData(array $data)
    {
        try {
            DB::beginTransaction();
            
            $metricData = $this->create($data);
            
            DB::commit();
            return $metricData;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error creating metricData: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Update an existing metricData
     */
    public function updateMetricData($id, array $data)
    {
        try {
            DB::beginTransaction();
            
            $metricData = $this->update($id, $data);
            
            DB::commit();
            return $metricData;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error updating metricData: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Delete a metricData
     */
    public function deleteMetricData($id)
    {
        try {
            DB::beginTransaction();
            
            $result = $this->delete($id);
            
            DB::commit();
            return $result;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error deleting metricData: ' . $e->getMessage());
            throw $e;
        }
    }
}