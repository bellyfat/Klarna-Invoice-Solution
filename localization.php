<?php
/**
 * Created by PhpStorm.
 * User: mattias.nording
 * Date: 2016-10-18
 * Time: 08:36
 */

Locale::acceptFromHttp($_SERVER['HTTP_ACCEPT_LANGUAGE']);
$languages = explode(',',$_SERVER['HTTP_ACCEPT_LANGUAGE']);
$lang= $languages[0];
$lang = str_replace("-","_",$lang);
$directory = dirname(__FILE__).'/../localization';
$domain = 'klarna';
$locale ="sv_se";
putenv("LANG=".$locale);
putenv("LC_ALL=sv_se");
setlocale( LC_ALL, $locale);
bindtextdomain($domain, $directory);
bind_textdomain_codeset($domain,"UTF-8");
textdomain($domain);
?>