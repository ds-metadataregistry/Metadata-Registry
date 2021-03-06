[?php
  /** @var sfContext $sf_context */
  /** @var sfParameterHolder $sf_flash */
  /** @var sfParameterHolder $sf_params */
  /** @var myWebRequest $sf_request */
  /** @var myUser $sf_user */
  /** @var sfPartialView $sf_view */
  /** @var <?php /** @var sfPropelAdminGenerator $this */
echo $this->getClassName() ?>  $<?php echo $this->getSingularName() ?> */
  ?]
<?php $first = true ?>
<?php foreach ($this->getColumnCategories('show.display') as $category): ?>
<?php
  if ($category[0] == '-')
  {
    $category_name = substr($category, 1);
    $collapse = true;

    if ($first)
    {
      $first = false;
      echo "[?php use_javascript(sfConfig::get('sf_jquery_web_dir').'/jquery.min.js', 'first') ?]\n";
      echo "[?php use_javascript(sfConfig::get('sf_admin_web_dir').'/js/collapse', 'last') ?]\n";
    }
  }
  else
  {
    $category_name = $category;
    $collapse = false;
  }
?>
<?php $catCredentials = $this->getParameterValue('show.display.'.$category.'.credentials') ?>
<?php if ($catCredentials): $catCredentials = str_replace("\n", ' ', var_export($catCredentials, true)) ?>
[?php /** @var MyUser $sf_user */ if ($sf_user->hasCredential(<?php echo $catCredentials ?>)): ?]
<?php endif; ?>
<fieldset id="sf_fieldset_<?php echo preg_replace('/[^a-z0-9_]/', '_', strtolower($category_name)) ?>"<?php if ($collapse): ?> class="collapse"<?php endif; ?>>
<?php if ($category != 'NONE'): ?>
  <h2>[?php echo __s('<?php echo $category_name ?>') ?]</h2>
<?php endif; ?>
<?php $hides = $this->getParameterValue('show.hide', array()) ?>
<?php foreach ($this->getColumns('show.display', $category) as $name => $column): ?>
<?php if (in_array($column->getName(), $hides)) continue ?>
<?php $condition = $this->getParameterValue('show.fields.'.$column->getName().'.condition') ?>
<?php if ($condition): ?>
[?php if (<?php echo $condition ?>): ?]
<?php endif; ?>
<?php $credentials = $this->getParameterValue('show.fields.'.$column->getName().'.credentials') ?>
<?php if ($credentials): $credentials = str_replace("\n", ' ', var_export($credentials, true)) ?>
    [?php if ($sf_user->hasCredential(<?php echo $credentials ?>)): ?]
<?php endif; ?>
<div id="show_row_<?php echo $this->getSingularName() ?>_<?php echo $column->getName() ?>" class="show-row">
  <label>[?php echo __s($labels['<?php echo $this->getSingularName() ?>{<?php echo $column->getName() ?>}']) ?]</label>
  <div id="show_row_content_<?php echo $this->getSingularName() ?>_<?php echo $column->getName() ?>" class="content">
[?php $showValue = <?php echo $this->getColumnShowTag($column) ?> ?]
[?php if ($showValue): ?]
<?php $helper = $this->getParameterValue('show.fields.'.$column->getName().'.helper') ?>
<?php if ($helper): ?>
    [?php echo <?php echo $helper ?>(<?php echo $this->getColumnShowTag($column) ?>) ?]
<?php else: ?>
    [?php echo <?php echo $this->getColumnShowTag($column) ?> ?]
<?php endif; ?>
[?php else: ?]
    &nbsp;
[?php endif; ?]
  <?php if ($this->getParameterValue('show.helptype') == 'div'): echo $this->getHelp($column, 'show'); elseif ($this->getParameterValue('show.helptype') == 'icon'): echo $this->getHelpAsIcon($column, 'show'); endif; ?>

  </div>
</div>
<?php if ($credentials): ?>
[?php endif; ?]
<?php endif; ?>
<?php if ($condition): ?>
[?php endif; ?]
<?php endif; ?>

<?php endforeach; ?>
</fieldset>
<?php if ($catCredentials): ?>
[?php endif; ?]
<?php endif; ?>
<?php endforeach; ?>

[?php include_partial('show_actions', array('<?php echo $this->getSingularName() ?>' => $<?php echo $this->getSingularName() ?>)) ?]

