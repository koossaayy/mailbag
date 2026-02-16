<?php

namespace App;

class Hello
{
    public function hello()
    {
        return __('This should be localized and translated');
    }
    public function maybe()
    {
        $hello = __('This one should trigger once only and thats it');
        return $hello;
    }
    public function anotherone()
    {
        $hello = __('empire of sun is a really good song');
        return $hello;
    }
    public function translations()
    {
        $hello = __("Let's do some translation");
        return $hello;
    }
    public function idiot()
    {
        $hello = __("I forgot the webhooks");
        return $hello;
    }
    public function forgotthesettings()
    {
        $hello = __("Forgot the settings again.");
        return $hello;
    }
    public function againPleaseWork()
    {
        $hello = __("Forgot the settings again.");
        $world = __("Please work.");
        return $hello . $world;
    }
}