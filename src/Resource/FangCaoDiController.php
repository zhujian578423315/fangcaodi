<?php

namespace FangCaoDi\Resource;

use App\Leyao\Contracts\Commerce\Models\Order\OrderInterface;
use App\Models\Commerce\Order\Order;
use Carbon\Carbon;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;

class FangCaoDiController extends Controller
{
    protected $date;

    protected $store;

    protected $shop_info;

    const VIPCODE = '';

    function __construct($date, $store, $shop_info)
    {
        $this->date = $date;
        $this->store = $store;
        $this->shop_info = $shop_info;
    }

    public function generateFile()
    {
        $orders = Order::where('store_id', $this->store->id)
            ->whereDate('created_at', $this->date)
            ->where('state', OrderInterface::STATE_FULFILLED)
            ->get();
        $contents_array = array();
        foreach ($orders as $order) {
            if ($order->items_total == 0) {
                continue;
            }
            try {
                array_push($contents_array, $this->contents($order));
            } catch (\Exception $e) {
                Log::info('订单上传失败，订单号'.$order->id.' error:'.$e->getMessage());
            }
        }
        if (count($contents_array) == 0) {
            return $this->noOrderContent();
        }
        return implode($contents_array, "\r\n");
    }

    protected function contents($order)
    {
        $TAB = "\t";
        $order_info =
            [
                $this->shop_info['shop_id'],
                $this->shop_info['tillid'],
                $this->date->format('Y/m/d'),
                Carbon::parse($order->created_at)->format('Hi'),
                $this->docnoGenerate($order->id),
                $this->shop_info['plu'],
                self::VIPCODE,
                $this->priceFormate($order->total),
                '0.00',
                '0.00',
                '0.00',
                $this->priceFormate($order->adjustments_total),
                $this->priceFormate($order->total),
                $order->number
            ];
        return implode($TAB, $order_info);
    }

    protected function noOrderContent()
    {
        $TAB = "\t";
        $order_info =
            [
                $this->shop_info['shop_id'],
                $this->shop_info['tillid'],
                $this->date->format('Y/m/d'),
                '0000',
                's' . $this->date->format('Ymd') . '0',
                $this->shop_info['plu'],
                self::VIPCODE,
                '0.00',
                '0.00',
                '0.00',
                '0.00',
                '0.00',
                '0.00',
                '0',
            ];
        return implode($TAB, $order_info);
    }

    protected function docnoGenerate($id)
    {
        $remain_num = 9 - strlen($id);
        return 'S' . implode(array_fill(0, $remain_num, 0), '') . $id;
    }


    protected function priceFormate($price)
    {
        return sprintf('%0.2f', abs((float)$price));
    }

}
