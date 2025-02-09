<?php

namespace Base;

use \Calendar as ChildCalendar;
use \CalendarQuery as ChildCalendarQuery;
use \Project as ChildProject;
use \ProjectInfo as ChildProjectInfo;
use \ProjectInfoQuery as ChildProjectInfoQuery;
use \ProjectQuery as ChildProjectQuery;
use \TeamProject as ChildTeamProject;
use \TeamProjectQuery as ChildTeamProjectQuery;
use \WorkStatus as ChildWorkStatus;
use \WorkStatusQuery as ChildWorkStatusQuery;
use \DateTime;
use \Exception;
use \PDO;
use Map\CalendarTableMap;
use Map\ProjectTableMap;
use Map\TeamProjectTableMap;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveRecord\ActiveRecordInterface;
use Propel\Runtime\Collection\Collection;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\BadMethodCallException;
use Propel\Runtime\Exception\LogicException;
use Propel\Runtime\Exception\PropelException;
use Propel\Runtime\Map\TableMap;
use Propel\Runtime\Parser\AbstractParser;
use Propel\Runtime\Util\PropelDateTime;

/**
 * Base class that represents a row from the 'project' table.
 *
 * 
 *
 * @package    propel.generator..Base
 */
abstract class Project implements ActiveRecordInterface 
{
    /**
     * TableMap class name
     */
    const TABLE_MAP = '\\Map\\ProjectTableMap';


    /**
     * attribute to determine if this object has previously been saved.
     * @var boolean
     */
    protected $new = true;

    /**
     * attribute to determine whether this object has been deleted.
     * @var boolean
     */
    protected $deleted = false;

    /**
     * The columns that have been modified in current object.
     * Tracking modified columns allows us to only update modified columns.
     * @var array
     */
    protected $modifiedColumns = array();

    /**
     * The (virtual) columns that are added at runtime
     * The formatters can add supplementary columns based on a resultset
     * @var array
     */
    protected $virtualColumns = array();

    /**
     * The value for the id field.
     * 
     * @var        int
     */
    protected $id;

    /**
     * The value for the name field.
     * 
     * @var        string
     */
    protected $name;

    /**
     * The value for the start field.
     * 
     * @var        DateTime
     */
    protected $start;

    /**
     * The value for the end field.
     * 
     * @var        DateTime
     */
    protected $end;

    /**
     * The value for the status_id field.
     * 
     * @var        string
     */
    protected $status_id;

    /**
     * @var        ChildWorkStatus
     */
    protected $aWorkStatus;

    /**
     * @var        ObjectCollection|ChildCalendar[] Collection to store aggregation of ChildCalendar objects.
     */
    protected $collCalendars;
    protected $collCalendarsPartial;

    /**
     * @var        ObjectCollection|ChildTeamProject[] Collection to store aggregation of ChildTeamProject objects.
     */
    protected $collTeamProjects;
    protected $collTeamProjectsPartial;

    /**
     * @var        ChildProjectInfo one-to-one related ChildProjectInfo object
     */
    protected $singleProjectInfo;

    /**
     * Flag to prevent endless save loop, if this object is referenced
     * by another object which falls in this transaction.
     *
     * @var boolean
     */
    protected $alreadyInSave = false;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection|ChildCalendar[]
     */
    protected $calendarsScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection|ChildTeamProject[]
     */
    protected $teamProjectsScheduledForDeletion = null;

    /**
     * Initializes internal state of Base\Project object.
     */
    public function __construct()
    {
    }

    /**
     * Returns whether the object has been modified.
     *
     * @return boolean True if the object has been modified.
     */
    public function isModified()
    {
        return !!$this->modifiedColumns;
    }

    /**
     * Has specified column been modified?
     *
     * @param  string  $col column fully qualified name (TableMap::TYPE_COLNAME), e.g. Book::AUTHOR_ID
     * @return boolean True if $col has been modified.
     */
    public function isColumnModified($col)
    {
        return $this->modifiedColumns && isset($this->modifiedColumns[$col]);
    }

    /**
     * Get the columns that have been modified in this object.
     * @return array A unique list of the modified column names for this object.
     */
    public function getModifiedColumns()
    {
        return $this->modifiedColumns ? array_keys($this->modifiedColumns) : [];
    }

    /**
     * Returns whether the object has ever been saved.  This will
     * be false, if the object was retrieved from storage or was created
     * and then saved.
     *
     * @return boolean true, if the object has never been persisted.
     */
    public function isNew()
    {
        return $this->new;
    }

    /**
     * Setter for the isNew attribute.  This method will be called
     * by Propel-generated children and objects.
     *
     * @param boolean $b the state of the object.
     */
    public function setNew($b)
    {
        $this->new = (boolean) $b;
    }

    /**
     * Whether this object has been deleted.
     * @return boolean The deleted state of this object.
     */
    public function isDeleted()
    {
        return $this->deleted;
    }

    /**
     * Specify whether this object has been deleted.
     * @param  boolean $b The deleted state of this object.
     * @return void
     */
    public function setDeleted($b)
    {
        $this->deleted = (boolean) $b;
    }

    /**
     * Sets the modified state for the object to be false.
     * @param  string $col If supplied, only the specified column is reset.
     * @return void
     */
    public function resetModified($col = null)
    {
        if (null !== $col) {
            if (isset($this->modifiedColumns[$col])) {
                unset($this->modifiedColumns[$col]);
            }
        } else {
            $this->modifiedColumns = array();
        }
    }

    /**
     * Compares this with another <code>Project</code> instance.  If
     * <code>obj</code> is an instance of <code>Project</code>, delegates to
     * <code>equals(Project)</code>.  Otherwise, returns <code>false</code>.
     *
     * @param  mixed   $obj The object to compare to.
     * @return boolean Whether equal to the object specified.
     */
    public function equals($obj)
    {
        if (!$obj instanceof static) {
            return false;
        }

        if ($this === $obj) {
            return true;
        }

        if (null === $this->getPrimaryKey() || null === $obj->getPrimaryKey()) {
            return false;
        }

        return $this->getPrimaryKey() === $obj->getPrimaryKey();
    }

    /**
     * Get the associative array of the virtual columns in this object
     *
     * @return array
     */
    public function getVirtualColumns()
    {
        return $this->virtualColumns;
    }

    /**
     * Checks the existence of a virtual column in this object
     *
     * @param  string  $name The virtual column name
     * @return boolean
     */
    public function hasVirtualColumn($name)
    {
        return array_key_exists($name, $this->virtualColumns);
    }

    /**
     * Get the value of a virtual column in this object
     *
     * @param  string $name The virtual column name
     * @return mixed
     *
     * @throws PropelException
     */
    public function getVirtualColumn($name)
    {
        if (!$this->hasVirtualColumn($name)) {
            throw new PropelException(sprintf('Cannot get value of inexistent virtual column %s.', $name));
        }

        return $this->virtualColumns[$name];
    }

    /**
     * Set the value of a virtual column in this object
     *
     * @param string $name  The virtual column name
     * @param mixed  $value The value to give to the virtual column
     *
     * @return $this|Project The current object, for fluid interface
     */
    public function setVirtualColumn($name, $value)
    {
        $this->virtualColumns[$name] = $value;

        return $this;
    }

    /**
     * Logs a message using Propel::log().
     *
     * @param  string  $msg
     * @param  int     $priority One of the Propel::LOG_* logging levels
     * @return boolean
     */
    protected function log($msg, $priority = Propel::LOG_INFO)
    {
        return Propel::log(get_class($this) . ': ' . $msg, $priority);
    }

    /**
     * Export the current object properties to a string, using a given parser format
     * <code>
     * $book = BookQuery::create()->findPk(9012);
     * echo $book->exportTo('JSON');
     *  => {"Id":9012,"Title":"Don Juan","ISBN":"0140422161","Price":12.99,"PublisherId":1234,"AuthorId":5678}');
     * </code>
     *
     * @param  mixed   $parser                 A AbstractParser instance, or a format name ('XML', 'YAML', 'JSON', 'CSV')
     * @param  boolean $includeLazyLoadColumns (optional) Whether to include lazy load(ed) columns. Defaults to TRUE.
     * @return string  The exported data
     */
    public function exportTo($parser, $includeLazyLoadColumns = true)
    {
        if (!$parser instanceof AbstractParser) {
            $parser = AbstractParser::getParser($parser);
        }

        return $parser->fromArray($this->toArray(TableMap::TYPE_PHPNAME, $includeLazyLoadColumns, array(), true));
    }

    /**
     * Clean up internal collections prior to serializing
     * Avoids recursive loops that turn into segmentation faults when serializing
     */
    public function __sleep()
    {
        $this->clearAllReferences();

        $cls = new \ReflectionClass($this);
        $propertyNames = [];
        $serializableProperties = array_diff($cls->getProperties(), $cls->getProperties(\ReflectionProperty::IS_STATIC));
        
        foreach($serializableProperties as $property) {
            $propertyNames[] = $property->getName();
        }
        
        return $propertyNames;
    }

    /**
     * Get the [id] column value.
     * 
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get the [name] column value.
     * 
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get the [optionally formatted] temporal [start] column value.
     * 
     *
     * @param      string $format The date/time format string (either date()-style or strftime()-style).
     *                            If format is NULL, then the raw DateTime object will be returned.
     *
     * @return string|DateTime Formatted date/time value as string or DateTime object (if format is NULL), NULL if column is NULL, and 0 if column value is 0000-00-00
     *
     * @throws PropelException - if unable to parse/validate the date/time value.
     */
    public function getStart($format = NULL)
    {
        if ($format === null) {
            return $this->start;
        } else {
            return $this->start instanceof \DateTimeInterface ? $this->start->format($format) : null;
        }
    }

    /**
     * Get the [optionally formatted] temporal [end] column value.
     * 
     *
     * @param      string $format The date/time format string (either date()-style or strftime()-style).
     *                            If format is NULL, then the raw DateTime object will be returned.
     *
     * @return string|DateTime Formatted date/time value as string or DateTime object (if format is NULL), NULL if column is NULL, and 0 if column value is 0000-00-00
     *
     * @throws PropelException - if unable to parse/validate the date/time value.
     */
    public function getEnd($format = NULL)
    {
        if ($format === null) {
            return $this->end;
        } else {
            return $this->end instanceof \DateTimeInterface ? $this->end->format($format) : null;
        }
    }

    /**
     * Get the [status_id] column value.
     * 
     * @return string
     */
    public function getStatusId()
    {
        return $this->status_id;
    }

    /**
     * Set the value of [id] column.
     * 
     * @param int $v new value
     * @return $this|\Project The current object (for fluent API support)
     */
    public function setId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->id !== $v) {
            $this->id = $v;
            $this->modifiedColumns[ProjectTableMap::COL_ID] = true;
        }

        return $this;
    } // setId()

    /**
     * Set the value of [name] column.
     * 
     * @param string $v new value
     * @return $this|\Project The current object (for fluent API support)
     */
    public function setName($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->name !== $v) {
            $this->name = $v;
            $this->modifiedColumns[ProjectTableMap::COL_NAME] = true;
        }

        return $this;
    } // setName()

    /**
     * Sets the value of [start] column to a normalized version of the date/time value specified.
     * 
     * @param  mixed $v string, integer (timestamp), or \DateTimeInterface value.
     *               Empty strings are treated as NULL.
     * @return $this|\Project The current object (for fluent API support)
     */
    public function setStart($v)
    {
        $dt = PropelDateTime::newInstance($v, null, 'DateTime');
        if ($this->start !== null || $dt !== null) {
            if ($this->start === null || $dt === null || $dt->format("Y-m-d") !== $this->start->format("Y-m-d")) {
                $this->start = $dt === null ? null : clone $dt;
                $this->modifiedColumns[ProjectTableMap::COL_START] = true;
            }
        } // if either are not null

        return $this;
    } // setStart()

    /**
     * Sets the value of [end] column to a normalized version of the date/time value specified.
     * 
     * @param  mixed $v string, integer (timestamp), or \DateTimeInterface value.
     *               Empty strings are treated as NULL.
     * @return $this|\Project The current object (for fluent API support)
     */
    public function setEnd($v)
    {
        $dt = PropelDateTime::newInstance($v, null, 'DateTime');
        if ($this->end !== null || $dt !== null) {
            if ($this->end === null || $dt === null || $dt->format("Y-m-d") !== $this->end->format("Y-m-d")) {
                $this->end = $dt === null ? null : clone $dt;
                $this->modifiedColumns[ProjectTableMap::COL_END] = true;
            }
        } // if either are not null

        return $this;
    } // setEnd()

    /**
     * Set the value of [status_id] column.
     * 
     * @param string $v new value
     * @return $this|\Project The current object (for fluent API support)
     */
    public function setStatusId($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->status_id !== $v) {
            $this->status_id = $v;
            $this->modifiedColumns[ProjectTableMap::COL_STATUS_ID] = true;
        }

        if ($this->aWorkStatus !== null && $this->aWorkStatus->getId() !== $v) {
            $this->aWorkStatus = null;
        }

        return $this;
    } // setStatusId()

    /**
     * Indicates whether the columns in this object are only set to default values.
     *
     * This method can be used in conjunction with isModified() to indicate whether an object is both
     * modified _and_ has some values set which are non-default.
     *
     * @return boolean Whether the columns in this object are only been set with default values.
     */
    public function hasOnlyDefaultValues()
    {
        // otherwise, everything was equal, so return TRUE
        return true;
    } // hasOnlyDefaultValues()

    /**
     * Hydrates (populates) the object variables with values from the database resultset.
     *
     * An offset (0-based "start column") is specified so that objects can be hydrated
     * with a subset of the columns in the resultset rows.  This is needed, for example,
     * for results of JOIN queries where the resultset row includes columns from two or
     * more tables.
     *
     * @param array   $row       The row returned by DataFetcher->fetch().
     * @param int     $startcol  0-based offset column which indicates which restultset column to start with.
     * @param boolean $rehydrate Whether this object is being re-hydrated from the database.
     * @param string  $indexType The index type of $row. Mostly DataFetcher->getIndexType().
                                  One of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_CAMELNAME
     *                            TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     *
     * @return int             next starting column
     * @throws PropelException - Any caught Exception will be rewrapped as a PropelException.
     */
    public function hydrate($row, $startcol = 0, $rehydrate = false, $indexType = TableMap::TYPE_NUM)
    {
        try {

            $col = $row[TableMap::TYPE_NUM == $indexType ? 0 + $startcol : ProjectTableMap::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)];
            $this->id = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 1 + $startcol : ProjectTableMap::translateFieldName('Name', TableMap::TYPE_PHPNAME, $indexType)];
            $this->name = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 2 + $startcol : ProjectTableMap::translateFieldName('Start', TableMap::TYPE_PHPNAME, $indexType)];
            if ($col === '0000-00-00') {
                $col = null;
            }
            $this->start = (null !== $col) ? PropelDateTime::newInstance($col, null, 'DateTime') : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 3 + $startcol : ProjectTableMap::translateFieldName('End', TableMap::TYPE_PHPNAME, $indexType)];
            if ($col === '0000-00-00') {
                $col = null;
            }
            $this->end = (null !== $col) ? PropelDateTime::newInstance($col, null, 'DateTime') : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 4 + $startcol : ProjectTableMap::translateFieldName('StatusId', TableMap::TYPE_PHPNAME, $indexType)];
            $this->status_id = (null !== $col) ? (string) $col : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }

            return $startcol + 5; // 5 = ProjectTableMap::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException(sprintf('Error populating %s object', '\\Project'), 0, $e);
        }
    }

    /**
     * Checks and repairs the internal consistency of the object.
     *
     * This method is executed after an already-instantiated object is re-hydrated
     * from the database.  It exists to check any foreign keys to make sure that
     * the objects related to the current object are correct based on foreign key.
     *
     * You can override this method in the stub class, but you should always invoke
     * the base method from the overridden method (i.e. parent::ensureConsistency()),
     * in case your model changes.
     *
     * @throws PropelException
     */
    public function ensureConsistency()
    {
        if ($this->aWorkStatus !== null && $this->status_id !== $this->aWorkStatus->getId()) {
            $this->aWorkStatus = null;
        }
    } // ensureConsistency

    /**
     * Reloads this object from datastore based on primary key and (optionally) resets all associated objects.
     *
     * This will only work if the object has been saved and has a valid primary key set.
     *
     * @param      boolean $deep (optional) Whether to also de-associated any related objects.
     * @param      ConnectionInterface $con (optional) The ConnectionInterface connection to use.
     * @return void
     * @throws PropelException - if this object is deleted, unsaved or doesn't have pk match in db
     */
    public function reload($deep = false, ConnectionInterface $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("Cannot reload a deleted object.");
        }

        if ($this->isNew()) {
            throw new PropelException("Cannot reload an unsaved object.");
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(ProjectTableMap::DATABASE_NAME);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $dataFetcher = ChildProjectQuery::create(null, $this->buildPkeyCriteria())->setFormatter(ModelCriteria::FORMAT_STATEMENT)->find($con);
        $row = $dataFetcher->fetch();
        $dataFetcher->close();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true, $dataFetcher->getIndexType()); // rehydrate

        if ($deep) {  // also de-associate any related objects?

            $this->aWorkStatus = null;
            $this->collCalendars = null;

            $this->collTeamProjects = null;

            $this->singleProjectInfo = null;

        } // if (deep)
    }

    /**
     * Removes this object from datastore and sets delete attribute.
     *
     * @param      ConnectionInterface $con
     * @return void
     * @throws PropelException
     * @see Project::setDeleted()
     * @see Project::isDeleted()
     */
    public function delete(ConnectionInterface $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("This object has already been deleted.");
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getWriteConnection(ProjectTableMap::DATABASE_NAME);
        }

        $con->transaction(function () use ($con) {
            $deleteQuery = ChildProjectQuery::create()
                ->filterByPrimaryKey($this->getPrimaryKey());
            $ret = $this->preDelete($con);
            if ($ret) {
                $deleteQuery->delete($con);
                $this->postDelete($con);
                $this->setDeleted(true);
            }
        });
    }

    /**
     * Persists this object to the database.
     *
     * If the object is new, it inserts it; otherwise an update is performed.
     * All modified related objects will also be persisted in the doSave()
     * method.  This method wraps all precipitate database operations in a
     * single transaction.
     *
     * @param      ConnectionInterface $con
     * @return int             The number of rows affected by this insert/update and any referring fk objects' save() operations.
     * @throws PropelException
     * @see doSave()
     */
    public function save(ConnectionInterface $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("You cannot save an object that has been deleted.");
        }

        if ($this->alreadyInSave) {
            return 0;
        }
 
        if ($con === null) {
            $con = Propel::getServiceContainer()->getWriteConnection(ProjectTableMap::DATABASE_NAME);
        }

        return $con->transaction(function () use ($con) {
            $ret = $this->preSave($con);
            $isInsert = $this->isNew();
            if ($isInsert) {
                $ret = $ret && $this->preInsert($con);
            } else {
                $ret = $ret && $this->preUpdate($con);
            }
            if ($ret) {
                $affectedRows = $this->doSave($con);
                if ($isInsert) {
                    $this->postInsert($con);
                } else {
                    $this->postUpdate($con);
                }
                $this->postSave($con);
                ProjectTableMap::addInstanceToPool($this);
            } else {
                $affectedRows = 0;
            }

            return $affectedRows;
        });
    }

    /**
     * Performs the work of inserting or updating the row in the database.
     *
     * If the object is new, it inserts it; otherwise an update is performed.
     * All related objects are also updated in this method.
     *
     * @param      ConnectionInterface $con
     * @return int             The number of rows affected by this insert/update and any referring fk objects' save() operations.
     * @throws PropelException
     * @see save()
     */
    protected function doSave(ConnectionInterface $con)
    {
        $affectedRows = 0; // initialize var to track total num of affected rows
        if (!$this->alreadyInSave) {
            $this->alreadyInSave = true;

            // We call the save method on the following object(s) if they
            // were passed to this object by their corresponding set
            // method.  This object relates to these object(s) by a
            // foreign key reference.

            if ($this->aWorkStatus !== null) {
                if ($this->aWorkStatus->isModified() || $this->aWorkStatus->isNew()) {
                    $affectedRows += $this->aWorkStatus->save($con);
                }
                $this->setWorkStatus($this->aWorkStatus);
            }

            if ($this->isNew() || $this->isModified()) {
                // persist changes
                if ($this->isNew()) {
                    $this->doInsert($con);
                    $affectedRows += 1;
                } else {
                    $affectedRows += $this->doUpdate($con);
                }
                $this->resetModified();
            }

            if ($this->calendarsScheduledForDeletion !== null) {
                if (!$this->calendarsScheduledForDeletion->isEmpty()) {
                    \CalendarQuery::create()
                        ->filterByPrimaryKeys($this->calendarsScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->calendarsScheduledForDeletion = null;
                }
            }

            if ($this->collCalendars !== null) {
                foreach ($this->collCalendars as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->teamProjectsScheduledForDeletion !== null) {
                if (!$this->teamProjectsScheduledForDeletion->isEmpty()) {
                    \TeamProjectQuery::create()
                        ->filterByPrimaryKeys($this->teamProjectsScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->teamProjectsScheduledForDeletion = null;
                }
            }

            if ($this->collTeamProjects !== null) {
                foreach ($this->collTeamProjects as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->singleProjectInfo !== null) {
                if (!$this->singleProjectInfo->isDeleted() && ($this->singleProjectInfo->isNew() || $this->singleProjectInfo->isModified())) {
                    $affectedRows += $this->singleProjectInfo->save($con);
                }
            }

            $this->alreadyInSave = false;

        }

        return $affectedRows;
    } // doSave()

    /**
     * Insert the row in the database.
     *
     * @param      ConnectionInterface $con
     *
     * @throws PropelException
     * @see doSave()
     */
    protected function doInsert(ConnectionInterface $con)
    {
        $modifiedColumns = array();
        $index = 0;

        $this->modifiedColumns[ProjectTableMap::COL_ID] = true;
        if (null !== $this->id) {
            throw new PropelException('Cannot insert a value for auto-increment primary key (' . ProjectTableMap::COL_ID . ')');
        }

         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(ProjectTableMap::COL_ID)) {
            $modifiedColumns[':p' . $index++]  = 'id';
        }
        if ($this->isColumnModified(ProjectTableMap::COL_NAME)) {
            $modifiedColumns[':p' . $index++]  = 'Name';
        }
        if ($this->isColumnModified(ProjectTableMap::COL_START)) {
            $modifiedColumns[':p' . $index++]  = 'Start';
        }
        if ($this->isColumnModified(ProjectTableMap::COL_END)) {
            $modifiedColumns[':p' . $index++]  = 'End';
        }
        if ($this->isColumnModified(ProjectTableMap::COL_STATUS_ID)) {
            $modifiedColumns[':p' . $index++]  = 'Status_id';
        }

        $sql = sprintf(
            'INSERT INTO project (%s) VALUES (%s)',
            implode(', ', $modifiedColumns),
            implode(', ', array_keys($modifiedColumns))
        );

        try {
            $stmt = $con->prepare($sql);
            foreach ($modifiedColumns as $identifier => $columnName) {
                switch ($columnName) {
                    case 'id':                        
                        $stmt->bindValue($identifier, $this->id, PDO::PARAM_INT);
                        break;
                    case 'Name':                        
                        $stmt->bindValue($identifier, $this->name, PDO::PARAM_STR);
                        break;
                    case 'Start':                        
                        $stmt->bindValue($identifier, $this->start ? $this->start->format("Y-m-d H:i:s.u") : null, PDO::PARAM_STR);
                        break;
                    case 'End':                        
                        $stmt->bindValue($identifier, $this->end ? $this->end->format("Y-m-d H:i:s.u") : null, PDO::PARAM_STR);
                        break;
                    case 'Status_id':                        
                        $stmt->bindValue($identifier, $this->status_id, PDO::PARAM_STR);
                        break;
                }
            }
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute INSERT statement [%s]', $sql), 0, $e);
        }

        try {
            $pk = $con->lastInsertId();
        } catch (Exception $e) {
            throw new PropelException('Unable to get autoincrement id.', 0, $e);
        }
        $this->setId($pk);

        $this->setNew(false);
    }

    /**
     * Update the row in the database.
     *
     * @param      ConnectionInterface $con
     *
     * @return Integer Number of updated rows
     * @see doSave()
     */
    protected function doUpdate(ConnectionInterface $con)
    {
        $selectCriteria = $this->buildPkeyCriteria();
        $valuesCriteria = $this->buildCriteria();

        return $selectCriteria->doUpdate($valuesCriteria, $con);
    }

    /**
     * Retrieves a field from the object by name passed in as a string.
     *
     * @param      string $name name
     * @param      string $type The type of fieldname the $name is of:
     *                     one of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_CAMELNAME
     *                     TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     *                     Defaults to TableMap::TYPE_PHPNAME.
     * @return mixed Value of field.
     */
    public function getByName($name, $type = TableMap::TYPE_PHPNAME)
    {
        $pos = ProjectTableMap::translateFieldName($name, $type, TableMap::TYPE_NUM);
        $field = $this->getByPosition($pos);

        return $field;
    }

    /**
     * Retrieves a field from the object by Position as specified in the xml schema.
     * Zero-based.
     *
     * @param      int $pos position in xml schema
     * @return mixed Value of field at $pos
     */
    public function getByPosition($pos)
    {
        switch ($pos) {
            case 0:
                return $this->getId();
                break;
            case 1:
                return $this->getName();
                break;
            case 2:
                return $this->getStart();
                break;
            case 3:
                return $this->getEnd();
                break;
            case 4:
                return $this->getStatusId();
                break;
            default:
                return null;
                break;
        } // switch()
    }

    /**
     * Exports the object as an array.
     *
     * You can specify the key type of the array by passing one of the class
     * type constants.
     *
     * @param     string  $keyType (optional) One of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_CAMELNAME,
     *                    TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     *                    Defaults to TableMap::TYPE_PHPNAME.
     * @param     boolean $includeLazyLoadColumns (optional) Whether to include lazy loaded columns. Defaults to TRUE.
     * @param     array $alreadyDumpedObjects List of objects to skip to avoid recursion
     * @param     boolean $includeForeignObjects (optional) Whether to include hydrated related objects. Default to FALSE.
     *
     * @return array an associative array containing the field names (as keys) and field values
     */
    public function toArray($keyType = TableMap::TYPE_PHPNAME, $includeLazyLoadColumns = true, $alreadyDumpedObjects = array(), $includeForeignObjects = false)
    {

        if (isset($alreadyDumpedObjects['Project'][$this->hashCode()])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['Project'][$this->hashCode()] = true;
        $keys = ProjectTableMap::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getId(),
            $keys[1] => $this->getName(),
            $keys[2] => $this->getStart(),
            $keys[3] => $this->getEnd(),
            $keys[4] => $this->getStatusId(),
        );
        if ($result[$keys[2]] instanceof \DateTime) {
            $result[$keys[2]] = $result[$keys[2]]->format('c');
        }
        
        if ($result[$keys[3]] instanceof \DateTime) {
            $result[$keys[3]] = $result[$keys[3]]->format('c');
        }
        
        $virtualColumns = $this->virtualColumns;
        foreach ($virtualColumns as $key => $virtualColumn) {
            $result[$key] = $virtualColumn;
        }
        
        if ($includeForeignObjects) {
            if (null !== $this->aWorkStatus) {
                
                switch ($keyType) {
                    case TableMap::TYPE_CAMELNAME:
                        $key = 'workStatus';
                        break;
                    case TableMap::TYPE_FIELDNAME:
                        $key = 'work_status';
                        break;
                    default:
                        $key = 'WorkStatus';
                }
        
                $result[$key] = $this->aWorkStatus->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
            if (null !== $this->collCalendars) {
                
                switch ($keyType) {
                    case TableMap::TYPE_CAMELNAME:
                        $key = 'calendars';
                        break;
                    case TableMap::TYPE_FIELDNAME:
                        $key = 'calendars';
                        break;
                    default:
                        $key = 'Calendars';
                }
        
                $result[$key] = $this->collCalendars->toArray(null, false, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collTeamProjects) {
                
                switch ($keyType) {
                    case TableMap::TYPE_CAMELNAME:
                        $key = 'teamProjects';
                        break;
                    case TableMap::TYPE_FIELDNAME:
                        $key = 'team_projects';
                        break;
                    default:
                        $key = 'TeamProjects';
                }
        
                $result[$key] = $this->collTeamProjects->toArray(null, false, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->singleProjectInfo) {
                
                switch ($keyType) {
                    case TableMap::TYPE_CAMELNAME:
                        $key = 'projectInfo';
                        break;
                    case TableMap::TYPE_FIELDNAME:
                        $key = 'projectinfo';
                        break;
                    default:
                        $key = 'ProjectInfo';
                }
        
                $result[$key] = $this->singleProjectInfo->toArray($keyType, $includeLazyLoadColumns, $alreadyDumpedObjects, true);
            }
        }

        return $result;
    }

    /**
     * Sets a field from the object by name passed in as a string.
     *
     * @param  string $name
     * @param  mixed  $value field value
     * @param  string $type The type of fieldname the $name is of:
     *                one of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_CAMELNAME
     *                TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     *                Defaults to TableMap::TYPE_PHPNAME.
     * @return $this|\Project
     */
    public function setByName($name, $value, $type = TableMap::TYPE_PHPNAME)
    {
        $pos = ProjectTableMap::translateFieldName($name, $type, TableMap::TYPE_NUM);

        return $this->setByPosition($pos, $value);
    }

    /**
     * Sets a field from the object by Position as specified in the xml schema.
     * Zero-based.
     *
     * @param  int $pos position in xml schema
     * @param  mixed $value field value
     * @return $this|\Project
     */
    public function setByPosition($pos, $value)
    {
        switch ($pos) {
            case 0:
                $this->setId($value);
                break;
            case 1:
                $this->setName($value);
                break;
            case 2:
                $this->setStart($value);
                break;
            case 3:
                $this->setEnd($value);
                break;
            case 4:
                $this->setStatusId($value);
                break;
        } // switch()

        return $this;
    }

    /**
     * Populates the object using an array.
     *
     * This is particularly useful when populating an object from one of the
     * request arrays (e.g. $_POST).  This method goes through the column
     * names, checking to see whether a matching key exists in populated
     * array. If so the setByName() method is called for that column.
     *
     * You can specify the key type of the array by additionally passing one
     * of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_CAMELNAME,
     * TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     * The default key type is the column's TableMap::TYPE_PHPNAME.
     *
     * @param      array  $arr     An array to populate the object from.
     * @param      string $keyType The type of keys the array uses.
     * @return void
     */
    public function fromArray($arr, $keyType = TableMap::TYPE_PHPNAME)
    {
        $keys = ProjectTableMap::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) {
            $this->setId($arr[$keys[0]]);
        }
        if (array_key_exists($keys[1], $arr)) {
            $this->setName($arr[$keys[1]]);
        }
        if (array_key_exists($keys[2], $arr)) {
            $this->setStart($arr[$keys[2]]);
        }
        if (array_key_exists($keys[3], $arr)) {
            $this->setEnd($arr[$keys[3]]);
        }
        if (array_key_exists($keys[4], $arr)) {
            $this->setStatusId($arr[$keys[4]]);
        }
    }

     /**
     * Populate the current object from a string, using a given parser format
     * <code>
     * $book = new Book();
     * $book->importFrom('JSON', '{"Id":9012,"Title":"Don Juan","ISBN":"0140422161","Price":12.99,"PublisherId":1234,"AuthorId":5678}');
     * </code>
     *
     * You can specify the key type of the array by additionally passing one
     * of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_CAMELNAME,
     * TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     * The default key type is the column's TableMap::TYPE_PHPNAME.
     *
     * @param mixed $parser A AbstractParser instance,
     *                       or a format name ('XML', 'YAML', 'JSON', 'CSV')
     * @param string $data The source data to import from
     * @param string $keyType The type of keys the array uses.
     *
     * @return $this|\Project The current object, for fluid interface
     */
    public function importFrom($parser, $data, $keyType = TableMap::TYPE_PHPNAME)
    {
        if (!$parser instanceof AbstractParser) {
            $parser = AbstractParser::getParser($parser);
        }

        $this->fromArray($parser->toArray($data), $keyType);

        return $this;
    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(ProjectTableMap::DATABASE_NAME);

        if ($this->isColumnModified(ProjectTableMap::COL_ID)) {
            $criteria->add(ProjectTableMap::COL_ID, $this->id);
        }
        if ($this->isColumnModified(ProjectTableMap::COL_NAME)) {
            $criteria->add(ProjectTableMap::COL_NAME, $this->name);
        }
        if ($this->isColumnModified(ProjectTableMap::COL_START)) {
            $criteria->add(ProjectTableMap::COL_START, $this->start);
        }
        if ($this->isColumnModified(ProjectTableMap::COL_END)) {
            $criteria->add(ProjectTableMap::COL_END, $this->end);
        }
        if ($this->isColumnModified(ProjectTableMap::COL_STATUS_ID)) {
            $criteria->add(ProjectTableMap::COL_STATUS_ID, $this->status_id);
        }

        return $criteria;
    }

    /**
     * Builds a Criteria object containing the primary key for this object.
     *
     * Unlike buildCriteria() this method includes the primary key values regardless
     * of whether or not they have been modified.
     *
     * @throws LogicException if no primary key is defined
     *
     * @return Criteria The Criteria object containing value(s) for primary key(s).
     */
    public function buildPkeyCriteria()
    {
        $criteria = ChildProjectQuery::create();
        $criteria->add(ProjectTableMap::COL_ID, $this->id);

        return $criteria;
    }

    /**
     * If the primary key is not null, return the hashcode of the
     * primary key. Otherwise, return the hash code of the object.
     *
     * @return int Hashcode
     */
    public function hashCode()
    {
        $validPk = null !== $this->getId();

        $validPrimaryKeyFKs = 0;
        $primaryKeyFKs = [];

        if ($validPk) {
            return crc32(json_encode($this->getPrimaryKey(), JSON_UNESCAPED_UNICODE));
        } elseif ($validPrimaryKeyFKs) {
            return crc32(json_encode($primaryKeyFKs, JSON_UNESCAPED_UNICODE));
        }

        return spl_object_hash($this);
    }
        
    /**
     * Returns the primary key for this object (row).
     * @return int
     */
    public function getPrimaryKey()
    {
        return $this->getId();
    }

    /**
     * Generic method to set the primary key (id column).
     *
     * @param       int $key Primary key.
     * @return void
     */
    public function setPrimaryKey($key)
    {
        $this->setId($key);
    }

    /**
     * Returns true if the primary key for this object is null.
     * @return boolean
     */
    public function isPrimaryKeyNull()
    {
        return null === $this->getId();
    }

    /**
     * Sets contents of passed object to values from current object.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param      object $copyObj An object of \Project (or compatible) type.
     * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param      boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setName($this->getName());
        $copyObj->setStart($this->getStart());
        $copyObj->setEnd($this->getEnd());
        $copyObj->setStatusId($this->getStatusId());

        if ($deepCopy) {
            // important: temporarily setNew(false) because this affects the behavior of
            // the getter/setter methods for fkey referrer objects.
            $copyObj->setNew(false);

            foreach ($this->getCalendars() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addCalendar($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getTeamProjects() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addTeamProject($relObj->copy($deepCopy));
                }
            }

            $relObj = $this->getProjectInfo();
            if ($relObj) {
                $copyObj->setProjectInfo($relObj->copy($deepCopy));
            }

        } // if ($deepCopy)

        if ($makeNew) {
            $copyObj->setNew(true);
            $copyObj->setId(NULL); // this is a auto-increment column, so set to default value
        }
    }

    /**
     * Makes a copy of this object that will be inserted as a new row in table when saved.
     * It creates a new object filling in the simple attributes, but skipping any primary
     * keys that are defined for the table.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param  boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @return \Project Clone of current object.
     * @throws PropelException
     */
    public function copy($deepCopy = false)
    {
        // we use get_class(), because this might be a subclass
        $clazz = get_class($this);
        $copyObj = new $clazz();
        $this->copyInto($copyObj, $deepCopy);

        return $copyObj;
    }

    /**
     * Declares an association between this object and a ChildWorkStatus object.
     *
     * @param  ChildWorkStatus $v
     * @return $this|\Project The current object (for fluent API support)
     * @throws PropelException
     */
    public function setWorkStatus(ChildWorkStatus $v = null)
    {
        if ($v === null) {
            $this->setStatusId(NULL);
        } else {
            $this->setStatusId($v->getId());
        }

        $this->aWorkStatus = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the ChildWorkStatus object, it will not be re-added.
        if ($v !== null) {
            $v->addProject($this);
        }


        return $this;
    }


    /**
     * Get the associated ChildWorkStatus object
     *
     * @param  ConnectionInterface $con Optional Connection object.
     * @return ChildWorkStatus The associated ChildWorkStatus object.
     * @throws PropelException
     */
    public function getWorkStatus(ConnectionInterface $con = null)
    {
        if ($this->aWorkStatus === null && (($this->status_id !== "" && $this->status_id !== null))) {
            $this->aWorkStatus = ChildWorkStatusQuery::create()->findPk($this->status_id, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aWorkStatus->addProjects($this);
             */
        }

        return $this->aWorkStatus;
    }


    /**
     * Initializes a collection based on the name of a relation.
     * Avoids crafting an 'init[$relationName]s' method name
     * that wouldn't work when StandardEnglishPluralizer is used.
     *
     * @param      string $relationName The name of the relation to initialize
     * @return void
     */
    public function initRelation($relationName)
    {
        if ('Calendar' == $relationName) {
            return $this->initCalendars();
        }
        if ('TeamProject' == $relationName) {
            return $this->initTeamProjects();
        }
    }

    /**
     * Clears out the collCalendars collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addCalendars()
     */
    public function clearCalendars()
    {
        $this->collCalendars = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collCalendars collection loaded partially.
     */
    public function resetPartialCalendars($v = true)
    {
        $this->collCalendarsPartial = $v;
    }

    /**
     * Initializes the collCalendars collection.
     *
     * By default this just sets the collCalendars collection to an empty array (like clearcollCalendars());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initCalendars($overrideExisting = true)
    {
        if (null !== $this->collCalendars && !$overrideExisting) {
            return;
        }

        $collectionClassName = CalendarTableMap::getTableMap()->getCollectionClassName();

        $this->collCalendars = new $collectionClassName;
        $this->collCalendars->setModel('\Calendar');
    }

    /**
     * Gets an array of ChildCalendar objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildProject is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return ObjectCollection|ChildCalendar[] List of ChildCalendar objects
     * @throws PropelException
     */
    public function getCalendars(Criteria $criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collCalendarsPartial && !$this->isNew();
        if (null === $this->collCalendars || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collCalendars) {
                // return empty collection
                $this->initCalendars();
            } else {
                $collCalendars = ChildCalendarQuery::create(null, $criteria)
                    ->filterByProject($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collCalendarsPartial && count($collCalendars)) {
                        $this->initCalendars(false);

                        foreach ($collCalendars as $obj) {
                            if (false == $this->collCalendars->contains($obj)) {
                                $this->collCalendars->append($obj);
                            }
                        }

                        $this->collCalendarsPartial = true;
                    }

                    return $collCalendars;
                }

                if ($partial && $this->collCalendars) {
                    foreach ($this->collCalendars as $obj) {
                        if ($obj->isNew()) {
                            $collCalendars[] = $obj;
                        }
                    }
                }

                $this->collCalendars = $collCalendars;
                $this->collCalendarsPartial = false;
            }
        }

        return $this->collCalendars;
    }

    /**
     * Sets a collection of ChildCalendar objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $calendars A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return $this|ChildProject The current object (for fluent API support)
     */
    public function setCalendars(Collection $calendars, ConnectionInterface $con = null)
    {
        /** @var ChildCalendar[] $calendarsToDelete */
        $calendarsToDelete = $this->getCalendars(new Criteria(), $con)->diff($calendars);

        
        //since at least one column in the foreign key is at the same time a PK
        //we can not just set a PK to NULL in the lines below. We have to store
        //a backup of all values, so we are able to manipulate these items based on the onDelete value later.
        $this->calendarsScheduledForDeletion = clone $calendarsToDelete;

        foreach ($calendarsToDelete as $calendarRemoved) {
            $calendarRemoved->setProject(null);
        }

        $this->collCalendars = null;
        foreach ($calendars as $calendar) {
            $this->addCalendar($calendar);
        }

        $this->collCalendars = $calendars;
        $this->collCalendarsPartial = false;

        return $this;
    }

    /**
     * Returns the number of related Calendar objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related Calendar objects.
     * @throws PropelException
     */
    public function countCalendars(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collCalendarsPartial && !$this->isNew();
        if (null === $this->collCalendars || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collCalendars) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getCalendars());
            }

            $query = ChildCalendarQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByProject($this)
                ->count($con);
        }

        return count($this->collCalendars);
    }

    /**
     * Method called to associate a ChildCalendar object to this object
     * through the ChildCalendar foreign key attribute.
     *
     * @param  ChildCalendar $l ChildCalendar
     * @return $this|\Project The current object (for fluent API support)
     */
    public function addCalendar(ChildCalendar $l)
    {
        if ($this->collCalendars === null) {
            $this->initCalendars();
            $this->collCalendarsPartial = true;
        }

        if (!$this->collCalendars->contains($l)) {
            $this->doAddCalendar($l);

            if ($this->calendarsScheduledForDeletion and $this->calendarsScheduledForDeletion->contains($l)) {
                $this->calendarsScheduledForDeletion->remove($this->calendarsScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param ChildCalendar $calendar The ChildCalendar object to add.
     */
    protected function doAddCalendar(ChildCalendar $calendar)
    {
        $this->collCalendars[]= $calendar;
        $calendar->setProject($this);
    }

    /**
     * @param  ChildCalendar $calendar The ChildCalendar object to remove.
     * @return $this|ChildProject The current object (for fluent API support)
     */
    public function removeCalendar(ChildCalendar $calendar)
    {
        if ($this->getCalendars()->contains($calendar)) {
            $pos = $this->collCalendars->search($calendar);
            $this->collCalendars->remove($pos);
            if (null === $this->calendarsScheduledForDeletion) {
                $this->calendarsScheduledForDeletion = clone $this->collCalendars;
                $this->calendarsScheduledForDeletion->clear();
            }
            $this->calendarsScheduledForDeletion[]= clone $calendar;
            $calendar->setProject(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Project is new, it will return
     * an empty collection; or if this Project has previously
     * been saved, it will retrieve related Calendars from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Project.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return ObjectCollection|ChildCalendar[] List of ChildCalendar objects
     */
    public function getCalendarsJoinUser(Criteria $criteria = null, ConnectionInterface $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ChildCalendarQuery::create(null, $criteria);
        $query->joinWith('User', $joinBehavior);

        return $this->getCalendars($query, $con);
    }

    /**
     * Clears out the collTeamProjects collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addTeamProjects()
     */
    public function clearTeamProjects()
    {
        $this->collTeamProjects = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collTeamProjects collection loaded partially.
     */
    public function resetPartialTeamProjects($v = true)
    {
        $this->collTeamProjectsPartial = $v;
    }

    /**
     * Initializes the collTeamProjects collection.
     *
     * By default this just sets the collTeamProjects collection to an empty array (like clearcollTeamProjects());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initTeamProjects($overrideExisting = true)
    {
        if (null !== $this->collTeamProjects && !$overrideExisting) {
            return;
        }

        $collectionClassName = TeamProjectTableMap::getTableMap()->getCollectionClassName();

        $this->collTeamProjects = new $collectionClassName;
        $this->collTeamProjects->setModel('\TeamProject');
    }

    /**
     * Gets an array of ChildTeamProject objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildProject is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return ObjectCollection|ChildTeamProject[] List of ChildTeamProject objects
     * @throws PropelException
     */
    public function getTeamProjects(Criteria $criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collTeamProjectsPartial && !$this->isNew();
        if (null === $this->collTeamProjects || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collTeamProjects) {
                // return empty collection
                $this->initTeamProjects();
            } else {
                $collTeamProjects = ChildTeamProjectQuery::create(null, $criteria)
                    ->filterByProject($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collTeamProjectsPartial && count($collTeamProjects)) {
                        $this->initTeamProjects(false);

                        foreach ($collTeamProjects as $obj) {
                            if (false == $this->collTeamProjects->contains($obj)) {
                                $this->collTeamProjects->append($obj);
                            }
                        }

                        $this->collTeamProjectsPartial = true;
                    }

                    return $collTeamProjects;
                }

                if ($partial && $this->collTeamProjects) {
                    foreach ($this->collTeamProjects as $obj) {
                        if ($obj->isNew()) {
                            $collTeamProjects[] = $obj;
                        }
                    }
                }

                $this->collTeamProjects = $collTeamProjects;
                $this->collTeamProjectsPartial = false;
            }
        }

        return $this->collTeamProjects;
    }

    /**
     * Sets a collection of ChildTeamProject objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $teamProjects A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return $this|ChildProject The current object (for fluent API support)
     */
    public function setTeamProjects(Collection $teamProjects, ConnectionInterface $con = null)
    {
        /** @var ChildTeamProject[] $teamProjectsToDelete */
        $teamProjectsToDelete = $this->getTeamProjects(new Criteria(), $con)->diff($teamProjects);

        
        //since at least one column in the foreign key is at the same time a PK
        //we can not just set a PK to NULL in the lines below. We have to store
        //a backup of all values, so we are able to manipulate these items based on the onDelete value later.
        $this->teamProjectsScheduledForDeletion = clone $teamProjectsToDelete;

        foreach ($teamProjectsToDelete as $teamProjectRemoved) {
            $teamProjectRemoved->setProject(null);
        }

        $this->collTeamProjects = null;
        foreach ($teamProjects as $teamProject) {
            $this->addTeamProject($teamProject);
        }

        $this->collTeamProjects = $teamProjects;
        $this->collTeamProjectsPartial = false;

        return $this;
    }

    /**
     * Returns the number of related TeamProject objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related TeamProject objects.
     * @throws PropelException
     */
    public function countTeamProjects(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collTeamProjectsPartial && !$this->isNew();
        if (null === $this->collTeamProjects || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collTeamProjects) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getTeamProjects());
            }

            $query = ChildTeamProjectQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByProject($this)
                ->count($con);
        }

        return count($this->collTeamProjects);
    }

    /**
     * Method called to associate a ChildTeamProject object to this object
     * through the ChildTeamProject foreign key attribute.
     *
     * @param  ChildTeamProject $l ChildTeamProject
     * @return $this|\Project The current object (for fluent API support)
     */
    public function addTeamProject(ChildTeamProject $l)
    {
        if ($this->collTeamProjects === null) {
            $this->initTeamProjects();
            $this->collTeamProjectsPartial = true;
        }

        if (!$this->collTeamProjects->contains($l)) {
            $this->doAddTeamProject($l);

            if ($this->teamProjectsScheduledForDeletion and $this->teamProjectsScheduledForDeletion->contains($l)) {
                $this->teamProjectsScheduledForDeletion->remove($this->teamProjectsScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param ChildTeamProject $teamProject The ChildTeamProject object to add.
     */
    protected function doAddTeamProject(ChildTeamProject $teamProject)
    {
        $this->collTeamProjects[]= $teamProject;
        $teamProject->setProject($this);
    }

    /**
     * @param  ChildTeamProject $teamProject The ChildTeamProject object to remove.
     * @return $this|ChildProject The current object (for fluent API support)
     */
    public function removeTeamProject(ChildTeamProject $teamProject)
    {
        if ($this->getTeamProjects()->contains($teamProject)) {
            $pos = $this->collTeamProjects->search($teamProject);
            $this->collTeamProjects->remove($pos);
            if (null === $this->teamProjectsScheduledForDeletion) {
                $this->teamProjectsScheduledForDeletion = clone $this->collTeamProjects;
                $this->teamProjectsScheduledForDeletion->clear();
            }
            $this->teamProjectsScheduledForDeletion[]= clone $teamProject;
            $teamProject->setProject(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Project is new, it will return
     * an empty collection; or if this Project has previously
     * been saved, it will retrieve related TeamProjects from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Project.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return ObjectCollection|ChildTeamProject[] List of ChildTeamProject objects
     */
    public function getTeamProjectsJoinTeam(Criteria $criteria = null, ConnectionInterface $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ChildTeamProjectQuery::create(null, $criteria);
        $query->joinWith('Team', $joinBehavior);

        return $this->getTeamProjects($query, $con);
    }

    /**
     * Gets a single ChildProjectInfo object, which is related to this object by a one-to-one relationship.
     *
     * @param  ConnectionInterface $con optional connection object
     * @return ChildProjectInfo
     * @throws PropelException
     */
    public function getProjectInfo(ConnectionInterface $con = null)
    {

        if ($this->singleProjectInfo === null && !$this->isNew()) {
            $this->singleProjectInfo = ChildProjectInfoQuery::create()->findPk($this->getPrimaryKey(), $con);
        }

        return $this->singleProjectInfo;
    }

    /**
     * Sets a single ChildProjectInfo object as related to this object by a one-to-one relationship.
     *
     * @param  ChildProjectInfo $v ChildProjectInfo
     * @return $this|\Project The current object (for fluent API support)
     * @throws PropelException
     */
    public function setProjectInfo(ChildProjectInfo $v = null)
    {
        $this->singleProjectInfo = $v;

        // Make sure that that the passed-in ChildProjectInfo isn't already associated with this object
        if ($v !== null && $v->getProject(null, false) === null) {
            $v->setProject($this);
        }

        return $this;
    }

    /**
     * Clears the current object, sets all attributes to their default values and removes
     * outgoing references as well as back-references (from other objects to this one. Results probably in a database
     * change of those foreign objects when you call `save` there).
     */
    public function clear()
    {
        if (null !== $this->aWorkStatus) {
            $this->aWorkStatus->removeProject($this);
        }
        $this->id = null;
        $this->name = null;
        $this->start = null;
        $this->end = null;
        $this->status_id = null;
        $this->alreadyInSave = false;
        $this->clearAllReferences();
        $this->resetModified();
        $this->setNew(true);
        $this->setDeleted(false);
    }

    /**
     * Resets all references and back-references to other model objects or collections of model objects.
     *
     * This method is used to reset all php object references (not the actual reference in the database).
     * Necessary for object serialisation.
     *
     * @param      boolean $deep Whether to also clear the references on all referrer objects.
     */
    public function clearAllReferences($deep = false)
    {
        if ($deep) {
            if ($this->collCalendars) {
                foreach ($this->collCalendars as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collTeamProjects) {
                foreach ($this->collTeamProjects as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->singleProjectInfo) {
                $this->singleProjectInfo->clearAllReferences($deep);
            }
        } // if ($deep)

        $this->collCalendars = null;
        $this->collTeamProjects = null;
        $this->singleProjectInfo = null;
        $this->aWorkStatus = null;
    }

    /**
     * Return the string representation of this object
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->exportTo(ProjectTableMap::DEFAULT_STRING_FORMAT);
    }

    /**
     * Code to be run before persisting the object
     * @param  ConnectionInterface $con
     * @return boolean
     */
    public function preSave(ConnectionInterface $con = null)
    {
        if (is_callable('parent::preSave')) {
            return parent::preSave($con);
        }
        return true;
    }

    /**
     * Code to be run after persisting the object
     * @param ConnectionInterface $con
     */
    public function postSave(ConnectionInterface $con = null)
    {
        if (is_callable('parent::postSave')) {
            parent::postSave($con);
        }
    }

    /**
     * Code to be run before inserting to database
     * @param  ConnectionInterface $con
     * @return boolean
     */
    public function preInsert(ConnectionInterface $con = null)
    {
        if (is_callable('parent::preInsert')) {
            return parent::preInsert($con);
        }
        return true;
    }

    /**
     * Code to be run after inserting to database
     * @param ConnectionInterface $con
     */
    public function postInsert(ConnectionInterface $con = null)
    {
        if (is_callable('parent::postInsert')) {
            parent::postInsert($con);
        }
    }

    /**
     * Code to be run before updating the object in database
     * @param  ConnectionInterface $con
     * @return boolean
     */
    public function preUpdate(ConnectionInterface $con = null)
    {
        if (is_callable('parent::preUpdate')) {
            return parent::preUpdate($con);
        }
        return true;
    }

    /**
     * Code to be run after updating the object in database
     * @param ConnectionInterface $con
     */
    public function postUpdate(ConnectionInterface $con = null)
    {
        if (is_callable('parent::postUpdate')) {
            parent::postUpdate($con);
        }
    }

    /**
     * Code to be run before deleting the object in database
     * @param  ConnectionInterface $con
     * @return boolean
     */
    public function preDelete(ConnectionInterface $con = null)
    {
        if (is_callable('parent::preDelete')) {
            return parent::preDelete($con);
        }
        return true;
    }

    /**
     * Code to be run after deleting the object in database
     * @param ConnectionInterface $con
     */
    public function postDelete(ConnectionInterface $con = null)
    {
        if (is_callable('parent::postDelete')) {
            parent::postDelete($con);
        }
    }


    /**
     * Derived method to catches calls to undefined methods.
     *
     * Provides magic import/export method support (fromXML()/toXML(), fromYAML()/toYAML(), etc.).
     * Allows to define default __call() behavior if you overwrite __call()
     *
     * @param string $name
     * @param mixed  $params
     *
     * @return array|string
     */
    public function __call($name, $params)
    {
        if (0 === strpos($name, 'get')) {
            $virtualColumn = substr($name, 3);
            if ($this->hasVirtualColumn($virtualColumn)) {
                return $this->getVirtualColumn($virtualColumn);
            }

            $virtualColumn = lcfirst($virtualColumn);
            if ($this->hasVirtualColumn($virtualColumn)) {
                return $this->getVirtualColumn($virtualColumn);
            }
        }

        if (0 === strpos($name, 'from')) {
            $format = substr($name, 4);

            return $this->importFrom($format, reset($params));
        }

        if (0 === strpos($name, 'to')) {
            $format = substr($name, 2);
            $includeLazyLoadColumns = isset($params[0]) ? $params[0] : true;

            return $this->exportTo($format, $includeLazyLoadColumns);
        }

        throw new BadMethodCallException(sprintf('Call to undefined method: %s.', $name));
    }

}
