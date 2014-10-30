<?php
/**
 * A simple demo of how to take control of your output.
 */

require_once 'vendor/autoload.php';
use OutputControl\OutputControl;

/** Load Twig */
$loader = new Twig_Loader_Filesystem('templates');
$twig = new Twig_Environment($loader, array(
    'cache' => 'compilation_cache',
));

/** Get the instance of Output Control */
$oc = OutputControl::getInstance($twig, $loader);

/** Register the templates and give them some default values */
$oc->registerTemplate('header', 'head.html', array('title' => 'Output Control Demo'));
$oc->registerTemplate('body', 'body.html', array('headline' => 'A demo of Output Control'));
$oc->registerTemplate('footer', 'foot.html', array());

/** Set a variable after the template has been registered */
$oc->setTemplateVariables('body', array('text' => 'A text that is set after the template has been registered.'));

/** Example on how to append as string to a previously set var. */
$oc->setTemplateVariables('header', array('title' => $oc->getTemplateVariables('header')->title . ' - Appended data to a previously set variable.'));

/** When all your data is set and you are ready to render the templates */
$oc->controledOutput();	