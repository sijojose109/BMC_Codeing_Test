<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use App\Models\Analytics;

class DbArchival extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:archival';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // return 0;

        DB::beginTransaction();

        try 
        {
            $date_before_6_months = date("Y-m-d",strtotime("-6 month"));

            Analytics::where('date','<', $date_before_6_months)->chunk(1000, function ($analytics) {

                $archived_data = array();
              
                foreach ($analytics as $analytics_row) 
                {

                    $archived_data[] = ['user_id'=>$analytics_row->user_id, 'date'=> $analytics_row->date, 'count'=>1, 'ip_address'=>$analytics_row->ip_address, 'referer'=>$analytics_row->referer, 'country'=>$analytics_row->country, 'created_at'=>$analytics_row->created_at, 'updated_at'=>$analytics_row->updated_at];

                }

                DB::table('analytics_archived')->insert($archived_data);

            });

            Analytics::where('date','<', $date_before_6_months)->delete();

            DB::commit();
            
        }
        catch (\Exception $e) 
        {
            echo $e->getMessage();
            DB::rollback();
        }

        return true;

    }
}
