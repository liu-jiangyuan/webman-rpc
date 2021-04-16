<?php
namespace app\org;


use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Exception as csvException;
class ParseCsv
{
    protected $fileType;
    protected $filePath;
    private $reader;
    private $dataSource = [
        'header' => [],
        'data' => []
    ];
    public function __construct(string $filePath,string $fileType = 'Csv')
    {
        $this->filePath = $filePath;
        $this->fileType = $fileType;
        $this->loadFile();
    }

    /**
     * 解析文件
     * @return $this|array|bool
     */
    private function loadFile()
    {
        try {
            $this->reader = IOFactory::createReader($this->fileType);
        } catch (csvException $e) {
            return [];
        }
        if (!file_exists($this->filePath)) return false;
        $data = $this->reader->load($this->filePath)->getActiveSheet()->toArray();
        $this->dataSource['header'] = empty($data) ? [] : $data[0];
        if (!empty($this->dataSource['header'])) unset($data[0]);
        $this->dataSource['data'] = array_values($data);
        return $this;
    }

    /**
     * 按照指定的字段归类数据，eg:id,uid之类的
     * @param string $key
     * @return array
     */
    public function getDataByDataHeader(string $key = 'id') :array
    {
        $newHeader = [];
        if (!empty($newHeader)){
            foreach ($this->dataSource['header'] as $k => $v){
                $newHeader[$v] = $v;
            }
        }
        $newData = [];
        if (!empty($this->dataSource['data'])){
            foreach ($this->dataSource['data'] as $keys => $val){
                $detail = [];
                foreach ($val as $k => $v){
                    $detail[$this->dataSource['header'][$k]] = $v;
                }
                if (!empty($detail) && isset($detail[$key])) $newData[$detail[$key]] = $detail;
            }
            if (!empty($newData)) $this->dataSource['data'] = $newData;
        }
        return $this->dataSource;
    }

    public function getResult()
    {
        return ['filePath'=>$this->filePath,'fileType'=>$this->fileType,'data'=>$this->dataSource];
    }

}