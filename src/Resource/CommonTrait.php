<?php

namespace FangCaoDi\Resource;

use App\Models\Group\Store;
use Carbon\Carbon;

trait CommonTrait
{

    /**
     * @var Carbon
     */
    protected $date;

    protected function setDate()
    {
        $date = $this->option('date');
        $date = Carbon::parse($date);
        $this->date = $date;
    }


    protected function getStoreConfig()
    {
        $store_id = $this->getStoreId();
        $storeConfigs = $this->storeConfigs();
        if ($store_id == 'all'){
            return $storeConfigs;
        }
        if (!isset($storeConfigs[$store_id])){
            $this->error('Store config not found!');
        }
            return [$store_id,$storeConfigs[$store_id]];
    }

    protected function getStoreId()
    {
        return $store_id = $this->argument('store');
    }

    protected function storeConfigs()
    {
        return $this->config('store_shop_ids') ;
    }

    protected function config($key)
    {
        if (config('fangcaodi.' . $key)) {
            return config('fangcaodi.' . $key);
        }

        $config = include(__DIR__ . '/config/fangcaodi.php');
        if (is_array($config)) {
            if (isset($config[$key])) {
                return $config[$key];
            }
        }
        return null;
    }
}