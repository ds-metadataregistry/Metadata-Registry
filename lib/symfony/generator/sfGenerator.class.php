<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfGenerator is the abstract base class for all generators.
 *
 * @package    symfony
 * @subpackage generator
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfGenerator.class.php 210 2007-03-01 23:59:16Z jphipps $
 */
abstract class sfGenerator
{

    /** @var sfGenerator $generatorClass */
    protected $generatorClass = '';

    /** @var sfGeneratorManager $generatorManager */
    protected $generatorManager = null;

    protected $generatedModuleName = '';

    protected $theme = 'default';

    protected $moduleName = '';

  /**
   * Initializes the current sfGenerator instance.
   *
   * @param sfGeneratorManager $generatorManager A sfGeneratorManager instance
   */
  public function initialize($generatorManager)
  {
      $this->generatorManager = $generatorManager;
  }

  /**
   * Generates classes and templates.
   *
   * @param array $params An array of parameters
   *
   * @return string The cache for the configuration file
   */
  abstract public function generate($params = array());


  /**
   * Generates PHP files for a given module name.
   *
   * @param string $generatedModuleName The name of module name to generate
   * @param array $templateFiles        A list of template files to generate
   * @param array $configFiles          A list of configuration files to generate
   *
   * @throws sfException
   */
  protected function generatePhpFiles($generatedModuleName, $templateFiles = array(), $configFiles = array())
  {
    // eval actions file
    $retval = $this->evalTemplate('actions/actions.class.php');

    // save actions class
    $this->getGeneratorManager()->getCache()->set('actions.class.php', $generatedModuleName.DIRECTORY_SEPARATOR.'actions', $retval);

    // generate template files
    foreach ($templateFiles as $template)
    {
      // eval template file
      $retval = $this->evalTemplate('templates/'.$template);

      // save template file
      $this->getGeneratorManager()->getCache()->set($template, $generatedModuleName.DIRECTORY_SEPARATOR.'templates', $retval);
    }

    // generate config files
    foreach ($configFiles as $config)
    {
      // eval config file
      $retval = $this->evalTemplate('config/'.$config);

      // save config file
      $this->getGeneratorManager()->getCache()->set($config, $generatedModuleName.DIRECTORY_SEPARATOR.'config', $retval);
    }
  }


  /**
   * Evaluates a template file.
   *
   * @param string $templateFile The template file path
   *
   * @return string The evaluated template
   * @throws sfException
   */
  protected function evalTemplate($templateFile)
  {
    $templateFile = sfLoader::getGeneratorTemplate($this->getGeneratorClass(), $this->getTheme(), $templateFile);

    // eval template file
    ob_start();
    require $templateFile;
    $content = ob_get_clean();

    // replace [?php and ?]
    $content = $this->replacePhpMarks($content);

    $retval = "<?php\n".
              "// auto-generated by ".$this->getGeneratorClass()."\n".
              "// date: %s\n?>\n%s";
    $retval = sprintf($retval, date('Y/m/d H:i:s'), $content);

    return $retval;
  }

  /**
   * Replaces PHP marks by <?php ?>.
   *
   * @param string $text The PHP code
   *
   * @return string The converted PHP code
   */
  protected function replacePhpMarks($text)
  {
    // replace [?php and ?]
    return str_replace(array('[?php', '[?=', '?]'), array('<?php', '<?php echo', '?>'), $text);
  }

  /**
   * Gets the generator class.
   *
   * @return string The generator class
   */
  public function getGeneratorClass()
  {
    return $this->generatorClass;
  }

  /**
   * Sets the generator class.
   *
   * @param string $generator_class The generator class
   */
  public function setGeneratorClass($generator_class)
  {
    $this->generatorClass = $generator_class;
  }

  /**
   * Gets the sfGeneratorManager instance.
   *
   * @return sfGeneratorManager The sfGeneratorManager instance
   */
  protected function getGeneratorManager()
  {
    return $this->generatorManager;
  }

  /**
   * Gets the module name of the generated module.
   *
   * @return string The module name
   */
  public function getGeneratedModuleName()
  {
    return $this->generatedModuleName;
  }

  /**
   * Sets the module name of the generated module.
   *
   * @param string $module_name The module name
   */
  public function setGeneratedModuleName($module_name)
  {
    $this->generatedModuleName = $module_name;
  }

  /**
   * Gets the module name.
   *
   * @return string The module name
   */
  public function getModuleName()
  {
    return $this->moduleName;
  }

  /**
   * Sets the module name.
   *
   * @param string $module_name The module name
   */
  public function setModuleName($module_name)
  {
    $this->moduleName = $module_name;
  }

  /**
   * Gets the theme name.
   *
   * @return string The theme name
   */
  public function getTheme()
  {
    return $this->theme;
  }

  /**
   * Sets the theme name.
   *
   * @param string $theme The theme name
   */
  public function setTheme($theme)
  {
    $this->theme = $theme;
  }


    /**
     * Calls methods defined via the sfMixer class.
     *
     * @param string $method   The method name
     * @param array $arguments The method arguments
     *
     * @return mixed The returned value of the called method
     *
     * @throws sfException
     * @see sfMixer
     */
  public function __call($method, $arguments)
  {
    if (!$callable = sfMixer::getCallable('sfGenerator:'.$method))
    {
      throw new sfException(sprintf('Call to undefined method sfGenerator::%s', $method));
    }

    array_unshift($arguments, $this);

    return call_user_func_array($callable, $arguments);
  }


  function has_string_keys(array $array)
  {
    return count(array_filter(array_keys($array), 'is_string')) > 0;
  }
}
