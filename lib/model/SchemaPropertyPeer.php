<?php

/**
 * Subclass for performing query and update operations on the 'reg_schema_property' table.
 *
 *
 *
 * @package lib.model
 */
class SchemaPropertyPeer extends BaseSchemaPropertyPeer
{
  /**
   * returns properties for the current schema
   *
   * @return array of schema_property
   */
  public static function getPropertiesByCurrentSchemaID()
  {
    $currentPropertyId = sfContext::getInstance()->getRequest()->getParameter('schema_property_id', '');
    $schema            = SchemaPropertyPeer::retrieveByPK($currentPropertyId)->getSchema();
    $c = new Criteria();
    $c->add(self::SCHEMA_ID, $schema->getId());
    $c->add(self::TYPE, 'property');
    $c->addOr(self::TYPE, 'subproperty');
    $c->addAscendingOrderByColumn(SchemaPropertyI18nPeer::NAME);
    $properties = self::doSelectWithI18n($c);

    $request           = sfContext::getInstance()->getRequest();
    $currentPropertyId = $request->getParameter('id');
    if ("schemaprop" == $request->getParameter('module') && "edit" == $request->getParameter('action')) {
      foreach ($properties as $id => $property) {
        if ($property->getId() == $currentPropertyId) {
          unset($properties[$id]);
          break;
        }
      }
    }
    return $properties;
  }

  /**
   * returns classes for the current schema
   *
   * @return array of schema_property
   */
  public static function getClassesByCurrentSchemaID()
  {
    $currentPropertyId = sfContext::getInstance()->getRequest()->getParameter('schema_property_id', '');
    $schema            = SchemaPropertyPeer::retrieveByPK($currentPropertyId)->getSchema();
    $c                 = new Criteria();
    $c->add(self::SCHEMA_ID, $schema->getId());
    $c->add(self::TYPE, 'class');
    $c->addOr(self::TYPE, 'subclass');
    $c->addAscendingOrderByColumn(SchemaPropertyI18nPeer::NAME);
    $classes = self::doSelectWithI18n($c);

    $request           = sfContext::getInstance()->getRequest();
    $currentPropertyId = $request->getParameter('id');
    if ("schemaprop" == $request->getParameter('module') && "edit" == $request->getParameter('action')) {
      /** @var $property SchemaProperty */
      foreach ($classes as $id => $property) {
        if ($property->getId() == $currentPropertyId) {
          unset($classes[$id]);
          break;
        }
      }
    }
    return $classes;
  }

    /**
     * description
     *
     * @param string $uri
     *
     * @return SchemaProperty
     */
    public static function retrieveByUri($uri)
    {
        $criteria = new Criteria();
        $criteria->add(self::URI, $uri);

        return self::doSelectOne($criteria);
    }

    /**
     * sets the criteria and returns the few columns needed for schema property search results
     *
     * @param Criteria   $c The Criteria object used to build the SELECT statement.
     * @param Connection $con
     *
     * @return array Array of selected Objects
     * @throws PropelException Any exceptions caught during processing will be
     *     rethrown wrapped into a PropelException.
     */
    public static function doSelectSearchResults(Criteria $c, $con = null)
    {
        $results = self::doSelectJoinSchema($c);

        return $results;
    }
}
