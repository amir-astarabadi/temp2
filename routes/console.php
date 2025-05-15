<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
// 
Artisan::command('test', function(){
    $d = App\Models\Chart::find(13)->metadata['series'];
    foreach($d as $data => $series){
        // dump($data, $series['data'][0]);
        foreach($series['data'] as $index => $value){
            if($index < count($series['data']) - 2 && floatval($value[0]) > floatval($series['data'][$index+1][0])){
                dd('danget');
                dump($index, $value[0], $series['data'][$index+1][0], floatval($value[0]) > floatval($series['data'][$index+1][0]),"*******************************************");
            }
        }
    }
});