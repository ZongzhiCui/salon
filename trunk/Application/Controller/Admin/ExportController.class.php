<?php
/**
 * User: 120.79.139.251:cuioo.cuiwq.cn
 * Date: 2018/3/4
 * Time: 15:41
 */

class ExportController extends PlatformController
{
    private $obj;
    public function __construct()
    {
        parent::__construct();
        $this->obj = new ExportModel();
    }
    public function index(){
        //>>1.接受数据
        //>>2.处理数据
        $data = $this->obj->gelIndex();//需要导出的数据
        foreach ($data as &$v){
            unset($v['photo']);
            unset($v['password']);
            unset($v['remark']);
            unset($v['last_login']);
            unset($v['last_login_ip']);
            $v['sex']=$v['sex']==0?'男':'女';
            $v['telephone'] .= "\t";
        }
        //导出
        $objPHPExcel = new PHPExcel();
        //添加一个表单
        $objPHPExcel->setActiveSheetIndex(0);
        //设置表单名称
        $objPHPExcel->getActiveSheet()->setTitle("用户信息表");
        //准备表头的名称
        $xlsHeader = [
            'ID',
            '昵称',
            '真实姓名',
            '性别',
            '电话',
            '余额',
            'vip等级'
        ];

        /**
         * 准备表格列名
         */
        $cellName = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AV', 'AW', 'AX', 'AY', 'AZ'];

        /**
         * 将表格第一行作为表格的简介行，需要合并
         */
        //>>1.获取需要合并多少列
        $column_count = count($xlsHeader);
        //>>2.合并第一行7列
        $objPHPExcel->getActiveSheet()->mergeCells("A1:" . $cellName[$column_count - 1] . "1");
        //>>3.设置合并后的内容
        $objPHPExcel->getActiveSheet()->setCellValue("A1", "用户信息统计  创建时间：" . date("Y-m-d"));

        /**
         * 表格第二行开始设置表头
         */
        foreach ($xlsHeader as $k => $v) {
            $objPHPExcel->getActiveSheet()->setCellValue($cellName[$k] . "2", $v);
        }

        /**
         * 表格第三行开始添加表格数据
         */
        foreach ($data as $k => $v) {
            //获取当前多少行
            $line = 3 + $k;
            $i = 0;
            foreach ($v as $key => $value) {
                $objPHPExcel->getActiveSheet()->setCellValue($cellName[$i] . $line, $value);
                ++$i;
            }
        }


        //导出excel
        $xlsname = iconv("utf-8", "gb2312", "用户信息表");

//         Redirect output to a client’s web browser (Excel5)
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $xlsname . '.xls"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        // If you're serving to IE over SSL, then the following may be needed
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        exit;
        //>>3.显示页面
    }

}