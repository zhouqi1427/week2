<?php

namespace app\admin\controller\brand;

use app\admin\controller\AuthController;
use app\admin\model\brand\Brand as BrandBrand;
use service\FormBuilder;
use service\JsonService;
use service\UtilService;
use think\Controller;
use think\Db;
use think\Loader;
use think\Request;
use think\Url;

class Brand extends AuthController
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        //
    //    return $this->fetch();
        $this->assign('brand', BrandBrand::getAll());
        
        return $this->fetch();
    }


    public function get_brand_list()
    {
        $where = UtilService::getMore([
            ['page', 1],
            ['limit', 20],
            // ['cid', ''],
            ['title', ''],
        ]);
        return JsonService::successlayui(BrandBrand::getAllList($where));
    }

    /**
     * 添加品牌
     * @param int $id
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function create($id = 0)
    {
        if ($id) $grade = BrandBrand::get($id);
        $form = FormBuilder::create(Url::build('save', ['id' => $id]), [
            FormBuilder::input('title', '品牌名称', isset($grade) ? $grade->title : ''),
            FormBuilder::number('sort', '排序', isset($grade) ? $grade->sort : 0),
            FormBuilder::frameImageOne('pic', '图标', Url::build('admin/widget.images/index', array('fodder' => 'pic')), isset($grade) ? $grade->pic : '')->icon('image')->width('70%')->height('500px'),
        ]);
        $form->setMethod('post')->setTitle($id ? '修改品牌' : '添加分类')->setSuccessScript('parent.$(".J_iframe:visible")[0].contentWindow.location.reload();');
        $this->assign(compact('form'));
        return $this->fetch('public/form-builder');
    }



    /**
     * 新增或者修改
     *
     * @return json
     */
    public function save($id = 0)
    {
        $post = UtilService::postMore([
            ['title', ''],
            ['pic', ''],
            ['sort', 0],
        ]);
        if (!$post['title']) return JsonService::fail('请输入品牌名称');
        if (!$post['pic']) return JsonService::fail('请选择分类图标');
        if ($id) {
            BrandBrand::update($post, ['id' => $id]);
            return JsonService::successful('修改成功');
        } else {
            $post['add_time'] = time();
            if (BrandBrand::set($post))
                return JsonService::successful('添加成功');
            else
                return JsonService::fail('添加失败');
        }
    }


    /**
     * 快速编辑
     *
     * @return json
     */
    public function set_value($field = '', $id = '', $value = '')
    {

        $field == '' || $id == '' || $value == '' && JsonService::fail('缺少参数');
        if (BrandBrand::where(['id' => $id])->update([$field => $value]))
            return JsonService::successful('保存成功');
        else
            return JsonService::fail('保存失败');
    }

    /**
     * 删除
     *
     * @return json
     */
    public function delete($id = 0)
    {
        if (!$id) return JsonService::fail('缺少参数');
        if (BrandBrand::del($id))
            return JsonService::successful('删除成功');
        else
            return JsonService::fail('删除失败');
    }


    public function excel()
     {
        //导出
	//##########################################################################
	$list = Db::name("brand")->select();
	$file_name = date('Y-m-d_His').'.xls';
        $path = dirname(__FILE__);
        Loader::import('PHPExcel.Classes.PHPExcel');
        Loader::import('PHPExcel.Classes.PHPExcel.IOFactory.PHPExcel_IOFactory');
      
        $PHPExcel = new \PHPExcel();
        // print_r($PHPExcel);die;
        $PHPSheet = $PHPExcel->getActiveSheet();
        $PHPSheet->setTitle("代理商");
        $PHPSheet->setCellValue("A1","ID");
        $PHPSheet->setCellValue("B1","名稱");
        $PHPSheet->setCellValue("C1","排序");
 
 
        $i = 2;
		foreach($list as $key => $value){
        	$PHPSheet->setCellValue('A'.$i,''.$value['id']);
        	$PHPSheet->setCellValue('B'.$i,''.$value['title']);
        	$PHPSheet->setCellValue('C'.$i,''.$value['sort']);
        	$i++;
    	}
        $PHPWriter = \PHPExcel_IOFactory::createWriter($PHPExcel,"Excel2007");
        header('Content-Disposition: attachment;filename='.$file_name);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $PHPWriter->save("php://output");
     }
}
