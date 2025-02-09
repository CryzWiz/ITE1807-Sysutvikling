<?php

namespace Base;

use \Task as ChildTask;
use \TaskQuery as ChildTaskQuery;
use \Team as ChildTeam;
use \TeamQuery as ChildTeamQuery;
use \TimeRegistrationQuery as ChildTimeRegistrationQuery;
use \User as ChildUser;
use \UserQuery as ChildUserQuery;
use \DateTime;
use \Exception;
use \PDO;
use Map\TimeRegistrationTableMap;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveRecord\ActiveRecordInterface;
use Propel\Runtime\Collection\Collection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\BadMethodCallException;
use Propel\Runtime\Exception\LogicException;
use Propel\Runtime\Exception\PropelException;
use Propel\Runtime\Map\TableMap;
use Propel\Runtime\Parser\AbstractParser;
use Propel\Runtime\Util\PropelDateTime;

/**
 * Base class that represents a row from the 'timeregistration' table.
 *
 * 
 *
 * @package    propel.generator..Base
 */
abstract class TimeRegistration implements ActiveRecordInterface 
{
    /**
     * TableMap class name
     */
    const TABLE_MAP = '\\Map\\TimeRegistrationTableMap';


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
     * The value for the user_id field.
     * 
     * @var        int
     */
    protected $user_id;

    /**
     * The value for the team_id field.
     * 
     * @var        int
     */
    protected $team_id;

    /**
     * The value for the task_id field.
     * 
     * @var        int
     */
    protected $task_id;

    /**
     * The value for the start field.
     * 
     * @var        DateTime
     */
    protected $start;

    /**
     * The value for the stop field.
     * 
     * @var        DateTime
     */
    protected $stop;

    /**
     * The value for the place field.
     * 
     * @var        string
     */
    protected $place;

    /**
     * The value for the predefinedtask field.
     * 
     * @var        string
     */
    protected $predefinedtask;

    /**
     * The value for the comment field.
     * 
     * @var        string
     */
    protected $comment;

    /**
     * The value for the approved field.
     * 
     * Note: this column has a database default value of: false
     * @var        boolean
     */
    protected $approved;

    /**
     * The value for the active field.
     * 
     * Note: this column has a database default value of: 'false'
     * @var        string
     */
    protected $active;

    /**
     * @var        ChildUser
     */
    protected $aUser;

    /**
     * @var        ChildTask
     */
    protected $aTask;

    /**
     * @var        ChildTeam
     */
    protected $aTeam;

    /**
     * Flag to prevent endless save loop, if this object is referenced
     * by another object which falls in this transaction.
     *
     * @var boolean
     */
    protected $alreadyInSave = false;

    /**
     * Applies default values to this object.
     * This method should be called from the object's constructor (or
     * equivalent initialization method).
     * @see __construct()
     */
    public function applyDefaultValues()
    {
        $this->approved = false;
        $this->active = 'false';
    }

    /**
     * Initializes internal state of Base\TimeRegistration object.
     * @see applyDefaults()
     */
    public function __construct()
    {
        $this->applyDefaultValues();
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
     * Compares this with another <code>TimeRegistration</code> instance.  If
     * <code>obj</code> is an instance of <code>TimeRegistration</code>, delegates to
     * <code>equals(TimeRegistration)</code>.  Otherwise, returns <code>false</code>.
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
     * @return $this|TimeRegistration The current object, for fluid interface
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
     * Get the [user_id] column value.
     * 
     * @return int
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * Get the [team_id] column value.
     * 
     * @return int
     */
    public function getTeamId()
    {
        return $this->team_id;
    }

    /**
     * Get the [task_id] column value.
     * 
     * @return int
     */
    public function getTaskId()
    {
        return $this->task_id;
    }

    /**
     * Get the [optionally formatted] temporal [start] column value.
     * 
     *
     * @param      string $format The date/time format string (either date()-style or strftime()-style).
     *                            If format is NULL, then the raw DateTime object will be returned.
     *
     * @return string|DateTime Formatted date/time value as string or DateTime object (if format is NULL), NULL if column is NULL, and 0 if column value is 0000-00-00 00:00:00
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
     * Get the [optionally formatted] temporal [stop] column value.
     * 
     *
     * @param      string $format The date/time format string (either date()-style or strftime()-style).
     *                            If format is NULL, then the raw DateTime object will be returned.
     *
     * @return string|DateTime Formatted date/time value as string or DateTime object (if format is NULL), NULL if column is NULL, and 0 if column value is 0000-00-00 00:00:00
     *
     * @throws PropelException - if unable to parse/validate the date/time value.
     */
    public function getStop($format = NULL)
    {
        if ($format === null) {
            return $this->stop;
        } else {
            return $this->stop instanceof \DateTimeInterface ? $this->stop->format($format) : null;
        }
    }

    /**
     * Get the [place] column value.
     * 
     * @return string
     */
    public function getPlace()
    {
        return $this->place;
    }

    /**
     * Get the [predefinedtask] column value.
     * 
     * @return string
     */
    public function getPredefinedtask()
    {
        return $this->predefinedtask;
    }

    /**
     * Get the [comment] column value.
     * 
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Get the [approved] column value.
     * 
     * @return boolean
     */
    public function getApproved()
    {
        return $this->approved;
    }

    /**
     * Get the [approved] column value.
     * 
     * @return boolean
     */
    public function isApproved()
    {
        return $this->getApproved();
    }

    /**
     * Get the [active] column value.
     * 
     * @return string
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Set the value of [id] column.
     * 
     * @param int $v new value
     * @return $this|\TimeRegistration The current object (for fluent API support)
     */
    public function setId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->id !== $v) {
            $this->id = $v;
            $this->modifiedColumns[TimeRegistrationTableMap::COL_ID] = true;
        }

        return $this;
    } // setId()

    /**
     * Set the value of [user_id] column.
     * 
     * @param int $v new value
     * @return $this|\TimeRegistration The current object (for fluent API support)
     */
    public function setUserId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->user_id !== $v) {
            $this->user_id = $v;
            $this->modifiedColumns[TimeRegistrationTableMap::COL_USER_ID] = true;
        }

        if ($this->aUser !== null && $this->aUser->getId() !== $v) {
            $this->aUser = null;
        }

        return $this;
    } // setUserId()

    /**
     * Set the value of [team_id] column.
     * 
     * @param int $v new value
     * @return $this|\TimeRegistration The current object (for fluent API support)
     */
    public function setTeamId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->team_id !== $v) {
            $this->team_id = $v;
            $this->modifiedColumns[TimeRegistrationTableMap::COL_TEAM_ID] = true;
        }

        if ($this->aTeam !== null && $this->aTeam->getId() !== $v) {
            $this->aTeam = null;
        }

        return $this;
    } // setTeamId()

    /**
     * Set the value of [task_id] column.
     * 
     * @param int $v new value
     * @return $this|\TimeRegistration The current object (for fluent API support)
     */
    public function setTaskId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->task_id !== $v) {
            $this->task_id = $v;
            $this->modifiedColumns[TimeRegistrationTableMap::COL_TASK_ID] = true;
        }

        if ($this->aTask !== null && $this->aTask->getId() !== $v) {
            $this->aTask = null;
        }

        return $this;
    } // setTaskId()

    /**
     * Sets the value of [start] column to a normalized version of the date/time value specified.
     * 
     * @param  mixed $v string, integer (timestamp), or \DateTimeInterface value.
     *               Empty strings are treated as NULL.
     * @return $this|\TimeRegistration The current object (for fluent API support)
     */
    public function setStart($v)
    {
        $dt = PropelDateTime::newInstance($v, null, 'DateTime');
        if ($this->start !== null || $dt !== null) {
            if ($this->start === null || $dt === null || $dt->format("Y-m-d H:i:s.u") !== $this->start->format("Y-m-d H:i:s.u")) {
                $this->start = $dt === null ? null : clone $dt;
                $this->modifiedColumns[TimeRegistrationTableMap::COL_START] = true;
            }
        } // if either are not null

        return $this;
    } // setStart()

    /**
     * Sets the value of [stop] column to a normalized version of the date/time value specified.
     * 
     * @param  mixed $v string, integer (timestamp), or \DateTimeInterface value.
     *               Empty strings are treated as NULL.
     * @return $this|\TimeRegistration The current object (for fluent API support)
     */
    public function setStop($v)
    {
        $dt = PropelDateTime::newInstance($v, null, 'DateTime');
        if ($this->stop !== null || $dt !== null) {
            if ($this->stop === null || $dt === null || $dt->format("Y-m-d H:i:s.u") !== $this->stop->format("Y-m-d H:i:s.u")) {
                $this->stop = $dt === null ? null : clone $dt;
                $this->modifiedColumns[TimeRegistrationTableMap::COL_STOP] = true;
            }
        } // if either are not null

        return $this;
    } // setStop()

    /**
     * Set the value of [place] column.
     * 
     * @param string $v new value
     * @return $this|\TimeRegistration The current object (for fluent API support)
     */
    public function setPlace($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->place !== $v) {
            $this->place = $v;
            $this->modifiedColumns[TimeRegistrationTableMap::COL_PLACE] = true;
        }

        return $this;
    } // setPlace()

    /**
     * Set the value of [predefinedtask] column.
     * 
     * @param string $v new value
     * @return $this|\TimeRegistration The current object (for fluent API support)
     */
    public function setPredefinedtask($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->predefinedtask !== $v) {
            $this->predefinedtask = $v;
            $this->modifiedColumns[TimeRegistrationTableMap::COL_PREDEFINEDTASK] = true;
        }

        return $this;
    } // setPredefinedtask()

    /**
     * Set the value of [comment] column.
     * 
     * @param string $v new value
     * @return $this|\TimeRegistration The current object (for fluent API support)
     */
    public function setComment($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->comment !== $v) {
            $this->comment = $v;
            $this->modifiedColumns[TimeRegistrationTableMap::COL_COMMENT] = true;
        }

        return $this;
    } // setComment()

    /**
     * Sets the value of the [approved] column.
     * Non-boolean arguments are converted using the following rules:
     *   * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *   * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     * Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     * 
     * @param  boolean|integer|string $v The new value
     * @return $this|\TimeRegistration The current object (for fluent API support)
     */
    public function setApproved($v)
    {
        if ($v !== null) {
            if (is_string($v)) {
                $v = in_array(strtolower($v), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
            } else {
                $v = (boolean) $v;
            }
        }

        if ($this->approved !== $v) {
            $this->approved = $v;
            $this->modifiedColumns[TimeRegistrationTableMap::COL_APPROVED] = true;
        }

        return $this;
    } // setApproved()

    /**
     * Set the value of [active] column.
     * 
     * @param string $v new value
     * @return $this|\TimeRegistration The current object (for fluent API support)
     */
    public function setActive($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->active !== $v) {
            $this->active = $v;
            $this->modifiedColumns[TimeRegistrationTableMap::COL_ACTIVE] = true;
        }

        return $this;
    } // setActive()

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
            if ($this->approved !== false) {
                return false;
            }

            if ($this->active !== 'false') {
                return false;
            }

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

            $col = $row[TableMap::TYPE_NUM == $indexType ? 0 + $startcol : TimeRegistrationTableMap::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)];
            $this->id = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 1 + $startcol : TimeRegistrationTableMap::translateFieldName('UserId', TableMap::TYPE_PHPNAME, $indexType)];
            $this->user_id = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 2 + $startcol : TimeRegistrationTableMap::translateFieldName('TeamId', TableMap::TYPE_PHPNAME, $indexType)];
            $this->team_id = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 3 + $startcol : TimeRegistrationTableMap::translateFieldName('TaskId', TableMap::TYPE_PHPNAME, $indexType)];
            $this->task_id = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 4 + $startcol : TimeRegistrationTableMap::translateFieldName('Start', TableMap::TYPE_PHPNAME, $indexType)];
            if ($col === '0000-00-00 00:00:00') {
                $col = null;
            }
            $this->start = (null !== $col) ? PropelDateTime::newInstance($col, null, 'DateTime') : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 5 + $startcol : TimeRegistrationTableMap::translateFieldName('Stop', TableMap::TYPE_PHPNAME, $indexType)];
            if ($col === '0000-00-00 00:00:00') {
                $col = null;
            }
            $this->stop = (null !== $col) ? PropelDateTime::newInstance($col, null, 'DateTime') : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 6 + $startcol : TimeRegistrationTableMap::translateFieldName('Place', TableMap::TYPE_PHPNAME, $indexType)];
            $this->place = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 7 + $startcol : TimeRegistrationTableMap::translateFieldName('Predefinedtask', TableMap::TYPE_PHPNAME, $indexType)];
            $this->predefinedtask = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 8 + $startcol : TimeRegistrationTableMap::translateFieldName('Comment', TableMap::TYPE_PHPNAME, $indexType)];
            $this->comment = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 9 + $startcol : TimeRegistrationTableMap::translateFieldName('Approved', TableMap::TYPE_PHPNAME, $indexType)];
            $this->approved = (null !== $col) ? (boolean) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 10 + $startcol : TimeRegistrationTableMap::translateFieldName('Active', TableMap::TYPE_PHPNAME, $indexType)];
            $this->active = (null !== $col) ? (string) $col : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }

            return $startcol + 11; // 11 = TimeRegistrationTableMap::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException(sprintf('Error populating %s object', '\\TimeRegistration'), 0, $e);
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
        if ($this->aUser !== null && $this->user_id !== $this->aUser->getId()) {
            $this->aUser = null;
        }
        if ($this->aTeam !== null && $this->team_id !== $this->aTeam->getId()) {
            $this->aTeam = null;
        }
        if ($this->aTask !== null && $this->task_id !== $this->aTask->getId()) {
            $this->aTask = null;
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
            $con = Propel::getServiceContainer()->getReadConnection(TimeRegistrationTableMap::DATABASE_NAME);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $dataFetcher = ChildTimeRegistrationQuery::create(null, $this->buildPkeyCriteria())->setFormatter(ModelCriteria::FORMAT_STATEMENT)->find($con);
        $row = $dataFetcher->fetch();
        $dataFetcher->close();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true, $dataFetcher->getIndexType()); // rehydrate

        if ($deep) {  // also de-associate any related objects?

            $this->aUser = null;
            $this->aTask = null;
            $this->aTeam = null;
        } // if (deep)
    }

    /**
     * Removes this object from datastore and sets delete attribute.
     *
     * @param      ConnectionInterface $con
     * @return void
     * @throws PropelException
     * @see TimeRegistration::setDeleted()
     * @see TimeRegistration::isDeleted()
     */
    public function delete(ConnectionInterface $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("This object has already been deleted.");
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getWriteConnection(TimeRegistrationTableMap::DATABASE_NAME);
        }

        $con->transaction(function () use ($con) {
            $deleteQuery = ChildTimeRegistrationQuery::create()
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
            $con = Propel::getServiceContainer()->getWriteConnection(TimeRegistrationTableMap::DATABASE_NAME);
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
                TimeRegistrationTableMap::addInstanceToPool($this);
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

            if ($this->aUser !== null) {
                if ($this->aUser->isModified() || $this->aUser->isNew()) {
                    $affectedRows += $this->aUser->save($con);
                }
                $this->setUser($this->aUser);
            }

            if ($this->aTask !== null) {
                if ($this->aTask->isModified() || $this->aTask->isNew()) {
                    $affectedRows += $this->aTask->save($con);
                }
                $this->setTask($this->aTask);
            }

            if ($this->aTeam !== null) {
                if ($this->aTeam->isModified() || $this->aTeam->isNew()) {
                    $affectedRows += $this->aTeam->save($con);
                }
                $this->setTeam($this->aTeam);
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

        $this->modifiedColumns[TimeRegistrationTableMap::COL_ID] = true;
        if (null !== $this->id) {
            throw new PropelException('Cannot insert a value for auto-increment primary key (' . TimeRegistrationTableMap::COL_ID . ')');
        }

         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(TimeRegistrationTableMap::COL_ID)) {
            $modifiedColumns[':p' . $index++]  = 'id';
        }
        if ($this->isColumnModified(TimeRegistrationTableMap::COL_USER_ID)) {
            $modifiedColumns[':p' . $index++]  = 'User_id';
        }
        if ($this->isColumnModified(TimeRegistrationTableMap::COL_TEAM_ID)) {
            $modifiedColumns[':p' . $index++]  = 'Team_id';
        }
        if ($this->isColumnModified(TimeRegistrationTableMap::COL_TASK_ID)) {
            $modifiedColumns[':p' . $index++]  = 'Task_id';
        }
        if ($this->isColumnModified(TimeRegistrationTableMap::COL_START)) {
            $modifiedColumns[':p' . $index++]  = 'Start';
        }
        if ($this->isColumnModified(TimeRegistrationTableMap::COL_STOP)) {
            $modifiedColumns[':p' . $index++]  = 'Stop';
        }
        if ($this->isColumnModified(TimeRegistrationTableMap::COL_PLACE)) {
            $modifiedColumns[':p' . $index++]  = 'Place';
        }
        if ($this->isColumnModified(TimeRegistrationTableMap::COL_PREDEFINEDTASK)) {
            $modifiedColumns[':p' . $index++]  = 'PredefinedTask';
        }
        if ($this->isColumnModified(TimeRegistrationTableMap::COL_COMMENT)) {
            $modifiedColumns[':p' . $index++]  = 'Comment';
        }
        if ($this->isColumnModified(TimeRegistrationTableMap::COL_APPROVED)) {
            $modifiedColumns[':p' . $index++]  = 'Approved';
        }
        if ($this->isColumnModified(TimeRegistrationTableMap::COL_ACTIVE)) {
            $modifiedColumns[':p' . $index++]  = 'Active';
        }

        $sql = sprintf(
            'INSERT INTO timeregistration (%s) VALUES (%s)',
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
                    case 'User_id':                        
                        $stmt->bindValue($identifier, $this->user_id, PDO::PARAM_INT);
                        break;
                    case 'Team_id':                        
                        $stmt->bindValue($identifier, $this->team_id, PDO::PARAM_INT);
                        break;
                    case 'Task_id':                        
                        $stmt->bindValue($identifier, $this->task_id, PDO::PARAM_INT);
                        break;
                    case 'Start':                        
                        $stmt->bindValue($identifier, $this->start ? $this->start->format("Y-m-d H:i:s.u") : null, PDO::PARAM_STR);
                        break;
                    case 'Stop':                        
                        $stmt->bindValue($identifier, $this->stop ? $this->stop->format("Y-m-d H:i:s.u") : null, PDO::PARAM_STR);
                        break;
                    case 'Place':                        
                        $stmt->bindValue($identifier, $this->place, PDO::PARAM_STR);
                        break;
                    case 'PredefinedTask':                        
                        $stmt->bindValue($identifier, $this->predefinedtask, PDO::PARAM_STR);
                        break;
                    case 'Comment':                        
                        $stmt->bindValue($identifier, $this->comment, PDO::PARAM_STR);
                        break;
                    case 'Approved':
                        $stmt->bindValue($identifier, (int) $this->approved, PDO::PARAM_INT);
                        break;
                    case 'Active':                        
                        $stmt->bindValue($identifier, $this->active, PDO::PARAM_STR);
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
        $pos = TimeRegistrationTableMap::translateFieldName($name, $type, TableMap::TYPE_NUM);
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
                return $this->getUserId();
                break;
            case 2:
                return $this->getTeamId();
                break;
            case 3:
                return $this->getTaskId();
                break;
            case 4:
                return $this->getStart();
                break;
            case 5:
                return $this->getStop();
                break;
            case 6:
                return $this->getPlace();
                break;
            case 7:
                return $this->getPredefinedtask();
                break;
            case 8:
                return $this->getComment();
                break;
            case 9:
                return $this->getApproved();
                break;
            case 10:
                return $this->getActive();
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

        if (isset($alreadyDumpedObjects['TimeRegistration'][$this->hashCode()])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['TimeRegistration'][$this->hashCode()] = true;
        $keys = TimeRegistrationTableMap::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getId(),
            $keys[1] => $this->getUserId(),
            $keys[2] => $this->getTeamId(),
            $keys[3] => $this->getTaskId(),
            $keys[4] => $this->getStart(),
            $keys[5] => $this->getStop(),
            $keys[6] => $this->getPlace(),
            $keys[7] => $this->getPredefinedtask(),
            $keys[8] => $this->getComment(),
            $keys[9] => $this->getApproved(),
            $keys[10] => $this->getActive(),
        );
        if ($result[$keys[4]] instanceof \DateTime) {
            $result[$keys[4]] = $result[$keys[4]]->format('c');
        }
        
        if ($result[$keys[5]] instanceof \DateTime) {
            $result[$keys[5]] = $result[$keys[5]]->format('c');
        }
        
        $virtualColumns = $this->virtualColumns;
        foreach ($virtualColumns as $key => $virtualColumn) {
            $result[$key] = $virtualColumn;
        }
        
        if ($includeForeignObjects) {
            if (null !== $this->aUser) {
                
                switch ($keyType) {
                    case TableMap::TYPE_CAMELNAME:
                        $key = 'user';
                        break;
                    case TableMap::TYPE_FIELDNAME:
                        $key = 'user';
                        break;
                    default:
                        $key = 'User';
                }
        
                $result[$key] = $this->aUser->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
            if (null !== $this->aTask) {
                
                switch ($keyType) {
                    case TableMap::TYPE_CAMELNAME:
                        $key = 'task';
                        break;
                    case TableMap::TYPE_FIELDNAME:
                        $key = 'task';
                        break;
                    default:
                        $key = 'Task';
                }
        
                $result[$key] = $this->aTask->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
            if (null !== $this->aTeam) {
                
                switch ($keyType) {
                    case TableMap::TYPE_CAMELNAME:
                        $key = 'team';
                        break;
                    case TableMap::TYPE_FIELDNAME:
                        $key = 'team';
                        break;
                    default:
                        $key = 'Team';
                }
        
                $result[$key] = $this->aTeam->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
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
     * @return $this|\TimeRegistration
     */
    public function setByName($name, $value, $type = TableMap::TYPE_PHPNAME)
    {
        $pos = TimeRegistrationTableMap::translateFieldName($name, $type, TableMap::TYPE_NUM);

        return $this->setByPosition($pos, $value);
    }

    /**
     * Sets a field from the object by Position as specified in the xml schema.
     * Zero-based.
     *
     * @param  int $pos position in xml schema
     * @param  mixed $value field value
     * @return $this|\TimeRegistration
     */
    public function setByPosition($pos, $value)
    {
        switch ($pos) {
            case 0:
                $this->setId($value);
                break;
            case 1:
                $this->setUserId($value);
                break;
            case 2:
                $this->setTeamId($value);
                break;
            case 3:
                $this->setTaskId($value);
                break;
            case 4:
                $this->setStart($value);
                break;
            case 5:
                $this->setStop($value);
                break;
            case 6:
                $this->setPlace($value);
                break;
            case 7:
                $this->setPredefinedtask($value);
                break;
            case 8:
                $this->setComment($value);
                break;
            case 9:
                $this->setApproved($value);
                break;
            case 10:
                $this->setActive($value);
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
        $keys = TimeRegistrationTableMap::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) {
            $this->setId($arr[$keys[0]]);
        }
        if (array_key_exists($keys[1], $arr)) {
            $this->setUserId($arr[$keys[1]]);
        }
        if (array_key_exists($keys[2], $arr)) {
            $this->setTeamId($arr[$keys[2]]);
        }
        if (array_key_exists($keys[3], $arr)) {
            $this->setTaskId($arr[$keys[3]]);
        }
        if (array_key_exists($keys[4], $arr)) {
            $this->setStart($arr[$keys[4]]);
        }
        if (array_key_exists($keys[5], $arr)) {
            $this->setStop($arr[$keys[5]]);
        }
        if (array_key_exists($keys[6], $arr)) {
            $this->setPlace($arr[$keys[6]]);
        }
        if (array_key_exists($keys[7], $arr)) {
            $this->setPredefinedtask($arr[$keys[7]]);
        }
        if (array_key_exists($keys[8], $arr)) {
            $this->setComment($arr[$keys[8]]);
        }
        if (array_key_exists($keys[9], $arr)) {
            $this->setApproved($arr[$keys[9]]);
        }
        if (array_key_exists($keys[10], $arr)) {
            $this->setActive($arr[$keys[10]]);
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
     * @return $this|\TimeRegistration The current object, for fluid interface
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
        $criteria = new Criteria(TimeRegistrationTableMap::DATABASE_NAME);

        if ($this->isColumnModified(TimeRegistrationTableMap::COL_ID)) {
            $criteria->add(TimeRegistrationTableMap::COL_ID, $this->id);
        }
        if ($this->isColumnModified(TimeRegistrationTableMap::COL_USER_ID)) {
            $criteria->add(TimeRegistrationTableMap::COL_USER_ID, $this->user_id);
        }
        if ($this->isColumnModified(TimeRegistrationTableMap::COL_TEAM_ID)) {
            $criteria->add(TimeRegistrationTableMap::COL_TEAM_ID, $this->team_id);
        }
        if ($this->isColumnModified(TimeRegistrationTableMap::COL_TASK_ID)) {
            $criteria->add(TimeRegistrationTableMap::COL_TASK_ID, $this->task_id);
        }
        if ($this->isColumnModified(TimeRegistrationTableMap::COL_START)) {
            $criteria->add(TimeRegistrationTableMap::COL_START, $this->start);
        }
        if ($this->isColumnModified(TimeRegistrationTableMap::COL_STOP)) {
            $criteria->add(TimeRegistrationTableMap::COL_STOP, $this->stop);
        }
        if ($this->isColumnModified(TimeRegistrationTableMap::COL_PLACE)) {
            $criteria->add(TimeRegistrationTableMap::COL_PLACE, $this->place);
        }
        if ($this->isColumnModified(TimeRegistrationTableMap::COL_PREDEFINEDTASK)) {
            $criteria->add(TimeRegistrationTableMap::COL_PREDEFINEDTASK, $this->predefinedtask);
        }
        if ($this->isColumnModified(TimeRegistrationTableMap::COL_COMMENT)) {
            $criteria->add(TimeRegistrationTableMap::COL_COMMENT, $this->comment);
        }
        if ($this->isColumnModified(TimeRegistrationTableMap::COL_APPROVED)) {
            $criteria->add(TimeRegistrationTableMap::COL_APPROVED, $this->approved);
        }
        if ($this->isColumnModified(TimeRegistrationTableMap::COL_ACTIVE)) {
            $criteria->add(TimeRegistrationTableMap::COL_ACTIVE, $this->active);
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
        $criteria = ChildTimeRegistrationQuery::create();
        $criteria->add(TimeRegistrationTableMap::COL_ID, $this->id);

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
     * @param      object $copyObj An object of \TimeRegistration (or compatible) type.
     * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param      boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setUserId($this->getUserId());
        $copyObj->setTeamId($this->getTeamId());
        $copyObj->setTaskId($this->getTaskId());
        $copyObj->setStart($this->getStart());
        $copyObj->setStop($this->getStop());
        $copyObj->setPlace($this->getPlace());
        $copyObj->setPredefinedtask($this->getPredefinedtask());
        $copyObj->setComment($this->getComment());
        $copyObj->setApproved($this->getApproved());
        $copyObj->setActive($this->getActive());
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
     * @return \TimeRegistration Clone of current object.
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
     * Declares an association between this object and a ChildUser object.
     *
     * @param  ChildUser $v
     * @return $this|\TimeRegistration The current object (for fluent API support)
     * @throws PropelException
     */
    public function setUser(ChildUser $v = null)
    {
        if ($v === null) {
            $this->setUserId(NULL);
        } else {
            $this->setUserId($v->getId());
        }

        $this->aUser = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the ChildUser object, it will not be re-added.
        if ($v !== null) {
            $v->addTimeRegistration($this);
        }


        return $this;
    }


    /**
     * Get the associated ChildUser object
     *
     * @param  ConnectionInterface $con Optional Connection object.
     * @return ChildUser The associated ChildUser object.
     * @throws PropelException
     */
    public function getUser(ConnectionInterface $con = null)
    {
        if ($this->aUser === null && ($this->user_id !== null)) {
            $this->aUser = ChildUserQuery::create()->findPk($this->user_id, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aUser->addTimeRegistrations($this);
             */
        }

        return $this->aUser;
    }

    /**
     * Declares an association between this object and a ChildTask object.
     *
     * @param  ChildTask $v
     * @return $this|\TimeRegistration The current object (for fluent API support)
     * @throws PropelException
     */
    public function setTask(ChildTask $v = null)
    {
        if ($v === null) {
            $this->setTaskId(NULL);
        } else {
            $this->setTaskId($v->getId());
        }

        $this->aTask = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the ChildTask object, it will not be re-added.
        if ($v !== null) {
            $v->addTimeRegistration($this);
        }


        return $this;
    }


    /**
     * Get the associated ChildTask object
     *
     * @param  ConnectionInterface $con Optional Connection object.
     * @return ChildTask The associated ChildTask object.
     * @throws PropelException
     */
    public function getTask(ConnectionInterface $con = null)
    {
        if ($this->aTask === null && ($this->task_id !== null)) {
            $this->aTask = ChildTaskQuery::create()->findPk($this->task_id, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aTask->addTimeRegistrations($this);
             */
        }

        return $this->aTask;
    }

    /**
     * Declares an association between this object and a ChildTeam object.
     *
     * @param  ChildTeam $v
     * @return $this|\TimeRegistration The current object (for fluent API support)
     * @throws PropelException
     */
    public function setTeam(ChildTeam $v = null)
    {
        if ($v === null) {
            $this->setTeamId(NULL);
        } else {
            $this->setTeamId($v->getId());
        }

        $this->aTeam = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the ChildTeam object, it will not be re-added.
        if ($v !== null) {
            $v->addTimeRegistration($this);
        }


        return $this;
    }


    /**
     * Get the associated ChildTeam object
     *
     * @param  ConnectionInterface $con Optional Connection object.
     * @return ChildTeam The associated ChildTeam object.
     * @throws PropelException
     */
    public function getTeam(ConnectionInterface $con = null)
    {
        if ($this->aTeam === null && ($this->team_id !== null)) {
            $this->aTeam = ChildTeamQuery::create()->findPk($this->team_id, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aTeam->addTimeRegistrations($this);
             */
        }

        return $this->aTeam;
    }

    /**
     * Clears the current object, sets all attributes to their default values and removes
     * outgoing references as well as back-references (from other objects to this one. Results probably in a database
     * change of those foreign objects when you call `save` there).
     */
    public function clear()
    {
        if (null !== $this->aUser) {
            $this->aUser->removeTimeRegistration($this);
        }
        if (null !== $this->aTask) {
            $this->aTask->removeTimeRegistration($this);
        }
        if (null !== $this->aTeam) {
            $this->aTeam->removeTimeRegistration($this);
        }
        $this->id = null;
        $this->user_id = null;
        $this->team_id = null;
        $this->task_id = null;
        $this->start = null;
        $this->stop = null;
        $this->place = null;
        $this->predefinedtask = null;
        $this->comment = null;
        $this->approved = null;
        $this->active = null;
        $this->alreadyInSave = false;
        $this->clearAllReferences();
        $this->applyDefaultValues();
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
        } // if ($deep)

        $this->aUser = null;
        $this->aTask = null;
        $this->aTeam = null;
    }

    /**
     * Return the string representation of this object
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->exportTo(TimeRegistrationTableMap::DEFAULT_STRING_FORMAT);
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
