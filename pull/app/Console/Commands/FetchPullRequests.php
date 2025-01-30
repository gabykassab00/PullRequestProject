<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Revolution\Google\Sheets\Facades\Sheets;
use App\Models\FourteenDaysOld;
use App\Models\ReviewRequired;  
use App\Models\StatusSuccess;  
use App\Models\NoReviews;       

class FetchPullRequests extends Command
{

    protected $signature = 'github:fetch-pull-requests';
    protected $description = 'fetch pull requests from github and save results into txt files';

    public function handle(){
        $repository = 'woocommerce/woocommerce';
        $baseUrl = "https://api.github.com/search/issues";


        $fourteendaysago = now()->subDays(14)->toDateString();

        $queries = [
            '14-days-old'=>"repo:$repository is:pr is:open created:<=$fourteendaysago",
            "review-required"=>"repo:$repository is:pr is:open review:required",
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
            $githubToken = env('GITHUB_TOKEN');
            $response = HTTP::withHeaders([
                'Authorization' => "token $githubToken", 
                'Accept' => 'application/vnd.github.v3+json',
                'verify'=>false,
            ])->get($baseUrl,['q'=>$query]);


            if($response->status() == 403 && $retry < 3 ){
                $waittime = 2 ** $retry;
                $this->warn("you have reached the rate limit");
                sleep($waittime);
                return $this->fetchandSave($baseUrl,$query,$filename,$retry+1);
            }

            if($response->failed()){
                $this->error("failing in fetching data for $filename:{$response->status()}");
                return;
            }

            $pullrequest = $response->json()['items']??[];
            $data = array_map(function($parse){
                return [$parse['number'],$parse['title'],$parse['html_url']];
            },$pullrequest);

            $filedata = array_map(fn($row)=>implode(',',$row),$data);
            Storage::disk('local')->put("$filename.txt",implode(PHP_EOL,$filedata));
            $this->info("the data has been saved to $filename.txt");

            if(!empty($data)){
                $this->savingToSheets($data,$filename);
                $this->savingToDatabase($data, $filename); 
            }else {
                $this->info("there is no pull request for $filename");
            }
        }catch(Exception $e){
            $this->error("error in fetching the data for $filename :{$e->getMessage()}");
        }
    }

    private function savingToSheets($data,$category){
        try{
            $speadsheetId = config('google.post_spreadsheet_id');
            $sheetName = str_replace([' ','-'],'_',$category);

            Sheets::spreadsheet($speadsheetId)
            ->sheet($sheetName)
            ->range("A1:C")
            ->append(array_merge([['Pull Request Number',' Pull Request Title','Pull Request Link']],$data));

            $this->info("data saved perfectly in $sheetName");
        }catch(Exception $e){
            $this->error("error while saving the data to google sheets: {$e->getMessage()}");
        }
    }
}
