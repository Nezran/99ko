<?php
defined('ROOT') OR exit('No direct script access allowed');

## Traitements à effecturer lors de l'installation du plugin
function extrasInstall(){
}

## Hook (header admin)
function extrasAdminHead(){
    $data = '<link rel="dns-prefetch" href="//cdnjs.cloudflare.com">';
    $data.= '<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>'."\n";
    $data.= '<link href="https://cdnjs.cloudflare.com/ajax/libs/normalize/4.2.0/normalize.min.css" type="text/css" />'."\n";
    echo $data;
}

## Hook (header thème)
function extrasFrontHead(){
    $data = '<link rel="dns-prefetch" href="//cdnjs.cloudflare.com">';
    $data.= '<meta name="generator" content="99ko CMS" />'."\n";
    $data.= '<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>'."\n";
    $data.= '<link href="https://cdnjs.cloudflare.com/ajax/libs/normalize/4.2.0/normalize.min.css" type="text/css" />'."\n";
    echo $data;
}
?>