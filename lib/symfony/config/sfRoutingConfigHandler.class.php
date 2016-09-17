<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * @package    symfony
 * @subpackage config
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfRoutingConfigHandler.class.php 210 2007-03-01 23:59:16Z jphipps $
 */
class sfRoutingConfigHandler extends sfYamlConfigHandler
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
   */
  public function execute($configFiles)
  {
    // parse the yaml
    $config = $this->parseYamls($configFiles);

    // connect routes
    $routes = sfRouting::getInstance();
    foreach ($config as $name => $params) {
      if (preg_match('/_resource$/', $name)) {
      $actions = [ 'edit', 'delete', 'show', 'create', 'list', 'cancel' ];
        if (isset( $params['param']['only'] )) {
          $actions = (array) $params['param']['only'];
          unset( $params['param']['only'] );
        } else {
          if (isset( $params['param']['except'] )) {
            foreach ($params['param']['except'] as $param) {
              unset( $actions[$param] );
            }
            unset( $params['param']['except'] );
          }
        }
        $name = preg_replace('/_resource$/', '', $name);
        foreach ($actions as $action) {
          $params['param']['action'] = $action;
          if (in_array($action, [ 'edit', 'delete', 'show' ])) {
            $url = ($action == 'show') ? $params['url'] . '/:id' : $params['url'] . '/:id/' . $action;
            $filterId = preg_replace("#(.*)/:(.*_id)(/.*)$#uis", "$2", $params['url']);
            $requirements = ($filterId == $params['url']) ? [ 'id' => '\d+' ] : [ $filterId => '\d+', 'id' => '\d+' ];
            $params['requirements'] = $requirements;
          } else {
            $url = ( $action == 'create' ) ? $params['url'] . '/' . $action : $params['url'];
            $params['requirements'] = [];
          }
          $routes->connect($name . '_' . $action,
                           $url,
            ( isset( $params['param'] ) ? $params['param'] : [] ),
            ( isset( $params['requirements'] ) ? $params['requirements'] : [] ));
        }
      } else {
        $routes->connect($name,
          ( $params['url'] ? $params['url'] : '/' ),
          ( isset( $params['param'] ) ? $params['param'] : [] ),
          ( isset( $params['requirements'] ) ? $params['requirements'] : [] ));
      }
    }

    // compile data
    $retval = sprintf("<?php\n".
                      "// auto-generated by sfRoutingConfigHandler\n".
                      "// date: %s\n\$routes = sfRouting::getInstance();\n\$routes->setRoutes(\n%s\n);\n",
                      date('Y/m/d H:i:s'), var_export($routes->getRoutes(), 1));

    return $retval;
  }
}
