<?php
use PHPHtmlParser\Dom;

function summernoteTempImage($html, $path)
{
    $dom = (new Dom)->loadStr($html);
    foreach ($dom->find('img') as $img) {
        if (preg_match('/^\/\/mapstrend.com\/storage\/temp/', $img->getAttribute('src'))) {
            $src = preg_replace('/^\/\/mapstrend\.com\/storage\//', '', $img->getAttribute('src'));
            if (!\Storage::disk('public')->has($src)) {
                abort(419);
            }
            $newPath = $path.'/'.last(explode('/', $src));
            \Storage::disk('public')->move($src, $newPath);
            $img->setAttribute('src', \Storage::disk('public')->url($newPath));
        }
    }
    return $dom->__toString();
}

function getScopes()
{
    $scopes = [];
    foreach (config('scope') as $scope) {
        if (preg_match('/^(?<scope>.*)\.(?<rw>read|write)$/', $scope, $match)) {
            $scopes[$match['scope']][] = $match['rw'];
        }
    }
    return $scopes;
}

function langOption()
{
    $result = [];
    foreach (config('app.lang') as $lang => $val) {
        $result[] = [
            'text' => config('app.lang_text.'.$lang),
            'value' => $lang,
        ];
    }
    return $result;
}

function is_closure($f)
{
    return $f instanceof Closure;
}
