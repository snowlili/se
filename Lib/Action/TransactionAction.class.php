<?php
class TransactionAction extends Action {
	public function index(){
        $Data = M('transaction'); // ʵ����Data����ģ��
		$Transaction = $Data->select();
		if($Transaction) {
			$this->transactions = $Transaction;// ģ�������ֵ
			//dump($this->transaction);
		}else{
			$this->error('���ݴ���');
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
