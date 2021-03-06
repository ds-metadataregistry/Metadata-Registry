<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
use Codeception\Exception\TestRuntimeException;
use Illuminate\Support\Debug\Dumper;

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
      $actions = [ 'edit', 'delete', 'show', 'create', 'list', 'save', 'cancel' ];
        if (isset( $params['param']['only'] )) {
          $actions = (array) $params['param']['only'];
          unset( $params['param']['only'] );
        } else {
          if (isset( $params['param']['except'] )) {
            foreach ((array) $params['param']['except'] as $param) {
              $key = array_search($param, $actions);
              if ($key) {
                unset( $actions[$key] );
              }
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
            $url = ( in_array($action, [ 'create', 'save' ]) ) ? $params['url'] . '/' . $action : $params['url'];
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

    //look at the routes -- change this to true to test
    $foo = false;
    if ($foo ===true) {
      //$urls = self::getRouteListByUrl($routes->getRoutes(),true, false, true);
      ksort($config);
      file_put_contents('symfony_routes.json', json_encode($config, JSON_PRETTY_PRINT));
      file_put_contents('symfony_routes.php', serialize($config));
      kint::enabled(kint::MODE_RICH);
      file_put_contents('symfony_routes.html', @kint::dump($config));
      kint::enabled(kint::MODE_WHITESPACE);
      file_put_contents('symfony_routes.txt', @kint::dump($config));
      kint::enabled(kint::MODE_CLI);
      ddd($config);
    }
    // compile data
    $retval = sprintf("<?php\n".
                      "// auto-generated by sfRoutingConfigHandler\n".
                      "// date: %s\n\$routes = sfRouting::getInstance();\n\$routes->setRoutes(\n%s\n);\n",
                      date('Y/m/d H:i:s'), var_export($routes->getRoutes(), 1));

    return $retval;
  }


  private static function getRouteListByUrl(array $routes, $sortByUrl = false, $sortByCount = false, $mergeListCancel = flase)
  {
    $urls = [];
    foreach ($routes as $name => $route) {
      //use $route[0] for the URL definition as key, $route[1] for the URL regex as key (more precise, includes default .html extension)
      $url = $route[1];

      //combines list and cancel if they have the same route
      if ($mergeListCancel) {
        $name = preg_replace("/_list|_cancel/uiUs", "_list/cancel", $name);
      }

      $urls[$url][$name] = $route;
    }

    if ($sortByUrl) {
      ksort($urls);
    }

    if ($sortByCount) {
      $urlCount = [];
      foreach ($urls as $key => $url) {
        $urlCount[count($url)][$key] = $url;
      }

      return $urlCount;

    }
    return $urls;
  }

}
