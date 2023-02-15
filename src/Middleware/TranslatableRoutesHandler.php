<?php

namespace SertxuDeveloper\Translatable\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use SertxuDeveloper\Translatable\Facades\Translatable;

class TranslatableRoutesHandler
{
    /**
     * Set language for localized route.
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next) {
        $params = explode('/', $request->getPathInfo());

        // Dump the first element (empty string) as getPathInfo() always returns a leading slash
        array_shift($params);

        if (count($params) > 0) {
            $locale = $params[0];

            if (Translatable::checkLocaleInSupportedLocales($locale)) {
                App::setLocale($locale);

                if (Translatable::isFallbackLocaleHidden() && Translatable::isFallbackLocale($locale)) {
                    array_shift($params);
                    $params = implode('/', $params);
                    $url = $request->root()."/$params";
                    $query = http_build_query($request->all());
                    $url = ($query) ? "$url?$query" : $url;

                    Session::reflash();

                    return new RedirectResponse($url, 302, ['Vary' => 'Accept-Language']);
                }
            }
        }

        return $next($request);
    }
}
