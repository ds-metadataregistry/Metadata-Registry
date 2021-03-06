<?php
class myActionTools
{
  /**
  * update the currently set filters
  *
   * @param sfParameterHolder $attributeHolder
   * @param  string           $filter    the name of the filter, typically from the query string
  * @param  string $value the value of the filter
  * @param  string $namespace the local filter namespace ('sf_admin/$namespace/filters')
  */
  public static function updateAdminFilters(sfParameterHolder $attributeHolder, $filter, $value, $namespace)
  {
    $attributeHolder->set($filter, $value, "sf_admin/$namespace/filters");

    return;
  }


  /**
   * require that there be a schema
   *
   * returns a 404 if no schema has already been selected
   * Performs a redirect if one has but the param has not been added to the request
   *
   */
  public static function requireSchemaFilter()
  {
    /* @var sfAction */
    $actionInstance = sfContext::getInstance()->getActionStack()->getLastEntry()->getActionInstance();
    /** @var Schema **/
    $schema = self::findCurrentSchema();
    $actionInstance->forward404Unless($schema,'No Element Set has been selected.');

    //check to see if there's the correct request parameter
    $schemaId = $schema->getId();
    $requestId = $actionInstance->getRequestParameter('schema_id','');

    if ($schemaId && !strlen($requestId))
    {
      //let's add the correct parameter
      //and add in any other params and redirect
      $params = sfContext::getInstance()->getRequest()->getParameterHolder()->getAll() + array('schema_id' => $schemaId);
      $actionInstance->redirect($params);
    }
    elseif ($schemaId != $requestId)
    {
      /**
      * @todo We really should reset the schema here if the request ID and the stored ID don't match
      **/
    }

    return;
  }

  /**
  * require that there be a property
  *
  * returns a 404 if no vocabulary has already been selected
  * Performs a redirect if one has but the param has not been added to the request
  *
  */
  public static function requireSchemaPropertyFilter()
  {
    $actionInstance = sfContext::getInstance()->getActionStack()->getLastEntry()->getActionInstance();
    /** @var SchemaProperty **/
    $property = self::findCurrentSchemaProperty();
    /* @var sfAction */
    $actionInstance->forward404Unless($property,'No element has been selected.');

    //check to see if there's the correct request parameter
    $propertyId = $property->getId();
    $requestId = $actionInstance->getRequestParameter('schema_property_id','');

    if ($propertyId && !strlen($requestId))
    {
      //let's add the correct parameter
      //and add in any other params and redirect
      $params = sfContext::getInstance()->getRequest()->getParameterHolder()->getAll() + array('schema_property_id' => $propertyId);
      $actionInstance->redirect($params);
    }
    elseif ($propertyId != $requestId)
    {
      /**
      * @todo We really should reset the property here if the request ID and the stored ID don't match
      **/
    }

    return;
  }

  /**
  * require that there be a vocabulary
  *
  * returns a 404 if no vocabulary has already been selected
  * Performs a redirect if one has but the param has not been added to the request
  *
  * @return  Vocabulary The current vocabulary object
  */
  public static function requireVocabularyFilter()
  {
    $actionInstance = sfContext::getInstance()->getActionStack()->getLastEntry()->getActionInstance();
    /** @var Vocabulary **/
    $vocabulary = self::findCurrentVocabulary();
    /* @var sfAction */
    $actionInstance->forward404Unless($vocabulary,'No vocabulary has been selected.');

    //check to see if there's the correct request parameter
    $vocabularyId = $vocabulary->getId();
    $requestId = $actionInstance->getRequestParameter('vocabulary_id','');

    if ($vocabularyId && !strlen($requestId))
    {
      //let's add the correct parameter
      //and add in any other params and redirect
      $params = sfContext::getInstance()->getRequest()->getParameterHolder()->getAll() + array('vocabulary_id' => $vocabularyId);
      $actionInstance->redirect($params);
    }
    elseif ($vocabularyId != $requestId)
    {
      /**
      * @todo We really should reset the vocabulary here if the request ID and the stored ID don't match
      **/
    }

    return $vocabulary;
  }

  /**
  * require that there be a concept
  *
  * returns a 404 if no vocabulary has already been selected
  * Peforms a redirect if one has but the param has not been added to the request
  *
  */
  public static function requireConceptFilter()
  {
    $actionInstance = sfContext::getInstance()->getActionStack()->getLastEntry()->getActionInstance();
    /** @var Concept **/
    $concept = self::findCurrentconcept();
    /* @var sfAction */
    $actionInstance->forward404Unless($concept,'No concept has been selected.');

    //check to see if there's the correct request parameter
    $conceptId = $concept->getId();
    $requestId = $actionInstance->getRequestParameter('concept_id','');

    if ($conceptId && !strlen($requestId))
    {
      //let's add the correct parameter
      //and add in any other params and redirect
      $params = sfContext::getInstance()->getRequest()->getParameterHolder()->getAll() + array('concept_id' => $conceptId);
      $actionInstance->redirect($params);
    }
    elseif ($conceptId != $requestId)
    {
      /**
      * @todo We really should reset the concept here if the request ID and the stored ID don't match
      **/
    }

    return;
  }

  /**
  * gets the current agent object
  *
  * @return mixed current agent object, or false
  */
  public static function findCurrentAgent()
  {
    $instance = sfContext::getInstance();
    $user = $instance->getUser();
    $request = $instance->getRequest();
    $action = $instance->getActionStack()->getLastEntry()->getActionInstance();
    $attributeHolder = $user->getAttributeHolder();

      //check if there's a request parameter
      $agentId    = $request->getParameter('agent_id', '');
      $vocabId    = $request->getParameter('vocabulary_id', '');
      /** @var Vocabulary $vocabulary */
      $vocabulary = $user->getAttribute('vocabulary', '', 'symfony/user/sfUser/attributes');
      $agent = $user->getAttribute('agent', '', 'symfony/user/sfUser/attributes');

      //agent_id's not in the query string, but it's in a filter
      if (empty( $agentId ) && !$agent) {
          if (empty( $vocabulary )) {
              if ($vocabId) {
                  $vocabulary = VocabularyPeer::retrieveByPK($vocabId);
              }
          }
          if ($vocabulary) {
              $agentId = $vocabulary->getAgentId();
              $agent   = $vocabulary->getAgent();
          }
      }

      //We got here and there's a agent_id but we didn't get the stored agent object
      if ($agentId && ! $agent) {
          //we get it from the database
          $agent = self::setLatestagent($agentId);
      }

      if (!$agentId && $agent) {
          $agentId = $agent->getId();
      }

      if ($agentId) {
          self::updateAdminFilters($attributeHolder, 'agent_id', $agentId, 'agent');
      }

      //we got here and there's a agent and a agentid (yay)
    if ($agent && $agentId)
    {
      //let's check the id of the stored agent
      $currentId = $agent->getId();

      //but what if the id of that agent doesn't match the one we have
      if ($currentId != $agentId)
      {
        //we set the stored object to be the one we know
        $agent = self::setLatestagent($agentId);
      }
    }

    //if we get here and there's still no agent then we return false
    return ($agent) ? $agent : false;
  }

  /**
  * description
  *
  * @return agent Current agent object
  * @param  integer $agentId
  */
  public static function setLatestagent($agentId)
  {
    $agentObj = AgentPeer::retrieveByPK($agentId);
    if ($agentObj) {
    sfContext::getInstance()->getUser()->setCurrentagent($agentObj);
    }
    return $agentObj;
  }

  /**
  * gets the current vocabulary object
  *
  * @return mixed current vocabulary object, or false
  */
  public static function findCurrentVocabulary()
  {
    $instance = sfContext::getInstance();
    $user = $instance->getUser();
    $request = $instance->getRequest();
    $action = $instance->getActionStack()->getLastEntry()->getActionInstance();
    $attributeHolder = $user->getAttributeHolder();

    //check if there's a request parameter
    $vocabularyId = $request->getParameter('vocabulary_id','');

    //vocabulary_id's in the query string
    if ($vocabularyId)
    {
      self::updateAdminFilters($attributeHolder, 'vocabulary_id', $vocabularyId, 'concept');
    }

    //vocabulary_id's not in the query string, but it's in a filter
    //note: this will still return the correct value if it's in the query string
    $vocabularyId = $attributeHolder->get('vocabulary_id','','sf_admin/concept/filters');

    $vocabulary = $user->getCurrentVocabulary();

    //We got here and there's a vocabulary_id but we didn't get the stored vocabulary object
    if ($vocabularyId && !$vocabulary)
    {
      //we get it from the database
      $vocabulary = self::setLatestVocabulary($vocabularyId);
    }

    //we got here and there's a vocabulary and a vocabularyid (yay)
    if ($vocabulary and $vocabularyId)
    {
      //let's check the id of the stored vocabulary
      $currentId = $vocabulary->getId();

      //but what if the id of that vocabulary doesn't match the one we have
      if ($currentId != $vocabularyId)
      {
        //we set the stored object to be the one we know
        $vocabulary = self::setLatestVocabulary($vocabularyId);
      }
    }

    //still no vocabulary!!!
    if (!$vocabulary)
    {
      $idType = $request->getParameter('IdType', null);
      $id = $request->getParameter('id', null);
        if (!$id) {
            $id = $request->getParameter($idType, null);
        }

        switch ($idType)
      {
        case "property_id":
          $property = ConceptPropertyPeer::retrieveByPK($id);

          $concept = $property->getConceptRelatedByConceptId();
          $conceptId = $concept->getId();
          self::updateAdminFilters($attributeHolder, 'concept_id', $conceptId, 'concept_property');
          $user->setCurrentConcept($concept);

          $vocabulary = $concept->getVocabulary();
          $vocabularyId = $concept->getVocabularyId();
          self::updateAdminFilters($attributeHolder, 'vocabulary_id', $vocabularyId, 'concept');
          $user->setCurrentVocabulary($vocabulary);
          break;
        case "concept_id":
          $concept = ConceptPeer::retrieveByPK($id);

          $conceptId = $concept->getId();
          self::updateAdminFilters($attributeHolder, 'concept_id', $conceptId, 'concept_property');
          $user->setCurrentConcept($concept);

          $vocabulary = $concept->getVocabulary();
          $vocabularyId = $concept->getVocabularyId();
          self::updateAdminFilters($attributeHolder, 'vocabulary_id', $vocabularyId, 'concept');
          $user->setCurrentVocabulary($vocabulary);
          break;
        default:
      }
    }

    //if we get here and there's still no vocabulary then we return false
    $vocabulary = (isset($vocabulary)) ? $vocabulary : false;

    return $vocabulary;
  }

  /**
  * description
  *
  * @return Vocabulary Current vocabulary object
  * @param  integer $vocabId
  */
  public static function setLatestVocabulary($vocabId)
  {
    $vocabObj = VocabularyPeer::retrieveByPK($vocabId);
    if ($vocabObj)
    {
      sfContext::getInstance()->getUser()->setCurrentVocabulary($vocabObj);
    }
    return $vocabObj;
  }

  /**
  * gets the current concept object
  *
  * @return mixed current concept object, or false
  */
  public static function findCurrentConcept()
  {
    $instance = sfContext::getInstance();
    $user = $instance->getUser();
    $request = $instance->getRequest();
    $action = $instance->getActionStack()->getLastEntry()->getActionInstance();
    $attributeHolder = $user->getAttributeHolder();

    //check if there's a request parameter
    $conceptId = $request->getParameter('concept_id','');

    //concept_id's in the query string
    if ($conceptId)
    {
      self::updateAdminFilters($attributeHolder, 'concept_id', $conceptId, 'concept_property');
    }

    //concept_id's not in the query string, but it's in a filter
    //note: this will still return the correct value if it's in the query string
    $conceptId = $attributeHolder->get('concept_id','','sf_admin/concept_property/filters');

    $concept = $user->getCurrentConcept();

    //We got here and there's a concept_id but we didn't get the stored concept object
    if ($conceptId && !$concept)
    {
      //we get it from the database
      $concept = self::setLatestConcept($conceptId);
    }

    //we got here and there's a concept and a conceptid (yay)
    if ($concept and $conceptId)
    {
      //let's check the id of the stored concept
      $currentId = $concept->getId();

      //but what if the id of that concept doesn't match the one we have
      if ($currentId != $conceptId)
      {
        //we set the stored object to be the one we know
        $concept = self::setLatestConcept($conceptId);
      }
    }

    //still no concept!!!
    if (!$concept)
    {
      $idType = $request->getParameter('IdType', null);
      $id = $request->getParameter('id', null);
      switch ($idType)
      {
        case "property_id":
          $property = ConceptPropertyPeer::retrieveByPK($id);
          $conceptId = $property->getConceptId();
          $concept = self::setLatestConcept($conceptId);
          self::updateAdminFilters($attributeHolder, 'concept_id', $conceptId, 'concept_property');
          break;
        case "concept_id":
          break;
        default:
      }
    }

    //if we get here and there's still no vocabulary then we return false
    $concept = (isset($concept)) ? $concept : false;

    return $concept;
  }

  /**
  * description
  *
  * @return Concept Current concept object
  * @param  integer $vocabId
  */
  public static function setLatestConcept($vocabId)
  {
    $vocabObj = ConceptPeer::retrieveByPK($vocabId);
    sfContext::getInstance()->getUser()->setCurrentConcept($vocabObj);
    return $vocabObj;
  }

  /**
  * gets the current schema object
  *
   * @param null|SchemaProperty|SchemaPropertyElement $using will be one of the subordinate objects
   *
   * @return bool|Schema current schema object, or false
  */
  public static function findCurrentSchema($using = null)
  {
    $instance = sfContext::getInstance();
    $user = $instance->getUser();
    $request = $instance->getRequest();
    $action = $instance->getActionStack()->getLastEntry()->getActionInstance();
    $attributeHolder = $user->getAttributeHolder();

    //check if there's a request parameter
    $schemaId = $request->getParameter('schema_id','');

    //schema_id's in the query string
    if ($schemaId)
    {
      self::updateAdminFilters($attributeHolder, 'schema_id', $schemaId, 'schema');
    }

    //schema_id's not in the query string, but it's in a filter
    //note: this will still return the correct value if it's in the query string
    $schemaId = $attributeHolder->get('schema_id','','sf_admin/schema/filters');

    $schema = $user->getCurrentSchema();

    if ($using and !$schema)
    {
      $schema = SchemaPeer::retrieveByPK($using);
      if ($schema)
      {
        $schemaId = $schema->getId();
      }
    }
    //We got here and there's a schema_id but we didn't get the stored schema object
    if ($schemaId && !$schema)
    {
      //we get it from the database
      $schema = self::setLatestSchema($schemaId);
    }

    //we got here and there's a schema and a schemaid (yay)
    if ($schema and $schemaId)
    {
      //let's check the id of the stored schema
      $currentId = $schema->getId();

      //but what if the id of that schema doesn't match the one we have
      if ($currentId != $schemaId)
      {
        //we set the stored object to be the one we know
        $schema = self::setLatestSchema($schemaId);
      }
    }

    //if we get here and there's still no schema then we return false
    $schema = (isset($schema)) ? $schema : false;

    return $schema;
  }

  /**
  * description
  *
  * @return bool|Schema
   *
   * @param int $schemaId
  */
  public static function setLatestSchema($schemaId)
  {
    if (is_object($schemaId))
    {
      $schemaObj = $schemaId;
    }
    else
    {
      $schemaObj = SchemaPeer::retrieveByPK($schemaId);
    }
    if ($schemaObj)
    {
      sfContext::getInstance()->getUser()->setCurrentSchema($schemaObj);
      return $schemaObj;
    }
    else
    {
      return false;
    }
  }

  /**
  * gets the current schema property object
  *
  * @return mixed current property object, or false
  */
  public static function findCurrentSchemaProperty()
  {
    $instance = sfContext::getInstance();
    $user = $instance->getUser();
    $request = $instance->getRequest();
    $action = $instance->getActionStack()->getLastEntry()->getActionInstance();
    $attributeHolder = $user->getAttributeHolder();

    //check if there's a request parameter
    $propertyId = $request->getParameter('schema_property_id','');

    //schema_property_id's in the query string
    if ($propertyId)
    {
      self::updateAdminFilters($attributeHolder, 'schema_property_id', $propertyId, 'schema_property_element');
    }

    //schema_property_id's not in the query string, but it's in a filter
    //note: this will still return the correct value if it's in the query string
    $propertyId = $attributeHolder->get('schema_property_id','','sf_admin/schema_property_element/filters');

    $property = $user->getCurrentSchemaProperty();

    //We got here and there's a schema_property_id but we didn't get the stored property object
    if ($propertyId && !$property)
    {
      //we get it from the database
      $property = self::setLatestSchemaProperty($propertyId);
    }

    //we got here and there's a property and a propertyid (yay)
    if ($property and $propertyId)
    {
      //let's check the id of the stored property
      $currentId = $property->getId();

      //but what if the id of that property doesn't match the one we have
      if ($currentId != $propertyId)
      {
        //we set the stored object to be the one we know
        $property = self::setLatestSchemaProperty($propertyId);
      }
    }
    //if we get here and there's still no vocabulary then we return false
    $property = (isset($property)) ? $property : false;

    return $property;
  }

  /**
  * description
  *
  * @return SchemaProperty Current property property object
  * @param  integer $propertyId
  */
  public static function setLatestSchemaProperty($propertyId)
  {
    $propertyObj = SchemaPropertyPeer::retrieveByPK($propertyId);
    sfContext::getInstance()->getUser()->setCurrentSchemaProperty($propertyObj);
    return $propertyObj;
  }

  /**
   * Gets the current editorial language
   */
  public static function getEditLanguage() {
    return new sfCultureInfo(sfContext::getInstance()->getUser()->getCulture());
  }


  /**
   * Builds a base domain using a 'uri' subdomain
   *
   * @param sfActions $action
   *
   * @return string
   */
  public static function getBaseDomain($action)
  {
    /** @var myWebRequest $request */
    $request = $action->getRequest();

    $host      = $request->getHost();
    $uriPrefix = $request->getUriPrefix();
    $protocol  = preg_replace('*' . $host . '*', '', $uriPrefix);

    return $protocol . "uri." . $host . "/";
  }


  /**
   * @param sfParameterHolder $requestParams
   * @param bool $findAll             if true, returns an array of all found Ids
   * @param bool $includeValue        if true, returns an array of the form ['idType' => $value]
   * @param null|array $suppliedTypes overrides the list of types to search for
   *
   * @return array|mixed returns false if no Ids found, a string if a single ID was requested, an array of Ids if $findAll is true
   *
   */
  public static function findIdType(
    sfParameterHolder $requestParams,
    $findAll = false,
    $includeValue = false,
    $suppliedTypes = null)
  {
    $found = [];
    $types = $suppliedTypes ? (array) $suppliedTypes : [
      'vocabulary_id',
      'concept_id',
      'concept_property_id',
      'schema_id',
      'schema_property_id',
      'schema_property_element_id',
      'import_id' ];
    foreach ($types as $type) {
      /** @var sfParameterHolder $sf_params */
      if ($requestParams->has($type)) {
        if ($findAll) {
          if ($includeValue) {
            $found[$type] = $requestParams->get($type);
          } else {
            $found[] = $type;
          }
        } else {
          if ($includeValue) {
            $found[$type] = $requestParams->get($type);
            break;
          } else {
            return $type;
          }
        }
      }
    }

    return count($found) ? $found : false;
  }
}
