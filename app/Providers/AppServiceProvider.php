<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Collection;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->setBlade();

        $this->setCollection();
    }

    /**
     * blade 문법 설정
     *
     * @return void
     */
    public function setBlade()
    {
        Blade::directive('name', function ($expression) {
            return '<?php echo ($tmp = explode(".", '.$expression.')) && count($tmp) > 1 ? $tmp[0]."[".$tmp[1]."]" : '.$expression.'; ?>';
        });
    }

    /**
     * collect macro 설정
     *
     * @return void
     */
    public function setCollection()
    {
        Collection::macro('lang', function($lang = null, $field = 'lang') {
            if ($lang == null) {
                $lang = \App::getLocale();
            }
            return $this->where($field, $lang);
        });

        Collection::macro('selectOption', function($value, $text, $onClick = null) {
            $result = [];
            foreach ($this as $row) {
                $option = [];
                $option['value'] = is_closure($value) ? $value($row) : $row->{$value};
                $option['text'] = is_closure($text) ? $text($row) : $row->{$text};
                if ($onClick != null) {
                    $option['on']['click'] = is_closure($onClick) ? $onClick($row) : $row->{$onClick};
                }
                $result[] = $option;
            }
            return $result;
        });
    }
}
