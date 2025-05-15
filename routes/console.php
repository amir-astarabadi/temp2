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

Artisan::command('test', function(){
    $data = App\Models\Chart::find(11)->metadata['series'][0]['data'];
    foreach($data as $index => $value){
        dump(floatval($value[0]) > floatval($data[$index+1][0]));
        if($index < count($data) - 2 && floatval($value[0]) > floatval($data[$index+1][0])){
            dd('danget');
            dump($index, $value[0], $data[$index+1][0], floatval($value[0]) > floatval($data[$index+1][0]),"*******************************************");
        }
    }
});