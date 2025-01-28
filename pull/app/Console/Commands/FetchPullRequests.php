<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class FetchPullRequests extends Command
{

    protected $signature = 'github:fetch-pull-requests';
    protected $description = 'fetch pull requests from github and save results into txt files';

    public function handle(){
        
    }
}
