<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfGeneratorConfigHandler.
 *
 * @package    symfony
 * @subpackage config
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfGeneratorConfigHandler.class.php 211 2007-03-02 00:41:28Z jphipps $
 */
class sfGeneratorConfigHandler extends sfYamlConfigHandler
{
  /**
   * Executes this configuration handler.
   *
   * @param array $configFiles An array of absolute filesystem path to a configuration file
   *
   * @return string Data to be written to a cache file
   *
   * @throws sfConfigurationException If a requested configuration file does not exist or is not readable
   * @throws sfParseException If a requested configuration file is improperly formatted
   * @throws sfInitializationException If a generator.yml key check fails
   */
  public function execute($configFiles)
  {
    // parse the yaml
    $config = $this->parseYamls($configFiles);
    if (!$config)
    {
      return '';
    }

    // hack to find the module name
    for($i=0; $i<count($configFiles); $i++)
    {
      preg_match('#'.sfConfig::get('sf_app_module_dir_name').'/([^/]+)/#', $configFiles[$i], $match);
      if (isset($match[1]))
      {
        $moduleName = $match[1];
        break;
      }
    }

    //only continue if we have a module-level generator.yml
    if (!isset($moduleName))
    {
      return '';
    }

    if (!isset($config['generator']))
    {
      throw new sfParseException(sprintf('Configuration file "%s" must specify a generator section', isset($configFiles[1]) ? $configFiles[1] : $configFiles[0]));
    }

    $config = $config['generator'];

    if (!isset($config['class']))
    {
      throw new sfParseException(sprintf('Configuration file "%s" must specify a generator class section under the generator section', isset($configFiles[1]) ? $configFiles[1] : $configFiles[0]));
    }

    foreach (array('fields', 'list', 'edit') as $section)
    {
      if (isset($config[$section]))
      {
        throw new sfParseException(sprintf('Configuration file "%s" can specify a "%s" section but only under the param section', isset($configFiles[1]) ? $configFiles[1] : $configFiles[0], $section));
      }
    }

    // generate class and add a reference to it
    $generatorManager = new sfGeneratorManager();
    $generatorManager->initialize();

    // generator parameters
    $generatorParam = (isset($config['param']) ? $config['param'] : array());

    $generatorParam['moduleName'] = $moduleName;

    $data = $generatorManager->generate($config['class'], $generatorParam);

    // compile data
    $retval = "<?php\n".
              "// auto-generated by sfGeneratorConfigHandler\n".
              "// date: %s\n%s\n";
    $retval = sprintf($retval, date('Y/m/d H:i:s'), $data);

    return $retval;
  }
}
