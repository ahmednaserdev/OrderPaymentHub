<?php
namespace App\Services\Order;

use App\Enums\OrderStatus;
use App\Models\Order;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderService
{

    public function getOrders(array $filters)
    {
        $query = Order::query();
        $this->applyFilters($query, $filters);
        return $query->paginate(10);
    }

    public function createOrder(array $data)
    {
        return DB::transaction(function () use ($data) {
            try {
                $total = collect($data['items'])->sum(function ($item) {
                    return $item['quantity'] * $item['price'];
                });

                $order = Order::create([
                    'items'   => $data['items'],
                    'user_details'   => $data['user_details'],
                    'total'   => $total,
                    'status'  => OrderStatus::PENDING->value,
                ]);

                DB::commit();
                return $order;
            } catch (Exception $e) {
                DB::rollBack();
                Log::error('Error creating order: ' . $e->getMessage());
                throw $e;
            }
        });
    }

    public function updateOrder(Order $order, array $data)
    {
        return DB::transaction(function () use ($order, $data) {
            try {

                if (isset($data['status'])) {
                    $status = OrderStatus::from($data['status']);
                    $order->update(['status' => $status->value]);
                }

                if (isset($data['items'])) {
                    $total = collect($data['items'])->sum(function ($item) {
                        return $item['quantity'] * $item['price'];
                    });

                    $order->update([
                        'items' => $data['items'],
                        'total' => $total,
                    ]);
                }

                if (isset($data['user_details'])) {
                    $order->update([
                        'user_details' => $data['user_details'],
                    ]);
                }

                DB::commit();
                return $order;
            } catch (Exception $e) {
                DB::rollBack();
                Log::error('Error updating order: ' . $e->getMessage());
                throw new Exception('Failed to update order details.');
            }
        });
    }

    public function deleteOrder(Order $order)
    {
        return DB::transaction(function () use ($order) {
            try {
                if ($order->payments()->exists()) {
                    throw new Exception('Cannot delete order with associated payments');
                }

                $order->delete();
                DB::commit();
                return true;
            } catch (Exception $e) {
                DB::rollBack();
                Log::error('Error deleting order: ' . $e->getMessage());
                throw $e;
            }
        });
    }


    private function applyFilters(Builder $query, array $filters): void
    {
        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
    }

}
