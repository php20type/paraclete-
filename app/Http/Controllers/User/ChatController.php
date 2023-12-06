<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Admin\LicenseController;
use App\Services\Statistics\UserService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\URL;
use Illuminate\Http\Request;
use Orhanerday\OpenAi\OpenAi;
use App\Services\Service;
use App\Models\SubscriptionPlan;
use App\Models\FavoriteChat;
use App\Models\ChatHistory;
use App\Models\Chat;
use App\Models\User;
use Log;	
use App\Library\URLFetcher;	
use Illuminate\Support\Facades\Storage;	
use App\Models\Voice;	
use App\Services\AzureTTSService;
use App\Models\ChatTemplates;


class ChatController extends Controller
{
    private $api;
    private $inst;	

    public function __construct()
    {
        $this->api = new LicenseController();
        $this->inst = 'Instructions: It\'s possible that the question or instruction, or just a portion of it, requires relevant information from the internet to give a satisfactory answer or complete the task. It\'s possible that the question or instruction, or just a portion of it, requires relevant information from the internet to give a satisfactory answer or complete the task. I\'m providing you with the necessary information already obtained from the internet below. This sets the context for addressing the question or fulfilling the instruction, so you don\'t need to access the internet to answer my question or fulfill my instruction. Write a comprehensive reply to the given question or instruction using the information provided below in the best way you can. Ensure to cite results using [[NUMBER](URL)] notation after the reference. If the provided information from the internet refers to multiple subjects with the same name, write separate answers for each subject.

						A strict requirement for you is that if the below information I provide does not contain the information you need to address the question or fulfill the instruction, just respond \'The search results do not contain the necessary content. Please try again with different query and/or search options (e.g., number of search results, search engine, etc.).\'

						Now, write a comprehensive reply to the given question or instruction with this information: You can provide a summarized answer based on all search results. You need to understand each search result separately with NUMBER[N] and then provide the final answer.</br>';
    }

    /** 
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {   
        if (session()->has('message_code')) {
            session()->forget('message_code');
        }

        $favorite_chats = Chat::select('chats.*', 'favorite_chats.*')->where('favorite_chats.user_id', auth()->user()->id)->join('favorite_chats', 'favorite_chats.chat_code', '=', 'chats.chat_code')->where('status', true)->orderBy('category', 'asc')->get();    
        $user_chats = FavoriteChat::where('user_id', auth()->user()->id)->pluck('chat_code');     
        $other_chats = Chat::whereNotIn('chat_code', $user_chats)->where('status', true)->orderBy('category', 'asc')->get();                 
        
        return view('user.chat.index', compact('favorite_chats', 'other_chats'));
    }


    /**
	*
	* Process Input Text
	* @param - file id in DB
	* @return - confirmation
	*
	*/
	public function process(Request $request) 
    {       
        # Check if user has access to the chat bot
        $template = Chat::where('chat_code', $request->chat_code)->first();
        if (auth()->user()->group == 'user') {
            if (config('settings.chat_feature_user') == 'allow') {
                if (config('settings.chats_access_user') != 'all' && config('settings.chats_access_user') != 'premium') {
                    if (is_null(auth()->user()->member_of)) {
                        if ($template->category == 'professional' && config('settings.chats_access_user') != 'professional') {                       
                            $data['status'] = 'error';
                            $data['message'] = __('This Ai chat assistant is not available for your account, subscribe to get a proper access');
                            return $data;                        
                        } else if($template->category == 'premium' && (config('settings.chats_access_user') != 'premium' && config('settings.chats_access_user') != 'all')) {
                            $data['status'] = 'error';
                            $data['message'] = __('This Ai chat assistant is not available for your account, subscribe to get a proper access');
                            return $data;
                        } else if(($template->category == 'standard' || $template->category == 'all') && (config('settings.chats_access_user') != 'professional' && config('settings.chats_access_user') != 'standard')) {
                            $data['status'] = 'error';
                            $data['message'] = __('This Ai chat assistant is not available for your account, subscribe to get a proper access');
                            return $data;
                        }

                    } else {
                        $user = User::where('id', auth()->user()->member_of)->first();
                        $plan = SubscriptionPlan::where('id', $user->plan_id)->first();
                        if ($plan->chats != 'all' && $plan->chats != 'premium') {          
                            if ($template->category == 'premium' && ($plan->chats != 'premium' && $plan->chats != 'all')) {
                                $status = 'error';
                                $message =  __('Your team subscription does not include support for this chat assistant category');
                                return response()->json(['status' => $status, 'message' => $message]); 
                            } else if(($template->category == 'standard' || $template->category == 'all') && ($plan->chats != 'standard' && $plan->chats != 'all')) {
                                $status = 'error';
                                $message =  __('Your team subscription does not include support for this chat assistant category');
                                return response()->json(['status' => $status, 'message' => $message]); 
                            } else if($template->category == 'professional' && $plan->chats != 'professional') {
                                $status = 'error';
                                $message =  __('Your team subscription does not include support for this chat assistant category');
                                return response()->json(['status' => $status, 'message' => $message]); 
                            }                  
                        }
                    }
                    
                }                
            } else {
                if (is_null(auth()->user()->member_of)) {
                    $status = 'error';
                    $message = __('Ai chat assistant feature is not available for free tier users, subscribe to get a proper access');
                    return response()->json(['status' => $status, 'message' => $message]);
                } else {
                    $user = User::where('id', auth()->user()->member_of)->first();
                    $plan = SubscriptionPlan::where('id', $user->plan_id)->first();
                    if ($plan->chats != 'all' && $plan->chats != 'premium') {          
                        if ($template->category == 'premium' && ($plan->chats != 'premium' && $plan->chats != 'all')) {
                            $status = 'error';
                            $message =  __('Your team subscription does not include support for this chat assistant category');
                            return response()->json(['status' => $status, 'message' => $message]); 
                        } else if(($template->category == 'standard' || $template->category == 'all') && ($plan->chats != 'standard' && $plan->chats != 'all')) {
                            $status = 'error';
                            $message =  __('Your team subscription does not include support for this chat assistant category');
                            return response()->json(['status' => $status, 'message' => $message]); 
                        } else if($template->category == 'professional' && $plan->chats != 'professional') {
                            $status = 'error';
                            $message =  __('Your team subscription does not include support for this chat assistant category');
                            return response()->json(['status' => $status, 'message' => $message]); 
                        }                  
                    }
                }
                      
            }
        } elseif (auth()->user()->group == 'subscriber') {
            $plan = SubscriptionPlan::where('id', auth()->user()->plan_id)->first();
            if ($plan->chats != 'all' && $plan->chats != 'premium') {     
                if ($template->category == 'premium' && ($plan->chats != 'premium' && $plan->chats != 'all')) {
                    $status = 'error';
                    $message =  __('Your current subscription does not include support for this chat assistant category');
                    return response()->json(['status' => $status, 'message' => $message]); 
                } else if(($template->category == 'standard' || $template->category == 'all') && ($plan->chats != 'standard' && $plan->chats != 'all')) {
                    $status = 'error';
                    $message =  __('Your current subscription does not include support for this chat assistant category');
                    return response()->json(['status' => $status, 'message' => $message]); 
                } else if($template->category == 'professional' && $plan->chats != 'professional') {
                    $status = 'error';
                    $message =  __('Your current subscription does not include support for this chat assistant category');
                    return response()->json(['status' => $status, 'message' => $message]); 
                }                   
            }
        }

        # Check if user has sufficient words available to proceed
        $balance = auth()->user()->available_words + auth()->user()->available_words_prepaid;
        $words = count(explode(' ', ($request->input('message'))));
        if ((auth()->user()->available_words + auth()->user()->available_words_prepaid) < $words) {
            if (!is_null(auth()->user()->member_of)) {
                if (auth()->user()->member_use_credits_chat) {
                    $member = User::where('id', auth()->user()->member_of)->first();
                    if (($member->available_words + $member->available_words_prepaid) < $words) {
                        $status = 'error';
                        $message = __('Not enough word balance to proceed, subscribe or top up your word balance and try again');
                        return response()->json(['status' => $status, 'message' => $message]);
                    }
                } else {
                    $status = 'error';
                    $message = __('Not enough word balance to proceed, subscribe or top up your word balance and try again');
                    return response()->json(['status' => $status, 'message' => $message]);
                }
             
            } else {
                $status = 'error';
                $message = __('Not enough word balance to proceed, subscribe or top up your word balance and try again');
                return response()->json(['status' => $status, 'message' => $message]);
            } 
        }


        $main_chat = Chat::where('chat_code', $request->chat_code)->first();
        $uploading = new UserService();
        $upload = $uploading->upload();
        if (!$upload['status']) return;

        if ($request->message_code == '') {
            $messages = ['role' => 'system', 'content' => $main_chat->prompt];            
            $messages[] = ['role' => 'user', 'content' => $request->input('message')];

            $chat = new ChatHistory();
            $chat->user_id = auth()->user()->id;
            $chat->title = 'New Chat';
            $chat->chat_code = $request->chat_code;
            $chat->message_code = strtoupper(Str::random(10));
            $chat->messages = 1;
            $chat->chat = $messages;
            $chat->save();
        } else {
            $chat_message = ChatHistory::where('message_code', $request->message_code)->first();

            if ($chat_message) {

                if (is_null($chat_message->chat)) {
                    $messages[] = ['role' => 'system', 'content' => $main_chat->prompt]; 
                } else {
                    $messages = $chat_message->chat;
                }
                
                array_push($messages, ['role' => 'user', 'content' => $request->input('message')]);
                $chat_message->messages = ++$chat_message->messages;
                $chat_message->chat = $messages;
                $chat_message->save();
            } else {
                $messages[] = ['role' => 'system', 'content' => $main_chat->prompt];            
                $messages[] = ['role' => 'user', 'content' => $request->input('message')];

                $chat = new ChatHistory();
                $chat->user_id = auth()->user()->id;
                $chat->title = 'New Chat';
                $chat->chat_code = $request->chat_code;
                $chat->message_code = $request->message_code;
                $chat->messages = 1;
                $chat->chat = $messages;
                $chat->save();
            }
        }

        session()->put('message_code', $request->message_code);
        session()->put('webAccessBtn', $request->webAccessBtn);	

        return response()->json(['status' => 'success', 'old'=> $balance, 'current' => ($balance - $words)]);

	}


     /**
	*
	* Process Chat
	* @param - file id in DB
	* @return - confirmation
	*
	*/
    public function generateChat(Request $request) 
    {  
        
		$webAccessBtn = session()->get('webAccessBtn');	
        	
        if (session()->has('webAccessBtn')) {	
                session()->forget('webAccessBtn');	
        }
        
        return response()->stream(function () use($webAccessBtn) {
            
            $open_ai = new OpenAi(config('services.openai.key'));
            $message_code = session()->get('message_code');

            # Apply proper model based on role and subsciption
            if (auth()->user()->group == 'user') {
                $model = config('settings.default_model_user');
            } elseif (auth()->user()->group == 'admin') {
                $model = config('settings.default_model_admin');
            } else {
                $plan = SubscriptionPlan::where('id', auth()->user()->plan_id)->first();
                $model = $plan->model;
            } 

            $chat_message = ChatHistory::where('message_code', $message_code)->first();
            $messages = $chat_message->chat;

			$lastArray = end($messages);
            $lastQuestion = $lastArray['content'];

            $text = "";
            if ($webAccessBtn == '1') {
                
                $systemMessage = [
                    "role" => "system",
                    "content" => 'The current UTC date and time now is ' . gmdate('Y-m-d H:i:s') . " . Using your web access and web scraping capabilities, please find the most recent and reliable sources online regarding the user questions. Please summarize your findings in a clear, concise manner for easy understanding.",
                ];
                $mergedMessages = array_merge([$systemMessage], $messages);
                $opts = [
                    'model' => 'gpt-3.5-turbo-16k-0613',
                    'messages' => $mergedMessages,
                    'functions' => [
                        [
                            "name" => "web_search",
                            "description"=>  "A search engine. useful for when you need to search the web. Please call the scrape function when searching for news.",
                            "parameters"=>  [
                              "type"=>  "object",
                              "properties"=>  [
                                "query"=> [
                                  "type"=>  "string",
                                  "description"=> "The information needed to search"
                            ]
                            ],
                              "required"=>  ["query"]
                            ]
                        ],
                        [
                            "name" => "get_current_weather",
                            "description"=>  "Get the current weather in a given location.",
                            "parameters"=>  [
                              "type"=>  "object",
                              "properties"=>  [
                                "location"=> [
                                  "type"=>  "string",
                                  "description"=> "The location need weather info"
                            ]
                            ],
                              "required"=>  ["location"]
                            ]
                        ],
                        [
                            "name" => "web_scraper",
                            "description"=>  "A web scraper. useful for when you need to scrape websites for additional information. Most useful for when gathering information for news.",
                            "parameters"=>  [
                              "type"=>  "object",
                              "properties"=>  [
                                "url"=> [
                                  "type"=>  "string",
                                  "description"=> "The web site url"
                            ]
                            ],
                              "required"=>  ["url"]
                            ]
                        ]
                    ],
                    'temperature' => 1.0,
                    'frequency_penalty' => 0,
                    'presence_penalty' => 0,
                    'stream' => true
                ];
            }else{
                $opts = [
                    'model' => 'gpt-3.5-turbo',
                    'messages' => $messages,
                    'temperature' => 1.0,
                    'frequency_penalty' => 0,
                    'presence_penalty' => 0,
                    'stream' => true
                ];
            }
            $arguments = '';
            $isFunction = true;
            $counter = 0; // prevent infinite loop

            while ($counter<5){
                $open_ai->chat($opts, function ($curl_info, $data) use (&$text, &$arguments, &$opts, &$counter, &$isFunction, &$messages) {
                    if ($obj = json_decode($data) and $obj->error->message != "") {
                        error_log(json_encode($obj->error->message));
                    } else {
                        $response = null;

                        $chunks = explode('data: ', $data);

                        $dataSent = false;
                        foreach ($chunks as $chunk) {
                            $trimmed_chunk = trim($chunk);
                            
                            if (!empty($trimmed_chunk)) {
                                $response = json_decode($trimmed_chunk, true);
                                $delta = null;
                                if(isset($response["choices"])){
                                    $delta = $response["choices"][0]["delta"];
                                }
                                if (isset($delta['function_call'])) {
                                    if (isset($delta['function_call']['arguments'])) {
                                        $arguments .= $delta['function_call']['arguments'];
                                    }
            
                                }
                                else if(isset($response["choices"][0]['finish_reason']) && $response["choices"][0]['finish_reason'] == 'function_call'){
                                    $jsonData = json_decode($arguments);
                                    $process = '';
                                    
                                    if(isset($jsonData->location)){
                                        $process = "Getting weather location for $jsonData->location";
                                        echo 'data: {"choices":[{"index":0,"delta":{"process":true,"content":"' . $process .'"}}]}' . "\n\n";
                                        echo PHP_EOL;
                                        ob_flush();
                                        flush();

                                        array_push($messages, ['role' => 'assistant', 'content' => $process]);
        
                                        $content = getWeather($jsonData);
                                    }
                                    else if(isset($jsonData->query)){
                                        $process = "Searching for $jsonData->query";
                                        echo 'data: {"choices":[{"index":0,"delta":{"process":true,"content":"' . $process .'"}}]}' . "\n\n";
                                        echo PHP_EOL;
                                        ob_flush();
                                        flush();

                                        array_push($messages, ['role' => 'assistant', 'content' => $process]);
                                        //
                                        array_push($opts['messages'], ['role' => 'user', 'content' => 'when searching if and only if you need more information to provide a more complete answer Please scrape into the urls as additional research. Scrape a url only once. If encounter problem scraping url, try other urls from the web search.']);

                                        $content = webSearch($jsonData);
                                    }
                                    else if(isset($jsonData->url)){
                                        $process = "Reading contents of $jsonData->url";
                                        echo 'data: {"choices":[{"index":0,"delta":{"process":true,"content":"' . $process .'"}}]}' . "\n\n";
                                        echo PHP_EOL;
                                        ob_flush();
                                        flush();

                                        array_push($messages, ['role' => 'assistant', 'content' => $process]);
        
                                        $content = webScrape($jsonData);
                                    }
        
                                    $arguments = '';
                                    ++$counter;
        
                                    array_push($opts['messages'], $content);
                                }
                                else if(isset($delta['content'])){ 
                        
                                    if (isset($response["choices"][0]["delta"]["content"])) {
                                        $text .= $response["choices"][0]["delta"]["content"];
                                        $isFunction = false;
                                    }
                                    if(!$dataSent){
                                        echo $data;
                                        echo PHP_EOL;
                                        ob_flush();
                                        flush();
                                        $dataSent = true;
                                    }

                                }
                                else if(isset($response["choices"][0]['finish_reason']) && $response["choices"][0]['finish_reason'] == 'stop'){
                                    echo $data;
                                    $counter = 5;
                                    echo PHP_EOL;
                                    ob_flush();
                                    flush();
                                }
                            }
                        }

                        return strlen($data);
                    }
                    
                });
            }
            # Update credit balance
            $words = count(explode(' ', ($text)));
            if ($webAccessBtn == '1') {
                $words *= 2;
            }
            $this->updateBalance($words);  
            
            array_push($messages, ['role' => 'assistant', 'content' => $text]);
            $chat_message->messages = ++$chat_message->messages;
            $chat_message->chat = $messages;
            $chat_message->save();
        }, 200, [
            'Cache-Control' => 'no-cache',
            'Content-Type' => 'text/event-stream',
        ]); 
    }

    /**
	*
	* Clear Session
	* @param - file id in DB
	* @return - confirmation
	*
	*/
	public function clear(Request $request) 
    {
        if (session()->has('message_code')) {
            session()->forget('message_code');
        }

        return response()->json(['status' => 'success']);
	}



    /**
	*
	* Update user word balance
	* @param - total words generated
	* @return - confirmation
	*
	*/
    public function updateBalance($words) {

        $user = User::find(Auth::user()->id);

        if (Auth::user()->available_words > $words) {

            $total_words = Auth::user()->available_words - $words;
            $user->available_words = ($total_words < 0) ? 0 : $total_words;
            $user->update();

        } elseif (Auth::user()->available_words_prepaid > $words) {

            $total_words_prepaid = Auth::user()->available_words_prepaid - $words;
            $user->available_words_prepaid = ($total_words_prepaid < 0) ? 0 : $total_words_prepaid;
            $user->update();

        } elseif ((Auth::user()->available_words + Auth::user()->available_words_prepaid) == $words) {

            $user->available_words = 0;
            $user->available_words_prepaid = 0;
            $user->update();

        } else {

            if (!is_null(Auth::user()->member_of)) {

                $member = User::where('id', Auth::user()->member_of)->first();

                if ($member->available_words > $words) {

                    $total_words = $member->available_words - $words;
                    $member->available_words = ($total_words < 0) ? 0 : $total_words;
        
                } elseif ($member->available_words_prepaid > $words) {
        
                    $total_words_prepaid = $member->available_words_prepaid - $words;
                    $member->available_words_prepaid = ($total_words_prepaid < 0) ? 0 : $total_words_prepaid;
        
                } elseif (($member->available_words + $member->available_words_prepaid) == $words) {
        
                    $member->available_words = 0;
                    $member->available_words_prepaid = 0;
        
                } else {
                    $remaining = $words - $member->available_words;
                    $member->available_words = 0;
    
                    $prepaid_left = $member->available_words_prepaid - $remaining;
                    $member->available_words_prepaid = ($prepaid_left < 0) ? 0 : $prepaid_left;
                }

                $member->update();

            } else {
                $remaining = $words - Auth::user()->available_words;
                $user->available_words = 0;

                $prepaid_left = Auth::user()->available_words_prepaid - $remaining;
                $user->available_words_prepaid = ($prepaid_left < 0) ? 0 : $prepaid_left;
                $user->update();
            }  

        }

        return true;
    }


    /**
	*
	* Update user word balance
	* @param - total words generated
	* @return - confirmation
	*
	*/
    public function messages(Request $request) {

        if ($request->ajax()) {

            if (session()->has('message_code')) {
                session()->forget('message_code');
            }

            $messages = ChatHistory::where('user_id', auth()->user()->id)->where('message_code', $request->code)->first();
            $message = ($messages) ? json_encode($messages, false) : 'new';
            return $message;
        }   
    }


    /**
	* 
	* Process media file
	* @param - file id in DB
	* @return - confirmation
	* 
	*/
	public function view($code) 
    {
        if (session()->has('message_code')) {
            session()->forget('message_code');
        }

        $chat = Chat::where('chat_code', $code)->first(); 
        $template = ChatTemplates::where([['chat_id', $chat->id],['status',1]])->get();
        $messages = ChatHistory::where('user_id', auth()->user()->id)->where('chat_code', $chat->chat_code)->orderBy('updated_at', 'desc')->get(); 
        $message_chat = ChatHistory::where('user_id', auth()->user()->id)->where('chat_code', $chat->chat_code)->latest('updated_at')->first(); 
        $default_message = ($message_chat) ? json_encode($message_chat, false) : 'new';



        return view('user.chat.view', compact('chat', 'messages', 'default_message','template'));
	}


    /**
	*
	* Rename chat
	* @param - file id in DB
	* @return - confirmation
	*
	*/
	public function rename(Request $request) 
    {
        if ($request->ajax()) {

            $chat = ChatHistory::where('message_code', request('code'))->first(); 

            if ($chat) {
                if ($chat->user_id == auth()->user()->id){

                    $chat->title = request('name');
                    $chat->save();
    
                    $data['status'] = 'success';
                    $data['code'] = request('code');
                    return $data;  
        
                } else{
    
                    $data['status'] = 'error';
                    $data['message'] = __('There was an error while changing the chat title');
                    return $data;
                }
            } 
              
        }
	}


    /**
	*
	* Delete chat
	* @param - file id in DB
	* @return - confirmation
	*
	*/
	public function delete(Request $request) 
    {
        if ($request->ajax()) {

            $chat = ChatHistory::where('message_code', request('code'))->first(); 

            if ($chat) {
                if ($chat->user_id == auth()->user()->id){

                    $chat->delete();

                    if (session()->has('message_code')) {
                        session()->forget('message_code');
                    }
    
                    $data['status'] = 'success';
                    return $data;  
        
                } else{
    
                    $data['status'] = 'error';
                    $data['message'] = __('There was an error while deleting the chat history');
                    return $data;
                }
            } else {
                $data['status'] = 'empty';
                return $data;
            }
              
        }
	}


     /**
	*
	* Set favorite status
	* @param - file id in DB
	* @return - confirmation
	*
	*/
	public function favorite(Request $request) 
    {
        if ($request->ajax()) {


            $chat = Chat::where('chat_code', request('id'))->first(); 

            $favorite = FavoriteChat::where('chat_code', $chat->chat_code)->where('user_id', auth()->user()->id)->first();

            if ($favorite) {

                $favorite->delete();

                $data['status'] = 'success';
                $data['set'] = true;
                return $data;  
    
            } else{

                $new_favorite = new FavoriteChat();
                $new_favorite->user_id = auth()->user()->id;
                $new_favorite->chat_code = $chat->chat_code;
                $new_favorite->save();

                $data['status'] = 'success';
                $data['set'] = false;
                return $data; 
            }  
        }
	}


    public function escapeJson($value) 
    { 
        $escapers = array("\\", "/", "\"", "\n", "\r", "\t", "\x08", "\x0c");
        $replacements = array("\\\\", "\\/", "\\\"", "\\n", "\\r", "\\t", "\\f", "\\b");
        $result = str_replace($escapers, $replacements, $value);
        return $result;
    }

    function googleCustomSearch($search_key)
{
    $search_result = [];

    $search_result_string = '';
    if (!empty($search_key)) {
        $searchQ = trim($search_key);

        $ch = curl_init();
        //$cr = "cr=" . urlencode($searchQ);
        $cr = "";

        $cx = "&cx=234018642682145d1";
        //$lr = "&lr=" . urlencode($searchQ);
        $lr = "lang_en";
        $q = "&q=" . urlencode($searchQ);
        $safe = "";
        $alt = "&alt=json";
        $num = "&num=6";
        //$prettyPrint = "&prettyPrint=true";
        $prettyPrint = "";
        //$general = "&%24.xgafv=1";
        $general = "";
        $key = "key=AIzaSyBC28sTa4fz5DddCYc6WWfLrX_48OqgiXk";
        curl_setopt($ch, CURLOPT_URL, 'https://customsearch.googleapis.com/customsearch/v1?' . $key . $cx . $q.$num);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');

        curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');

        $headers = array();
        $headers[] = 'Accept: application/json';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);
        $result = json_decode($result);
        $search_result = $result->items;
        if (is_array($search_result) && count($search_result) > 0) {
            $web_search_result_string = '';
            foreach ($search_result as $key => $single_result) {
                $result_count = $key + 1;
                
                $fetchdata = new URLFetcher($single_result->link);
                $title =  $fetchdata->get_heading();
                $content =  $fetchdata->get_paragraph_content();

                $web_search_result_string .= '</br>NUMBER : ' . $result_count .
                    "</br> URL: " . $single_result->link .
                    "</br> TITLE: " . $single_result->title .
                    "</br> CONTENT: " . $content .
                    "</br>";
            }

            $search_result_string = 'I will give you a question or an instruction. Your objective is to answer my question or fulfill my instruction.
';
            $search_result_string .= "</br>";

            $search_result_string .= 'My question or instruction is: ' . $search_key;

            $search_result_string .= "</br>";

            $search_result_string .= 'For your reference, today\'s date is ' . date("Y-m-d H:i:s");

            $search_result_string .= "</br>";

            $search_result_string .= $this->inst;           
              
 

            $search_result_string .=  $web_search_result_string;
        }
    }


    $res[0] = $web_search_result_string;


    return $search_result_string;
}

    public function saveAudio(Request $request)
    {
        $audio = $request->file('audio');
        $format = $audio->getClientOriginalExtension();
        $file_name = $audio->getClientOriginalName();
        $size = $audio->getSize();
        $name = Str::random(10) . '.' . $format;
        if (config('settings.whisper_default_storage') == 'local') {
            $audio_url = $audio->store('transcribe','public');
        } elseif (config('settings.whisper_default_storage') == 'aws') {
            Storage::disk('s3')->put($name, file_get_contents($audio));
            $audio_url = Storage::disk('s3')->url($name);
        } elseif (config('settings.whisper_default_storage') == 'wasabi') {
            Storage::disk('wasabi')->put($name, file_get_contents($audio));
            $audio_url = Storage::disk('wasabi')->url($name);
        }
        
        if (config('settings.whisper_default_storage') == 'local') {
            $file = curl_file_create($audio_url);
        } else {
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_URL, $audio_url);
            $content = curl_exec($curl);
            Storage::disk('public')->put('transcribe/' . $file_name, $content);
            $file = curl_file_create('transcribe/' . $file_name);
            curl_close($curl);
            
        }
		$open_ai = new OpenAi(config('services.openai.key')); 
       	$complete = $open_ai->translate([
			'model' => 'whisper-1',
			'file' => $file,
			'prompt' => "",
		]);
        $response = json_decode($complete , true);
		
		return response()->json(['response' => $response, 'message' => 'Audio recorded successfully']);
    }

    public function audioConvert(Request $request){
		$message_code = $request->message_code;
        $chat_message = ChatHistory::where('message_code', $message_code)->first();
        $messages = $chat_message->chat;
        $voice_code = Chat::where('chat_code', $chat_message->chat_code)->first();
        $i = count($messages)-1;
        $lastAssistantData['voice_code'] = $voice_code->voice_code;
        $lastAssistantData['data'] = $messages[$i]['content'];
        return  $lastAssistantData;
    }

    public function convertTextToAudio(Request $request)
    {
        // language_code , voice_id
		$text = $request->text;
		if($request->voiceCode == 1){
			$voice = Voice::where('id', 208)->first();
		} else {
			$voice = Voice::where('id', 424)->first();
		}
        

        $format = 'mp3';
        $file_name = 'LISTEN--' . Str::random(10) . '.mp3';
        $azure = new AzureTTSService();

        return $azure->synthesizeSpeech($voice, $text, $format, $file_name);
    } 

    public function updateWords(){
        $data['balance']                        = auth()->user()->available_words + auth()->user()->available_words_prepaid;
        $data['available_words']                = auth()->user()->available_words;
        $data['available_words_prepaid']        = auth()->user()->available_words_prepaid;
        return $data;
    }

    function getWeather($jsonData){
        Log::info("Using weather api");
        $location = $jsonData->location;
        $url = "http://api.weatherapi.com/v1/current.json?key=0191ce76160f4b5b9ad31403230408&&aqi=no&q=" .urlencode($location);
        $s_response = file_get_contents($url);
        Log::info(json_encode($s_response));
        return ['role' => 'function','name' => 'get_current_weather', 'content' => $s_response];
      }
    
      function webSearch($jsonData){
        Log::info("Searching the web");
        $query = $jsonData->query;
      
        $curl = curl_init();
      
        curl_setopt_array($curl, array(
          CURLOPT_URL => 'https://google.serper.dev/search',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => 'CURL_HTTP_VERSION_1_1',
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS =>'{"q":"'.$query.'","num":5}',
          CURLOPT_HTTPHEADER => array(
            'X-API-KEY: 228c19b0a498a82d61554ff2801c28b4c92e0145',
            'Content-Type: application/json'
          ),
        ));
        
        $s_response = curl_exec($curl);
        $results = json_decode($s_response);
        $concat_results="";
        if(property_exists($results, 'knowledgeGraph')){
          $concat_results= "knowledgeGraph: " . json_encode($results->knowledgeGraph) . "\n";
        }
        if(property_exists($results, 'answerBox')){
          $concat_results= "answerBox : " . json_encode($results->answerBox) . "\n";
        }
        $concat_results .= "Organic:";
        foreach ($results->organic as $item) {
            $concat_results  .= ' Title: ' . $item->title . "\n";
            $concat_results  .= ' Link: ' . $item->link . "\n";
            $concat_results  .= ' Snippet: ' . $item->snippet . "\n\n";
        }
        return ['role' => 'function','name' => 'web_search', 'content' => $concat_results];
      }
    
      function advanceScraping($url){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_PROXY, 'http://4b9f58f85fc01061f99cf38a65804a4a1c3ae4fd:js_render=true&antibot=true&premium_proxy=true@proxy.zenrows.com:8001');
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        return curl_exec($ch);
      }
    
      function jsRenderingScraping($url){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_PROXY, 'http://4b9f58f85fc01061f99cf38a65804a4a1c3ae4fd:js_render=true@proxy.zenrows.com:8001');
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        return curl_exec($ch);
      }
    
      function basicScraping($url){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_PROXY, 'http://6fe25a60306000f1500cb95cc2b58d05b3fe24aa:@proxy.zenrows.com:8001');
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        return curl_exec($ch);
      }
    
      function checkType($html){
        if (substr(trim($html), 0, 1) === '<') {
          // Probably HTML
          return 'html';
        } else if (in_array(substr(trim($html), 0, 1), ['{', '['], true)) {
            // Probably JSON
            return 'json';
        } else {
            // Unknown format
            return 'unkown';
        }
      }
    
      function parseHtml($html) {
        $dom = new \DOMDocument();
        // Suppress HTML errors (optional)
        libxml_use_internal_errors(true);
    
        // Load the HTML content into the DOMDocument
        $dom->loadHTML($html);
    
        // Create a DOMXPath object to navigate the DOMDocument
        $xpath = new \DOMXPath($dom);
        // Remove all script tags from the DOMDocument
    
        if($dom->getElementsByTagName('body')->item(0)===null){
          Log::info("no body found");
          return [false, "no body found"];
        }
    
        $scriptTags = $xpath->query('//script');
        foreach ($scriptTags as $scriptTag) {
            $scriptTag->parentNode->removeChild($scriptTag);
        }
    
        // Remove all style tags from the DOMDocument
        $styleTags = $xpath->query('//style');
        foreach ($styleTags as $styleTag) {
            $styleTag->parentNode->removeChild($styleTag);
        }
    
        // Get the text content of the body
        $textContent = $dom->getElementsByTagName('body')->item(0)->textContent;
    
        // Clean up whitespace and remove unrelated info
        $lines = explode("\n", $textContent);
        $filteredText = '';
    
        foreach ($lines as $line) {
            $line = trim($line);
    
            // Skip empty lines and lines with just a few characters
            if (empty($line) || strlen($line) < 10) {
                continue;
            }
    
            // Add relevant lines to the filtered text
            // You can add additional filtering conditions based on your specific needs
            $filteredText .= $line . "\n";
        }
    
    
        $parse_result = trim($filteredText) . "\n\n" . "Scrape Source URL: ";
        return [true, $parse_result];
      }
      
      
      function webScrape($jsonData) {
          $url = $jsonData->url;
          // Create a new DOMDocument instance
          $type = 'unkown';
          $html = null;
          $parse_result = null;
      
      
          // Basic scraping
          $html = basicScraping($url);
          
          //if json assume it is error proceed using advanced scrape
          if(checkType($html)=='json'){
            $html = advanceScraping($url);
          }
    
          $parse_result = parseHtml($html);
          if(!$parse_result[0]){
            $html = jsRenderingScraping($url);
            $parse_result = parseHtml($html);
          }
          
          // Clean up whitespace and return the text content
          return ['role' => 'function','name' => 'web_scraper', 'content' => $parse_result[1] . $url];
      }
      
}
