<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Utils\CoreUtils;

class SpeakingController extends Controller {

    /**
     * Operation speakPost
     *
     * output and audio file .wav from a text
     * 
     * @return Http response
     */
    public function speakPost(Request $request) {
        $input = $request->all();
        if (!isset($input['text']) ) {
            throw new \InvalidArgumentException('Missing the required parameter $text when calling speakPost');
        }
        if (!isset($input['language']) ) {
            $language = 'fr-FR_ReneeVoice';
        }
        else
            $language = ($input['language']);
        
        $credentials = CoreUtils::loadCredentials();

/// this username and password are in resources/credentials/surirobotTTS.json 
    //but I don't know how to access the directory resources
        //$username = 'cbe6f058-0b37-4e9d-83a2-d8da15a04cf4';
        //$password = 'TWi3NCHBh4nO';
        $headers = array( 'Content-Type: application/json',
                          'Accept: audio/wav');

        //$filepath = CoreUtils::saveFile($data_json); 
        $data_json = json_decode($input['text'], true); 
        $postdata = array(
            'accept'    => 'audio/wav',
            'text'      => $data_json['text']
        );
        $data_json = json_encode($postdata); 

        $curlPost = curl_init();
        curl_setopt($curlPost, CURLOPT_POST, 1);
        curl_setopt($curlPost, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curlPost, CURLOPT_URL, $credentials->url . "/v1/synthesize?voice=" . $language); //en-US_AllisonVoice
        curl_setopt($curlPost, CURLOPT_USERPWD, $credentials->username . ':' . $credentials->password);
        curl_setopt($curlPost, CURLOPT_HTTPHEADER, $headers); 
        curl_setopt($curlPost, CURLOPT_POSTFIELDS, $data_json); //"{\"text\":\"hello world\"}"
        $response  = curl_exec($curlPost);
        curl_close($curlPost);

        $data_json = json_decode($response, true); 
        if ($data_json['code']>299 ) {
            return response()->json(array(
                        'code' => $data_json['code'],
                        'msg'  => $data_json['error']
            ));
        }
        $filePathName = './storage/' . uniqid() . '-Speaking.wav';
        if(!file_put_contents($filePathName, $response)){
            return response()->json(array(
                        'code' => '300',
                        'msg'  => 'Error putting file'
            ));
        }
        return response()->json(array(
                    'code' => 200,
                    'msg'  => 'OK',
                    'downloadLink'  => $filePathName,
                    'data' => json_decode($input['text'], true)['text']
        ));
    }

}

/*

public function speakPost(Request $request) {
        $input = $request->all();
        if (!isset($input['text']) || !isset($input['nameFile'])) {
            throw new \InvalidArgumentException('Missing the required parameter $text when calling speakPost');
        }

/// this username and password are in resources/credentials/surirobotTTS.json 
    //but I don't know how to access the directory resources
        $username = 'cbe6f058-0b37-4e9d-83a2-d8da15a04cf4';
        $password = 'TWi3NCHBh4nO';
        $headers = array( 'Content-Type: application/json',
                          'Accept: audio/wav');
        $data_json = json_decode($input['text']);    
        //or
        //$data_json = = $input['text'];

        //$filepath = CoreUtils::saveFile($data_json); 

        $postdata = array(
            'accept'    => 'audio/wav',
            'text'      => $data_json->{'text'}
        );

        $curlPost = curl_init();
        curl_setopt($curlPost, CURLOPT_POST, 1);
        curl_setopt($curlPost, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curlPost, CURLOPT_URL, "https://stream.watsonplatform.net/text-to-speech/api/v1/synthesize?voice=fr-FR_ReneeVoice"); //en-US_AllisonVoice
        curl_setopt($curlPost, CURLOPT_USERPWD, $username.':'.$password);
        curl_setopt($curlPost, CURLOPT_HTTPHEADER, $headers); 
        curl_setopt($curlPost, CURLOPT_POSTFIELDS, $postdata); //"{\"text\":\"hello world\"}"
        $response  = curl_exec($curlPost);
        curl_close($curlPost);

        file_put_contents('./' . $input['nameFile'] . '.wav', $response);
        return response()->json(array(
                    'code' => 200,
                    'msg'  => 'OK'
        ));
    }

    */




/****
working


public function speakPost(Request $request) {
        $input = $request->all();
        if (!isset($input['text']) ) {
            throw new \InvalidArgumentException('Missing the required parameter $text when calling speakPost');
        }

/// this username and password are in resources/credentials/surirobotTTS.json 
    //but I don't know how to access the directory resources
        $username = 'cbe6f058-0b37-4e9d-83a2-d8da15a04cf4';
        $password = 'TWi3NCHBh4nO';
        $headers = array( 'Content-Type: application/json',
                          'Accept: audio/wav');

        //$filepath = CoreUtils::saveFile($data_json); 

        $postdata = array(
            'accept'    => 'audio/wav',
            'text'      => $input['text']
        );
        $data_json = json_encode($postdata); 

        $curlPost = curl_init();
        curl_setopt($curlPost, CURLOPT_POST, 1);
        curl_setopt($curlPost, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curlPost, CURLOPT_URL, "https://stream.watsonplatform.net/text-to-speech/api/v1/synthesize?voice=fr-FR_ReneeVoice"); //en-US_AllisonVoice
        curl_setopt($curlPost, CURLOPT_USERPWD, $username.':'.$password);
        curl_setopt($curlPost, CURLOPT_HTTPHEADER, $headers); 
        curl_setopt($curlPost, CURLOPT_POSTFIELDS, $data_json); //"{\"text\":\"hello world\"}"
        $response  = curl_exec($curlPost);
        curl_close($curlPost);

        file_put_contents('./' . uniqid() . '-Speaking.wav', $response);
        return response()->json(array(
                    'code' => 200,
                    'msg'  => 'OK',
                    'data' => $input['text']
        ));
    }


    */