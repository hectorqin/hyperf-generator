declare(strict_types=1);

namespace App\Controller;

use <?=$cBase?>;
use App\Constants\Business;
use App\Model\<?=$modelName?>;
use App\Constants\ErrorCode;
use App\Exception\BusinessException;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Hyperf\DbConnection\Db;
<?php if($validateEnable) {?>
use App\Request\<?=$modelName?>Request;
<?php } ?>
use Hyperf\HttpServer\Annotation\AutoController;

/**
 * @AutoController(prefix="<?=lcfirst($modelName)?>")
 */
class <?=$modelName?>Controller extends <?=$cBaseName?>

{
    /**
     * 列表
     *
     * @return ResponseInterface
     * @throw  BusinessException
     */
    public function index()
    {
        $page = $this->request->input('page', 1);
        if ($page < 1) {
            throw new BusinessException(ErrorCode::ARGS_WRONG, '页码不能小于1');
        }
        $listRows = $this->request->input('listRows', $this->config->get('business.pagination.list_rows'));
        if ($listRows <= 0) {
            throw new BusinessException(ErrorCode::ARGS_WRONG, '分页大小不能小于0');
        }
        $maxRows = $this->config->get('business.pagination.list_max_rows', 100);
        if ($listRows > $maxRows) {
            throw new BusinessException(ErrorCode::ARGS_WRONG, '分页大小不能大于' . $maxRows);
        }
        $option = $this->request->input('option', '');
        if ($option) {
            <?php if(function_exists('\\App\\checkListOption')){ ?>

            list($action, $isValid) = \App\checkListOption($option);
            if (!$isValid) {
                throw new BusinessException(ErrorCode::ARGS_WRONG, 'option参数校验失败');
            }
            <?php } ?>
        }

        $query = <?=$modelName?>::withSearch(
            <?=$modelName?>::$searchFields,
            $this->request->all()
        );

        $query->order($this->request->input('sort',  ''));
        if ($relations = $this->request->input('with', '')) {
            $query->with($relations);
        }

        $total = $query->count();

        if(!isset($action) || $action != Business::LIST_ALL_DATA){
            $query->page($page, $listRows);
        }

        $hiddenFields = [<?=$hiddenFieldStr?>];
        <?php if(function_exists('\\App\\getHiddenFields')){ ?>

        $hiddenFields = \App\getHiddenFields($hiddenFields);
        <?php } ?>

        $list  = $query->get()->makeHidden($hiddenFields);
        return $this->success([
            'list'       => $list,
            'pagination' => [
                'total'    => $total,
                'page'     => $page,
                'listRows' => $listRows,
            ],
        ]);
    }

    /**
     * 新增
     *
     * @return ResponseInterface
     * @throw  BusinessException
     */
    public function save()
    {
        <?php if($validateEnable) {?>
        $request = $this->validate(<?=$modelName?>Request::class, 'save');

        <?php } else { ?>
        $request = $this->getFormRequest();

        <?php }?>

        $data = $request->only([
            <?=$fieldStr?>
        ]);

        try {
            $<?=$modelInstance?> = <?=$modelName?>::create($data);
            $<?=$modelInstance?>->refresh();

            return $this->success('保存成功', [
                'data' => $<?=$modelInstance?>->makeHidden(\App\getHiddenFields([<?=$hiddenFieldStr?>]))

            ]);
        } catch (\Throwable $th) {
            throw new BusinessException(ErrorCode::DB_WRONG, '保存失败', $th);
        }
    }

    /**
     * 查看详情
     *
     * @param  int  $id
     * @param  mixed  $with
     * @param  array  $readBy 查询条件
     * @throw  BusinessException
     * @return ResponseInterface
     */
    public function read(int $id = 0, $with='', array $readBy = null)
    {
        if (empty($readBy) && $id <= 0) {
            throw new BusinessException(ErrorCode::ARGS_WRONG, '参数错误');
        }
        if ($id) {
            $<?=$modelInstance?> = ($with ? <?=$modelName?>::with($with) : <?=$modelName?>::query())->find($id);
        } else {
            $<?=$modelInstance?> = ($with ? <?=$modelName?>::with($with) : <?=$modelName?>::query())->withSearch(<?=$modelName?>::$searchFields, $readBy)->first();
        }
        if (!$<?=$modelInstance?>) {
            throw new BusinessException(ErrorCode::DATA_NOT_FOUND, '数据不存在');
        }
        return $this->success([
            'data' => $<?=$modelInstance?>->makeHidden(\App\getHiddenFields([<?=$hiddenFieldStr?>]))
        ]);
    }

    /**
     * 更新
     *
     * @param  int  $id
     * @return ResponseInterface
     * @throw  BusinessException
     */
    public function update(int $id = 0)
    {
        if ($id <= 0) {
            throw new BusinessException(ErrorCode::ARGS_WRONG, '参数错误');
        }
        $<?=$modelInstance?> = <?=$modelName?>::find($id);
        if (!$<?=$modelInstance?>) {
            throw new BusinessException(ErrorCode::DATA_NOT_FOUND, '数据不存在');
        }

        <?php if($validateEnable) {?>
        $request = <?=$modelName?>Request::validatedRequest('update');

        <?php } else { ?>

        $request = $this->getFormRequest();
        <?php }?>

        $data = $request->only([
            <?=$updateFieldStr?>
        ]);

        $<?=$modelInstance?>->fill($data);
        if (!$<?=$modelInstance?>->isDirty()) {
            return $this->success('数据未变化');
        }

        try {
            $<?=$modelInstance?>->save();
            return $this->success('保存成功', [
                'data' => $<?=$modelInstance?>->makeHidden(\App\getHiddenFields([<?=$hiddenFieldStr?>]))

            ]);
        } catch (\Throwable $th) {
            throw new BusinessException(ErrorCode::DB_WRONG, '保存失败', $th);
        }
    }

    /**
     * 删除
     *
     * @param  int|array  $id
     * @return ResponseInterface
     */
    public function delete($id)
    {
        if (empty($id)) {
            throw new BusinessException(ErrorCode::ARGS_WRONG, '参数错误');
        }
        if (!is_array($id)){
            if( (int) $id < 0){
                throw new BusinessException(ErrorCode::ARGS_WRONG, '参数错误');
            }
            $id = [ (int)$id ];
        }
        $list = <?=$modelName?>::whereIn(<?=$modelName?>::PRIMARY_KEY, $id)->get();
        if ($list->isEmpty()) {
            throw new BusinessException(ErrorCode::DATA_NOT_FOUND, '数据不存在');
        }
        $success = [];
        $error   = [];
        /** @var <?=$modelName?> $item */
        foreach ($list as $item) {
            // 多个删除不互相影响
            $pkID = $item-><?=$pk?>;
            try {
                Db::beginTransaction();
                $item->tryToDelete();
                Db::commit();
                $success[] = $pkID;
            } catch (\Throwable $th) {
                Db::rollback();
                $error[] = ErrorCode::getErrorMessage($th, [
                    'id' => $pkID
                ]);
            }
        }
        return $this->success(count($success) ? '删除成功' : '删除失败', [
            'error'   => $error,
            'success' => $success,
        ]);
    }

}
