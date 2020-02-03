namespace app\<?=$vModule?>\validate<?=$vLayer?>;

use <?=$vBase?>;

class <?=$modelName?> extends <?=$vBaseName?>

{
    /**
     * 定义验证规则
     * 格式：'字段名'    =>    ['规则1','规则2'...]
     *
     * @var array
     */
    protected $rule = [
        <?=$validateStr?>
    ];

    /**
     * 定义错误信息
     * 格式：'字段名.规则名'    =>    '错误信息'
     *
     * @var array
     */
    protected $message = [];

    /**
     * 验证场景定义
     * @var array
     */
    protected $scene = [];

    // /**
    //  * 检查字段
    //  *
    //  * @param mixed $value
    //  * @param string $rule
    //  * @param array $data
    //  * @return boolean|string
    //  */
    // protected function checkField($value, $rule, $data = [])
    // {
    //     return true;
    // }
}
