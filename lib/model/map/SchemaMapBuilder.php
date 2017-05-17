<?php


/**
 * This class adds structure of 'reg_schema' table to 'propel' DatabaseMap object.
 *
 *
 *
 * These statically-built map classes are used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 * @package    lib.model.map
 */
class SchemaMapBuilder {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'lib.model.map.SchemaMapBuilder';

	/**
	 * The database map.
	 */
	private $dbMap;

	/**
	 * Tells us if this DatabaseMapBuilder is built so that we
	 * don't have to re-build it every time.
	 *
	 * @return     boolean true if this DatabaseMapBuilder is built, false otherwise.
	 */
	public function isBuilt()
	{
		return ($this->dbMap !== null);
	}

	/**
	 * Gets the databasemap this map builder built.
	 *
	 * @return     the databasemap
	 */
	public function getDatabaseMap()
	{
		return $this->dbMap;
	}

	/**
	 * The doBuild() method builds the DatabaseMap
	 *
	 * @return     void
	 * @throws     PropelException
	 */
	public function doBuild()
	{
		$this->dbMap = Propel::getDatabaseMap('propel');

		$tMap = $this->dbMap->addTable('reg_schema');
		$tMap->setPhpName('Schema');

		$tMap->setUseIdGenerator(true);

		$tMap->addPrimaryKey('ID', 'Id', 'int', CreoleTypes::INTEGER, true, null);

		$tMap->addForeignKey('AGENT_ID', 'AgentId', 'int', CreoleTypes::INTEGER, 'reg_agent', 'ID', true, null);

		$tMap->addColumn('CREATED_AT', 'CreatedAt', 'int', CreoleTypes::TIMESTAMP, false, null);

		$tMap->addColumn('UPDATED_AT', 'UpdatedAt', 'int', CreoleTypes::TIMESTAMP, false, null);

		$tMap->addColumn('DELETED_AT', 'DeletedAt', 'int', CreoleTypes::TIMESTAMP, false, null);

		$tMap->addForeignKey('CREATED_USER_ID', 'CreatedUserId', 'int', CreoleTypes::INTEGER, 'users', 'ID', false, null);

		$tMap->addForeignKey('UPDATED_USER_ID', 'UpdatedUserId', 'int', CreoleTypes::INTEGER, 'users', 'ID', false, null);

		$tMap->addForeignKey('DELETED_USER_ID', 'DeletedUserId', 'int', CreoleTypes::INTEGER, 'users', 'ID', false, null);

		$tMap->addColumn('CHILD_UPDATED_AT', 'ChildUpdatedAt', 'int', CreoleTypes::TIMESTAMP, false, null);

		$tMap->addColumn('CHILD_UPDATED_USER_ID', 'ChildUpdatedUserId', 'int', CreoleTypes::INTEGER, false, null);

		$tMap->addColumn('NAME', 'Name', 'string', CreoleTypes::VARCHAR, true, 191);

		$tMap->addColumn('NOTE', 'Note', 'string', CreoleTypes::LONGVARCHAR, false, null);

		$tMap->addColumn('URI', 'Uri', 'string', CreoleTypes::VARCHAR, true, 255);

		$tMap->addColumn('URL', 'Url', 'string', CreoleTypes::VARCHAR, false, 255);

		$tMap->addColumn('BASE_DOMAIN', 'BaseDomain', 'string', CreoleTypes::VARCHAR, true, 255);

		$tMap->addColumn('TOKEN', 'Token', 'string', CreoleTypes::VARCHAR, true, 45);

		$tMap->addColumn('COMMUNITY', 'Community', 'string', CreoleTypes::VARCHAR, false, 45);

		$tMap->addColumn('LAST_URI_ID', 'LastUriId', 'int', CreoleTypes::INTEGER, false, null);

		$tMap->addForeignKey('STATUS_ID', 'StatusId', 'int', CreoleTypes::INTEGER, 'reg_status', 'ID', true, null);

		$tMap->addColumn('LANGUAGE', 'Language', 'string', CreoleTypes::CHAR, true, 6);

		$tMap->addForeignKey('PROFILE_ID', 'ProfileId', 'int', CreoleTypes::INTEGER, 'profile', 'ID', false, null);

		$tMap->addColumn('NS_TYPE', 'NsType', 'string', CreoleTypes::CHAR, true, 6);

		$tMap->addColumn('PREFIXES', 'Prefixes', 'string', CreoleTypes::LONGVARCHAR, false, null);

		$tMap->addColumn('LANGUAGES', 'Languages', 'string', CreoleTypes::LONGVARCHAR, false, null);

		$tMap->addColumn('REPO', 'Repo', 'string', CreoleTypes::VARCHAR, false, 255);

		$tMap->addColumn('PREFIX', 'Prefix', 'string', CreoleTypes::VARCHAR, true, 255);

	} // doBuild()

} // SchemaMapBuilder
