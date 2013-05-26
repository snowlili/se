<?php
class TransactionAction extends Action {
	public function index(){
        $Data = M('transaction'); // 实例化Data数据模型
		$Transaction = $Data->select();
		if($Transaction) {
			$this->transactions = $Transaction;// 模板变量赋值
			//dump($this->transaction);
		}else{
			$this->error('数据错误');
		}
		if( $_POST ) 
		{ 
			$abc = $_POST['checktype']; 
			trace($abc);
		} 
		$this->checktype[1]=true;
		trace($this->checktype[1]);
		trace($array);
        $this->display();
    }
}
