<?php

namespace App\Http\Middleware;

use MenuRepository;
use Closure;

class SetMenu
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $page = null)
    {        
        if ($page != null && method_exists($this, $page.'MenuSet')) {
            $this->{$page.'MenuSet'}();

            \Config::set('menu', MenuRepository::toArray());
        }
        return $next($request);
    }

    private function getReadPermission($menu_id)
    {        
        if(!\Auth::guard('cms')->user())
            return false;

        $read_level = \App\Models\Cms\Menu::find($menu_id)->read;
        $user_level = \Auth::guard('cms')->user()->level;
        $menu_permission = \Auth::guard('cms')->user()->permission()->where('menu_id', $menu_id)->first();
        
        if ($menu_permission)
            $user_level = $menu_permission->level;

        if ($read_level <= $user_level)
            return true;
        else
            return false;
    }

    /**
     * cms menu 설정
     *
     * @return void
     */
    public function cmsMenuSet()
    {
        MenuRepository::append('home', function($menu) {
            $menu->setPermission($this->getReadPermission(1));
        });

        MenuRepository::append('service.home', function($menu) {
            
        });
        MenuRepository::append('service.list', function($menu) {
            $menu->parent('service.home');
            $menu->setPermission($this->getReadPermission(2));
        });
        MenuRepository::append('service.evaluate', function($menu) {
            $menu->parent('service.home');
            $menu->setPermission($this->getReadPermission(3));
        });
        MenuRepository::append('service.restoration', function($menu) {
            $menu->parent('service.home');
            $menu->setPermission($this->getReadPermission(4));
        });

        MenuRepository::append('service.service_display', function($menu) {
            $menu->parent('service.home');
            $menu->setPermission($this->getReadPermission(5));
        });

        MenuRepository::append('company.home', function($menu) {
            
        });
        MenuRepository::append('company.client', function($menu) {
            $menu->parent('company.home');
            $menu->setPermission($this->getReadPermission(5));
        });
        MenuRepository::append('company.agent', function($menu) {
            $menu->parent('company.home');
            $menu->setPermission($this->getReadPermission(6));
        });

        MenuRepository::append('company.question', function($menu) {
            $menu->parent('company.home');            
        });
        MenuRepository::append('company.client_question', function($menu) {
            $menu->parent('company.question');
            $menu->setPermission($this->getReadPermission(7));
        });
        MenuRepository::append('company.agent_question', function($menu) {
            $menu->parent('company.question');
            $menu->setPermission($this->getReadPermission(8));
        });

        MenuRepository::append('company.review', function($menu) {
            $menu->parent('company.home');
            $menu->setPermission($this->getReadPermission(9));
        });

        MenuRepository::append('company.goodbye', function($menu) {
            $menu->parent('company.home');
            $menu->setPermission($this->getReadPermission(16));
        });

        MenuRepository::append('category.home', function($menu) {
            
        });
        MenuRepository::append('category.service', function($menu) {
            $menu->parent('category.home');
            $menu->setPermission($this->getReadPermission(10));
        });

        MenuRepository::append('order.home', function($menu) {
            
        });
        MenuRepository::append('order.list', function($menu) {
            $menu->parent('order.home');
            $menu->setPermission($this->getReadPermission(11));
        });
        MenuRepository::append('order.payment', function($menu) {
            $menu->parent('order.home');
            $menu->setPermission($this->getReadPermission(12));
        });
        MenuRepository::append('order.refund', function($menu) {
            $menu->parent('order.home');
            $menu->setPermission($this->getReadPermission(13));
        });
        MenuRepository::append('order.settlement', function($menu) {
            $menu->parent('order.home');            
        });
        MenuRepository::append('order.settle_summary', function($menu) {
            $menu->parent('order.settlement');
            $menu->setPermission($this->getReadPermission(14));
        });
        MenuRepository::append('order.settle_detail', function($menu) {
            $menu->parent('order.settlement');
            $menu->setPermission($this->getReadPermission(15));
        });
        
        MenuRepository::append('stat.home', function($menu) {
            
        });
        MenuRepository::append('stat.using', function($menu) {
            $menu->parent('stat.home');
            $menu->setPermission($this->getReadPermission(16));
        });
        MenuRepository::append('stat.service', function($menu) {
            $menu->parent('stat.home');
            $menu->setPermission($this->getReadPermission(17));
        });
        MenuRepository::append('stat.category', function($menu) {
            $menu->parent('stat.home');
            $menu->setPermission($this->getReadPermission(18));
        });
        MenuRepository::append('stat.agent', function($menu) {
            $menu->parent('stat.home');
            $menu->setPermission($this->getReadPermission(19));
        });
        
        MenuRepository::append('store.home_cms', function($menu) {
            
        });
        MenuRepository::append('store.banner', function($menu) {
            $menu->parent('store.home_cms');
            $menu->setPermission($this->getReadPermission(20));
        });
        MenuRepository::append('store.func', function($menu) {
            $menu->parent('store.home_cms');
            $menu->setPermission($this->getReadPermission(21));
        });
        MenuRepository::append('store.conte', function($menu) {
            $menu->parent('store.home_cms');
            $menu->setPermission($this->getReadPermission(21));
        });
        
        MenuRepository::append('setting.home', function($menu) {            
            
        });

        if (\Auth::guard('cms')->user() && \Auth::guard('cms')->user()->super == 1) {
            MenuRepository::append('setting.admin', function($menu) {
                $menu->parent('setting.home');
                $menu->setPermission($this->getReadPermission(22));
            });
        }
        // MenuRepository::append('setting.question', function($menu) {
        //     $menu->parent('setting.home');
        // });
        
    
        MenuRepository::append('setting.notice', function($menu) {
            $menu->parent('setting.home');
            $menu->setPermission($this->getReadPermission(23));
        });

        MenuRepository::append('setting.faq_set', function($menu) {
            $menu->parent('setting.home');
            $menu->setPermission($this->getReadPermission(23));
        });
        MenuRepository::append('setting.site', function($menu) {
            $menu->parent('setting.home');
            $menu->setPermission($this->getReadPermission(24));
        });
        MenuRepository::append('setting.term', function($menu) {
            $menu->parent('setting.home');
            $menu->setPermission($this->getReadPermission(25));
        });
        MenuRepository::append('setting.script_set', function($menu) {
            $menu->parent('setting.home');
            $menu->setPermission($this->getReadPermission(10));

        });
    }

    /**
     * client menu 설정
     *
     * @return void
     */
    public function clientMenuSet()
    {
        MenuRepository::append('client.dashboard', function($menu) {
            $menu->setIcon('dashboard');
        });
        MenuRepository::append('client.myservice', function($menu) {
            $menu->setIcon('apps');
        });
        MenuRepository::append('client.payment', function($menu) {
            $menu->setIcon('attach_money');
        });
        MenuRepository::append('client.payment_list', function($menu) {
            $menu->parent('client.payment');
        });
        MenuRepository::append('client.refund', function($menu) {
            $menu->parent('client.payment');
        });
        MenuRepository::append('client.bbs', function($menu) {
            $menu->setIcon('help_outline');
        });
        MenuRepository::append('client.inquiry', function($menu) {
            $menu->parent('client.bbs');
        });
        MenuRepository::append('client.review', function($menu) {
            $menu->parent('client.bbs');
        });
        MenuRepository::append('store.home', function($menu) {
            $menu->setIcon('storefront');
            $menu->setTarget('_blank');
        });
    }

    /**
     * agent menu 설정
     *
     * @return void
     */
    public function agentMenuSet()
    {
        MenuRepository::append('agent.dashboard', function($menu) {
            $menu->setIcon('dashboard');
        });
        MenuRepository::append('agent.service', function($menu) {
            $menu->setIcon('apps');
        });
        MenuRepository::append('agent.service_list', function($menu) {
            $menu->parent('agent.service');
        });
        MenuRepository::append('agent.service_modify', function($menu) {
            $menu->parent('agent.service');
            $menu->setDisplay(false);
        });
        MenuRepository::append('agent.service_append', function($menu) {
            $menu->parent('agent.service');
        });
        // MenuRepository::append('agent.service_document', function($menu) {
        //     $menu->parent('agent.service');
        // });
        MenuRepository::append('agent.stat', function($menu) {
            $menu->setIcon('show_chart');
        });
        MenuRepository::append('agent.stat_order', function($menu) {
            $menu->parent('agent.stat');
        });
        MenuRepository::append('agent.stat_sales', function($menu) {
            $menu->parent('agent.stat');
        });
        MenuRepository::append('agent.stat_service', function($menu) {
            $menu->parent('agent.stat');
        });
        MenuRepository::append('agent.payment.home', function($menu) {
            $menu->setIcon('attach_money');
        });
        MenuRepository::append('agent.order.home', function($menu) {
            $menu->parent('agent.payment.home');
        });
        MenuRepository::append('agent.payment.list', function($menu) {
            $menu->parent('agent.payment.home');
        });
        MenuRepository::append('agent.payment.refund', function($menu) {
            $menu->parent('agent.payment.home');
        });
        MenuRepository::append('agent.payment.settlement', function($menu) {
            $menu->parent('agent.payment.home');
        });
        MenuRepository::append('agent.inquiry', function($menu) {
            $menu->setIcon('help_outline');
        });
        MenuRepository::append('agent.inquiry_client', function($menu) {
            $menu->parent('agent.inquiry');
        });
        MenuRepository::append('agent.inquiry_agent', function($menu) {
            $menu->parent('agent.inquiry');
        });
        MenuRepository::append('agent.store.review', function($menu) {
            $menu->setIcon('reviews');
        });
        MenuRepository::append('store.home', function($menu) {
            $menu->setIcon('storefront');
            $menu->setTarget('_blank');
        });
    }
}
