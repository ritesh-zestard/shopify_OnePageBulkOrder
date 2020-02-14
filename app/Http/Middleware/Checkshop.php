<?php

namespace App\Http\Middleware;

use Closure;

class Checkshop
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        //echo session('shop'); 
        if(session('shop')) {
            $shop = session('shop');
        } else {
            if($request['shop'])
            {
                session(['shop' => $request['shop']]);
                $shop = session('shop');
            }
            else{
				if(isset($_SERVER['HTTP_REFERER']))
				{
					$url = $_SERVER['HTTP_REFERER'];
					$pieces = parse_url($url);   
					session(['shop' => $pieces['host']]);
					//echo '<script>window.top.location.href="admin/apps"</script>';
				}
            }
            
        }
        
        return $next($request);
    }
}
