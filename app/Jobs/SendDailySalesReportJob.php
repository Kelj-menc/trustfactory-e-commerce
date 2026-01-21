<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

use App\Mail\DailySalesReportMail;
use App\Models\Order;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;

class SendDailySalesReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //group sales by product and sum quantities and total price for today
        $sales = Order::whereDate('created_at', now())
            ->select('product_name', 
                DB::raw('SUM(quantity) as total_quantity'), 
                DB::raw('SUM(quantity * price_at_purchase) as total_price'))
            ->groupBy('product_name')
            ->get();

        $totalRevenue = $sales->sum('total_price');

        // send email to admin if there are sales
        if ($sales->isNotEmpty()) {
            Mail::to('admin@example.com')->send(new DailySalesReportMail($sales, $totalRevenue));
        }
    }
}
