<?php
/*
    This request will allow you to, with a given input text, download the link of the audio conversion of your text.
*/


namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Utils\CoreUtils;

function suppressAudioFilesFromMoreThan24Hours(){
    $path = './storage/';
    if ($handle = opendir($path)) {
        while (false !== ($file = readdir($handle))) {
            if ((time()-filectime($path.'/'.$file)) > 86400) {  // 86400 = 60*60*24
                if (preg_match('/\.wav$/i', $file)) {
                unlink($path.'/'.$file);
                }
            }
        }
    }
}

class SpeakingController extends Controller {

    /**
     * Operation speakPost
     *
     * output and audio file .wav from a text
     * 
     * @return Http response
     */
    public function speakPost(Request $request) {
        $params = (array) json_decode(file_get_contents('php://input'), TRUE);

        if (!isset($params['text']) ) {
            return response()->json(array(
                        'code' => 400,
                        'msg'  => 'BAD REQUEST : Missing field text in Speaking Controller'
            ));
            throw new \InvalidArgumentException('Missing the required parameter $text when calling speakPost');
        }
        if (!isset($params['language']) ) {
            $language = 'fr-FR_ReneeVoice';
        }
        else if ($params['language'] == "en-US" ) {
            $language = 'en-US_AllisonVoice';
        }
        else if ($params['language'] == "fr-FR" ) {
            $language = 'fr-FR_ReneeVoice';
        }
        else
            return response()->json(array(
                        'code' => 422,
                        'msg'  => 'BAD REQUEST : field language is unknown, should be one of the list : fr-FR ; en-US'
            ));
        
        $credentials = CoreUtils::loadCredentials();

        $headers = array( 'Content-Type: application/json',
                          'Accept: audio/wav');

       // $data_json = $input['text']; 
        $postdata = array(
            'accept'    => 'audio/wav',
            'text'      => $params['text']
        );
        $data_json = json_encode($postdata);  

        $curlPost = curl_init();
        curl_setopt($curlPost, CURLOPT_POST, 1);
        curl_setopt($curlPost, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curlPost, CURLOPT_URL, $credentials->url . "/v1/synthesize?voice=" . $language); //en-US_AllisonVoice
        curl_setopt($curlPost, CURLOPT_USERPWD, $credentials->username . ':' . $credentials->password);
        curl_setopt($curlPost, CURLOPT_HTTPHEADER, $headers); 
        curl_setopt($curlPost, CURLOPT_POSTFIELDS, $data_json); //"{\"text\":\"hello world\"}"
        // retrieve the answer from the API
        $response  = curl_exec($curlPost);
        suppressAudioFilesFromMoreThan24Hours();
        curl_close($curlPost);
        
        //transcript the json into an array
        $data_json = json_decode($response, true); 
        //check the code if we encounter an error
        if ($data_json['code']>299 ) {
            return response()->json(array(
                        'code' => $data_json['code'],
                        'msg'  => $data_json['error']
            ));
        }
        // Creating file 
        $filePathName = 'storage/' . uniqid() . '-Speaking.wav';
        //if we did not succeed to creat it
        if(!file_put_contents('./'.$filePathName, $response)){
            return response()->json(array(
                        'code' => 300,
                        'msg'  => 'Error putting file'
            ));
        }
        
        // if it encountered no problem
        return response()->json(array(
                    'code' => 200,
                    'msg'  => 'OK',
                    'downloadLink'  =>'https://text-to-speech.api.surirobot.net/' .$filePathName,
                    'data' => $params['text']
        ));
    }

}
