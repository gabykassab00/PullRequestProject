<?php

return [
    'application_name'=>env('GOOGLE_APPLICATION_NAME','Pull Request Project'),
    'client_id' =>env('GOOGLE_CLIENT_ID',''),
    'client_secret'=>env('GOOGLE_CLIENT',''),
    'redirect_uri'=>env('GOOGLE_REDIRECT',''),
    'scopes' => [\Google_Service_Sheets::DRIVE,\Google_Service_Sheets::SPREADSHEETS],
    'access_type' =>'offline',
    'approval_prompt'=>'force',
    'prompt'=>'consent',
    'developer_key'=>env('GOOGLE_DEVELOPER_KEY',''),
    'service'=>[
        'enable'=>env('GOOGLE_SERVICE_ENABLED',false),
        'file'=>env('GOOGLE_SERVICE_ACCOUNT_JSON_LOCATION',storage_path('credentials.json')),

    ],
    'config' =>[],
    'post_spreadsheet_id'=>env('POST_SPREADSHEET_ID'),
    'post_sheet_id' =>env('POST_SHEET_ID'),
];