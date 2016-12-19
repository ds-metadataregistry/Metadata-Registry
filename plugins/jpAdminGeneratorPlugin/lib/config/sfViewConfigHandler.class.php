<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfViewConfigHandler allows you to configure views.
 *
 * @package    symfony
 * @subpackage config
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfViewConfigHandler.class.php 211 2007-03-02 00:41:28Z jphipps $
 */
class sfViewConfigHandler extends sfYamlConfigHandler
{
  /**
   * Executes this configuration handler.
   *
   * @param array $configFiles An array of absolute filesystem path to a configuration file
   *
   * @return string Data to be written to a cache file
   *
   * @throws <b>sfConfigurationException</b> If a requested configuration file does not exist or is not readable
   * @throws <b>sfParseException</b> If a requested configuration file is improperly formatted
   * @throws <b>sfInitializationException</b> If a view.yml key check fails
   */
  public function execute($configFiles)
  {
    // set our required categories list and initialize our handler
    $categories = array('required_categories' => array());
    $this->initialize($categories);

    // parse the yaml
    $this->mergeConfig($this->parseYamls($configFiles));

    // init our data array
    $data = array();

    $data[] = "\$context  = \$this->getContext();\n";
    $data[] = "\$response = \$context->getResponse();\n\n";

    // first pass: iterate through all view names to determine the real view name
    $first = true;
    foreach ((array) $this->yamlConfig as $viewName => $values)
    {
      if ($viewName == 'all')
      {
        continue;
      }

      $data[] = ($first ? '' : 'else ')."if (\$this->actionName.\$this->viewName == '$viewName')\n".
                "{\n";
      $data[] = $this->addTemplate($viewName);
      $data[] = "}\n";

      $first = false;
    }

    // general view configuration
    $data[] = ($first ? '' : "else\n{")."\n";
    $data[] = $this->addTemplate($viewName);
    $data[] = ($first ? '' : "}")."\n\n";

    // second pass: iterate through all real view names
    $first = true;
    foreach ((array) $this->yamlConfig as $viewName => $values)
    {
      if ($viewName == 'all')
      {
        continue;
      }

      $data[] = ($first ? '' : 'else ')."if (\$templateName.\$this->viewName == '$viewName')\n".
                "{\n";

      $data[] = $this->addLayout($viewName);
      $data[] = $this->addComponentSlots($viewName);
      $data[] = $this->addHtmlHead($viewName);
      $data[] = $this->addEscaping($viewName);

      $data[] = $this->addHtmlAsset($viewName);

      $data[] = "}\n";

      $first = false;
    }

    // general view configuration
    $data[] = ($first ? '' : "else\n{")."\n";

    $data[] = $this->addLayout();
    $data[] = $this->addComponentSlots();
    $data[] = $this->addHtmlHead();
    $data[] = $this->addEscaping();

    $data[] = $this->addHtmlAsset();
    $data[] = ($first ? '' : "}")."\n";

    // compile data
    $retval = sprintf("<?php\n".
                      "/** @var myUser \$sf_user */\n" .
                      "/** @var sfWebRequest \$sf_request */\n".
                      "// auto-generated by sfViewConfigHandler\n".
                      "// date: %s\n%s\n",
                      date('Y/m/d H:i:s'), implode('', $data));

    return $retval;
  }

  /**
   * Merges assets and environment configuration.
   *
   * @param array $myConfig A configuration array
   */
  protected function mergeConfig($myConfig)
  {
    // merge javascripts and stylesheets
    $myConfig['all']['stylesheets'] = array_merge(isset($myConfig['default']['stylesheets']) && is_array($myConfig['default']['stylesheets']) ? $myConfig['default']['stylesheets'] : array(), isset($myConfig['all']['stylesheets']) && is_array($myConfig['all']['stylesheets']) ? $myConfig['all']['stylesheets'] : array());
    unset($myConfig['default']['stylesheets']);

    $myConfig['all']['javascripts'] = array_merge(isset($myConfig['default']['javascripts']) && is_array($myConfig['default']['javascripts']) ? $myConfig['default']['javascripts'] : array(), isset($myConfig['all']['javascripts']) && is_array($myConfig['all']['javascripts']) ? $myConfig['all']['javascripts'] : array());
    unset($myConfig['default']['javascripts']);

    // merge default and all
    $myConfig['all'] = sfToolkit::arrayDeepMerge(
      isset($myConfig['default']) && is_array($myConfig['default']) ? $myConfig['default'] : array(),
      isset($myConfig['all']) && is_array($myConfig['all']) ? $myConfig['all'] : array()
    );

    unset($myConfig['default']);

    $this->yamlConfig = $myConfig;
  }

  /**
   * Adds a component slot statement to the data.
   *
   * @param string $viewName The view name
   *
   * @return string The PHP statement
   */
  protected function addComponentSlots($viewName = '')
  {
    $data = '';

    $components = $this->mergeConfigValue('components', $viewName);
    foreach ((array) $components as $name => $component)
    {
      if (!is_array($component) || count($component) < 1)
      {
        $component = array(null, null);
      }

      $data .= "  \$this->setComponentSlot('$name', '{$component[0]}', '{$component[1]}');\n";
      $data .= "  if (sfConfig::get('sf_logging_enabled')) \$context->getLogger()->info('{sfViewConfig} set component \"$name\" ({$component[0]}/{$component[1]})');\n";
    }

    return $data;
  }

  /**
   * Adds a template setting statement to the data.
   *
   * @param string $viewName The view name
   *
   * @return string The PHP statement
   */
  protected function addTemplate($viewName = '')
  {
    $data = '';

    $templateName = $this->getConfigValue('template', $viewName);
    $defaultTemplateName = $templateName ? "'$templateName'" : '$this->actionName';

    $data .= "  \$templateName = \$response->getParameter(\$this->moduleName.'_'.\$this->actionName.'_template', $defaultTemplateName, 'symfony/action/view');\n";
    $data .= "  \$this->setTemplate(\$templateName.\$this->viewName.\$this->getExtension());\n";

    return $data;
  }

  /**
   * Adds a layout statement statement to the data.
   *
   * @param string $viewName The view name
   *
   * @return string The PHP statement
   */
  protected function addLayout($viewName = '')
  {
    $data = '';

    if ($this->getConfigValue('has_layout', $viewName) && false !== $layout = $this->getConfigValue('layout', $viewName))
    {
      $data = "  \$this->setDecoratorTemplate('$layout'.\$this->getExtension());\n";
    }

    // For XMLHttpRequest, we want no layout by default
    // So, we check if the user requested has_layout: true or if he gave a layout: name for this particular action
    $localLayout = isset($this->yamlConfig[$viewName]['layout']) || isset($this->yamlConfig[$viewName]['has_layout']);
    if (!$localLayout && $data)
    {
      $data = "  if (!\$context->getRequest()->isXmlHttpRequest())\n  {\n  $data  }\n";
    }

    return $data;
  }

  /**
   * Adds http metas and metas statements to the data.
   *
   * @param string $viewName The view name
   *
   * @return string The PHP statement
   */
  protected function addHtmlHead($viewName = '')
  {
    $data = array();

    foreach ((array) $this->mergeConfigValue('http_metas', $viewName) as $httpequiv => $content)
    {
      $data[] = sprintf("  \$response->addHttpMeta('%s', '%s', false);", $httpequiv, str_replace('\'', '\\\'', $content));
    }

    foreach ((array) $this->mergeConfigValue('metas', $viewName) as $name => $content)
    {
      $data[] = sprintf("  \$response->addMeta('%s', '%s', false, false);", $name, str_replace('\'', '\\\'', preg_replace('/&amp;(?=\w+;)/', '&', htmlentities($content, ENT_QUOTES, sfConfig::get('sf_charset')))));
    }

    return implode("\n", $data)."\n";
  }

  /**
   * Adds stylesheets and javascripts statements to the data.
   *
   * @param string $viewName The $viewName view name
   *
   * @return string The PHP statement
   */
  protected function addHtmlAsset($viewName = '')
  {
    $data = array();
    $assetTypes = array('stylesheets' => 'addStylesheet','javascripts' => 'addJavascript');
    foreach ($assetTypes as $assetType => $insertValue)
    {
       $assets = $this->mergeConfigValue($assetType, $viewName);
       $tmp = array();
       foreach ((array) $assets as $asset)
       {
         $position = '';
         if (is_array($asset))
         {
           $key = key($asset);
           $options = $asset[$key];
           if (isset($options['position']))
           {
             $position = $options['position'];
             unset($options['position']);
           }
         }
         else
         {
           $key = $asset;
           $options = array();
         }

         $key = $this->replaceConstants($key);

         if ('-*' == $key)
         {
           $tmp = array();
         }
         else if ('-' == $key[0])
         {
           unset($tmp[substr($key, 1)]);
         }
         else
         {
            $tmp[$key] = sprintf("  \$response->$insertValue('%s', '%s', %s);", $key, $position, str_replace("\n", '', var_export($options, true)));
         }
       }
       // Merge the current view's stylesheets and javascripts with the app's default stylesheets
       $data = array_merge($data, array_values($tmp));
    }

    return implode("\n", $data)."\n";
  }

  /**
   * Adds an escaping statement to the data.
   *
   * @param string $viewName The view name
   *
   * @return string The PHP statement
   */
  protected function addEscaping($viewName = '')
  {
    $data = array();

    $escaping = $this->getConfigValue('escaping', $viewName);

    if (isset($escaping['strategy']))
    {
      $data[] = sprintf("  \$this->setEscaping(%s);", var_export($escaping['strategy'], true));
    }

    if (isset($escaping['method']))
    {
      $data[] = sprintf("  \$this->setEscapingMethod(%s);", var_export($escaping['method'], true));
    }

    return implode("\n", $data)."\n";
  }
}