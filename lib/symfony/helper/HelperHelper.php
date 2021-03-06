<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * HelperHelper.
 *
 * @package    symfony
 * @subpackage helper
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: HelperHelper.php 210 2007-03-01 23:59:16Z jphipps $
 */

function use_helper()
{
  sfLoader::loadHelpers(func_get_args(), sfContext::getInstance()->getModuleName());
}
