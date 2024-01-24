<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FixStatesDocumentToDocuments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
//        $results = DB::table('documents')->select('id','state_type_id')->whereDate('date_of_issue','>=', '2021-07-16')
//            ->where('state_type_id','!=','11')
//            ->where('state_type_id','!=','13')
//            ->where('state_type_id','!=','07')
//            ->where('state_type_id','!=','09')
//            ->get();
//
//        $i = 1;
//        foreach ($results as $result){
//            DB::table('documents')
//                ->where('id',$result->id)
//                ->update([
//                    "state_type_id" => "01"
//                ]);
//            $i++;
//        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
