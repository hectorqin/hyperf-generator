declare (strict_types=1);

namespace App\Model;

use <?=$mBase?>;
use App\Kernel\Model\Concerns\SoftDeletes;

<?=$modelDoc?>

class <?=$modelName?> extends <?=$mBaseName?>

{
    <?php if ($deleteTime){ ?>
    use SoftDeletes;
    <?php } ?>

    /**
     * 数据表名称
     * @var string
     */
    const TABLE = '<?=$dbNamePrefix?><?=$tableName?>';

    <?php if ($createTime){ ?>
    /**
     * 数据表插入时间字段
     * @var string
     */
    const CREATED_AT = '<?=$createTime?>';
    <?php }?>

    <?php if ($updateTime){ ?>
    /**
     * 数据表更新时间字段
     * @var string
     */
    const UPDATED_AT = '<?=$updateTime?>';
    <?php }?>

    <?php if ($deleteTime){ ?>
    /**
     * 数据表删除时间字段
     * @var string
     */
    const DELETED_AT = '<?=$deleteTime?>';

    /**
     * 软删除默认值
     * @var int
     */
    const DELETED_AT_DEFAULT = 0;
    <?php }?>

    /**
     * 数据表主键 复合主键使用数组定义
     * @var string|array
     */
    const PRIMARY_KEY = '<?=$pk?>';

    /**
     * 数据表名称
     * @var string
     */
    protected $table = '<?=$dbNamePrefix?><?=$tableName?>';

    /**
     * 数据表主键 复合主键使用数组定义
     * @var string|array
     */
    protected $primaryKey = '<?=$pk?>';

    <?php if ($autoTime){ ?>

    /**
     * 是否需要自动写入时间戳
     * @var bool
     */
    public $timestamps = true;

    <?php } else { ?>

    /**
     * 是否自动管理时间戳
     *
     * @var bool
     */
    public $timestamps = false;

    <?php }?>

    /**
     * 搜索字段
     * @var array
     */
    public static $searchFields = [<?=$searchFieldStr?>];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [<?=$fillableFieldStr?>];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        <?=$castFieldStr?>
    ];


    <?php foreach ($varcharField as $field) { ?>

    /**
     * <?=$field['COLUMN_COMMENT']?> 查询器
     *
     * @param \Hyperf\Database\Model\Builder $builder 查询对象
     * @param mixed $value 对应字段值
     * @param mixed $data 数据
     * @return void
     */
    public function search<?=$field['COLUMN_NAME_UPPER']?>Attr($builder, $value, $data)
    {
        if(is_null($value)){
            return;
        }
        $this->searchVarCharAttr($builder, '<?=$field['COLUMN_NAME']?>', $value);
    }

    <?php }?>

    <?php foreach ($enumField as $field) { ?>

    /**
     * <?=$field['COLUMN_COMMENT']?> 查询器
     *
     * @param \Hyperf\Database\Model\Builder $builder 查询对象
     * @param mixed $value 对应字段值
     * @param mixed $data 数据
     * @return void
     */
    public function search<?=$field['COLUMN_NAME_UPPER']?>Attr($builder, $value, $data)
    {
        if(is_null($value)){
            return;
        }
        $this->searchEnumAttr($builder, '<?=$field['COLUMN_NAME']?>', $value);
    }

    <?php }?>

    <?php foreach ($numberField as $field) { ?>

    /**
     * <?=$field['COLUMN_COMMENT']?> 查询器
     *
     * @param \Hyperf\Database\Model\Builder $builder 查询对象
     * @param mixed $value 对应字段值
     * @param mixed $data 数据
     * @return void
     */
    public function search<?=$field['COLUMN_NAME_UPPER']?>Attr($builder, $value, $data)
    {
        if(is_null($value)){
            return;
        }
        $this->searchNumberZoneAttr($builder, '<?=$field['COLUMN_NAME']?>', $value);
    }

    <?php }?>



    <?php if(!$baseModelCorrect){ ?>

    /**
     * 创建时间查询器
     *
     * @param \Hyperf\Database\Model\Builder $builder 查询对象
     * @param mixed $value 对应字段值
     * @param mixed $data 数据
     * @return void
     */
    public function searchCreateTimeAttr($builder, $value, $data)
    {
        if (is_null($value)) {
            return;
        }
        if (!defined('static::CREATED_AT')) {
            return;
        }
        $this->searchTimestampAttr($builder, static::CREATED_AT, $value);
    }

    /**
     * 更新时间查询器
     *
     * @param \Hyperf\Database\Model\Builder $builder 查询对象
     * @param mixed $value 对应字段值
     * @param mixed $data 数据
     * @return void
     */
    public function searchUpdateTimeAttr($builder, $value, $data)
    {
        if (is_null($value)) {
            return;
        }
        if (!defined('static::UPDATED_AT')) {
            return;
        }
        $this->searchTimestampAttr($builder, static::UPDATED_AT, $value);
    }

    /**
     * 查找字符串类型的字段
     * 字符串  ==> 精确匹配  数组且[0]是like，模糊搜索  否则 用 IN 搜索
     *
     * @param \Hyperf\Database\Model\Builder $builder
     * @param string $field
     * @param mixed $value
     * @return void
     */
    public function searchVarCharAttr($builder, $field, $value)
    {
        if (is_string($value)) {
            $builder->where($field, '=', $value);
        } else if (is_array($value)) {
            if ($value[0] === 'like' && isset($value[1])) {
                $builder->where($field, 'like', $value[1]);
            } else {
                $builder->whereIn($field, $value);
            }
        }
    }


    /**
     * 查找时间戳类型的字段
     * 非数组      ===> = 时间戳
     * 1元素数组   ==> >= 时间戳
     * 2元素数组   ==> between 时间戳区间
     *
     * @param \Hyperf\Database\Model\Builder $builder
     * @param string $field
     * @param mixed $value
     * @return void
     */
    public function searchTimestampAttr($builder, $field, $value)
    {
        if (!is_array($value)) {
            if ($value === 0 || $value === '0') {
                $builder->where($field, '=', 0);
            } else {
                if (is_string($value)) {
                    $value = strtotime($value) ?: $value;
                }
                $builder->where($field, '=', $value);
            }
        } else if (count($value) == 1) {
            if (is_string($value[0])) {
                $value[0] = strtotime($value[0]) ?: $value[0];
            }
            $builder->where($field, '>=', $value[0]);
        } else if (count($value) > 1) {
            if (is_string($value[0])) {
                $value[0] = strtotime($value[0]) ?: $value[0];
            }
            if (is_string($value[1])) {
                $value[1] = strtotime($value[1]) ?: $value[1];
            }
            $builder->whereBetween($field, $value);
        }
    }

    /**
     * 查找数字区间类型的字段
     * 非数组      ===> = 数字
     * 1元素数组   ==> >= 数字
     * 2元素数组   ==> between 数字区间
     *
     * @param \Hyperf\Database\Model\Builder $builder
     * @param string $field
     * @param mixed $value
     * @return void
     */
    public function searchNumberZoneAttr($builder, $field, $value)
    {
        if (!is_array($value)) {
            $builder->where($field, '=', $value);
        } else if (count($value) == 1) {
            $builder->where($field, '>=', $value[0]);
        } else if (count($value) > 1) {
            $builder->whereBetween($field, $value);
        }
    }

    /**
     * 查找枚举类型的字段
     * 非数组  ==> = 数字
     * 数组   ==> in 数组
     *
     * @param \Hyperf\Database\Model\Builder $builder
     * @param string $field
     * @param mixed $value
     * @return void
     */
    public function searchEnumAttr($builder, $field, $value)
    {
        if (!is_array($value)) {
            $builder->where($field, '=', (int) $value);
        } else {
            $builder->whereIn($field, $value);
        }
    }

    /**
     * 查找set字段
     * 非数组  ==> FIND_IN_SET(value, field)
     * 数组 第一个元素为逻辑字段 [0] == and  FIND_IN_SET(value[1], field) AND FIND_IN_SET(value[2], field) ..
     *
     * @param \Hyperf\Database\Model\Builder $builder
     * @param string $field
     * @param mixed $value
     * @return void
     */
    public function searchFindInSetAttr($builder, $field, $value)
    {
        $field = "`" . implode('`.`', explode('.', $field)) . "`";
        if (!is_array($value)) {
            $builder->whereRaw("FIND_IN_SET('${value}', ${field})");
        } else {
            $logic = strtolower(array_shift($value)) == 'and' ? 'AND' : 'OR';
            $builder->where(function ($builder) use ($field, $value, $logic) {
                foreach ($value as $key => $v) {
                    $builder->whereRaw("FIND_IN_SET('${v}', ${field})", [], $logic);
                }
            });
        }
    }

    <?php }?>

}