<?php

namespace OutputControl;
use OutputControl\RegistryItem;

/**
 * A way of holding on to all templates and there data
 * in order to enable manipulation of all the template
 * variables right up until the output is rendered.
 */
class OutputControl
{
    private static $instance;
    private $templateRegistry = array();
    private $environments = array();

    private function __construct($twig, $loader)
    {
        $this->environments[] = ['twig' => $twig, 'loader' => $loader];
    }

    /**
     * A implementation of the Singelton pattern.
     *
     * @param  Twig_Environment $twig   the Twig environment
     * @param  mixed            loader any twig loader
     * @return OutputControl\OutputControl
     */
    public static function getInstance(\Twig_Environment $twig, $loader)
    {
        if(empty(self::$instance)) {
            self::$instance = new OutputControl($twig, $loader);
        }
        return self::$instance;
    }

    /**
     * Adds templates to the registry.
     *
     * @param  string $name     A custom template name
     * @param  string $template The full template name
     * @param  array  $values   Variables and there values.
     * @return void
     */
    public function registerTemplate($name, $template, array $values = array())
    {
        if($this->runLoadersExists($template) !== false) {
            if(!$this->isRegistered($name)) {
                $rt = new RegistryItem($template);
                foreach ($values as $item_name => $item_value) {
                    $rt->$item_name = $item_value;
                }
                $this->templateRegistry[$name] = $rt;
            } else {
                throw new \Exception('Template: ' . $name . ' is all ready registered.' , 1);
            }
        } else {
            throw new \Exception('Template: ' . $tempalte . ' is not loaded.' , 1);
        }
    }

    /**
     * Unregister a previously registered template.
     *
     * @param  [type] $name [description]
     * @return [type]       [description]
     */
    public function unregisterTemplate($name)
    {
        if($this->isRegistered($name)) {
            unset($this->templateRegistry[$name]);
        }  else {
            throw new \Exception('Template: ' . $name . ' is not registered.' , 1);
        }
    }

    /**
     * Check if a template is registered.
     *
     * @param  string $name The template name
     * @return bool
     */
    public function isRegistered($name)
    {
        if(array_key_exists($name, $this->templateRegistry)) {
            return true;
        }
        return false;
    }

    /**
     * Set or update template variables.
     *
     * @param  string $name   The template name
     * @param  array  $values The variables and there values
     * @return void
     */
    public function setTemplateVariables($name, array $values)
    {
        if($this->isRegistered($name)) {
            foreach ($values as $item_name => $item_value) {
                $this->templateRegistry[$name]->$item_name = $item_value;
            }
        } else {
            throw new \Exception('Template: ' . $name . ' is not registered.' , 1);
        }
    }

    /**
     * Get all variables for a template.
     *
     * @param  string $name The template name
     * @return OutputControl\RegistryItem
     */
    public function getTemplateVariables($name)
    {
        if($this->isRegistered($name)) {
            return $this->templateRegistry[$name];
        }
        throw new \Exception('Template not found.', 1);
    }

    /**
     * Outputs all registered templates and deletes the registry.
     *
     * @return void
     */
    public function controledOutput()
    {
        if(count($this->templateRegistry) > 0) {
            foreach ($this->templateRegistry as $templates) {
                $twig = $this->runLoadersExists($templates->getTemplate());
                if(is_array($twig)) {
                    $twig = reset(reset($twig));
                    echo $twig->render($templates->getTemplate(), $templates->getData());
                } else {
                    throw new \Exception('No environment is configured for template: ' . $templates->getTemplate() , 1);
                }
            }
            unset($this->templateRegistry);
        } else {
            throw new \Exception('No templates registered. Nothing to output.', 1);
        }
    }

    /**
     * Runs the exists method on all registered loaders until
     * it finds a loader finds the template.
     * @param  [type] $template [description]
     * @return [type]           [description]
     */
    private function runLoadersExists($template)
    {
        foreach ($this->environments as $key => $environment) {
            if($environment['loader']->exists($template)) {
                return [$key => $environment];
            }
        }
        return false;
    }
}
