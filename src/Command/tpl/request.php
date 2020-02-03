declare(strict_types=1);

namespace App\Request;

use App\Kernel\Request\BaseFormRequest;
use Closure;

class <?=$modelName?>Request extends BaseFormRequest
{
    public $scene = 'default';

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            <?=$requestAttrStr?>
        ];
    }

    /**
     * 公共校验规则
     * @return array
     */
    public function commonRules(): array
    {
        return [
            <?=$requestRuleStr?>
        ];
    }

    // /**
    //  * test scene
    //  * @return array
    //  */
    // public function testRules(): array
    // {
    //     return [
    //         'email' => [
    //             'required',
    //             'email'
    //         ]
    //     ];
    // }

    // /**
    //  * 测试
    //  * @param string $attribute
    //  * @param mixed $value
    //  * @param Closure $error
    //  * @return void
    //  */
    // public function checkTestRule(string $attribute, $value, Closure $error)
    // {
    //     if (!$value || mb_strlen($value) < 3) {
    //         $error(':attribute 必填且长度不能小于3');
    //     }
    // }
}
