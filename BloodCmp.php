<?php
//include ("fetion.php");
class BloodCmp{
	// 判断标准数据区
	private $StdHigh = 178;													// 测量血糖值高压阀值标准
	private $StdLow = 100;													// 测量血糖值低压阀值标准
	private $StdHighLevel = 10;												// 测量血糖值轻重度高压标准
	private $StdLowLevel = 10;												// 测量血糖值轻重度低压标准		

	// 获取数据
	private $Telephone;
	private $BloodData;

	// 构造函数
	function __construct($Tphone, $BData)
	{
		$this->BloodData = $BData;
		$this->Telephone = $Tphone;
	}
	
	// 云端日志记录
	function RecordLog($result)
	{
		date_default_timezone_set('Etc/GMT-8');							    //这里设置了时区
		$nowtime = date("Y-m-d H:i:s");
		
		$filename = "log/" . $this->Telephone . ".txt";
		$tmp = "电话:" . $this->Telephone . " \n时间:" . $nowtime . "\n血糖值:" . $this->BloodData . "\n结果:" . $result . "\n\n";
		$fp = fopen($filename, "a");
		fwrite($fp, $tmp);
		fclose($fp);
	}
	
	// 进行单次阀值比较
	public function Blood_cmp()
	{
		$temph = $this->BloodData - $this->StdHigh;							// 本次测量值和高阀值的比较
		$templ = $this->BloodData - $this->StdLow;							// 本次测量值和低阀值的比较
		if ($this->BloodData > $this->StdHigh)								// 超出高阀值
		{
			if ($temph < $this->StdHighLevel)
				$level = 1;													// 轻度高, level 进行等级设定
			else
				$level = 2;													// 重度高
			$result = "血糖高";
			echo "\n\n\t超出了阀值,进行飞信通知用户!\n\n";
		}
		else if($this->BloodData < $this->StdLow)							// 超出低阀值
		{
			if ($templ < $this->StdHighLevel)
				$level = -1;												// 轻度低
			else 
				$level = -2;												// 重度低
			$result = "血糖低";
			echo "\n\n\t超出了阀值,进行飞信通知用户!\n\n";
		}
		else
		{
			$result = "正常";
			echo "\n\n\t本次测量正常，不进行通知用户!\n\n";
		}
		$this->RecordLog($result);											// 进行云端日志记录
		$SendFetion = new Fetion($level, $this->Telephone, "");
		$SendFetion->fetion();
		return $result;														// 写如数据库（仅仅写 result 的返回结果，和记录数值）
	}
}
?>
