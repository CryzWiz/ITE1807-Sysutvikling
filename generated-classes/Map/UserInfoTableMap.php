<?php

namespace Map;

use \UserInfo;
use \UserInfoQuery;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\InstancePoolTrait;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\DataFetcher\DataFetcherInterface;
use Propel\Runtime\Exception\PropelException;
use Propel\Runtime\Map\RelationMap;
use Propel\Runtime\Map\TableMap;
use Propel\Runtime\Map\TableMapTrait;


/**
 * This class defines the structure of the 'userinfo' table.
 *
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 */
class UserInfoTableMap extends TableMap
{
    use InstancePoolTrait;
    use TableMapTrait;

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = '.Map.UserInfoTableMap';

    /**
     * The default database name for this class
     */
    const DATABASE_NAME = 'local';

    /**
     * The table name for this class
     */
    const TABLE_NAME = 'userinfo';

    /**
     * The related Propel class for this table
     */
    const OM_CLASS = '\\UserInfo';

    /**
     * A class that can be returned by this tableMap
     */
    const CLASS_DEFAULT = 'UserInfo';

    /**
     * The total number of columns
     */
    const NUM_COLUMNS = 7;

    /**
     * The number of lazy-loaded columns
     */
    const NUM_LAZY_LOAD_COLUMNS = 0;

    /**
     * The number of columns to hydrate (NUM_COLUMNS - NUM_LAZY_LOAD_COLUMNS)
     */
    const NUM_HYDRATE_COLUMNS = 7;

    /**
     * the column name for the User_id field
     */
    const COL_USER_ID = 'userinfo.User_id';

    /**
     * the column name for the Work_Phone field
     */
    const COL_WORK_PHONE = 'userinfo.Work_Phone';

    /**
     * the column name for the Mobile_Phone field
     */
    const COL_MOBILE_PHONE = 'userinfo.Mobile_Phone';

    /**
     * the column name for the Civil_Registration_Number field
     */
    const COL_CIVIL_REGISTRATION_NUMBER = 'userinfo.Civil_Registration_Number';

    /**
     * the column name for the Bankaccount field
     */
    const COL_BANKACCOUNT = 'userinfo.Bankaccount';

    /**
     * the column name for the Address field
     */
    const COL_ADDRESS = 'userinfo.Address';

    /**
     * the column name for the PostCode field
     */
    const COL_POSTCODE = 'userinfo.PostCode';

    /**
     * The default string format for model objects of the related table
     */
    const DEFAULT_STRING_FORMAT = 'YAML';

    /**
     * holds an array of fieldnames
     *
     * first dimension keys are the type constants
     * e.g. self::$fieldNames[self::TYPE_PHPNAME][0] = 'Id'
     */
    protected static $fieldNames = array (
        self::TYPE_PHPNAME       => array('UserId', 'WorkPhone', 'MobilePhone', 'CivilRegistrationNumber', 'Bankaccount', 'Address', 'Postcode', ),
        self::TYPE_CAMELNAME     => array('userId', 'workPhone', 'mobilePhone', 'civilRegistrationNumber', 'bankaccount', 'address', 'postcode', ),
        self::TYPE_COLNAME       => array(UserInfoTableMap::COL_USER_ID, UserInfoTableMap::COL_WORK_PHONE, UserInfoTableMap::COL_MOBILE_PHONE, UserInfoTableMap::COL_CIVIL_REGISTRATION_NUMBER, UserInfoTableMap::COL_BANKACCOUNT, UserInfoTableMap::COL_ADDRESS, UserInfoTableMap::COL_POSTCODE, ),
        self::TYPE_FIELDNAME     => array('User_id', 'Work_Phone', 'Mobile_Phone', 'Civil_Registration_Number', 'Bankaccount', 'Address', 'PostCode', ),
        self::TYPE_NUM           => array(0, 1, 2, 3, 4, 5, 6, )
    );

    /**
     * holds an array of keys for quick access to the fieldnames array
     *
     * first dimension keys are the type constants
     * e.g. self::$fieldKeys[self::TYPE_PHPNAME]['Id'] = 0
     */
    protected static $fieldKeys = array (
        self::TYPE_PHPNAME       => array('UserId' => 0, 'WorkPhone' => 1, 'MobilePhone' => 2, 'CivilRegistrationNumber' => 3, 'Bankaccount' => 4, 'Address' => 5, 'Postcode' => 6, ),
        self::TYPE_CAMELNAME     => array('userId' => 0, 'workPhone' => 1, 'mobilePhone' => 2, 'civilRegistrationNumber' => 3, 'bankaccount' => 4, 'address' => 5, 'postcode' => 6, ),
        self::TYPE_COLNAME       => array(UserInfoTableMap::COL_USER_ID => 0, UserInfoTableMap::COL_WORK_PHONE => 1, UserInfoTableMap::COL_MOBILE_PHONE => 2, UserInfoTableMap::COL_CIVIL_REGISTRATION_NUMBER => 3, UserInfoTableMap::COL_BANKACCOUNT => 4, UserInfoTableMap::COL_ADDRESS => 5, UserInfoTableMap::COL_POSTCODE => 6, ),
        self::TYPE_FIELDNAME     => array('User_id' => 0, 'Work_Phone' => 1, 'Mobile_Phone' => 2, 'Civil_Registration_Number' => 3, 'Bankaccount' => 4, 'Address' => 5, 'PostCode' => 6, ),
        self::TYPE_NUM           => array(0, 1, 2, 3, 4, 5, 6, )
    );

    /**
     * Initialize the table attributes and columns
     * Relations are not initialized by this method since they are lazy loaded
     *
     * @return void
     * @throws PropelException
     */
    public function initialize()
    {
        // attributes
        $this->setName('userinfo');
        $this->setPhpName('UserInfo');
        $this->setIdentifierQuoting(false);
        $this->setClassName('\\UserInfo');
        $this->setPackage('');
        $this->setUseIdGenerator(false);
        // columns
        $this->addForeignPrimaryKey('User_id', 'UserId', 'INTEGER' , 'user', 'id', true, null, null);
        $this->addColumn('Work_Phone', 'WorkPhone', 'VARCHAR', false, 45, '');
        $this->addColumn('Mobile_Phone', 'MobilePhone', 'VARCHAR', false, 45, '');
        $this->addColumn('Civil_Registration_Number', 'CivilRegistrationNumber', 'BIGINT', false, null, null);
        $this->addColumn('Bankaccount', 'Bankaccount', 'BIGINT', false, null, null);
        $this->addColumn('Address', 'Address', 'VARCHAR', false, 64, null);
        $this->addForeignKey('PostCode', 'Postcode', 'SMALLINT', 'postal', 'PostCode', false, null, null);
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('User', '\\User', RelationMap::MANY_TO_ONE, array (
  0 =>
  array (
    0 => ':User_id',
    1 => ':id',
  ),
), null, null, null, false);
        $this->addRelation('Postal', '\\Postal', RelationMap::MANY_TO_ONE, array (
  0 =>
  array (
    0 => ':PostCode',
    1 => ':PostCode',
  ),
), null, null, null, false);
    } // buildRelations()

    /**
     * Retrieves a string version of the primary key from the DB resultset row that can be used to uniquely identify a row in this table.
     *
     * For tables with a single-column primary key, that simple pkey value will be returned.  For tables with
     * a multi-column primary key, a serialize()d version of the primary key will be returned.
     *
     * @param array  $row       resultset row.
     * @param int    $offset    The 0-based offset for reading from the resultset row.
     * @param string $indexType One of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_CAMELNAME
     *                           TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM
     *
     * @return string The primary key hash of the row
     */
    public static function getPrimaryKeyHashFromRow($row, $offset = 0, $indexType = TableMap::TYPE_NUM)
    {
        // If the PK cannot be derived from the row, return NULL.
        if ($row[TableMap::TYPE_NUM == $indexType ? 0 + $offset : static::translateFieldName('UserId', TableMap::TYPE_PHPNAME, $indexType)] === null) {
            return null;
        }

        return null === $row[TableMap::TYPE_NUM == $indexType ? 0 + $offset : static::translateFieldName('UserId', TableMap::TYPE_PHPNAME, $indexType)] || is_scalar($row[TableMap::TYPE_NUM == $indexType ? 0 + $offset : static::translateFieldName('UserId', TableMap::TYPE_PHPNAME, $indexType)]) || is_callable([$row[TableMap::TYPE_NUM == $indexType ? 0 + $offset : static::translateFieldName('UserId', TableMap::TYPE_PHPNAME, $indexType)], '__toString']) ? (string) $row[TableMap::TYPE_NUM == $indexType ? 0 + $offset : static::translateFieldName('UserId', TableMap::TYPE_PHPNAME, $indexType)] : $row[TableMap::TYPE_NUM == $indexType ? 0 + $offset : static::translateFieldName('UserId', TableMap::TYPE_PHPNAME, $indexType)];
    }

    /**
     * Retrieves the primary key from the DB resultset row
     * For tables with a single-column primary key, that simple pkey value will be returned.  For tables with
     * a multi-column primary key, an array of the primary key columns will be returned.
     *
     * @param array  $row       resultset row.
     * @param int    $offset    The 0-based offset for reading from the resultset row.
     * @param string $indexType One of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_CAMELNAME
     *                           TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM
     *
     * @return mixed The primary key of the row
     */
    public static function getPrimaryKeyFromRow($row, $offset = 0, $indexType = TableMap::TYPE_NUM)
    {
        return (int) $row[
            $indexType == TableMap::TYPE_NUM
                ? 0 + $offset
                : self::translateFieldName('UserId', TableMap::TYPE_PHPNAME, $indexType)
        ];
    }
    
    /**
     * The class that the tableMap will make instances of.
     *
     * If $withPrefix is true, the returned path
     * uses a dot-path notation which is translated into a path
     * relative to a location on the PHP include_path.
     * (e.g. path.to.MyClass -> 'path/to/MyClass.php')
     *
     * @param boolean $withPrefix Whether or not to return the path with the class name
     * @return string path.to.ClassName
     */
    public static function getOMClass($withPrefix = true)
    {
        return $withPrefix ? UserInfoTableMap::CLASS_DEFAULT : UserInfoTableMap::OM_CLASS;
    }

    /**
     * Populates an object of the default type or an object that inherit from the default.
     *
     * @param array  $row       row returned by DataFetcher->fetch().
     * @param int    $offset    The 0-based offset for reading from the resultset row.
     * @param string $indexType The index type of $row. Mostly DataFetcher->getIndexType().
                                 One of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_CAMELNAME
     *                           TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     *
     * @throws PropelException Any exceptions caught during processing will be
     *                         rethrown wrapped into a PropelException.
     * @return array           (UserInfo object, last column rank)
     */
    public static function populateObject($row, $offset = 0, $indexType = TableMap::TYPE_NUM)
    {
        $key = UserInfoTableMap::getPrimaryKeyHashFromRow($row, $offset, $indexType);
        if (null !== ($obj = UserInfoTableMap::getInstanceFromPool($key))) {
            // We no longer rehydrate the object, since this can cause data loss.
            // See http://www.propelorm.org/ticket/509
            // $obj->hydrate($row, $offset, true); // rehydrate
            $col = $offset + UserInfoTableMap::NUM_HYDRATE_COLUMNS;
        } else {
            $cls = UserInfoTableMap::OM_CLASS;
            /** @var UserInfo $obj */
            $obj = new $cls();
            $col = $obj->hydrate($row, $offset, false, $indexType);
            UserInfoTableMap::addInstanceToPool($obj, $key);
        }

        return array($obj, $col);
    }

    /**
     * The returned array will contain objects of the default type or
     * objects that inherit from the default.
     *
     * @param DataFetcherInterface $dataFetcher
     * @return array
     * @throws PropelException Any exceptions caught during processing will be
     *                         rethrown wrapped into a PropelException.
     */
    public static function populateObjects(DataFetcherInterface $dataFetcher)
    {
        $results = array();
    
        // set the class once to avoid overhead in the loop
        $cls = static::getOMClass(false);
        // populate the object(s)
        while ($row = $dataFetcher->fetch()) {
            $key = UserInfoTableMap::getPrimaryKeyHashFromRow($row, 0, $dataFetcher->getIndexType());
            if (null !== ($obj = UserInfoTableMap::getInstanceFromPool($key))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj->hydrate($row, 0, true); // rehydrate
                $results[] = $obj;
            } else {
                /** @var UserInfo $obj */
                $obj = new $cls();
                $obj->hydrate($row);
                $results[] = $obj;
                UserInfoTableMap::addInstanceToPool($obj, $key);
            } // if key exists
        }

        return $results;
    }
    /**
     * Add all the columns needed to create a new object.
     *
     * Note: any columns that were marked with lazyLoad="true" in the
     * XML schema will not be added to the select list and only loaded
     * on demand.
     *
     * @param Criteria $criteria object containing the columns to add.
     * @param string   $alias    optional table alias
     * @throws PropelException Any exceptions caught during processing will be
     *                         rethrown wrapped into a PropelException.
     */
    public static function addSelectColumns(Criteria $criteria, $alias = null)
    {
        if (null === $alias) {
            $criteria->addSelectColumn(UserInfoTableMap::COL_USER_ID);
            $criteria->addSelectColumn(UserInfoTableMap::COL_WORK_PHONE);
            $criteria->addSelectColumn(UserInfoTableMap::COL_MOBILE_PHONE);
            $criteria->addSelectColumn(UserInfoTableMap::COL_CIVIL_REGISTRATION_NUMBER);
            $criteria->addSelectColumn(UserInfoTableMap::COL_BANKACCOUNT);
            $criteria->addSelectColumn(UserInfoTableMap::COL_ADDRESS);
            $criteria->addSelectColumn(UserInfoTableMap::COL_POSTCODE);
        } else {
            $criteria->addSelectColumn($alias . '.User_id');
            $criteria->addSelectColumn($alias . '.Work_Phone');
            $criteria->addSelectColumn($alias . '.Mobile_Phone');
            $criteria->addSelectColumn($alias . '.Civil_Registration_Number');
            $criteria->addSelectColumn($alias . '.Bankaccount');
            $criteria->addSelectColumn($alias . '.Address');
            $criteria->addSelectColumn($alias . '.PostCode');
        }
    }

    /**
     * Returns the TableMap related to this object.
     * This method is not needed for general use but a specific application could have a need.
     * @return TableMap
     * @throws PropelException Any exceptions caught during processing will be
     *                         rethrown wrapped into a PropelException.
     */
    public static function getTableMap()
    {
        return Propel::getServiceContainer()->getDatabaseMap(UserInfoTableMap::DATABASE_NAME)->getTable(UserInfoTableMap::TABLE_NAME);
    }

    /**
     * Add a TableMap instance to the database for this tableMap class.
     */
    public static function buildTableMap()
    {
        $dbMap = Propel::getServiceContainer()->getDatabaseMap(UserInfoTableMap::DATABASE_NAME);
        if (!$dbMap->hasTable(UserInfoTableMap::TABLE_NAME)) {
            $dbMap->addTableObject(new UserInfoTableMap());
        }
    }

    /**
     * Performs a DELETE on the database, given a UserInfo or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or UserInfo object or primary key or array of primary keys
     *              which is used to create the DELETE statement
     * @param  ConnectionInterface $con the connection to use
     * @return int             The number of affected rows (if supported by underlying database driver).  This includes CASCADE-related rows
     *                         if supported by native driver or if emulated using Propel.
     * @throws PropelException Any exceptions caught during processing will be
     *                         rethrown wrapped into a PropelException.
     */
     public static function doDelete($values, ConnectionInterface $con = null)
     {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(UserInfoTableMap::DATABASE_NAME);
        }

        if ($values instanceof Criteria) {
            // rename for clarity
            $criteria = $values;
        } elseif ($values instanceof \UserInfo) { // it's a model object
            // create criteria based on pk values
            $criteria = $values->buildPkeyCriteria();
        } else { // it's a primary key, or an array of pks
            $criteria = new Criteria(UserInfoTableMap::DATABASE_NAME);
            $criteria->add(UserInfoTableMap::COL_USER_ID, (array) $values, Criteria::IN);
        }

        $query = UserInfoQuery::create()->mergeWith($criteria);

        if ($values instanceof Criteria) {
            UserInfoTableMap::clearInstancePool();
        } elseif (!is_object($values)) { // it's a primary key, or an array of pks
            foreach ((array) $values as $singleval) {
                UserInfoTableMap::removeInstanceFromPool($singleval);
            }
        }

        return $query->delete($con);
    }

    /**
     * Deletes all rows from the userinfo table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public static function doDeleteAll(ConnectionInterface $con = null)
    {
        return UserInfoQuery::create()->doDeleteAll($con);
    }

    /**
     * Performs an INSERT on the database, given a UserInfo or Criteria object.
     *
     * @param mixed               $criteria Criteria or UserInfo object containing data that is used to create the INSERT statement.
     * @param ConnectionInterface $con the ConnectionInterface connection to use
     * @return mixed           The new primary key.
     * @throws PropelException Any exceptions caught during processing will be
     *                         rethrown wrapped into a PropelException.
     */
    public static function doInsert($criteria, ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(UserInfoTableMap::DATABASE_NAME);
        }

        if ($criteria instanceof Criteria) {
            $criteria = clone $criteria; // rename for clarity
        } else {
            $criteria = $criteria->buildCriteria(); // build Criteria from UserInfo object
        }


        // Set the correct dbName
        $query = UserInfoQuery::create()->mergeWith($criteria);

        // use transaction because $criteria could contain info
        // for more than one table (I guess, conceivably)
        return $con->transaction(function () use ($con, $query) {
            return $query->doInsert($con);
        });
    }

} // UserInfoTableMap
// This is the static code needed to register the TableMap for this table with the main Propel class.
//
UserInfoTableMap::buildTableMap();
