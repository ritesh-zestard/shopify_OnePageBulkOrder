<?php
namespace App\Helpers;
class Helper
{
      public static function wcurl($method, $url, $query='', $payload='', $request_headers=array(), &$response_headers=array(), $curl_opts=array())
      {
        $ch = curl_init(wcurl_request_uri($url, $query));
        wcurl_setopts($ch, $method, $payload, $request_headers, $curl_opts);
        $response = curl_exec($ch);
        $curl_info = curl_getinfo($ch);
        $errno = curl_errno($ch);
        $error = curl_error($ch);
        curl_close($ch);
        if ($errno) throw new WcurlException($error, $errno);
        $header_size = $curl_info["header_size"];
        $msg_header = substr($response, 0, $header_size);
        $msg_body = substr($response, $header_size);

        $response_headers = wcurl_response_headers($msg_header);
        return $msg_body;
      }

        public static function wcurl_request_uri($url, $query)
        {
          if (empty($query))
          {
            return $url;
          }
          if (is_array($query))
          {
            return "$url?".http_build_query($query);
          }
          else
          {
            return "$url?$query";
          }
        }

        public static function wcurl_setopts($ch, $method, $payload, $request_headers, $curl_opts)
        {
          $default_curl_opts = array
          (
            CURLOPT_HEADER => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => 3,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_USERAGENT => 'wcurl',
            CURLOPT_CONNECTTIMEOUT => 30,
            CURLOPT_TIMEOUT => 30,
          );

          if ('GET' == $method)
          {
            $default_curl_opts[CURLOPT_HTTPGET] = true;
          }
          else
          {
            $default_curl_opts[CURLOPT_CUSTOMREQUEST] = $method;

            // Disable cURL's default 100-continue expectation
            if ('POST' == $method) array_push($request_headers, 'Expect:');

            if (!empty($payload))
            {
              if (is_array($payload))
              {
                $payload = http_build_query($payload);
                array_push($request_headers, 'Content-Type: application/x-www-form-urlencoded; charset=utf-8');

              }

              $default_curl_opts[CURLOPT_POSTFIELDS] = $payload;
            }
          }

          if (!empty($request_headers))
            $default_curl_opts[CURLOPT_HTTPHEADER] = $request_headers;

          $overriden_opts = $curl_opts + $default_curl_opts;
          foreach ($overriden_opts as $curl_opt=>$value)
            curl_setopt($ch, $curl_opt, $value);
        }

        public static function wcurl_response_headers($msg_header)
        {

          $multiple_headers = preg_split("/\r\n\r\n|\n\n|\r\r/", trim($msg_header));
          $last_response_header_lines = array_pop($multiple_headers);
          $response_headers = array();

          $header_lines = preg_split("/\r\n|\n|\r/", $last_response_header_lines);
          list(, $response_headers['http_status_code'], $response_headers['http_status_message']) = explode(' ', trim(array_shift($header_lines)), 3);
          foreach ($header_lines as $header_line)
          {
            list($name, $value) = explode(':', $header_line, 2);
            $response_headers[strtolower($name)] = trim($value);
          }

          return $response_headers;
        }

      public static function install_url($shop, $api_key)
      {
        return "http://$shop/admin/api/auth?api_key=$api_key";
      }


      public static function is_valid_request($query_params, $shared_secret)
      {
        $seconds_in_a_day = 24 * 60 * 60;
        $older_than_a_day = $query_params['timestamp'] < (time() - $seconds_in_a_day);
        if ($older_than_a_day) return false;

        //$signature = $query_params['signature'];
        //unset($query_params['signature']);

        foreach ($query_params as $key=>$val) $params[] = "$key=$val";
        sort($params);

        return true;
        //return (md5($shared_secret.implode('', $params)) === $signature);
      }


      public static function permission_url($shop, $api_key, $scope=array(), $redirect_uri='')
      {
        $scope = empty($scope) ? '' : '&scope='.implode(',', $scope);
        $redirect_uri = empty($redirect_uri) ? '' : '&redirect_uri='.urlencode($redirect_uri);
        return "https://$shop/admin/oauth/authorize?client_id=$api_key$scope$redirect_uri";
      }


      public static function oauth_access_token($shop, $api_key, $shared_secret, $code)
      {
        /*$url = 'https://$shop/admin/oauth/access_token'; //url for post parameter
        $apiData = array(
          'client_id'       => $api_key,
          'client_secret'   => $shared_secret,
          'grant_type'      => "authorization_code",//parameter
          'code'            => $code //parameter code of instagram where you get in instagram
        );

        $curlObject = curl_init();		//initial curl
        curl_setopt($curlObject, CURLOPT_URL, $url); //this only use in post method
        curl_setopt($curlObject, CURLOPT_POST, count($apiData));	//post method
        curl_setopt($curlObject, CURLOPT_POSTFIELDS, http_build_query($apiData));//post array of prameter
        //curl_setopt($curlObject, CURLOPT_HTTPHEADER, array('Accept: application/json'));//header of request
        curl_setopt($curlObject, CURLOPT_RETURNTRANSFER, 1);
        $jsonData = curl_exec($curlObject);
        curl_close($curlObject);
        return json_decode($jsonData);*/
        return _api('POST', "https://$shop/admin/oauth/access_token", NULL, array('client_id'=>$api_key, 'client_secret'=>$shared_secret, 'code'=>$code));
      }


      public static function client($shop, $shops_token, $api_key, $shared_secret, $private_app=false)
      {
        $password = $shops_token;
        $baseurl = "https://$shop/";

        return function ($method, $path, $params=array(), &$response_headers=array()) use ($baseurl, $shops_token)
        {
          $url = $baseurl.ltrim($path, '/');
          $query = in_array($method, array('GET','DELETE')) ? $params : array();
          $payload = in_array($method, array('POST','PUT')) ? stripslashes(json_encode($params)) : array();
          $request_headers = array();
          array_push($request_headers, "X-Shopify-Access-Token: $shops_token");
          if (in_array($method, array('POST','PUT'))) array_push($request_headers, "Content-Type: application/json; charset=utf-8");
          return _api($method, $url, $query, $payload, $request_headers, $response_headers);
        };
      }

        public static function _api($method, $url, $query='', $payload='', $request_headers=array(), &$response_headers=array())
        {
          try
          {
            //$query = $payload;
            $response = wcurl($method, $url, $query, $payload, $request_headers, $response_headers);
          }
          catch(WcurlException $e)
          {
            throw new CurlException($e->getMessage(), $e->getCode());
          }

          $response = json_decode($response, true);
          if (isset($response['errors']) or ($response_headers['http_status_code'] >= 400))
          {
            //throw new ApiException(compact('method', 'path', 'params', 'response_headers', 'response', 'shops_myshopify_domain', 'shops_token'));
            $a = (compact('method', 'path', 'params', 'response_headers', 'response', 'shops_myshopify_domain', 'shops_token'));
            echo '<pre>';print_r($a);die('ApiException-Died');
          }

          return (is_array($response) and !empty($response)) ? array_shift($response) : $response;
        }


      public static function calls_made($response_headers)
      {
        return _shop_api_call_limit_param(0, $response_headers);
      }


      public static function call_limit($response_headers)
      {
        return _shop_api_call_limit_param(1, $response_headers);
      }


      public static function calls_left($response_headers)
      {
        return call_limit($response_headers) - calls_made($response_headers);
      }


      public static function _shop_api_call_limit_param($index, $response_headers)
        {
          $params = explode('/', $response_headers['http_x_shopify_shop_api_call_limit']);
          return (int) $params[$index];
        }

      public static function legacy_token_to_oauth_token($shops_token, $shared_secret, $private_app=false)
      {
        return $private_app ? $secret : md5($shared_secret.$shops_token);
      }


      public static function legacy_baseurl($shop, $api_key, $password)
      {
        return "https://$api_key:$password@$shop/";

      }

      public static function appUninstallHook($TOKEN, $url, $params = array())
      {
        $curlObject = curl_init();
        curl_setopt($curlObject, CURLOPT_URL, $url);
        curl_setopt($curlObject, CURLOPT_POST, count($params));
        // Tell curl that this is the body of the POST
        curl_setopt($curlObject, CURLOPT_POSTFIELDS, stripslashes(json_encode($params)));
        curl_setopt($curlObject, CURLOPT_HEADER, false);
        curl_setopt($curlObject, CURLOPT_HTTPHEADER, array('Accept: application/json', 'Content-Type: application/json', 'X-Shopify-Access-Token: '.$TOKEN));
        curl_setopt($curlObject, CURLOPT_RETURNTRANSFER, true);
                    if(preg_match("/^(https)/",$url)) {
        curl_setopt($curlObject,CURLOPT_SSL_VERIFYPEER,false);
        }

        $response = curl_exec($curlObject);
        curl_close($curlObject);
        //echo '<pre>';print_r(json_decode($response));die('dod');
        return json_decode($response);
      }

      public static function accessToken($shop,$api_key,$shared_secret,$code)
      {
        $params = array('client_id'=>$api_key, 'client_secret'=>$shared_secret, 'code'=>$code);
        $url ="https://$shop/admin/oauth/access_token";
        $curlObject = curl_init();
        curl_setopt($curlObject, CURLOPT_URL, $url);
        curl_setopt($curlObject, CURLOPT_POST, count($params));
        // Tell curl that this is the body of the POST
        curl_setopt($curlObject, CURLOPT_POSTFIELDS, stripslashes(json_encode($params)));
        curl_setopt($curlObject, CURLOPT_HEADER, false);
        curl_setopt($curlObject, CURLOPT_HTTPHEADER, array('Accept: application/json', 'Content-Type: application/json', 'X-Shopify-Access-Token: '.NULL));
        curl_setopt($curlObject, CURLOPT_RETURNTRANSFER, true);
                    if(preg_match("/^(https)/",$url)) {
        curl_setopt($curlObject,CURLOPT_SSL_VERIFYPEER,false);
        }

        $response = curl_exec($curlObject);
        curl_close($curlObject);
        return json_decode($response);
      }
      /*
      class CurlException extends \Exception { }
      class Exception extends \Exception
      {
      protected $info;

      function __construct($info)
      {
        $this->info = $info;
        parent::__construct($info['response_headers']['http_status_message'], $info['response_headers']['http_status_code']);
      }

      function getInfo() { $this->info; }
      }
      */
}
?>
