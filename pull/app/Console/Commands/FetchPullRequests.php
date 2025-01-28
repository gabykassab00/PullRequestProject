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
        $repository = 'woocommerce/woocommerce';
        $baseUrl = "https://api.github.com/search/issues";

        $queries = [
            '14-days-old'=>"repo:$repository is:pr is:open created:<=14-days",
            "review-required"=>"repo:$reposiroty is:pr is:open review:required",
            "status-success"=>"repo:$repository is:pr is:open status:success",
            "no-reviews" =>"repo:$repository is:pr is:open review:none",
        ];

        foreach ($queries as $filename=>$query){
            $this->fetchandSave($baseUrl,$query,$filename);
        }
        $this->info('pull request has been fetched as it should be ');
    }

    private function fetchandSave($baseUrl,$query,$filename){
        try {
            $response = HTTP::withOptions([
                'verify'=>false,
            ])->get($baseUrl,['q'=>$query]);

            if($response->failed()){
                $this->error("failing in fetching data for $filename:{$response->status()}");
                return;
            }

            $pullrequest = $response->json()['items']??[];
            $data = array_map(function($parse){
                return "{$parse['number']},{$parse['title']},{$parse['html_url']}";
            },$pullrequest);
            Storage::disk('local')->put("$filename.txt",implode(PHP_EOL,$data));
            $this->info("the data has been saved to $filename.txt");
        }
    }
}
